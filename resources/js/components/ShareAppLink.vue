<template>
    <div class="">

        <button :data-target="'#shareAppModal' + patient.id" class="btn btn-secondary" data-toggle="modal" type="button"><i class="fa fa-address"></i> Compartir App
        </button>
        <div :id="'shareAppModal' + patient.id" aria-labelledby="shareAppModalLabel" class="modal fade" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                        <h4 id="sharelocationModalLabel" class="modal-title">Compartir Link App</h4>
                    </div>
                    <div class="modal-body">
                        <div class="tw-mb-4">
                            <label for="">Teléfono</label>
                            <input v-model="phone_number" class="form-control" placeholder="Número de teléfono" type="text">
                        </div>
                        <div class="tw-mb-4">
                            <label for="">Mensaje</label>
                            <textarea v-model="message" class="form-control" cols="30" required rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" @click="share">Compartir</button>
                        <button class="btn btn-default btn-close" data-dismiss="modal" type="button">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    props: {
        patient: {
            type: Object,
            required: true
        },
        defaultMessage: { type: String, default: 'Enfermarse puede suceder en cualquier momento... En Doctor Blue encuentras el médico, odontólogo o especialista que necesites, cerca de tu casa. https://mobile.cittacr.com'}
    },
    data() {
        return {
            phone_number: this.patient.phone_number || '',
            message: this.defaultMessage
        };
    },
    computed: {
        phoneNumber() {
            if (!this.phone_number) return false;

            return this.phone_number.startsWith('+506')
                ? this.phone_number
                : '+506' + this.phone_number;
        },
    },
    methods: {
        share() {
            if (this.phoneNumber && this.message) {

                const data = {
                    phone_number: this.phoneNumber,
                    message: this.message,
                };

                axios.post('/general/patients/share-app-link', data).then(() => {

                    this.emitter.emit('sharedLink');

                    flash('Link Compartido!');

                    $('#shareAppModal' + this.patient.id + ' .btn-close').click();
                }).catch((error) => {
                    console.log(error);
                    Swal.fire('Error al compartir link', 'Error al compartir link', 'error');
                });


            }

        }
    }
};
</script>