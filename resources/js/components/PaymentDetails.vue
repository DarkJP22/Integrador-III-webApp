<template>





    <div class="table-responsive">
        <table class="table no-margin">
            <thead>
                <tr>

                    <th>Consultas Atendidas</th>
                    <th>Monto por Citas Online</th>
                    <th>Periodo</th>
                    <th>Total</th>



                </tr>
            </thead>
            <tbody>
                <tr>

                    <td>{{ data.attented }}</td>
                    <td>${{ money(data.amountByAttended) }}</td>
                    <td>{{ data.month }}</td>
                    <td>${{ money(parseFloat(data.attented_amount)) }}</td>


                </tr>


            </tbody>
        </table>
    </div>









</template>

<script>


export default {
    props: ['incomeId'],
    data() {
        return {


            data: {
                attented: 0,
                amountByAttended: 0,
                attented_amount: 0,
                month: '',

            },
            loader: false,


        };
    },


    methods: {
        money(n) {

            if (n)
                return n.toLocaleString();//toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")

            return 0;
        },


        getPaymentDetail() {


            this.data = [];




            this.loader = true;

            axios.get('/medic/payments/' + this.incomeId + '/details')
                .then(response => {

                    this.data = response.data;
                    this.loader = false;

                }).catch(() => {

                    flash('Error al buscar los detalles de pago', 'danger');

                });





        }

    },
    created() {
        this.getPaymentDetail();

        console.log('Component ready. payment details');



    }
};
</script>