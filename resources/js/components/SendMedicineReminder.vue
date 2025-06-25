<template>
    <div>

        <button :data-target="'#medicineReminderModal' + reminderId" class="btn btn-primary btn-sm" data-toggle="modal"
                type="button">ENVIAR NOTIFICACIÓN A PACIENTES
        </button>

        <div :id="'medicineReminderModal' + reminderId" aria-labelledby="medicineReminderModalLabel" class="modal fade" role="dialog"
             tabindex="-1">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span
                            aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Enviar Mensaje de Recordatorio</h4>
                    </div>
                    <div class="modal-body">

                        <div class="tw-mb-4">
                            <label for="">Mensaje Personalizado</label>
                            <textarea v-model="message" class="form-control" cols="30" required rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" @click="send">Enviar</button>
                        <button class="btn btn-default btn-close" data-dismiss="modal" type="button">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

    </div>


</template>
<script>
export default {
    props: ['reminderId', 'defaultMessage'],
    data() {
        return {
            message: this.defaultMessage,
            loader: false
        };
    },
    methods: {
        send() {


            axios.post('/pharmacy/medicines/reminders/' + this.reminderId + '/notifications', {
                body: this.message
            })
                .then(() => {
                    this.loader = false;
                    flash('Notificaciones Enviadas.');
                    //$(this.$el).parents('tr').hide();
                    $('#medicineReminderModal' + this.reminderId + ' .btn-close').click();
                    window.location.reload();

                })
                .catch(() => {
                    flash('Error al enviar notificación', 'danger');

                });


        }
    }
};
</script>
