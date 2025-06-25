<template>
  <div class="row">
    <div class="col-sm-12">
      <h2>Resumen de seguimiento</h2>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>FECHA</th>
              <th>TRATAMIENTO</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr class="no-items" v-if="!appointments.length">
              <td colspan="3">No hay citas programadas para este paciente</td>
            </tr>

            <tr v-for="app in appointments" :key="app.id" :class="app.id === appointment.id ? 'current-appointment' : ''">
              <td> <a :href="'/beautician/agenda/appointments/'+ app.id">{{ formatDateTime(app.start) }}</a></td>
              <td><a :href="'/beautician/agenda/appointments/'+ app.id">{{ app.optreatment ? app.optreatment.name : 'No asignado' }}</a></td>

              <td>
                <button type="button" class="btn btn-primary" @click="changeTreatment(app)" :disabled="app.id === appointment.id || app.date < appointment.date">{{ app.id === appointment.id ? 'Cita Actual' : 'Seleccionar'}}</button>
              </td>
            </tr>
          </tbody>
        </table>
        <!-- <paginator
        :dataSet="dataSet"
        @changed="fetch"
        :noUpdateUrl="true"
      ></paginator> -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
    props: ['patient', 'appointment', 'appointments'],
    data() {
        return {
            loader: false,
            loader_message: '',
        };
    },
    methods: {
        formatDateTime(date) {
            return moment(date).format('YYYY-MM-DD HH:mm');
        },
        changeTreatment(appointment) {
            this.loader = true;
            this.loader_message = 'Guardando...';

            axios
                .put('/appointments/' + this.appointment.id + '/treatments', {
                    optreatment_id: appointment.optreatment_id,
                })
                .then(() => {
                    this.loader_message = 'Cambios Guardados';
                    this.loader = false;
                    window.location.reload();
                })
                .catch(() => {
                    this.loader = false;
                    flash('Error al guardar cambios', 'danger');
                });
        },
    },
};
</script>
<style scoped>
.current-appointment {
  background-color: #F0EAD2;
}
</style>