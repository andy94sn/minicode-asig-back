<?php

namespace App\Services;

use App\Enums\DocumentType;
use DOMDocument;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Soap\Psr18WsseMiddleware\WSSecurity\DigestMethod;
use Soap\Psr18WsseMiddleware\WSSecurity\SignatureMethod;
use SoapClient;
use SoapFault;
use SoapHeader;

class RcaApiService
{
    protected $client;
    protected $token;
    protected $username;
    protected $password;
    protected $securityToken;
    protected $employee;
    protected $signature;

    /**
     * @throws \SoapFault
     */
    public function __construct()
    {
        $endpoint = env('RCA_API_URL_WSDL');
        $this->securityToken  = env('RCA_API_SECURITY_TOKEN');
        $this->username = env('RCA_API_USERNAME');
        $this->password = env('RCA_API_PASSWORD');
        $this->employee = env('RCA_API_SECURITY_IDNP_TEST');
        $privateKey = storage_path('app/public/certificates/key.pem');
        $publicKey = storage_path('app/public/certificates/certificate.pem');
        $passphrase = env('WS_SECURITY_CERT_PASSWORD');
        $signatureOptions = [
            'signature_method' => SignatureMethod::RSA_SHA256,
            'digest_method' => DigestMethod::SHA256,
            'sign_all_headers' => true,
            'sign_body' => true
        ];

        $this->signature = new Signature($privateKey, $publicKey, $passphrase, $signatureOptions);

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]);

        $this->client = new SoapClient($endpoint, [
            'login' => $this->username,
            'password' => $this->password,
            'trace' => true,
            'exceptions' => true,
            'soap_version' => SOAP_1_2,
            'stream_context' => $context,
        ]);

        $this->token = $this->authenticate();
    }

    /**
     * Auth Token
     */
    public function authenticate()
    {
        $cachedToken = Cache::get('token');
        $validToken  = $this->checkAccess();

        if ($cachedToken && $validToken) {
            return $cachedToken;
        }

        try {
            $response = $this->client->__soapCall('Authenticate', [[
                'author' => [
                    'SecurityToken' => $this->securityToken,
                    'UserName' => $this->username,
                    'UserPassword' => $this->password,
                ]
            ]]);

            if ($response && isset($response->AuthenticateResult)) {
                return $response->AuthenticateResult;
            }

            return null;
        }catch (SoapFault $e) {
            Log::error("[" . __METHOD__ . "] Authentication failed: " . $e->getMessage());
            return null;
        }
    }

    public function calculate($params) {
        switch ($params['type']) {
            case 'Rca':
                return $this->calculateRCAIPremium($params);
            case 'GreenCard':
                return $this->calculateRCAEPremium($params);
            default:
                return [
                    'success' => false,
                    'message' => self::message($params['lang'])['error']
                ];
        }
    }

    private function calculateRCAIPremium($params) {
        $request = [
            'Employee' => [
                'IDNP' => $this->employee,
            ],
            'OperatingModes' => $params['OperatingModes'] ?? 1,
            'PersonIsJuridical' => true,
            'IDNX' => $params['id'],
            'VehicleRegistrationCertificateNumber' => $params['certificate']
        ];

        if(isset($params['zone']) || isset($params['term'])){
            return [
                'success' => false,
                'message' => self::message($params['lang'])['param']
            ];
        }

        try{
            $response = $this->client->__soapCall('CalculateRCAIPremium', [[
                'SecurityToken' => $this->token,
                'request' => $request
            ]]);

            $result = $response->CalculateRCAIPremiumResult;

            if (!$result->IsSuccess) {
                return [
                    'success' => false,
                    'message' => $result->ErrorMessage
                ];
            }

            $data = [
                'primeSum' => (float)$result->PrimeSum,
                'bonusMalusClass' => (int)$result->BonusMalusClass,
                'firstName' => $result->PersonFirstName,
                'lastName' => $result->PersonLastName,
                'vehicleMark' => $result->VehicleMark,
                'vehicleModel' => $result->VehicleModel,
                'vehicleRegistrationNumber' => $result->VehicleRegistrationNumber,
                'primeSumMDL' => null,
                'exchangeRate' => null,
                'personName' => null,
                'vehicleCategory' => null,
            ];

            return [
                'success' => true,
                'message' => null,
                'data' => (object) $data
            ];
        }catch(Exception $exception){
            Log::error('Error: ' . $exception->getMessage());
            return [
                'success' => false,
                'message' => self::message($params['lang'])['error']
            ];
        }
    }

    private function calculateRCAEPremium($params) {
        $data = array();
        $request = [
            'Employee' => [
                'IDNP' => $this->employee,
            ],
            'GreenCardZone' => $params['zone'],
            'IDNX' => $params['id'],
            'VehicleRegistrationCertificateNumber' => $params['certificate'],
            'TermInsurance' => $params['term']
        ];

        try {
            $response = $this->client->__soapCall('CalculateRCAEPremium', [[
                'SecurityToken' => $this->token,
                'request' => $request
            ]]);

            $result = $response->CalculateRCAEPremiumResult;

            if (!$result->IsSuccess) {
                return [
                    'success' => false,
                    'message' => $result->ErrorMessage
                ];
            }

            $data = [
                'primeSum' => (float)$result->PrimeSum,
                'primeSumMDL' => (float)$result->PrimeSumMDL,
                'exchangeRate' => $result->ExchangeRate,
                'personName' => $result->PersonName,
                'vehicleCategory' => $result->VehicleCategory
            ];

            return [
                'success' => true,
                'message' => null,
                'data'    => $data
            ];
        } catch (Exception $exception) {
            Log::error('Error: ' . $exception->getMessage());
            return [
                'success' => false,
                'message' => self::message($params['lang'])['error'],
                'data'    => $data
            ];
        }
    }

    public function lastContractExpirationDate($params){

        $request = [
            'IDNX' => $params['id'],
            'VehicleRegistrationCertificateNumber' => $params['certificate'],
            'ContractType' => $params['type']
        ];


        try {
            $response = $this->client->__soapCall('GetLastContractExpirationDate', [[
                'SecurityToken' => $this->token,
                'request' => $request
            ]]);

            $result = $response->GetLastContractExpirationDateResult;

            if(!$result->ExpirationDate){
                return [
                    'success' => false,
                    'message' => self::message($params['lang'])['found'],
                ];
            }


            if (!$result->IsSuccess){
                return [
                    'success' => false,
                    'message' => $result->ErrorMessage,
                ];
            }

            return [
                'success' => true,
                'date' => $result->ExpirationDate
            ];
        }catch(Exception $exception){
            Log::error('Error: ' . $exception->getMessage());
            return [
                'success' => false,
                'message' => self::message($params['lang'])['error']
            ];
        }
    }

    /**
     * @throws Exception
     */
    public function save($params) {
        $type = $params['type'];
        $requestData = $this->prepareRequestData($params);

        try {
            $response = $this->client->__soapCall('Save' . $type . 'Document', [$requestData]);
            $result = $response->{'Save' . $type . 'DocumentResult'};

            if (!$result->Success) {
                return [
                    'success' => false,
                    'message' => $result->ErrorMessage,
                    'data'    => null
                ];
            }

            return [
                'success' => true,
                'data'    => $result->Response->Id
            ];
        } catch (Exception $exception) {
            Log::error('Error ' . $type . ' document: ' . $exception->getMessage());
            return [
                'success' => false
            ];
        }

    }

    public function contract($params): array
    {

        $type = DocumentType::getType($params['document']);

        $requestData = [
            'SecurityToken' => $this->token,
            'fileRequest' => [
                'DocumentId' => $params['id'],
                'DocumentType' => $params['document'],
                'ContractType' => $params['type']
            ]
        ];

        try{
            $response = $this->client->__soapCall('GetFile', [$requestData]);
            $result = $response->{'GetFileResult'};

            if (!$result->IsSuccess) {
                return [
                    'success' => false,
                    'message' => $result->ErrorMessage,
                    'data'    => null
                ];
            }

            $fileContent = $result->FileContent;
            $fileName = strtolower($params['document']) . '_' . uniqid() . '.pdf';
            $directory = strtolower($params['type']);

            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            $path = Storage::disk('public')->put($directory.'/'.$fileName, $fileContent);

            if ($path) {
                $url = env('APP_URL') . Storage::url($directory . '/' . $fileName);

                return [
                    'success' => true,
                    'data' => $url
                ];
            }else{
                return [
                    'success' => false,
                    'message' => self::message($params['lang'])[$type]
                ];
            }
        }catch(Exception $exception) {
            Log::error('Error:' . $exception->getMessage());
            return [
                'success' => false
            ];
        }
    }

    private function sendEmail(){

    }

    /**
     * @throws Exception
     */
    private function prepareRequestData($params)
    {
        $requestData = [
            'SecurityToken' => $this->token
        ];

        $requestData['request'] = [
            'OperatingMode' => $params['operatingMode'] ?? 'Usual',
            'Employee' => [
                'IDNP' => $this->employee
            ],
        ];

        if ($params['person'] == 1) {
            $responsePerson = (new PersonService())->getPerson($params);
            if ($responsePerson['status']) {
                $responsePerson['data']['IsFromTransnistria'] = 'false';
                $responsePerson['data']['PersonIsExternal'] = 'false';
                $requestData['request']['InsuredPhysicalPerson'] = $responsePerson['data'];
            }
        }elseif ($params['person'] == 2) {
            $responsePerson = (new EntityService())->getEntity($params);
            if ($responsePerson['status']) {
                $requestData['request']['InsuredJuridicalPerson'] = $responsePerson['data'];
            }
        }

        $responseVehicle = (new VehicleService())->getVehicle($params);

        if ($responseVehicle['status']) {
            $responseVehicle['data']['EnginePower'] = ['_attributes' => ['xsi:nil' => 'true']];
            $requestData['request']['InsuredVehicle'] = $responseVehicle['data'];
        }

        $requestData['request']['StartDate'] = (string)$params['start'];
        $requestData['request']['PaymentDate'] = (string)date('Y-m-d');
        $requestData['request']['TermInsurance'] = (string)$params['term'];
        $requestData['request']['PaymentMode'] = 'Transfer';
        $requestData['request']['PossessionBase'] = $params['possession'];

        if ($params['type'] == 'GreenCard') {
            $requestData['request']['GreenCardZone'] = $params['zone'];
        }

        return $requestData;
    }


    /**
     * Verify Access Token
     */
    private function checkAccess(): ?bool
    {
        if (!$this->token) {
            return false;
        }

        try {
            $params = [
                'login' => $this->username,
                'password' => $this->password,
            ];

            $response = $this->client->__soapCall('CheckAccess', [$params]);

            if (isset($response->CheckAccessResult) && $response->CheckAccessResult === 'Successful authorization') {
                return true;
            } else {
                return false;
            }
        } catch (SoapFault $e) {
            Log::error("Check access failed: " . $e->getMessage());
            return false;
        }
    }


    public static function message($language): array
    {
        $messages = [
            'ro' => [
                'invalid' => 'Date invalide',
                'error' => 'Eroare: Ceva nu a mers bine',
                'param' => 'Parametri incorecți',
                'contract' => 'Contractul nu a fost găsit',
                'policy'   => 'Polița de asigurare nu poate fi returnată pentru un contract nefinisat',
                'demand'   => 'Cererea nu a fost găsită'
            ],
            'en' => [
                'invalid' => 'Invalid data',
                'error' => 'Error: Something went wrong',
                'param' => 'Incorrect parameters',
                'contract' => 'Contract not found',
                'policy'   => 'Insurance Policy not found',
                'demand'   => 'Demand not found'
            ],
            'ru' => [
                'invalid' => 'Неверные данные',
                'error' => 'Ошибка: Что-то пошло не так',
                'param' => 'Неверные параметры',
                'contract' => 'Контракт не найден',
                'policy'   => 'Страховой полис не найден',
                'demand'   => 'Запрос не найден'
            ]
        ];

        return $messages[$language] ?? $messages['en'];
    }

}
