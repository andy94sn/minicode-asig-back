<?php

namespace App\Services;

use Exception;
use Http\Client\Common\PluginClient;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Soap\Engine\HttpBinding\SoapRequest;
use GuzzleHttp\Client;
use Soap\Psr18Transport\Psr18Transport;
use Soap\Psr18WsseMiddleware\WSSecurity\DigestMethod;
use Soap\Psr18WsseMiddleware\WSSecurity\SignatureMethod;

class VehicleService
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
    public function getVehicle(array $params): array
    {
        $data = null;
        $id = $params['id'];
        $certificate = $params['certificate'];
        $requestParams = [
            'request' => [
                'CallingUser' => $id,
                'CallingEntity' => $this->company_id,
                'CallBasis' => 'LEGII Nr. 92 din 07-04-2022 „privind activitatea de asigurare sau de reasigurare”, Articolul 51 alin. (1)',
                'CallReason' => 'Încheierea contractului de asigurare',
            ],
            'certificate' => $certificate
        ];

        try {
            $soapXML = $this->buildSoapXml($requestParams);
            $signature = $this->signature->getWsseMiddleware();
            $psr18Client = new PluginClient($this->client, [$signature]);
            $transport = Psr18Transport::createForClient($psr18Client);

            $soapRequest = new SoapRequest(
                $soapXML,
                $this->endpoint,
                'GetVehicle',
                1
            );


            $soapResponse = $transport->request($soapRequest);
            $xmlResponse = new SimpleXMLElement($soapResponse->getPayload());


            if(isset($xmlResponse->Body)) {
                $vehicle = $xmlResponse->Body->Vehicle;

                $data['MarkName'] = $this->isValidXMLTag((string)$vehicle->Make);
                $data['Model']    = $this->isValidXMLTag((string)$vehicle->Model[0]);
                $data['ProductionYear']    = $this->isValidXMLTag((string)$vehicle->Year);
                $data['RegistrationNumber']    = $this->isValidXMLTag((string)$vehicle->Plate->RegistrationNumber);
                $data['RegistrationCertificateNumber']    = $this->isValidXMLTag($certificate);
                $data['CilinderVolume']    = $this->isValidXMLTag((string)$vehicle->EngineVolume);
                $data['TotalWeight']    = $this->isValidXMLTag((string)$vehicle->AuthorizedWeight);
                $data['Seats']    = $this->isValidXMLTag((string)$vehicle->NumberOfPlaces);
                $data['VinCode']    = $this->isValidXMLTag((string)$vehicle->VIN);
                $data['IDNV']    = $this->isValidXMLTag((string)$vehicle->IDNV);
                $data['EngineNr']    = $this->isValidXMLTag((string)$vehicle->EngineNumber);
                $data['OwnerPersonalCode']    = $this->isValidXMLTag($id);

                return [
                    'status' => true,
                    'data' => $data,
                    'message' => null
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
        $certificate = $data['certificate'];
        $series = ctype_alpha($certificate) ? strtoupper(substr($certificate, 0, 1)) : null;
        $number = substr($certificate, 1);

        if($series){
            $xml = <<<XML
                   <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mcon="https://mconnect.gov.md">
                        <soapenv:Header>
                            <mcon:CallingUser>$personalID</mcon:CallingUser>
                            <mcon:CallingEntity>$companyID</mcon:CallingEntity>
                            <mcon:CallBasis>LEGII Nr. 92 din 07-04-2022 „privind activitatea de asigurare sau de reasigurare”, Articolul 51 alin. (1)</mcon:CallBasis>
                            <mcon:CallReason>Încheierea contractului de asigurare</mcon:CallReason>
                        </soapenv:Header>
                        <soapenv:Body>
                            <mcon:GetVehicle>
                                <mcon:DocumentType>45</mcon:DocumentType>
                                <mcon:DocumentSeries>$series</mcon:DocumentSeries>
                                <mcon:DocumentNumber>$number</mcon:DocumentNumber>
                            </mcon:GetVehicle>
                        </soapenv:Body>
                    </soapenv:Envelope>
XML;
        }else{
            $xml = <<<XML
                   <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mcon="https://mconnect.gov.md">
                        <soapenv:Header>
                            <mcon:CallingUser>$personalID</mcon:CallingUser>
                            <mcon:CallingEntity>$companyID</mcon:CallingEntity>
                            <mcon:CallBasis>LEGII Nr. 92 din 07-04-2022 „privind activitatea de asigurare sau de reasigurare”, Articolul 51 alin. (1)</mcon:CallBasis>
                            <mcon:CallReason>Încheierea contractului de asigurare</mcon:CallReason>
                        </soapenv:Header>
                        <soapenv:Body>
                            <mcon:GetVehicle>
                                <mcon:DocumentType>15</mcon:DocumentType>
                                <mcon:DocumentNumber>$certificate</mcon:DocumentNumber>
                            </mcon:GetVehicle>
                        </soapenv:Body>
                    </soapenv:Envelope>
XML;
        }

        return $xml;
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
