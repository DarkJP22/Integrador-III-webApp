<template>
    <div class="modal fade" id="paymentsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="paymentsModalLabel">Abonos de la factura {{ invoice.consecutivo }}</h4>
                </div>
                <div class="modal-body">

                    <new-payment @created="add" :invoice="invoice"></new-payment>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>FECHA</th>
                                    <th>MONTO</th>
                                    <!-- <th>MODO PAGO</th> -->
                                    <th>COMPROBANTE</th>
                                    <th></th>


                                </tr>
                            </thead>
                            <tbody>


                                <tr v-for="(payment, index) in items" :key="payment.id">

                                    <td>{{ payment.date }}</td>
                                    <td>{{ moneyFormat(payment.amount) }}</td>
                                    <!-- <td>{{ payment.modoPago }}</td> -->
                                    <td>{{ payment.comprobante }}</td>
                                    <td>
                                        <button class="btn btn-danger" @click="destroy(payment, index)">Eliminar</button>
                                    </td>

                                </tr>
                                <tr>
                                    <td>Monto Cuenta</td>
                                    <td colspan="4"> {{ moneyFormat(parseFloat(invoice.TotalComprobante)) }}</td>

                                </tr>
                                <tr class="table-success">
                                    <td>Total Abonos</td>
                                    <td colspan="4"> {{ moneyFormat(total()) }}</td>

                                </tr>
                                <tr class="table-danger">
                                    <td>Pendiente</td>
                                    <td colspan="4"> {{ moneyFormat(pending()) }}</td>

                                </tr>


                            </tbody>
                        </table>
                        <paginator :dataSet="dataSet" @changed="fetch" :noUpdateUrl="true"></paginator>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-link waves-effect" @click="print()">IMPRIMIR</button> -->
                    <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import NewPayment from './NewPayment.vue';
import collection from '../mixins/collection';


export default {

    data() {
        return {
            dataSet: false,
            q: '',
            invoiceId: false,
            invoice: false


        };
    },
    components: {
        NewPayment
    },
    mixins: [collection],

    methods: {

        total() {
            return _.sumBy(this.items, function (o) { return o.amount; });
        },
        pending() {
            return parseFloat(this.invoice.TotalComprobante) - this.total();
        },
        moneyFormat(n) {

            if (typeof n === 'number') {

                return '₡' + n.format(2);
            }

            return '₡' + n;
        },

        print() {
            //window.open(`/cxc/${this.invoiceId}/print`,'_blank');
            window.location = `/cxc/${this.invoiceId}/print`;
        },

        fetch(page) {
            axios.get(this.url(page))
                .then(this.refresh);
        },

        url(page) {
            let url = `/invoices/${this.invoiceId}/payments`;

            if (!page) {
                //let query = location.search.match(/page=(\d+)/);
                page = 1;//query ? query[1] : 1;
            }

            url = `${url}?page=${page}`;

            if (this.q) {
                url += `&q=${this.q}`;
            }


            return url;
        },

        refresh({ data }) {


            this.dataSet = data;
            this.items = data.data;

        },
        destroy(item, index) {
            axios.delete(`/payments/${item.id}`);
            this.remove(index);
        },
        getInvoice() {
            axios.get(`/invoices/${this.invoiceId}`)
                .then(({ data }) => {
                    this.invoice = data;
                });
        }

    },
    created() {

        this.emitter.on('showPaymentsModal', (data) => {
            this.invoiceId = data;
            this.getInvoice();
            this.fetch();
        });


    }

};
</script>

