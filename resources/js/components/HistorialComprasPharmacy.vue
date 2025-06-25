<template>
    <div>
        <h4>Historial de compra de medicamentos</h4>



        <ul id="contact-list" class="todo-list ui-sortable">

            <li v-for="(item) in items" :key="item.id">

                <span>
                    <i class="fa fa-plus-square"></i>
                    <span class="text">
                        <span v-show="parseDecimal(item.TotalUnidades)">{{ parseDecimal(item.TotalUnidades) }}Unid</span> <span v-show="parseDecimal(item.TotalFracciones)">{{ parseDecimal(item.TotalFracciones) }}Frac</span> x {{ item.Detalle }} <br>
                        <small>{{ item.pharmacy }} - {{ item.created_at}}</small>
                    </span>
                </span>

                <div class="tools">

                    <!-- <i class="fa fa-edit" @click="edit(contact)"  title="Editar Contacto"></i>
           <i class="fa fa-trash-o delete" @click="destroy(contact, index)"  title="Eliminar Contacto"></i> -->
                </div>
            </li>

        </ul>

        <paginator :dataSet="dataSet" @changed="fetch" :no-update-url="true"></paginator>
    </div>
</template>

<script>


import collection from '../mixins/collection';
export default {
    props: ['patient', 'pharmacy'],
    components: {},
    mixins: [collection],
    data() {
        return {
            showForm: false,
            dataSet: false,


        };
    },
    created() {

        this.fetch();
    },
    methods: {
        parseDecimal(cant) {
            return parseFloat(cant);
        },
        fetch(page) {
            axios.get(this.url(page)).then(this.refresh);
        },
        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            let url = `/general/patients/${this.patient.id}/historialcompras`;

            url = `${url}?start=`;

            if (this.pharmacy) {
                url = `${url}&pharmacy=${this.pharmacy.id}`;
            }

            url = `${url}&page=${page}`;

            return url;
            //             axios.get('http://posfarmacia.test/api/customers/503600224/invoices?api_token=BdMgAzEwb1BeBU89OlBGTgcxbA9itUuVS4BszswUKoTUQMy3rZCtCM9Y4all&start=2019-08-19',{ headers: {

            //                  //"Accept": "application/json"
            //                 }
            // }).then((data) =>{
            //                 console.log(data);
            //             })
        },
        refresh({ data }) {
            console.log(data);
            this.dataSet = data;
            this.items = data.data;
            window.scrollTo(0, 0);
        },




    }
};
</script>
