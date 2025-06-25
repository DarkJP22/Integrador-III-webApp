<template>
  <tr class="form-group" :class="evaluationAppointment ? 'evaluation-appointment' : ''">
    <td>
      <p v-if="evaluationAppointment">{{ date }}</p>
      <div class="input-group" style="width: 130px" v-else>
        <flat-pickr :config="configDate" v-model="date" class="form-control" placeholder="Seleccione fecha " @input="getHorario()">
        </flat-pickr>
        <div class="input-group-btn">
          <button type="button" class="btn btn-default" data-toggle>
            <i class="fa fa-calendar">
              <span aria-hidden="true" class="sr-only">Toggle</span>
            </i>
          </button>
        </div>
      </div>
    </td>
    <td>
      <p v-if="evaluationAppointment">Cita de evaluación</p>
      <div class="form-group" style="width: 130px" v-else>
        <select name="room_id" id="room_id" v-model="currentAppointment.room_id" class="form-control" required @change="getHorario()">
          <option value="">-- Seleccione una sala --</option>
          <option :value="room.id" v-for="(room, index) in rooms" :selected="index == 0 ? 'selected' : ''" :key="room.id">
            {{ room.name }}
          </option>
        </select>
        <form-error v-if="errors.room_id" :errors="errors" style="float: right">
          {{ errors.room_id[0] }}
        </form-error>
      </div>
    </td>
    <td>
      <p v-if="evaluationAppointment">Cita de evaluación</p>
      <div class="form-group" style="width: 130px" v-else>
        <select name="user_id" id="user_id" v-model="currentAppointment.user_id" class="form-control" required @change="getHorario()">
          <option value="">-- Seleccione una esteticista --</option>
          <option :value="esteticista.id" v-for="(esteticista, index) in esteticistas" :selected="index == 0 ? 'selected' : ''" :key="esteticista.id">
            {{ esteticista.name }}
          </option>
        </select>
        <form-error v-if="errors.user_id" :errors="errors" style="float: right">
          {{ errors.user_id[0] }}
        </form-error>
      </div>
    </td>
    <td>
      <p v-if="evaluationAppointment">{{ currentAppointmentTime }}</p>
      <div v-else class="edit-time-field" style="width: 90px">
        <div class="form-group" v-show="currentAppointment.id && !editTime">
          <a href="#" @click.prevent="editTime = true">{{ currentAppointmentTime }}</a>
        </div>
        <div class="form-group w-full" v-show="editTime">
          <dropdown classes="" :items="horas" @itemSelected="startTime = $event.value">
            <template v-slot:trigger>
              <button class="btn-current-appointment-time">
                {{ startTime ? startTime : "--:--" }}
              </button>
            </template>
          </dropdown>
          <dropdown classes="" :items="horas" @itemSelected="endTime = $event.value">
            <template v-slot:trigger>
              <button class="btn-current-appointment-time">
                {{ endTime ? endTime : "--:--" }}
              </button>
            </template>
          </dropdown>
          <!-- <select
            name="startTime"
            id="startTime"
            v-model="startTime"
            class="form-control"
            required
          >
            <option value="">-- Seleccione una hora --</option>
            <option
              :value="hora.value"
              v-for="hora in horas"
              :key="hora.value"
              :disabled="hora.disabled"
            >
              {{ hora.label }}
            </option>
          </select> -->
          <!-- <select
            name="endTime"
            id="endTime"
            v-model="endTime"
            class="form-control"
            required
          >
            <option value="">-- Seleccione una hora --</option>
            <option
              :value="hora.value"
              v-for="hora in horas"
              :key="hora.value"
              :disabled="hora.disabled"
            >
              {{ hora.label }}
            </option>
          </select> -->
          <form-error v-if="errors.startTime" :errors="errors" style="float: right">
            {{ errors.startTime[0] }}
          </form-error>
          <form-error v-if="errors.endTime" :errors="errors" style="float: right">
            {{ errors.endTime[0] }}
          </form-error>
        </div>
      </div>
    </td>
    <td>
      <p v-if="evaluationAppointment">Cita de evaluación</p>
      <div class="form-group" style="" v-else>
        <v-select :options="treatments" placeholder="" label="name" multiple v-model="estreatments">

          <template slot="no-options">
            Escribe para buscar los tratamientos
          </template>
        </v-select>
        <!-- <select
          v-model="estreatments"
          class="form-control"
          required
          multiple
        >
          <option value="">-- Seleccione un tratamiento --</option>
          <option
            :value="treatment.id"
            v-for="(treatment, index) in treatments"
            :selected="index == 0 ? 'selected' : ''"
            :key="treatment.id"
          >
            {{ treatment.name }}
          </option>
        </select> -->
        <form-error v-if="errors.estreatments" :errors="errors" style="float: right">
          {{ errors.estreatments[0] }}
        </form-error>
      </div>
    </td>
    <td>
      <div style="width: 80px">
        <button v-if="!evaluationAppointment" type="button" class="btn btn-primary" @click="save" title="Guardar" :disabled="loader">
          <i class="fa fa-save"></i>
        </button>
        <button type="button" class="btn btn-danger" @click="remove()" v-if="showDeleteButton" data-clear>
          <i class="fa fa-trash">
            <span aria-hidden="true" class="sr-only">Remove</span>
          </i>
        </button>
      </div>
    </td>
  </tr>
</template>
<script>
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import flatPickr from 'vue-flatpickr-component';
import Dropdown from './Dropdown.vue';
import 'flatpickr/dist/flatpickr.css';
export default {
    props: {
        appointment: {
            type: Object,
            required: true,
        },
        treatments: {
            type: Array,
            required: true,
        },
        patient: {
            type: Object,
            required: true,
        },
        officeId: {
            type: Number,
            required: true,
        },
        rooms: {
            type: Array,
            required: true,
        },
        esteticistas: {
            type: Array,
            required: true,
        },
    },
    components: {
        flatPickr,
        Dropdown,
        vSelect
    },
    data() {
        return {
            loader: false,
            errors: [],
            configDate: {
                wrap: true,
                enableTime: false,
                dateFormat: 'Y-m-d',
                disableMobile: true,
            },
            configTime: {
                wrap: true,
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                disableMobile: true,
            },
            currentAppointment: this.appointment,
            date: this.appointment.date
                ? moment(this.appointment.date).format('YYYY-MM-DD')
                : null,
            startTime: this.appointment.start
                ? moment(this.appointment.start).format('HH:mm')
                : null,
            endTime: this.appointment.end
                ? moment(this.appointment.end).format('HH:mm')
                : null,
            horas: [],
            estreatments: this.appointment.estreatments?.length ? this.appointment.estreatments.filter(t => t.optreatment_id).map(t => { return { id: t.optreatment_id, name: t.name }; }) : [],
            editTime: false,
        };
    },
    watch: {
        editTime(editTime) {
            if (editTime) {
                document.addEventListener('click', this.closeIfClickedOutside);
            }
        },
    },
    computed: {
        currentAppointmentTime() {
            if (
                !this.startTime &&
        !this.currentAppointment.start &&
        !this.endTime &&
        !this.currentAppointment.end
            ) {
                return 'Seleccionar horas';
            }

            return (
                (this.startTime ??
          (this.currentAppointment.start
              ? moment(this.currentAppointment.start).format('HH:mm')
              : '')) +
        ' - ' +
        (this.endTime ??
          (this.currentAppointment.end
              ? moment(this.currentAppointment.end).format('HH:mm')
              : ''))
            );
        },
        showDeleteButton() {
            return !isNaN(this.currentAppointment.id);
        },
        evaluationAppointment() {
            return (
                this.currentAppointment.office_id &&
        !this.currentAppointment.estreatments.length
            );
        },
    },
    methods: {
        closeIfClickedOutside(event) {
            if (!event.target.closest('.edit-time-field')) {
                this.editTime = false;
            }
        },
        async getHorario() {
            if (
                !this.date ||
        !this.currentAppointment.room_id ||
        !this.currentAppointment.user_id
            ) {
                this.horas = [];
                return;
            }
            try {
                const { data } = await axios.get(
                    '/beautician/users/' +
          this.currentAppointment.user_id +
          '/available-hours',
                    {
                        params: {
                            date: this.date,
                            room_id: this.currentAppointment.room_id,
                            user_id: this.currentAppointment.user_id,
                            office_id: this.officeId,
                        },
                    }
                );
                this.horas = [];
                this.horas.push(...data);
            } catch (error) {
                this.horas = [];
                flash(
                    'Ha occurrido un error al consultar el horario disponible',
                    'danger'
                );
            }
        },
        remove() {
            if (!confirm('Estas Seguro')) {
                return;
            }
            this.loader = true;
            axios
                .delete(
                    '/beautician/patients/' +
          this.patient.id +
          '/agenda/treatment-appointments/' +
          this.currentAppointment.id
                )
                .then(() => {
                    this.loader = false;

                    this.$emit('appointmentDeleted', this.currentAppointment.id);
                    this.currentAppointment = {};
                    flash('Cita eliminada!');
                })
                .catch((error) => {
                    this.loader = false;
                    if (error.response.status == 422) {
                        Swal.fire({
                            title: 'Eliminación de cita',
                            html: error.response.data.message ?? 'Error al eliminar cita', //"Parece que no puedes eliminar",
                            showCancelButton: false,
                            confirmButtonColor: '#67BC9A',
                            cancelButtonColor: '#dd4b39',
                            cancelButtonText: 'No',
                            confirmButtonText: 'Ok',
                        });
                    } else {
                        flash('Error al eliminar cita', 'danger');
                    }
                    // this.errors = error.response.data.errors ? error.response.data.errors : [];
                });
        },
        save() {
            if (!this.date || !this.startTime || !this.endTime) {
                return;
            }

            if (this.startTime > this.endTime) {
                alert('La hora de inicio no puede ser mayor a la hora final');
                return;
            }

            if (!this.estreatments.length) {
                return;
            }

            if (!this.currentAppointment.room_id) {
                return;
            }
            if (!this.currentAppointment.user_id) {
                return;
            }

            this.loader = true;
            if (!isNaN(this.currentAppointment.id)) {
                axios
                    .put(
                        '/beautician/patients/' +
            this.patient.id +
            '/agenda/treatment-appointments/' +
            this.currentAppointment.id,
                        {
                            date: this.date,
                            start: this.date + ' ' + this.startTime,
                            end: this.date + ' ' + this.endTime,
                            user_id: this.currentAppointment.user_id,
                            patient_id: this.patient.id,
                            office_id: this.officeId,
                            room_id: this.currentAppointment.room_id,
                            optreatment_ids: this.estreatments.map(t => t.id),
                        }
                    )
                    .then(({ data }) => {
                        this.loader = false;
                        this.currentAppointment = data;
                        this.$emit('appointmentUpdated', data);
                        flash('Cita agendada!');
                        this.getHorario();
                    })
                    .catch((error) => {
                        this.loader = false;

                        if (error.response.status == 422) {
                            Swal.fire({
                                title: 'Editando cita',
                                html: error.response.data.message ?? 'Error al actualizar cita',
                                showCancelButton: false,
                                confirmButtonColor: '#67BC9A',
                                cancelButtonColor: '#dd4b39',
                                cancelButtonText: 'No',
                                confirmButtonText: 'Ok',
                            });
                        } else {
                            flash('Error al actualizar cita', 'danger');
                        }

                        // this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            } else {
                axios
                    .post(
                        '/beautician/patients/' +
            this.patient.id +
            '/agenda/treatment-appointments',
                        {
                            date: this.date,
                            start: this.date + ' ' + this.startTime,
                            end: this.date + ' ' + this.endTime,
                            user_id: this.currentAppointment.user_id,
                            patient_id: this.patient.id,
                            office_id: this.officeId,
                            room_id: this.currentAppointment.room_id,
                            optreatment_ids: this.estreatments.map(t => t.id),
                        }
                    )
                    .then(({ data }) => {
                        this.loader = false;

                        this.$emit('appointmentAdded', {
                            appointment: data,
                            tempId: this.currentAppointment.id,
                        });
                        this.currentAppointment.id = data.id;
                        flash('Cita agendada!');
                        this.getHorario();
                    })
                    .catch((error) => {
                        this.loader = false;

                        if (error.response.status == 422) {
                            Swal.fire({
                                title: 'Creacion de cita',
                                html: error.response.data.message ?? 'Error al guardar cita',
                                showCancelButton: false,
                                confirmButtonColor: '#67BC9A',
                                cancelButtonColor: '#dd4b39',
                                cancelButtonText: 'No',
                                confirmButtonText: 'Ok',
                            });
                        } else {
                            flash('Error al guardar cita', 'danger');
                        }

                        // this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            }
        },
    },
    created() {
        this.getHorario();
    },
};
</script>
<style scoped>
.w-full {
  width: 100%;
}

.evaluation-appointment {
  background-color: #f0ead2;
}

.edit-time-field select {
  -webkit-appearance: none !important;
  -moz-appearance: none !important;
  appearance: none !important;
}

.edit-time-field select option:disabled {
  cursor: not-allowed !important;
  cursor: -moz-not-allowed !important;
  cursor: -webkit-not-allowed !important;
  background-color: #dcdcdc !important;
}

.btn-current-appointment-time {
  background-color: white;
  border: 1px solid #dcdcdc;
  width: 100%;
  margin-bottom: 0.5rem;
  padding: 0.5rem;
}

.edit-time-field {
  display: flex;
  justify-items: center;
  align-items: center;
}
</style>