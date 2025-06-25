<?php

namespace App\Services;

use App\Traits\AuthorizesHaciendaRequests;
use App\Traits\ConsumesExternalServices;
use App\Traits\InteractsWithHaciendaResponses;
use App\Invoice;
use Illuminate\Support\Facades\Storage;
use App\FacturaElectronica\Common;
use App\FacturaElectronica\Firmadocr;
use App\FacturaElectronica\DocumentoEletronico;
use App\Receptor;

class FacturaService
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
    public function sendDocument(Invoice $invoice)
    {
        $invoice->refresh();

        $signedinvoiceXML = $this->generateXML($invoice);

        $formParams = $this->getFormParams($signedinvoiceXML);

        return $this->makeRequest('POST', 'recepcion', [] , $formParams, [], false, true, $invoice->obligadoTributario);
    }

    /**
     * Obtains the list of products from the API
     * @return stdClass
     */
    public function recepcionDocument($clave, $obligadoTributario = null)
    {
        
        return $this->makeRequest('GET', 'recepcion/'. $clave, [], [], [], false, false, $obligadoTributario);
    }

    public function getFormParams($signedinvoiceXML)
    {
        $invoiceXML = new \SimpleXMLElement($signedinvoiceXML);
        $invoice64String = $this->parseBase64($signedinvoiceXML);

        $formParams = [
            'clave' => (string)$invoiceXML->Clave, 
            'fecha' => (string)$invoiceXML->FechaEmision, 
            'emisor' => [
                'tipoIdentificacion' => (string)$invoiceXML->Emisor->Identificacion->Tipo,
                'numeroIdentificacion' => (string)Common::validarId($invoiceXML->Emisor->Identificacion->Numero),
            ],

            'callbackUrl' => route('haciendaresponse'),
            'comprobanteXml' => $invoice64String
        ];

        if ((string)$invoiceXML->Receptor && (string)Common::validarId($invoiceXML->Receptor->Identificacion->Numero)) {
            
            $receptor = [
                'tipoIdentificacion' => (string)$invoiceXML->Receptor->Identificacion->Tipo,
                'numeroIdentificacion' => (string)Common::validarId($invoiceXML->Receptor->Identificacion->Numero)
            ];
               
            $formParams['receptor'] = $receptor;

        } 

        return $formParams;

    }

    public function generateXML($invoice)
    {
        $obligadoTributario = $invoice->obligadoTributario;

        if ($invoice->created_xml && Storage::disk('local')->exists('facturaelectronica/' . $obligadoTributario->id . '/gpsm_' . $invoice->clave_fe . '_signed.xml')) {
            $invoiceXML = Storage::disk('local')->get('facturaelectronica/' . $obligadoTributario->id . '/gpsm_' . $invoice->clave_fe . '.xml');
            $invoiceXML = new \SimpleXMLElement($invoiceXML);

            $situacionComprobante = ($invoice->sent_to_hacienda) ? '1' : '3';

            $invoiceXML->Clave = substr_replace((string)$invoiceXML->Clave, $situacionComprobante, 41, 1); // cambiar la situacion del comprobante 1- normal 2- contigencia 3 -no internet
            Storage::disk('local')->put('facturaelectronica/' . $obligadoTributario->id . '/gpsm_' . $invoiceXML->Clave . '.xml', $invoiceXML->asXML());
            $invoice->clave_fe = (string)$invoiceXML->Clave;
            $invoice->save();

            $signedinvoiceXML = $this->signXML($obligadoTributario, $invoice);


        } elseif(!$invoice->created_xml && Storage::disk('local')->exists('facturaelectronica/' . $obligadoTributario->id . '/gpsm_' . $invoice->clave_fe . '.xml')) {
           
            $signedinvoiceXML = $this->signXML($obligadoTributario, $invoice);
            
            if ($signedinvoiceXML) {
                $invoice->created_xml = 1;

                $invoice->save();
            }
        }else{
           
            $signedinvoiceXML = $this->createFacturaXML($obligadoTributario, $invoice);
           
            if ($signedinvoiceXML) {
                $invoice->created_xml = 1;

                $invoice->save();
            }
        }

        return $signedinvoiceXML;
    }

    public function createFacturaXML($obligadoTributario, $invoice)
    {


        $numeroCedulaEmisor = $obligadoTributario->identificacion;

        $miNumeroConsecutivo = $invoice->consecutivo;

       
        $documentoEletronico = new DocumentoEletronico($numeroCedulaEmisor, null, $miNumeroConsecutivo);
        

        $objDoc = $documentoEletronico->getClave('1', $obligadoTributario->sucursal, $obligadoTributario->pos, $invoice->TipoDocumento);

        $invoice->clave_fe = $objDoc->clave;
        $invoice->NumeroConsecutivo = $objDoc->consecutivoHacienda;

        $invoice->save();

        $documentoEletronico->generateXML($obligadoTributario, $invoice);

        $signedinvoiceXML = $this->signXML($obligadoTributario, $invoice); 

        return $signedinvoiceXML;
    }


    public function signXML($configFactura, $invoice)
    {
        $cert = 'cert';
        $pin = $configFactura->pin_certificado;
       
        $fac = new Firmadocr();
        $inXml = $this->parseBase64(Storage::disk('local')->get('facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '.xml'));
    
        $returnBase64Xml = $fac->firmar(storage_path('app/facturaelectronica/' . $configFactura->id . '/' . $cert . '.p12'), $pin, $inXml, $invoice->TipoDocumento);
 
        $facturaXML = $this->decodeRespuestaXML($returnBase64Xml);

        Storage::disk('local')->put('facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '_signed.xml', $facturaXML->asXML());

        if (Storage::disk('local')->exists('facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '_signed.xml')) {

            $xmlSigned = Storage::disk('local')->get('facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '_signed.xml');
            
        
            return $xmlSigned;

        } else {
    
            throw new \App\Exceptions\SignerException('Factura xml no encontrada. Firmador no la creo');
            

        }
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

    public function decodeRespuestaXML($respuestaBase64)
    {
        $facturaXML = new \SimpleXMLElement(base64_decode($respuestaBase64));

        return $facturaXML;
    }
    

    
}
