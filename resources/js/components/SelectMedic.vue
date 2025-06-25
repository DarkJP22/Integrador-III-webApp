<template>
    <div>
        <v-select v-model="selected" :disabled="disabled" :filterable="false" :options="items" label="name" placeholder="Buscar Médico..." @search="onSearch">

            <template slot="no-options">
                Escribe para buscar los médicos
            </template>

        </v-select>
        <input v-if="hiddenField" :name="hiddenField" :value="selectedId" type="hidden">
    </div>




</template>

<script>
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
export default {
    props: ['medic', 'url', 'disabled', 'hiddenField'],
    emits:['selectedMedic'],
    data() {
        return {
            items: [],
            selectedId: null,
            selected: null,
            endpoint: this.url ? this.url : '/medics'
        };
    },
    components: {
        vSelect
    },
    watch:{
        selected(val){
            this.$emit('selectedMedic', val);
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
           
            this.onClear();
           
            if (item) {
                this.selected = item;
                this.selectedId = item.id;
            }

            this.$emit('selectedMedic', item);
        },
        onClear() {
            console.log('onClear');
            this.selectedId = null;
            this.selected = null;
        }

    },
    created() {
        if (this.medic && this.medic != 'null') {
            this.items.push(this.medic);
            this.selectItem(this.medic);
        }
        this.emitter.on('clearSelectMedics', this.onClear);
    }
};
</script>
<style>
/* .v-select input[type="search"]{
    margin: 0 !important;
}
.v-select .dropdown-toggle{
    padding: 0;
}
.v-select .selected-tag{
    margin: 0px 2px 0;
} */
</style>