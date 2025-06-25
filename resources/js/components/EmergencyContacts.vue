<template>
    <div>
        <h4>Contactos de referencia <button class="btn btn-primary btn-sm btn-flat mb-1" @click="showForm = !showForm"><i class="fa fa-plus"></i> </button></h4>


        <new-contact @created="creado" @updated="actualizado" @canceled="cancel" v-show="showForm" :patient="patient"></new-contact>

        <ul id="contact-list" class="todo-list ui-sortable">

            <li v-for="(contact, index) in items" :key="contact.id">

                <a href="#" @click.prevent="edit(contact)">
                    <i class="fa fa-user"></i>
                    <span class="text">
                        {{ contact.name }} {{ contact.phone_number }}
                    </span>
                </a>

                <div class="tools">

                    <i class="fa fa-edit" @click="edit(contact)" title="Editar Contacto"></i>
                    <i class="fa fa-trash-o delete" @click="destroy(contact, index)" title="Eliminar Contacto"></i>
                </div>
            </li>

        </ul>

        <paginator :dataSet="dataSet" @changed="fetch" :no-update-url="true"></paginator>
    </div>
</template>

<script>

import NewContact from './NewContact.vue';
import collection from '../mixins/collection';
export default {
    props: ['patient'],
    components: { NewContact },
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
            return `/patients/${this.patient.id}/contacts?page=${page}`;
        },
        refresh({ data }) {
            this.dataSet = data;
            this.items = data.data;
            window.scrollTo(0, 0);
        },


        edit(contact) {
            this.showForm = true;
            this.emitter.emit('editContact', contact);

        },

        destroy(item, index) {
            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                axios.delete(`/emergency-contacts/${item.id}`)
                    .then(() => {

                        this.remove(index);

                        flash('Contacto Eliminado!');

                    }).catch(error => {

                        if (error.response.status == 422) {
                            flash(error.response.data.message, 'danger');
                        } else {

                            flash('Error al eliminar Contacto', 'danger');
                        }

                    });
            }

        },
    }
};
</script>
