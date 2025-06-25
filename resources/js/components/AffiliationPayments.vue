<template>
    <div>
        <div class="payments-header">
            <h3>Abonos</h3>
            <div class="flex-container-sb">
                <div>
                    <button type="button" data-toggle="modal" data-target="#affiliationPaymentModal" class="btn btn-primary btn-sm" @click="showModalAffiliationPayment()">Agregar Abono</button>
                </div>


                <div> <b> Acumulado:</b> {{ moneyFormat(acumulado) }}</div>
            </div>

        </div>


        <div class="table-responsive">

            <table class="table">
                <thead>
                    <tr>
                        <th class="py-2 px-2 text-left">DETALLE</th>
                        <th class="py-2 px-2 text-left">FECHA</th>
                        <th class="py-2 px-2 text-left">MONTO</th>
                        <th class="py-2 px-2 text-left">FORMA PAGO</th>
                        <th class="py-2 px-2 text-left">NO. COMPROBANTE</th>
                        <th class="py-2 px-2 text-left"></th>

                    </tr>
                </thead>
                <tbody>


                    <tr class="border-t" v-for="(payment, index) in payments" :key="payment.id">
                        <td class="py-2 px-2">{{ payment.detail }}</td>
                        <td class="py-2 px-2">{{ payment.date }}</td>
                        <td class="py-2 px-2">{{ payment.amount }}</td>
                        <td class="py-2 px-2">{{ payment.payment_way }}</td>
                        <td class="py-2 px-2">{{ payment.code_transaction }}</td>

                        <td class="py-2 px-2">

                            <button type="button" class="btn btn-danger btn-sm" @click="removePayment(payment, index)">Quitar</button>


                        </td>

                    </tr>


                </tbody>
            </table>
            <paginator :dataSet="dataSet" @changed="fetch" :no-update-url="true"></paginator>
        </div>
        <affiliation-payment-modal :affiliation="affiliation" :medio-pagos="medioPagos" @created="createdPayment"></affiliation-payment-modal>
    </div>

</template>

<script>
import AffiliationPaymentModal from './AffiliationPaymentModal.vue';
export default {
    props: ['affiliation', 'medioPagos'],
    components: {
        AffiliationPaymentModal
    },
    data() {
        return {
            loader: false,
            payments: [],
            dataSet: false,
            acumulado: this.affiliation ? parseFloat(this.affiliation.acumulado) : 0
        };
    },
    methods: {
        createdPayment(payment) {
            this.acumulado += parseFloat(payment.amount);
            this.fetch();
        },
        moneyFormat(n) {

            if (typeof n === 'number') {

                return n.format(2);
            }
            if (typeof n === 'string') {

                return parseFloat(n).format(2);
            }

            return n;
        },
        fetch(page) {
            axios.get(this.url(page)).then(this.refresh);
        },
        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            let url = `/affiliations/${this.affiliation.id}/payments?page=${page}`;

            if (this.q) {
                url += `&q=${this.q}`;
            }


            return url;
        },
        refresh({ data }) {
            this.dataSet = data;
            this.payments = data.data;

        },
        showModalAffiliationPayment() {
            // debugger

            this.emitter.emit('showModalAffiliationPayment', '');

        },
        addPayment(payment) {
            console.log(payment);
            this.p;
        },
        removePayment(payment) {
            if (this.loader) { return; }

            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                this.loader = true;
                axios.delete(`/affiliations/${this.affiliation.id}/payments/${payment.id}`)
                    .then(() => {
                        this.loader = false;

                        this.$emit('deleted');
                        flash('Abono Eliminado Correctamente');
                        this.acumulado -= parseFloat(payment.amount);
                        this.fetch();
                    }).catch(() => {
                        this.loader = false;
                        flash('Error al eliminar', 'danger');
                    });
            }
        }

    },
    created() {

        if (this.affiliation.id) {

            this.fetch();
        }
    }
};
</script>


