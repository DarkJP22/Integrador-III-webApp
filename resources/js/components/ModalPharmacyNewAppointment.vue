<template>
    <div>
        <div id="myModal" aria-labelledby="myModalLabel" class="modal fade" role="dialog">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <h4 id="myModalLabel" class="modal-title">Crear cita</h4>
                    </div>
                    <div class="modal-body" data-modaldate data-modaldate-end>

                        <div v-show="!newPatient" class="box-widget widget-user-2"
                             v-bind:data-office="(office) ? office : '' "
                             v-bind:data-patient="(paciente) ? paciente.id : '' "
                             v-bind:data-title=" (paciente) ? paciente.first_name : '' ">
                            <select-patient :patient="paciente" :url="'/general/patients'"
                                            @selectedPatient="select"></select-patient>

<!--                            <div v-show="!isPatient || !validated" class="form-group">-->
<!--                                <div v-if="isPatient && !validated">-->
<!--                                    <p>Parece que este paciente aun no te ha autorizado para crear consultas. Asignalo-->
<!--                                        agregando el Código de autorización para confirmar y poder crear una-->
<!--                                        consulta</p>-->
<!--                                </div>-->
<!--                                <div v-else>-->
<!--                                    <p>Este paciente no esta asociado a la clínica. Asignalo agregando el Código de-->
<!--                                        autorización para confirmar y poder crear una consulta</p>-->
<!--                                </div>-->

<!--                                <input id="modal-patient-id" v-model="tokenPatient" class="form-control" placeholder="Código"-->
<!--                                       type="text">-->
<!--                                <button class="btn btn-primary" type="button" @click="verifyId()">Verificar</button>-->
<!--                                <button class="btn btn-secondary" title="Solicitar Código de autorización" type="button"-->
<!--                                        @click="requestCode()">Solicitar-->
<!--                                </button>-->
<!--                                <span v-if="errors.code" class="label label-danger">Código Incorrecto o no existe</span>-->
<!--                            </div>-->

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
                        <button class="btn btn-primary btn-finalizar-cita"
                                type="button">Crear cita
                        </button>
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
            validated: true,
            errors: []


        };
    },

    methods: {

        requestCode() {
            this.showModalRequestCode();

        },
        showModalRequestCode() {
            let html = 'Selecciona en la lista a quienes quieras solicitar el codigo y/o escribe un número en el campo de abajo<br><br>';

            html += '<div class="checks-contacts text-left"></div>';

            Swal.fire({
                title: 'Solicitar Código de autorización',
                html: html,
                showCancelButton: true,
                confirmButtonColor: '#67BC9A',
                cancelButtonColor: '#dd4b39',
                cancelButtonText: 'Cerrar',
                confirmButtonText: 'Solicitar',
                didOpen: () => {

                    if (this.loader) {
                        return;
                    }
                    const $checksContacs = $('.checks-contacts');

                    $checksContacs.html('<h2>Cargando...</h2>');

                    this.loader = true;
                    axios.get(`/patients/${this.paciente.id}/responsables`)
                        .then(({data}) => {
                            this.loader = false;

                            let html = `<label for="contact_${this.paciente.id}"><input id="contact_${this.paciente.id}" class="" type="checkbox" value="${this.paciente.phone_country_code}${this.paciente.phone_number}" checked> ${this.paciente.fullname} (${this.paciente.phone_country_code}) ${this.paciente.phone_number}</label> <br>`;

                            if (data) {

                                data.forEach((contact, index) => {
                                    html += `<label for="contact_${this.paciente.id + index + 1}"><input id="contact_${this.paciente.id + index + 1}" class="" type="checkbox" value="${contact.phone_country_code}${contact.phone_number}"> ${contact.name} (${contact.phone_country_code}) ${contact.phone_number}</label> <br>`;
                                });

                            }
                            html += '<input type="text" class="form-control" name="numero" id="customNum" placeholder="Otro Numero Telefónico" />';


                            $checksContacs.html(html);


                        });


                }

            }).then((result) => {

                const contacts = [];

                const inputs = document.querySelectorAll('input[type=\'checkbox\']');
                const otroNumero = document.getElementById('customNum');

                for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].checked == true) {
                        contacts.push(inputs[i].value);
                    }
                }

                if (otroNumero && otroNumero.value) {
                    contacts.push(`+506${otroNumero.value}`);
                }


                if (result.value) {

                    this.generateCode(this.paciente, contacts);

                }


            });
        },
        generateCode(patient, contacts) {

            axios.post(`/patients/${patient.id}/generateauth`, {contacts})
                .then(() => {

                    this.emitter.emit('generatedCode', patient);

                    flash('Codigo Generado!');

                }).catch(error => {
                    if (error.response.status == 500) {

                        flash('Error al generar código. ' + error.response.data.message, 'danger');
                    } else {
                        flash('Error al generar código.', 'danger');
                    }

                });


        },
        verifyId() {

            axios.post('/patients/' + this.selectedPatient + '/addauth', {code: this.tokenPatient})
                .then(() => {
                    this.validated = true;
                    this.isPatient = true;
                    flash('Paciente Asignado Correctamente! Ya puedes crear la cita');

                }).catch(error => {
                    this.validated = false;

                    flash('Código Incorrecto o no existe', 'danger');

                    this.errors = error.response.data.errors ? error.response.data.errors : [];

                });


        },

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

                axios.get('/offices/' + this.office + '/patients/' + patient.id + '/verify')
                    .then(({data}) => {


                        this.isPatient = data['isPatient'];
                        this.validated = data['isAuthorized'];


                        this.paciente = patient;
                        this.selectedPatient = patient.id;

                    });


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