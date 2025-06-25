<template>
    <div>
        <span :class="'label label-'+ message.status">{{ message.body }}</span>
        <button class="btn btn-sm btn-success" @click="send()" v-show="show">Enviar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
    </div>

</template>
<script>

export default {
    props: ['invoiceId'],
    data() {
        return {

            invoice: {},
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

            if (!this.invoiceId) return;

            this.loader = true;
            this.show = false;

            axios.put('/invoices/' + this.invoiceId + '/sendhacienda')
                .then(({ data }) => {
                    this.loader = false;
                    this.errors = [];


                    if (data.sent_to_hacienda) {
                        flash('Factura enviada correctamente. Esperando respuesta por parte de hacienda.');

                        this.message.body = 'Esperando respuesta de hacienda';
                        this.message.status = 'warning';
                        this.show = false;
                    } else {
                        flash('Error al enviar Factura. No hubo conexion con hacienda', 'danger');

                        this.show = true;
                    }

                })
                .catch(error => {
                    this.loader = false;
                    this.show = true;
                    console.log(error.response);
                    if (error.response.status == 500 || error.response.status == 504) {


                        flash('La Factura No fue enviada, ha ocurrió un error. ' + error.response.data.message, 'danger');

                    } else if (error.response.status == 422) {
                        flash('Error al enviar Factura', 'danger');

                    } else {

                        flash(error.response.data.message, 'danger');
                    }

                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });




        }



    }
};
</script>