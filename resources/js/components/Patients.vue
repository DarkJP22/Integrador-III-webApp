<template>
    <div>
        <button class="btn btn-primary btn-flat mb-1" @click="showForm = !showForm">Crear Paciente</button>
        <button class="btn btn-secondary btn-flat mb-1" @click="cancel()" v-show="fromReservation">Regresar</button>
        <new-patient @created="add" @updated="update" v-show="showForm" @canceled="cancel" @selected="select" :from-user="1" :from-reservation="fromReservation"></new-patient>


        <ul id="patients-list" class="todo-list ui-sortable">

            <li v-for="(patient, index) in items" :key="patient.id">

                <a href="#" @click.prevent="edit(patient)">
                    <i class="fa fa-user"></i>
                    <span class="text">
                        {{ patient.first_name }}
                    </span>
                </a>

                <div class="tools">
                    <a href="#" @click="generateCode(patient)" title="Generar Código de autorización">Codigo de autorización</a>
                    <i class="fa fa-check" @click="select(patient)" title="Seleccionar Paciente" v-show="fromReservation"></i>
                    <i class="fa fa-edit" @click="edit(patient)" title="Editar Paciente"></i>
                    <i class="fa fa-trash-o delete" @click="destroy(patient, index)" title="Eliminar Paciente"></i>
                </div>
                <emergency-contacts :patient="patient"></emergency-contacts>
            </li>


        </ul>

        <paginator :dataSet="dataSet" @changed="fetch" :no-update-url="true"></paginator>
    </div>
</template>

<script>

import NewPatient from './NewPatient.vue';
import collection from '../mixins/collection';
export default {
    props: ['fromReservation'],
    components: { NewPatient },
    mixins: [collection],
    data() {
        return {
            showForm: false,
            dataSet: false

        };
    },
    created() {
        this.fetch();
    },
    methods: {
        cancel() {
            this.$emit('canceled');
        },
        fetch(page) {
            axios.get(this.url(page)).then(this.refresh);
        },
        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            return `/patients?page=${page}`;
        },
        refresh({ data }) {
            this.dataSet = data;
            this.items = data.data;
            window.scrollTo(0, 0);
        },
        select(patient) {
            this.$emit('selected', patient);
        },
        generateCode(patient) {

            axios.post(`/patients/${patient.id}/generateauth`)
                .then(() => {

                    this.emitter.emit('generatedCode', patient);

                    flash('Codigo Generado!');

                }).catch(error => {
                    if (error.response.status == 500) {

                        flash('Error al generar código. ' + error.response.data.message, 'danger');
                    } else {
                        flash('Error al generar código.', 'danger');
                    }

                });



        },

        edit(patient) {
            this.showForm = true;
            this.emitter.emit('editPatient', patient);

        },

        destroy(item, index) {
            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                axios.delete(`/patients/${item.id}`)
                    .then(() => {

                        this.remove(index);

                        flash('Paciente Eliminado!');

                    }).catch(error => {

                        if (error.response.status == 422) {
                            flash(error.response.data.message, 'danger');
                        } else {

                            flash('Error al eliminar Paciente', 'danger');
                        }

                    });
            }

        },
    }
};
</script>
