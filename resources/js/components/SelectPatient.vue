<template>
    <div class="form-group">
        <v-select v-model="selected" :filterable="false" :options="items" label="fullname" placeholder="Buscar Paciente..." @search="onSearch">

            <template slot="no-options">
                Escribe para buscar los pacientes
            </template>

        </v-select>


    </div>
</template>

<script>
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
export default {
    props: ['patient', 'url'],
    data() {
        return {
            items: [],
            selectedId: null,
            selected: null,
            endpoint: this.url ? this.url : '/patients'
        };
    },
    components: {
        vSelect
    },
    watch:{
        selected(val){
            this.$emit('selectedPatient', val);
        }
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

                    vm.items = response.data.data;
                    loading(false);

                });

        }, 350),


        selectItem(item) {

            this.selectedId = null;
            this.selected = null;

            if (item) {
                this.selected = item;
                this.selectedId = item.id;
                this.$emit('selectedPatient', item);
            }
        }

    },
    created() {
        if (this.patient && this.patient != 'null') {
            this.items.push(this.patient);
            this.selectItem(this.patient);
        }
        this.emitter.on('selectedPatientToSelect', this.selectItem);
    }
};
</script>