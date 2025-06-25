<template>
  <div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog " role="document">
        <div class="modal-content">
          <div class="modal-header">

            <h4 class="modal-title" id="myModalLabel">Confirmacion de la cita</h4>
          </div>
          <div class="modal-body" data-modaldate>

            <p v-show="phone"> Si lo desea puede confirmar su cita al teléfono: <a :href="'tel:'+ phone" class="btn btn-primary btn-xs"><i class="fa fa-phone" :title="phone"></i> {{ phone }}</a></p>
            <div class="box box-widget widget-user-2" v-show="!newPatient" v-bind:data-patient="paciente.id " v-bind:data-title=" paciente.first_name ">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              <div class="widget-user-header bg-primary">
                <div class="widget-user-image">

                  <img class="profile-user-img img-responsive img-circle" v-bind:src="'/img/default-avatar.jpg'" alt="User profile picture">

                </div>
                <!-- /.widget-user-image -->
                <h3 class="widget-user-username">{{ paciente.first_name }}</h3>
                <h5 class="widget-user-desc">{{ (paciente.gender == 'm') ? 'Masculino' : 'Femenino' }}</h5>
              </div>
              <div class="box-footer no-padding">




                <ul class="nav nav-stacked">

                  <li><a href="#">Teléfono: {{ paciente.phone_number }} </a></li>
                  <li><a href="#">Email: {{ paciente.email }} </a></li>

                </ul>


                <a href="#" @click="nuevo()" class="">Es un paciente distinto?</a>
              </div>


            </div>
            <div v-show="newPatient">
              <div class="callout callout-info">
                <h4>Información !</h4>
                <p>Agrega un paciente nuevo o selecciona uno de la lista de abajo.</p>
              </div>

              <patients @canceled="cancel" @selected="select" :from-reservation="1"></patients>

            </div>




          </div>
          <div class="modal-footer" v-show="!newPatient">

            <button type="button" class="btn btn-default pull-left btn-cancelar-cita" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary btn-finalizar-cita">Crear cita</button>
            <button type="button" class="btn btn-primary btn-close-cita" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>


  </div>
</template>

<script>

import Patients from './Patients.vue';


export default {

    props: ['patient', 'patients', 'phone'],

    data() {
        return {
            paciente: {},
            loader: false,
            newPatient: false,
            editPhone: false,
            editEmail: false,
            oldPhone: '',
            oldEmail: ''


        };
    },
    components: {
        Patients
    },
    methods: {
        nuevo() {
            this.newPatient = true;
            this.paciente = {};
        },
        cancelLine() {

            this.editPhone = false;
            this.editEmail = false;
            this.paciente.phone = this.oldPhone;
            this.paciente.email = this.oldEmail;

        },
        cancel() {
            this.paciente = this.patient;
            this.newPatient = false;

        },
        edit(patient) {

            this.paciente = patient;

        },
        select(patient) {

            this.paciente = patient;
            this.newPatient = false;

        },
        save() {

            //var resource = this.$resource('/medic/account/offices');
            this.loader = true;
            if (this.paciente.id) {

                axios.put('/patients/' + this.paciente.id, this.paciente)
                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.paciente = data;
                        this.oldEmail = this.paciente.email;
                        this.oldPhone = this.paciente.phone;
                        this.editPhone = false;
                        this.editEmail = false;
                        flash('Paciente Actualizado.');
                        this.$emit('updated', data);
                    })
                    .catch(error => {
                        this.loader = false;
                        flash(error.response.data.message, 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            } else {
                axios.post('/patients', this.paciente)

                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.paciente = data;
                        this.newPatient = false;
                        flash('Paciente Creado.');
                        this.$emit('created', data);
                    })
                    .catch(error => {
                        this.loader = false;
                        flash(error.response.data.message, 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            }

        }//save


    }, //methods
    created() {
        console.log('Component ready. office');

        this.emitter.on('selectedPatient', this.select);
        this.emitter.on('cancelNewPatient', this.cancel);

        this.paciente = this.patient;
        this.oldEmail = this.patient.email;
        this.oldPhone = this.patient.phone;


    }
};
</script>