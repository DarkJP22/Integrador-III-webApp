<template>
    <form class="newform form-horizontal" enctype="multipart/form-data" method="POST" @submit.prevent="save()">

        <div class="form-group">
            <label class="col-sm-2 control-label" for="office_name">Nombre</label>
            <div class="col-sm-10">

                <input v-model="office.name" :disabled="isDisabled" class="form-control" name="name" placeholder="Nombre del consultorio, clínica u hospital" type="text">
                <form-error v-if="errors.name" :errors="errors" style="float:right;">
                    {{ errors.name[0] }}
                </form-error>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="office_address">Dirección</label>

            <div class="col-sm-10">
                <input v-model="office.address" :disabled="isDisabled" class="form-control" name="address" placeholder="Dirección" type="text">
                <form-error v-if="errors.address" :errors="errors" style="float:right;">
                    {{ errors.address[0] }}
                </form-error>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="office_province">Provincia</label>

            <div class="col-sm-10">
                <select v-model="office.province" :disabled="isDisabled" class="form-control " name="province" placeholder="-- Selecciona provincia --" style="width: 100%;"
                        v-on:change="onChangeProvince">
                    <option disabled="disabled"></option>
                    <option v-for="item in provincias" :key="item.id" v-bind:value="item.id">{{ item.title }}</option>

                </select>
                <form-error v-if="errors.province" :errors="errors" style="float:right;">
                    {{ errors.province[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="office_canton">Canton</label>

            <div class="col-sm-10">
                <select v-model="office.canton" :disabled="isDisabled" class="form-control " name="canton" placeholder="-- Selecciona canton --" style="width: 100%;"
                        v-on:change="onChangeCanton">
                    <option disabled="disabled"></option>
                    <option v-for="item in cantones" :key="item.id" v-bind:value="item.id">{{ item.title }}</option>

                </select>
                <form-error v-if="errors.canton" :errors="errors" style="float:right;">
                    {{ errors.canton[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="office_district">Distrito</label>

            <div class="col-sm-10">
                <select v-model="office.district" :disabled="isDisabled" class="form-control " name="district" placeholder="-- Selecciona distrito --" style="width: 100%;">
                    <option disabled="disabled"></option>
                    <option v-for="item in distritos" :key="item.id" v-bind:value="item.id">{{ item.title }}</option>

                </select>
                <form-error v-if="errors.district" :errors="errors" style="float:right;">
                    {{ errors.district[0] }}
                </form-error>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" for="office_phone">Teléfono</label>

            <div class="col-sm-10">
                <input v-model="office.phone" :disabled="isDisabled" class="form-control" name="phone" placeholder="Teléfono" type="text">
                <form-error v-if="errors.phone" :errors="errors" style="float:right;">
                    {{ errors.phone[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="office_whatsapp_number">Whatsapp (Solicitudes de citas)</label>

            <div class="col-sm-10">
                <input v-model="office.whatsapp_number" :disabled="isDisabled" class="form-control" name="whatsapp_number" placeholder="Teléfono" type="text">
                <form-error v-if="errors.whatsapp_number" :errors="errors" style="float:right;">
                    {{ errors.whatsapp_number[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="office_whatsapp_number">Agenda</label>

            <div class="col-sm-10">
                <div class="checkbox">
                    <label>
                        <input v-model="office.utiliza_agenda_gps" :disabled="isDisabled" name="utiliza_agenda_gps" type="checkbox" value="1">
                        Utiliza Agenda Doctor Blue
                    </label>
                </div>
                <p><small>Al seleccionar esta opción usted utiliza la agenda de Doctor Blue para recibir citas</small></p>
            </div>
        </div>
        <!-- <div class="form-group" v-show="office.type == 1">
                <label for="office_bill_to" class="col-sm-2 control-label">Facturación</label>

                <div class="col-sm-10">
                  <select class="form-control " style="width: 100%;" name="bill_to"  v-model="office.bill_to" :disabled="isDisabled">

                    <option v-bind:value="'M'">Persona Física</option>
                     <option v-bind:value="'C'">Persona Jurídica</option>

                  </select>
                  <form-error v-if="errors.bill_to" :errors="errors" style="float:right;">
                      {{ errors.bill_to[0] }}
                  </form-error>
                </div>
              </div>
               <div class="form-group"  v-show="office.bill_to == 'C'" >
                <label for="office_ide" class="col-sm-2 control-label">Cédula</label>

                <div class="col-sm-10">
                  <input type="text" class="form-control" name="ide" placeholder="Cédula Jurídica"  v-model="office.ide" :disabled="isDisabled">
                  <form-error v-if="errors.ide" :errors="errors" style="float:right;">
                      {{ errors.ide[0] }}
                  </form-error>
                </div>
              </div>
                <div class="form-group"  v-show="office.bill_to == 'C'" >
                  <label for="ide_name" class="col-sm-2 control-label">Nombre Jurídico</label>
                  <div class="col-sm-10">

                     <input type="text" class="form-control" name="ide_name" placeholder="Nombre Jurídico" v-model="office.ide_name" :disabled="isDisabled">
                    <form-error v-if="errors.ide_name" :errors="errors" style="float:right;">
                        {{ errors.ide_name[0] }}
                    </form-error>
                  </div>
                </div> -->

        <div class="form-group">
            <label class="col-sm-2 control-label" for="lat">Coordenadas (Para Google Maps y Waze)</label>


            <div class="col-sm-3">
                <div class="form-group">
                    <div class="col-sm-10">
                        <div class="input-group">
                            <span class="input-group-addon">lat:</span>
                            <input v-model="office.lat" :disabled="isDisabled" class="form-control" name="lat" placeholder="10.637875" type="text">
                        </div>
                    </div>
                </div>


            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <div class="col-sm-10">
                        <div class="input-group">
                            <span class="input-group-addon">lon:</span>
                            <input v-model="office.lon" :disabled="isDisabled" class="form-control" name="lon" placeholder="-85.434431" type="text">
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-sm-3">

                <div class="form-group">


                    <div class="col-sm-6">


                        <button v-show="office.type == 1 || isAdminClinic" class="btn btn-default btn-geo" type="button" @click="getGeolocation"><i class="fa fa-"></i>Tu ubicación
                            Actual
                        </button>

                    </div>


                </div>


                <!-- Modal -->
                <div id="modalOfficeNotification" aria-labelledby="modalOfficeNotificationLabel" class="modal fade" role="dialog" tabindex="-1">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                                <h4 id="modalOfficeNotificationLabel" class="modal-title">Recordatorio</h4>
                            </div>
                            <div class="modal-body">

                                <div class="callout callout-info">
                                    <h4>Recordatorio de actualizacion de ubicación de consultorio o clinica</h4>

                                    <p>Selecciona el dia y la hora del recordatorio</p>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-group ">
                                            <input id="datetimepicker1" v-model="office.notification_datetime" class="form-control datepicker" data-input name="notification_date"
                                                   type="text" @blur="onBlurDatetime">

                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <input id="datetimepicker2" v-model="office.notification_hour" class="form-control timepicker" data-input name="notification_date"
                                                   type="text" @blur="onBlurHour">

                                            <div class="input-group-addon">
                                                <i class="fa fa-clock-o"></i>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>
                            <div class="modal-footer">

                                <button v-show="office.notification_date" class="btn btn-primary" type="button" @click="clearNotification()">Quitar Notificación</button>
                                <button class="btn btn-secondary" data-dismiss="modal" type="button">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal -->


            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <div class="col-sm-5">
                        <button v-show="office.type == 1" class="btn btn-secondary" data-target="#modalOfficeNotification" data-toggle="modal" type="button">
                            Actualizar coordenadas despues
                        </button>
                    </div>
                </div>
            </div>


        </div>

        <div v-show="office.id || office.type == 1" class="form-group">
            <label class="col-sm-2 control-label" for="file">Logo</label>
            <div v-show="office.id" class="col-sm-4">
                <img alt="logo" style="height:100px;width:auto;" v-bind:src="office.logo_path">
            </div>
            <div v-show="office.type == 1 || isAdminClinic" class="col-sm-4">
                <photo-upload @input="handleFileUpload"></photo-upload>
                <form-error v-if="errors.file" :errors="errors" style="float:right;">
                    {{ errors.file[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button :disabled="loader" class="btn btn-primary" type="submit">Guardar</button>
                <img v-show="loader" alt="Cargando..." src="/img/loading.gif">
                <button v-if="!backUrl" class="btn btn-secondary" type="button" @click="cancel()">Limpiar</button>
                <img v-show="loader" alt="Cargando..." src="/img/loading.gif">
                <a v-if="backUrl" :href="backUrl" class="btn btn-secondary">Regresar</a>
            </div>
        </div>

    </form>
</template>
<script>
import PhotoUpload from './PhotoUpload.vue';

export default {
    props: ['backUrl'],
    data() {
        return {
            office: {
                lat: '',
                lon: '',
                notification_date: '',
                notification_datetime: '',
                notification_hour: '',
                type: 1,
                bill_to: 'M',
                ide: '',
                ide_name: '',
                utiliza_agenda_gps: false
            },
            provincias: window.provincias,
            cantones: [],
            distritos: [],
            errors: [],
            loader: false,

        };
    },
    components: {PhotoUpload},

    computed: {
        // logo(){
        //     return (this.office.logo_path) ? '/storage/'+ this.office.logo_path : '/img/default-avatar.jpg'
        // },

        isAdmin() {

            return window.App.isAdministrator;
        },

        isAdminClinic() {

            return window.App.currentOffice;
        },
        isDisabled() {

            return (this.office.id && this.office.type == 2 && !this.isAdminClinic);
        }
    },
    methods: {
        clearNotification() {
            this.office.notification = 0;
            this.office.notification_date = '';
            this.office.notification_datetime = '';
            this.office.notification_hour = '';
        },
        save() {

            if (this.loader) {
                return;
            }

            this.loader = true;

            const form = new FormData();
            const officeObj = this.office;

            Object.keys(officeObj).forEach(function (key) {

                if (Array.isArray(officeObj[key])) {
                    form.append(key, JSON.stringify(officeObj[key]));
                } else {
                    // Convert null to empty strings (because formData does not support null values and converts it to string)
                    if (officeObj[key] === null) {
                        officeObj[key] = '';
                    }

                    form.append(key, officeObj[key]);
                }


            });

            if (!this.office.id) {

                axios.post('/offices', form)
                    .then(({data}) => {
                        this.loader = false;
                        this.clearForm();
                        flash('Consultorio Creado.');
                        this.$emit('created', data);

                        this.emitter.emit('clearImage');

                        if (this.backUrl) {
                            window.location.href = this.backUrl;
                        }
                    })
                    .catch(error => {
                        this.loader = false;

                        flash('Error al guardar consultorio', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            } else {

                axios.post('/offices/' + this.office.id, form)
                    .then(({data}) => {
                        this.loader = false;
                        flash('Consultorio Actualizado.');

                        if (!this.isAdminClinic) {
                            this.clearForm();
                            this.$emit('updated', data);
                            this.emitter.emit('clearImage');
                        }

                        if (this.backUrl) {
                            window.location.href = this.backUrl;
                        }


                    })
                    .catch(error => {
                        this.loader = false;

                        flash('Error al guardar consultorio', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            }

        },

        handleFileUpload(file) {

            this.office.file = file;

        },

        onBlurDatetime(e) {

            this.office.notification_datetime = e.target.value;
            this.$emit('input');
        },
        onBlurHour(e) {

            this.office.notification_hour = e.target.value;
            this.$emit('input');
        },
        changeValue() {
            this.office.notification_date = this.office.notification_datetime + ' ' + this.office.notification_hour;


            if (moment(this.office.notification_date).isValid()) {

                this.office.notification = 1;
            } else {
                this.office.notification = 0;
                this.office.notification_date = '';
            }


        },
        getGeolocation() {


            var vm = this;
            window.navigator.geolocation.getCurrentPosition(vm.localitation);

        },
        localitation(geo) {


            this.office.lat = geo.coords.latitude;
            this.office.lon = geo.coords.longitude;


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
        fill(office) {

            var cant = [];
            var dist = [];


            this.provincias.forEach(function (prov) {
                if (office.province === prov.id) {
                    cant = prov.cantones;
                }
            });

            this.cantones = cant;

            this.cantones.forEach(function (cant) {
                if (office.canton === cant.id) {
                    dist = cant.distritos;
                }
            });

            this.distritos = dist;


            this.office = office;

            if (this.office.notification_date == '0000-00-00 00:00:00' || this.office.notification_date == null || this.office.notification_date == 'null') {
                this.office.notification_date = '';
                this.office.notification_datetime = '';
                this.office.notification_hour = '';
                this.office.notification = 0;
            }

        },
        clearForm() {

            this.office = {
                lat: '',
                lon: '',
                notification_date: '',
                notification_datetime: '',
                notification_hour: '',
                type: 1,
                bill_to: 'M',
                ide: '',
                ide_name: '',
            };

            this.errors = [];

        }

    },
    created() {

        this.emitter.on('editOffice', this.fill);
        this.emitter.on('newOffice', this.clearForm);
        this.emitter.on('input', this.changeValue);

    }
};
</script>
