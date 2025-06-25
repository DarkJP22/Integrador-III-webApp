<template>
    <form autocomplete="off" class="form-horizontal" enctype="multipart/form-data" @submit.prevent="save()">
        <loading :show="loader"></loading>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="CodigoActividad">Código Actividad</label>

            <div class="col-sm-10">
                <!-- <select class="form-control" style="width: 100%;" name="CodigoActividad" v-model="form.CodigoActividad" multiple>
                    <option value=""></option>
                    <option v-for="(activity, index) in activities" :value="activity.codigo" :key="activity.id">
                        {{ activity.actividad }}
                    </option>
                </select> -->
                <v-select v-model="form.CodigoActividad" :options="activities" label="actividad" multiple placeholder="Buscar Actividades...">

                    <template v-slot:no-options>
                        Escribe para buscar las actividades
                    </template>

                </v-select>
                <form-error v-if="errors.CodigoActividad" :errors="errors" style="float:right;">
                    {{ errors.CodigoActividad[0] }}
                </form-error>
            </div>

        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="nombre">Nombre o Razón social del emisor*</label>

            <div class="col-sm-5">
                <input v-model="form.nombre" class="form-control" name="nombre" placeholder="Nombre" required type="text">
                <form-error v-if="errors.nombre" :errors="errors" style="float:right;">
                    {{ errors.nombre[0] }}
                </form-error>
            </div>

            <div class="col-sm-5">
                <input v-model="form.nombre_comercial" class="form-control" name="nombre_comercial" placeholder="Nombre Comercial" type="text">
                <form-error v-if="errors.nombre_comercial" :errors="errors" style="float:right;">
                    {{ errors.nombre_comercial[0] }}
                </form-error>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="tipo_identificacion">Identificación</label>

            <div class="col-sm-3">
                <select v-model="form.tipo_identificacion" class="form-control" name="tipo_identificacion" style="width: 100%;">
                    <option value=""></option>
                    <option v-for="(value, key) in tipoIdentificaciones" :key="key" :value="key">
                        {{ value }}
                    </option>
                </select>
                <form-error v-if="errors.tipo_identificacion" :errors="errors" style="float:right;">
                    {{ errors.tipo_identificacion[0] }}
                </form-error>
            </div>
            <div class="col-sm-7">
                <input v-model="form.identificacion" class="form-control" name="identificacion" placeholder="Numero de identificación" required type="text">
                <!-- <small>Formato: rellernar con 0. Ej: 505550555</small> -->
                <form-error v-if="errors.identificacion" :errors="errors" style="float:right;">
                    {{ errors.identificacion[0] }}
                </form-error>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="sucursal">Sucursal</label>

            <div class="col-sm-5">
                <input v-model="form.sucursal" class="form-control" min="1" name="sucursal" placeholder="Número de Sucursal" required type="number">
                <form-error v-if="errors.sucursal" :errors="errors" style="float:right;">
                    {{ errors.sucursal[0] }}
                </form-error>
            </div>

            <div class="col-sm-5">
                <input v-model="form.pos" class="form-control" min="1" name="pos" placeholder="Número de caja" required type="number">
                <form-error v-if="errors.pos" :errors="errors" style="float:right;">
                    {{ errors.pos[0] }}
                </form-error>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="provincia">Provincia</label>

            <div class="col-sm-10">
                <select v-model="form.provincia" class="form-control " name="provincia" placeholder="-- Selecciona provincia --" style="width: 100%;" v-on:change="onChangeProvince">
                    <option disabled="disabled"></option>
                    <option v-for="item in provincias" :key="item.id" v-bind:value="item.id">{{ item.title }}</option>

                </select>
                <form-error v-if="errors.provincia" :errors="errors" style="float:right;">
                    {{ errors.provincia[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="office_canton">Canton</label>

            <div class="col-sm-10">
                <select v-model="form.canton" class="form-control " name="canton" placeholder="-- Selecciona canton --" style="width: 100%;" v-on:change="onChangeCanton">
                    <option disabled="disabled"></option>
                    <option v-for="item in cantones" :key="item.id" v-bind:value="item.id">{{ item.title }}</option>

                </select>
                <form-error v-if="errors.canton" :errors="errors" style="float:right;">
                    {{ errors.canton[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="distrito">Distrito</label>

            <div class="col-sm-10">
                <select v-model="form.distrito" class="form-control " name="distrito" placeholder="-- Selecciona distrito --" style="width: 100%;">
                    <option disabled="disabled"></option>
                    <option v-for="item in distritos" :key="item.id" v-bind:value="item.id">{{ item.title }}</option>

                </select>
                <form-error v-if="errors.distrito" :errors="errors" style="float:right;">
                    {{ errors.distrito[0] }}
                </form-error>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="barrio">Barrio</label>

            <div class="col-sm-10">
                <select v-model="form.barrio" class="form-control " name="barrio" placeholder="-- Selecciona Barrio --" style="width: 100%;">
                    <option disabled="disabled"></option>
                    <option v-for="item in barrios" :key="item.id" v-bind:value="item.id">{{ item.title }}</option>

                </select>
                <form-error v-if="errors.barrio" :errors="errors" style="float:right;">
                    {{ errors.barrio[0] }}
                </form-error>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="codigo_pais_tel">Teléfono</label>

            <div class="col-sm-2">
                <!-- <input type="text" class="form-control" name="codigo_pais_tel" placeholder="Codigo Pais" v-model="form.codigo_pais_tel" > -->
                <select v-model="form.codigo_pais_tel" class="form-control " name="codigo_pais_tel" placeholder="-- Selecciona Codigo Pais --" style="width: 100%;">

                    <option value="506">506</option>

                </select>
                <form-error v-if="errors.codigo_pais_tel" :errors="errors" style="float:right;">
                    {{ errors.codigo_pais_tel[0] }}
                </form-error>
            </div>

            <div class="col-sm-3">
                <input v-model="form.telefono" class="form-control" name="telefono" placeholder="Teléfono" type="text">
                <form-error v-if="errors.telefono" :errors="errors" style="float:right;">
                    {{ errors.telefono[0] }}
                </form-error>
            </div>
            <div class="col-sm-2">
                <!-- <input type="text" class="form-control" name="codigo_pais_fax" placeholder="Codigo Pais" v-model="form.codigo_pais_fax" > -->
                <select v-model="form.codigo_pais_fax" class="form-control " name="codigo_pais_fax" placeholder="-- Selecciona Codigo Pais --" style="width: 100%;">

                    <option value="506">506</option>

                </select>
                <form-error v-if="errors.codigo_pais_fax" :errors="errors" style="float:right;">
                    {{ errors.codigo_pais_fax[0] }}
                </form-error>
            </div>

            <div class="col-sm-3">
                <input v-model="form.fax" class="form-control" name="fax" placeholder="Fax" type="text">
                <form-error v-if="errors.fax" :errors="errors" style="float:right;">
                    {{ errors.fax[0] }}
                </form-error>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="barrio">Otras Señas</label>

            <div class="col-sm-10">
                <input v-model="form.otras_senas" class="form-control" name="otras_senas" placeholder="Otras Señas" required type="text">
                <form-error v-if="errors.otras_senas" :errors="errors" style="float:right;">
                    {{ errors.otras_senas[0] }}
                </form-error>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" for="email">Email</label>

            <div class="col-sm-10">
                <input v-model="form.email" class="form-control" name="email" placeholder="Email" required type="email">
                <form-error v-if="errors.email" :errors="errors" style="float:right;">
                    {{ errors.email[0] }}
                </form-error>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="consecutivo_inicio">Consecutivos de inicio</label>

            <div class="col-sm-3">

                <label class="control-label" for="consecutivo_inicio">Facturas</label>
                <input v-model="form.consecutivo_inicio" class="form-control" min="1" name="consecutivo_inicio" placeholder="Consecutivo Facturas" required type="number">
                <form-error v-if="errors.consecutivo_inicio" :errors="errors" style="float:right;">
                    {{ errors.consecutivo_inicio[0] }}
                </form-error>

            </div>
            <div class="col-sm-3">
                <label class="control-label" for="consecutivo_inicio_ND">Notas Débito</label>
                <input v-model="form.consecutivo_inicio_ND" class="form-control" min="1" name="consecutivo_inicio_ND" placeholder="Consecutivo Notas de débito" required type="number">
                <form-error v-if="errors.consecutivo_inicio_ND" :errors="errors" style="float:right;">
                    {{ errors.consecutivo_inicio_ND[0] }}
                </form-error>
            </div>
            <div class="col-sm-3">
                <label class="control-label" for="consecutivo_inicio_NC">Notas Crédito</label>
                <input v-model="form.consecutivo_inicio_NC" class="form-control" min="1" name="consecutivo_inicio_NC" placeholder="Consecutivo Notas de crédito" required type="number">
                <form-error v-if="errors.consecutivo_inicio_NC" :errors="errors" style="float:right;">
                    {{ errors.consecutivo_inicio_NC[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="consecutivo_inicio">Consecutivos de inicio</label>

            <div class="col-sm-5">

                <label class="control-label" for="consecutivo_inicio_tiquete">Tiquete</label>
                <input v-model="form.consecutivo_inicio_tiquete" class="form-control" min="1" name="consecutivo_inicio_tiquete" placeholder="Consecutivo Tiquetes" required type="number">
                <form-error v-if="errors.consecutivo_inicio_tiquete" :errors="errors" style="float:right;">
                    {{ errors.consecutivo_inicio_tiquete[0] }}
                </form-error>

            </div>
            <div class="col-sm-5">
                <label class="control-label" for="consecutivo_inicio_ND">Receptor</label>
                <input v-model="form.consecutivo_inicio_receptor" class="form-control" min="1" name="consecutivo_inicio_receptor" placeholder="Consecutivo Mensaje Receptor" required type="number">
                <form-error v-if="errors.consecutivo_inicio_receptor" :errors="errors" style="float:right;">
                    {{ errors.consecutivo_inicio_receptor[0] }}
                </form-error>
            </div>

        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" for="atv_user">ATV Api usuario </label>
            <div class="col-sm-10">
                <input v-model="form.atv_user" class="form-control" name="atv_user" placeholder="ATV usuario" required type="text">
                <form-error v-if="errors.atv_user" :errors="errors" style="float:right;">
                    {{ errors.atv_user[0] }}
                </form-error>
            </div>

        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="atv_password">ATV Api Contraseña </label>
            <div class="col-sm-10">
                <input v-model="form.atv_password" class="form-control" name="atv_password" placeholder="ATV Contraseña" required type="password">
                <form-error v-if="errors.atv_password" :errors="errors" style="float:right;">
                    {{ errors.atv_password[0] }}
                </form-error>
            </div>

        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="certificado">Certificado .p12 </label>


            <div class="col-sm-10">
                <h4 v-if="form.certificadoInstalado" class="label label-success">Certificado Instalado</h4>

                <h4 v-else class="label label-danger">Certificado No Instalado</h4>
                <!-- <input type="file" class="form-control" name="certificado" placeholder="Certificado ATV"> -->
                <photo-upload @input="handleFileUpload"></photo-upload>
                <form-error v-if="errors.certificado" :errors="errors">
                    {{ errors.certificado[0] }}
                </form-error>




            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="pin_certificado">PIN certificado </label>
            <div class="col-sm-10">
                <input v-model="form.pin_certificado" class="form-control" name="pin_certificado" placeholder="PIN del certificado" required type="password">
                <form-error v-if="errors.pin_certificado" :errors="errors">
                    {{ errors.pin_certificado[0] }}
                </form-error>
            </div>

        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button class="btn btn-primary" type="submit">Guardar</button>

                <!-- <button type="button" class="btn btn-danger" @click="destroy()" v-if="configFactura">Eliminar configuración</button> -->

            </div>
        </div>
    </form>
</template>
<script>
import PhotoUpload from './PhotoUpload.vue';
import Loading from './Loading.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
export default {
    props: ['configFactura', 'tipoIdentificaciones', 'endpoint', 'activities'],
    components: { PhotoUpload, Loading, vSelect },
    data() {
        return {
            form: {
                CodigoActividad: this.selectedActivities(),
                nombre: '',
                nombre_comercial: '',
                tipo_identificacion: '01',
                identificacion: '',
                sucursal: 1,
                pos: 1,
                provincia: '',
                canton: '',
                distrito: '',
                barrio: '',
                codigo_pais_tel: '506',
                codigo_pais_fax: '506',
                telefono: '',
                fax: '',
                otras_senas: '',
                email: '',
                consecutivo_inicio: 1,
                consecutivo_inicio_ND: 1,
                consecutivo_inicio_NC: 1,
                consecutivo_inicio_tiquete: 1,
                consecutivo_inicio_receptor: 1,
                atv_user: '',
                atv_password: '',
                pin_certificado: ''

            },
            loader: false,
            errors: [],
            certificado: false,
            provincias: window.provincias,
            cantones: [],
            distritos: [],
            barrios: []

        };
    },
    methods: {
        selectedActivities() {

            return this.activities.length ? this.configFactura.activities : [];
        },
        onChangeProvince: function (event) {

            var cant = [];

            window.provincias.forEach(function (prov) {

                if (event.currentTarget.value === prov.id) {
                    cant = prov.cantones;
                }
            });

            this.cantones = cant;
        },
        onChangeCanton: function (event) {

            var dist = [];

            this.cantones.forEach(function (cant) {
                if (event.currentTarget.value === cant.id) {
                    dist = cant.distritos;
                }
            });

            this.distritos = dist;
        },
        onChangeDistrito: function (event) {

            var barr = [];

            this.distritos.forEach(function (dist) {
                if (event.currentTarget.value === dist.id) {
                    barr = dist.barrios;
                }
            });

            this.barrios = barr;
        },
        createFormData(formData, key, data) {
            if (data === Object(data) || Array.isArray(data)) {
                for (var i in data) {
                    this.createFormData(formData, key + '[' + i + ']', data[i]);
                }
            } else {
                formData.append(key, data);
            }
        },
        getFormData(formData, data, previousKey) {
            if (data instanceof Object) {
                Object.keys(data).forEach(key => {
                    const value = data[key];
                    if (value instanceof Object && !Array.isArray(value)) {
                        return this.getFormData(formData, value, key);
                    }
                    if (previousKey) {
                        key = `${previousKey}[${key}]`;
                    }
                    if (Array.isArray(value)) {
                        value.forEach(val => {
                            formData.append(`${key}[]`, val);
                        });
                    } else {
                        formData.append(key, value);
                    }
                });
            }
        },
        save() {

            if (this.loader) {
                return;
            }

            this.loader = true;

            const form = new FormData();

            form.append('certificado', this.certificado);

            //this.createFormData(form, 'form', this.form);
            //this.getFormData(form, this.form);
            //form

            const formActivities = [];
            this.form.CodigoActividad.forEach((item) => {
                formActivities.push(item.codigo);
            });

            this.form.CodigoActividad = formActivities;

            for (const key in this.form) {
                if (this.form[key]) {
                    Array.isArray(this.form[key])
                        ? this.form[key].forEach(value => form.append(key + '[]', value))
                        : form.append(key, this.form[key]);
                }
            }
            // Object.keys(this.form).forEach( (key) => {

            //       if(this.form[key]){
            //          Array.isArray(this.form[key])
            //         ? this.form[key].forEach(value => form.append(key + '[]', value))
            //         : form.append(key, this.form[key]) ;  
            //         //form.append(key, this.form[key]);
            //       }
            // });

            axios.post(this.endpoint, form).then(({ data }) => {

                this.loader = false;
                flash('Configuración Guardada');


                this.certificado = '';
                this.errors = [];
                this.form = data;
                this.form.CodigoActividad = this.selectedActivities();
                this.emitter.emit('clearImage');

            })
                .catch(error => {

                    this.loader = false;
                    flash('Error al guardar. Revisar todos los campos requeridos', 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];

                });



        },
        destroy() {
            if (this.loader) {
                return;
            }
            Swal.fire({
                title: 'Deseas eliminar La configuración de hacienda?',
                text: 'Requerda que te no podras facturar!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#67BC9A',
                cancelButtonColor: '#dd4b39',
                confirmButtonText: 'Eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {

                if (result.value) {
                    this.loader = true;
                    axios.delete(`/configfactura/${this.configFactura.id}`)
                        .then(() => {
                            this.loader = false;

                            flash('Configuracion Eliminada!');
                            this.form = {};

                        }).catch(error => {
                            this.loader = false;
                            flash(error.response.data.message, 'danger');

                        });

                    Swal.fire(
                        'Eliminado!',
                        'Configuracion Eliminada.',
                        'success'
                    );

                }



            });
        },

        handleFileUpload(file) {

            this.certificado = file;

        },
    },
    created() {
        if (this.configFactura) {
            this.form = this.configFactura;
            this.form.CodigoActividad = this.selectedActivities();
            var cant = [];
            var dist = [];
            var barr = [];


            this.provincias.forEach((prov) => {
                if (this.configFactura.provincia === prov.id) {
                    cant = prov.cantones;
                }
            });

            this.cantones = cant;

            this.cantones.forEach((cant) => {
                if (this.configFactura.canton === cant.id) {
                    dist = cant.distritos;
                }
            });

            this.distritos = dist;

            this.distritos.forEach((dist) => {
                if (this.configFactura.distrito === dist.id) {
                    barr = cant.barrios;
                }
            });

            this.barrios = barr;
        }
    }
};
</script>

