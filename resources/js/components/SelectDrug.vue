<template>

    <v-select v-model="selected" :filterable="false" :options="items" label="name" placeholder="Buscar Medicamento..." @search="onSearch">

        <template v-slot:no-options="{ search, searching }">
            <template v-if="searching">
                No hay resultados para <em>{{ search }}</em> ¿Desear Crearlo? <br /> <button type="button" class="btn btn-primary" @click="$emit('createDrug')">Crear Medicamento</button>.
            </template>
            <em v-else style="opacity: 0.5">Escribe para buscar los medicamentos.</em>
        </template>

    </v-select>



</template>

<script>
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
export default {
    props: [],
    emits: ['selectedDrug', 'createDrug'],
    data() {
        return {
            items: [],
            selectedId: null,
            selected: null
        };
    },
    components: {
        vSelect
    },
    watch:{
        selected(val){
            this.$emit('selectedDrug', val);
        }
    },
    methods: {

        onSearch(search, loading) {
            loading(true);
            this.search(loading, search, this);
        },
        search: _.debounce((loading, search, vm) => {

            let url = `/drugs?q=${search}`;

            if (vm.type) {
                url = `${url}`;
            }

            axios.get(url)
                .then(response => {

                    vm.items = response.data.data;
                    loading(false);

                });

        }, 350),


    },
    created() {



    }
};
</script>