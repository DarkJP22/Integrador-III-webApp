<template>
    <a class="btn btn-secondary btn-sm pull-right" href="#" title="Imprimir Agenda" @click.prevent="send()"><i class="fa fa-print"></i></a>
</template>

<script>
export default {
    props: ['url'],
    data() {
        return {
            loader: false,

        };
    },
    methods: {

        send() {
            // let message = "Selecciona la Fecha para imprimir la agenda";
            let html = 'Selecciona la Fecha para imprimir la agenda<br><br>';

            html += '<input id="dateAgenda" class="swal2-input" type="text" placeholder="Fecha de agenda" required />';
            Swal.fire({
                title: 'Imprimir',
                html: html,
                showCancelButton: true,
                confirmButtonColor: '#67BC9A',
                cancelButtonColor: '#dd4b39',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Imprimir',
                didOpen: function () {
                    $('#dateAgenda').datetimepicker({
                        format: 'YYYY-MM-DD',
                        locale: 'es',
                        defaultDate: new Date()
                    });
                },

            }).then((result) => {
                const date = document.getElementById('dateAgenda').value;

                if (result.value) {

                    window.location = `${this.url}&date=${date}`;

                } 




            });
        },




    },
    created() {

    }
};
</script>
<style>
.swal2-popup .swal2-html-container {
    z-index: 2 !important;
}
</style>

