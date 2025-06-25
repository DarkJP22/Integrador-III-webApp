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

class NotaCredito
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
     * @var string $condicionVenta
     */
    public $condicionVenta;

    /**
     * Undocumented variable
     *
     * @var string $plazoCredito
     */
    public $plazoCredito;

    /**
     * Undocumented variable
     *
     * @var string $medioPago
     */
    public $medioPago;

    /**
     * Undocumented variable
     *
     * @var string $detalleServicio
     */
    public $detalleServicio;

    /**
     * Undocumented variable
     *
     * @var string $resumenFactura
     */
    public $resumenFactura;

    /**
     * Undocumented variable
     *
     * @var string $normativa
     */
    public $normativa;

    /**
     * Undocumented variable
     *
     * @var string $otros
     */
    public $otros;

    /**
     * Undocumented variable
     *
     * @var string $signature
     */
    public $signature;

    /**
     * Undocumented variable
     *
     * @var string $establecimiento
     */
    public $establecimiento;
    public $pos;
    public $consecutivoHacienda;

    public function __construct($emisorId, $receptorId = null, $numeroConsecutivo, $fechaEmision = '')
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
    public function getClave($situacionComprobante = '1', $establecimiento = '1', $pos = '1')
    {
        $this->establecimiento = $establecimiento;
        $this->pos = $pos;
        $this->clave = Common::generarClave($this->fechaEmision, $this->emisor, Common::NOTA_CREDITO, $this->numeroConsecutivo, $situacionComprobante, getUniqueNumber(8), $this->establecimiento, $this->pos);
        $this->consecutivoHacienda = Common::generarConsecutivo(Common::NOTA_CREDITO, $this->numeroConsecutivo, $this->establecimiento, $this->pos);

        return $this;
    }

    public function generateXML($configFactura, $invoice)
    {
        $facuraBase = Storage::disk('local')->get('facturaelectronica/nota_credito.xml');

        $facturaXML = new \SimpleXMLElement($facuraBase);
        $facturaXML->Clave = $this->clave;
        $facturaXML->NumeroConsecutivo = $this->consecutivoHacienda; //$this->numeroConsecutivo;
        $facturaXML->FechaEmision = Carbon::createFromFormat('dmy', $this->fechaEmision)->toAtomString();

        $facturaXML->Emisor->Nombre = $configFactura->nombre;
        $facturaXML->Emisor->Identificacion->Tipo = $configFactura->tipo_identificacion;
        $facturaXML->Emisor->Identificacion->Numero = $configFactura->identificacion;//'205530597';
        $facturaXML->Emisor->NombreComercial = $configFactura->nombre_comercial ? $configFactura->nombre_comercial : '';
        $facturaXML->Emisor->Ubicacion->Provincia = $configFactura->provincia;
        $facturaXML->Emisor->Ubicacion->Canton = $configFactura->canton; //bagaces
        $facturaXML->Emisor->Ubicacion->Distrito = $configFactura->distrito;//'03'; //guayabo mogote
        if ($configFactura->barrio) {
            $facturaXML->Emisor->Ubicacion->Barrio = $configFactura->barrio;
        } else {
            unset($facturaXML->Emisor->Ubicacion->Barrio);
        }
        $facturaXML->Emisor->Ubicacion->OtrasSenas = $configFactura->otras_senas;

        if ($configFactura->codigo_pais_tel && $configFactura->telefono) {
            $facturaXML->Emisor->Telefono->CodigoPais = $configFactura->codigo_pais_tel;
            $facturaXML->Emisor->Telefono->NumTelefono = $configFactura->telefono;
        } else {
            unset($facturaXML->Emisor->Telefono);
        }
        if ($configFactura->codigo_pais_fax && $configFactura->fax) {
            $facturaXML->Emisor->Fax->CodigoPais = $configFactura->codigo_pais_fax;
            $facturaXML->Emisor->Fax->NumTelefono = $configFactura->fax;
        } else {
            unset($facturaXML->Emisor->Fax);
        }

        $facturaXML->Emisor->CorreoElectronico = $configFactura->email;

       if ($invoice->identificacion_cliente) {

            $facturaXML->Receptor->Nombre = $invoice->cliente;
            $facturaXML->Receptor->Identificacion->Tipo = $invoice->tipo_identificacion_cliente;
            $facturaXML->Receptor->Identificacion->Numero = $invoice->identificacion_cliente;

            if ($invoice->email) {
                $facturaXML->Receptor->CorreoElectronico = $invoice->email;
            } else {
                unset($facturaXML->Receptor->CorreoElectronico);
            }
           
            

        } elseif($invoice->cliente) {
            
            
            $facturaXML->Receptor->Nombre = $invoice->cliente;
            
            unset($facturaXML->Receptor->Identificacion);
            
            if ($invoice->email) {
                $facturaXML->Receptor->CorreoElectronico = $invoice->email;
            } else {
                unset($facturaXML->Receptor->CorreoElectronico);
            }
          
        }else{
            unset($facturaXML->Receptor);
        }

       $facturaXML->CondicionVenta = $invoice->CondicionVenta; //'01' //contado 02 credito

        $facturaXML->MedioPago = $invoice->MedioPago; //01 efectivo 02 tarjeta

        if ($invoice->CondicionVenta == '02') {
            $facturaXML->PlazoCredito = $invoice->PlazoCredito ? $invoice->PlazoCredito : '0';
        }else{
            unset($facturaXML->PlazoCredito);
        }

        //$facturaXML->DetalleServicio;

        foreach ($invoice->lines as $key => $detail) {
            $detalle = $facturaXML->DetalleServicio->addChild('LineaDetalle');
            $detalle->addChild('NumeroLinea', $key + 1);
            $codigo = $detalle->addChild('Codigo');
            $codigo->addChild('Tipo', $detail->CodigoTipo);
            $codigo->addChild('Codigo', $detail->Codigo);
            $detalle->addChild('Cantidad', $detail->Cantidad);
            $detalle->addChild('UnidadMedida', $detail->UnidadMedida);
            $detalle->addChild('UnidadMedidaComercial', '');
            $detalle->addChild('Detalle', htmlspecialchars($detail->Detalle));
            $detalle->addChild('PrecioUnitario', $detail->PrecioUnitario);
            $detalle->addChild('MontoTotal', $detail->MontoTotal);

            if ($detail->MontoDescuento > 0) {
                $detalle->addChild('MontoDescuento', $detail->MontoDescuento);
                $detalle->addChild('NaturalezaDescuento', $detail->MontoDescuento ? ($detail->NaturalezaDescuento ? $detail->NaturalezaDescuento : 'Descuento Cliente') : '');
            }
            
            $detalle->addChild('SubTotal', $detail->SubTotal);

            if (!$detail->exo) {
                foreach ($detail->taxes as $key => $tax) {
                    $impuesto = $detalle->addChild('Impuesto');
                    $impuesto->addChild('Codigo', $tax->code);
                    $impuesto->addChild('Tarifa', $tax->tarifa);
                    $impuesto->addChild('Monto', $tax->amount);
                }
            }
            
            $detalle->addChild('MontoTotalLinea', $detail->MontoTotalLinea);
        }

        $facturaXML->ResumenFactura->CodigoMoneda = $invoice->CodigoMoneda;
        $facturaXML->ResumenFactura->TipoCambio = $invoice->TipoCambio;
        $facturaXML->ResumenFactura->TotalServGravados = $invoice->TotalServGravados;
        $facturaXML->ResumenFactura->TotalServExentos = $invoice->TotalServExentos;
        $facturaXML->ResumenFactura->TotalMercanciasGravadas = $invoice->TotalMercanciasGravadas;
        $facturaXML->ResumenFactura->TotalMercanciasExentas = $invoice->TotalMercanciasExentas;
        $facturaXML->ResumenFactura->TotalGravado = $invoice->TotalGravado;
        $facturaXML->ResumenFactura->TotalExento = $invoice->TotalExento;
        $facturaXML->ResumenFactura->TotalVenta = $invoice->TotalVenta;
        $facturaXML->ResumenFactura->TotalDescuentos = $invoice->TotalDescuentos;
        $facturaXML->ResumenFactura->TotalVentaNeta = $invoice->TotalVentaNeta;
        $facturaXML->ResumenFactura->TotalImpuesto = $invoice->TotalImpuesto;
        $facturaXML->ResumenFactura->TotalComprobante = $invoice->TotalComprobante;

        foreach ($invoice->referencias as $key => $doc) {
            $InformacionReferencia = $facturaXML->InformacionReferencia;
            $InformacionReferencia->addChild('TipoDoc', $doc->TipoDocumento);
            $InformacionReferencia->addChild('Numero', $doc->NumeroDocumento);
            $InformacionReferencia->addChild('FechaEmision', Carbon::parse($doc->FechaEmision)->toAtomString());
            $InformacionReferencia->addChild('Codigo', $doc->CodigoReferencia);
            $InformacionReferencia->addChild('Razon', $doc->Razon);
        }

        $facturaXML->Normativa->NumeroResolucion = 'DGT-R-48-2016';
        $facturaXML->Normativa->FechaResolucion = Carbon::now()->format('d-m-Y h:i:s');//->toDateTimeString();
        $facturaXML->Otros->OtroTexto = 'Id y Consecutivo Sistema Interno: ' . $invoice->id . '-' . $invoice->consecutivo;

        Storage::disk('local')->put('facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '.xml', $facturaXML->asXML());

        if (Storage::disk('local')->exists('facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '.xml')) {
            return Storage::disk('local')->get('facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '.xml');
        } else {
            dd('Error al generar el xml de la factura. Ponte en contacto con el proveedor');
        }
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
