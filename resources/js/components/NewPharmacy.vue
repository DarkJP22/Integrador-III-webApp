<template>
  <form method="POST" enctype="multipart/form-data" class="newform form-horizontal" @submit.prevent="save()">

    <div class="form-group">
      <label for="pharmacy_name" class="col-sm-2 control-label">Nombre</label>
      <div class="col-sm-10">

        <input type="text" class="form-control" name="name" placeholder="Nombre de la farmacia" v-model="pharmacy.name" :disabled="isDisabled">
        <form-error v-if="errors.name" :errors="errors" style="float:right;">
          {{ errors.name[0] }}
        </form-error>
      </div>
    </div>

    <div class="form-group">
      <label for="pharmacy_address" class="col-sm-2 control-label">Dirección</label>

      <div class="col-sm-10">
        <input type="text" class="form-control" name="address" placeholder="Dirección" v-model="pharmacy.address" :disabled="isDisabled">
        <form-error v-if="errors.address" :errors="errors" style="float:right;">
          {{ errors.address[0] }}
        </form-error>
      </div>
    </div>

    <div class="form-group">
      <label for="pharmacy_province" class="col-sm-2 control-label">Provincia</label>

      <div class="col-sm-10">
        <select class="form-control " style="width: 100%;" name="province" placeholder="-- Selecciona provincia --" v-model="pharmacy.province" v-on:change="onChangeProvince" :disabled="isDisabled">
          <option disabled="disabled"></option>
          <option v-for="item in provincias" v-bind:value="item.id" :key="item.id">{{ item.title }}</option>

        </select>
        <form-error v-if="errors.province" :errors="errors" style="float:right;">
          {{ errors.province[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <label for="pharmacy_canton" class="col-sm-2 control-label">Canton</label>

      <div class="col-sm-10">
        <select class="form-control " style="width: 100%;" name="canton" placeholder="-- Selecciona canton --" v-model="pharmacy.canton" v-on:change="onChangeCanton" :disabled="isDisabled">
          <option disabled="disabled"></option>
          <option v-for="item in cantones" v-bind:value="item.id" :key="item.id">{{ item.title }}</option>

        </select>
        <form-error v-if="errors.canton" :errors="errors" style="float:right;">
          {{ errors.canton[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <label for="pharmacy_district" class="col-sm-2 control-label">Distrito</label>

      <div class="col-sm-10">
        <select class="form-control " style="width: 100%;" name="district" placeholder="-- Selecciona distrito --" v-model="pharmacy.district" :disabled="isDisabled">
          <option disabled="disabled"></option>
          <option v-for="item in distritos" v-bind:value="item.id" :key="item.id">{{ item.title }}</option>

        </select>
        <form-error v-if="errors.district" :errors="errors" style="float:right;">
          {{ errors.district[0] }}
        </form-error>
      </div>
    </div>



    <div class="form-group">
      <label for="pharmacy_phone" class="col-sm-2 control-label">Teléfono</label>

      <div class="col-sm-10">
        <input type="text" class="form-control" name="phone" placeholder="Teléfono" v-model="pharmacy.phone" :disabled="isDisabled">
        <form-error v-if="errors.phone" :errors="errors" style="float:right;">
          {{ errors.phone[0] }}
        </form-error>
      </div>
    </div>

    <div class="form-group">
      <label for="pharmacy_ide" class="col-sm-2 control-label">Cédula</label>

      <div class="col-sm-10">
        <input type="text" class="form-control" name="ide" placeholder="Cédula Jurídica" v-model="pharmacy.ide" :disabled="isDisabled">
        <form-error v-if="errors.ide" :errors="errors" style="float:right;">
          {{ errors.ide[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <label for="ide_name" class="col-sm-2 control-label">Nombre Jurídico</label>
      <div class="col-sm-10">

        <input type="text" class="form-control" name="ide_name" placeholder="Nombre Jurídico" v-model="pharmacy.ide_name" :disabled="isDisabled">
        <form-error v-if="errors.ide_name" :errors="errors" style="float:right;">
          {{ errors.ide_name[0] }}
        </form-error>
      </div>
    </div>

    <div class="form-group">
      <label for="lat" class="col-sm-2 control-label">Coordenadas (Para Google Maps y Waze)</label>



      <div class="col-sm-3">
        <div class="form-group">
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">lat:</span>
              <input type="text" class="form-control" name="lat" placeholder="10.637875" v-model="pharmacy.lat" :disabled="isDisabled">
            </div>
          </div>
        </div>


      </div>
      <div class="col-sm-3">
        <div class="form-group">
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">lon:</span>
              <input type="text" class="form-control" name="lon" placeholder="-85.434431" v-model="pharmacy.lon" :disabled="isDisabled">
            </div>
          </div>
        </div>

      </div>

      <div class="col-sm-3">

        <div class="form-group">


          <div class="col-sm-6">


            <button type="button" class="btn btn-default btn-geo" @click="getGeolocation"><i class="fa fa-"></i>Tu ubicación Actual</button>

          </div>


        </div>


        <!-- Modal -->
        <div class="modal fade" id="modalPharmacyNotification" tabindex="-1" role="dialog" aria-labelledby="modalPharmacyNotificationLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalPharmacyNotificationLabel">Recordatorio</h4>
              </div>
              <div class="modal-body">

                <div class="callout callout-info">
                  <h4>Recordatorio de actualizacion de ubicación de la farmacia</h4>

                  <p>Selecciona el dia y la hora del recordatorio</p>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="input-group">
                      <input type="text" class="form-control datepicker" name="notification_date" id="datetimepicker1" v-model="pharmacy.notification_datetime" @blur="onBlurDatetime">

                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="input-group">
                      <input type="text" class="form-control timepicker" name="notification_date" id="datetimepicker2" v-model="pharmacy.notification_hour" @blur="onBlurHour">

                      <div class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                      </div>
                    </div>

                  </div>

                </div>

              </div>
              <div class="modal-footer">

                <button type="button" class="btn btn-primary" v-show="pharmacy.notification_date" @click="clearNotification()">Quitar Notificación</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
        <!-- Modal -->


      </div>

      <div class="col-sm-3">
        <div class="form-group">
          <div class="col-sm-5">
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modalPharmacyNotification" v-show="isAdminPharmacy">
              Actualizar coordenadas despues
            </button>
          </div>
        </div>
      </div>




    </div>

    <div class="form-group" v-show="pharmacy.id">
      <label for="file" class="col-sm-2 control-label">Logo</label>
      <div class="col-sm-4" v-show="pharmacy.id">
        <img v-bind:src="pharmacy.logo_path" alt="logo" style="height:100px;width:auto;">
      </div>
      <div class="col-sm-4" v-show="isAdminPharmacy">
        <photo-upload @input="handleFileUpload"></photo-upload>
        <form-error v-if="errors.file" :errors="errors" style="float:right;">
          {{ errors.file[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-primary" :disabled="loader">Guardar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
        <button type="button" class="btn btn-secondary" @click="clearForm" v-if="!isAdminPharmacy">Limpiar</button>
      </div>
    </div>

  </form>
</template>
<script>
import PhotoUpload from './PhotoUpload.vue';
export default {

    data() {
        return {
            pharmacy: {
                lat: '',
                lon: '',
                notification_date: '',
                notification_datetime: '',
                notification_hour: '',
                ide: '',
                ide_name: '',
            },
            provincias: window.provincias,
            cantones: [],
            distritos: [],
            errors: [],
            loader: false,

        };
    },
    components: { PhotoUpload },

    computed: {

        isAdminPharmacy() {

            return window.App.currentPharmacy;
        },
        isDisabled() {

            return (this.pharmacy.id && !this.isAdminPharmacy);
        }
    },
    methods: {
        clearNotification() {
            this.pharmacy.notification = 0;
            this.pharmacy.notification_date = '';
            this.pharmacy.notification_datetime = '';
            this.pharmacy.notification_hour = '';
        },
        save() {

            if (this.loader) return;

            this.loader = true;

            const form = new FormData();
            const pharmacyObj = this.pharmacy;

            Object.keys(pharmacyObj).forEach(function (key) {


                form.append(key, pharmacyObj[key]);

            });

            if (!this.pharmacy.id) {

                axios.post('/pharmacies', form)
                    .then(({ data }) => {
                        this.loader = false;
                        this.clearForm();
                        flash('Farmacia Creada.');
                        this.$emit('created', data);
                        this.emitter.emit('clearImage');
                    })
                    .catch(error => {
                        this.loader = false;

                        flash('Error al guardar farmacia', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            } else {

                axios.post('/pharmacies/' + this.pharmacy.id, form)
                    .then(({ data }) => {
                        this.loader = false;
                        flash('Farmacia Actualizada.');

                        if (!this.isAdminPharmacy) {
                            this.clearForm();
                            this.$emit('updated', data);
                            this.emitter.emit('clearImage');
                        }



                    })
                    .catch(error => {
                        this.loader = false;

                        flash('Error al guardar farmacia', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            }

        },

        handleFileUpload(file) {

            this.pharmacy.file = file;

        },

        onBlurDatetime(e) {

            this.pharmacy.notification_datetime = e.target.value;
            this.$emit('input');
        },
        onBlurHour(e) {

            this.pharmacy.notification_hour = e.target.value;
            this.$emit('input');
        },
        changeValue() {
            this.pharmacy.notification_date = this.pharmacy.notification_datetime + ' ' + this.pharmacy.notification_hour;


            if (moment(this.pharmacy.notification_date).isValid()) {

                this.pharmacy.notification = 1;
            } else {
                this.pharmacy.notification = 0;
                this.pharmacy.notification_date = '';
            }


        },
        getGeolocation() {


            var vm = this;
            window.navigator.geolocation.getCurrentPosition(vm.localitation);

        },
        localitation(geo) {


            this.pharmacy.lat = geo.coords.latitude;
            this.pharmacy.lon = geo.coords.longitude;


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
        fill(pharmacy) {

            var cant = [];
            var dist = [];


            this.provincias.forEach(function (prov) {
                if (pharmacy.province === prov.id) {
                    cant = prov.cantones;
                }
            });

            this.cantones = cant;

            this.cantones.forEach(function (cant) {
                if (pharmacy.canton === cant.id) {
                    dist = cant.distritos;
                }
            });

            this.distritos = dist;


            this.pharmacy = pharmacy;

            if (this.pharmacy.notification_date == '0000-00-00 00:00:00' || this.pharmacy.notification_date == null || this.pharmacy.notification_date == 'null') {
                this.pharmacy.notification_date = '';
                this.pharmacy.notification_datetime = '';
                this.pharmacy.notification_hour = '';
                this.pharmacy.notification = 0;
            }

        },
        clearForm() {

            this.pharmacy = {
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

        this.emitter.on('editPharmacy', this.fill);
        this.emitter.on('newPharmacy', this.clearForm);
        this.emitter.on('input', this.changeValue);

    }
};
</script>
