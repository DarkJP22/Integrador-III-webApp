<template>
    <div class="invoice-form">
        <loading :show="loader"></loading> 
        
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
                                     
                                    <div :class="!isMedic() ? 'col-md-6': 'col-md-12'" class="form-group">
                                        <label for="office_id">Consultorio</label>
                                        <select id="office_id" v-model="invoice.office_id" :disabled="disableFields()" class="form-control" name="office_id" required @change="loadDiscounts()">
                             
                                        <option v-for="(item, index) in offices" :key="item.id" :selected="index == 0 ? 'selected' : '' " :value="item.id"> {{ item.name }}</option>
                                        
                                        </select>
                                    </div>
                                     
                                    <div v-if="isLab" class="form-group col-md-6">
                                        <label for="user_id">Médico</label>
                                        <select-medic :disabled="!!disableFields()" :medic="medic" :url="'/lab/medics'" @selectedMedic="selectedMedic"></select-medic>
                                    </div>
                                    <div v-else v-show="!isMedic()" :class="!isMedic() ? 'col-md-6': 'col-md-12'" class="form-group">
                                        <label for="user_id">Médico</label>
                                        <select id="user_id" v-model="invoice.user_id" :disabled="disableFields()" class="form-control" name="user_id" required @change="changeMedic()">
                                        <option value=""></option>
                                        <option v-for="(item, index) in medics" :key="item.id" :selected="index == 0 ? 'selected' : '' " :value="item.id"> {{ item.name }}</option>
                                        
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
                                     <div class="col-md-12">
                                        <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2" for="CodigoActividad">
                                            Actividad Económica
                                        </label>
                                    
                                    
                                       <div class="form-group">
                                            <div class="form-line">
                                                <select v-model="invoice.CodigoActividad"  :disabled="disableFields()" class="form-control selectpicker" required>
                                                    <option v-for="(item, index) in activitiesHacienda" :key="index" :value="item.codigo">
                                                           {{ item.codigo }} - {{ item.actividad }}
                                                    </option>
                                        
                                                </select>
                                            </div>
                                        
                                        </div>
                                            <form-error v-if="errors.CodigoActividad" :errors="errors">
                                                {{ errors.CodigoActividad[0] }}
                                            </form-error> 
                                    </div>

                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="TipoDocumento">Tipo Documento</label>
                                        <select id="TipoDocumento" v-model="invoice.TipoDocumento" :disabled="disableFields()" class="form-control custom-select" name="TipoDocumento">
                                    
                                        <option v-for="(value, key) in tipoDocumentos" :key="key" :value="key">
                                            {{ value }}
                                        </option>
                                        
                                        </select>
                                    </div>
                                
                                     <div class="form-group col-md-4">
                                        <label for="MedioPago">Medio Pago</label>
                                        <select id="MedioPago" v-model="invoice.MedioPago" :disabled="disableFields()" class="form-control custom-select" name="MedioPago" @change="calculateInvoice(invoice.lines)">
                                    
                                        <option v-for="(value, key) in medioPagos" :key="key" :value="key">
                                            {{ value }}
                                        </option>
                                        
                                        </select>
                                    </div>
                                   <div class="form-group col-md-4">
                                        <label for="CodigoMoneda">Moneda</label>
                                        <select id="CodigoMoneda" v-model="invoice.CodigoMoneda" :disabled="disableFields()" class="form-control custom-select" name="CodigoMoneda" @change="setTipoCambio()">
                                    
                                        <option v-for="(item) in currencies" :key="item.code" :value="item.code">
                                            {{ item.name }}
                                        </option>
                                        
                                        </select>
                                    </div>
                                
                                </div>
                                <div class="form-row">
                                     <div class="form-group col-md-4">
                                        <label for="CondicionVenta">Condición de venta</label>
                                        <select id="CondicionVenta" v-model="invoice.CondicionVenta" :disabled="disableFields()" class="form-control custom-select" name="CondicionVenta">
                                    
                                        <option v-for="(value, key) in condicionVentas" :key="key" :value="key">
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
                                                name="PlazoCredito"
                                                placeholder="Selecione una fecha">
                                        </flat-pickr>
                                    </div>
                                     <div class="form-group col-md-4">
                                        <label for="PlazoCredito">Abono Inicial</label>
                                        <input v-model="invoice.initialPayment" :disabled="invoice.CondicionVenta != '02' || disableFields()" class="form-control" name="initialPayment" type="text" >
                                    </div>
                                   
                                </div>
                                
                                <div class="form-row">
                                   
                                
                                    <div class="form-group col-md-12">
                                        <label for="observations">Observaciones</label>
                                         <textarea v-model="invoice.observations" :disabled="disableFields()" class="form-control" cols="30" name="observations" rows="2"></textarea>
                                       
                                            
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
                                <div v-if="isGPSUser" class="callout callout-info">
                                    <h4>Información importante!</h4>

                                    <p>Es un usuario de Doctor Blue</p>
                                    
                                </div>
                                <div v-if="isAffiliateUser" class="callout callout-info">
                                  <h4>Información importante!</h4>

                                    <p>Es un usuario afiliado</p>
  
                                      
                                      </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="TipoDocumento">Tipo Identificacion</label>
                                        <select id="tipo_identificacion_cliente" v-model="invoice.tipo_identificacion_cliente" class="form-control custom-select" name="tipo_identificacion_cliente" @change="changeTipoIdentificacion()" >
                                        
                                        <option value=""></option>
                                        <option v-for="(value, key) in tipoIdentificaciones" :key="key" :value="key">
                                            {{ value }}
                                        </option>
                                        
                                        </select>
                                        <form-error v-if="errors.tipo_identificacion_cliente" :errors="errors" style="float:right;">
                                            {{ errors.tipo_identificacion_cliente[0] }}
                                        </form-error>
                                    </div>
                                
                                    <div class="form-group col-md-4">
                                        <label for="cliente">Identificacion</label>
                                       
                                        <input id="identificacion_cliente" v-model="invoice.identificacion_cliente" class="form-control" placeholder="" type="text" @keyup="listenCliente($event)" @keydown.prevent.enter="searchCustomer(invoice.identificacion_cliente)" >
                                            
                                        <form-error v-if="errors.identificacion_cliente" :errors="errors" style="float:right;">
                                            {{ errors.identificacion_cliente[0] }}
                                        </form-error>
                                    </div>
                                    <div class="form-group col-md-4">
                                         <label for="email">Nombre Cliente</label>
                                        <div class="input-group input-group">
                                            <input v-model="invoice.cliente" class="form-control" name="cliente" type="text" @keyup="listenCliente($event)" @keydown.prevent.enter="searchCustomer(invoice.cliente)" >
                                            <div class="input-group-btn">
                                                <button class="btn btn-primary"
                                                        data-target="#customersModal"
                                                        data-toggle="modal" type="button" @click="showModalCustomers()">Buscar</button>
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
                                        <label for="email">Email</label>
                                        <input v-model="invoice.email" class="form-control" name="email" type="email" >
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="discount_id">Descuento</label>
                                         <select id="discount_id" v-model="invoice.discount_id" :disabled="disableFields() && !isCreatingNota" class="form-control custom-select" name="discount_id" @change="setDiscount()" >
                                        
                                            <option value=""></option>
                                            <option v-for="(discount) in discounts" :key="discount.id" :value="discount.id" >
                                                {{ discount.name }} - {{ discount.tarifa }}%
                                            </option>
                                            
                                            </select>

                                    </div>
                                   
                                </div>
<!--                                <div v-if="affiliation.id" class="form-row">-->
<!--                                    -->
<!--                                    <div class="form-group col-md-4">-->
<!--                                        <label for="plan">Acumulado</label>-->
<!--                                        <input v-model="affiliation.acumulado" class="form-control" disabled name="acumulado" type="text">-->
<!--                                    </div>-->
<!--                                    -->
<!--                                   -->
<!--                                </div>-->
                                
                                
                            </div>
                        </div>
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Agregar Servicios</h3>
                                <button v-if="!disableFields() || isCreatingNota"
                                        class="btn btn-primary btn-sm"
                                        data-target="#productsModal"
                                        data-toggle="modal"
                                        type="button"
                                        @click="showModalProducts()"
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
                                            
                                            <th scope="col">Codigo</th>
                                            <th scope="col" >Detalle</th>
                                            <th scope="col" style="width:80px;">Cantidad</th>
                                            <th scope="col">Laboratorio</th>
                                            <th scope="col" style="width:90px;">Unid</th>
                                            <th scope="col" style="width:100px;">Precio Uni.</th>
                                            <th scope="col" style="width:110px;">% Desc</th>
                                            <th scope="col">Subtotal</th>
                                            <th scope="col">IVA</th>
                                            <th scope="col">Monto IVA</th>
                                            
                                            </tr>
                                        </thead>
                                        <tbody>
                                        
                                        <template v-for="(line, index) in invoice.lines" :key="line.id">
                                            <tr>
                                                <td>
                                                    <button 
                                                        v-if="!disableFields() || isCreatingNota"
                                                        class="btn btn-sm btn-danger"
                                                        type="button"
                                                        @click="removeLine(line, index)"
                                                        >
                                                        <span class="fa fa-remove"></span>
                                                    </button>
                                                </td>
                                               
                                                <td class="py-2 px-2">
                                                     <button v-if="!disableFields() || isCreatingNota" class="btn btn-primary btn-sm" data-target="#taxesModal"
                                                     data-toggle="modal"
                                                    title="Impuestos"
                                                     type="button" @click="showModalTaxes(line, index)" >
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
                                                    <input v-model="line.exo"
                                                           :data-target="'.multi-collapse-line'+ index"
                                                           data-toggle="collapse"
                                                           name="exo" title="Exonerar Linea" type="checkbox" @change="showExo(line, index)">
                                                </td>
                                                <th scope="row">{{ index + 1 }}</th>
                                               
                                                <td>
                                                    {{ line.Codigo }} <br>
                                                    <small>Cabys: {{ line.CodigoProductoHacienda }}</small>
                                                </td>
                                                <td>{{ line.Detalle }}</td>
                                                <td>
                                                    <input 
                                                        v-if="!disableFields() || isCreatingNota"
                                                        v-model="line.Cantidad"
                                                        class="form-control form-control-sm"
                                                        type="number"
                                                        @blur="refreshLine(line, index)"
                                                        @keydown.enter.prevent="refreshLine(line, index)"
                                                        >
                                                     <span v-else> {{ line.Cantidad }}</span>
                                                </td>
                                                <td>
                                                     <label>
                                                    <input v-model="line.laboratory"
                                                           class="flat-red"
                                                           name="laboratory"
                                                           title="Laboratorio"
                                                           type="checkbox">
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
                                                        v-if="!disableFields() || isCreatingNota"
                                                        v-model="line.PrecioUnitario"
                                                        class="form-control form-control-sm"
                                                        type="text"
                                                        @blur="refreshLine(line, index)"
                                                        @keydown.enter.prevent="refreshLine(line, index)"
                                                        >
                                                    <span v-else> {{ moneyFormat(line.PrecioUnitario,'') }}</span>
                                                   
                                                    </td>
                                                <td>
                                                    <template v-for="(discountLine, indexDiscount) in line.discounts" :key="indexDiscount">
                                                        <div :title="discountLine.NaturalezaDescuento">
                                                             <div v-if="!disableFields() || isCreatingNota" class="input-group " >
                                                               <input
                                                                v-model="discountLine.PorcentajeDescuento"
                                                                :readonly="discountLine.NoEditable"
                                                                class="form-control form-control-sm"
                                                                type="text"
                                                                @blur="refreshLine(line, index)"
                                                                @keydown.enter.prevent="refreshLine(line, index)"
                                                                >
                                                                <div class="input-group-btn">
                                                                    <button :disabled="discountLine.NoEditable" class="btn btn-primary" type="button" @click="removeDiscount(line, index, indexDiscount)"><i class="fa fa-close"></i></button>
                                                                </div>
                                                            </div>
                                                            
                                                            <span v-else> {{ discountLine.PorcentajeDescuento }}</span>
                                                            
                                                           
                                                        </div>
                                                        
                                                    
                                                    </template>
                                                     <button v-if="!disableFields() || isCreatingNota" class="btn btn-secondary btn-sm" type="button" @click="addDiscount(line)"><i class="fa fa-plus"></i></button>
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

                                                            <button :aria-controls="'collapseTax'+ index + indexTax" :data-target="'#collapseTax'+ index + indexTax" aria-expanded="false" class="btn btn-primary btn-sm" data-toggle="collapse" type="button">
                                                                {{ numberFormat(tax.PorcentajeExoneracion) }}% Exo
                                                            </button>
                                                            <div :id="'collapseTax'+ index + indexTax" :class="'collapse multi-collapse-line'+ index">
                                                                <div class="card card-body">
                                                                    <h4>Exoneración Impuesto {{ numberFormat(tax.TarifaOriginal) }}%</h4>
                                                                    <div class="form-row">
                                                                    
                                                                        <div class="form-group col-md-3">
                                                                            <label for="TipoDocumento">Tipo Documento</label>
                                                                            <select v-model="tax.TipoDocumento" :disabled="disableFields() && !isCreatingNota"  class="form-control custom-select" name="TipoDocumento">
                                                                            
                                                                            <option v-for="(value, key) in tipoDocumentosExo" :key="key" :value="key">
                                                                                {{ value }}
                                                                            </option>
                                                                            
                                                                            </select>
                                                                             <form-error v-if="errors.TipoDocumento" :errors="errors" style="float:right;">
                                                                                {{ errors.TipoDocumento[0] }}
                                                                            </form-error>
                                                                            
                                                                        </div>
                                                                        <div class="form-group col-md-3">
                                                                            <label for="NumeroDocumento">Numero Documento</label>
                                                                        
                                                                            <input id="NumeroDocumento" v-model="tax.NumeroDocumento" :disabled="disableFields() && !isCreatingNota" class="form-control" placeholder="" type="text" >
                                                                                
                                                                            <form-error v-if="errors.NumeroDocumento" :errors="errors" style="float:right;">
                                                                                {{ errors.NumeroDocumento[0] }}
                                                                            </form-error>
                                                                        </div>
                                                                        
                                                                        <div class="form-group col-md-6">
                                                                            <label for="NombreInstitucion">Nombre institución</label>
                                                                        
                                                                            <input id="NombreInstitucion" v-model="tax.NombreInstitucion" :disabled="disableFields() && !isCreatingNota" class="form-control" placeholder="" type="text"  >
                                                                                
                                                                            <form-error v-if="errors.NombreInstitucion" :errors="errors" style="float:right;">
                                                                                {{ errors.NombreInstitucion[0] }}
                                                                            </form-error>
                                                                        </div>
                                                                        
                                                                        <div class="form-group col-md-3">
                                                                            <label for="FechaEmision">Fecha Emisión</label>
                                                                        
                                                                            <flat-pickr
                                                                                    v-model="tax.FechaEmision"                                             
                                                                                    class="form-control" 
                                                                                    name="date"
                                                                                    placeholder="Select date">
                                                                            </flat-pickr>
                                                                            <form-error v-if="errors.FechaEmision" :errors="errors" style="float:right;">
                                                                                {{ errors.FechaEmision[0] }}
                                                                            </form-error>
                                                                        </div>

                                                                        <div class="form-group col-md-3">
                                                                            <label for="PorcentajeExoneracion">Porcentaje Exo.(0-13%)</label>
                                                                        
                                                                            <input id="PorcentajeExoneracion" v-model="tax.PorcentajeExoneracion" :disabled="disableFields() && !isCreatingNota" class="form-control" max="13" min="0" placeholder=""
                                                                            type="number"
                                                                            @blur="addExoneration(line, tax, index)" @keydown.enter.prevent="addExoneration(line, tax, index)" >
                                                                                
                                                                            <form-error v-if="errors.PorcentajeExoneracion" :errors="errors" style="float:right;">
                                                                                {{ errors.PorcentajeExoneracion[0] }}
                                                                            </form-error>
                                                                        </div>
                                                                        <div class="form-group col-md-3">
                                                                            <label for="MontoExoneracion">Monto Exoneración</label>
                                                                        
                                                                            <input id="MontoExoneracion" v-model="tax.MontoExoneracion" class="form-control" disabled placeholder="" type="text" >
                                                                                
                                                                            <form-error v-if="errors.MontoExoneracion" :errors="errors" style="float:right;">
                                                                                {{ errors.MontoExoneracion[0] }}
                                                                            </form-error>
                                                                        </div>

                                                                        <div class="form-group col-md-3">
                                                                            <label for="ImpuestoNeto">Impuesto Neto</label>
                                                                        
                                                                            <input id="ImpuestoNeto" v-model="tax.ImpuestoNeto" class="form-control" disabled placeholder="" type="text" >
                                                                                
                                                                            <form-error v-if="errors.ImpuestoNeto" :errors="errors" style="float:right;">
                                                                                {{ errors.ImpuestoNeto[0] }}
                                                                            </form-error>
                                                                        </div>
                                                                        <div class="form-group col-md-3">
                                                                            <button :aria-controls="'collapseTax'+ index + indexTax" :data-target="'#collapseTax'+ index + indexTax" aria-expanded="false" class="btn btn-primary btn-sm" data-toggle="collapse" type="button">
                                                                                Cerrar
                                                                            </button>
                                                                        </div>
                                                                        <div class="form-group col-md-3">
                                                                             <input v-model="exoall"
                                                                            name="exoall"
                                                                            title="Aplicar Exoneracion a todas las lineas"
                                                                            type="checkbox" @change="allLinesExo(line, tax, index)"> Aplicar Exoneracion a todas las lineas?
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
                                                <td :colspan="isCreatingNota ? 13 : 13" class="text-right">SubTotal:</td>
                                                <td> {{ moneyFormat(invoice.TotalVentaNeta,'') }} {{ invoice.CodigoMoneda }} </td>
                                            </tr>
                                            <tr>
                                                <td :colspan="isCreatingNota ? 13 : 13" class="text-right"> {{ titleDescuentos }}:</td>
                                                <td> {{ moneyFormat(invoice.TotalDescuentos) }} {{ invoice.CodigoMoneda }}</td>
                                            </tr>
                                            <tr>
                                                <td :colspan="isCreatingNota ? 13 : 13" class="text-right">Impuestos:</td>
                                                <td> {{ moneyFormat(invoice.TotalImpuesto,'') }} {{ invoice.CodigoMoneda }} </td>
                                            </tr>
                                            <tr v-show="invoice.MedioPago == '02'">
                                                <td :colspan="isCreatingNota ? 13 : 13" class="text-right">IVA Devuelto:</td>
                                                <td> -{{ moneyFormat(invoice.TotalIVADevuelto) }} {{ invoice.CodigoMoneda }}</td>
                                            </tr>
<!--                                            <tr v-show="affiliation.id">-->
<!--                                                <td :colspan="isCreatingNota ? 13 : 13" class="text-right">Pagar con Acumul:</td>-->
<!--                                                <td> <select v-model="invoice.utiliza_acumulado_afiliado" :disabled="!!isCreatingNota || !this.accumulated_affiliation" name="utiliza_acumulado_afiliado" @change="changeUtilizaAcumulado">-->
<!--                                                    <option :value="1">Si</option>-->
<!--                                                    <option :value="0">No</option>-->
<!--                                                    </select>-->
<!--                                                </td>-->
<!--                                            </tr>-->
                                            <!-- <tr v-show="affiliation.id">
                                                <td :colspan="isCreatingNota ? 13 : 13" class="text-right">Saldo Acumulado:</td>
                                                <td> <input type="text" name="acumulado" v-model="invoice.acumulado_utilizado" class="form-control" />
                                                   
                                                </td>
                                            </tr> -->
                                            <!-- <tr v-if="!invoice.id && invoice.utiliza_acumulado_afiliado == 1">
                                                <td :colspan="isCreatingNota ? 13 : 13" class="text-right">Total:</td>
                                                <td> {{ moneyFormat( totalComprobanteConAcumulado,'') }} {{ invoice.CodigoMoneda }}</td>
                                            </tr> -->
                                            <tr>
                                                <td :colspan="isCreatingNota ? 13 : 13" class="text-right">Total:</td>
                                                <td> {{ moneyFormat(invoice.TotalComprobante,'') }} {{ invoice.CodigoMoneda }}</td>
                                            </tr>
                                            
                                        </tbody>
                                        </table>
                                </div>
                               
                                
                            </div>
                            <div class="card-footer">
                                 <button v-if="!invoice.id" class="btn btn-default" type="button" @click="showReferencias = !showReferencias">Agregar Referencia</button>
                            </div>
                        </div> <!--card productos-->

                        <div v-if="isCreatingNota || invoice.referencias.length || showReferencias" class="box box-default">
                            <div class="box-header bg-primary text-white">
                                Documentos Referencias
                            </div>
                            <div class="box-body">
                                <div v-show="isCreatingNota || !invoice.status || showReferencias" class="form-referencias">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="TipoDocumento">Tipo Documento</label>
                                            <select v-model="referencia.TipoDocumento" :disabled="disableFields()" class="form-control custom-select" name="TipoDocumento">
                                        
                                            <option v-for="(value, key) in tipoDocumentos" :key="key" :value="key">
                                                {{ value }}
                                            </option>
                                            
                                            </select>
                                            <form-error v-if="errors.TipoDocumento" :errors="errors" style="float:right;">
                                                {{ errors.TipoDocumento[0] }}
                                            </form-error>
                                        </div>
                                    
                                        <div class="form-group col-md-4">
                                            <label for="NumeroDocumento">Número Documento</label>
                                            
                                            <input v-model="referencia.NumeroDocumento" class="form-control" placeholder="" type="text">
                                            <form-error v-if="errors.NumeroDocumento" :errors="errors" style="float:right;">
                                                {{ errors.NumeroDocumento[0] }}
                                            </form-error>    
                                            
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="FechaEmision">Fecha Emisión</label>
                                            <input v-model="referencia.FechaEmision" class="form-control" name="FechaEmision" placeholder="YYYY-MM-DD HH:mm" type="text">
                                            <form-error v-if="errors.FechaEmision" :errors="errors" style="float:right;">
                                                {{ errors.FechaEmision[0] }}
                                            </form-error>    
                                        </div>
                                    
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="CodigoReferencia">Código Referencia</label>
                                            <select id="CodigoReferencia" v-model="referencia.CodigoReferencia" class="form-control custom-select" name="CodigoReferencia">
                                        
                                            <option v-for="(value, key) in codigoReferencias" :key="key" :value="key">
                                                {{ value }}
                                            </option>
                                            
                                            </select>
                                            <form-error v-if="errors.CodigoReferencia" :errors="errors" style="float:right;">
                                                {{ errors.CodigoReferencia[0] }}
                                            </form-error>   
                                        </div>
                                    
                                        <div class="form-group col-md-6">
                                            <label for="Razon">Razón</label>
                                            
                                            <input id="Razon" v-model="referencia.Razon" class="form-control" placeholder="" type="text">
                                             <form-error v-if="errors.Razon" :errors="errors" style="float:right;">
                                                {{ errors.Razon[0] }}
                                            </form-error>      
                                            
                                        </div>

                                        <div class="form-group">
                                            <button class="btn btn-primary" type="button" @click="addReferencia()">Agregar Referencia</button>
                                        </div>
                                    
                                    </div>
                                </div>
                                <div v-show="invoice.referencias.length" class="table-responsive">
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
                                                        v-if="!disableFields() || isCreatingNota"
                                                        class="btn btn-sm btn-outline-danger"
                                                        type="button"
                                                        @click="removeReferencia(ref, index)"
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
                            <form-error v-if="errors.certificate" :errors="errors">
                            
                                <div class="callout callout-danger">
                                <h4>Información importante!!</h4>

                                <p> {{ errors.certificate[0] }}</p>
                                </div>
                            </form-error>
                         <div class="form-group">
                            <button v-if="( (!disableFields() || isCreatingNota) && canFE)" class="btn btn-primary" type="submit">Generar Factura</button>
                            <!-- <button type="submit" class="btn" :class="invoice.fe ? 'btn-secondary' : 'btn-primary'" v-if="!disableFields() || isCreatingNota">Guardar</button> -->
                            <button v-if="((!disableFields() || isCreatingNota) && isMedic() && canFE)" class="btn btn-primary" type="submit" @click="sendToAssistant = 1">Enviar a Secretaria</button>
                            <a v-if="invoice.id && (invoice.TipoDocumento == '01' || invoice.TipoDocumento == '04') && invoice.status && canFE" :href="'/invoices/'+ invoice.id +'/notacredito'" class="btn btn-primary" role="button">Nota de Crédito</a>
                            <a v-if="invoice.id && (invoice.TipoDocumento == '01' || invoice.TipoDocumento == '04') && invoice.status && canFE" :href="'/invoices/'+ invoice.id +'/notadebito'" class="btn btn-primary" role="button">Nota de Débito</a>
                            <a v-if="invoice.id && invoice.status" :href="'/invoices/'+ invoice.id +'/print'" class="btn btn-secondary" role="button">Imprimir</a>
                            <a v-if="invoice.id && invoice.status" :href="'/invoices/'+ invoice.id +'/download/pdf'" class="btn btn-secondary" role="button">Descargar PDF</a>
                             <button v-if="invoice.id && invoice.status" class="btn btn-secondary" type="button" @click="requestEmail(invoice)">Enviar por correo</button>
                        
                            <a class="btn btn-secondary" href="/invoices" role="button">Regresar</a>
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
       
        
        <customers-modal :endpoint="endpoint" :tipo-identificaciones="tipoIdentificaciones" @assigned="addCliente"></customers-modal>
        <products-modal :currencies="currencies" @assigned="addProduct"></products-modal>
        <modal-resumen-factura :affiliation="affiliation" :medio-pagos="medioPagos" @recalculateInvoice="calculateInvoice(invoice.lines)" @saveResumenFactura="persistOrUpdate()"></modal-resumen-factura>
        <taxes-modal @assigned="addTax" @remove="removeTax"></taxes-modal>

        <modal-paciente-gps-medica :tipo-identificaciones="tipoIdentificaciones"></modal-paciente-gps-medica>
        <modal-request-cedula @addClient="addCliente"></modal-request-cedula>
    </div>
    
</template>
<script>
import CustomersModal from './CustomersModal.vue';
import ProductsModal from './ProductsModal.vue';
import ModalResumenFactura from './ModalResumenFactura.vue';
import ModalRequestCedula from './ModalRequestCedula.vue';
import TaxesModal from './TaxesModal.vue';
import SelectMedic from './SelectMedic.vue';
import FormError from './FormError.vue';
import Loading from './Loading.vue';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
    props:['currentInvoice','tipoDocumentos','tipoDocumentosNotas','codigoReferencias','medioPagos','condicionVentas','currentTipoDocumento','isCreatingNota','offices', 'currentOffice','patient', 'tipoIdentificaciones','medic','appointment','currencies','medics','tipoDocumentosExo','proforma', 'activities', 'porc_discount_accumulated', 'endpoint'],
    data () {
        return {
            invoice:{
                CodigoActividad:'',
                TipoDocumento: this.currentTipoDocumento ? this.currentTipoDocumento : '04',
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
                change:0,
                utiliza_acumulado_afiliado:0,
                affiliation_id:0,
                acumulado_utilizado:0,
               
               
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
            customerDiscountLab:0,
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
            affiliation:{},
            possiblePatient:{},
            activitiesHacienda: this.activities ? this.activities : [],
            isGPSUser:false,
            isAffiliateUser:false,
            applyDiscountAffiliate:false,
            discountsAffiliate:0,
            customerDiscountUserGPS:0,
            currentAcumuladoNota: 0,
            accumulated_affiliation: false,
            currentAcumuladoUtilizado:0
           
           
           
        };
    },
    components:{
        ProductsModal,
        CustomersModal,
        ModalResumenFactura,
        ModalRequestCedula,
        TaxesModal,
        FormError,
        Loading,
        flatPickr,
        SelectMedic
    },
    watch: {
        customerDiscount() {
            this.setDiscount();
        },
    },
    computed:{
        titleDescuentos(){
            return (this.invoice.affiliation_id && this.invoice.utiliza_acumulado_afiliado) ? 'Descuentos Afiliado' : 'Descuentos';
        },
        isAssistant(){
            return window.App.isAssistant;
        },
        canFE(){
           
            if( !this.isMedic() ){
                return true;
            }

            return window.App.subscriptionPlanHasFe;
        },
        isLab(){
            return window.App.isLab;
        },
        //    totalComprobanteConAcumulado(){
        //        let total = 0;
        //        total = ((this.invoice.TotalComprobante - this.invoice.acumulado_utilizado) < 0) ? 0 : (this.invoice.TotalComprobante - this.invoice.acumulado_utilizado);

        //        return total;
        //    }
       
      
    },
    methods:{
    //    sendAssistant(){
    //        this.sendToAssistant = 1
    //    },
        selectedMedic(medic){
            this.invoice.user_id = medic?.id ?? null;
            this.accumulated_affiliation = medic?.accumulated_affiliation ?? 0;
            //this.invoice.utiliza_acumulado_afiliado = this.accumulated_affiliation ? 1 : 0;
        
            if(!this.accumulated_affiliation || (!this.isCreatingNota && !this.invoice.id) || !this.invoice.status){
                this.invoice.utiliza_acumulado_afiliado = 0;
            }
            this.changeUtilizaAcumulado();
        },
        changeMedic(){
            const medic = this.medics.find( m => m.id == this.invoice.user_id);
            this.accumulated_affiliation = medic.accumulated_affiliation ?? 0;
            //this.invoice.utiliza_acumulado_afiliado = 0;//this.accumulated_affiliation ? 1 : 0;
            if(!this.accumulated_affiliation || (!this.isCreatingNota && !this.invoice.id) || !this.invoice.status){
                this.invoice.utiliza_acumulado_afiliado = 0;
            }
            this.changeUtilizaAcumulado();
        },
        changeUtilizaAcumulado(){
            
            if(this.affiliation.id){
                this.invoice.affiliation_id = this.affiliation.id;

                if(this.invoice.utiliza_acumulado_afiliado == 1){
                  
                    this.calculateAccumulatedDiscount();
                    // this.setDiscount();
                    this.invoice.acumulado_utilizado =  this.currentAcumuladoUtilizado;//this.invoice.TotalDescuentos;
                }else{
                    this.customerDiscount = 0;
                    this.invoice.acumulado_utilizado = 0;
                }
                
            }
        },
        changeTipoIdentificacion(){
            
            if(this.invoice.tipo_identificacion_cliente == '00' && this.invoice.TipoDocumento != '04'){

                Swal.fire({
                    title: 'Facturacion a extranjero',
                    html: 'Para facturar a un extranjero solo se puede con Tiquete Electrónico. Deseas realizar el cambio?',
                    showCancelButton: true,
                    confirmButtonColor: '#67BC9A',
                    cancelButtonColor: '#dd4b39',
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
          
            const officeSelected = _.find(this.offices, { 'id':  this.invoice.office_id });
            let activities = [];

            if(officeSelected && officeSelected.config_factura.length){
                
                activities = officeSelected.config_factura[0].activities;
            }

            if(activities && activities.length){
                this.activitiesHacienda = activities;
                this.CodigoActividad = [];
                if(this.activitiesHacienda.length === 1){
                    this.invoice.CodigoActividad = this.activitiesHacienda[0].codigo;
                }
            }


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
       

        async addCliente(cliente){
            this.affiliation = {};
           
            this.invoice.customer_id = cliente.id;
            this.invoice.cliente = cliente.fullname;
            this.invoice.email = cliente.email;
            this.invoice.tipo_identificacion_cliente = cliente.tipo_identificacion ? cliente.tipo_identificacion : '';
            this.invoice.identificacion_cliente = cliente.ide;

            
         
            
            const discountPatient = cliente.discounts[0]; //descuento asignado a paciente
            if(discountPatient?.id){
                this.invoice.discount_id = discountPatient.id;
            }
            
            if(cliente.accumulateds && cliente.accumulateds.length){ //descuento afiliacion a clinica monserrat
                this.affiliation = cliente.accumulateds[0];
                this.invoice.affiliation_id = this.affiliation.id;
                //    if(parseFloat(this.affiliation.acumulado) > 0 && this.accumulated_affiliation){
                //         this.invoice.utiliza_acumulado_afiliado = 1
                //         //this.invoice.affiliation_id = this.affiliation.id;
                //    }
               
                //this.invoice.acumulado_utilizado = this.invoice.TotalDescuentos <= parseFloat(this.affiliation.acumulado) ? this.invoice.TotalDescuentos : 0;
                //this.customerDiscount = parseFloat(this.porc_discount_accumulated ?? 0);
                //let discount_lab = this.affiliation.plan ? this.affiliation.plan.discount_lab : 0;
                //this.customerDiscountLab = parseFloat(discount_lab);
               
            }

            this.calculateAccumulatedDiscount();
            //this.setDiscount();

            this.verifyIsPatientGpsMedica();
           
        },
        calculateAccumulatedDiscount(){
           
            let acumulado = 0;
            if(this.affiliation.id && !this.isCreatingNota && this.invoice.status){
                acumulado = parseFloat(this.affiliation.acumulado);
                this.invoice.affiliation_id = this.affiliation.id;
            }
            else if(this.isCreatingNota || !this.invoice.status){
                acumulado = parseFloat(this.currentAcumuladoNota);
            }
           
            if(this.invoice.utiliza_acumulado_afiliado){
                const defaultDiscount = parseFloat(this.porc_discount_accumulated ?? 0);
                const maxAccumulatedDiscount = (defaultDiscount / 100) * this.invoice.TotalVenta;
                if(maxAccumulatedDiscount <= acumulado){
                    this.customerDiscount = defaultDiscount;
                    if(this.isCreatingNota || !this.invoice.status){
                        this.invoice.acumulado_utilizado =  this.currentAcumuladoUtilizado; //this.invoice.TotalDescuentos;
                    }else{
                        this.invoice.acumulado_utilizado = maxAccumulatedDiscount;
                    }
                        
                }else{
  
                    if(this.isCreatingNota || !this.invoice.status || (!this.isCreatingNota && this.invoice.id)){
                        // this.customerDiscount =  redondeo((this.invoice.TotalDescuentos / this.invoice.TotalVenta) * 100);
                        this.invoice.acumulado_utilizado = this.currentAcumuladoUtilizado;//this.invoice.TotalDescuentos;
                    }else{
                        this.customerDiscount =  redondeo((acumulado / this.invoice.TotalVenta) * 100);
                        this.invoice.acumulado_utilizado = (this.customerDiscount / 100) * this.invoice.TotalVenta;
                    }
                        
                }

            }else{
                this.customerDiscount = 0;
                this.invoice.acumulado_utilizado = 0;
            }
           

        },
        async verifyIsPatientGpsMedica(){

            await axios.get('/general/patients/'+ this.invoice.identificacion_cliente +'/verifyispatientgpsmedica')
                .then(({data}) =>{

                

                    //    this.possiblePatient = {
                    //         email: (data['patient'] && data['patient']['email']) ? data['patient']['email'] : this.invoice.email,
                    //         ide: data['patient'] ? data['patient']['ide'] : this.invoice.identificacion_cliente,
                    //         tipo_identificacion: data['patient'] ? data['patient']['tipo_identificacion'] : this.invoice.tipo_identificacion_cliente,
                    //         first_name: this.invoice.cliente,
                    //         isPatient: data['isPatient'],
                    //         patientHasAccount: data['patientHasAccount'],
                    //         phone_country_code: data['patient'] ? data['patient']['phone_country_code'] : '+506',
                    //         phone_number: data['patient'] ? data['patient']['phone_number'] : '',
                    //         id: data['patient'] ? data['patient']['id'] : null
                    //     }

                    if(data['patientHasAccount']){
                        this.isGPSUser = true;
                        this.customerDiscountUserGPS = +data['lab_exam_discount'];
                        if(this.invoice.lines.length){
                            this.setDiscount();
                        }
                    }
                    //Modificación para determinar si el paciente cuenta con un plan de afiliación grupo g1
                    if(data['affiliation'] && data['acceptedDiscountAffiliation'] == 1){
                        this.isAffiliateUser = true;
                        this.applyDiscountAffiliate = true;
                        this.discountsAffiliate = data['affiliation'].discount;
                    }
                  //Fin de las modificaciones para determinar si el paciente cuenta con un plan de afiliación grupo g1
                });
       
        },
        addProduct(product){
            this.createInvoiceLine(product);
          
        },
        removeLine(product, index){
            this.invoice.lines.splice(index, 1);
            this.calculateInvoice(this.invoice.lines);
            this.calculateAccumulatedDiscount();
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
                this.invoice.TipoCambio = moneda.exchange_venta;
            }
                
           

            this.invoice.lines.forEach((line, index) => {

                if(line.product && line.product.CodigoMoneda){
                  
                    line.PrecioUnitario = this.convertMonedaAmount(line.PrecioOriginalTemp, line.product.CodigoMoneda);
                      
                    this.refreshLine(line, index);
                        
                }
                   
            });
             
            

        },
        addDiscount(line){

            if(line.discounts.length < 5){

                line.discounts.push({
                    PorcentajeDescuento: 0,
                    MontoDescuento: 0,
                    NaturalezaDescuento: 'Descuento Cliente'
                });

            }
          
        },
        removeDiscount(line, index, indexDiscount ){
            line.discounts.splice(indexDiscount, 1);
            this.refreshLine(line, index);
           
        },
        setDiscount(){
   
            const vm = this;
            const discounts = [];
      
            if(this.isGPSUser && this.customerDiscountUserGPS && this.isLab){

                discounts.push({
                    PorcentajeDescuento: this.customerDiscountUserGPS,
                    MontoDescuento: 0,
                    NaturalezaDescuento: 'Descuento Usuario de Doctor Blue',
                    NoEditable:true
                });


            }
            //Inicio de modificaciones grupo g1
            if(this.isAffiliateUser && this.applyDiscountAffiliate){
                discounts.push({
                    PorcentajeDescuento: this.discountsAffiliate,
                    MontoDescuento: 0,
                    NaturalezaDescuento: 'Descuento Afiliado'
                });
            }

            //Fin de Modificaciones grupo g1    
            const discount = _.find( this.discounts, function(o) {
                return o.id === vm.invoice.discount_id;
            });

            if(discount){

                discounts.push({
                    PorcentajeDescuento: discount.tarifa,
                    MontoDescuento: 0,
                    NaturalezaDescuento: 'Descuento empresarial: '+ discount.name
                });

            }

            discounts.push({
                PorcentajeDescuento: this.customerDiscount,
                MontoDescuento: 0,
                NaturalezaDescuento: 'Descuento paciente'
            });
            this.invoice.lines.forEach((line, index) => {
                
                line.discounts = discounts;
                    
                // if(this.affiliation.id){
                //     if(line.type == 'S' && line.laboratory){
                //         line.PorcentajeDescuento = this.customerDiscountLab;
                //     }else if(line.type == 'S'){
                //         line.PorcentajeDescuento = this.customerDiscount;
                //     }
                //     line.NaturalezaDescuento = 'Descuento de Afiliado Clinica Monserrat';

                //     line.discounts.push({
                //         PorcentajeDescuento: line.PorcentajeDescuento,
                //         MontoDescuento: 0,
                //         NaturalezaDescuento: line.NaturalezaDescuento
                //     })
                // }
                    
                this.refreshLine(line, index);
            });
            
           
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
                    PrecioUnitario: product.price ? this.convertMonedaAmount(product.price, product.CodigoMoneda) : 0,
                    MontoTotal: 0,
                    PorcentajeDescuento: 0,
                    MontoDescuento: 0,
                    discounts: [],
                    NaturalezaDescuento:'',
                    SubTotal: 0,
                    MontoTotalLinea: 0,
                    totalTaxes:0,
                    taxes: product.taxes,
                    type: product.type,
                    laboratory: product.laboratory,
                    PrecioOriginalTemp: product.price,
                    product:product,
                    is_servicio_medico: product.is_servicio_medico,
                    reference_commission: product.reference_commission,
                    no_aplica_commission: product.no_aplica_commission,
                    overrideImp: false
                       
                       
                };
                  
                const line = this.calculateInvoiceLine(nuevo);

                this.invoice.lines.push(line);
                
                
               
                this.setDiscount();
                
            }
            
            this.calculateInvoice(this.invoice.lines);
            this.calculateAccumulatedDiscount();
        },

        refreshLine(line, index){
            this.updateInvoiceLine(this.calculateInvoiceLine(line, index), index);
        },

        updateInvoiceLine(line, index){
          
            this.invoice.lines.splice(index, 1, line);
            this.calculateInvoice(this.invoice.lines);
            this.calculateAccumulatedDiscount();
        },
        allLinesExo(lineInvoice, lineTax, lineInvoiceindex){
           
            this.invoice.lines.forEach((line, index) => {
               
                if(this.exoall){
                    line.exo = true;
                    line.taxes.forEach(tax => {
                        // tax.name = tax.name;
                        tax.tarifa = lineTax.tarifa;
                        tax.TipoDocumento = lineTax.TipoDocumento;
                        tax.NumeroDocumento = lineTax.NumeroDocumento;
                        tax.NombreInstitucion = lineTax.NombreInstitucion;
                        tax.FechaEmision = lineTax.FechaEmision;
                        tax.PorcentajeExoneracion = lineTax.PorcentajeExoneracion;
                        tax.ImpuestoOriginal = lineTax.ImpuestoOriginal;
                        //tax.TarifaOriginal = tax.TarifaOriginal;
                        
                
                    });
                }else{
                    if(index != lineInvoiceindex){
                        line.exo = false;
                        line.taxes.forEach(tax => {
                            
                            //tax.name = tax.name;
                            tax.tarifa = tax.TarifaOriginal;
                            tax.amount = tax.ImpuestoOriginal;
                            tax.TipoDocumento = '';
                            tax.NumeroDocumento = '';
                            tax.NombreInstitucion = '';
                            tax.FechaEmision = null;
                            tax.PorcentajeExoneracion = 0;
                            tax.MontoExoneracion = 0;
                            //tax.ImpuestoOriginal = tax.ImpuestoOriginal;
                            
                    
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
                   
                    //tax.name = tax.name;
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
        calculateExoneration(line, lineTax/*, index*/){
          
           
            const taxes = [];
            let PorcentajeExo = 0;
            let ImpuestoNeto = 0;
            let MontoExoneracion = 0;
            
            const lineasTaxes = (line.product && line.product.taxes && line.product.taxes.length && !line.overrideImp) ? line.product.taxes : line.taxes;

            lineasTaxes.forEach(tax => {
                
                const tarifa = parseFloat(tax.TarifaOriginal ? tax.TarifaOriginal : tax.tarifa);
                const subTotal = (tax.code == '07') ? line.BaseImponible : line.SubTotal; //IVA especial se utliza base imponible

                const MontoImpuesto = redondeo((parseFloat(tarifa)/100) * subTotal, 5); // se roundM por problemas de decimales de hacienda
            
                ImpuestoNeto = MontoImpuesto;

                if(line.exo /* && (!this.invoice.id || this.isCreatingNota) */ ){
                    
                    PorcentajeExo = parseFloat(lineTax.PorcentajeExoneracion ? lineTax.PorcentajeExoneracion : 0);

                    if(PorcentajeExo > tarifa){
                        PorcentajeExo = tarifa;
                        lineTax.PorcentajeExoneracion = tarifa;
                    }
                   
                    MontoExoneracion = redondeo((PorcentajeExo / 100) * subTotal, 5); // se cambió MontoImpuesto por subTotaldel cambio del 1 de julio 2020
                

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
            const discounts = [];
            const MontoTotal = line.PrecioUnitario * line.Cantidad;

            /********* Multiple Discounts */
            let SubTotal = MontoTotal;
            let MontoTotalDescuentos = 0;
             
            line.discounts.forEach(discount => {

                const MontoDescuentoLinea = redondeo((discount.PorcentajeDescuento / 100) * SubTotal, 5);
                SubTotal = MontoTotal - MontoDescuentoLinea;
                MontoTotalDescuentos += MontoDescuentoLinea;

                discounts.push({
                    PorcentajeDescuento: discount.PorcentajeDescuento,
                    MontoDescuento: MontoDescuentoLinea,
                    NaturalezaDescuento: discount.NaturalezaDescuento,
                    NoEditable: discount.NoEditable,
                });
            });
            
            const MontoDescuento = MontoTotalDescuentos; //redondeo((line.PorcentajeDescuento / 100) * MontoTotal, 5);
            SubTotal = MontoTotal - MontoDescuento;
            /********************* */

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
                const MontoImpuesto = redondeo((parseFloat(tax.tarifa)/100) * subTotalbase, 5);
                        

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
            line.discounts = discounts;
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
                                
                                
                                const porcenExoProporcion = 100-((parseFloat(tax.tarifa) - parseFloat(tax.PorcentajeExoneracion)) * (100 / parseFloat(tax.tarifa)));
                               
                                TotalMercanciasGravadas += (1 - porcenExoProporcion/100) * line.MontoTotal;
                                  
                                TotalMercExonerada += (porcenExoProporcion/100) * line.MontoTotal;

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
                                  
                                const porcenExoProporcion = 100-((parseFloat(tax.tarifa) - parseFloat(tax.PorcentajeExoneracion)) * (100 / parseFloat(tax.tarifa)));
                                       
                                TotalServGravados += (1-porcenExoProporcion/100) * line.MontoTotal;
                                    
                                TotalServExonerado += (porcenExoProporcion/100) * line.MontoTotal;
                                   
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

            if(this.invoice.utiliza_acumulado_afiliado){
                this.invoice.acumulado_utilizado = TotalDescuentos;
            }
            
        
           
            return this.invoice;


        },
        convertMonedaAmount(amount, MonedaProducto){

            if(!MonedaProducto){ return amount;}
           
            let result = amount;
            let tipoC = parseFloat(this.invoice.TipoCambio);

            if(this.invoice.CodigoMoneda == MonedaProducto){
                return amount;
            }



            const monedaDolar = _.find( this.currencies, (currency) => {
              
                return currency.code === 'USD';
            });

            if(monedaDolar){
                tipoC = monedaDolar.exchange_venta;
            }

            if(this.invoice.CodigoMoneda == 'USD' && MonedaProducto == 'CRC'){

                result = amount / (tipoC <= 0 ? 1 : tipoC);

            }else{
                result = amount * (tipoC <= 0 ? 1 : tipoC);
            }

            return redondeo(result);
        },
        save(){
            if(!this.invoice.user_id && this.isLab){
                Swal.fire({
                    title: 'Médico',
                    html: 'Necesitas seleccionar un médico o el administrador del laboratorio para poder facturar',
                    showCancelButton: false,
                    confirmButtonColor: '#67BC9A',
                    cancelButtonColor: '#dd4b39',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Ok'
                }).then( () => {
    

                });

                return;
            }

            if(this.invoice.tipo_identificacion_cliente == '00' && this.invoice.TipoDocumento != '04'){
                Swal.fire({
                    title: 'Facturación Extranjero',
                    html: 'Para facturar a un extranjero tiene que ser un Tiquete Electrónico',
                    showCancelButton: false,
                    confirmButtonColor: '#67BC9A',
                    cancelButtonColor: '#dd4b39',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Ok'
                }).then( () => {
    

                });

                return;
            }

            if(!this.invoice.lines.length){
                Swal.fire({
                    title: 'lineas de detalle requerida',
                    html: 'Necesitar agregar al menos una linea para poder crear la factura',
                    showCancelButton: false,
                    confirmButtonColor: '#67BC9A',
                    cancelButtonColor: '#dd4b39',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Ok'
                }).then( () => {
    

                });

                return;
            }
            if(this.isCreatingNota && this.invoice.TipoDocumento != '01' && !this.invoice.referencias.length)
            {
                Swal.fire({
                    title: 'Documento de referencia requerido',
                    html: 'Necesitar agregar al menos un documento de referencia para poder crear la nota',
                    showCancelButton: false,
                    confirmButtonColor: '#67BC9A',
                    cancelButtonColor: '#dd4b39',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Ok'
                }).then( () => {
    

                }, () => { });

                return;
            }

            const errorM = {};
            let faltaCodigoCabys = false;
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
                if(!line.CodigoProductoHacienda){
                    faltaCodigoCabys = true;
                }
                
                this.errors = errorM;
            });

            if(!_.isEmpty(this.errors)){

                Swal.fire({
                    title: 'Información de exoneración requerido o erronea',
                    html: 'En algunas de las lineas que tienen exoneración falta o hay información erronea. Revisa!',
                    showCancelButton: false,
                    confirmButtonColor: '#67BC9A',
                    cancelButtonColor: '#dd4b39',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Ok'
                }).then( () => {
    

                });

                return;
            }

            if(this.sendToAssistant){
                this.invoice.status = 0;
            }

            if(this.invoice.identificacion_cliente){
                this.verifyIsPatientGpsMedica();
            }
          
            if(faltaCodigoCabys){

                Swal.fire({
                    title: 'Información de requerida',
                    html: 'En algunas de las lineas falta la informacion del código Cabys. ¿Deseas continuar? esto podria rechazar la factura por parte de Hacienda',
                    showCancelButton: true,
                    confirmButtonColor: '#67BC9A',
                    cancelButtonColor: '#dd4b39',
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Continuar'
                }).then( (result) => {
                    if(result.value){
                        this.continuarFacturacion();
                    }

                });

               
            }else{

                this.continuarFacturacion();

            }
           

           
        },
        continuarFacturacion(){
            if((this.invoice.TipoDocumento == '01' || this.invoice.TipoDocumento == '04') && this.invoice.MedioPago == '01'&& !this.sendToAssistant){

                $('#modalResumenFactura').modal();
                this.emitter.emit('showResumenFacturaModal', this.invoice);
                        
            }else{
                            
                if(this.invoice.id && !this.invoice.status && !this.isCreatingNota){
                    this.update();
                }else{

                    this.persist();
                }
            }
        },
        persistOrUpdate(){

            if(this.invoice.id && !this.invoice.status && !this.isCreatingNota){
                this.update();
            }else{

                this.persist();
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
                    confirmButtonColor: '#67BC9A',
                    cancelButtonColor: '#dd4b39',
                    cancelButtonText: 'Volver al panel de facturación',
                    confirmButtonText: 'Nueva Factura'
                }).then( (result) => {
                
                    
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        
                        window.location = '/invoices';
                        
                    }else{
                        //console.log('click afuera del popup')
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
                    confirmButtonColor: '#67BC9A',
                    cancelButtonColor: '#dd4b39',
                    cancelButtonText: 'Correo',
                    confirmButtonText: 'Imprimir'
                }).then( (result) => {
                    
                    if (result.value) {
                        window.location = '/invoices/' + invoice.id + '/print';

                    }else if (result.dismiss === Swal.DismissReason.cancel) {
                        
                        this.requestEmail(invoice);
                        
                    }/*else{ //se desabilito por que son los pacientes los que se tienen que registrar
                        
                        if( _.isEmpty(this.possiblePatient) || (this.possiblePatient.isPatient && this.possiblePatient.patientHasAccount) ) return;

                        $('#modalPacienteGpsMedica').modal();
                        this.emitter.emit('showPacienteGpsMedicaModal', this.possiblePatient)
                       // window.location = '/invoices'

                    }*/


                    

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
                confirmButtonColor: '#67BC9A',
                cancelButtonColor: '#dd4b39',
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
                        .then(() => {})
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

                        if( _.isEmpty(this.possiblePatient) || (this.possiblePatient.isPatient && this.possiblePatient.patientHasAccount) ) return;

                        $('#modalPacienteGpsMedica').modal();
                        this.emitter.emit('showPacienteGpsMedicaModal', this.possiblePatient);
                
                    }else{
                    
                        if( _.isEmpty(this.possiblePatient) || (this.possiblePatient.isPatient && this.possiblePatient.patientHasAccount) ) return;
                    
                        $('#modalPacienteGpsMedica').modal();

                        this.emitter.emit('showPacienteGpsMedicaModal', this.possiblePatient);
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
                CodigoActividad:'',
                TipoDocumento:'04',
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
                change:0,
                utiliza_acumulado_afiliado:0,
                affiliation_id:0,
                acumulado_utilizado:0
               
            };
            this.affiliation = {},
            this.code = '';
            this.customerDiscount = 0;
            this.customerDiscountLab = 0;
            this.isGPSUser = false;
            this.customerDiscountUserGPS = 0;
            this.currentAcumuladoNota = 0;
            this.discountsAffiliate = 0; // Se añade el descuento afiliado grupo g1
            this.applyDiscountAffiliate = false; // Se añade la aplicacion del descuento afiliado grupo g1
            this.accumulated_affiliation = false;
            this.currentAcumuladoUtilizado = 0;

            this.emitter.emit('clearSelectMedics');
           
            if(this.activitiesHacienda && this.activitiesHacienda.length === 1){
                this.invoice.CodigoActividad = this.activitiesHacienda[0].codigo;
            }
            

        }
       
       
    },
    mounted(){
        setTimeout(() =>{ // se pone esto por bootstrap esta despues del app.js hay q esperarlo

            if(!this.currentInvoice /*&& !this.proforma*/){
                $('#modalRequestCedula').modal();
                this.emitter.emit('showRequestCedulaModal', this.invoice);
            }

        }, 100);
      
    },
   
    created(){
        this.emitter.on('setGpsUserDiscount', (data) => {
            //console.log('seteando tarfia de descuento')
            this.customerDiscountUserGPS = parseFloat(data?.tarifa);
            this.isGPSUser = true;
        
        });

        this.setTipoCambio();
        this.loadDiscounts();

        let por_descuento = 0;

        if(this.currentInvoice){
        
            this.invoice = this.currentInvoice;
            this.currentAcumuladoUtilizado = this.invoice.acumulado_utilizado;   
            this.affiliation = this.invoice.affiliation ?? {};
            this.loadDiscounts();

            if(this.currentTipoDocumento){
                this.invoice.TipoDocumento = this.currentTipoDocumento;
            }
        
            if(this.isCreatingNota){
                this.invoice.referencias = [];
                this.currentAcumuladoNota = parseFloat(this.affiliation?.acumulado ?? 0) + parseFloat(this.invoice.acumulado_utilizado);
            }
           
       
            this.invoice.lines.forEach((line, index) => {
                line.updateStock = 0;
                por_descuento = parseFloat(line.PorcentajeDescuento);
                if(por_descuento > 0 &&
                  line.MontoDescuento > 0 &&
                  line.discounts.length == 0){
                    line.discounts.push({
                        PorcentajeDescuento: por_descuento,
                        MontoDescuento: line.MontoDescuento,
                        NaturalezaDescuento: line.NaturalezaDescuento ?? 'Descuento Cliente'
                    });
                }

                this.refreshLine(line, index);
            });
            this.calculateInvoice(this.invoice.lines);

         
        }else if(this.proforma){
            this.invoice = this.proforma;
            this.invoice.referencias = [];
            this.invoice.status = 1;
            delete this.invoice.id;
            delete this.invoice.created_at;
            delete this.invoice.updated_at;

            this.invoice.lines.forEach((line, index) => {
                line.updateStock = 0;
                delete line.id;
                delete line.proforma_id;
                delete line.created_at;
                delete line.updated_at;

                por_descuento = parseFloat(line.PorcentajeDescuento);
               
                if(por_descuento > 0 &&
                  line.MontoDescuento > 0 &&
                  line.discounts.length == 0){
                    line.discounts.push({
                        PorcentajeDescuento: por_descuento,
                        MontoDescuento: line.MontoDescuento,
                        NaturalezaDescuento: line.NaturalezaDescuento ?? 'Descuento Cliente'
                    });
                }
                this.refreshLine(line, index);
            });
            this.calculateInvoice(this.invoice.lines);

            this.searchCustomer(this.invoice.identificacion_cliente);

        }else{
           
            this.accumulated_affiliation = 1;

            if(this.medics.length === 1){
                this.invoice.user_id = this.medics[0].id;
                this.accumulated_affiliation = this.medics[0].accumulated_affiliation ?? 0;
            }
          
        }

       

    }

};
</script>
