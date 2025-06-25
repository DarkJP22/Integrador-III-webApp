<template>
    <div>

        <ul id="patients-list" class="todo-list ui-sortable">

            <li v-for="(patient) in items" :key="patient.id">

                <a href="#">
                    <i class="fa fa-user"></i>
                    <span class="text">
                        {{ patient.fullname }}
                    </span>
                </a>
                <ul>
                    <li v-for="(authorization, index) in patient.authorizations" :key="index">

                        <a href="#">
                            <i class="fa fa-user-md"></i>
                            <span class="text">
                                {{ authorization.name }}
                            </span>
                        </a>

                        <div class="tools">

                            <i class="fa fa-trash-o delete" @click="destroy(patient, authorization, index)" title="Eliminar Autorización"></i>
                        </div>

                    </li>
                </ul>



            </li>


        </ul>

        <paginator :dataSet="dataSet" @changed="fetch" :no-update-url="true"></paginator>
    </div>
</template>

<script>

import collection from '../mixins/collection';
export default {
    //props:[''],
    components: {},
    mixins: [collection],
    data() {
        return {

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
            return `/authorizations?page=${page}`;
        },
        refresh({ data }) {
            this.dataSet = data;
            this.items = data.data;
            window.scrollTo(0, 0);
        },

        destroy(patient, item, index) {
            const r = confirm('¿Deseas Eliminar la autorización de este médico?');

            if (r == true) {
                axios.delete(`/patients/${patient.id}/authorizations/${item.id}`)
                    .then(() => {

                        patient.authorizations.splice(index, 1);

                        flash('Autorización Eliminada!');

                    }).catch(error => {

                        if (error.response.status == 422) {
                            flash(error.response.data.message, 'danger');
                        } else {

                            flash('Error al eliminar Autorización', 'danger');
                        }

                    });
            }

        },
    }
};
</script>
