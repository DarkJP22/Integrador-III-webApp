<template>
    <div>
        <div class="payments-header">
            <h3>Transacciones</h3>
            <div class="flex-container-sb">

            </div>

        </div>


        <div class="table-responsive">

            <table class="table">
                <thead>
                    <tr>

                        <th class="py-2 px-2 text-left">FECHA</th>
                        <th class="py-2 px-2 text-left">TIPO</th>
                        <th class="py-2 px-2 text-left">MONTO TRANSACCION</th>
                        <th class="py-2 px-2 text-left">ACUM. ANTES DE LA TRANS.</th>
                        <th class="py-2 px-2 text-left">ACUM. DESPUES DE LA TRANS.</th>
                        <th class="py-2 px-2 text-left"></th>

                    </tr>
                </thead>
                <tbody>


                    <tr class="border-t" v-for="(transaction) in transactions" :key="transaction.id">

                        <td class="py-2 px-2">{{ transaction.created_at }}</td>
                        <td class="py-2 px-2">{{ nameType(transaction.transactable_type) }} <br> <small> {{ transaction.transactable.NumeroConsecutivo }}</small> </td>
                        <td class="py-2 px-2">{{ moneyFormat(transaction.MontoTransaccion) }}</td>
                        <td class="py-2 px-2">{{ moneyFormat(transaction.AcumuladoAntesTransaccion) }}</td>
                        <td class="py-2 px-2">{{ moneyFormat(transaction.AcumuladoDespuesTransaccion) }}</td>

                        <td class="py-2 px-2">




                        </td>

                    </tr>


                </tbody>
            </table>
            <paginator :dataSet="dataSet" @changed="fetch" :no-update-url="true"></paginator>
        </div>

    </div>

</template>

<script>

export default {
    props: ['affiliation'],
    components: {

    },
    data() {
        return {
            loader: false,
            transactions: [],
            dataSet: false,

        };
    },
    methods: {

        nameType(n) {

            if (n) {
                return n.replace('App\\', '');
            }

            return n;


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
            let url = `/affiliations/${this.affiliation.id}/transactions?page=${page}`;

            if (this.q) {
                url += `&q=${this.q}`;
            }


            return url;
        },
        refresh({ data }) {
            this.dataSet = data;
            this.transactions = data.data;

        },



    },
    created() {

        if (this.affiliation.id) {

            this.fetch();
        }
    }
};
</script>


