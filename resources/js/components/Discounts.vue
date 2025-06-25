<template>
    <div>
        <button class="btn btn-primary btn-flat mb-1" @click="showForm = !showForm">Agregar Descuento</button>
        <new-discount @created="add" @updated="update" v-show="showForm"></new-discount>
        <h3>Tus Descuentos registrados</h3>
        <ul id="patients-list" class="todo-list ui-sortable">

            <li v-for="(discount, index) in items" :key="discount.id">

                <a href="#" @click.prevent="edit(discount)">
                    <i class="fa fa-user"></i>
                    <span class="text">
                        {{ discount.name }} - {{ discount.tarifa }}
                    </span>
                </a>

                <div class="tools">
                    <i class="fa fa-edit" @click="edit(discount)"></i>
                    <i class="fa fa-trash-o delete" @click="destroy(discount, index)"></i>
                </div>
            </li>

        </ul>

        <paginator :dataSet="dataSet" @changed="fetch" :no-update-url="true"></paginator>
    </div>
</template>

<script>

import NewDiscount from './NewDiscount.vue';
import collection from '../mixins/collection';
export default {
    components: { NewDiscount },
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
            return `/discounts?page=${page}`;
        },
        refresh({ data }) {
            this.dataSet = data;
            this.items = data.data;
            window.scrollTo(0, 0);
        },

        edit(discount) {
            this.showForm = true;
            this.emitter.emit('edit', discount);

        },

        destroy(item, index) {
            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                axios.delete(`/discounts/${item.id}`)
                    .then(() => {

                        this.remove(index);

                        flash('Descuento Eliminado!');

                    }).catch(error => {

                        flash(error.response.data.message, 'danger');

                    });
            }

        },
    }
};
</script>
