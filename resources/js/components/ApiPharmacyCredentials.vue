<template>
    <div>
        <h4>Accesos para historial de farmacia <button class="btn btn-primary btn-sm btn-flat mb-1" @click="crearAcceso()"><i class="fa fa-plus"></i> </button></h4>


        <new-credentials-pharmacy @created="creado" @updated="actualizado" @canceled="cancel" v-show="showForm" :patient="patient"></new-credentials-pharmacy>

        <ul id="contact-list" class="todo-list ui-sortable">

            <li v-for="(credential, index) in items" :key="credential.id">

                <a href="#" @click.prevent="edit(credential)">
                    <i class="fa fa-user"></i>
                    <span class="text">
                        {{ credential.name }} {{ credential.api_url }}
                    </span>
                </a>

                <div class="tools">

                    <i class="fa fa-edit" @click="edit(credential)" title="Editar Credenciales"></i>
                    <i class="fa fa-trash-o delete" @click="destroy(credential, index)" title="Eliminar Credenciales"></i>
                </div>
            </li>

        </ul>

        <paginator :dataSet="dataSet" @changed="fetch" :no-update-url="true"></paginator>
    </div>
</template>

<script>

import NewCredentialsPharmacy from './NewCredentialsPharmacy.vue';
import collection from '../mixins/collection';
export default {
    props: ['patient'],
    components: { NewCredentialsPharmacy },
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
        crearAcceso() {
            this.showForm = !this.showForm;
            this.emitter.emit('createCredential');

        },
        creado(item) {
            this.add(item);
            this.showForm = false;
        },
        actualizado(item) {
            this.update(item);
            this.showForm = false;
        },
        cancel() {
            this.$emit('canceled');
            this.showForm = false;
        },
        fetch(page) {
            axios.get(this.url(page)).then(this.refresh);
        },
        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            return `/patients/${this.patient.id}/apipharmacredentials?page=${page}`;
        },
        refresh({ data }) {
            this.dataSet = data;
            this.items = data.data;
            window.scrollTo(0, 0);
        },


        edit(credential) {
            this.showForm = true;
            this.emitter.emit('editCredential', credential);

        },

        destroy(item, index) {
            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                axios.delete(`/apipharmacredentials/${item.id}`)
                    .then(() => {

                        this.remove(index);

                        flash('Credencial  Eliminado!');

                    }).catch(error => {

                        if (error.response.status == 422) {
                            flash(error.response.data.message, 'danger');
                        } else {

                            flash('Error al eliminar Credencial', 'danger');
                        }

                    });
            }

        },
    }
};
</script>
