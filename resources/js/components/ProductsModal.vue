<template>
    <div class="modal fade" id="productsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="productsModalLabel">Servicios</h4>

                </div>
                <div class="modal-body">

                    <new-product @created="add" @updated="update" @canceled="showProductForm = false" v-show="showProductForm" :currencies="currencies"></new-product>

                    <div class="row">
                        <div class="col-sm-9">
                            <input type="search" class="form-control input-sm" v-model="q" @keyup.enter="search" placeholder="Buscar...">
                        </div>
                        <div class="col-sm-3">
                            <button type="button" class="btn btn-sm btn-secondary" @click="create()">Nuevo <i class="fa fa-plus"></i></button>
                        </div>

                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>CODIGO</th>
                                    <th>NOMBRE</th>
                                    <th>PRECIO</th>
                                    <th>LABORATORIO</th>
                                    <th>SERV. MÉDICO</th>
                                    <th>Referencia Comision</th>
                                    <th>N/A Comision</th>
                                    <th></th>


                                </tr>
                            </thead>
                            <tbody>


                                <tr v-for="(product, index) in items" :key="product.id">
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" @click="destroy(product, index)" title="Eliminar">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary" @click="edit(product)" title="Editar">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>{{ product.code }}</td>
                                    <td>{{ product.name }}</td>

                                    <td>{{ product.price }}</td>
                                    <td>
                                        <input class="form-check-input" type="checkbox" name="laboratory" id="laboratory" v-model="product.laboratory" @change="updateLaboratory(product, index)">

                                    </td>
                                    <td>
                                        <input class="form-check-input" type="checkbox" name="is_servicio_medico" id="laboratory" v-model="product.is_servicio_medico" @change="updateServicioMedico(product, index)">

                                    </td>
                                    <td>
                                        <input class="form-check-input" type="checkbox" name="reference_commission" id="reference_commission" v-model="product.reference_commission" @change="updateReferenceCommission(product, index)">

                                    </td>
                                    <td>
                                        <input class="form-check-input" type="checkbox" name="no_aplica_commission" id="no_aplica_commission" v-model="product.no_aplica_commission" @change="updateNoAplicaCommission(product, index)">

                                    </td>
                                    <td>
                                        <button class="btn btn-primary" @click="assign(product)">Agregar</button>
                                    </td>

                                </tr>


                            </tbody>
                        </table>
                        <paginator :dataSet="dataSet" @changed="fetch" :noUpdateUrl="true"></paginator>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CERRAR</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import collection from '../mixins/collection';
import NewProduct from './NewProduct.vue';

export default {
    props: ['currencies'],
    data() {
        return {
            dataSet: false,
            q: '',
            productId: false,
            officeId: false,
            showProductForm: false,
            taxes: []

        };
    },
    components: {
        NewProduct
    },
    mixins: [collection],

    methods: {

        assign(item) {
            console.log(item);
            this.$emit('assigned', item);
            flash('Servicio Agregado');
        },
        create() {
            this.showProductForm = true;
            this.emitter.emit('createProduct', this.officeId);
        },
        edit(item) {
            this.showProductForm = true;
            this.emitter.emit('editProduct', item);

        },

        updateLaboratory(item, index) {
            const prod = this.prepareTaxes(item);

            axios.put(`/products/${prod.id}`, prod)
                .then(response => {

                    this.update(response.data, index);



                }).catch(() => {

                    flash('Error al actualizar laboratory', 'danger');


                });
        },
        prepareTaxes(item) {

            //    let codigoTarifa = '04';

            //     if(item.is_servicio_medico){
            //         codigoTarifa = '04' //servicio medico
            //     }else{
            //         codigoTarifa = '08' // iva 13%    '03' => 2% productos medicos
            //     }

            //     let tax = _.find(this.taxes, { 'CodigoTarifa': codigoTarifa });

            //     let newtaxes = [];

            //     newtaxes.push(tax.id)

            //     item.taxes = newtaxes;

            const newtaxes = [];

            item.taxes.forEach(tax => {
                newtaxes.push(tax.id);
            });

            item.taxes = newtaxes;

            return item;
        },
        updateServicioMedico(item, index) {

            const prod = this.prepareTaxes(item);

            axios.put(`/products/${prod.id}`, prod)
                .then(response => {

                    this.update(response.data, index);



                }).catch(() => {

                    flash('Error al actualizar laboratory', 'danger');


                });
        },
        updateReferenceCommission(item, index) {

            const prod = this.prepareTaxes(item);

            axios.put(`/products/${prod.id}`, prod)
                .then(response => {

                    this.update(response.data, index);



                }).catch(() => {

                    flash('Error al actualizar referencia', 'danger');


                });
        },
        updateNoAplicaCommission(item, index) {

            const prod = this.prepareTaxes(item);

            axios.put(`/products/${prod.id}`, prod)
                .then(response => {

                    this.update(response.data, index);



                }).catch(() => {

                    flash('Error al actualizar N/A comision', 'danger');


                });
        },
        search() {

            this.fetch();

        },

        fetch(page) {
            axios.get(this.url(page))
                .then(this.refresh);
        },

        url(page) {
            let url = '/products';

            if (!page) {
                //let query = location.search.match(/page=(\d+)/);
                page = 1;//query ? query[1] : 1;
            }

            url = `/products?page=${page}`;

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
            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                axios.delete(`/products/${item.id}`)
                    .then(() => {

                        this.remove(index);

                        flash('Producto Eliminado!');

                    }).catch(() => {

                        flash('Error al guardar Producto', 'danger');


                    });
            }


        },
        fetchTaxes(page) {
            axios.get(`/taxes?page=${page}`).then(({ data }) => {

                this.taxes = data.data;
            });
        },



    },
    created() {

        //this.fetch()

        this.emitter.on('showProductsModal', (data) => {

            this.officeId = data;
            this.fetchTaxes();
            this.fetch();
        });


    }

};
</script>

