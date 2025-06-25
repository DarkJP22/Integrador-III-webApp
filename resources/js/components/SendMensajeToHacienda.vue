<template>
    <div>
        <span :class="'label label-'+ message.status">{{ message.body }}</span>
        <button class="btn btn-sm btn-success" @click="send()" v-show="show">Enviar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
    </div>

</template>
<script>

export default {
    props: ['receptorId'],
    data() {
        return {

            receptor: {},
            loader: false,
            message: {
                body: 'No enviado',
                status: 'danger'
            },
            show: true



        };
    },

    methods: {

        send() {

            if (!this.receptorId) return;

            this.loader = true;
            this.show = false;

            axios.put('/receptor/mensajes/' + this.receptorId + '/sendhacienda')
                .then(({ data }) => {
                    this.loader = false;
                    this.errors = [];


                    if (data.sent_to_hacienda) {
                        flash('Mensaje de receptor enviado correctamente. Esperando respuesta por parte de hacienda.');

                        this.message.body = 'Esperando respuesta de hacienda';
                        this.message.status = 'warning';
                        this.show = false;
                    } else {
                        flash('Error al enviar Mensaje de receptor. No hubo conexion con hacienda', 'danger');

                        this.show = true;
                    }

                })
                .catch(error => {
                    this.loader = false;
                    this.show = true;
                    console.log(error.response);
                    if (error.response.status == 500 || error.response.status == 504) {


                        flash('El Mensaje de receptor No fue enviado. ' + error.response.data.message, 'danger');

                    } else if (error.response.status == 422) {
                        flash('Error al enviar Mensaje de receptor', 'danger');

                    } else {

                        flash(error.response.data.message, 'danger');
                    }

                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });




        }



    }
};
</script>