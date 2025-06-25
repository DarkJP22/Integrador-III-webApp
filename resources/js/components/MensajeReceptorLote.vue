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
                            <h3 class="box-title">Paso 1: Subir Varios Archivos XML</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="form-row">
                                <div class="form-group col-md-12">

                                    <input class="" type="file" @change="handleFiles" multiple required />
                                    <div>
                                        <form-error v-if="errors.file" :errors="errors">
                                            {{ errors.file[0] }}
                                        </form-error>
                                    </div>


                                    <!-- <button type="button" @click="uploadXML()" class="btn btn-primary">Cargar XML</button> -->

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

                                <div class="form-group col-md-12">
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
                                <div class="form-group col-md-12">
                                    <label for="CondicionImpuesto">Condición del Impuesto</label>
                                    <select class="form-control " style="width: 100%;" name="CondicionImpuesto" placeholder="-- Selecciona Mensaje --" v-model="form.CondicionImpuesto">
                                        <option disabled="disabled"></option>
                                        <option v-for="(value, key) in condicionImpuesto" v-bind:value="key" :key="key" :disabled="key != '01' && key != '04'">{{ value }}</option>

                                    </select>
                                    <form-error v-if="errors.CondicionImpuesto" :errors="errors" style="float:right;">
                                        {{ errors.CondicionImpuesto[0] }}
                                    </form-error>

                                </div>
                                <!-- <div class="form-group col-md-4">
                                        <label for="MontoTotalImpuestoAcreditar">Monto Total Impuesto Acreditar</label>
                                            
                                        <input type="text" class="form-control" name="MontoTotalImpuestoAcreditar" placeholder="" v-model="form.MontoTotalImpuestoAcreditar" >
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
                                    
                                    </div> -->

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
                                        <button type="submit" class="btn btn-primary" :disabled="loader">Confirmar Documentos</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
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
import FormError from './FormError.vue';
import Loading from './Loading.vue';
export default {
    props: ['mensajesReceptor', 'condicionImpuesto', 'configFacturas'],
    data() {
        return {
            loader: false,
            files: [],
            file: {},
            form: {
                obligado_tributario_id: this.configFacturas ? this.configFacturas[0]?.id : '',
                Mensaje: '',
                DetalleMensaje: '',
                CondicionImpuesto: ''

            },

            errors: []
        };
    },
    components: {
        Loading,
        FormError,

    },
    methods: {
        save() {
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
            this.files.forEach(file => {
                form.append('files[]', file);
            });

            form.append('Mensaje', this.form.Mensaje);
            form.append('CondicionImpuesto', this.form.CondicionImpuesto);
            form.append('DetalleMensaje', this.form.DetalleMensaje);
            form.append('obligado_tributario_id', this.form.obligado_tributario_id);


            axios.post('/receptor/mensajes/lote', form, config)

                .then(() => {

                    this.loader = false;
                    flash('Se estan procesando los archivos agregados');



                    this.files = [];
                    this.errors = [];


                })
                .catch(error => {

                    this.loader = false;
                    flash('Error al cargar los Archivos', 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];

                });

            // this.emitter.emit('clearImage');




        },
        handleFiles(event) {
            const files = event.target.files;
            if (files.length === 0) {
                return;
            }
            for (let index = 0; index < files.length; index++) {

                this.files.push(files[index]);

            }

        },


        clearForm() {

            this.form = {
                obligado_tributario_id: this.configFacturas ? this.configFacturas[0]?.id : '',
                Mensaje: '',
                DetalleMensaje: '',
                CondicionImpuesto: ''
            };
            this.emitter.emit('clearImage');
            this.errors = [];

        }
    }
};
</script>
