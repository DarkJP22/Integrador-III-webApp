<?php

namespace App\FacturaElectronica;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * Clase para generar Facturas Electrónicas
 *
 * @package clase
 * @author Open Source Costa Rica <opensource.lat@email.com>
 * @version 1.0.0
 * @license MIT
 * @copyright 2017 (c) Open Source Costa Rica
 *
 */

class MensajeReceptor
{
    /* ATRIBUTOS */

    /**
     * Corresponde a la clave del comprobante. Es un campo de 50 posiciones y se tiene que utilizar para la consulta
     * del código QR. Ver nota 1 y 4.1
     *
     * @var string $clave
     */
    public $clave;

    /**
     * Numeración consecutiva del comprobante
     *
     * @var string $numeroConsecutivo
     */
    public $numeroConsecutivo;

    /**
     * Undocumented variable
     *
     * @var string $fechaEmision
     */
    public $fechaEmision;

    /**
     * Undocumented variable
     *
     * @var string $emisor
     */
    public $emisor;

    /**
     * Undocumented variable
     *
     * @var string $receptor
     */
    public $receptor;

   
    /**
     * Undocumented variable
     *
     * @var string $establecimiento
     */
    public $establecimiento;
    public $pos;
    public $consecutivoHacienda;

    public function __construct($emisorId, $receptorId, $numeroConsecutivo, $fechaEmision = '')
    {
        $this->emisor = Common::validarId($emisorId);
        $this->receptor = ($receptorId) ? Common::validarId($receptorId) : null;
        $this->numeroConsecutivo = $numeroConsecutivo;
        $this->fechaEmision = ($fechaEmision == '') ? date('dmy') : $fechaEmision;
       
    }

    /* METODOS PUBLICOS */

    /**
     * Se obtiene la estructura de la clave
     * https://tribunet.hacienda.go.cr/docs/esquemas/2016/v4.1/Resolucion_Comprobantes_Electronicos_DGT-R-48-2016_v4.1.pdf
     * Página 7, Artículo 5° [Clave numérica]
     *
     * @param string $fechaComprobante Fecha en que se generó la factura electrónica (dd-mm-yyyy)
     * @return stting
     */
    public function getConsecutivo($tipoDocumento = '05', $establecimiento = '1', $pos = '1')
    {
        $this->establecimiento = $establecimiento;
        $this->pos = $pos;
        $this->consecutivoHacienda = Common::generarConsecutivo($tipoDocumento, $this->numeroConsecutivo, $this->establecimiento, $this->pos);

        return $this;
    }

    public function generateXML($configFactura, $receptor)
    {
        $facuraBase = $this->getMensajeReceptorXMLString();//Storage::disk('local')->get('facturaelectronica/mensaje_receptor.xml');
        //dd($facuraBase);
        $facturaXML = new \SimpleXMLElement($facuraBase);
        $facturaXML->Clave = $receptor->Clave;
        $facturaXML->NumeroCedulaEmisor = $receptor->NumeroCedulaEmisor;//$this->numeroConsecutivo;
        $facturaXML->FechaEmisionDoc = Carbon::createFromFormat('dmy', $this->fechaEmision)->toAtomString();
        $facturaXML->Mensaje = $receptor->Mensaje;
        $facturaXML->DetalleMensaje = $receptor->DetalleMensaje ? $receptor->DetalleMensaje : $receptor->MensajeName;

        if ($receptor->ExisteImpuesto) {

            $facturaXML->MontoTotalImpuesto = $receptor->MontoTotalImpuesto ? $receptor->MontoTotalImpuesto : 0;
        } else {
            unset($facturaXML->MontoTotalImpuesto);
        }

        if ($receptor->CodigoActividad) {
            $facturaXML->CodigoActividad = fillZeroLeftNumber($receptor->CodigoActividad, 6); //$configFactura->CodigoActividad;
        } else {
            unset($facturaXML->CodigoActividad);
        }

        if ($receptor->CondicionImpuesto) {
            $facturaXML->CondicionImpuesto = $receptor->CondicionImpuesto;
        } else {
            
            unset($facturaXML->CondicionImpuesto);
        }
        if ($receptor->MontoTotalImpuestoAcreditar) {

            $facturaXML->MontoTotalImpuestoAcreditar = $receptor->MontoTotalImpuestoAcreditar ? $receptor->MontoTotalImpuestoAcreditar : 0;
        } else {
            
           unset($facturaXML->MontoTotalImpuestoAcreditar);
        }

        if ($receptor->MontoTotalDeGastoAplicable) {

            $facturaXML->MontoTotalDeGastoAplicable = $receptor->MontoTotalDeGastoAplicable ? $receptor->MontoTotalDeGastoAplicable : 0;
        } else {
           
            unset($facturaXML->MontoTotalDeGastoAplicable);
        }
       
    
        $facturaXML->TotalFactura = $receptor->TotalFactura;
        $facturaXML->NumeroCedulaReceptor = $receptor->NumeroCedulaReceptor;
        $facturaXML->NumeroConsecutivoReceptor = $receptor->NumeroConsecutivoReceptor;
       

        Storage::disk('local')->put('facturaelectronica/' . $configFactura->id . '/receptor/gpsm_mensaje_' . $receptor->Clave . '.xml', $facturaXML->asXML());

        if (Storage::disk('local')->exists('facturaelectronica/' . $configFactura->id . '/receptor/gpsm_mensaje_' . $receptor->Clave . '.xml')) {
            return Storage::disk('local')->get('facturaelectronica/' . $configFactura->id . '/receptor/gpsm_mensaje_' . $receptor->Clave . '.xml');
        } else {
            dd('Error al generar el xml del mensaje. Ponte en contacto con el proveedor');
        }
    }

    public function getMensajeReceptorXMLString()
    {
        $doc = '<?xml version="1.0" encoding="utf-8"?>
                <MensajeReceptor xsi:schemaLocation="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/mensajeReceptor  https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.3/mensajeReceptor.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/mensajeReceptor">
                    <Clave>50630041800999999999900199999010000000081113004191</Clave>
                    <NumeroCedulaEmisor>9999999999</NumeroCedulaEmisor>
                    <FechaEmisionDoc>2018-05-24T14:47:00-06:00</FechaEmisionDoc>
                    <Mensaje>1</Mensaje>
                    <DetalleMensaje>test</DetalleMensaje>
                    <MontoTotalImpuesto>0.00000</MontoTotalImpuesto>
                    <CodigoActividad>123456</CodigoActividad>
                    <CondicionImpuesto>01</CondicionImpuesto>
                    <MontoTotalImpuestoAcreditar>0.00000</MontoTotalImpuestoAcreditar>
                    <MontoTotalDeGastoAplicable>0.00000</MontoTotalDeGastoAplicable>
                    <TotalFactura>1000.000</TotalFactura>
                    <NumeroCedulaReceptor>8888888888</NumeroCedulaReceptor>
                    <NumeroConsecutivoReceptor>00100001050000000001</NumeroConsecutivoReceptor>
                </MensajeReceptor>';
        return $doc;
    }


    public function imprimir()
    {
        echo 'Clave......: ', $this->clave, "\n";
        echo 'Emisor.....: ', $this->emisor, "\n";
        echo 'Receptor...: ', $this->receptor, "\n";
        echo 'Consecutivo: ', $this->numeroConsecutivo, "\n";
        echo "------------------------------ \n";
    }

    /* METODOS PRIVATOS */
}
