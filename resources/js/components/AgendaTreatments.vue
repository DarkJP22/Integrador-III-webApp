<template>
  <div>
    <loading :show="loader"></loading>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            
             <th>FECHA</th>
             <th>SALA</th>
             <th>ESTETICISTA</th>
             <th>HORA</th>
             <th>TRATAMIENTO</th>
             <th></th>
           
           
          </tr>
        </thead>
        <tbody>
          <tr class="no-items" v-if="!agenda.length">
            <td colspan="7">
              No hay tratamientos programados para este paciente
            </td>
          </tr>
          <agenda-treatments-appointment
            :appointment="appointment"
            :treatments="treatmentsComputed"
            :rooms="rooms"
            :patient="patient"
            :office-id="officeId"
            :esteticistas="esteticistas"
            @appointmentAdded="addAppointment"
            @appointmentDeleted="removeAppointment"
            @appointmentUpdated="updateAppointment"
            v-for="appointment in agenda"
            :key="appointment.id"
          ></agenda-treatments-appointment>
        </tbody>
      </table>
      <!-- <paginator
        :dataSet="dataSet"
        @changed="fetch"
        :noUpdateUrl="true"
      ></paginator> -->
      <button type="button" @click="newAppointment()">
        <i class="fa fa-plus">
          <span aria-hidden="true" class="sr-only">Add</span>
        </i>
      </button>
    </div>
  </div>
</template>

<script>
import AgendaTreatmentsAppointment from './AgendaTreatmentsAppointment.vue';
import Loading from './Loading.vue';
export default {
    props: {
        officeId: {
            type: Number,
            required: true,
        },
        patient: {
            type: Object,
            required: true,
        },
        appointments: {
            type: Array,
            require: true,
        },
        esteticistas: {
            type: Array,
            required: true,
        },
        optreatments: {
            type: Array,
            required: true,
        },
    },
    components: {
        Loading,
        AgendaTreatmentsAppointment,
    },
    data() {
        return {
            loader: false,
            rooms: [],
            agenda: this.appointments ?? [],
        };
    },
    computed: {
        treatmentsComputed() {
            return this.optreatments;
        },
    },
    methods: {
        newAppointment() {
            this.agenda.push({
                id: 'new' + (this.agenda.length + 1),
            });
        },
        removeAppointment(appointmentId) {
            this.agenda = this.agenda.filter((a) => a.id !== appointmentId);
        },
        addAppointment(data) {
            const idx = this.agenda.map((a) => a.id).indexOf(data.tempId);
            this.agenda[idx] = data.appointment;
        },
        updateAppointment(appointment) {
            const idx = this.agenda.map((a) => a.id).indexOf(appointment.id);
            this.agenda[idx] = appointment;
        },
        getRoomsOffice() {
            if (this.officeId) {
                axios.get('/offices/' + this.officeId + '/rooms').then(({ data }) => {
                    this.rooms = data;
                });
            }
        },
    },
    created() {
        this.getRoomsOffice();
    },
};
</script>