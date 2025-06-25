<template>
    <div class="modal fade" id="customersModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="customersModalLabel">Pacientes</h4>
                    <a href="#" @click="nuevo()" class="">Crear un paciente?</a>
                </div>
                <div class="modal-body">
                    <div v-show="newPatient">
                        <!-- <new-patient @created="assign" @canceled="cancel"></new-patient> -->
                        <short-patient-form :endpoint="endpoint" @created="assign" @canceled="cancel"></short-patient-form>
                    </div>
                    <div v-show="!newPatient">
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="search" class="form-control input-sm" v-model="q" @keyup.enter="search" placeholder="Buscar...">
                            </div>

                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>

                                        <th>NOMBRE</th>
                                        <th>EMAIL</th>
                                        <th>IDENTIFICACIÓN</th>
                                        <th></th>


                                    </tr>
                                </thead>
                                <tbody>

                                    <tr class="no-items" v-if="!items.length">
                                        <td colspan="3">No existes elementos con esta busqueda..
                                        </td>
                                    </tr>
                                    <tr v-for="customer in items" :key="customer.id" @click="assign(customer)">

                                        <td>{{ customer.fullname }}</td>
                                        <td>{{ customer.email }}</td>
                                        <td>{{ customer.ide }}</td>
                                        <td>
                                            <button type="button" class="btn btn-primary">Agregar</button>
                                        </td>

                                    </tr>


                                </tbody>
                            </table>
                            <paginator :dataSet="dataSet" @changed="fetch" :noUpdateUrl="true"></paginator>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-link waves-effect btn-close-modal" data-dismiss="modal" @click="q = ''">CERRAR</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import collection from '../mixins/collection';
import ShortPatientForm from './ShortPatientForm.vue';

export default {
    props: ['tipoIdentificaciones', 'endpoint'],
    data() {
        return {
            dataSet: false,
            q: '',
            customerId: false,
            createCustomer: false,
            newPatient: false,

        };
    },
    components: {
        ShortPatientForm
    },
    mixins: [collection],

    methods: {
        nuevo() {
            this.newPatient = true;
            //this.paciente = {};
        },

        cancel() {
            //this.paciente = this.patient;
            this.newPatient = false;

        },
        agregar(item) {
            this.add(item);
            this.createCustomer = false;
            this.newPatient = false;
        },
        assign(item) {
            console.log(item);
            this.newPatient = false;
            this.$emit('assigned', item);
            $(this.$el).find('.btn-close-modal').click();
        },

        search() {

            this.fetch();

        },

        fetch(page) {
            axios.get(this.url(page))
                .then(this.refresh);
        },

        url(page) {
            let url = '/invoices/patients';

            if (!page) {
                //let query = location.search.match(/page=(\d+)/);
                page = 1;//query ? query[1] : 1;
            }

            url = `/invoices/patients?page=${page}`;

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

        this.emitter.on('showCustomersModal', (data) => {
            if (data && data.searchTerm) {
                this.q = data.searchTerm;
            }
            this.fetch();
        });


    }

};
</script>

