<template>
    <div class="modal fade" id="contact-modal" role="dialog" aria-labelledby="contact-modal">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="contact-modal-label">Contacto - Soporte</h4>
                </div>
                <div class="modal-body">


                    <form class="form-horizontal push-10-t" action="#" method="post" @submit.prevent="send()" id="modal-contact-form">
                        <input type="hidden" name="user" id="modal-contact-user">

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input class="form-control" type="text" id="modal-contact-subject" v-model="subject" placeholder="Asunto">

                                    <span class="input-group-addon"><i class="fa fa-envelope-o"></i></span>
                                </div>
                                <form-error v-if="errors.subject" :errors="errors" style="float:right;">
                                    {{ errors.subject[0] }}
                                </form-error>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12">
                                <div class="">
                                    <textarea class="form-control" id="modal-contact-msg" v-model="message" rows="7" placeholder="Ingresa tu mensaje"></textarea>

                                </div>
                                <form-error v-if="errors.message" :errors="errors" style="float:right;">
                                    {{ errors.message[0] }}
                                </form-error>

                            </div>
                        </div>
                        <div class="form-group remove-margin-b">
                            <div class="col-xs-12">
                                <button class="btn btn-sm btn-primary modal-contact-btn-send" type="submit" :disabled="loader"><i class="fa fa-send push-5-r"></i> Enviar</button>
                                <span class="fa fa-cog fa-spin" v-show="loader"></span>
                            </div>
                        </div>
                    </form>


                </div>
                <div class="modal-footer">


                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            subject: '',
            message: '',
            errors: [],
            loader: false
        };
    },
    methods: {
        send() {

            if (this.loader) return;

            this.loader = true;

            axios.post('/support', {
                subject: this.subject,
                message: this.message

            })
                .then(() => {
                    this.loader = false;
                    this.errors = [];

                    this.subject = '';
                    this.message = '';


                    flash('Mensaje Enviado.');

                })
                .catch(error => {
                    this.loader = false;
                    flash('Error enviando la consulta', 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });
        }
    },
    created() {

        this.emitter.on('subjectEvent', (data) => {
            this.subject = data;
        });
        this.emitter.on('messageEvent', (data) => {
            this.message = data;
        });
    }
};
</script>

