<template>
    <div class="modal fade" id="taxesModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="taxesModalLabel">Impuestos</h4>
                </div>
                <div class="modal-body">
                    <loading :show="loader" position-css="absolute"></loading>

                    <div class="row">
                        <div class="col-sm-12">
                            <input type="search" class="form-control input-sm" v-model="q" @keyup.enter="search" placeholder="Buscar...">
                        </div>

                    </div>

                    <div class="table-responsive">


                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="py-2 px-2 text-left">CODIGO</th>
                                    <th class="py-2 px-2 text-left">NOMBRE</th>
                                    <th class="py-2 px-2 text-left">TARIFA</th>
                                    <th class="py-2 px-2 text-left">COD. TARIFA</th>
                                    <th class="py-2 px-2 text-left">IMPUESTO ACTUAL</th>

                                </tr>
                            </thead>
                            <tbody>


                                <tr class="border-t" v-for="tax in items" :key="tax.id">
                                    <td class="py-2 px-2">{{ tax.code }}</td>
                                    <td class="py-2 px-2">{{ tax.name }}</td>
                                    <td class="py-2 px-2">{{ tax.tarifa }}</td>
                                    <td class="py-2 px-2">{{ tax.CodigoTarifa }}</td>
                                    <td class="py-2 px-2">

                                        <button class="btn btn-danger btn-sm" @click="remove(tax)" v-if="isAssigned(tax)">Quitar</button>
                                        <button class="btn btn-primary  btn-sm" @click="assign(tax)" v-else>Agregar</button>

                                    </td>

                                </tr>


                            </tbody>
                        </table>
                    </div>
                    <paginator :dataSet="dataSet" @changed="fetch" :noUpdateUrl="true"></paginator>

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-link waves-effect btn-close-modal" data-dismiss="modal">CERRAR</button>
                </div>
            </div>


        </div>
    </div>

</template>

<script>
import collection from '../mixins/collection';
import Loading from './Loading.vue';

export default {
    props: ['show', 'tags'],
    data() {
        return {
            modal: this.show ? this.show : false,
            dataSet: false,
            dataLine: false,
            q: '',
            taxId: false,
            loader: false,

        };
    },
    components: {
        Loading
    },
    mixins: [collection],
    //  computed: {
    //     classes() {
    //         return ['btn', this.assigned ? 'btn-primary' : 'btn-default'];
    //     },
    //     name() {
    //         return this.assigned ? 'Quitar' : 'Agregar';
    //     }
    // },
    methods: {
        isAssigned(tax) {
            // debugger
            let result = false;

            if (this.dataLine.line.taxes.length) {
                result = _.find(this.dataLine.line.taxes, (taxInLine) => {
                    if (taxInLine.CodigoTarifa) {

                        return taxInLine.code === tax.code && taxInLine.CodigoTarifa === tax.CodigoTarifa;
                    } else {
                        return taxInLine.code === tax.code;
                    }
                });

            }
            return result;
        },

        assign(item) {
            console.log(item);
            this.$emit('assigned', { tax: item, dataLine: this.dataLine });
            //$(this.$el).find('.btn-close-modal').click();
        },
        remove(item) {
            console.log(item);
            this.$emit('remove', { tax: item, dataLine: this.dataLine });
            //$(this.$el).find('.btn-close-modal').click();
        },

        search() {

            this.fetch();

        },

        fetch(page) {
            axios.get(this.url(page))
                .then(this.refresh);
        },

        url(page) {
            let url = '/taxes';

            if (!page) {
                //let query = location.search.match(/page=(\d+)/);
                page = 1;//query ? query[1] : 1;
            }

            url = `/taxes?page=${page}`;

            if (this.q) {
                url += `&q=${this.q}`;
            }


            return url;
        },

        refresh({ data }) {


            this.dataSet = data;
            this.items = data.data;

        }

    },
    created() {

        //this.fetch()

        this.emitter.on('showTaxesModal', (dataLine) => {
            this.dataLine = dataLine;
            this.fetch();
        });


    }

};
</script>

