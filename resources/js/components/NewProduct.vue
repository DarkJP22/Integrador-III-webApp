<template>
    <form class="form-horizontal" @submit.prevent="save()">
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">Tipo</label>

            <div class="col-sm-10">
                <select class="form-control " name="type" v-model="product.type" required>

                    <option value="S"> Servicio</option>
                    <option value="P"> Producto o Mercancia</option>

                </select>
                <form-error v-if="errors.type" :errors="errors" style="float:right;">
                    {{ errors.type[0] }}
                </form-error>
            </div>
        </div>

        <div class="form-group">
            <label for="CodigoProductoHacienda" class="col-sm-2 control-label">Código CABYS*</label>
            <div class="col-sm-10">
                <div class="input-group input-group">
                    <!-- <input type="text" class="form-control" name="cliente" v-model="invoice.cliente" @keyup="listenCliente($event)" @keydown.prevent.enter="searchCustomer(invoice.cliente)" > -->
                    <input type="text" class="form-control" id="CodigoProductoHacienda" name="CodigoProductoHacienda" placeholder="" v-model="product.CodigoProductoHacienda">
                    <div class="input-group-btn">
                        <button class="btn btn-outline-secondary" type="button" @click="showBuscadorCabys = !showBuscadorCabys"><svg v-show="!showBuscadorCabys" class="w-6 h-6" style="width:16px; height:16px;" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                            <svg v-show="showBuscadorCabys" class="w-6 h-6" style="width:16px; height:16px;" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <form-error v-if="errors.CodigoProductoHacienda" :errors="errors" style="float:right;">
                {{ errors.CodigoProductoHacienda[0] }}
            </form-error>

            <div class="col-sm-2 control-label"></div>
            <div class="card bg-light col-sm-10" v-show="showBuscadorCabys">
                <div class="card-body" style="margin-top: 1rem;">
                    <input type="text" class="form-control" name="q" id="inputCabys" placeholder="Buscar por código o nombre..." v-model="searchCabys" @keydown.prevent.enter="searchCodigoCabys">

                    <div v-show="loader" class="spinner-border text-primary mt-3" role="status">
                        <img src="/img/loading.gif" alt="Cargando...">
                    </div>

                    <div style="max-height: 300px; overflow-y:scroll" class="mt-3" v-show="!loader">
                        <ul class="list-group ">
                            <li class="list-group-item d-flex justify-content-between align-items-center" v-for="item in cabysProducts" :key="item.codigo" style="display:flex; justify-content: space-between; align-items:center">
                                <div>
                                    <div class="font-weight-bold" @click="seleccionarCodigoCabys(item);">
                                        {{ item.codigo }}
                                    </div>

                                    <div class="text-muted">
                                        {{ item.descripcion }}
                                    </div>
                                    <div class="label label-warning">
                                        Impuesto: %{{ item.impuesto }}
                                    </div>
                                </div>

                                <button type="button" class="btn btn-sm btn-primary" @click="seleccionarCodigoCabys(item);">Seleccionar</button>
                            </li>

                        </ul>
                        <div v-if="noResults">No hay artículos</div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-outline-dark" @click="showBuscadorCabys = false;">Cerrar</button>
                    </div>
                </div>

            </div>

        </div>


        <div class="form-group">
            <label for="code" class="col-sm-2 control-label">Código</label>

            <div class="col-sm-10">
                <input type="text" class="form-control" name="code" v-model="product.code">
                <form-error v-if="errors.code" :errors="errors" style="float:right;">
                    {{ errors.code[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Nombre</label>

            <div class="col-sm-10">
                <input type="text" class="form-control" name="name" v-model="product.name" required>
                <form-error v-if="errors.name" :errors="errors" style="float:right;">
                    {{ errors.name[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label for="description" class="col-sm-2 control-label">Descripción</label>

            <div class="col-sm-10">
                <textarea name="description" class="form-control" cols="30" rows="2" v-model="product.description"></textarea>
                <form-error v-if="errors.description" :errors="errors" style="float:right;">
                    {{ errors.description[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label for="CodigoMoneda" class="col-sm-2 control-label">Moneda</label>
            <div class="col-sm-10">
                <select class="form-control" name="CodigoMoneda" v-model="product.CodigoMoneda">
                    <option v-for="(item, index) in currencies" :value="item.code" :key="index">
                        {{ item.code }}
                    </option>

                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="price" class="col-sm-2 control-label">Precio</label>

            <div class="col-sm-10">
                <input type="text" class="form-control" name="price" v-model="product.price" required>
                <form-error v-if="errors.price" :errors="errors" style="float:right;">
                    {{ errors.price[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label for="laboratory" class="col-sm-2 control-label">Servicio</label>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="laboratory" id="laboratory" v-model="product.laboratory">
                <label class="form-check-label" for="laboratory" style="margin-right: 1rem;">
                    Laboratorio
                </label>
                <input class="form-check-input" type="checkbox" name="is_servicio_medico" id="is_servicio_medico" v-model="product.is_servicio_medico">
                <label class="form-check-label" for="is_servicio_medico">
                    Servicio Médico
                </label>
            </div>
            <div class="form-check">

            </div>


        </div>
        <div class="form-group">
            <label for="taxes" class="col-sm-2 control-label">Impuestos</label>
            <div class="col-sm-10">
                <div class="form-check" v-for="tax in taxes" :key="tax.id">
                    <input class="form-check-input" type="checkbox" :value="tax.id" :id="'tax-'+ tax.id" name="taxes[]" v-model="product.taxes" @change="calculatePrice()">
                    <label class="form-check-label" :for="'tax-'+ tax.id">
                        {{ tax.name }} ({{ tax.tarifa }}%)
                    </label>
                </div>
            </div>


        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" @click="cancel()"> {{ fromBlade ? 'Regresar': 'Cancelar' }}</button>
                <img src="/img/loading.gif" alt="Cargando..." v-show="loader" />
            </div>
        </div>
    </form>
</template>
<script>

export default {
    props: {
        currencies: Array,
        fromBlade:{ type: Boolean, default: false},
        backUrl:{ type: String, default: ''}
    },
    data() {
        return {

            loader: false,
            product: {
                type: 'S',
                measure: 'Unid',
                code: '',
                name: '',
                description: '',
                CodigoMoneda: 'CRC',
                quantity: 1,
                laboratory: 0,
                price: 0,
                office_id: 0,
                user_id: window.App.user.id,
                taxes: [],
                CodigoProductoHacienda: ''

            },
            officeId: 0,
            dataSet: false,
            taxes: [],
            errors: [],
            showBuscadorCabys: false,
            searchCabys: '',
            cabysProducts: [],
            noResults: false,

        };
    },

    methods: {
        seleccionarCodigoCabys(item) {
            this.product.CodigoProductoHacienda = item.codigo;
            this.showBuscadorCabys = false;
            this.cabysProducts = [];
            this.searchCabys = '';


            const tax = _.find(this.taxes, { 'tarifa': parseFloat(item.impuesto) });

            if (tax) {
                this.product.taxes = [];
                this.product.taxes.push(tax.id);
            }

        },
        searchCodigoCabys() {
            if (this.loader) {
                return;
            }
            if (!this.searchCabys) {
                return;
            }
            this.loader = true;
            this.noResults = false;

            const isCode = /^\d+$/.test(this.searchCabys);

            const queryParam = isCode ? 'codigo' : 'q';

            axios.get('https://api.hacienda.go.cr/fe/cabys?' + queryParam + '=' + this.searchCabys)
                .then(({ data }) => {

                    this.loader = false;
                    this.cabysProducts = queryParam == 'codigo' ? data : data.cabys;
                    this.noResults = !this.cabysProducts.length ? true : false;
                });
        },
        calculatePrice() {

            const price = parseFloat(this.product.price);
            let taxAmount = 0;

            this.product.taxes.forEach(taxId => {

                const tax = _.find(this.taxes, { 'id': parseInt(taxId) });

                if (tax) {

                    taxAmount += price * (parseFloat(tax.tarifa) / 100);
                }
            });

            this.product.price = price;
            this.product.priceWithTaxes = price + taxAmount;


        },
        cancel() {
            this.clear();
            this.$emit('canceled');
            if(this.fromBlade){
                window.location.href = this.backUrl;
            }

        },
        clear() {
            this.product = {
                type: 'S',
                code: '',
                name: '',
                description: '',
                CodigoMoneda: 'CRC',
                measure: 'Unid',
                quantity: 1,
                laboratory: 0,
                is_servicio_medico: 1,
                price: 0,
                office_id: this.officeId,
                user_id: window.App.user.id,
                taxes: [],
                CodigoProductoHacienda: '',


            };
        },
        save() {

            this.product.measure = (this.product.type == 'S') ? 'Sp' : 'Unid';
            this.product.is_servicio_medico = (this.product.type == 'S') ? this.product.is_servicio_medico : 0;

            if (!this.product.id) {

                axios.post('/products', this.product)

                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clear();
                        flash('Producto Creado.');
                        this.$emit('created', data);
                        if(this.fromBlade){
                            window.location.href = this.backUrl;
                        }
                    })
                    .catch(error => {
                        flash('Error al guardar Producto', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            } else {

                axios.put('/products/' + this.product.id, this.product)
                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clear();
                        flash('Producto Actualizado.');
                        this.$emit('updated', data);
                        if(this.fromBlade){
                            window.location.href = this.backUrl;
                        }
                    })
                    .catch(error => {

                        flash('Error al actualizar Producto', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            }

        },
        fill(product) {

            this.product.id = product.id;
            this.product.type = product.type;
            this.product.code = product.code;
            this.product.name = product.name;
            this.product.description = product.description;
            this.product.quantity = product.quantity;
            this.product.price = product.price;
            this.product.priceWithTaxes = product.priceWithTaxes;
            this.product.taxesAmount = product.taxesAmount;
            this.product.exo = product.exo;
            this.product.laboratory = product.laboratory;
            this.product.is_servicio_medico = product.is_servicio_medico;
            this.product.measure = product.measure;
            this.product.user_id = product.user_id;
            this.product.office_id = product.office_id;
            this.product.taxes = _.map(product.taxes, 'id');
            this.product.CodigoMoneda = product.CodigoMoneda;
            this.product.CodigoProductoHacienda = product.CodigoProductoHacienda;
        },
        fetchTaxes(page) {
            axios.get(this.url(page)).then(this.refresh);
        },

        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            return `/taxes?page=${page}`;
        },

        refresh({ data }) {
            this.dataSet = data;
            this.taxes = data.data;


        }


    },

    created() {
        this.fetchTaxes();
        this.emitter.on('editProduct', this.fill);
        this.emitter.on('createProduct', (officeId) => {
            this.officeId = officeId;
            this.product.office_id = officeId;
        });



    }

};
</script>

