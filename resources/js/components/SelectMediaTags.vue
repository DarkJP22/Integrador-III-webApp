<template>
    <div>
        <v-select :options="mediaTags" placeholder="Buscar Padecimiento..." label="name" multiple v-model="selected">

            <template slot="no-options">
                Escribe para buscar los padecimientos
            </template>
        </v-select>
        <input type="hidden" v-if="selected && !selected.length" name="conditions">
        <template  v-else>
            <input type="hidden" name="conditions[]" v-for="(item, index) in selected" :key="index" :value="item">
        </template>
    </div>


</template>

<script>
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

export default {
    props: ['tags', 'url', 'patientConditions'],
    data() {
        return {
            mediaTags: this.tags,
            items: [],
            selected: null,
            endpoint: this.url ? this.url : '/mediatags/tags'
        };
    },
    components: {
        vSelect
    },

    methods: {

        onSearch(search, loading) {
            loading(true);
            this.search(loading, search, this);
        },
        search: _.debounce((loading, search, vm) => {

            const url = `${vm.endpoint}?q=${search}`;

            axios.get(url)
                .then(response => {

                    vm.items = response.data;
                    loading(false);

                });

        }, 350),


        selectItem(item) {


            this.selected = null;

            if (item) {
                this.selected = item;


            }
        }

    },
    created() {
        if (this.patientConditions) {
            // this.items.push(this.conditions)
            this.selectItem(this.patientConditions);
        }
        // this.emitter.on('selectedPatientToSelect', this.selectItem)
    }
};
</script>