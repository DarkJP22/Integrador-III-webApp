<template>
    <div class="invoice-form">
        <loading :show="loader"></loading>
        <form-error v-if="errors.certificate" :errors="errors">

            <div class="callout callout-danger">
                <h4>Información importante!</h4>

                <p> {{ errors.certificate[0] }}</p>
            </div>
        </form-error>
        <form @submit.prevent="save()">
            <div class="row">

                <div class="form-group col-md-12">


                    <label for="obligado_tributario_id">Obligado Tributario</label>
                    <select name="obligado_tributario_id" id="obligado_tributario_id" v-model="form.obligado_tributario_id" class="form-control" required>

                        <option :value="item.id" v-for="(item, index) in configFacturas" :selected="index == 0 ? 'selected' : '' " :key="item.id"> {{ item.nombre }}</option>

                    </select>

                    <form-error v-if="errors.obligado_tributario_id" :errors="errors" style="float:right;">
                        {{ errors.obligado_tributario_id[0] }}
                    </form-error>

                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Paso 1: Subir XML</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="form-row">
                                <div class="form-group col-md-12">

                                    <file-upload @input="handleFileUpload"></file-upload>
                                    <div>
                                        <form-error v-if="errors.file" :errors="errors">
                                            {{ errors.file[0] }}
                                        </form-error>
                                    </div>


                                    <button type="button" @click="uploadXML()" class="btn btn-primary">Cargar XML</button>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Paso 2: Información Documento</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="Clave">Clave</label>

                                    <input type="text" class="form-control" name="Clave" placeholder="" v-model="form.Clave" disabled>

                                    <form-error v-if="errors.Clave" :errors="errors" style="float:right;">
                                        {{ errors.Clave[0] }}
                                    </form-error>

                                </div>
                                <div class="form-group col-md-4">
                                    <label for="NumeroCedulaEmisor">Numero Cedula Emisor</label>

                                    <input type="text" class="form-control" name="NumeroCedulaEmisor" placeholder="" v-model="form.NumeroCedulaEmisor" disabled>
                                    <form-error v-if="errors.NumeroCedulaEmisor" :errors="errors" style="float:right;">
                                        {{ errors.NumeroCedulaEmisor[0] }}
                                    </form-error>

                                </div>
                                <div class="form-group col-md-4">
                                    <label for="MontoTotalImpuesto">Monto Total Impuesto</label>
                                    <input type="text" class="form-control" name="MontoTotalImpuesto" placeholder="" v-model="form.MontoTotalImpuesto" disabled>
                                    <form-error v-if="errors.MontoTotalImpuesto" :errors="errors" style="float:right;">
                                        {{ errors.MontoTotalImpuesto[0] }}
                                    </form-error>

                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="TotalFactura">Total Factura</label>

                                    <input type="text" class="form-control" name="TotalFactura" placeholder="" v-model="form.TotalFactura" disabled>
                                    <form-error v-if="errors.TotalFactura" :errors="errors" style="float:right;">
                                        {{ errors.TotalFactura[0] }}
                                    </form-error>

                                </div>
                                <div class="form-group col-md-4">
                                    <label for="NumeroCedulaReceptor">Numero Cedula Receptor</label>

                                    <input type="text" class="form-control" name="NumeroCedulaReceptor" placeholder="" v-model="form.NumeroCedulaReceptor" disabled>
                                    <form-error v-if="errors.NumeroCedulaReceptor" :errors="errors" style="float:right;">
                                        {{ errors.NumeroCedulaReceptor[0] }}
                                    </form-error>

                                </div>
                                <div class="form-group col-md-4">
                                    <label for="MontoTotaMensajelImpuesto">Mensaje</label>
                                    <select class="form-control " style="width: 100%;" name="Mensaje" placeholder="-- Selecciona Mensaje --" v-model="form.Mensaje" required>
                                        <option disabled="disabled"></option>
                                        <option v-for="(value, key) in mensajesReceptor" v-bind:value="key" :key="key">{{ value }}</option>

                                    </select>
                                    <form-error v-if="errors.Mensaje" :errors="errors" style="float:right;">
                                        {{ errors.Mensaje[0] }}
                                    </form-error>

                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="CondicionImpuesto">Condición del Impuesto</label>
                                    <select class="form-control " style="width: 100%;" name="CondicionImpuesto" placeholder="-- Selecciona Mensaje --" v-model="form.CondicionImpuesto" @change="onChangeCondicionImpuesto()">
                                        <option disabled="disabled"></option>
                                        <option v-for="(value, key) in condicionImpuesto" v-bind:value="key" :key="key">{{ value }}</option>

                                    </select>
                                    <form-error v-if="errors.CondicionImpuesto" :errors="errors" style="float:right;">
                                        {{ errors.CondicionImpuesto[0] }}
                                    </form-error>

                                </div>
                                <div class="form-group col-md-4">
                                    <label for="MontoTotalImpuestoAcreditar">Monto Total Impuesto Acreditar</label>

                                    <input type="text" class="form-control" name="MontoTotalImpuestoAcreditar" placeholder="" v-model="form.MontoTotalImpuestoAcreditar">
                                    <form-error v-if="errors.MontoTotalImpuestoAcreditar" :errors="errors" style="float:right;">
                                        {{ errors.MontoTotalImpuestoAcreditar[0] }}
                                    </form-error>

                                </div>
                                <div class="form-group col-md-4">
                                    <label for="MontoTotalDeGastoAplicable">Monto Total De Gasto Aplicable</label>

                                    <input type="text" class="form-control" name="MontoTotalDeGastoAplicable" placeholder="" v-model="form.MontoTotalDeGastoAplicable">
                                    <form-error v-if="errors.MontoTotalDeGastoAplicable" :errors="errors" style="float:right;">
                                        {{ errors.MontoTotalDeGastoAplicable[0] }}
                                    </form-error>

                                </div>

                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="DetalleMensaje">Detalle Mensaje (Opcional)</label>

                                    <textarea class="form-control" name="DetalleMensaje" v-model="form.DetalleMensaje" :required="form.Mensaje !=1 ? 'required' : false"></textarea>
                                    <form-error v-if="errors.DetalleMensaje" :errors="errors" style="float:right;">
                                        {{ errors.DetalleMensaje[0] }}
                                    </form-error>

                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary" :disabled="loader">Confirmar Documento</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
                                        <button type="button" class="btn btn-secondary" @click="clearForm">Limpiar</button>
                                        <a href="/receptor/mensajes" class="btn btn-secondary">Regresar</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</template>
<script>
import FileUpload from './FileUpload.vue';
import FormError from './FormError.vue';
import Loading from './Loading.vue';
export default {
    props: ['mensajesReceptor', 'condicionImpuesto', 'configFacturas'],
    data() {
        return {
            loader: false,
            file: {},
            form: {
                obligado_tributario_id: this.configFacturas ? this.configFacturas[0]?.id : '',
                Clave: '',
                NumeroConsecutivo: '',
                tipo_identificacion_emisor: '',
                NumeroCedulaEmisor: '',
                Mensaje: '',
                DetalleMensaje: '',
                MontoTotalImpuesto: '',
                ExisteImpuesto: 0,
                TotalFactura: '',
                tipo_identificacion_receptor: '',
                NumeroCedulaReceptor: '',
                status: 1,
                nombre_emisor: '',
                email_emisor: '',
                TipoDocumento: '',
                MedioPago: '',
                CondicionVenta: '',
                PlazoCredito: '',
                CodigoMoneda: '',
                CodigoActividad: '',
                CondicionImpuesto: '',
                MontoTotalImpuestoAcreditar: 0,
                MontoTotalDeGastoAplicable: 0,
                FechaEmisionFactura: '',
                TipoCambio: 1

            },

            errors: []
        };
    },
    components: {
        FileUpload,
        Loading,
        FormError,

    },
    methods: {
        onChangeCondicionImpuesto() {
            if (this.form.CondicionImpuesto == '01') {
                this.form.MontoTotalImpuestoAcreditar = this.form.MontoTotalImpuesto ? this.form.MontoTotalImpuesto : 0;
                this.form.MontoTotalDeGastoAplicable = 0;
            }
            if (this.form.CondicionImpuesto == '04') {
                this.form.MontoTotalImpuestoAcreditar = 0;
                this.form.MontoTotalDeGastoAplicable = this.form.TotalFactura;
            }
        },
        uploadXML() {
            if (this.loader) {
                return;
            }

            this.loader = true;

            const config = {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            };

            const form = new FormData();

            form.append('file', this.file);
            form.append('obligado_tributario_id', this.form.obligado_tributario_id);

            axios.post('/receptor/mensajes/uploadxml', form, config)

                .then(({ data }) => {

                    this.loader = false;
                    flash('Archivo Cargado');

                    this.form.FechaEmisionFactura = data.FechaEmision;
                    this.form.Clave = data.Clave;
                    this.form.NumeroConsecutivo = data.NumeroConsecutivo;
                    this.form.tipo_identificacion_emisor = data.Emisor.Identificacion.Tipo;
                    this.form.NumeroCedulaEmisor = data.Emisor.Identificacion.Numero;
                    this.form.MontoTotalImpuesto = data.ResumenFactura.TotalImpuesto ? data.ResumenFactura.TotalImpuesto : 0;
                    this.form.ExisteImpuesto = data.ResumenFactura.TotalImpuesto >= 0 ? 1 : 0;
                    this.form.TotalFactura = data.ResumenFactura.TotalComprobante;

                    if (data.Receptor) {
                        this.form.tipo_identificacion_receptor = data.Receptor.Identificacion ? data.Receptor.Identificacion.Tipo : '';
                        this.form.NumeroCedulaReceptor = data.Receptor.Identificacion ? data.Receptor.Identificacion.Numero : '';
                    }

                    this.form.nombre_emisor = data.Emisor.Nombre;
                    this.form.email_emisor = data.Emisor.CorreoElectronico;
                    this.form.TipoDocumento = data.NumeroConsecutivo.substring(8, 10);
                    this.form.MedioPago = data.MedioPago;
                    this.form.CondicionVenta = data.CondicionVenta;
                    this.form.PlazoCredito = data.PlazoCredito;
                    this.form.CodigoMoneda = data.ResumenFactura.CodigoTipoMoneda ? data.ResumenFactura.CodigoTipoMoneda.CodigoMoneda : 'CRC';
                    this.form.TipoCambio = data.ResumenFactura.CodigoTipoMoneda ? data.ResumenFactura.CodigoTipoMoneda.TipoCambio : 1;



                    this.file = '';
                    this.errors = [];
                    this.emitter.emit('clearImage');

                })
                .catch(error => {

                    this.loader = false;
                    flash('Error al cargar el Archivo', 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];

                });

            // this.emitter.emit('clearImage');




        },
        handleFileUpload(file) {
            console.log(file);
            this.file = file;

        },
        save() {
            if (this.loader) {
                return;
            }


            this.loader = true;



            axios.post('/receptor/mensajes', this.form)

                .then(() => {
                    this.loader = false;
                    flash('Mensaje Enviado');


                    this.file = '';
                    this.errors = [];
                    this.clearForm();
                    this.emitter.emit('clearImage');

                })
                .catch(error => {
                    this.loader = false;
                    flash('Error al enviar mensaje', 'danger');
                    if (error.response.status == 500 || error.response.status == 504) {


                        flash('El Mensaje de receptor fue creado, pero ocurrio error con hacienda. ' + error.response.data.message, 'danger');

                    } else if (error.response.status == 422) {
                        flash('Error al enviar Mensaje de receptor', 'danger');

                    } else {

                        flash(error.response.data.message, 'danger');
                    }
                    this.errors = error.response.data.errors ? error.response.data.errors : [];

                });

            // this.emitter.emit('clearImage');




        },
        clearForm() {

            this.form = {
                obligado_tributario_id: this.configFacturas ? this.configFacturas[0]?.id : '',
                Clave: '',
                NumeroConsecutivo: '',
                tipo_identificacion_emisor: '',
                NumeroCedulaEmisor: '',
                Mensaje: '',
                DetalleMensaje: '',
                MontoTotalImpuesto: '',
                ExisteImpuesto: 0,
                TotalFactura: '',
                tipo_identificacion_receptor: '',
                NumeroCedulaReceptor: '',
                status: 1,
                nombre_emisor: '',
                email_emisor: '',
                TipoDocumento: '',
                MedioPago: '',
                CondicionVenta: '',
                PlazoCredito: '',
                CodigoMoneda: '',
                CodigoActividad: '',
                CondicionImpuesto: '',
                MontoTotalImpuestoAcreditar: 0,
                MontoTotalDeGastoAplicable: 0,
                FechaEmisionFactura: '',
                TipoCambio: 1
            };
            this.emitter.emit('clearImage');
            this.errors = [];

        }
    }
};
</script>
