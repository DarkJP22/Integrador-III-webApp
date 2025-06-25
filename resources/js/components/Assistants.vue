<template>
    <div>
        <button class="btn btn-primary btn-flat mb-1" @click="showForm = !showForm">Agregar Secretaria</button>
        <new-assistant @created="add" @updated="update" v-show="showForm"></new-assistant>
        <h3>Tus Secretarias registradas</h3>
        <ul id="patients-list" class="todo-list ui-sortable">

            <li v-for="(assistant, index) in items" :key="assistant.id">

                <a href="#" @click.prevent="edit(assistant)">
                    <i class="fa fa-user"></i>
                    <span class="text">
                        {{ assistant.name }}
                    </span>
                </a>

                <div class="tools">
                    <i class="fa fa-edit" @click="edit(assistant)"></i>
                    <i class="fa fa-trash-o delete" @click="destroy(assistant, index)"></i>
                </div>
            </li>

        </ul>

        <paginator :dataSet="dataSet" @changed="fetch" :no-update-url="true"></paginator>
    </div>
</template>

<script>

import NewAssistant from './NewAssistant.vue';
import collection from '../mixins/collection';
export default {
    components: { NewAssistant },
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
        fetch(page) {
            axios.get(this.url(page)).then(this.refresh);
        },
        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            return `/assistants?page=${page}`;
        },
        refresh({ data }) {
            this.dataSet = data;
            this.items = data.data;
            window.scrollTo(0, 0);
        },

        edit(assistant) {
            this.showForm = true;
            this.emitter.emit('editAssistant', assistant);

        },

        destroy(item, index) {
            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                axios.delete(`/assistants/${item.id}`)
                    .then(() => {

                        this.remove(index);

                        flash('Asistente Eliminado!');

                    }).catch(error => {

                        flash(error.response.data.message, 'danger');

                    });
            }

        },
    }
};
</script>
