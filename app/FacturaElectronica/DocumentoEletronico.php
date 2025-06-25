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

class DocumentoEletronico
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
    public function getClave($situacionComprobante = '1', $establecimiento = '1', $pos = '1', $tipoDocumento = '01')
    {
        $this->establecimiento = $establecimiento;
        $this->pos = $pos;
        $this->clave = Common::generarClave($this->fechaEmision, $this->emisor, $tipoDocumento, $this->numeroConsecutivo, $situacionComprobante, getUniqueNumber(8), $this->establecimiento, $this->pos);
        $this->consecutivoHacienda = Common::generarConsecutivo($tipoDocumento, $this->numeroConsecutivo, $this->establecimiento, $this->pos);

        return $this;
    }

    public function generateXML($configFactura, $invoice)
    {

        switch ($invoice->TipoDocumento) {
            case '01':
                $docBase = $this->getFacturaXMLString();//Storage::disk('local')->get('facturaelectronica/factura.xml');
                break;
            case '02':
                $docBase = $this->getNDXMLString();//Storage::disk('local')->get('facturaelectronica/nota_debito.xml');
                break;
            case '03':
                $docBase = $this->getNCXMLString();//Storage::disk('local')->get('facturaelectronica/nota_credito.xml');
                break;
            case '04':
                $docBase = $this->getTiqueteXMLString();//Storage::disk('local')->get('facturaelectronica/tiquete.xml');
                break;

            default:
                $docBase = $this->getFacturaXMLString();//Storage::disk('local')->get('facturaelectronica/factura.xml');
                break;
        }

        

        $facturaXML = new \SimpleXMLElement($docBase);
        $facturaXML->Clave = $this->clave;
        $facturaXML->CodigoActividad = fillZeroLeftNumber($invoice->CodigoActividad, 6);// $configFactura->CodigoActividad;
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
        }else{
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

        if ($invoice->identificacion_cliente && $invoice->tipo_identificacion_cliente != '00') {

            $facturaXML->Receptor->Nombre = $invoice->cliente;
            $facturaXML->Receptor->Identificacion->Tipo = $invoice->tipo_identificacion_cliente;
            $facturaXML->Receptor->Identificacion->Numero = $invoice->identificacion_cliente;
            
            unset($facturaXML->Receptor->IdentificacionExtranjero);

            if ($invoice->email) {
                $facturaXML->Receptor->CorreoElectronico = $invoice->email;
            } else {
                unset($facturaXML->Receptor->CorreoElectronico);
            }
           
            

        } elseif($invoice->cliente) {
            
            
            $facturaXML->Receptor->Nombre = $invoice->cliente;

            if($invoice->tipo_identificacion_cliente == '00'){
                $facturaXML->Receptor->IdentificacionExtranjero = $invoice->identificacion_cliente;
            }else{
                unset($facturaXML->Receptor->IdentificacionExtranjero);
            }
            
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
            if($detail->CodigoProductoHacienda){
                $detalle->addChild('Codigo', $detail->CodigoProductoHacienda); // opcional hasta 2020
            }
            $codigo = $detalle->addChild('CodigoComercial');
            $codigo->addChild('Tipo', $detail->CodigoTipo);
            $codigo->addChild('Codigo', $detail->Codigo);
            $detalle->addChild('Cantidad', $detail->Cantidad);
            $detalle->addChild('UnidadMedida', $detail->UnidadMedida);
            $detalle->addChild('UnidadMedidaComercial', '');
            $detalle->addChild('Detalle', htmlspecialchars($detail->Detalle));
            $detalle->addChild('PrecioUnitario', $detail->PrecioUnitario);
            $detalle->addChild('MontoTotal', $detail->MontoTotal);


            if ($detail->MontoDescuento > 0) {
                
                foreach ($detail->discounts as $key => $discount) {

                    $descuento = $detalle->addChild('Descuento');
                    $descuento->addChild('MontoDescuento', $discount->MontoDescuento);
                    $descuento->addChild('NaturalezaDescuento', $discount->NaturalezaDescuento ? $discount->NaturalezaDescuento : 'Descuento Cliente');

                }
              
            }

            $detalle->addChild('SubTotal', $detail->SubTotal);
            $detalle->addChild('BaseImponible', $detail->BaseImponible);
            

            //if(!$detail->exo){
                foreach ($detail->taxes as $key => $tax) {

                    $impuesto = $detalle->addChild('Impuesto');
                    $impuesto->addChild('Codigo', $tax->code);
                    $impuesto->addChild('CodigoTarifa', $tax->CodigoTarifa);
                    $impuesto->addChild('Tarifa', $tax->tarifa);
                    $impuesto->addChild('Monto', $tax->amount);

                    // if ($tax->code == '07') {
                    //     $detalle->addChild('BaseImponible', 0);
                    // }

                    if($detail->exo){
                       
                            $exoneracion = $impuesto->addChild('Exoneracion');
                            $exoneracion->addChild('TipoDocumento', $tax->TipoDocumento);
                            $exoneracion->addChild('NumeroDocumento', $tax->NumeroDocumento);
                            $exoneracion->addChild('NombreInstitucion', $tax->NombreInstitucion);
                            $exoneracion->addChild('FechaEmision', Carbon::parse($tax->FechaEmision)->toAtomString());
                            // $exoneracion->addChild('MontoImpuesto', $tax->ImpuestoOriginal);
                            // $exoneracion->addChild('PorcentajeCompra', intval($tax->PorcentajeExoneracion));

                            /** para version 4.3  */
                            $exoneracion->addChild('PorcentajeExoneracion', intval($tax->PorcentajeExoneracion));
                            $exoneracion->addChild('MontoExoneracion', $tax->MontoExoneracion);
                            
                       
                    }



                    $detalle->addChild('ImpuestoNeto', $tax->ImpuestoNeto);
                }
            //}
            
            $detalle->addChild('MontoTotalLinea', $detail->MontoTotalLinea);
        }

        $facturaXML->ResumenFactura->CodigoTipoMoneda->CodigoMoneda = $invoice->CodigoMoneda;
        $facturaXML->ResumenFactura->CodigoTipoMoneda->TipoCambio = $invoice->TipoCambio;
        $facturaXML->ResumenFactura->TotalServGravados = $invoice->TotalServGravados;
        $facturaXML->ResumenFactura->TotalServExentos = $invoice->TotalServExentos;
        $facturaXML->ResumenFactura->TotalServExonerado = $invoice->TotalServExonerado;
        $facturaXML->ResumenFactura->TotalMercanciasGravadas = $invoice->TotalMercanciasGravadas;
        $facturaXML->ResumenFactura->TotalMercanciasExentas = $invoice->TotalMercanciasExentas;
        $facturaXML->ResumenFactura->TotalMercExonerada = $invoice->TotalMercExonerada;
        $facturaXML->ResumenFactura->TotalGravado = $invoice->TotalGravado;
        $facturaXML->ResumenFactura->TotalExento = $invoice->TotalExento;
        $facturaXML->ResumenFactura->TotalExonerado = $invoice->TotalExonerado;
        $facturaXML->ResumenFactura->TotalVenta = $invoice->TotalVenta;
        $facturaXML->ResumenFactura->TotalDescuentos = $invoice->TotalDescuentos;
        $facturaXML->ResumenFactura->TotalVentaNeta = $invoice->TotalVentaNeta;
        $facturaXML->ResumenFactura->TotalImpuesto = $invoice->TotalImpuesto;

        if($invoice->TotalIVADevuelto){
            $facturaXML->ResumenFactura->TotalIVADevuelto = $invoice->TotalIVADevuelto;
        }else{
            //unset($facturaXML->ResumenFactura->TotalIVADevuelto);
        }

        $facturaXML->ResumenFactura->TotalComprobante = $invoice->TotalComprobante;

        // if($invoice->TipoDocumento == '02' || $invoice->TipoDocumento =='03') quitamos el if por que una factura puede tener referencia a un documento de contingencia
        // {
           
                foreach ($invoice->referencias as $key => $doc) {
                
                    $InformacionReferencia = $facturaXML->addChild('InformacionReferencia');
                    $InformacionReferencia->addChild('TipoDoc', $doc->TipoDocumento);
                    $InformacionReferencia->addChild('Numero', $doc->NumeroDocumento);
                    $InformacionReferencia->addChild('FechaEmision', Carbon::parse($doc->FechaEmision)->toAtomString());
                    $InformacionReferencia->addChild('Codigo', $doc->CodigoReferencia);
                    $InformacionReferencia->addChild('Razon', $doc->Razon);
                }
          
       // }

        //$facturaXML->Normativa->NumeroResolucion = 'DGT-R-48-2016';
        //$facturaXML->Normativa->FechaResolucion = Carbon::now()->format('d-m-Y h:i:s');//->toDateTimeString();
        $Otros = $facturaXML->addChild('Otros');
        $Otros->addChild('OtroTexto', 'Id y Consecutivo Sistema Interno: ' . $invoice->id . '-' . $invoice->consecutivo);

        Storage::disk('local')->put('facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '.xml', $facturaXML->asXML());

        if (Storage::disk('local')->exists('facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '.xml')) {
            return Storage::disk('local')->get('facturaelectronica/' . $configFactura->id . '/gpsm_' . $invoice->clave_fe . '.xml');
        } else {
            dd('Error al generar el xml de la factura. Ponte en contacto con el proveedor');
        }
    }


    //Documentos XML

    public function getFacturaXMLString()
    {
        $doc = '<?xml version="1.0" encoding="UTF-8"?>
                <FacturaElectronica xsi:schemaLocation="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/facturaElectronica  https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.3/facturaElectronica.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/facturaElectronica">
                <Clave>50627051900070150060400100001010000002115100002278</Clave>
                <CodigoActividad>523601</CodigoActividad>
                <NumeroConsecutivo>00100001010000002115</NumeroConsecutivo>
                <FechaEmision>2019-05-27T21:13:18.6078-06:00</FechaEmision>
                <Emisor>
                    <Nombre>Nombre</Nombre>
                    <Identificacion>
                        <Tipo>01</Tipo>
                        <Numero>503600225</Numero>
                    </Identificacion>
                    <NombreComercial>Nombre Comercial</NombreComercial>
                    <Ubicacion>
                        <Provincia>1</Provincia>
                        <Canton>01</Canton>
                        <Distrito>01</Distrito>
                        <Barrio>01</Barrio>
                        <OtrasSenas>Barrio</OtrasSenas>
                    </Ubicacion>
                    <Telefono>
                        <CodigoPais>506</CodigoPais>
                        <NumTelefono>12345678</NumTelefono>
                    </Telefono>
                    <Fax>
                        <CodigoPais>506</CodigoPais>
                        <NumTelefono>12345678</NumTelefono>
                    </Fax>
                    <CorreoElectronico>info@test.com</CorreoElectronico>
                </Emisor>
                <Receptor>
                    <Nombre>Cliente</Nombre>
                    <Identificacion>
                        <Tipo>01</Tipo>
                        <Numero>503600225</Numero>
                    </Identificacion>
                    <IdentificacionExtranjero>12345678</IdentificacionExtranjero>
                    <CorreoElectronico>cliente@test.com</CorreoElectronico>
                </Receptor>
                <CondicionVenta>01</CondicionVenta>
                <PlazoCredito>test</PlazoCredito>
                <MedioPago>01</MedioPago>
                <DetalleServicio>

                </DetalleServicio>
                <ResumenFactura>
                    <CodigoTipoMoneda>
                        <CodigoMoneda>CRC</CodigoMoneda>
                        <TipoCambio>1</TipoCambio>
                    </CodigoTipoMoneda>
                    <TotalServGravados>0</TotalServGravados>
                    <TotalServExentos>0</TotalServExentos>
                    <TotalServExonerado>0</TotalServExonerado>
                    <TotalMercanciasGravadas>0</TotalMercanciasGravadas>
                    <TotalMercanciasExentas>0</TotalMercanciasExentas>
                    <TotalMercExonerada>0</TotalMercExonerada>
                    <TotalGravado>0</TotalGravado>
                    <TotalExento>0</TotalExento>
                    <TotalExonerado>0</TotalExonerado>
                    <TotalVenta>0</TotalVenta>
                    <TotalDescuentos>0</TotalDescuentos>
                    <TotalVentaNeta>0</TotalVentaNeta>
                    <TotalImpuesto>0</TotalImpuesto>
                    <TotalIVADevuelto>0</TotalIVADevuelto>
                    <TotalComprobante>0</TotalComprobante>
                </ResumenFactura>
                </FacturaElectronica>';
        return $doc;
    }

    public function getTiqueteXMLString()
    {
        $doc = '<?xml version="1.0" encoding="UTF-8"?>
                <TiqueteElectronico xsi:schemaLocation="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/tiqueteElectronico  https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.3/tiqueteElectronico.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/tiqueteElectronico">
                <Clave>50627051900070150060400100001010000002115100002278</Clave>
                <CodigoActividad>523601</CodigoActividad>
                <NumeroConsecutivo>00100001010000002115</NumeroConsecutivo>
                <FechaEmision>2019-05-27T21:13:18.6078-06:00</FechaEmision>
                <Emisor>
                    <Nombre>Nombre</Nombre>
                    <Identificacion>
                        <Tipo>01</Tipo>
                        <Numero>503600225</Numero>
                    </Identificacion>
                    <NombreComercial>Nombre Comercial</NombreComercial>
                    <Ubicacion>
                        <Provincia>1</Provincia>
                        <Canton>01</Canton>
                        <Distrito>01</Distrito>
                        <Barrio>01</Barrio>
                        <OtrasSenas>Barrio</OtrasSenas>
                    </Ubicacion>
                    <Telefono>
                        <CodigoPais>506</CodigoPais>
                        <NumTelefono>12345678</NumTelefono>
                    </Telefono>
                    <Fax>
                        <CodigoPais>506</CodigoPais>
                        <NumTelefono>12345678</NumTelefono>
                    </Fax>
                    <CorreoElectronico>info@test.com</CorreoElectronico>
                </Emisor>
                <Receptor>
                    <Nombre>Cliente</Nombre>
                    <Identificacion>
                        <Tipo>01</Tipo>
                        <Numero>503600225</Numero>
                    </Identificacion>
                    <IdentificacionExtranjero>12345678</IdentificacionExtranjero>
                    <CorreoElectronico>cliente@test.com</CorreoElectronico>
                </Receptor>
                <CondicionVenta>01</CondicionVenta>
                <PlazoCredito>test</PlazoCredito>
                <MedioPago>01</MedioPago>
                <DetalleServicio>

                </DetalleServicio>
                <ResumenFactura>
                    <CodigoTipoMoneda>
                        <CodigoMoneda>CRC</CodigoMoneda>
                        <TipoCambio>1</TipoCambio>
                    </CodigoTipoMoneda>
                    <TotalServGravados>0</TotalServGravados>
                    <TotalServExentos>0</TotalServExentos>
                    <TotalServExonerado>0</TotalServExonerado>
                    <TotalMercanciasGravadas>0</TotalMercanciasGravadas>
                    <TotalMercanciasExentas>0</TotalMercanciasExentas>
                    <TotalMercExonerada>0</TotalMercExonerada>
                    <TotalGravado>0</TotalGravado>
                    <TotalExento>0</TotalExento>
                    <TotalExonerado>0</TotalExonerado>
                    <TotalVenta>0</TotalVenta>
                    <TotalDescuentos>0</TotalDescuentos>
                    <TotalVentaNeta>0</TotalVentaNeta>
                    <TotalImpuesto>0</TotalImpuesto>
                    <TotalIVADevuelto>0</TotalIVADevuelto>
                    <TotalComprobante>0</TotalComprobante>
                </ResumenFactura>
                </TiqueteElectronico>';
        return $doc;
    }

    public function getNCXMLString()
    {
        $doc = '<?xml version="1.0" encoding="UTF-8"?>
                <NotaCreditoElectronica xsi:schemaLocation="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/notaCreditoElectronica  https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.3/notaCreditoElectronica.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/notaCreditoElectronica">
                <Clave>50627051900070150060400100001010000002115100002278</Clave>
                <CodigoActividad>523601</CodigoActividad>
                <NumeroConsecutivo>00100001010000002115</NumeroConsecutivo>
                <FechaEmision>2019-05-27T21:13:18.6078-06:00</FechaEmision>
                <Emisor>
                    <Nombre>Nombre</Nombre>
                    <Identificacion>
                        <Tipo>01</Tipo>
                        <Numero>503600225</Numero>
                    </Identificacion>
                    <NombreComercial>Nombre Comercial</NombreComercial>
                    <Ubicacion>
                        <Provincia>1</Provincia>
                        <Canton>01</Canton>
                        <Distrito>01</Distrito>
                        <Barrio>01</Barrio>
                        <OtrasSenas>Barrio</OtrasSenas>
                    </Ubicacion>
                    <Telefono>
                        <CodigoPais>506</CodigoPais>
                        <NumTelefono>12345678</NumTelefono>
                    </Telefono>
                    <Fax>
                        <CodigoPais>506</CodigoPais>
                        <NumTelefono>12345678</NumTelefono>
                    </Fax>
                    <CorreoElectronico>info@test.com</CorreoElectronico>
                </Emisor>
                <Receptor>
                    <Nombre>Cliente</Nombre>
                    <Identificacion>
                        <Tipo>01</Tipo>
                        <Numero>503600225</Numero>
                    </Identificacion>
                    <IdentificacionExtranjero>12345678</IdentificacionExtranjero>
                    <CorreoElectronico>cliente@test.com</CorreoElectronico>
                </Receptor>
                <CondicionVenta>01</CondicionVenta>
                <PlazoCredito>test</PlazoCredito>
                <MedioPago>01</MedioPago>
                <DetalleServicio>

                </DetalleServicio>
                <ResumenFactura>
                    <CodigoTipoMoneda>
                        <CodigoMoneda>CRC</CodigoMoneda>
                        <TipoCambio>1</TipoCambio>
                    </CodigoTipoMoneda>
                    <TotalServGravados>0</TotalServGravados>
                    <TotalServExentos>0</TotalServExentos>
                    <TotalServExonerado>0</TotalServExonerado>
                    <TotalMercanciasGravadas>0</TotalMercanciasGravadas>
                    <TotalMercanciasExentas>0</TotalMercanciasExentas>
                    <TotalMercExonerada>0</TotalMercExonerada>
                    <TotalGravado>0</TotalGravado>
                    <TotalExento>0</TotalExento>
                    <TotalExonerado>0</TotalExonerado>
                    <TotalVenta>0</TotalVenta>
                    <TotalDescuentos>0</TotalDescuentos>
                    <TotalVentaNeta>0</TotalVentaNeta>
                    <TotalImpuesto>0</TotalImpuesto>
                    <TotalIVADevuelto>0</TotalIVADevuelto>
                    <TotalComprobante>0</TotalComprobante>
                </ResumenFactura>
                </NotaCreditoElectronica>';
        return $doc;
    }

    public function getNDXMLString()
    {
        $doc = '<?xml version="1.0" encoding="UTF-8"?>
                <NotaDebitoElectronica xsi:schemaLocation="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/notaDebitoElectronica  https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.3/notaDebitoElectronica.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/notaDebitoElectronica">
                <Clave>50627051900070150060400100001010000002115100002278</Clave>
                <CodigoActividad>523601</CodigoActividad>
                <NumeroConsecutivo>00100001010000002115</NumeroConsecutivo>
                <FechaEmision>2019-05-27T21:13:18.6078-06:00</FechaEmision>
                <Emisor>
                    <Nombre>Nombre</Nombre>
                    <Identificacion>
                        <Tipo>01</Tipo>
                        <Numero>503600225</Numero>
                    </Identificacion>
                    <NombreComercial>Nombre Comercial</NombreComercial>
                    <Ubicacion>
                        <Provincia>1</Provincia>
                        <Canton>01</Canton>
                        <Distrito>01</Distrito>
                        <Barrio>01</Barrio>
                        <OtrasSenas>Barrio</OtrasSenas>
                    </Ubicacion>
                    <Telefono>
                        <CodigoPais>506</CodigoPais>
                        <NumTelefono>12345678</NumTelefono>
                    </Telefono>
                    <Fax>
                        <CodigoPais>506</CodigoPais>
                        <NumTelefono>12345678</NumTelefono>
                    </Fax>
                    <CorreoElectronico>info@test.com</CorreoElectronico>
                </Emisor>
                <Receptor>
                    <Nombre>Cliente</Nombre>
                    <Identificacion>
                        <Tipo>01</Tipo>
                        <Numero>503600225</Numero>
                    </Identificacion>
                    <IdentificacionExtranjero>12345678</IdentificacionExtranjero>
                    <CorreoElectronico>cliente@test.com</CorreoElectronico>
                </Receptor>
                <CondicionVenta>01</CondicionVenta>
                <PlazoCredito>test</PlazoCredito>
                <MedioPago>01</MedioPago>
                <DetalleServicio>

                </DetalleServicio>
                <ResumenFactura>
                    <CodigoTipoMoneda>
                        <CodigoMoneda>CRC</CodigoMoneda>
                        <TipoCambio>1</TipoCambio>
                    </CodigoTipoMoneda>
                    <TotalServGravados>0</TotalServGravados>
                    <TotalServExentos>0</TotalServExentos>
                    <TotalServExonerado>0</TotalServExonerado>
                    <TotalMercanciasGravadas>0</TotalMercanciasGravadas>
                    <TotalMercanciasExentas>0</TotalMercanciasExentas>
                    <TotalMercExonerada>0</TotalMercExonerada>
                    <TotalGravado>0</TotalGravado>
                    <TotalExento>0</TotalExento>
                    <TotalExonerado>0</TotalExonerado>
                    <TotalVenta>0</TotalVenta>
                    <TotalDescuentos>0</TotalDescuentos>
                    <TotalVentaNeta>0</TotalVentaNeta>
                    <TotalImpuesto>0</TotalImpuesto>
                    <TotalIVADevuelto>0</TotalIVADevuelto>
                    <TotalComprobante>0</TotalComprobante>
                </ResumenFactura>
                </NotaDebitoElectronica>';
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
