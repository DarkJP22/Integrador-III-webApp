<template>
    <div>
        <div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Crear cita</h4>
                    </div>
                    <div class="modal-body" data-modaldate data-modaldate-end>
                        <div class="box-widget widget-user-2" v-show="!newPatient"
                             v-bind:data-patient="paciente ? paciente.id : ''"
                             v-bind:data-title="paciente ? paciente.first_name : ''"
                             v-bind:data-office="office ? office : ''" v-bind:data-room="selectedRoom"
                             v-bind:data-optreatments="estreatmentIds">
                            <div class="form-group">
                                <select name="room_id" id="room_id" v-model="selectedRoom" class="form-control" required
                                        @change="checkRoomAvailable()">
                                    <option value="">-- Seleccione una sala --</option>
                                    <option :value="item.id" v-for="(item, index) in rooms"
                                            :selected="index == 0 ? 'selected' : ''" :key="item.id">
                                        {{ item.name }}
                                    </option>
                                </select>
                            </div>

                            <select-patient :patient="paciente" @selectedPatient="select"
                                            :url="'/general/patients'"></select-patient>

                            <!-- <div class="form-group" v-show="!isPatient || !validated">
                <div v-if="isPatient && !validated">
                  <p>
                    Parece que este paciente aun no te ha autorizado para crear
                    consultas. Asignalo agregando el Código de autorización para
                    confirmar y poder crear una consulta
                  </p>
                </div>
                <div v-else>
                  <p>
                    Este paciente no esta asociado a la clínica. Asignalo
                    agregando el Código de autorización para confirmar y poder
                    crear una consulta
                  </p>
                </div>

                <input id="modal-patient-id" type="text" class="form-control" v-model="tokenPatient" placeholder="Código" />
                <button type="button" class="btn btn-primary" @click="verifyId()">
                  Verificar
                </button>
                <button type="button" class="btn btn-secondary" @click="requestCode()" title="Solicitar Código de autorización">
                  Solicitar
                </button>
                <span class="label label-danger" v-if="errors.code">Código Incorrecto o no existe</span>
              </div> -->
                            <div class="form-group" v-if="isEsthetic">
                                <v-select :options="optreatments" placeholder="Buscar tratamiento..." label="name"
                                          multiple v-model="estreatments">
                                    <template slot="no-options">
                                        Escribe para buscar los tratamientos
                                    </template>
                                </v-select>
                            </div>
                            <div class="form-group">
                                <input id="modal-new-event" type="text" class="form-control"
                                       placeholder="Motivo de la cita" data-modaldate data-modaldate-end/>
                            </div>
                            <a href="#" @click="nuevo()" class="">o Crear un paciente?</a>
                        </div>

                        <div v-show="newPatient">
                            <!-- <new-patient @created="selectedPatientRecentlyCreated" @canceled="cancel"></new-patient> -->
                            <short-patient-form :endpoint="endpoint" @created="selectedPatientRecentlyCreated"
                                                @canceled="cancel"></short-patient-form>
                        </div>
                    </div>
                    <div class="modal-footer" v-show="!newPatient">
                        <button type="button" class="btn btn-secondary pull-left btn-cancelar-cita"
                                data-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-primary btn-finalizar-cita" :disabled="!roomAvailable">
                            Crear cita
                        </button>
                        <button type="button" class="btn btn-primary btn-close-cita" data-dismiss="modal">
                            Cerrar
                        </button>
                        <img src="/img/loading.gif" alt="Cargando..." v-show="loader"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import SelectPatient from './SelectPatient.vue';
import ShortPatientForm from './ShortPatientForm.vue';
//import Select2 from './Select2.vue';

export default {
    props: ['patient', 'fromClinic', 'office', 'optreatments', 'isEsthetic', 'endpoint'],
    components: {SelectPatient, ShortPatientForm, vSelect},
    data() {
        return {
            paciente: null,
            loader: false,
            newPatient: false,
            selectedPatient: 0,
            selectedRoom: '',
            options: [],
            isPatient: true,
            tokenPatient: '',
            validated: true,
            errors: [],
            rooms: [],
            roomAvailable: false,
            estreatments: []
        };
    },
    computed: {
        estreatmentIds() {
            return this.estreatments.map(t => t.id);
        }
    },
    methods: {
        checkRoomAvailable() {
            //console.log('se chequea disponibilidad');
            this.roomAvailable = false;

            if (this.selectedRoom) {
                const modalBody = document.querySelector('#myModal .modal-body');

                axios
                    .get(
                        '/offices/' +
                        this.office +
                        '/rooms/' +
                        this.selectedRoom +
                        '/checkavailability?date1=' +
                        modalBody.dataset.modaldate
                    )
                    .then(({data}) => {
                        if (data) {
                            Swal.fire({
                                title: 'Disponibilidad de sala',
                                html: 'Parece que no puedes reservar en esta fecha y hora ya que la sala esta ocupada',
                                showCancelButton: false,
                                confirmButtonColor: '#67BC9A',
                                cancelButtonColor: '#dd4b39',
                                cancelButtonText: 'No',
                                confirmButtonText: 'Ok',
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
            let html =
                'Selecciona en la lista a quienes quieras solicitar el codigo y/o escribe un número en el campo de abajo<br><br>';

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
                    axios
                        .get(`/patients/${this.paciente.id}/responsables`)
                        .then(({data}) => {
                            this.loader = false;

                            let html = `<label for="contact_${this.paciente.id}"><input id="contact_${this.paciente.id}" class="" type="checkbox" value="${this.paciente.phone_country_code}${this.paciente.phone_number}" checked> ${this.paciente.fullname} (${this.paciente.phone_country_code}) ${this.paciente.phone_number}</label> <br>`;

                            if (data) {
                                data.forEach((contact, index) => {
                                    html += `<label for="contact_${this.paciente.id + index + 1
                                    }"><input id="contact_${this.paciente.id + index + 1
                                    }" class="" type="checkbox" value="${contact.phone_country_code
                                    }${contact.phone_number}"> ${contact.name} (${contact.phone_country_code
                                    }) ${contact.phone_number}</label> <br>`;
                                });
                            }
                            html += '<input type="text" class="form-control" name="numero" id="customNum" placeholder="Otro Numero Telefónico" />';

                            $checksContacs.html(html);
                        });
                },
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
            axios
                .post(`/patients/${patient.id}/generateauth`, {contacts})
                .then(() => {
                    this.emitter.emit('generatedCode', patient);

                    flash('Codigo Generado!');
                })
                .catch((error) => {
                    if (error.response.status == 500) {
                        flash(
                            'Error al generar código. ' + error.response.data.message,
                            'danger'
                        );
                    } else {
                        flash('Error al generar código.', 'danger');
                    }
                });
        },
        verifyId() {
            axios
                .post('/patients/' + this.selectedPatient + '/addauth', {
                    code: this.tokenPatient,
                })
                .then(() => {
                    this.validated = true;
                    this.isPatient = true;
                    flash('Paciente Asignado Correctamente! Ya puedes crear la cita');
                })
                .catch((error) => {
                    this.validated = false;

                    flash('Código Incorrecto o no existe', 'danger');

                    this.errors = error.response.data.errors
                        ? error.response.data.errors
                        : [];
                });
        },
        //  verifyId(){

        //       axios.post('/patients/'+ this.selectedPatient +'/medics/'+ $('.modal-body').attr('data-medic') +'/authorization', { code: this.tokenPatient})
        //        .then(({data}) =>{
        //             this.validated = true;
        //             this.isPatient = true;
        //             flash('Paciente Asignado Correctamente! Ya puedes crear la cita');

        //        }).catch(error =>{
        //             this.validated = false;

        //            flash('Código Incorrecto o no existe', 'danger');

        //           this.errors = error.response.data.errors ? error.response.data.errors : [];

        //        });

        // //   if(this.selectedPatient == this.patientId)
        // //   {
        // //     this.validated = true;
        // //   }else
        // //     this.validated = false;
        //   //this.paciente = {};
        // },
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
                //this.paciente = patient;
                // this.selectedPatient = patient.id;
                axios
                    .get(
                        '/offices/' + this.office + '/patients/' + patient.id + '/verify'
                    )
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
            if (this.office) {
                axios.get('/offices/' + this.office + '/rooms').then(({data}) => {
                    this.rooms = data;
                });
            }
        },
    }, //methods
    created() {
        console.log('Component ready. Modal Appointments');

        this.emitter.on('openModalClinicNewAppointment', () => {
            this.selectedRoom = '';
        });

        if (this.patient) {
            this.paciente = this.patient;
        }

        this.getRoomsOffice();
    },
};
</script>