<template>
    <div id="myModal" aria-labelledby="myModalLabel" class="modal fade" role="dialog">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 v-show="!showPackages && !showPendingPayment" id="myModalLabel" class="modal-title">Crear
                        cita</h4>
                    <h4 v-show="showPackages">Parece que no tienes una <b>subscripción</b> todavia o esta se encuentra
                        vencida! Selecciona una para continuar con el proceso</h4>
                    <h4 v-show="showPendingPayment">Tienes pagos pendientes!. Haz el pago para poder continuar</h4>
                </div>
                <div class="modal-body" data-modaldate data-modaldate-end data-office data-officename>

                    <div v-show="!newPatient && !showPackages && !showPendingPayment" class="box-widget widget-user-2"
                         v-bind:data-office="(office) ? office.id : '' "
                         v-bind:data-patient="(paciente) ? paciente.id : '' "
                         v-bind:data-room="selectedRoom" v-bind:data-title=" (paciente) ? paciente.first_name : 'Paciente Desconocido' ">

                        <div class="form-group">
                            <select id="room_id" v-model="selectedRoom" class="form-control" name="room_id" required
                                    @change="checkRoomAvailable()">
                                <option value="">-- Seleccione una sala --</option>
                                <option v-for="(item, index) in rooms" :key="item.id"
                                        :selected="index == 0 ? 'selected' : '' " :value="item.id"> {{ item.name }}
                                </option>
                            </select>
                        </div>

                        <select-patient :patient="paciente" :url="'/general/patients'"
                                        @selectedPatient="select"></select-patient>
                        <!-- <div class="form-group" v-show="!isPatient || !validated">
                          <div v-if="isPatient && !validated">
                            <p>Parece que este paciente aun no te ha autorizado para iniciar consultas. Asignalo agregando el Código de autorización para confirmar y poder iniciar una consulta.. o solamente crea la cita e iniciala despues</p>
                          </div>
                          <div v-else>
                            <p>Este paciente no esta asociado al médico. Asignalo agregando el Código de autorización para confirmar y poder iniciar una consulta.. o solamente crea la cita e iniciala despues</p>
                          </div>
            
                          <input id="modal-patient-id" type="text" class="form-control" v-model="tokenPatient" placeholder="Código">
                          <button type="button" class="btn btn-primary" @click="verifyId()">Verificar</button>
                          <button type="button" class="btn btn-secondary" @click="requestCode()" title="Solicitar Código de autorización">Solicitar</button>
                          <span class="label label-danger" v-if="errors.code">Código Incorrecto o no existe</span>
                        </div> -->

                        <div class="form-group">
                            <input id="modal-new-event" class="form-control" data-modaldate data-modaldate-end
                                   placeholder="Motivo de la cita" type="text">
                        </div>
                        <a class="" href="#" @click="nuevo()">o Crear un paciente?</a>
                    </div>

                    <div v-show="newPatient && !showPackages">
                        <!-- <div class="callout callout-info"><h4>Información !</h4> <p>Agrega un paciente nuevo</p></div> -->
                        <!-- <new-patient @created="selectedPatientRecentlyCreated" @canceled="cancel"></new-patient> -->
                        <short-patient-form :endpoint="endpoint" @canceled="cancel"
                                            @created="selectedPatientRecentlyCreated"></short-patient-form>
                    </div>
                    <div v-show="showPendingPayment" class="pending-payment text-center">

                        <table-pending-payments :monthlyCharges="monthlyCharges"></table-pending-payments>


                    </div>
                    <div v-show="showPackages" class="text-center">
                        <table-subscriptions></table-subscriptions>
                    </div>


                </div>
                <div v-show="!newPatient" class="modal-footer">

                    <button class="btn btn-secondary pull-left btn-cancelar-cita" data-dismiss="modal" type="button"
                            @click="showPackages = false; showPendingPayment = false; ">Cancelar
                    </button>
                    <button class="btn btn-secondary btn-iniciar-cita" data-url="/beautician/agenda/appointments/"
                            type="button">Iniciar consulta
                    </button>
                    <button :disabled="!roomAvailable" class="btn btn-primary btn-finalizar-cita" type="button">Crear
                        cita
                    </button>
                    <button class="btn btn-default btn-close-cita" data-dismiss="modal" type="button">Cerrar</button>
                    <img v-show="loader" alt="Cargando..." src="/img/loading.gif">
                </div>
            </div>
        </div>
    </div>

</template>
<script>
import SelectPatient from './SelectPatient.vue';
import ShortPatientForm from './ShortPatientForm.vue';
import TableSubscriptions from './TableSubscriptions.vue';
import TablePendingPayments from './TablePendingPayments.vue';


export default {

    props: ['patient', 'hasSubscription', 'pendingPayment', 'pendingPaymentTotal', 'token', 'endpoint'],
    components: {SelectPatient, ShortPatientForm, TableSubscriptions, TablePendingPayments},
    data() {
        return {
            paciente: null,
            loader: false,
            newPatient: false,
            selectedPatient: 0,
            selectedRoom: '',
            options: [],
            offices: [],
            office: null,
            subscriptionsPackages: [],
            monthlyCharges: [],
            showPackages: false,
            showPendingPayment: false,
            isPatient: true,
            tokenPatient: '',
            validated: true,
            errors: [],
            rooms: [],
            roomAvailable: false,
            officeId: null

        };
    },

    methods: {
        checkRoomAvailable() {
            //console.log('se chequea disponibilidad');
            this.roomAvailable = false;

            if (this.selectedRoom) {
                const modalBody = document.querySelector('#myModal .modal-body');

                axios.get('/offices/' + this.officeId + '/rooms/' + this.selectedRoom + '/checkavailability?date1=' + modalBody.dataset.modaldate)
                    .then(({data}) => {

                        if (data) {

                            Swal.fire({
                                title: 'Disponibilidad de sala',
                                html: 'Parece que no puedes reservar en esta fecha y hora ya que la sala esta ocupada',
                                showCancelButton: false,
                                confirmButtonColor: '#67BC9A',
                                cancelButtonColor: '#dd4b39',
                                cancelButtonText: 'No',
                                confirmButtonText: 'Ok'
                            }).then(() => {

                                this.roomAvailable = false;

                                return;

                            });


                        } else {
                            this.roomAvailable = true;
                        }

                    });
            }
        },
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

            axios.post('/patients/' + this.selectedPatient + '/medics/' + window.App.user.id + '/addauthorization', {code: this.tokenPatient})
                .then(() => {
                    this.validated = true;
                    this.isPatient = true;
                    flash('Paciente Asignado Correctamente! Ya puedes iniciar la cita');

                }).catch(error => {
                    this.validated = false;

                    flash('Código Incorrecto o no existe', 'danger');

                    this.errors = error.response.data.errors ? error.response.data.errors : [];

                });

            //   if(this.selectedPatient == this.pacienteId)
            //   {
            //     this.validated = true;
            //   }else
            //     this.validated = false;
            //this.paciente = {};
        },
        nuevo() {
            this.newPatient = true;
            //this.paciente = {};
        },

        cancel() {
            //this.paciente = this.paciente;
            this.newPatient = false;

        },

        select(patient) {

            if (patient) {
                //this.paciente = patient;
                // this.selectedPatient = patient.id;
                axios.get('/medics/' + window.App.user.id + '/patients/' + patient.id + '/verify')
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
        getRoomsOffice() {


            if (this.officeId) {
                axios.get('/offices/' + this.officeId + '/rooms')
                    .then(({data}) => {
                        this.rooms = data;
                    });
            }
        },
        // getOffices(search, loading) {

        //     loading(true)

        //    let queryParam = {
        //         ['q']: search
        //       }
        //     this.$http.get('/medic/account/offices/list', {params: Object.assign(queryParam, this.data)})
        //     .then(resp => {

        //        this.offices = resp.data
        //        loading(false)
        //     })

        //   },
        //   selectOffice(clinica) {

        //     if(clinica){
        //       this.office = clinica;
        //       /*this.appointment.title = clinica.name;
        //       this.appointment.office_info = JSON.stringify(clinica);*/


        //     }


        //    },


    }, //methods

    created() {
        console.log('Component ready. Modal Appointments');

        this.emitter.on('openModalNewAppointmentEstetica', (data) => {

            this.officeId = data;
            this.selectedRoom = '';
            this.getRoomsOffice();
        });

        //this.monthlyCharges = this.pending_payment;

        //  this.emitter.on('patientCreated', this.selectedPatientRecentlyCreated);
        //  this.emitter.on('selectedPatient', this.select);
        //  this.emitter.on('cancelNewPatient', this.cancel);


    }
};
</script>
