<template>
    <div class="content">

        <sugar :patient="patient" @created="add" :items-length="items.length" :url="this.url"></sugar>

        <ul id="medicines-list" class="todo-list ui-sortable" v-show="items.length">

            <li v-for="(item, index) in items" :key="item.id">

                <span> Glicemia: <span class="text">{{ item.glicemia }}</span></span> <span v-if="item.condition">/ Condición: <span
                class="text">{{ item.condition }}</span></span>
                <br>
                <small>{{ item.date_control }} {{ item.time_control }}</small>

                <div class="tools" style="display:inline-block;">
                    <a href="#" class="badge bg-primary" title="Enviar Alerta a encargado"
                       @click="alertToEncargado(item)" v-if="alerts">Notificar</a>
                    <i class="fa fa-trash-o delete" title="Eliminar" @click="destroy(item, index)"></i>
                </div>
            </li>

        </ul>


    </div>
</template>
<script>
import collection from '../mixins/collection';
import Sugar from './Sugar.vue';

export default {
    props: ['sugars', 'patient', 'url', 'alerts'],
    data() {
        return {
            items: this.sugars,
            endpoint: this.url ? this.url + '/sugars' : '/sugars'

        };
    },
    components: {Sugar},
    mixins: [collection],
    methods: {

        alertToEncargado(item) {
            let html = `Enviar a encargado notificación de alerta de <b>Control de Azúcar</b> ${item.date_control} - ${item.time_control}: <b>Glicemia ${item.glicemia}. Paciente: ${this.patient.fullname}</b>.<br> <br>`;
            html += '<div class="checks-messages text-left">';

            html += '<label for="muy_alto"><input id="muy_alto" class="" type="radio" name="medicion" value="Muy alto. Dar seguimiento médico" checked> Muy alto. Dar seguimiento médico</label> <br>';
            html += '<label for="alto"><input id="alto" class="" type="radio" name="medicion" value="Alto"> Alto</label> <br>';
            html += '<label for="bajo"><input id="bajo" class="" type="radio" name="medicion" value="Bajo"> Bajo</label> <br>';
            html += '<label for="muy_bajo"><input id="muy_bajo" class="" type="radio" name="medicion" value="Muy bajo. Dar seguimiento médico"> Muy bajo. Dar seguimiento médico</label> <br>';


            html += '</div>';
            Swal.fire({
                title: 'Notificacion de alerta',
                html: html,
                // input: 'textarea',
                // inputPlaceholder: 'Algun mensaje?',
                showCancelButton: true,
                confirmButtonText: 'Enviar',
                confirmButtonColor: '#67BC9A',
                cancelButtonColor: '#dd4b39',
                showLoaderOnConfirm: true,
                inputValue: '',
                // inputValidator: (value) => {
                //     return new Promise((resolve) => {
                //         if (value === '') {
                //             resolve('Necesitas agregar al menos un correo')
                //         } else {
                //             resolve()
                //         }
                //     })
                // },
                preConfirm: (message) => {

                    const inputs = document.querySelectorAll('input[type=\'radio\']');

                    for (var i = 0; i < inputs.length; i++) {
                        if (inputs[i].checked == true) {
                            message = inputs[i].value;
                        }
                    }
                    let body = 'Alerta de Control de Azúcar ' + item.date_control + ' - ' + item.time_control + ': Glicemia ' + item.glicemia + '. Paciente: ' + this.patient.fullname + '.';

                    if (message) {
                        body = body + ' ' + message;
                    }


                    return axios.post('/patients/' + item.patient_id + '/alerts', {item: item, message: body})
                        .then(() => {
                        })
                        .catch(error => {

                            Swal.showValidationError(
                                `Request failed: ${error}`
                            );

                            flash('Error al enviar los notificacion!!', 'danger');
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()

            })
                .then((result) => {


                    if (result.value) {

                        Swal.fire({
                            title: 'Alerta Enviada Correctamente',

                        }).then(() => {


                        });

                    }


                });


        },

        destroy(item, index) {

            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                axios.delete(`${this.endpoint}/${item.id}`)
                    .then(() => {

                        this.remove(index);

                        flash('Glicemia Eliminada!');

                    }).catch(error => {

                        flash(error.response.data.message, 'danger');

                    });
            }

        },


    }

};
</script>
