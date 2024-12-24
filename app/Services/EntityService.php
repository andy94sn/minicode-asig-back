<?php

namespace App\Services;

use Exception;
use Http\Client\Common\PluginClient;
use Illuminate\Support\Facades\Log;
use Soap\Engine\HttpBinding\SoapRequest;
use GuzzleHttp\Client;
use Soap\Psr18Transport\Psr18Transport;
use Soap\Psr18WsseMiddleware\WSSecurity\DigestMethod;
use Soap\Psr18WsseMiddleware\WSSecurity\SignatureMethod;

class EntityService
{

    protected $client;
    protected $endpoint;


    public function __construct()
    {
        $this->endpoint = env('MCONNECT_API_URL_TEST_WSDL');
        $privateKeyPath = storage_path('app/public/certificates/key.pem');
        $certificatePath = storage_path('app/public/certificates/certificate.pem');
        $passphrase = env('WS_SECURITY_CERT_PASSWORD');
        $signatureOptions = [
            'signature_method' => SignatureMethod::RSA_SHA256,
            'digest_method' => DigestMethod::SHA256,
            'sign_all_headers' => true,
            'sign_body' => true
        ];
        $this->company_id = env('RCA_API_SECURITY_IDNP_TEST');
        $this->signature = new Signature($privateKeyPath, $certificatePath, $passphrase, $signatureOptions);
        $this->client = new Client();
    }

    /**
     * @throws \Exception
     */
    public function getEntity(array $params): array
    {
        $data = null;
        $code = $params['code'];
        $requestParams = [
            'request' => [
                'CallingUser' => $params['id'],
                'CallingEntity' => $this->company_id,
                'CallBasis' => 'LEGII Nr. 92 din 07-04-2022 „privind activitatea de asigurare sau de reasigurare”, Articolul 51 alin. (1)',
                'CallReason' => 'Încheierea contractului de asigurare',
            ],
            'id' => $code,
        ];

        try {
            $soapXML = $this->buildSoapXml($requestParams);

            $signature = $this->signature->getWsseMiddleware();
            $psr18Client = new PluginClient($this->client, [$signature]);
            $transport = Psr18Transport::createForClient($psr18Client);

            $soapRequest = new SoapRequest(
                $soapXML,
                $this->endpoint,
                'GetLegalEntity',
                1
            );


            $soapResponse = $transport->request($soapRequest);
            $responseXML = simplexml_load_string($soapResponse->getPayload());
            $body = $responseXML->Body;

            if(isset($body->Fault)) {
                $fault = $body->Fault;
                $data['fault'] = (string) $fault->faultstring;
                $data['code']  = 422;

                return [
                    'status' => false,
                    'data' => $data,
                    'message' => self::message($params['lang'])['invalid']
                ];
            }else{
                $person = $body->LegalEntity;
                $address = $person->Address;

                $data['IdentificationCode'] = (string)$code;
                $data['Name'] =  $this->isValidXMLTag((string)$person->Name) ?? null;
                $data['Phone'] = $this->isValidXMLTag((string)$params['phone']) ?? $this->isValidXMLTag((string)$person->ShortName);
                $data['Address'] = [
                    'RegionName' => $this->isValidXMLTag((string)$address->Region) ?? $this->isValidXMLTag((string) $address->Locality),
                    'Locality' => $this->isValidXMLTag((string) $address->Locality) ?? null
                ];

                return [
                    'status' => true,
                    'data' => $data
                ];
            }
        }catch(Exception $exception){
            Log::error('Error: ' . $exception->getMessage());
            return [
                'status' => false,
                'data' => null,
                'message' => self::message($params['lang'])['error']
            ];
        }
    }


    private function buildSoapXml(array $data){
        $personalID = $data['request']['CallingUser'];
        $companyID = $data['request']['CallingEntity'];

        return <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mcon="https://mconnect.gov.md">
            <soapenv:Header>
                <mcon:CallingUser>$personalID</mcon:CallingUser>
                <mcon:CallingEntity>$companyID</mcon:CallingEntity>
                <mcon:CallBasis>LEGII Nr. 92 din 07-04-2022 „privind activitatea de asigurare sau de reasigurare”, Articolul 51 alin. (1)</mcon:CallBasis>
                <mcon:CallReason>Încheierea contractului de asigurare</mcon:CallReason>
            </soapenv:Header>
            <soapenv:Body>
                <mcon:GetPerson>
                    <mcon:IDNP>{$data['id']}</mcon:IDNP>
                </mcon:GetPerson>
            </soapenv:Body>
        </soapenv:Envelope>
XML;
    }

    private function isValidXMLTag($string)
    {
        $invalidChars = ['<', '>', '&', '\'', '"'];

        foreach ($invalidChars as $char) {
            if (strpos($string, $char) !== false) {
                return null;
            }
        }

        return $string;
    }

    public static function message($language)
    {
        $messages = [
            'ro' => [
                'invalid' => 'Date invalide',
                'error' => 'Eroare: Ceva nu a mers bine',
                'param' => 'Parametri incorecți'
            ],
            'en' => [
                'invalid' => 'Invalid data',
                'error' => 'Error: Something went wrong',
                'param' => 'Incorrect parameters'
            ],
        ];

        return $messages[$language] ?? $messages['en'];
    }
}
