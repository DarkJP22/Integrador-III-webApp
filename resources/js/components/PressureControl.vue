<template>
    <div class="content">

        <pressure :patient="patient" @created="add" :items-length="items.length" :url="this.url"></pressure>

        <ul id="medicines-list" class="todo-list ui-sortable" v-show="items.length">

            <li v-for="(item, index) in items" :key="item.id">
                <span> P.S: <span class="text">{{ item.ps }}</span> / P.D: <span class="text">{{
                        item.pd
                    }}</span> </span> <span v-if="item.heart_rate"> / F.C: <span class="text">{{
                    item.heart_rate
                }}</span></span> <span v-if="item.measurement_place">/ Lugar de medición: <span class="text">{{ item.measurement_place }}</span></span>

                <span v-if="item.observations">  <br> Observaciones: <span class="text">{{ item.observations }}</span></span>
                <br>
                <small>{{ item.date_control }} {{ item.time_control }}</small>

                <!-- General tools such as edit or delete-->
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
import Pressure from './Pressure.vue';

export default {
    props: ['pressures', 'patient', 'url', 'alerts'],
    data() {
        return {
            items: this.pressures,
            endpoint: this.url ? this.url + '/pressures' : '/pressures'
        };
    },
    components: {Pressure},
    mixins: [collection],
    methods: {
        alertToEncargado(item) {

            let html = `Enviar a encargado notificación de alerta de <b>Control de Presión</b> ${item.date_control} - ${item.time_control}: <b>P.S ${item.ps} / P.D ${item.pd}. Paciente: ${this.patient.fullname}</b>.<br> <br>`;
            html += '<div class="checks-messages text-left">';
            html += '<select name="levels" class="form-control">';
            html += '<option value="Muy alto. Dar seguimiento médico">Muy alto. Dar seguimiento médico</option>';
            html += '<option value="Alto">Alto</option>';
            html += '<option value="Bajo">Bajo</option>';
            html += '<option value="Muy bajo. Dar seguimiento médico">Muy bajo. Dar seguimiento médico</option>';
            html += '</select>';
            // html += `<label for="muy_alto"><input id="muy_alto" class="" type="radio" name="medicion" value="Muy alto. Dar seguimiento médico" checked> Muy alto. Dar seguimiento médico</label> <br>`;
            // html += `<label for="alto"><input id="alto" class="" type="radio" name="medicion" value="Alto"> Alto</label> <br>`;
            // html += `<label for="bajo"><input id="bajo" class="" type="radio" name="medicion" value="Bajo"> Bajo</label> <br>`;
            // html += `<label for="muy_bajo"><input id="muy_bajo" class="" type="radio" name="medicion" value="Muy bajo. Dar seguimiento médico"> Muy bajo. Dar seguimiento médico</label> <br>`;


            html += '</div>';
            Swal.fire({
                title: 'Notificacion de alerta',
                html: html,
                //input: 'textarea',
                //inputPlaceholder: 'Algun mensaje?',
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

                    let body = 'Alerta de Control de Presión ' + item.date_control + ' - ' + item.time_control + ': P.S ' + item.ps + '/ P.D ' + item.pd + '. Paciente: ' + this.patient.fullname + '.';

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

                        flash('Presion Eliminada!');

                    }).catch(error => {

                        flash(error.response.data.message, 'danger');

                    });
            }

        },


    }

};
</script>
