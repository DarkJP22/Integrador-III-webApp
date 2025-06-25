<template>
    <div class="invoice-form">
        <loading :show="loader"></loading> 
        <form-error v-if="errors.certificate" :errors="errors">
           
            <div class="callout callout-danger">
              <h4>Información importante!!</h4>

              <p> {{ errors.certificate[0] }}</p>
            </div>
        </form-error>
        <form @submit.prevent="save()" >
            <div class="row">
            
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Factura</h3> <small v-if="medic">({{ medic.name }})</small>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="TipoDocumento">Tipo Documento</label>
                                        <select class="form-control custom-select" name="TipoDocumento" id="TipoDocumento" v-model="invoice.TipoDocumento" :disabled="disableFields()">
                                    
                                        <option v-for="(value, key, index) in tipoDocumentos" :value="key">
                                            {{ value }}
                                        </option>
                                        
                                        </select>
                                    </div>
                                
                                     <div class="form-group col-md-4">
                                        <label for="MedioPago">Medio Pago</label>
                                        <select class="form-control custom-select" name="MedioPago" id="MedioPago" v-model="invoice.MedioPago" :disabled="disableFields()" @change="calculateInvoice(invoice.lines)">
                                    
                                        <option v-for="(value, key, index) in medioPagos" :value="key">
                                            {{ value }}
                                        </option>
                                        
                                        </select>
                                    </div>
                                   <div class="form-group col-md-4">
                                        <label for="CodigoMoneda">Moneda</label>
                                        <select class="form-control custom-select" name="CodigoMoneda" id="CodigoMoneda" v-model="invoice.CodigoMoneda" :disabled="disableFields()" @change="setTipoCambio()">
                                    
                                        <option v-for="(item, index) in currencies" :value="item.code">
                                            {{ item.name }}
                                        </option>
                                        
                                        </select>
                                    </div>
                                
                                </div>
                                <div class="form-row">
                                     <div class="form-group col-md-4">
                                        <label for="CondicionVenta">Condición de venta</label>
                                        <select class="form-control custom-select" name="CondicionVenta" id="CondicionVenta" v-model="invoice.CondicionVenta" :disabled="disableFields()">
                                    
                                        <option v-for="(value, key, index) in condicionVentas" :value="key">
                                            {{ value }}
                                        </option>
                                        
                                        </select>
                                    </div>
                                   
                                    <div class="form-group col-md-4">
                                        <label for="PlazoCredito">Plazo de credito</label>
                                        <!-- <input type="text" class="form-control" name="PlazoCredito" v-model="invoice.PlazoCredito" :disabled="invoice.CondicionVenta != '02' || disableFields()" > -->
                                        <flat-pickr
                                                v-model="invoice.PlazoCredito"                                             
                                                class="form-control" 
                                                placeholder="Selecione una fecha"               
                                                name="PlazoCredito">
                                        </flat-pickr>
                                    </div>
                                     <div class="form-group col-md-4">
                                        <label for="PlazoCredito">Abono Inicial</label>
                                        <input type="text" class="form-control" name="initialPayment" v-model="invoice.initialPayment" :disabled="invoice.CondicionVenta != '02' || disableFields()" >
                                    </div>
                                   
                                </div>
                                <div class="form-row">
                                     
                                    <div class="form-group" :class="!isMedic() ? 'col-md-6': 'col-md-12'">
                                        <label for="office_id">Consultorio</label>
                                        <select name="office_id" id="office_id" v-model="invoice.office_id" class="form-control" :disabled="disableFields()" required @change="loadDiscounts()">
                             
                                        <option :value="item.id" v-for="(item, index) in offices" :selected="index == 0 ? 'selected' : '' "> {{ item.name }}</option>
                                        
                                        </select>
                                    </div>
                                    <div class="form-group" v-show="!isMedic()" :class="!isMedic() ? 'col-md-6': 'col-md-12'">
                                        <label for="user_id">Médico</label>
                                        <select name="user_id" id="user_id" v-model="invoice.user_id" class="form-control" :disabled="disableFields()">
                                        <option value=""></option>
                                        <option :value="item.id" v-for="(item, index) in medics" :selected="index == 0 ? 'selected' : '' "> {{ item.name }}</option>
                                        
                                        </select>
                                    </div>
                                     <!-- <div class="form-group col-md-4">
                                       
                                            <div class="checkbox">
                                                <label>
                                                <input type="checkbox" v-model="invoice.laboratory" :disabled="disableFields()">
                                                Laboratorio
                                                </label>
                                            </div>

                                           
                                    </div> -->

                                </div>
                                <div class="form-row">
                                   
                                
                                    <div class="form-group col-md-12">
                                        <label for="observations">Observaciones</label>
                                         <textarea name="observations" class="form-control" v-model="invoice.observations" cols="30" rows="2" :disabled="disableFields()"></textarea>
                                       
                                            
                                        <form-error v-if="errors.observations" :errors="errors" style="float:right;">
                                            {{ errors.observations[0] }}
                                        </form-error>
                                    </div>
                                    
                                
                                </div>
                                
                            </div>
                        </div>
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Receptor</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="TipoDocumento">Tipo Identificacion</label>
                                        <select class="form-control custom-select" name="tipo_identificacion_cliente" id="tipo_identificacion_cliente" v-model="invoice.tipo_identificacion_cliente" @change="changeTipoIdentificacion()" >
                                        
                                        <option value=""></option>
                                        <option v-for="(value, key, index) in tipoIdentificaciones" :value="key">
                                            {{ value }}
                                        </option>
                                        
                                        </select>
                                        <form-error v-if="errors.tipo_identificacion_cliente" :errors="errors" style="float:right;">
                                            {{ errors.tipo_identificacion_cliente[0] }}
                                        </form-error>
                                    </div>
                                
                                    <div class="form-group col-md-4">
                                        <label for="cliente">Identificacion</label>
                                       
                                        <input type="text" class="form-control" id="identificacion_cliente" placeholder="" v-model="invoice.identificacion_cliente" @keyup="listenCliente($event)" @keydown.prevent.enter="searchCustomer(invoice.identificacion_cliente)" >
                                            
                                        <form-error v-if="errors.identificacion_cliente" :errors="errors" style="float:right;">
                                            {{ errors.identificacion_cliente[0] }}
                                        </form-error>
                                    </div>
                                    <div class="form-group col-md-4">
                                         <label for="email">Nombre Cliente</label>
                                        <div class="input-group input-group">
                                            <input type="text" class="form-control" name="cliente" v-model="invoice.cliente" @keyup="listenCliente($event)" @keydown.prevent.enter="searchCustomer(invoice.cliente)" >
                                            <div class="input-group-btn">
                                                <button type="button"
                                                        data-toggle="modal"
                                                        data-target="#customersModal" class="btn btn-primary" @click="showModalCustomers()">Buscar</button>
                                            </div>
                                        </div>
                                        <!-- <input type="text" class="form-control" name="cliente" v-model="invoice.cliente" @keyup="listenCliente($event)" @keydown.prevent.enter="searchCustomer(invoice.cliente)" > -->
                                        <form-error v-if="errors.cliente" :errors="errors" style="float:right;">
                                            {{ errors.cliente[0] }}
                                        </form-error>
                                    </div>
                                
                                </div>
                                <div class="form-row">
                                    
                                    <div class="form-group col-md-6">
                                        <label for="MedioPago">Email</label>
                                        <input type="email" class="form-control" name="email" v-model="invoice.email" >
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="MedioPago">Descuento Empresarial</label>
                                         <select class="form-control custom-select" name="discount_id" id="discount_id" v-model="invoice.discount_id" @change="setDiscount()" :disabled="disableFields() && !isCreatingNota" >
                                        
                                            <option value=""></option>
                                            <option v-for="(discount, index) in discounts" :value="discount.id" >
                                                {{ discount.name }} - {{ discount.tarifa }}%
                                            </option>
                                            
                                            </select>

                                    </div>
                                   
                                </div>
                                
                                
                            </div>
                        </div>
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Agregar Servicios</h3>
                                <button class="btn btn-primary btn-sm" 
                                        type="button"
                                        data-toggle="modal"
                                        data-target="#productsModal"
                                        @click="showModalProducts()"
                                        v-if="!disableFields() || isCreatingNota"
                                        ><i class="fa fa-plus"></i></button>
                                
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="thead-dark">
                                            <tr>
                                            <th scope="col"></th>
                                            <th scope="col" title="Actualizar impuesto">Imp.</th>
                                            <!-- <th scope="col" v-if="isCreatingNota" title="Actualizar stock del inventario">Act. Inven.</th> -->
                                            <th scope="col" title="Exonerar Linea">Exo.</th>
                                            <th scope="col">#</th>
                                            
                                            <!-- <th scope="col">Codigo</th> -->
                                            <th scope="col" >Detalle</th>
                                            <th scope="col" style="width:90px;">Cantidad</th>
                                            <th scope="col">Laboratorio</th>
                                            <th scope="col" style="width:90px;">Unid</th>
                                            <th scope="col" style="width:100px;">Precio Uni.</th>
                                            <th scope="col" style="width:90px;">% Desc</th>
                                            <th scope="col">Subtotal</th>
                                            <th scope="col">IVA</th>
                                            <th scope="col">Monto IVA</th>
                                            
                                            </tr>
                                        </thead>
                                        <tbody>
                                        
                                        <template v-for="(line, index) in invoice.lines">
                                            <tr :key="line.id">
                                                <td>
                                                    <button 
                                                        type="button" 
                                                        @click="removeLine(line, index)" 
                                                        class="btn btn-sm btn-danger"
                                                        v-if="!disableFields() || isCreatingNota"
                                                        >
                                                        <span class="fa fa-remove"></span>
                                                    </button>
                                                </td>
                                               
                                                <td class="py-2 px-2">
                                                     <button type="button" class="btn btn-primary btn-sm" title="Impuestos" 
                                                     data-toggle="modal"
                                                    data-target="#taxesModal"
                                                     @click="showModalTaxes(line, index)" v-if="!disableFields() || isCreatingNota" >
                                                        Imp
                                                    </button>
                                                </td>
                                                <!-- <td v-if="isCreatingNota">
                                                    <input type="checkbox"
                                                           name="updateStock"
                                                           v-model="line.updateStock"
                                                           title="Actualizar stock del inventario">
                                                </td> -->
                                                <td>
                                                    <input type="checkbox"
                                                           name="exo"
                                                           v-model="line.exo"
                                                           title="Exonerar Linea" @change="showExo(line, index)" :data-target="'.multi-collapse-line'+ index" data-toggle="collapse">
                                                </td>
                                                <th scope="row">{{ index + 1 }}</th>
                                               
                                                <!-- <td>{{ line.Codigo }}</td> -->
                                                <td>{{ line.Detalle }}</td>
                                                <td>
                                                    <input 
                                                        class="form-control form-control-sm"
                                                        type="number"
                                                        v-model="line.Cantidad"
                                                        @blur="refreshLine(line, index)"
                                                        @keydown.enter.prevent="refreshLine(line, index)"
                                                        v-if="!disableFields() || isCreatingNota"
                                                        >
                                                     <span v-else> {{ line.Cantidad }}</span>
                                                </td>
                                                <td>
                                                     <label>
                                                    <input type="checkbox"
                                                           name="laboratory"
                                                           v-model="line.laboratory"
                                                           title="Laboratorio"
                                                           class="flat-red">
                                                    Servicio Lab.
                                                    </label>
                                                    <!-- <input type="checkbox"
                                                           name="laboratory"
                                                           v-model="line.laboratory"
                                                           title="Laboratorio"> -->
                                                </td>
                                                <td>{{ line.UnidadMedida }}</td>
                                                <td>
                                                     <input
                                                        class="form-control form-control-sm"
                                                        type="text" 
                                                        v-model="line.PrecioUnitario"
                                                        @blur="refreshLine(line, index)"
                                                        @keydown.enter.prevent="refreshLine(line, index)"
                                                        v-if="!disableFields() || isCreatingNota"
                                                        >
                                                    <span v-else> {{ moneyFormat(line.PrecioUnitario,'') }}</span>
                                                   
                                                    </td>
                                                <td>
                                                    <input
                                                        class="form-control form-control-sm"
                                                        type="text" 
                                                        v-model="line.PorcentajeDescuento"
                                                        @blur="refreshLine(line, index)"
                                                        @keydown.enter.prevent="refreshLine(line, index)"
                                                        v-if="!disableFields() || isCreatingNota"
                                                        :readonly="isAssistant">
                                                    <span v-else> {{ line.PorcentajeDescuento }}</span>
                                                </td>
                                            
                                                <td>{{ moneyFormat(line.SubTotal,'') }}</td>
                                                <!-- No aplica para este negocio -->
                                                <!-- <td>
                                                    <input
                                                        class="form-control form-control-sm"
                                                        type="text" 
                                                        v-model="line.BaseImponible"
                                                        @blur="refreshLine(line, index)"
                                                        @keydown.enter.prevent="refreshLine(line, index)"
                                                        v-if="!disableFields() || isCreatingNota"
                                                        >
                                                    <span v-else> {{ line.BaseImponible }}</span>
                                                </td> -->
                                                <td><div v-for="(tax, indexTaxD) in line.taxes" :key="indexTaxD" >{{ numberFormat(tax.tarifa) }}%</div></td>
                                                <td><div v-for="(tax, indexTaxD) in line.taxes" :key="indexTaxD" >{{ moneyFormat(tax.amount) }}</div></td>
                                            </tr>
                                           <tr v-show="line.exo">
                                                <td colspan="12">
                                                    <div class="box-exo">
                                                        <div v-for="(tax, indexTax) in line.taxes" :key="tax.id" >

                                                            <button class="btn btn-primary btn-sm" type="button" data-toggle="collapse" :data-target="'#collapseTax'+ index + indexTax" aria-expanded="false" :aria-controls="'collapseTax'+ index + indexTax">
                                                                {{ numberFormat(tax.PorcentajeExoneracion) }}% Exo
                                                            </button>
                                                            <div :class="'collapse multi-collapse-line'+ index" :id="'collapseTax'+ index + indexTax">
                                                                <div class="card card-body">
                                                                    <h4>Exoneración Impuesto {{ numberFormat(tax.TarifaOriginal) }}%</h4>
                                                                    <div class="form-row">
                                                                    
                                                                        <div class="form-group col-md-3">
                                                                            <label for="TipoDocumento">Tipo Documento</label>
                                                                            <select class="form-control custom-select" name="TipoDocumento"  v-model="tax.TipoDocumento" :disabled="disableFields() && !isCreatingNota">
                                                                            
                                                                            <option v-for="(value, key) in tipoDocumentosExo" :value="key" :key="key">
                                                                                {{ value }}
                                                                            </option>
                                                                            
                                                                            </select>
                                                                             <form-error v-if="errors.TipoDocumento" :errors="errors" style="float:right;">
                                                                                {{ errors.TipoDocumento[0] }}
                                                                            </form-error>
                                                                            
                                                                        </div>
                                                                        <div class="form-group col-md-3">
                                                                            <label for="NumeroDocumento">Numero Documento</label>
                                                                        
                                                                            <input type="text" class="form-control" id="NumeroDocumento" placeholder="" v-model="tax.NumeroDocumento" :disabled="disableFields() && !isCreatingNota" >
                                                                                
                                                                            <form-error v-if="errors.NumeroDocumento" :errors="errors" style="float:right;">
                                                                                {{ errors.NumeroDocumento[0] }}
                                                                            </form-error>
                                                                        </div>
                                                                        
                                                                        <div class="form-group col-md-6">
                                                                            <label for="NombreInstitucion">Nombre institución</label>
                                                                        
                                                                            <input type="text" class="form-control" id="NombreInstitucion" placeholder="" v-model="tax.NombreInstitucion" :disabled="disableFields() && !isCreatingNota"  >
                                                                                
                                                                            <form-error v-if="errors.NombreInstitucion" :errors="errors" style="float:right;">
                                                                                {{ errors.NombreInstitucion[0] }}
                                                                            </form-error>
                                                                        </div>
                                                                        
                                                                        <div class="form-group col-md-3">
                                                                            <label for="FechaEmision">Fecha Emisión</label>
                                                                        
                                                                            <flat-pickr
                                                                                    v-model="tax.FechaEmision"                                             
                                                                                    class="form-control" 
                                                                                    placeholder="Select date"               
                                                                                    name="date">
                                                                            </flat-pickr>
                                                                            <form-error v-if="errors.FechaEmision" :errors="errors" style="float:right;">
                                                                                {{ errors.FechaEmision[0] }}
                                                                            </form-error>
                                                                        </div>

                                                                        <div class="form-group col-md-3">
                                                                            <label for="PorcentajeExoneracion">Porcentaje Exoneración</label>
                                                                        
                                                                            <input type="text" class="form-control" id="PorcentajeExoneracion" placeholder="" v-model="tax.PorcentajeExoneracion"
                                                                            @blur="addExoneration(line, tax, index)"
                                                                            @keydown.enter.prevent="addExoneration(line, tax, index)" :disabled="disableFields() && !isCreatingNota" >
                                                                                
                                                                            <form-error v-if="errors.PorcentajeExoneracion" :errors="errors" style="float:right;">
                                                                                {{ errors.PorcentajeExoneracion[0] }}
                                                                            </form-error>
                                                                        </div>
                                                                        <div class="form-group col-md-3">
                                                                            <label for="MontoExoneracion">Monto Exoneración</label>
                                                                        
                                                                            <input type="text" class="form-control" id="MontoExoneracion" placeholder="" v-model="tax.MontoExoneracion" disabled >
                                                                                
                                                                            <form-error v-if="errors.MontoExoneracion" :errors="errors" style="float:right;">
                                                                                {{ errors.MontoExoneracion[0] }}
                                                                            </form-error>
                                                                        </div>

                                                                        <div class="form-group col-md-3">
                                                                            <label for="ImpuestoNeto">Impuesto Neto</label>
                                                                        
                                                                            <input type="text" class="form-control" id="ImpuestoNeto" placeholder="" v-model="tax.ImpuestoNeto" disabled >
                                                                                
                                                                            <form-error v-if="errors.ImpuestoNeto" :errors="errors" style="float:right;">
                                                                                {{ errors.ImpuestoNeto[0] }}
                                                                            </form-error>
                                                                        </div>
                                                                        <div class="form-group col-md-3">
                                                                            <button class="btn btn-primary btn-sm" type="button" data-toggle="collapse" :data-target="'#collapseTax'+ index + indexTax" aria-expanded="false" :aria-controls="'collapseTax'+ index + indexTax">
                                                                                Cerrar
                                                                            </button>
                                                                        </div>
                                                                        <div class="form-group col-md-3">
                                                                             <input type="checkbox"
                                                                            name="exoall"
                                                                            v-model="exoall"
                                                                            title="Aplicar Exoneracion a todas las lineas" @change="allLinesExo(line, tax, index)"> Aplicar Exoneracion a todas las lineas?
                                                                        </div>
                                                                    
                                                                    </div> 
                                                                </div>
                                                            </div>
                                                        
                            
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </template>
                                            <tr>
                                                <td :colspan="isCreatingNota ? 13 : 12" class="text-right">SubTotal:</td>
                                                <td> {{ moneyFormat(invoice.TotalVentaNeta,'') }} {{ invoice.CodigoMoneda }} </td>
                                            </tr>
                                            <tr>
                                                <td :colspan="isCreatingNota ? 13 : 12" class="text-right">Descuentos:</td>
                                                <td> {{ moneyFormat(invoice.TotalDescuentos) }} {{ invoice.CodigoMoneda }}</td>
                                            </tr>
                                            <tr>
                                                <td :colspan="isCreatingNota ? 13 : 12" class="text-right">Impuestos:</td>
                                                <td> {{ moneyFormat(invoice.TotalImpuesto,'') }} {{ invoice.CodigoMoneda }} </td>
                                            </tr>
                                            <tr v-show="invoice.MedioPago == '02'">
                                                <td :colspan="isCreatingNota ? 13 : 12" class="text-right">IVA Devuelto:</td>
                                                <td> -{{ moneyFormat(invoice.TotalIVADevuelto) }} {{ invoice.CodigoMoneda }}</td>
                                            </tr>
                                            <tr>
                                                <td :colspan="isCreatingNota ? 13 : 12" class="text-right">Total:</td>
                                                <td> {{ moneyFormat(invoice.TotalComprobante,'') }} {{ invoice.CodigoMoneda }}</td>
                                            </tr>
                                        </tbody>
                                        </table>
                                </div>
                               
                                
                            </div>
                            <div class="card-footer">
                                 <button type="button" class="btn btn-default" @click="showReferencias = !showReferencias" v-if="!invoice.id">Agregar Referencia</button>
                            </div>
                        </div> <!--card productos-->

                        <div class="box box-default" v-if="isCreatingNota || invoice.referencias.length || showReferencias">
                            <div class="box-header bg-primary text-white">
                                Documentos Referencias
                            </div>
                            <div class="box-body">
                                <div class="form-referencias" v-show="isCreatingNota || !invoice.status || showReferencias">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="TipoDocumento">Tipo Documento</label>
                                            <select class="form-control custom-select" name="TipoDocumento" id="TipoDocumento" v-model="referencia.TipoDocumento" :disabled="disableFields()">
                                        
                                            <option v-for="(value, key, index) in tipoDocumentos" :value="key">
                                                {{ value }}
                                            </option>
                                            
                                            </select>
                                            <form-error v-if="errors.TipoDocumento" :errors="errors" style="float:right;">
                                                {{ errors.TipoDocumento[0] }}
                                            </form-error>
                                        </div>
                                    
                                        <div class="form-group col-md-4">
                                            <label for="NumeroDocumento">Número Documento</label>
                                            
                                            <input type="text" class="form-control" id="NumeroDocumento" placeholder="" v-model="referencia.NumeroDocumento">
                                            <form-error v-if="errors.NumeroDocumento" :errors="errors" style="float:right;">
                                                {{ errors.NumeroDocumento[0] }}
                                            </form-error>    
                                            
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="FechaEmision">Fecha Emisión</label>
                                            <input type="text" class="form-control" name="FechaEmision" v-model="referencia.FechaEmision" placeholder="YYYY-MM-DD HH:mm">
                                            <form-error v-if="errors.FechaEmision" :errors="errors" style="float:right;">
                                                {{ errors.FechaEmision[0] }}
                                            </form-error>    
                                        </div>
                                    
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="CodigoReferencia">Código Referencia</label>
                                            <select class="form-control custom-select" name="CodigoReferencia" id="CodigoReferencia" v-model="referencia.CodigoReferencia">
                                        
                                            <option v-for="(value, key, index) in codigoReferencias" :value="key">
                                                {{ value }}
                                            </option>
                                            
                                            </select>
                                            <form-error v-if="errors.CodigoReferencia" :errors="errors" style="float:right;">
                                                {{ errors.CodigoReferencia[0] }}
                                            </form-error>   
                                        </div>
                                    
                                        <div class="form-group col-md-6">
                                            <label for="Razon">Razón</label>
                                            
                                            <input type="text" class="form-control" id="Razon" placeholder="" v-model="referencia.Razon">
                                             <form-error v-if="errors.Razon" :errors="errors" style="float:right;">
                                                {{ errors.Razon[0] }}
                                            </form-error>      
                                            
                                        </div>

                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary" @click="addReferencia()">Agregar Referencia</button>
                                        </div>
                                    
                                    </div>
                                </div>
                                <div class="table-responsive" v-show="invoice.referencias.length">
                                    <table class="table">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col">Número documento</th>
                                                <th scope="col">Tipo de documento</th>
                                                <th scope="col">Código Referencia</th>
                                                <th scope="col">Fecha</th>
                                                <th scope="col"></th>
                                            
                                            </tr>
                                        </thead>
                                        <tbody>
                                       
                                        <tr v-for="(ref, index) in invoice.referencias" :key="ref.id">
                                                
                                                <td>{{ ref.NumeroDocumento }}</td>
                                                <td>{{ tipoDocumentoName(ref.TipoDocumento) }}</td>
                                                <td>{{ tipoCodigoReferenciaName(ref.CodigoReferencia) }}</td>
                                                <td>
                                                    {{ ref.FechaEmision }}
                                                </td>
                                                <td>
                                                    <button 
                                                        type="button" 
                                                        @click="removeReferencia(ref, index)" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        v-if="!disableFields() || isCreatingNota"
                                                        >
                                                        <span class="fa fa-remove"></span>
                                                    </button>
                                                </td>
                                              
                                            </tr>
                                            
                                        </tbody>
                                        </table>
                                </div>
                            </div>
                        </div> <!--card referencias-->

                         <div class="form-group">
                            <button type="submit" class="btn btn-primary" v-if="(!disableFields() || isCreatingNota)">Generar Factura</button>
                            <!-- <button type="submit" class="btn" :class="invoice.fe ? 'btn-secondary' : 'btn-primary'" v-if="!disableFields() || isCreatingNota">Guardar</button> -->
                            <button type="submit" class="btn btn-primary" v-if="((!disableFields() || isCreatingNota) && isMedic())" @click="sendToAssistant = 1">Enviar a Secretaria</button>
                            <a :href="'/invoices/'+ invoice.id +'/notacredito'" class="btn btn-primary" v-if="invoice.id && invoice.TipoDocumento == '01' && invoice.status" role="button">Nota de Crédito</a>
                            <a :href="'/invoices/'+ invoice.id +'/notadebito'" class="btn btn-primary" v-if="invoice.id && invoice.TipoDocumento == '01' && invoice.status" role="button">Nota de Débito</a>
                            <a :href="'/invoices/'+ invoice.id +'/print'" class="btn btn-secondary" v-if="invoice.id && invoice.status" role="button">Imprimir</a>
                            <a :href="'/invoices/'+ invoice.id +'/download/pdf'" class="btn btn-secondary" v-if="invoice.id && invoice.status" role="button">Descargar PDF</a>
                             <button type="button" class="btn btn-secondary" @click="requestEmail(invoice)" v-if="invoice.id && invoice.status">Enviar por correo</button>
                        
                            <a href="/invoices" class="btn btn-secondary" role="button">Regresar</a>
                        </div>
                    </div>  <!--col-->
                    <!-- <div class="col-md-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white"></div>

                            <div class="card-body">
                                
                                
                                
                            </div>
                        </div>
                    </div> -->


          
            </div>

        </form>

        <customers-modal @assigned="addCliente" :tipo-identificaciones="tipoIdentificaciones"></customers-modal>
        <products-modal @assigned="addProduct"></products-modal>
        <modal-resumen-factura :medio-pagos="medioPagos" @recalculateInvoice="calculateInvoice(invoice.lines)" @saveResumenFactura="persist()"></modal-resumen-factura>
        <taxes-modal @assigned="addTax" @remove="removeTax"></taxes-modal>

    </div>
    
</template>
<script>
import CustomersModal from './CustomersModal.vue';
import ProductsModal from './ProductsModal.vue';
import ModalResumenFactura from './ModalResumenFactura.vue';
import TaxesModal from './TaxesModal.vue';
import FormError from './FormError.vue';
import Loading from './Loading.vue';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
    props:['currentInvoice','tipoDocumentos','tipoDocumentosNotas','codigoReferencias','medioPagos','condicionVentas','currentTipoDocumento','isCreatingNota','offices', 'currentOffice','patient', 'tipoIdentificaciones','medic','appointment','currencies','medics','tipoDocumentosExo'],
    data () {
        return {
            invoice:{
                TipoDocumento: this.currentTipoDocumento ? this.currentTipoDocumento : '01',
                customer_id: this.patient ? this.patient.id : 0,
                cliente: this.patient ? this.patient.fullname : '',
                email: this.patient ? this.patient.email : '',
                user_id: this.medic ? this.medic.id : false,
                appointment_id: this.appointment ? this.appointment.id : 0,
                tipo_identificacion_cliente: '',
                identificacion_cliente: '',
                CodigoMoneda:'CRC',
                TipoCambio: 1,
                MedioPago:'01',
                CondicionVenta:'01',
                PlazoCredito:'',
                TotalServGravados: 0,
                TotalServExentos: 0,
                TotalServExonerado:0,
                TotalMercanciasGravadas: 0,
                TotalMercanciasExentas: 0,
                TotalMercExonerada:0,
                TotalGravado: 0,
                TotalExento: 0,
                TotalExonerado: 0,
                TotalVenta: 0,
                TotalDescuentos: 0,
                TotalVentaNeta: 0,
                TotalImpuesto: 0,
                TotalIVADevuelto:0,
                TotalComprobante:0,
                lines:[],
                referencias:[],
                initialPayment:'',
                office_id: this.currentOffice ? this.currentOffice.id : 0,
                status:1,
                fe: window.App.user.fe,
                discount_id: 0,
                observations:'',
                pay_with:0,
                change:0
               
               
            },
            referencia:{
                referencia_id: this.currentInvoice ? this.currentInvoice.id : 0,
                TipoDocumento: this.currentInvoice.TipoDocumento ? this.currentInvoice.TipoDocumento : '01',
                NumeroDocumento: this.currentInvoice.fe ? this.currentInvoice.NumeroConsecutivo : this.currentInvoice.consecutivo,
                FechaEmision:this.currentInvoice.created_at,
                CodigoReferencia:'',
                Razon:''
            },
            code:'',
            customerDiscount:0,
            errors:[],
            loader: false,
            sendToAssistant:0,
            cambio:false,
            discounts:[],
            exoall:false,
            showReferencias:false,
            configDateReferencia:{
                dateFormat:'Y-m-d H:m:s'
            },
           
           
           
        };
    },
    components:{
        ProductsModal,
        CustomersModal,
        ModalResumenFactura,
        TaxesModal,
        FormError,
        Loading,
        flatPickr
    },
    computed:{
        isAssistant(){
            return window.App.isAssistant;
        }
      
    },
    methods:{
    //    sendAssistant(){
    //        this.sendToAssistant = 1
    //    },
        changeTipoIdentificacion(){
            
            if(this.invoice.tipo_identificacion_cliente == '00' && this.invoice.TipoDocumento != '04'){

                Swal.fire({
                    title: 'Facturacion a extranjero',
                    html: 'Para facturar a un extranjero solo se puede con Tiquete Electrónico. Deseas realizar el cambio?',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Si'
                }).then( (result) => {
                    
                    if (result.value) {
                        this.invoice.TipoDocumento = '04'; // si es extranjero pasamos la factura a tiquete
                    }

                });

                
               
            }
        },
        isMedic(){
            return window.App.isMedic;
        },
        disableFields(){
           
            return (this.invoice.id && this.invoice.status);
        },
        tipoDocumentoName(tipoDocumento){
            return _.get(this.tipoDocumentosNotas, tipoDocumento);
        },
        tipoCodigoReferenciaName(codigo){
            return _.get(this.codigoReferencias, codigo);
        },
        discountName(discountId){
            const discount = _.find(this.discounts, { 'id': discountId });

            return discount ? discount.name : '';
        },
        numberFormat(n){
            if(n){

                return parseFloat(n).format(0);
            }
            return 0;
         
        },
        moneyFormat(n){
         
            if(typeof n === 'number'){

                return n.format(2);
            }

            return n;
        },
       
        searchCustomer(q){
            
            axios.get(`/invoices/patients?q=${q}`)
                .then(({data}) => {
                    
                    this.invoice.customer_id = 0;
                    this.invoice.email = '';
                    //this.invoice.cliente = '';

                    if(data.data){
                        
                        if(data.data.length == 1){
                            this.addCliente(data.data[0]);
                            flash('Paciente Agregado');

                        }else{
                            //flash('Paciente No encontrado', 'danger');
                            $('#customersModal').modal();
                            this.emitter.emit('showCustomersModal', { searchTerm: q});
                        }
                       
                    }else{
                        flash('Paciente No encontrado', 'danger');
                    }
                });
        },
        listenCliente(e){
          
            if(!e.target.value){
                this.invoice.customer_id = 0;
            }
        },
        showModalTaxes(line, index){
            const data = {
                line:line,
                index:index
            };
            this.emitter.emit('showTaxesModal', data);
   
        },
        removeTax(data){
            const line = data.dataLine.line; 
            const index = data.dataLine.index;
            const tax = data.tax;
           

            const indexTax = _.findIndex(line.taxes, { 'code': tax.code });

            line.taxes.splice(indexTax, 1);
            line.overrideImp = true;

            this.refreshLine(line, index);
              
           
        },
        addTax(data){
            const line = data.dataLine.line; 
            const index = data.dataLine.index;
            const tax = data.tax;
            

            line.taxes.push(tax);
            line.overrideImp = true;

            this.refreshLine(line, index);
              
           
        },
        showModalCustomers(){
         
            this.emitter.emit('showCustomersModal','');
   
        },
        showModalProducts(){
         
            this.emitter.emit('showProductsModal', this.invoice.office_id);
   
        },
        loadDiscounts(){
          
            axios.get(`/discounts?office=${this.invoice.office_id}`)
                .then(({data}) => {
                      
                    this.discounts = data.data;

                    this.setPatient();
                        
                   
                });
        },
        searchProduct(){
        
            axios.get(`/products?code=${this.code}`)
                .then(data => {
                    if(data.data){
                        this.addProduct(data.data);
                        flash('Servicio Agregado');
                    }else{
                        flash('Servicio No encontrado', 'danger');
                    }
                });
        },
       

        addCliente(cliente){
            this.invoice.customer_id = cliente.id;
            this.invoice.cliente = cliente.fullname;
            this.invoice.email = cliente.email;
            this.invoice.tipo_identificacion_cliente = '01';
            this.invoice.identificacion_cliente = cliente.ide;
            const discount = parseFloat(cliente.PorcentajeDescuento);

            this.customerDiscount = discount;
           
         
            if(this.invoice.lines.length){
                this.invoice.lines.forEach((line, index) => {
                    line.PorcentajeDescuento = this.customerDiscount;
                    this.refreshLine(line, index);
                });
             
            }
           
        },
        addProduct(product){
            this.createInvoiceLine(product);
          
        },
        removeLine(product, index){
            this.invoice.lines.splice(index, 1);
            this.calculateInvoice(this.invoice.lines);
        },
        setPatient(){
            if(this.patient){

                const firstDiscount =  _.head(this.discounts);
                    

                if(firstDiscount){

                    const discount = _.find( this.patient.discounts, function(o) {
                        return o.user_id === firstDiscount.user_id;
                    });

                    this.invoice.discount_id = discount ? discount.id : 0;

                }
                    
                    
            }
        },
        setTipoCambio(){

            const moneda = _.find( this.currencies, (currency) => {
              
                return currency.code === this.invoice.CodigoMoneda;
            });

            if(moneda){
                this.invoice.TipoCambio = moneda.exchange;
            }
                
           

            this.invoice.lines.forEach((line, index) => {
                   
                line.PrecioUnitario = (this.invoice.CodigoMoneda == 'USD') ? this.convertMonedaAmount(line.PrecioColonTemp) : line.PrecioColonTemp;

                this.refreshLine(line, index);
            });
             
            

        },
        setDiscount(){
        
            const vm = this;

            const discount = _.find( this.discounts, function(o) {
              
                return o.id === vm.invoice.discount_id;
            });

            if(this.invoice.lines.length && discount){
                this.invoice.lines.forEach((line, index) => {
                
                    line.PorcentajeDescuento = discount.tarifa;
                    line.NaturalezaDescuento = 'Descuento empresarial: '+ discount.name;
                    this.refreshLine(line, index);
                });
             
            }else{
                this.invoice.lines.forEach((line, index) => {
                   
                    line.PorcentajeDescuento = 0;
                    line.NaturalezaDescuento = '';
                    this.refreshLine(line, index);
                });
            }

            
           
        },

        createInvoiceLine(product){
           

            const lineIndexFound = _.findIndex( this.invoice.lines, function(o) {
                return o.Codigo === product.id;
            });
            const lineFound = _.find( this.invoice.lines, function(o) {
                return o.Codigo === product.id;
            });

            if(lineFound && lineIndexFound !== -1)
            {
                lineFound.Cantidad++;
   
                this.updateInvoiceLine(this.calculateInvoiceLine(lineFound), lineIndexFound);
                 
            }else{


                const nuevo = {
                    CodigoProductoHacienda: product.CodigoProductoHacienda,
                    Codigo: product.id,
                    Detalle: product.name,
                    Cantidad: 1,
                    UnidadMedida: product.measure,
                    PrecioUnitario: this.convertMonedaAmount(product.price),
                    MontoTotal: 0,
                    PorcentajeDescuento: this.customerDiscount,
                    MontoDescuento: 0,
                    NaturalezaDescuento:'',
                    SubTotal: 0,
                    MontoTotalLinea: 0,
                    totalTaxes:0,
                    taxes: product.taxes,
                    type: product.type,
                    laboratory: product.laboratory,
                    PrecioColonTemp: product.price,
                    product:product,
                    is_servicio_medico: product.is_servicio_medico,
                    overrideImp: false
                       
                       
                };
                  
                const line = this.calculateInvoiceLine(nuevo);

                this.invoice.lines.push(line);
                

                this.setDiscount();
                
            }
            
            this.calculateInvoice(this.invoice.lines);

        },

        refreshLine(line, index){
            this.updateInvoiceLine(this.calculateInvoiceLine(line, index), index);
        },

        updateInvoiceLine(line, index){
          
            this.invoice.lines.splice(index, 1, line);
            this.calculateInvoice(this.invoice.lines);
        },
        allLinesExo(lineInvoice, lineTax, lineInvoiceindex){
           
            this.invoice.lines.forEach((line, index) => {
               
                if(this.exoall){
                    line.exo = true;
                    line.taxes.forEach(tax => {
                        tax.name = tax.name;
                        tax.tarifa = lineTax.tarifa;
                        tax.TipoDocumento = lineTax.TipoDocumento;
                        tax.NumeroDocumento = lineTax.NumeroDocumento;
                        tax.NombreInstitucion = lineTax.NombreInstitucion;
                        tax.FechaEmision = lineTax.FechaEmision;
                        tax.PorcentajeExoneracion = lineTax.PorcentajeExoneracion;
                        tax.ImpuestoOriginal = lineTax.ImpuestoOriginal;
                        tax.TarifaOriginal = tax.TarifaOriginal;
                        
                
                    });
                }else{
                    if(index != lineInvoiceindex){
                        line.exo = false;
                        line.taxes.forEach(tax => {
                            
                            tax.name = tax.name;
                            tax.tarifa = tax.TarifaOriginal;
                            tax.amount = tax.ImpuestoOriginal;
                            tax.TipoDocumento = '';
                            tax.NumeroDocumento = '';
                            tax.NombreInstitucion = '';
                            tax.FechaEmision = null;
                            tax.PorcentajeExoneracion = 0;
                            tax.MontoExoneracion = 0;
                            tax.ImpuestoOriginal = tax.ImpuestoOriginal;
                            
                    
                        });
                    }
                  
                }

                this.refreshLine(line, index);
            });
        },
        showExo(line, index){
           
            $('.multi-collapse-line'+ index).addClass('show');
            
            if(!line.exo){
                //line.taxes = line.product.taxes;
                line.taxes.forEach(tax => {
                   
                    tax.name = tax.name;
                    tax.tarifa = tax.TarifaOriginal;
                    tax.amount = tax.ImpuestoOriginal;
                    tax.MontoExoneracion = 0;
                    tax.PorcentajeExoneracion = 0;
                    tax.ImpuestoNeto = tax.ImpuestoOriginal;
        
                });
              
            }
           

            this.refreshLine(line, index);
        },
       
        addExoneration(line, lineTax, index){
           
            if(!this.invoice.id || this.isCreatingNota){
                this.calculateExoneration(line, lineTax, index);
            }
            this.updateInvoiceLine(this.calculateInvoiceLine(line, index), index);
          
        },
        calculateExoneration(line, lineTax, index){
          
           
            const taxes = [];
            let PorcentajeExo = 0;
            let ImpuestoNeto = 0;
            let MontoExoneracion = 0;
            
            const lineasTaxes = (line.product && line.product.taxes && line.product.taxes.length && !line.overrideImp) ? line.product.taxes : line.taxes;

            lineasTaxes.forEach(tax => {
                
                const tarifa = parseFloat(tax.TarifaOriginal ? tax.TarifaOriginal : tax.tarifa);
                const subTotal = (tax.code == '07') ? line.BaseImponible : line.SubTotal; //IVA especial se utliza base imponible

                const MontoImpuesto = redondeo((parseFloat(tarifa)/100) * subTotal); // se roundM por problemas de decimales de hacienda
            
                ImpuestoNeto = MontoImpuesto;

                if(line.exo /* && (!this.invoice.id || this.isCreatingNota) */ ){
                    
                    PorcentajeExo = parseFloat(lineTax.PorcentajeExoneracion ? lineTax.PorcentajeExoneracion : 0);
                   
                    MontoExoneracion = redondeo((PorcentajeExo / 100) * MontoImpuesto);
                

                    ImpuestoNeto =  MontoImpuesto - MontoExoneracion;

                    
                }

                lineTax.MontoExoneracion = MontoExoneracion;
                lineTax.ImpuestoNeto = ImpuestoNeto;
                lineTax.tarifa = tarifa;
                
                taxes.push({
                    code: tax.code,
                    CodigoTarifa: tax.CodigoTarifa,
                    name: tax.name,
                    tarifa: tarifa,
                    amount: MontoImpuesto,
                    TipoDocumento: lineTax.TipoDocumento,
                    NumeroDocumento: lineTax.NumeroDocumento,
                    NombreInstitucion: lineTax.NombreInstitucion,
                    FechaEmision: lineTax.FechaEmision,
                    PorcentajeExoneracion: lineTax.PorcentajeExoneracion ? lineTax.PorcentajeExoneracion : 0,
                    MontoExoneracion: lineTax.MontoExoneracion ? lineTax.MontoExoneracion : 0,
                    TarifaOriginal: tarifa,
                    ImpuestoOriginal: MontoImpuesto,
                    ImpuestoNeto: lineTax.ImpuestoNeto
                    
                });

            });


            line.taxes = taxes;

            return line;
            
           

        },

        calculateInvoiceLine(line, index){
            
            line.Cantidad = parseFloat(line.Cantidad);
            line.PrecioUnitario = parseFloat(line.PrecioUnitario);
            
            line.PorcentajeDescuento = parseFloat(line.PorcentajeDescuento ? line.PorcentajeDescuento : 0);

            const taxes = [];
            const MontoTotal = line.PrecioUnitario * line.Cantidad;
            const MontoDescuento = (line.PorcentajeDescuento / 100) * MontoTotal;
            const SubTotal = MontoTotal - MontoDescuento;
            const BaseImponible = SubTotal;//line.BaseImponible ? parseFloat(line.BaseImponible) : SubTotal;
            let totalTaxes = 0;

            line.MontoTotal = MontoTotal;
            line.MontoDescuento = MontoDescuento;
            //line.NaturalezaDescuento ='';
            line.SubTotal = SubTotal;
            line.BaseImponible = BaseImponible;
              
              
            line.taxes.forEach(tax => {

                this.calculateExoneration(line, tax, index);
                const subTotalbase = (tax.code == '07') ? line.BaseImponible : line.SubTotal; //IVA especial se utliza base imponible
                const MontoImpuesto = redondeo((parseFloat(tax.tarifa)/100) * subTotalbase);
                        

                taxes.push({
                    code: tax.code,
                    CodigoTarifa: tax.CodigoTarifa,
                    name: tax.name,
                    tarifa: tax.tarifa,
                    amount: MontoImpuesto,
                    TipoDocumento: tax.TipoDocumento,
                    NumeroDocumento: tax.NumeroDocumento,
                    NombreInstitucion: tax.NombreInstitucion,
                    FechaEmision: tax.FechaEmision,
                    PorcentajeExoneracion: tax.PorcentajeExoneracion ? tax.PorcentajeExoneracion : 0,
                    MontoExoneracion: tax.MontoExoneracion ? tax.MontoExoneracion : 0,
                    TarifaOriginal: tax.TarifaOriginal ? tax.TarifaOriginal : tax.tarifa,
                    ImpuestoOriginal: tax.ImpuestoOriginal ? tax.ImpuestoOriginal : 0,
                    ImpuestoNeto: tax.ImpuestoNeto ? tax.ImpuestoNeto : 0
                            
                });

                totalTaxes += tax.ImpuestoNeto;

                     
            });

           
           
            
            //line.MontoTotal = MontoTotal;
            // line.MontoDescuento = MontoDescuento;
            //line.NaturalezaDescuento ='';
            //line.SubTotal = SubTotal;
            
            line.totalTaxes = totalTaxes;
            line.MontoTotalLinea = line.SubTotal + totalTaxes;
            line.taxes = taxes;

            return line;


        },
        calculateInvoice(lines){
          
            let TotalMercanciasGravadas = 0;
            let TotalMercanciasExentas = 0;
            let TotalServGravados = 0;
            let TotalServExentos = 0;
            let TotalGravado = 0;
            let TotalExento = 0;
            let TotalVenta = 0; 
            let TotalDescuentos = 0;
            let TotalVentaNeta = 0;
            let TotalImpuesto = 0;
            let TotalComprobante = 0;
            let TotalServExonerado = 0;
            let TotalMercExonerada = 0;
            let TotalExonerado = 0;
            let TotalIVADevuelto = 0;
            let PagoCon = 0;
            let Vuelto = 0;

            lines.forEach(line => {
               
                if(line.type == 'P'){

                    if(line.taxes.length){
                        
                        line.taxes.forEach(tax => {
                            if(line.exo){
                                
                                
                                TotalMercanciasGravadas += (1-parseFloat(tax.PorcentajeExoneracion)/100) * line.MontoTotal;
                                  
                                TotalMercExonerada += (parseFloat(tax.PorcentajeExoneracion)/100) * line.MontoTotal;

                            }else{
                                TotalMercanciasGravadas += parseFloat(line.MontoTotal);
                                TotalMercExonerada += 0;
                            }
                        });
                        
                        
                    }else{
                        TotalMercanciasExentas += parseFloat(line.MontoTotal);
                        TotalMercExonerada += 0;
                    }

                }else{ // type S : Servicio
                    if(line.taxes.length){
                        
                        line.taxes.forEach(tax => {
                            if(line.exo){
                                  
                                    
                                       
                                TotalServGravados += (1-parseFloat(tax.PorcentajeExoneracion)/100) * line.MontoTotal;
                                    
                                TotalServExonerado += (parseFloat(tax.PorcentajeExoneracion)/100) * line.MontoTotal;
                                   
                            }else{
                                TotalServGravados += parseFloat(line.MontoTotal);
                                TotalServExonerado += 0;
                            }

                                
                            //IVA devuelto para servicios medicos pagados con tarjeta
                            if(line.is_servicio_medico && this.invoice.MedioPago == '02'){
                                TotalIVADevuelto += tax.ImpuestoNeto;
                            }
                        });
                       
                       
                        
                        
                    }else{
                        TotalServExentos += parseFloat(line.MontoTotal);
                        TotalServExonerado += 0;
                    }

                }

                TotalDescuentos += parseFloat(line.MontoDescuento);
                TotalImpuesto += parseFloat(line.totalTaxes);
             

            });

            TotalGravado = TotalMercanciasGravadas + TotalServGravados;
            TotalExento = TotalMercanciasExentas + TotalServExentos;
            TotalExonerado = TotalMercExonerada + TotalServExonerado;

            TotalVenta = TotalGravado + TotalExento + TotalExonerado;
            TotalVentaNeta = TotalVenta - TotalDescuentos;
            TotalComprobante = TotalVentaNeta + TotalImpuesto - TotalIVADevuelto;

            this.invoice.TotalMercanciasGravadas = TotalMercanciasGravadas;
            this.invoice.TotalMercanciasExentas = TotalMercanciasExentas;
            this.invoice.TotalMercExonerada = TotalMercExonerada;
            
            this.invoice.TotalServGravados = TotalServGravados;
            this.invoice.TotalServExentos = TotalServExentos;
            this.invoice.TotalServExonerado = TotalServExonerado;
            this.invoice.TotalGravado = TotalGravado;
            this.invoice.TotalExento = TotalExento;
            this.invoice.TotalExonerado = TotalExonerado;
            this.invoice.TotalVenta = TotalVenta; 
            this.invoice.TotalDescuentos = TotalDescuentos;
            this.invoice.TotalVentaNeta = TotalVentaNeta;
            this.invoice.TotalImpuesto = TotalImpuesto;
            this.invoice.TotalIVADevuelto = TotalIVADevuelto;
            this.invoice.TotalComprobante = TotalComprobante;


            // para vuelto
            PagoCon = parseFloat(this.invoice.pay_with);
            Vuelto =  PagoCon - this.invoice.TotalComprobante;
            this.invoice.change = Vuelto < 0 ? 0 : Vuelto;
  
            
        
           
            return this.invoice;


        },
        convertMonedaAmount(amount){
           
            let result = amount;
            let tipoC = parseFloat(this.invoice.TipoCambio);

            const monedaDolar = _.find( this.currencies, (currency) => {
              
                return currency.code === 'USD';
            });

            if(monedaDolar){
                tipoC = monedaDolar.exchange;
            }

            if(this.invoice.CodigoMoneda == 'USD'){
               
                result = amount / (tipoC <= 0 ? 1 : tipoC);
            }

            return redondeo(result);
        },
        save(){

            if(this.invoice.tipo_identificacion_cliente == '00' && this.invoice.TipoDocumento != '04'){
                Swal.fire({
                    title: 'Facturación Extranjero',
                    html: 'Para facturar a un extranjero tiene que ser un Tiquete Electrónico',
                    showCancelButton: false,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Ok'
                }).then( (result) => {
    

                });

                return;
            }

            if(!this.invoice.lines.length){
                Swal.fire({
                    title: 'lineas de detalle requerida',
                    html: 'Necesitar agregar al menos una linea para poder crear la factura',
                    showCancelButton: false,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Ok'
                }).then( (result) => {
    

                });

                return;
            }
            if(this.isCreatingNota && this.invoice.TipoDocumento != '01' && !this.invoice.referencias.length)
            {
                Swal.fire({
                    title: 'Documento de referencia requerido',
                    html: 'Necesitar agregar al menos un documento de referencia para poder crear la nota',
                    showCancelButton: false,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Ok'
                }).then( (result) => {
    

                }, (dismiss) => { });

                return;
            }

            const errorM = {};
            this.invoice.lines.forEach(line => {
                if(line.exo){
                    line.taxes.forEach(tax => {
                        if(!tax.TipoDocumento)
                        {
                            errorM.TipoDocumento = ['Tipo de documento requerido'];

                        }
                        if(!tax.NumeroDocumento)
                        {
                            errorM.NumeroDocumento = ['Numero de documento requerido'];

                        }
                        if(tax.NumeroDocumento && tax.NumeroDocumento.length > 17)
                        {
                            errorM.NumeroDocumento = ['Numero de documento tiene que ser de 17 caracteres'];

                        }
                        if(!tax.NombreInstitucion)
                        {
                            errorM.NombreInstitucion = ['Nombre de la institución requerido'];

                        }
                        if(!tax.FechaEmision)
                        {
                            errorM.FechaEmision = ['Fecha Emisión requerido'];

                        }
                        if(!tax.PorcentajeExoneracion)
                        {
                            errorM.PorcentajeExoneracion = ['Porcentaje Exoneración requerido'];

                        }
                        
                    });
                   
                }
                this.errors = errorM;
            });
            if(!_.isEmpty(this.errors)){

                Swal.fire({
                    title: 'Información de exoneración requerido o erronea',
                    html: 'En algunas de las lineas que tienen exoneración falta o hay información erronea. Revisa!',
                    showCancelButton: false,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Ok'
                }).then( (result) => {
    

                });

                return;
            }

            if(this.sendToAssistant){
                this.invoice.status = 0;
            }
          
          
            if((this.invoice.TipoDocumento == '01' || this.invoice.TipoDocumento == '04') && this.invoice.MedioPago == '01'&& !this.sendToAssistant){

                $('#modalResumenFactura').modal();
                this.emitter.emit('showResumenFacturaModal', this.invoice);
                //    Swal.fire({
                //         title: 'Pago Con',
                //         input: 'text',
                //         inputPlaceholder: '',
                //         showCancelButton: true,
                //         inputValidator: (value) => {
                //             let pay_with = _.toNumber(value);
                       
                //            return !_.isFinite(pay_with) && 'Valor numerico requerido!'

                      
                      
                //         }
                //     })
                //     .then( (result) => {
                   
                 
                //         //if (result.value) {
                //            let pay = _.toNumber(result.value);
                //            let change = pay - this.invoice.TotalComprobante;


                //            this.invoice.pay_with = pay;
                //            this.invoice.change = change < 0 ? 0 : change;
                //            this.cambio = this.invoice.change;
                //            if(this.invoice.id && !this.invoice.status){
                //                 this.update();
                //            }else{

                //                 this.persist();
                //            }
                //        // }

                //     }, (dismiss) => { });



            }else{
                if(this.invoice.id && !this.invoice.status && !this.isCreatingNota){
                    this.update();
                }else{

                    this.persist();
                }
            }

           
        },
        update(){

            if(this.loader) {
                return;
            }

            this.loader = true;
            axios.put(`/invoices/${this.invoice.id}`, this.invoice)
                .then(({data}) => {
                    this.loader = false;
                    this.clearForm();
                    flash('Factura Guardada Correctamente.');
                    this.$emit('created', data);

                    this.actions(data);


                })
                .catch(error => {
                  
                    this.loader = false;
                    if(error.response.status == 500 || error.response.status == 504)
                    {
                        this.clearForm();
                       
                        flash('La Factura fue creada, pero ocurrió un error. ' + error.response.data.message, 'danger');

                    }else if(error.response.status == 422)
                    {
                        flash('Error al enviar Factura', 'danger');

                    }else{
                        
                        flash(error.response.data.message, 'danger');
                    }

                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });

        },
        persist(){
            if(this.loader) {
                return;
            }

            this.loader = true;
            axios.post('/invoices', this.invoice)
                .then(({data}) => {
                    this.loader = false;
                    this.clearForm();
                    flash('Factura Guardada Correctamente.');
                    this.$emit('created', data);

                    this.actions(data);
                        


                })
                .catch(error => {
                  
                    this.loader = false;
                    if(error.response.status == 500 || error.response.status == 504)
                    {
                        this.clearForm();
                        
                        flash('La Factura fue creada, pero ocurrió un error. ' + error.response.data.message, 'danger');

                    }else{
                        flash('Error al guardar la factura!!', 'danger');
                    }

                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });
        },
        actions(invoice){

            if(this.sendToAssistant){
                Swal.fire({
                    title: 'Factura Enviada',
                    html: '¿Que deseas hacer?',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    cancelButtonText: 'Volver al panel de facturación',
                    confirmButtonText: 'Nueva Factura'
                }).then( (result) => {
                
                    
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        
                        window.location = '/invoices';
                        
                    }else{

                        if(this.isCreatingNota){
                            window.location = '/invoices';
                        }
                    }

                

                });


            }else{
                let message = '¿Deseas Imprimir o enviar por correo?';
                const cambio = invoice.change ? parseFloat(invoice.change) : 0;
                if(cambio >= 0){
                    message = '<h3>Cambio: '+  this.moneyFormat(cambio) +' '+  invoice.CodigoMoneda + ' .</h3> ¿Deseas Imprimir o enviar por correo?';
                }

                Swal.fire({
                    title: 'Factura Guardada',
                    html: message,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    cancelButtonText: 'Correo',
                    confirmButtonText: 'Imprimir'
                }).then( (result) => {
                    
                    if (result.value) {
                        window.location = '/invoices/' + invoice.id + '/print';

                    }else if (result.dismiss === Swal.DismissReason.cancel) {
                        
                        this.requestEmail(invoice);
                        
                    }else{
                        
                        window.location = '/invoices';

                    }


                    

                });


            }
           

            
        },
        requestEmail(invoice){
            Swal.fire({
                title: 'Correo',
                input: 'text',
                inputPlaceholder: '',
                showCancelButton: true,
                confirmButtonText: 'Enviar',
                showLoaderOnConfirm: true,
                inputValue: invoice.email ? invoice.email : '',
                inputValidator: (value) => {
                    return new Promise((resolve) => {
                        if (value === '') {
                            resolve('Necesitas agregar al menos un correo');
                        } else {
                            resolve();
                        }
                    });
                },
                preConfirm: (email) => {
                    
                    return axios.post('/invoices/'+ invoice.id + '/email/pdf',{ to: email })
                        .then(resp => {})
                        .catch(error =>{
                           
                            Swal.showValidationError(
                                `Request failed: ${error}`
                            );
                                
                            flash('Error al enviar la factura por correo!!', 'danger');
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
                
            })
                .then( (result) => {
            
            
                    if (result.value) {

                        Swal.fire({
                            title: 'Factura Enviada Correctamente',
                       
                        });
                
                    }
                

                });
        },
        addReferencia(){
          
            if(this.referencia){
                const errorM = {};
                if(!this.referencia.TipoDocumento)
                {
                    errorM.TipoDocumento = ['Tipo de documento requerido'];
                
                
                }
                if(!this.referencia.NumeroDocumento)
                {
                    errorM.NumeroDocumento = ['Numero de documento requerido'];
                
                
                }
                if(!this.referencia.FechaEmision)
                {
                    errorM.FechaEmision = ['Fecha de emisión requerida'];
                
                
                }
                if(!this.referencia.CodigoReferencia)
                {
                    errorM.CodigoReferencia = ['Código de referencia requerido'];
                
                
                }
                if(!this.referencia.Razon)
                {
                    errorM.Razon = ['Razon requerido'];
                
                
                }

                this.errors = errorM;

                if(!_.isEmpty(this.errors)){
                    return;
                }
            
                const newDocumentoReferencia = {
                    TipoDocumento: this.referencia.TipoDocumento,
                    NumeroDocumento: this.referencia.NumeroDocumento,
                    FechaEmision: this.referencia.FechaEmision,
                    CodigoReferencia:this.referencia.CodigoReferencia,
                    Razon: this.referencia.Razon,
                    referencia_id: this.currentInvoice ? this.currentInvoice.id : 0,
                };

                this.invoice.referencias.push(newDocumentoReferencia);
                this.referencia.CodigoReferencia = '';
                this.referencia.Razon = '';

            }
        },
        removeReferencia(item, index){
    
            this.invoice.referencias.splice(index, 1);
    
            


        },
        clearForm(){

            this.invoice = {
                TipoDocumento:'01',
                customer_id:0,
                cliente:'',
                user_id:false,
                appointment_id:0,
                email:'',
                tipo_identificacion_cliente: '',
                identificacion_cliente: '',
                CodigoMoneda:'CRC',
                MedioPago:'01',
                CondicionVenta:'01',
                PlazoCredito:'',
                TotalServGravados: 0,
                TotalServExentos: 0,
                TotalServExonerado:0,
                TotalMercanciasGravadas: 0,
                TotalMercanciasExentas: 0,
                TotalMercExonerada:0,
                TotalGravado: 0,
                TotalExento: 0,
                TotalExonerado: 0,
                TotalVenta: 0,
                TotalDescuentos: 0,
                TotalVentaNeta: 0,
                TotalImpuesto: 0,
                TotalIVADevuelto:0,
                TotalComprobante:0,
                lines:[],
                referencias:[],
                initialPayment:'',
                office_id: this.currentOffice ? this.currentOffice.id : 0,
                status:1,
                fe: window.App.user.fe,
                discount_id: 0,
                observations:'',
                pay_with:0,
                change:0
               
            };

            this.code = '';
            this.customerDiscount = 0;

            

        }
       
       
    },
   
    created(){
        this.loadDiscounts();

        if(this.currentInvoice){
            this.invoice = this.currentInvoice;
            if(this.isCreatingNota){
                this.invoice.referencias = [];
            }
          
            if(this.currentTipoDocumento){
                this.invoice.TipoDocumento = this.currentTipoDocumento;
            }
            this.invoice.lines.forEach((line, index) => {
                line.updateStock = 0;
                this.refreshLine(line, index);
            });
            this.calculateInvoice(this.invoice.lines);

         
        }
       

    }

};
</script>
