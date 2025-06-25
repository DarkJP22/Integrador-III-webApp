<template>
  <form class="form-horizontal" @submit.prevent="save()">
    <loading :show="loader"></loading>
    <!-- <div class="form-group">
        <label for="name" class="col-sm-2 control-label">ID</label>

        <div class="col-sm-10">
           <label for="name" class="control-label">{{ paciente.id }}</label>  
        </div>
      </div> -->
    <div class="form-group">
      <label class="col-sm-2 control-label" for="tipo_identificacion">Tipo identificación</label>

      <div class="col-sm-10">
        <select v-model="paciente.tipo_identificacion" class="form-control" name="tipo_identificacion" style="width: 100%;">
          <option value=""></option>
          <option v-for="(value, key) in tipoIdentificaciones" :key="key" :value="key">
            {{ value }}
          </option>
        </select>
        <form-error v-if="errors.tipo_identificacion" :errors="errors" style="float:right;">
          {{ errors.tipo_identificacion[0] }}
        </form-error>
      </div>

    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="ide">Cédula</label>

      <div class="col-sm-10">
        <input v-model="paciente.ide" :disabled="loader" class="form-control" name="ide" placeholder="" type="text" @keydown.prevent.enter="searchCustomer()">
        <form-error v-if="errors.ide" :errors="errors" style="float:right;">
          {{ errors.ide[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="paciente_name">Nombre Completo</label>

      <div class="col-sm-10">
        <input v-model="paciente.first_name" class="form-control" name="first_name" placeholder="Nombre del paciente" required type="text">
        <form-error v-if="errors.first_name" :errors="errors" style="float:right;">
          {{ errors.first_name[0] }}
        </form-error>
      </div>
    </div>
    <!-- <div class="form-group">
        <label for="paciente_address" class="col-sm-2 control-label">Apellidos</label>

        <div class="col-sm-10">
          <input type="text" class="form-control" name="last_name" placeholder="Apellidos"  v-model="paciente.last_name" required>
          <form-error v-if="errors.last_name" :errors="errors" style="float:right;">
              {{ errors.last_name[0] }}
          </form-error>
        </div>
      </div> -->
    <div class="form-group">
      <label class="col-sm-2 control-label" for="paciente_birth_date">Fecha de Nacimiento</label>

      <div class="col-sm-10">
        <input v-model="paciente.birth_date" class="form-control" name="birth_date" placeholder="yyyy-mm-dd" required  type="text">
        <form-error v-if="errors.birth_date" :errors="errors" style="float:right;">
          {{ errors.birth_date[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="paciente_province">Sexo</label>

      <div class="col-sm-10">
        <select v-model="paciente.gender" class="form-control " name="gender" required style="width: 100%;">

          <option v-for="item in genders" :key="item.id" v-bind:value="item.value"> {{ item.text }}</option>

        </select>
        <form-error v-if="errors.gender" :errors="errors" style="float:right;">
          {{ errors.gender[0] }}
        </form-error>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-2 control-label" for="paciente_phone">Teléfono</label>

      <div class="col-sm-3">
        <select v-model="paciente.phone_country_code" class="form-control " name="phone_country_code" required style="width: 100%;">

          <option v-for="item in phoneCodes" :key="item.id" v-bind:value="item.value"> {{ item.text }}</option>

        </select>
        <form-error v-if="errors.phone_country_code" :errors="errors" style="float:right;">
          {{ errors.phone_country_code[0] }}
        </form-error>
      </div>
      <div class="col-sm-7">
        <input v-model="paciente.phone_number" class="form-control" name="phone_number" placeholder="Teléfono Celular" required type="text">
        <form-error v-if="errors.phone_number" :errors="errors" style="float:right;">
          {{ errors.phone_number[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="paciente_phone">Email</label>

      <div class="col-sm-10">
        <input v-model="paciente.email" class="form-control" name="email" placeholder="Email" type="email">
        <form-error v-if="errors.email" :errors="errors" style="float:right;">
          {{ errors.email[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="paciente_address">Dirección</label>

      <div class="col-sm-10">
        <input v-model="paciente.address" class="form-control" name="address" placeholder="Dirección" type="text">
        <form-error v-if="errors.address" :errors="errors" style="float:right;">
          {{ errors.address[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="paciente_province">Provincia</label>

      <div class="col-sm-10">
        <select v-model="paciente.province" class="form-control " name="province" required style="width: 100%;">

          <option v-for="item in provincias" :key="item.id" v-bind:value="item.id"> {{ item.title }}</option>

        </select>
        <form-error v-if="errors.province" :errors="errors" style="float:right;">
          {{ errors.province[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="paciente_city">Ciudad</label>

      <div class="col-sm-10">
        <input v-model="paciente.city" class="form-control" name="city" placeholder="Ciudad" type="text">
        <form-error v-if="errors.city" :errors="errors" style="float:right;">
          {{ errors.city[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="conditions">Padecimientos</label>

      <div class="col-sm-10">
        <v-select v-model="paciente.conditions" :options="mediaTags" label="name" multiple placeholder="Buscar Padecimiento...">

          <template slot="no-options">
            Escribe para buscar los padecimientos
          </template>

        </v-select>
        <!-- <input type="text" class="form-control" name="conditions" placeholder="" v-model="paciente.conditions"> -->
        <form-error v-if="errors.conditions" :errors="errors" style="float:right;">
          {{ errors.conditions[0] }}
        </form-error>
      </div>
    </div>
    <div v-if="showDiscountField" class="form-group">
      <label class="col-sm-2 control-label" for="paciente_discount">Descuento Empresarial</label>

      <div class="col-sm-10">
        <select v-model="paciente.discount_id" class="form-control " name="discount_id" style="width: 100%;">

          <option v-for="item in discounts" :key="item.id" v-bind:value="item.id"> {{ item.name }}</option>

        </select>
        <form-error v-if="errors.discount_id" :errors="errors" style="float:right;">
          {{ errors.discount_id[0] }}
        </form-error>
      </div>
    </div>

    <!-- <div class="form-group" v-if="showPasswordField">
          <label for="password" class="col-sm-2 control-label">Contraseña (Accesso a la plataforma): </label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="password" placeholder="Si dejas este espacio en blanco la contraseña sera el numero de teléfono" v-model="paciente.password">
            <span class="label label-warning">Recordar al usuario que su perfil queda creado y que esta es su clave genérica.</span>
          </div>

        </div> -->
    <div v-if="showEmergencyContactField" class="form-group">
      <label class="col-sm-2 control-label" for="contact_name">Contacto emergencia</label>

      <div class="col-sm-10">
        <input v-model="paciente.contact_name" class="form-control" name="contact_name" placeholder="Nombre del Contacto " type="text">
        <form-error v-if="errors.contact_name" :errors="errors" style="float:right;">
          {{ errors.contact_name[0] }}
        </form-error>
      </div>
    </div>
    <div v-if="showEmergencyContactField" class="form-group">
      <label class="col-sm-2 control-label" for="contact_phone_number">Teléfono emergencia</label>

      <div class="col-sm-3">
        <select v-model="paciente.contact_phone_country_code" class="form-control " name="contact_phone_country_code" style="width: 100%;">

          <option v-for="item in phoneCodes" :key="item.id" v-bind:value="item.value"> {{ item.text }}</option>

        </select>
        <form-error v-if="errors.contact_phone_country_code" :errors="errors" style="float:right;">
          {{ errors.contact_phone_country_code[0] }}
        </form-error>
      </div>
      <div class="col-sm-7">
        <input v-model="paciente.contact_phone_number" class="form-control" name="contact_phone_number" placeholder="Teléfono" type="text">
        <form-error v-if="errors.contact_phone_number" :errors="errors" style="float:right;">
          {{ errors.contact_phone_number[0] }}
        </form-error>
      </div>
    </div>
    <!-- <div class="form-group" v-if="showInvitationField">
        <div class="col-md-12">
          <h4>Asignar a cuenta</h4>
          <div class="input-group">
                <span class="input-group-addon" title="En caso de no tener correo propio llenar con correo de un usuario inscrito en la plataforma "><i class="fa fa-question"></i></span>
                <input type="text" class="form-control" name="invitation" placeholder="Correo de la cuenta de usuario" v-model="paciente.account" title="En caso de no tener correo propio llenar con correo de un usuario inscrito en la plataforma ">
              </div>
            
            <form-error v-if="errors.account" :errors="errors" style="float:right;">
                {{ errors.account[0] }}
            </form-error>


          </div>
          
       
      </div>
      <div class="form-group" v-if="showInvitationField">
        <div class="col-md-12">
          <h4>Enviar invitación a encargado</h4>
          <div class="input-group">
                <span class="input-group-addon" title="Tel. de la persona encargada del paciente"><i class="fa fa-question"></i></span>
                <input type="text" class="form-control" name="invitation" placeholder="Tel. del encargado del paciente" v-model="paciente.invitation" title="Tel. de la persona encargada del paciente">
              </div>
            
            <form-error v-if="errors.invitation" :errors="errors" style="float:right;">
                {{ errors.invitation[0] }}
            </form-error>


          </div>
          
       
      </div> -->


    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <button v-if="showSelectButton" class="btn btn-secondary" type="button" @click="select(paciente)">Seleccionar</button>
        <button class="btn btn-secondary" type="button" @click="cancel()">Cancelar</button><img v-show="loader" alt="Cargando..." src="/img/loading.gif">
      </div>
    </div>
  </form>
</template>
<script>
import Loading from './Loading.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
export default {
    props: ['patient', 'fromUser', 'fromReservation'],
    data() {
        return {
            tipoIdentificaciones: [],
            provincias: window.provincias,
            genders: [
                {
                    text: 'Masculino',
                    value: 'm'
                },
                {
                    text: 'Femenino',
                    value: 'f'
                },

            ],
            phoneCodes: [
                {
                    text: '+506',
                    value: '+506'
                }
            ],
            loader: false,
            paciente: {
                gender: 'm',
                province: '',
                phone_country_code: '+506',
                password: '',
                medic_to_assign: '',
                invitation: '',
                account: '',
                contact_name: '',
                contact_phone_number: '',
                contact_phone_country_code: '+506'

            },
            discounts: [],
            showDiscountField: this.fromUser ? false : true,
            showPasswordField: this.fromUser ? false : true,
            showSelectButton: (this.fromReservation) ? true : false,
            showInvitationField: this.fromUser ? false : true,
            showEmergencyContactField: true,
            errors: [],
            mediaTags: []

        };
    },
    components: {
        Loading,
        vSelect
    },
    methods: {
        clear() {
            this.showEmergencyContactField = true;
            this.paciente = {
                gender: 'm',
                province: '',
                phone_country_code: '+506',
                password: '',
                medic_to_assign: '',
                invitation: '',
                account: '',
                contact_name: '',
                contact_phone_number: '',
                contact_phone_country_code: '+506'
            };

        },
        select(patient) {
            if (patient.id) {

                this.$emit('selected', patient);
            }
        },
        cancel() {
            this.clear();
            this.$emit('canceled');

        },
        loadTags() {

            axios.get('/mediatags/tags')
                .then(({ data }) => {

                    this.mediaTags = data;

                    // this.setPatient();


                });
        },
        loadTipoIdentificaciones() {

            axios.get('/identificaciones/tipos')
                .then(({ data }) => {

                    this.tipoIdentificaciones = data;

                    // this.setPatient();


                });
        },
        loadDiscounts() {

            axios.get('/discounts')
                .then(({ data }) => {

                    this.discounts = data.data;

                    // this.setPatient();


                });
        },
        searchCustomer() {
            console.log(this.paciente.ide);

            const instance = axios.create();
            instance.defaults.headers.common = {};
            instance.defaults.headers.common.accept = 'application/json';

            this.loader = true;
            instance.get('https://api.hacienda.go.cr/fe/ae?identificacion=' + this.paciente.ide)
                .then(({ data }) => {
                    this.loader = false;
                    console.log(data);
                    this.paciente.first_name = data.nombre;
                    this.paciente.tipo_identificacion = data.tipoIdentificacion;
                }).catch(error => {
                    this.loader = false;
                    flash('Ocurrio un error en la consulta!!', 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });




        },
        save() {

            if (this.loader) {
                return;
            }

            this.loader = true;

            if (!this.paciente.id) {

                if ($('#myModal').find('.modal-body').attr('data-medic')) {
                    this.paciente.medic_to_assign = $('#myModal').find('.modal-body').attr('data-medic');
                }

                axios.post('/patients', this.paciente)

                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clear();
                        flash('Paciente Creado.');
                        this.$emit('created', data);
                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar paciente', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            } else {

                axios.put('/patients/' + this.paciente.id, this.paciente)
                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clear();
                        flash('Paciente Actualizado.');
                        this.$emit('updated', data);
                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar paciente', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            }

        },
        fill(patient) {
            this.showEmergencyContactField = false;
            this.paciente = {

                'id': patient.id,
                'tipo_identificacion': patient.tipo_identificacion,
                'ide': patient.ide,
                'first_name': patient.first_name,
                'last_name': patient.last_name,
                'birth_date': patient.birth_date,
                'gender': patient.gender,
                'phone_country_code': patient.phone_country_code,
                'phone_number': patient.phone_number,
                'email': patient.email,
                'address': patient.address,
                'province': patient.province,
                'city': patient.city,
                'conditions': patient.conditions,

            };

        }

    },

    created() {
        this.loadTipoIdentificaciones();
        this.loadDiscounts();
        this.loadTags();
        this.emitter.on('editPatient', this.fill);
        if (this.patient) {
            this.fill(this.patient);
        }

    }

};
</script>

