<template>
    <div>
        <div id="myModal" aria-labelledby="myModalLabel" class="modal fade" role="dialog">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <h4 id="myModalLabel" class="modal-title">Crear cita</h4>
                    </div>
                    <div class="modal-body" data-modaldate data-modaldate-end>

                        <div v-show="!newPatient" class="box box-widget widget-user-2"
                             v-bind:data-office="(office) ? office : '' "
                             v-bind:data-patient="(paciente) ? paciente.id : '' "
                             v-bind:data-title=" (paciente) ? paciente.first_name : '' ">
                            <select-patient :patient="paciente" :url="'/general/patients'"
                                            @selectedPatient="select"></select-patient>

                            <div v-show="!isPatient" class="form-group">
                                <p>Este paciente no esta asociado al medico. Deseas asignarlo a este medic?</p>
                                <button class="btn btn-secondary" type="button" @click="verifyId()">Asignar</button>
                                <span v-if="errors.code" class="label label-danger">Código Incorrecto o no existe</span>
                            </div>

                            <div class="form-group">
                                <input id="modal-new-event" class="form-control" data-modaldate
                                       data-modaldate-end placeholder="Motivo de la cita" type="text">
                            </div>
                            <a class="" href="#" @click="nuevo()">o Crear un paciente?</a>
                        </div>

                        <div v-show="newPatient">
<!--                            <new-patient @created="selectedPatientRecentlyCreated" @canceled="cancel"></new-patient>-->
                            <short-patient-form :endpoint="endpoint" @canceled="cancel"
                                                @created="selectedPatientRecentlyCreated"></short-patient-form>

                        </div>


                    </div>
                    <div v-show="!newPatient" class="modal-footer">

                        <button class="btn btn-secondary pull-left btn-cancelar-cita" data-dismiss="modal"
                                type="button">Cancelar
                        </button>
                        <button class="btn btn-primary btn-finalizar-cita" type="button">Crear cita</button>
                        <button class="btn btn-primary btn-close-cita" data-dismiss="modal" type="button">Cerrar
                        </button>
                        <img v-show="loader" alt="Cargando..." src="/img/loading.gif">
                    </div>
                </div>
            </div>
        </div>


    </div>
</template>

<script>
import SelectPatient from './SelectPatient.vue';
import ShortPatientForm from './ShortPatientForm.vue';
//import Select2 from './Select2.vue';


export default {

    props: ['patient', 'fromClinic', 'office', 'endpoint'],
    components: {SelectPatient, ShortPatientForm},
    data() {
        return {
            paciente: null,
            loader: false,
            newPatient: false,
            selectedPatient: 0,
            options: [],
            isPatient: true,
            tokenPatient: '',
            validated: false,
            errors: []


        };
    },

    methods: {

        nuevo() {
            this.newPatient = true;
            //this.paciente = {};
        },

        cancel() {
            //this.paciente = this.patient;
            this.newPatient = false;

        },

        select(patient) {

            if (patient) {

                this.paciente = patient;
                this.selectedPatient = patient.id;

                //  axios.get('/medics/'+ $('.modal-body').attr('data-medic') +'/patients/'+ patient.id +'/verify')
                //  .then(({data}) =>{

                //    if(data == 'yes')
                //      { 
                //       this.isPatient = true;
                //       this.validated = true;
                //      }
                //     else{
                //       this.isPatient = false;
                //       this.validated = false;
                //     }

                //     this.paciente = patient;
                //     this.selectedPatient = patient.id;

                //  })


            } else {
                this.selectedPatient = 0;
                this.paciente = null;

            }

            this.newPatient = false;

        },
        selectedPatientRecentlyCreated(patient) {

            if (patient) {
                this.paciente = patient;
                this.selectedPatient = patient.id;
                this.emitter.emit('selectedPatientToSelect', patient);
            } else {
                this.selectedPatient = 0;
                this.paciente = null;
            }
            this.newPatient = false;

        },


    }, //methods
    created() {
        console.log('Component ready. Modal Appointments');

        //  bus.$on('patientCreated', this.selectedPatientRecentlyCreated);
        //  bus.$on('selectedPatient', this.select);
        //  bus.$on('cancelNewPatient', this.cancel);

        if (this.patient) {
            this.paciente = this.patient;
        }


    }
};
</script>