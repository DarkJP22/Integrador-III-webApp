<template>
    <form class="form-horizontal" @submit.prevent="save">
        <div class="form-group">
            <label class="col-sm-2 control-label" for="category">Categoria</label>
            <div class="col-sm-10">
                <select v-model="form.category" class="form-control" name="category" required style="width: 100%;">

                    <option value="facial">
                        Facial</option>
                    <option value="corporal">
                        Corporal</option>
                    <option value="depilacion">
                        Depilación</option>

                </select>
                <form-error v-if="errors.category" :errors="errors" style="float:right;">
                    {{ errors.category[0] }}
                </form-error>


            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="product_id">Producto</label>

            <div class="col-sm-10">
                <v-select :filterable="false" :options="items" :value="selectedProduct" label="name" placeholder="Buscar Producto..." @input="selectItem" @search="onSearch">

                    <template slot="no-options">
                        Escribe para buscar los productos
                    </template>

                </v-select>
                <!-- <input type="hidden" class="form-control" name="product_id" value="{{ isset($treatment) ? $treatment->product_id : old('product_id') }}" v-model="selectedProductId"> -->
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Nombre</label>

            <div class="col-sm-10">
                <input v-model="form.name" class="form-control" name="name" placeholder="Nombre" required type="text">
                <form-error v-if="errors.name" :errors="errors" style="float:right;">
                    {{ errors.name[0] }}
                </form-error>

            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="price">Precio</label>

            <div class="col-sm-10">
                <input v-model="form.price" class="form-control" name="price" placeholder="" type="text">
                <form-error v-if="errors.price" :errors="errors" style="float:right;">
                    {{ errors.price[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="discount">% Descuento</label>

            <div class="col-sm-10">
                <input v-model="form.discount" class="form-control" name="discount" placeholder="%" type="text">
                <form-error v-if="errors.discount" :errors="errors" style="float:right;">
                    {{ errors.discount[0] }}
                </form-error>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="tax">% Impuesto</label>

            <div class="col-sm-10">
                <input v-model="form.tax" class="form-control" name="tax" placeholder="%" type="text">
                <form-error v-if="errors.tax" :errors="errors" style="float:right;">
                    {{ errors.tax[0] }}
                </form-error>
            </div>
        </div>

        <!-- <input type="hidden" class="form-control" name="CodigoMoneda" value="{{ isset($treatment) ? $treatment->CodigoMoneda : 'CRC' }}" v-model="productMoneda"> -->

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button class="btn btn-primary" type="submit">Guardar</button>
            </div>
        </div>
    </form>
</template>
<script>
import axios from 'axios';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
export default {
    props: {
        treatment: {
            type: [Object, Array],
        },
        product: {
            type: [Object, Boolean],
        },
    },
    components: {
        vSelect,
    },
    data() {
        return {
            items: [],
            selectedProductId: this.treatment.id ? this.treatment.product_id : null,
            selectedProduct: this.product ? this.product : null,
            form: {
                category: this.treatment.category ? this.treatment.category : '',
                name: this.treatment.name ? this.treatment.name : '',
                price: this.treatment.price ? this.treatment.price : 0,
                tax: this.treatment.tax ? this.treatment.tax : 0,
                discount: this.treatment.discount ? this.treatment.discount : 0,
                CodigoMoneda: 'CRC',
            },
            errors: [],
            loader: false
            // productName: this.treatment.id ? this.treatment.name : '',
            // productPrice: this.treatment.id ? this.treatment.price : 0,
            // productTax: this.treatment.id ? this.treatment.tax : 0,
            // productMoneda: 'CRC'
        };
    },
    methods: {
        clear(){
            this.form = {
                category: '',
                name: '',
                price: 0,
                tax: 0,
                discount: 0,
                CodigoMoneda: 'CRC',
            };
        },
        async save() {

            try {
                this.loader = true;
                if (this.treatment?.id) {
                    await axios.put('/clinic/esthetic/treatments/' + this.treatment.id, this.form);
                } else {
                    await axios.post('/clinic/esthetic/treatments', this.form);
                }
                this.loader = false;
                this.errors = [];
                this.clear();
                flash('Tratamiento guardado.');
                
                window.location.href = '/clinic/esthetic/treatments';

            } catch (error) {
                this.loader = false;
                flash('Error al guardar tratamiento', 'danger');
                this.errors = error.response.data.errors ? error.response.data.errors : [];
            }

        },
        onSearch(search, loading) {
            loading(true);
            this.search(loading, search, this);
        },
        search: _.debounce((loading, search, vm) => {
            const url = `/products?q=${search}`;

            axios.get(url).then((response) => {
                vm.items = response.data.data;
                loading(false);
            });
        }, 350),

        selectItem(item) {
            this.selectedProductId = null;
            this.selectedProduct = null;

            if (item) {

                this.selectedProduct = item;
                this.selectedProductId = item.id;
                this.form.name = item.name;
                this.form.price = item.price;
                this.form.CodigoMoneda = item.CodigoMoneda;
                this.form.tax = item.taxes[0]?.tarifa ?? 0;
                //this.$emit("selectedProduct", item);
            }
        },
    },
    created() { },
};
</script>
