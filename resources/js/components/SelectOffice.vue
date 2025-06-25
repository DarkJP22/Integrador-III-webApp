<template>

    <v-select v-model="selected" :filterable="false" :options="items" label="name" placeholder="Buscar Consultorio o clínica..." @search="onSearch">

        <template slot="no-options">
            Escribe para buscar los consultorios o clínicas privadas
        </template>

    </v-select>



</template>

<script>
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
export default {
    props: ['office', 'type'],
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
            this.$emit('selectedOffice', val);
        }
    },
    methods: {

        onSearch(search, loading) {
            loading(true);
            this.search(loading, search, this);
        },
        search: _.debounce((loading, search, vm) => {

            let url = `/offices?q=${search}`;

            if (vm.type) {
                url = `${url}&type=${vm.type}`;
            }

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
                this.$emit('selectedOffice', item);
            }
        }

    },
    created() {
        if (this.office && this.office != 'null') {
            this.items.push(this.office);
            this.selectItem(this.office);
        }
        this.emitter.on('officeToSelect', this.selectItem);

    }
};
</script>