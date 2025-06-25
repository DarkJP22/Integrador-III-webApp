<template>
    <div class="invoice-form">
        <loading :show="loader"></loading>

        <form @submit.prevent="save()">
            <div class="row">

                <div class="col-md-12">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Proforma</h3> <small v-if="medic">({{ medic.name }})</small>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="TipoDocumento">Tipo Documento</label>
                                    <select id="TipoDocumento" v-model="proforma.TipoDocumento" :disabled="true" class="form-control custom-select" name="TipoDocumento">

                                        <option v-for="(value, key) in tipoDocumentos" :key="key" :value="key">
                                            {{ value }}
                                        </option>

                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="MedioPago">Medio Pago</label>
                                    <select id="MedioPago" v-model="proforma.MedioPago" :disabled="disableFields()" class="form-control custom-select" name="MedioPago" @change="calculateProforma(proforma.lines)">

                                        <option v-for="(value, key) in medioPagos" :key="key" :value="key">
                                            {{ value }}
                                        </option>

                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="CodigoMoneda">Moneda</label>
                                    <select id="CodigoMoneda" v-model="proforma.CodigoMoneda" :disabled="disableFields()" class="form-control custom-select" name="CodigoMoneda" @change="setTipoCambio()">

                                        <option v-for="(item) in currencies" :key="item.code" :value="item.code">
                                            {{ item.name }}
                                        </option>

                                    </select>
                                </div>

                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="CondicionVenta">Condición de venta</label>
                                    <select id="CondicionVenta" v-model="proforma.CondicionVenta" :disabled="disableFields()" class="form-control custom-select" name="CondicionVenta">

                                        <option v-for="(value, key) in condicionVentas" :key="key" :value="key">
                                            {{ value }}
                                        </option>

                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="PlazoCredito">Plazo de credito</label>
                                    <!-- <input type="text" class="form-control" name="PlazoCredito" v-model="proforma.PlazoCredito" :disabled="proforma.CondicionVenta != '02' || disableFields()" > -->
                                    <flat-pickr v-model="proforma.PlazoCredito" class="form-control" name="PlazoCredito" placeholder="Selecione una fecha">
                                    </flat-pickr>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="PlazoCredito">Abono Inicial</label>
                                    <input v-model="proforma.initialPayment" :disabled="proforma.CondicionVenta != '02' || disableFields()" class="form-control" name="initialPayment" type="text">
                                </div>

                            </div>
                            <div class="form-row">

                                <div :class="!isMedic() ? 'col-md-6': 'col-md-12'" class="form-group">
                                    <label for="office_id">Consultorio</label>
                                    <select id="office_id" v-model="proforma.office_id" :disabled="disableFields()" class="form-control" name="office_id" required @change="loadDiscounts()">

                                        <option v-for="(item, index) in offices" :key="item.id" :selected="index == 0 ? 'selected' : '' " :value="item.id"> {{ item.name }}</option>

                                    </select>
                                </div>
                                <div v-if="isLab" class="form-group col-md-6">
                                    <label for="user_id">Médico</label>
                                    <select-medic :disabled="!!disableFields()" :medic="medic" :url="'/lab/medics'" @selectedMedic="selectedMedic"></select-medic>
                                </div>
                                <div v-else v-show="!isMedic()" :class="!isMedic() ? 'col-md-6': 'col-md-12'" class="form-group">
                                    <label for="user_id">Médico</label>
                                    <select id="user_id" v-model="proforma.user_id" :disabled="disableFields()" class="form-control" name="user_id" required>
                                        <option value=""></option>
                                        <option v-for="(item, index) in medics" :key="item.id" :selected="index == 0 ? 'selected' : '' " :value="item.id"> {{ item.name }}</option>

                                    </select>
                                </div>


                            </div>
                            <div class="form-row">


                                <div class="form-group col-md-12">
                                    <label for="observations">Observaciones</label>
                                    <textarea v-model="proforma.observations" :disabled="disableFields()" class="form-control" cols="30" name="observations" rows="2"></textarea>


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

                                <div class="form-group col-md-6">
                                    <label for="email">Nombre Cliente</label>
                                    <div class="input-group input-group">
                                        <input v-model="proforma.cliente" class="form-control" name="cliente" type="text" @keyup="listenCliente($event)" @keydown.prevent.enter="searchCustomer(proforma.cliente)">
                                        <div class="input-group-btn">
                                            <button class="btn btn-primary" data-target="#customersModal" data-toggle="modal" type="button" @click="showModalCustomers()">Buscar</button>
                                        </div>
                                    </div>
                                    <!-- <input type="text" class="form-control" name="cliente" v-model="proforma.cliente" @keyup="listenCliente($event)" @keydown.prevent.enter="searchCustomer(proforma.cliente)" > -->
                                    <form-error v-if="errors.cliente" :errors="errors" style="float:right;">
                                        {{ errors.cliente[0] }}
                                    </form-error>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="MedioPago">Email</label>
                                    <input v-model="proforma.email" class="form-control" name="email" type="email">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="MedioPago">Descuento Empresarial</label>
                                    <select id="discount_id" v-model="proforma.discount_id" :disabled="disableFields()" class="form-control custom-select" name="discount_id" @change="setDiscount()" >

                                        <option value=""></option>
                                        <option v-for="(discount) in discounts" :key="discount.id" :value="discount.id" >
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
                            <button v-if="!disableFields() || isCreatingNota" class="btn btn-primary btn-sm" data-target="#productsModal" data-toggle="modal" type="button" @click="showModalProducts()"><i class="fa fa-plus"></i></button>

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
                                            <th scope="col">Detalle</th>
                                            <th scope="col" style="width:90px;">Cantidad</th>
                                            <th scope="col">Laboratorio</th>
                                            <th scope="col" style="width:80px;">Unid</th>
                                            <th scope="col" style="width:100px;">Precio Uni.</th>
                                            <th scope="col" style="width:110px;">% Desc</th>
                                            <th scope="col">Subtotal</th>
                                            <th scope="col">IVA</th>
                                            <th scope="col">Monto IVA</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        <template v-for="(line, index) in proforma.lines" :key="line.id">
                                            <tr >
                                                <td>
                                                    <button v-if="!disableFields() || isCreatingNota" class="btn btn-sm btn-danger" type="button" @click="removeLine(line, index)">
                                                        <span class="fa fa-remove"></span>
                                                    </button>
                                                </td>

                                                <td class="py-2 px-2">
                                                    <button v-if="!disableFields() || isCreatingNota" class="btn btn-primary btn-sm" data-target="#taxesModal" data-toggle="modal" title="Impuestos" type="button" @click="showModalTaxes(line, index)">
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
                                                    <input v-model="line.exo" :data-target="'.multi-collapse-line'+ index" data-toggle="collapse" name="exo" title="Exonerar Linea" type="checkbox" @change="showExo(line, index)">
                                                </td>
                                                <th scope="row">{{ index + 1 }}</th>

                                                <!-- <td>{{ line.Codigo }}</td> -->
                                                <td>{{ line.Detalle }}</td>
                                                <td>
                                                    <input v-if="!disableFields() || isCreatingNota" v-model="line.Cantidad" class="form-control form-control-sm" type="number" @blur="refreshLine(line, index)" @keydown.enter.prevent="refreshLine(line, index)">
                                                    <span v-else> {{ line.Cantidad }}</span>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input v-model="line.laboratory" class="flat-red" name="laboratory" title="Laboratorio" type="checkbox">
                                                        Servicio Lab.
                                                    </label>
                                                    <!-- <input type="checkbox"
                                                           name="laboratory"
                                                           v-model="line.laboratory"
                                                           title="Laboratorio"> -->
                                                </td>
                                                <td>{{ line.UnidadMedida }}</td>
                                                <td>
                                                    <input v-if="!disableFields() || isCreatingNota" v-model="line.PrecioUnitario" class="form-control form-control-sm" type="text" @blur="refreshLine(line, index)" @keydown.enter.prevent="refreshLine(line, index)">
                                                    <span v-else> {{ moneyFormat(line.PrecioUnitario,'') }}</span>

                                                </td>
                                                <td>
                                                    <template v-for="(discountLine, indexDiscount) in line.discounts" :key="indexDiscount">
                                                        <div  :title="discountLine.NaturalezaDescuento">
                                                            <div v-if="!disableFields() || isCreatingNota" class="input-group ">
                                                                <input v-model="discountLine.PorcentajeDescuento" :readonly="discountLine.NoEditable" class="form-control form-control-sm" type="text" @blur="refreshLine(line, index)" @keydown.enter.prevent="refreshLine(line, index)">
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
                                                <td>
                                                    <div v-for="(tax, indexTaxD) in line.taxes" :key="indexTaxD">{{ numberFormat(tax.tarifa) }}%</div>
                                                </td>
                                                <td>
                                                    <div v-for="(tax, indexTaxD) in line.taxes" :key="indexTaxD">{{ moneyFormat(tax.amount) }}</div>
                                                </td>
                                            </tr>
                                            <tr v-show="line.exo">
                                                <td colspan="12">
                                                    <div class="box-exo">
                                                        <div v-for="(tax, indexTax) in line.taxes" :key="tax.id">

                                                            <button :aria-controls="'collapseTax'+ index + indexTax" :data-target="'#collapseTax'+ index + indexTax" aria-expanded="false" class="btn btn-primary btn-sm" data-toggle="collapse" type="button">
                                                                {{ numberFormat(tax.PorcentajeExoneracion) }}% Exo
                                                            </button>
                                                            <div :id="'collapseTax'+ index + indexTax" :class="'collapse multi-collapse-line'+ index">
                                                                <div class="card card-body">
                                                                    <h4>Exoneración Impuesto {{ numberFormat(tax.TarifaOriginal) }}%</h4>
                                                                    <div class="form-row">

                                                                        <div class="form-group col-md-3">
                                                                            <label for="TipoDocumento">Tipo Documento</label>
                                                                            <select v-model="tax.TipoDocumento" :disabled="disableFields() && !isCreatingNota" class="form-control custom-select" name="TipoDocumento">

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

                                                                            <input v-model="tax.NumeroDocumento" :disabled="disableFields() && !isCreatingNota" class="form-control" name="NumeroDocumento" placeholder="" type="text">

                                                                            <form-error v-if="errors.NumeroDocumento" :errors="errors" style="float:right;">
                                                                                {{ errors.NumeroDocumento[0] }}
                                                                            </form-error>
                                                                        </div>

                                                                        <div class="form-group col-md-6">
                                                                            <label for="NombreInstitucion">Nombre institución</label>

                                                                            <input v-model="tax.NombreInstitucion" :disabled="disableFields() && !isCreatingNota" class="form-control" name="NombreInstitucion" placeholder="" type="text">

                                                                            <form-error v-if="errors.NombreInstitucion" :errors="errors" style="float:right;">
                                                                                {{ errors.NombreInstitucion[0] }}
                                                                            </form-error>
                                                                        </div>

                                                                        <div class="form-group col-md-3">
                                                                            <label for="FechaEmision">Fecha Emisión</label>

                                                                            <flat-pickr v-model="tax.FechaEmision" class="form-control" name="date" placeholder="Select date">
                                                                            </flat-pickr>
                                                                            <form-error v-if="errors.FechaEmision" :errors="errors" style="float:right;">
                                                                                {{ errors.FechaEmision[0] }}
                                                                            </form-error>
                                                                        </div>

                                                                        <div class="form-group col-md-3">
                                                                            <label for="PorcentajeExoneracion">Porcentaje Exoneración</label>

                                                                            <input v-model="tax.PorcentajeExoneracion" :disabled="disableFields() && !isCreatingNota" class="form-control" name="PorcentajeExoneracion" placeholder="" type="text" @blur="addExoneration(line, tax, index)" @keydown.enter.prevent="addExoneration(line, tax, index)">

                                                                            <form-error v-if="errors.PorcentajeExoneracion" :errors="errors" style="float:right;">
                                                                                {{ errors.PorcentajeExoneracion[0] }}
                                                                            </form-error>
                                                                        </div>
                                                                        <div class="form-group col-md-3">
                                                                            <label for="MontoExoneracion">Monto Exoneración</label>

                                                                            <input v-model="tax.MontoExoneracion" class="form-control" disabled name="MontoExoneracion" placeholder="" type="text">

                                                                            <form-error v-if="errors.MontoExoneracion" :errors="errors" style="float:right;">
                                                                                {{ errors.MontoExoneracion[0] }}
                                                                            </form-error>
                                                                        </div>

                                                                        <div class="form-group col-md-3">
                                                                            <label for="ImpuestoNeto">Impuesto Neto</label>

                                                                            <input v-model="tax.ImpuestoNeto" class="form-control" disabled name="ImpuestoNeto" placeholder="" type="text">

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
                                                                            <input v-model="exoall" name="exoall" title="Aplicar Exoneracion a todas las lineas" type="checkbox" @change="allLinesExo(line, tax, index)"> Aplicar Exoneracion a todas las lineas?
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
                                            <td> {{ moneyFormat(proforma.TotalVentaNeta,'') }} {{ proforma.CodigoMoneda }} </td>
                                        </tr>
                                        <tr>
                                            <td :colspan="isCreatingNota ? 13 : 12" class="text-right">Descuentos</td>
                                            <td> {{ moneyFormat(proforma.TotalDescuentos) }} {{ proforma.CodigoMoneda }}</td>
                                        </tr>
                                        <tr>
                                            <td :colspan="isCreatingNota ? 13 : 12" class="text-right">Impuestos:</td>
                                            <td> {{ moneyFormat(proforma.TotalImpuesto,'') }} {{ proforma.CodigoMoneda }} </td>
                                        </tr>
                                        <tr v-show="proforma.MedioPago == '02'">
                                            <td :colspan="isCreatingNota ? 13 : 12" class="text-right">IVA Devuelto:</td>
                                            <td> -{{ moneyFormat(proforma.TotalIVADevuelto) }} {{ proforma.CodigoMoneda }}</td>
                                        </tr>

                                        <tr v-if="!proforma.id && proforma.utiliza_acumulado_afiliado == 1">
                                            <td :colspan="isCreatingNota ? 13 : 12" class="text-right">Total:</td>
                                            <td> {{ moneyFormat( totalComprobanteConAcumulado,'') }} {{ proforma.CodigoMoneda }}</td>
                                        </tr>
                                        <tr v-else>
                                            <td :colspan="isCreatingNota ? 13 : 12" class="text-right">Total:</td>
                                            <td> {{ moneyFormat(proforma.TotalComprobante,'') }} {{ proforma.CodigoMoneda }}</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>


                        </div>

                    </div>
                    <!--card productos-->



                    <div class="form-group">
                        <button v-if="(!disableFields() || isCreatingNota)" class="btn btn-primary" type="submit">Guardar Proforma</button>
                        <!-- <button type="submit" class="btn" :class="proforma.fe ? 'btn-secondary' : 'btn-primary'" v-if="!disableFields() || isCreatingNota">Guardar</button> -->


                        <a v-if="proforma.id && proforma.status" :href="'/proformas/'+ proforma.id +'/print'" class="btn btn-secondary" role="button">Imprimir</a>
                        <a v-if="proforma.id && proforma.status" :href="'/proformas/'+ proforma.id +'/download/pdf'" class="btn btn-secondary" role="button">Descargar PDF</a>
                        <button v-if="proforma.id && proforma.status" class="btn btn-secondary" type="button" @click="requestEmail(proforma)">Enviar por correo</button>

                        <a v-if="proforma.id" :href="'/invoices/create?pr='+proforma.id" class="btn btn-primary" role="button">Generar Factura</a>

                        <a class="btn btn-secondary" href="/proformas" role="button">Regresar</a>
                    </div>
                </div>
                <!--col-->
                <!-- <div class="col-md-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white"></div>

                            <div class="card-body">
                                
                                
                                
                            </div>
                        </div>
                    </div> -->



            </div>

        </form>


        <customers-modal :tipo-identificaciones="tipoIdentificaciones" @assigned="addCliente"></customers-modal>
        <products-modal :currencies="currencies" @assigned="addProduct"></products-modal>

        <taxes-modal @assigned="addTax" @remove="removeTax"></taxes-modal>

        <modal-paciente-gps-medica :tipo-identificaciones="tipoIdentificaciones"></modal-paciente-gps-medica>
        <modal-request-cedula></modal-request-cedula>
    </div>

</template>
<script>
import CustomersModal from './CustomersModal.vue';
import ProductsModal from './ProductsModal.vue';
import ModalRequestCedula from './ModalRequestCedula.vue';
import TaxesModal from './TaxesModal.vue';
import FormError from './FormError.vue';
import Loading from './Loading.vue';
import SelectMedic from './SelectMedic.vue';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
    props: ['currentProforma', 'tipoDocumentos', 'tipoDocumentosNotas', 'codigoReferencias', 'medioPagos', 'condicionVentas', 'currentTipoDocumento', 'isCreatingNota', 'offices', 'currentOffice', 'patient', 'tipoIdentificaciones', 'medic', 'appointment', 'currencies', 'medics', 'tipoDocumentosExo'],
    data() {
        return {
            proforma: {
                TipoDocumento: this.currentTipoDocumento ? this.currentTipoDocumento : '04',
                customer_id: this.patient ? this.patient.id : 0,
                cliente: this.patient ? this.patient.fullname : '',
                email: this.patient ? this.patient.email : '',
                user_id: this.medic ? this.medic.id : false,
                appointment_id: this.appointment ? this.appointment.id : 0,
                tipo_identificacion_cliente: '',
                identificacion_cliente: '',
                CodigoMoneda: 'CRC',
                TipoCambio: 1,
                MedioPago: '01',
                CondicionVenta: '01',
                PlazoCredito: '',
                TotalServGravados: 0,
                TotalServExentos: 0,
                TotalServExonerado: 0,
                TotalMercanciasGravadas: 0,
                TotalMercanciasExentas: 0,
                TotalMercExonerada: 0,
                TotalGravado: 0,
                TotalExento: 0,
                TotalExonerado: 0,
                TotalVenta: 0,
                TotalDescuentos: 0,
                TotalVentaNeta: 0,
                TotalImpuesto: 0,
                TotalIVADevuelto: 0,
                TotalComprobante: 0,
                lines: [],
                initialPayment: '',
                office_id: this.currentOffice ? this.currentOffice.id : 0,
                status: 1,
                discount_id: 0,
                observations: '',
                pay_with: 0,
                change: 0,


            },

            code: '',
            customerDiscount: 0,
            customerDiscountLab: 0,
            errors: [],
            loader: false,
            sendToAssistant: 0,
            cambio: false,
            discounts: [],
            exoall: false,
            showReferencias: false,


            possiblePatient: {},
            customerDiscountUserGPS: 0,
            isGPSUser: false



        };
    },
    components: {
        ProductsModal,
        CustomersModal,
        ModalRequestCedula,
        TaxesModal,
        FormError,
        Loading,
        flatPickr,
        SelectMedic
    },
    computed: {
        isAssistant() {
            return window.App.isAssistant;
        },
        isLab() {
            return window.App.isLab;
        },



    },
    methods: {
        selectedMedic(medic) {
            this.proforma.user_id = medic?.id;

        },
        changeTipoIdentificacion() {

            if (this.proforma.tipo_identificacion_cliente == '00' && this.proforma.TipoDocumento != '04') {

                Swal.fire({
                    title: 'Facturacion a extranjero',
                    html: 'Para facturar a un extranjero solo se puede con Tiquete Electrónico. Deseas realizar el cambio?',
                    showCancelButton: true,
                    confirmButtonColor: '#67BC9A',
                    cancelButtonColor: '#dd4b39',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Si'
                }).then((result) => {

                    if (result.value) {
                        this.proforma.TipoDocumento = '04'; // si es extranjero pasamos la factura a tiquete
                    }

                });



            }
        },
        isMedic() {
            return window.App.isMedic;
        },
        disableFields() {

            return false; //(this.proforma.id && this.proforma.status)
        },
        tipoDocumentoName(tipoDocumento) {
            return _.get(this.tipoDocumentosNotas, tipoDocumento);
        },

        discountName(discountId) {
            const discount = _.find(this.discounts, { 'id': discountId });

            return discount ? discount.name : '';
        },
        numberFormat(n) {
            if (n) {

                return parseFloat(n).format(0);
            }
            return 0;

        },
        moneyFormat(n) {

            if (typeof n === 'number') {

                return n.format(2);
            }

            return n;
        },

        searchCustomer(q) {

            axios.get(`/invoices/patients?q=${q}`)
                .then(({ data }) => {

                    this.proforma.customer_id = 0;
                    this.proforma.email = '';
                    //this.proforma.cliente = '';

                    if (data.data) {

                        if (data.data.length == 1) {
                            this.addCliente(data.data[0]);
                            flash('Paciente Agregado');

                        } else {
                            //flash('Paciente No encontrado', 'danger');
                            $('#customersModal').modal();
                            this.emitter.emit('showCustomersModal', { searchTerm: q });
                        }

                    } else {
                        flash('Paciente No encontrado', 'danger');
                    }
                });


        },
        listenCliente(e) {

            if (!e.target.value) {
                this.proforma.customer_id = 0;
            }
        },
        showModalTaxes(line, index) {
            const data = {
                line: line,
                index: index
            };
            this.emitter.emit('showTaxesModal', data);

        },
        removeTax(data) {
            const line = data.dataLine.line;
            const index = data.dataLine.index;
            const tax = data.tax;


            const indexTax = _.findIndex(line.taxes, { 'code': tax.code });

            line.taxes.splice(indexTax, 1);
            line.overrideImp = true;

            this.refreshLine(line, index);


        },
        addTax(data) {
            const line = data.dataLine.line;
            const index = data.dataLine.index;
            const tax = data.tax;


            line.taxes.push(tax);
            line.overrideImp = true;

            this.refreshLine(line, index);


        },
        showModalCustomers() {

            this.emitter.emit('showCustomersModal', '');

        },
        showModalProducts() {

            this.emitter.emit('showProductsModal', this.proforma.office_id);

        },
        loadDiscounts() {

            axios.get(`/discounts?office=${this.proforma.office_id}`)
                .then(({ data }) => {

                    this.discounts = data.data;

                    this.setPatient();


                });
        },
        searchProduct() {

            axios.get(`/products?code=${this.code}`)
                .then(data => {
                    if (data.data) {
                        this.addProduct(data.data);
                        flash('Servicio Agregado');
                    } else {
                        flash('Servicio No encontrado', 'danger');
                    }
                });
        },


        async addCliente(cliente) {


            this.proforma.customer_id = cliente.id;
            this.proforma.cliente = cliente.fullname;
            this.proforma.email = cliente.email;
            this.proforma.tipo_identificacion_cliente = cliente.tipo_identificacion ? cliente.tipo_identificacion : '';
            this.proforma.identificacion_cliente = cliente.ide;
            // const discount = 0;


            await this.verifyIsPatientGpsMedica();  //descuento de usuario Doctor Blue

            const discountPatient = cliente.discounts[0]; //descuento asignado a paciente
            if (discountPatient?.id) {
                this.proforma.discount_id = discountPatient.id;
            }


            this.setDiscount();


        },
        async verifyIsPatientGpsMedica() {

            await axios.get('/general/patients/' + this.proforma.identificacion_cliente + '/verifyispatientgpsmedica')
                .then(({ data }) => {



                    this.possiblePatient = {
                        email: (data['patient'] && data['patient']['email']) ? data['patient']['email'] : this.proforma.email,
                        ide: data['patient'] ? data['patient']['ide'] : this.proforma.identificacion_cliente,
                        tipo_identificacion: data['patient'] ? data['patient']['tipo_identificacion'] : this.proforma.tipo_identificacion_cliente,
                        first_name: this.proforma.cliente,
                        isPatient: data['isPatient'],
                        patientHasAccount: data['patientHasAccount'],
                        phone_country_code: data['patient'] ? data['patient']['phone_country_code'] : '+506',
                        phone_number: data['patient'] ? data['patient']['phone_number'] : '',
                        id: data['patient'] ? data['patient']['id'] : null
                    };

                    if (data['patientHasAccount']) {
                        this.isGPSUser = true;
                        this.customerDiscountUserGPS = data['discount']?.tarifa;

                    }


                });

        },
        addProduct(product) {
            this.createProformaLine(product);

        },
        removeLine(product, index) {
            this.proforma.lines.splice(index, 1);
            this.calculateProforma(this.proforma.lines);
        },
        setPatient() {
            if (this.patient) {

                const firstDiscount = _.head(this.discounts);


                if (firstDiscount) {

                    const discount = _.find(this.patient.discounts, function (o) {
                        return o.user_id === firstDiscount.user_id;
                    });

                    this.proforma.discount_id = discount ? discount.id : 0;

                }


            }
        },
        setTipoCambio() {

            const moneda = _.find(this.currencies, (currency) => {

                return currency.code === this.proforma.CodigoMoneda;
            });

            if (moneda) {
                this.proforma.TipoCambio = moneda.exchange_venta;
            }



            this.proforma.lines.forEach((line, index) => {

                if (line.product && line.product.CodigoMoneda) {

                    line.PrecioUnitario = this.convertMonedaAmount(line.PrecioOriginalTemp, line.product.CodigoMoneda);

                    this.refreshLine(line, index);

                }

            });



        },
        addDiscount(line) {

            if (line.discounts.length < 5) {

                line.discounts.push({
                    PorcentajeDescuento: 0,
                    MontoDescuento: 0,
                    NaturalezaDescuento: 'Descuento Cliente'
                });

            }

        },
        removeDiscount(line, index, indexDiscount) {
            line.discounts.splice(indexDiscount, 1);
            this.refreshLine(line, index);

        },
        setDiscount() {

            const vm = this;

            const discounts = [];

            if (this.isGPSUser && this.customerDiscountUserGPS) {

                discounts.push({
                    PorcentajeDescuento: this.customerDiscountUserGPS,
                    MontoDescuento: 0,
                    NaturalezaDescuento: 'Descuento Usuario de Doctor Blue',
                    NoEditable: true
                });


            }
            const discount = _.find(this.discounts, function (o) {
                return o.id === vm.proforma.discount_id;
            });

            if (discount) {

                discounts.push({
                    PorcentajeDescuento: discount.tarifa,
                    MontoDescuento: 0,
                    NaturalezaDescuento: 'Descuento empresarial: ' + discount.name
                });

            }

            discounts.push({
                PorcentajeDescuento: 0,
                MontoDescuento: 0,
                NaturalezaDescuento: 'Descuento paciente'
            });

            this.proforma.lines.forEach((line, index) => {

                line.discounts = discounts;

                this.refreshLine(line, index);
            });



        },

        createProformaLine(product) {


            const lineIndexFound = _.findIndex(this.proforma.lines, function (o) {
                return o.Codigo === product.id;
            });
            const lineFound = _.find(this.proforma.lines, function (o) {
                return o.Codigo === product.id;
            });

            if (lineFound && lineIndexFound !== -1) {
                lineFound.Cantidad++;

                this.updateProformaLine(this.calculateProformaLine(lineFound), lineIndexFound);

            } else {

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
                    NaturalezaDescuento: '',
                    discounts: [],
                    SubTotal: 0,
                    MontoTotalLinea: 0,
                    totalTaxes: 0,
                    taxes: product.taxes,
                    type: product.type,
                    laboratory: product.laboratory,
                    PrecioOriginalTemp: product.price,
                    product: product,
                    is_servicio_medico: product.is_servicio_medico,
                    overrideImp: false


                };

                const line = this.calculateProformaLine(nuevo);

                this.proforma.lines.push(line);


                this.setDiscount();

            }

            this.calculateProforma(this.proforma.lines);

        },

        refreshLine(line, index) {
            this.updateProformaLine(this.calculateProformaLine(line, index), index);
        },

        updateProformaLine(line, index) {

            this.proforma.lines.splice(index, 1, line);
            this.calculateProforma(this.proforma.lines);
        },
        allLinesExo(lineProforma, lineTax, lineProformaindex) {

            this.proforma.lines.forEach((line, index) => {

                if (this.exoall) {
                    line.exo = true;
                    line.taxes.forEach(tax => {
                        //tax.name = tax.name;
                        tax.tarifa = lineTax.tarifa;
                        tax.TipoDocumento = lineTax.TipoDocumento;
                        tax.NumeroDocumento = lineTax.NumeroDocumento;
                        tax.NombreInstitucion = lineTax.NombreInstitucion;
                        tax.FechaEmision = lineTax.FechaEmision;
                        tax.PorcentajeExoneracion = lineTax.PorcentajeExoneracion;
                        tax.ImpuestoOriginal = lineTax.ImpuestoOriginal;
                        //tax.TarifaOriginal = tax.TarifaOriginal;


                    });
                } else {
                    if (index != lineProformaindex) {
                        line.exo = false;
                        line.taxes.forEach(tax => {

                            // tax.name = tax.name;
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
        showExo(line, index) {

            $('.multi-collapse-line' + index).addClass('show');

            if (!line.exo) {
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

        addExoneration(line, lineTax, index) {

            if (!this.proforma.id || this.isCreatingNota) {
                this.calculateExoneration(line, lineTax, index);
            }
            this.updateProformaLine(this.calculateProformaLine(line, index), index);

        },
        calculateExoneration(line, lineTax/*, index*/) {


            const taxes = [];
            let PorcentajeExo = 0;
            let ImpuestoNeto = 0;
            let MontoExoneracion = 0;

            const lineasTaxes = (line.product && line.product.taxes && line.product.taxes.length && !line.overrideImp) ? line.product.taxes : line.taxes;

            lineasTaxes.forEach(tax => {

                const tarifa = parseFloat(tax.TarifaOriginal ? tax.TarifaOriginal : tax.tarifa);
                const subTotal = (tax.code == '07') ? line.BaseImponible : line.SubTotal; //IVA especial se utliza base imponible

                const MontoImpuesto = redondeo((parseFloat(tarifa) / 100) * subTotal, 5); // se roundM por problemas de decimales de hacienda

                ImpuestoNeto = MontoImpuesto;

                if (line.exo /* && (!this.proforma.id || this.isCreatingNota) */) {

                    PorcentajeExo = parseFloat(lineTax.PorcentajeExoneracion ? lineTax.PorcentajeExoneracion : 0);

                    MontoExoneracion = redondeo((PorcentajeExo / 100) * MontoImpuesto, 5);


                    ImpuestoNeto = MontoImpuesto - MontoExoneracion;


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

        calculateProformaLine(line, index) {

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
                const MontoImpuesto = redondeo((parseFloat(tax.tarifa) / 100) * subTotalbase, 5);


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
        calculateProforma(lines) {

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

                if (line.type == 'P') {

                    if (line.taxes.length) {

                        line.taxes.forEach(tax => {
                            if (line.exo) {


                                TotalMercanciasGravadas += (1 - parseFloat(tax.PorcentajeExoneracion) / 100) * line.MontoTotal;

                                TotalMercExonerada += (parseFloat(tax.PorcentajeExoneracion) / 100) * line.MontoTotal;

                            } else {
                                TotalMercanciasGravadas += parseFloat(line.MontoTotal);
                                TotalMercExonerada += 0;
                            }
                        });


                    } else {
                        TotalMercanciasExentas += parseFloat(line.MontoTotal);
                        TotalMercExonerada += 0;
                    }

                } else { // type S : Servicio
                    if (line.taxes.length) {

                        line.taxes.forEach(tax => {
                            if (line.exo) {



                                TotalServGravados += (1 - parseFloat(tax.PorcentajeExoneracion) / 100) * line.MontoTotal;

                                TotalServExonerado += (parseFloat(tax.PorcentajeExoneracion) / 100) * line.MontoTotal;

                            } else {
                                TotalServGravados += parseFloat(line.MontoTotal);
                                TotalServExonerado += 0;
                            }


                            //IVA devuelto para servicios medicos pagados con tarjeta
                            if (line.is_servicio_medico && this.proforma.MedioPago == '02') {
                                TotalIVADevuelto += tax.ImpuestoNeto;
                            }
                        });




                    } else {
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

            this.proforma.TotalMercanciasGravadas = TotalMercanciasGravadas;
            this.proforma.TotalMercanciasExentas = TotalMercanciasExentas;
            this.proforma.TotalMercExonerada = TotalMercExonerada;

            this.proforma.TotalServGravados = TotalServGravados;
            this.proforma.TotalServExentos = TotalServExentos;
            this.proforma.TotalServExonerado = TotalServExonerado;
            this.proforma.TotalGravado = TotalGravado;
            this.proforma.TotalExento = TotalExento;
            this.proforma.TotalExonerado = TotalExonerado;
            this.proforma.TotalVenta = TotalVenta;
            this.proforma.TotalDescuentos = TotalDescuentos;
            this.proforma.TotalVentaNeta = TotalVentaNeta;
            this.proforma.TotalImpuesto = TotalImpuesto;
            this.proforma.TotalIVADevuelto = TotalIVADevuelto;
            this.proforma.TotalComprobante = TotalComprobante;


            // para vuelto
            PagoCon = parseFloat(this.proforma.pay_with);
            Vuelto = PagoCon - this.proforma.TotalComprobante;
            this.proforma.change = Vuelto < 0 ? 0 : Vuelto;




            return this.proforma;


        },
        convertMonedaAmount(amount, MonedaProducto) {

            if (!MonedaProducto) { return amount; }

            let result = amount;
            let tipoC = parseFloat(this.proforma.TipoCambio);

            if (this.proforma.CodigoMoneda == MonedaProducto) {
                return amount;
            }



            const monedaDolar = _.find(this.currencies, (currency) => {

                return currency.code === 'USD';
            });

            if (monedaDolar) {
                tipoC = monedaDolar.exchange_venta;
            }

            if (this.proforma.CodigoMoneda == 'USD' && MonedaProducto == 'CRC') {

                result = amount / (tipoC <= 0 ? 1 : tipoC);

            } else {
                result = amount * (tipoC <= 0 ? 1 : tipoC);
            }

            return redondeo(result);
        },
        save() {



            if (!this.proforma.lines.length) {
                Swal.fire({
                    title: 'lineas de detalle requerida',
                    html: 'Necesitar agregar al menos una linea para poder crear la factura',
                    showCancelButton: false,
                    confirmButtonColor: '#67BC9A',
                    cancelButtonColor: '#dd4b39',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Ok'
                }).then(() => {


                });

                return;
            }


            const errorM = {};
            this.proforma.lines.forEach(line => {
                if (line.exo) {
                    line.taxes.forEach(tax => {
                        if (!tax.TipoDocumento) {
                            errorM.TipoDocumento = ['Tipo de documento requerido'];

                        }
                        if (!tax.NumeroDocumento) {
                            errorM.NumeroDocumento = ['Numero de documento requerido'];

                        }
                        if (tax.NumeroDocumento && tax.NumeroDocumento.length > 17) {
                            errorM.NumeroDocumento = ['Numero de documento tiene que ser de 17 caracteres'];

                        }
                        if (!tax.NombreInstitucion) {
                            errorM.NombreInstitucion = ['Nombre de la institución requerido'];

                        }
                        if (!tax.FechaEmision) {
                            errorM.FechaEmision = ['Fecha Emisión requerido'];

                        }
                        if (!tax.PorcentajeExoneracion) {
                            errorM.PorcentajeExoneracion = ['Porcentaje Exoneración requerido'];

                        }

                    });

                }
                this.errors = errorM;
            });
            if (!_.isEmpty(this.errors)) {

                Swal.fire({
                    title: 'Información de exoneración requerido o erronea',
                    html: 'En algunas de las lineas que tienen exoneración falta o hay información erronea. Revisa!',
                    showCancelButton: false,
                    confirmButtonColor: '#67BC9A',
                    cancelButtonColor: '#dd4b39',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Ok'
                }).then(() => {


                });

                return;
            }

            if (this.sendToAssistant) {
                this.proforma.status = 0;
            }








            if (this.proforma.id) {
                this.update();
            } else {

                this.persist();
            }



        },
        persistOrUpdate() {

            if (this.proforma.id) {
                this.update();
            } else {

                this.persist();
            }
        },
        update() {

            if (this.loader) {
                return;
            }

            this.loader = true;
            axios.put(`/proformas/${this.proforma.id}`, this.proforma)
                .then(({ data }) => {
                    this.loader = false;
                    this.clearForm();
                    flash('Proforma Guardada Correctamente.');
                    this.$emit('created', data);

                    this.actions(data);


                })
                .catch(error => {

                    this.loader = false;
                    if (error.response.status == 500 || error.response.status == 504) {
                        this.clearForm();

                        flash('La Proforma fue creada, pero ocurrió un error. ' + error.response.data.message, 'danger');

                    } else if (error.response.status == 422) {
                        flash('Error al enviar Proforma', 'danger');

                    } else {

                        flash(error.response.data.message, 'danger');
                    }

                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });

        },
        persist() {
            if (this.loader) {
                return;
            }

            this.loader = true;
            axios.post('/proformas', this.proforma)
                .then(({ data }) => {
                    this.loader = false;
                    this.clearForm();
                    flash('Proforma Guardada Correctamente.');
                    this.$emit('created', data);

                    this.actions(data);



                })
                .catch(error => {

                    this.loader = false;
                    if (error.response.status == 500 || error.response.status == 504) {
                        this.clearForm();

                        flash('La Proforma fue creada, pero ocurrió un error. ' + error.response.data.message, 'danger');

                    } else {
                        flash('Error al guardar la proforma!!', 'danger');
                    }

                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });
        },
        actions(proforma) {



            let message = '¿Deseas Imprimir o enviar por correo?';
            const cambio = proforma.change ? parseFloat(proforma.change) : 0;
            if (cambio >= 0) {
                message = '<h3>Cambio: ' + this.moneyFormat(cambio) + ' ' + proforma.CodigoMoneda + ' .</h3> ¿Deseas Imprimir o enviar por correo?';
            }

            Swal.fire({
                title: 'Proforma Guardada',
                html: message,
                showCancelButton: true,
                confirmButtonColor: '#67BC9A',
                cancelButtonColor: '#dd4b39',
                cancelButtonText: 'Correo',
                confirmButtonText: 'Imprimir'
            }).then((result) => {

                if (result.value) {
                    window.location = '/proformas/' + proforma.id + '/print';

                } else if (result.dismiss === Swal.DismissReason.cancel) {

                    this.requestEmail(proforma);

                } else {
                    console.log('click afuera del popup');

                    window.location = '/proformas';

                }




            });






        },
        requestEmail(proforma) {
            Swal.fire({
                title: 'Correo',
                input: 'text',
                inputPlaceholder: '',
                showCancelButton: true,
                confirmButtonText: 'Enviar',
                confirmButtonColor: '#67BC9A',
                cancelButtonColor: '#dd4b39',
                showLoaderOnConfirm: true,
                inputValue: proforma.email ? proforma.email : '',
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

                    return axios.post('/proformas/' + proforma.id + '/email/pdf', { to: email })
                        .then(() => { })
                        .catch(error => {

                            Swal.showValidationError(
                                `Request failed: ${error}`
                            );

                            flash('Error al enviar la proforma por correo!!', 'danger');
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()

            })
                .then((result) => {


                    if (result.value) {

                        Swal.fire({
                            title: 'Proforma Enviada Correctamente',

                        });



                    }


                });
        },


        clearForm() {

            this.proforma = {
                TipoDocumento: '04',
                customer_id: 0,
                cliente: '',
                user_id: false,
                appointment_id: 0,
                email: '',
                tipo_identificacion_cliente: '',
                identificacion_cliente: '',
                CodigoMoneda: 'CRC',
                MedioPago: '01',
                CondicionVenta: '01',
                PlazoCredito: '',
                TotalServGravados: 0,
                TotalServExentos: 0,
                TotalServExonerado: 0,
                TotalMercanciasGravadas: 0,
                TotalMercanciasExentas: 0,
                TotalMercExonerada: 0,
                TotalGravado: 0,
                TotalExento: 0,
                TotalExonerado: 0,
                TotalVenta: 0,
                TotalDescuentos: 0,
                TotalVentaNeta: 0,
                TotalImpuesto: 0,
                TotalIVADevuelto: 0,
                TotalComprobante: 0,
                lines: [],
                referencias: [],
                initialPayment: '',
                office_id: this.currentOffice ? this.currentOffice.id : 0,
                status: 1,
                discount_id: 0,
                observations: '',
                pay_with: 0,
                change: 0,


            };

            this.code = '';
            this.customerDiscount = 0;
            this.customerDiscountUserGPS = 0;
            this.isGPSUser = false;



        }


    },
    mounted() {
        // setTimeout(() =>{ // se pone esto por bootstrap esta despues del app.js hay q esperarlo

        //      if(!this.currentProforma /*&& !this.proforma*/){
        //             $('#modalRequestCedula').modal();
        //             this.emitter.emit('showRequestCedulaModal', this.proforma)
        //         }

        //     }, 100);

    },

    created() {
        this.setTipoCambio();
        this.loadDiscounts();


        if (this.currentProforma) {
            this.proforma = this.currentProforma;


            if (this.currentTipoDocumento) {
                this.proforma.TipoDocumento = this.currentTipoDocumento;
            }
            let por_descuento = 0;
            this.proforma.lines.forEach((line, index) => {
                line.updateStock = 0;
                por_descuento = parseFloat(line.PorcentajeDescuento);

                if (por_descuento > 0 &&
                    line.MontoDescuento > 0 &&
                    line.discounts.length == 0) {
                    line.discounts.push({
                        PorcentajeDescuento: por_descuento,
                        MontoDescuento: line.MontoDescuento,
                        NaturalezaDescuento: line.NaturalezaDescuento ?? 'Descuento Cliente'
                    });
                }
                this.refreshLine(line, index);
            });
            this.calculateProforma(this.proforma.lines);


        } else {
            if (this.medics.length === 1) {
                this.proforma.user_id = this.medics[0].id;
            }
        }



    }

};
</script>
