<?php

namespace App\Services;

use App\Traits\AuthorizesHaciendaRequests;
use App\Traits\ConsumesExternalServices;
use App\Traits\InteractsWithHaciendaResponses;
use Illuminate\Support\Facades\Storage;
use App\FacturaElectronica\Common;
use App\FacturaElectronica\Firmadocr;
use App\Receptor;
use App\FacturaElectronica\MensajeReceptor;

class MensajeReceptorService
{
    use ConsumesExternalServices, AuthorizesHaciendaRequests, InteractsWithHaciendaResponses;

    /**
     * The url from which send the requests
     * @var string
     */
    protected $baseUri;

    public function __construct()
    {
        $this->baseUri = config('services.hacienda.base_uri');
       
    }

    /**
     * Obtains the list of products from the API
     * @return stdClass
     */
    public function sendDocument(Receptor $receptor)
    {
        $receptor->refresh();

        $signedMensajeXML = $this->generateXML($receptor);

        $formParams = $this->getFormParams($signedMensajeXML, $receptor);

        return $this->makeRequest('POST', 'recepcion', [], $formParams, [], false, true, $receptor->obligadoTributario);
    }

    /**
     * Obtains the list of products from the API
     * @return stdClass
     */
    public function recepcionDocument($clave, $obligadoTributario = null)
    {
        
        return $this->makeRequest('GET', 'recepcion/'.$clave, [], [], [], false, false, $obligadoTributario);
    }

    public function getFormParams($signedMensajeXML, $receptor)
    {
        $mensajeXML = new \SimpleXMLElement($signedMensajeXML);
        $mensaje64String = $this->parseBase64($signedMensajeXML);

        $formParams = [
            'clave' => (string)$mensajeXML->Clave,
            'fecha' => (string)$mensajeXML->FechaEmisionDoc,
            'emisor' => [
                'tipoIdentificacion' => $receptor->tipo_identificacion_emisor,
                'numeroIdentificacion' => (string)Common::validarId($mensajeXML->NumeroCedulaEmisor),
            ],
            'receptor' => [
                'tipoIdentificacion' => $receptor->tipo_identificacion_receptor,
                'numeroIdentificacion' => (string)Common::validarId($mensajeXML->NumeroCedulaReceptor)
            ],
            'callbackUrl' => route('haciendamensajeresponse'),
            "consecutivoReceptor" => (string)$mensajeXML->NumeroConsecutivoReceptor,
            'comprobanteXml' => $mensaje64String
        ];


        return $formParams;
    }

    public function generateXML($receptor)
    {

        $obligadoTributario = $receptor->obligadoTributario;


        if ($receptor->created_xml && Storage::disk('local')->exists('facturaelectronica/' . $obligadoTributario->id . '/receptor/gpsm_mensaje_' . $receptor->clave . '_signed.xml')) {

            $mensaje = $receptor->Mensaje;

            if ($mensaje == '1') {
                $tipoDocumento = '05';
            }
            if ($mensaje == '2') {
                $tipoDocumento = '06';
            }
            if ($mensaje == '3') {
                $tipoDocumento = '07';
            }

            $signedMensajeXML = $this->signXML($obligadoTributario, $receptor, $tipoDocumento);
        } else {
            $signedMensajeXML = $this->createMensajeXML($obligadoTributario, $receptor);

            if ($signedMensajeXML) {
                $receptor->created_xml = 1;

                $receptor->save();
            }
        }

        return $signedMensajeXML;

    }


    public function createMensajeXML($obligadoTributario, $receptor)
    {

        $numeroCedulaEmisor = $receptor->NumeroCedulaEmisor;
        $numeroCedulaReceptor = $receptor->NumeroCedulaReceptor;

        $miNumeroConsecutivo = $receptor->consecutivo;
        $mensaje = $receptor->Mensaje;


        $mensajeReceptor = new MensajeReceptor($numeroCedulaEmisor, $numeroCedulaReceptor, $miNumeroConsecutivo);

        if ($mensaje == '1') {
            $tipoDocumento = '05';
        }
        if ($mensaje == '2') {
            $tipoDocumento = '06';
        }
        if ($mensaje == '3') {
            $tipoDocumento = '07';
        }


        $objDoc = $mensajeReceptor->getConsecutivo($tipoDocumento, $obligadoTributario->sucursal, $obligadoTributario->pos);


        $receptor->NumeroConsecutivoReceptor = $objDoc->consecutivoHacienda;
        $receptor->Clave_Mensaje = $receptor->Clave . '-' . $objDoc->consecutivoHacienda;

        $receptor->save();

        $mensajeReceptor->generateXML($obligadoTributario, $receptor);

        $signedMensajeXML = $this->signXML($obligadoTributario, $receptor, $tipoDocumento);

        //\Log::info('results of invoiceXML: ' . json_encode($signedinvoiceXML));

        return $signedMensajeXML;

    }


    public function signXML($configFactura, $receptor, $tipoDocumento)
    {
        $cert = 'cert';
        $pin = $configFactura->pin_certificado;
       
        $fac = new Firmadocr();
        $inXml = $this->parseBase64(Storage::disk('local')->get('facturaelectronica/' . $configFactura->id . '/receptor/gpsm_mensaje_' . $receptor->Clave . '.xml'));
    
        $returnBase64Xml = $fac->firmar(storage_path('app/facturaelectronica/' . $configFactura->id . '/' . $cert . '.p12'), $pin, $inXml, $tipoDocumento);
 
        $facturaXML = $this->decodeRespuestaXML($returnBase64Xml);

        Storage::disk('local')->put('facturaelectronica/' . $configFactura->id . '/receptor/gpsm_mensaje_' . $receptor->Clave . '_signed.xml', $facturaXML->asXML());

        if (Storage::disk('local')->exists('facturaelectronica/' . $configFactura->id . '/receptor/gpsm_mensaje_' . $receptor->Clave . '_signed.xml')) {

            $xmlSigned = Storage::disk('local')->get('facturaelectronica/' . $configFactura->id . '/receptor/gpsm_mensaje_' . $receptor->Clave . '_signed.xml');
            
        
            return $xmlSigned;

        } else {
    
            throw new \App\Exceptions\SignerException('Mensaje Receptor xml no encontrada. Firmador no la creo');
            

        }
    }

    public function parseBase64($document)
    {
        //parse byte_array to base64
        $base64 = base64_encode($document);
        return $base64;
    }

    public function decodeRespuestaXML($respuestaBase64)
    {
        $documentXML = new \SimpleXMLElement(base64_decode($respuestaBase64));

        return $documentXML;
    }
    

    
}
