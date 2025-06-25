<?php

namespace App\Repositories;

use App\User;
use GuzzleHttp\Client;
use App\FacturaElectronica\Common;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class FacturaElectronicaRepository extends DbRepository
{
    /**
     * Construct
     * @param User $model
     */
    public function __construct()
    {
        $this->limit = 10;
        //$this->client = $client;
    
        $type = env('FE_ENV') ? env('FE_ENV') : 'test';
        
        switch ($type) {
            case 'prod':
                $this->baseUrl = 'https://api.comprobanteselectronicos.go.cr/recepcion/v1/';
                $this->authUrl = 'https://idp.comprobanteselectronicos.go.cr/auth/realms/rut/protocol/openid-connect/token';
                $this->clientId = 'api-prod';
                break;
            case 'test':
                $this->baseUrl = 'https://api.comprobanteselectronicos.go.cr/recepcion-sandbox/v1/';
                $this->authUrl = 'https://idp.comprobanteselectronicos.go.cr/auth/realms/rut-stag/protocol/openid-connect/token';
                $this->clientId = 'api-stag';
                break;
            }
        $this->accessToken = null;
        $this->refreshToken = null;

        $this->client = new Client([
            'timeout' => env('FE_TIMEOUT') ? env('FE_TIMEOUT') : 120
        ]);
        $this->type = $type;

        $this->schema_factura = 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/facturaElectronica';
        $this->schema_nota_credito = 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaCreditoElectronica';
        $this->schema_nota_debito = 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaDebitoElectronica';
        $this->schema_tiquete = 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/tiqueteElectronico';
        $this->schema_mensaje_receptor = 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/mensajeReceptor';
    }

    public function get_token($username, $password)
    {
        $client_secret = '';
        $scope = '';
        $grant_type = 'password';
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
        $body = [
            'client_id' => $this->clientId,
            'username' => $username,
            'password' => $password,
            'client_secret' => $client_secret,
            'scope' => $scope,
            'grant_type' => $grant_type,
        ];
        try {
            $response = $this->client->request('POST', $this->authUrl, ['form_params' => $body]);
            $body = $response->getBody();
            $content = $body->getContents();
            $result = json_decode($content);

            if (!empty($result->access_token) && !empty($result->refresh_token)) {
                // $this->setAccessToken($result->access_token);
                // $this->setRefreshToken($result->refresh_token);
                return $result;
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {

            \Log::error(\GuzzleHttp\Psr7\Message::toString($e->getResponse()));
            //\Log::info('status code:'. $e->getResponse()->getStatusCode());
            switch ($e->getResponse()->getStatusCode()) {
                case 401:
                    throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Invalid user credentials');
                    break;
                
                default:
                    throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error Hacienda no identificado');
                    break;
            }
            // if ($e->getResponse()->getStatusCode() == 404) {
            //     throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error de conexion con hacienda');
            // } else {

            //     if ($e->getResponse()->hasHeader('X-Error-Cause')) {
            //         throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. ' . $e->getResponse()->getHeader('X-Error-Cause')[0]);
            //     } else {
            //         throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error Hacienda no identificado');
            //     }

            // }
        }

        /*$url = 'https://idp.comprobanteselectronicos.go.cr/auth/realms/rut-stag/protocol/openid-connect/token';//access token url
        $data = [
            'client_id' => $client_id, //Test: 'api-stag' Production: 'api-prod'
            'client_secret' => '', //always empty
            'grant_type' => 'password', //always 'password'
                      //go to https://www.hacienda.go.cr/ATV/login.aspx to generate a username and password credentials
            'username' => $username, // 'cpf-02-0553-0597@stag.comprobanteselectronicos.go.cr',
            'password' => $password, //':w:Kc.}(Og@7w}}y!c]Q',
            'scope' => ''
        ];//always empty
        // use key 'http' even if you send the request to https://...
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context = stream_context_create($options);

        $result = file_get_contents($url, false, $context);

        if ($result === false) {
            echo $result;
        }

        $token = json_decode($result); //get a token object
        return $token; //return a json object whith token and refresh token*/
    }

    public function sendHacienda($configFactura, $signedinvoiceXML)
    {
        $invoice64String = $this->parseBase64($signedinvoiceXML);
        $invoiceXML = new \SimpleXMLElement($signedinvoiceXML);

        $authToken = $this->get_token($configFactura->atv_user, $configFactura->atv_password);


            if((string)$invoiceXML->Receptor && (string)Common::validarId($invoiceXML->Receptor->Identificacion->Numero)){
                $body = [
                    'clave' => (string)$invoiceXML->Clave, //encabezadoFactura->clave, //$invoiceXML->Clave,
                    'fecha' => (string)$invoiceXML->FechaEmision, //Carbon::createFromFormat('dmy', $encabezadoFactura->fechaEmision)->toAtomString(), //$invoiceXML->FechaEmision,
                    'emisor' => [
                        'tipoIdentificacion' => (string)$invoiceXML->Emisor->Identificacion->Tipo, 
                        'numeroIdentificacion' => (string)Common::validarId($invoiceXML->Emisor->Identificacion->Numero),
                    ],
                    'receptor' => [
                        'tipoIdentificacion' => (string)$invoiceXML->Receptor->Identificacion->Tipo,
                        'numeroIdentificacion' => (string)Common::validarId($invoiceXML->Receptor->Identificacion->Numero)
                    ],
                    'callbackUrl' => env('APP_URL') . '/hacienda/response',
                    'comprobanteXml' => $invoice64String
                ];

            }else{
            

                $body = [
                    'clave' => (string)$invoiceXML->Clave, //encabezadoFactura->clave, //$invoiceXML->Clave,
                    'fecha' => (string)$invoiceXML->FechaEmision, //Carbon::createFromFormat('dmy', $encabezadoFactura->fechaEmision)->toAtomString(), //$invoiceXML->FechaEmision,
                    'emisor' => [
                        'tipoIdentificacion' => (string)$invoiceXML->Emisor->Identificacion->Tipo,  
                        'numeroIdentificacion' => (string)Common::validarId($invoiceXML->Emisor->Identificacion->Numero), 
                    ],
                
                    'callbackUrl' => env('APP_URL') . '/hacienda/response',
                    'comprobanteXml' => $invoice64String
                ];
            }
        

        $headers = [
            'authorization' => 'Bearer ' . $authToken->access_token,
            'content-type' => 'application/json'
        ];

        try {
            $response = $this->client->request('POST', $this->baseUrl . 'recepcion', ['headers' => $headers, 'json' => $body]);
            $body = $response->getBody();
            $content = $body->getContents();
            $result = json_decode($content);

            return $result;
            /*if (!$result) {
                $headers = [
                            'authorization' => 'Bearer ' . $authToken->access_token,
                            'content-type' => 'application/json'
                        ];

                $response = $this->client->request('GET', $this->baseUrl . 'recepcion/' . $encabezadoFactura->clave, ['headers' => $headers]);
                $body = $response->getBody();
                $content = $body->getContents();
                $result = json_decode($content);

                return json_encode($result);
            } else {
                return 'Ha ocurrido un error al enviar la factura a hacienda';
            }*/
        } catch (\GuzzleHttp\Exception\ClientException $e) {

            \Log::error(\GuzzleHttp\Psr7\Message::toString($e->getResponse()));

            switch ($e->getResponse()->getStatusCode()) {
                case 400:
                    throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. ' . $e->getResponse()->getHeader('X-Error-Cause')[0]);
                    break;
                
                default:
                    throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error Hacienda no identificado');
                    break;
            }

            // if ($e->getResponse()->getStatusCode() == 404) {
            //     throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error de conexion con hacienda');
            // } else {

            //     if ($e->getResponse()->hasHeader('X-Error-Cause')) {
            //         throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. ' . $e->getResponse()->getHeader('X-Error-Cause')[0]);
            //     } else {
            //         throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error Hacienda no identificado');
            //     }

            // }
            
        }
    }

    public function sendMensajeHacienda($configFactura, $signedMensajeXML, $receptor)
    {
       
        $mensaje64String = $this->parseBase64($signedMensajeXML);
        $mensajeXML = new \SimpleXMLElement($signedMensajeXML);
       

        $authToken = $this->get_token($configFactura->atv_user, $configFactura->atv_password);


       

        $body = [
            'clave' => (string)$mensajeXML->Clave, //encabezadoFactura->clave, //$invoiceXML->Clave,
            'fecha' => (string)$mensajeXML->FechaEmisionDoc, //Carbon::createFromFormat('dmy', $encabezadoFactura->fechaEmision)->toAtomString(), //$invoiceXML->FechaEmision,
            'emisor' => [
                'tipoIdentificacion' => $receptor->tipo_identificacion_emisor,
                'numeroIdentificacion' => (string)Common::validarId($mensajeXML->NumeroCedulaEmisor),
            ],
            'receptor' => [
                'tipoIdentificacion' => 222,
                'numeroIdentificacion' => 123
            ],
            'callbackUrl' => env('APP_URL') . '/hacienda/mensaje/response',
            "consecutivoReceptor" => (string)$mensajeXML->NumeroConsecutivoReceptor,
            'comprobanteXml' => $mensaje64String
        ];

       

        $headers = [
            'authorization' => 'Bearer ' . $authToken->access_token,
            'content-type' => 'application/json'
        ];

        try {
            
            $response = $this->client->request('POST', $this->baseUrl . 'recepcion', ['headers' => $headers, 'json' => $body]);
            $body = $response->getBody();
            $content = $body->getContents();
            $result = json_decode($content);
            
            return $result;
           
            // if (!$result) {
            //     $headers = [
            //                 'authorization' => 'Bearer ' . $authToken->access_token,
            //                 'content-type' => 'application/json'
            //             ];

            //     $response = $this->client->request('GET', $this->baseUrl . 'recepcion', ['headers' => $headers]);
            //     $body = $response->getBody();
            //     $content = $body->getContents();
            //     $result = json_decode($content);
               
            //     return json_encode($result);
            // } else {
            //     return 'Ha ocurrido un error al enviar la factura a hacienda';
            // }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            
            \Log::error(\GuzzleHttp\Psr7\Message::toString($e->getResponse()));

            switch ($e->getResponse()->getStatusCode()) {
                case 400:
                    throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. ' . $e->getResponse()->getHeader('X-Error-Cause')[0]);
                    break;
                
                default:
                    throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error Hacienda no identificado');
                    break;
            }
            
            // if($e->getResponse()->getStatusCode() == 404){
            //     throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode(). '. Error de conexion con hacienda');
            // }else{

            //     if ($e->getResponse()->hasHeader('X-Error-Cause')) {
            //         throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode(). '. '. $e->getResponse()->getHeader('X-Error-Cause')[0]);
            //     } else {
            //         throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error Hacienda no identificado');
            //     }

            // }

            

        }
    }

    public function recepcion($configFactura, $clave) //ver el estado de la factura enviada a hacienda
    {
        $authToken = $this->get_token($configFactura->atv_user, $configFactura->atv_password);//$this->get_token();

        $headers = [
            'authorization' => 'Bearer ' . $authToken->access_token,
            'content-type' => 'application/json'
        ];

        try {
            $response = $this->client->request('GET', $this->baseUrl . 'recepcion/' . $clave, ['headers' => $headers]);

            $body = $response->getBody();
            $content = $body->getContents();
            $result = json_decode($content);
            
            if (isset($result->{'respuesta-xml'})) {
                Storage::disk('local')->put('facturaelectronica/' . $configFactura->id . '/gpsm_mensaje_hacienda_' . $clave . '.xml', base64_decode($result->{'respuesta-xml'}));
            }

            return $result;//json_encode($this->decodeRespuestaXML($result->{'respuesta-xml'}));
        } catch (\GuzzleHttp\Exception\ClientException $e) {

            \Log::error(\GuzzleHttp\Psr7\Message::toString($e->getResponse()));

            switch ($e->getResponse()->getStatusCode()) {
                case 400:
                    throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. ' . $e->getResponse()->getHeader('X-Error-Cause')[0]);
                    break;
                
                default:
                    throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error Hacienda no identificado');
                    break;
            }

            // if ($e->getResponse()->getStatusCode() == 404) {
            //     throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error de conexion con hacienda');
            // } else {

            //     if ($e->getResponse()->hasHeader('X-Error-Cause')) {
            //         throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. ' . $e->getResponse()->getHeader('X-Error-Cause')[0]);
            //     } else {
            //         throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error Hacienda no identificado');
            //     }

            // }
        }
    }

    public function comprobante($configFactura, $clave)
    {
        $authToken = $this->get_token($configFactura->atv_user, $configFactura->atv_password);

        $headers = [
            'authorization' => 'Bearer ' . $authToken->access_token,
            'content-type' => 'application/json'
        ];

        try {
            $response = $this->client->request('GET', $this->baseUrl . 'comprobantes/' . $clave, ['headers' => $headers]);

            $body = $response->getBody();
            $content = $body->getContents();
            $result = json_decode($content);

            return json_encode($result);
        } catch (\GuzzleHttp\Exception\ClientException $e) {

            \Log::error(\GuzzleHttp\Psr7\Message::toString($e->getResponse()));

            switch ($e->getResponse()->getStatusCode()) {
                case 404:
                    throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. ' . $e->getResponse()->getHeader('X-Error-Cause')[0]);
                    break;
                
                default:
                    throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error Hacienda no identificado');
                    break;
            }

            // if ($e->getResponse()->getStatusCode() == 404) {
            //     throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error de conexion con hacienda');
            // } else {

            //     if ($e->getResponse()->hasHeader('X-Error-Cause')) {
            //         throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. ' . $e->getResponse()->getHeader('X-Error-Cause')[0]);
            //     } else {
            //         throw new \App\Exceptions\HaciendaConexionException($e->getResponse()->getStatusCode() . '. Error Hacienda no identificado');
            //     }

            // }
        }
    }

    public function decodeRespuestaXML($respuestaBase64)
    {
        $facturaXML = new \SimpleXMLElement(base64_decode($respuestaBase64));

        return $facturaXML;
    }

    public function parseBase64($invoice)
    {
        //set $data to UTF-8 format
        /*$invoiceUTF8 = '';
        $len = strlen($invoice);
        for ($i = 0; $i < $len; $i++) {
            $invoiceUTF8 .= sprintf("%08b", ord($invoice {
                $i}));
        }*/
        //parse byte_array to base64
        $base64 = base64_encode($invoice);
        return $base64;
    }

    public function signXML($configFactura, $invoice)
    {
        $cert = 'cert';
        $pin = $configFactura->pin_certificado;

        switch ($invoice->TipoDocumento) {
            case '01':
                $schema = $this->schema_factura;
                break;
            case '02':
                $schema = $this->schema_nota_debito;
                break;
            case '03':
                $schema = $this->schema_nota_credito;
                break;
            case '04':
                $schema = $this->schema_tiquete;
                break;

            default:
                $schema = $this->schema_factura;
                break;
        }

        $salida = exec('java -jar ' . storage_path('app/facturaelectronica/xadessignercrv2.jar') . ' sign ' . storage_path('app/facturaelectronica/' . $configFactura->id . '/' . $cert . '.p12') . ' ' . $pin . ' ' . storage_path('app/facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '.xml') . ' ' . storage_path('app/facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '_signed.xml') . ' ' . $schema);

        \Log::info('results of xadessignercr: ' . json_encode($salida));

        /*$salida2 = exec('java -jar ' . storage_path('app/facturaelectronica/xadessignercr.jar') . ' send https://api.comprobanteselectronicos.go.cr/recepcion-sandbox/v1 '. storage_path('app/facturaelectronica/out.xml') . ' cpf-02-0553-0597@stag.comprobanteselectronicos.go.cr ":w:Kc.}(Og@7w}}y!c]Q" ');

        $salida3 = exec('java -jar ' . storage_path('app/facturaelectronica/xadessignercr.jar') . ' query https://api.comprobanteselectronicos.go.cr/recepcion-sandbox/v1 ' . storage_path('app/facturaelectronica/out.xml') . ' cpf-02-0553-0597@stag.comprobanteselectronicos.go.cr ":w:Kc.}(Og@7w}}y!c]Q" ');*/

        if (Storage::disk('local')->exists('facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '_signed.xml')) {
            
            $xmlSigned = Storage::disk('local')->get('facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '_signed.xml');
            
            //Storage::disk('s3')->put('facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '_signed.xml', $xmlSigned);
           

            return $xmlSigned;

        } else {
            //dd('Error al firmar el xml de la factura. Ponte en contacto con el proveedor');

            throw new \App\Exceptions\SignerException('Factura xml no encontrada. Firmador no la creo');
            
           // return response(['message' => 'Error al firmar el xml de la factura. Ponte en contacto con el proveedor!!'], 500);
            
        }
    }

    public function signMensajeXML($configFactura, $receptor)
    {
        $cert = 'cert';
        $pin = $configFactura->pin_certificado;

       
        $schema = $this->schema_mensaje_receptor;
             

        $salida = exec('java -jar ' . storage_path('app/facturaelectronica/xadessignercrv2.jar') . ' sign ' . storage_path('app/facturaelectronica/' . $configFactura->id . '/' . $cert . '.p12') . ' ' . $pin . ' ' . storage_path('app/facturaelectronica/' . $configFactura->id . '/receptor/gpsm_mensaje_' . $receptor->Clave . '.xml') . ' ' . storage_path('app/facturaelectronica/' . $configFactura->id . '/receptor/gpsm_mensaje_' . $receptor->Clave . '_signed.xml') . ' ' . $schema);

        \Log::info('results of xadessignercr: ' . json_encode($salida));

        /*$salida2 = exec('java -jar ' . storage_path('app/facturaelectronica/xadessignercr.jar') . ' send https://api.comprobanteselectronicos.go.cr/recepcion-sandbox/v1 '. storage_path('app/facturaelectronica/out.xml') . ' cpf-02-0553-0597@stag.comprobanteselectronicos.go.cr ":w:Kc.}(Og@7w}}y!c]Q" ');

        $salida3 = exec('java -jar ' . storage_path('app/facturaelectronica/xadessignercr.jar') . ' query https://api.comprobanteselectronicos.go.cr/recepcion-sandbox/v1 ' . storage_path('app/facturaelectronica/out.xml') . ' cpf-02-0553-0597@stag.comprobanteselectronicos.go.cr ":w:Kc.}(Og@7w}}y!c]Q" ');*/

        if (Storage::disk('local')->exists('facturaelectronica/' . $configFactura->id . '/receptor/gpsm_mensaje_' . $receptor->Clave . '_signed.xml')) {

            $xmlSigned = Storage::disk('local')->get('facturaelectronica/' . $configFactura->id . '/receptor/gpsm_mensaje_' . $receptor->Clave . '_signed.xml');

           // Storage::disk('s3')->put('facturaelectronica/' . $configFactura->id . '/receptor/gpsm_mensaje_' . $receptor->Clave . '_signed.xml', $xmlSigned);


            return $xmlSigned;

        } else {
            //dd('Error al firmar el xml de la factura. Ponte en contacto con el proveedor');

            throw new \App\Exceptions\SignerException('Factura xml no encontrada. Firmador no la creo');
            
           // return response(['message' => 'Error al firmar el xml de la factura. Ponte en contacto con el proveedor!!'], 500);

        }
    }
}
