<template>
    <div class="card">
        <div class="card-body ">
            <form @submit.prevent="addPayment()">

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="amount">Monto</label>

                        <input type="text" class="form-control" id="amount" placeholder="" v-model="amount">
                        <form-error v-if="errors.amount" :errors="errors" style="float:right;">
                            {{ errors.amount[0] }}
                        </form-error>

                    </div>
                    <div class="form-group col-md-4">
                        <label for="fecha_factura">Fecha</label>

                        <flat-pickr v-model="date" class="form-control" placeholder="" name="date">
                        </flat-pickr>
                    </div>
                    <!-- <div class="form-group col-md-4">
                    <label for="modoPago">Modo Pago</label>
                    
                    <input type="text" class="form-control" id="modoPago" placeholder="" v-model="modoPago">
                    <form-error v-if="errors.modoPago" :errors="errors" style="float:right;">
                        {{ errors.modoPago[0] }}
                    </form-error>    
                    
                </div> -->
                    <div class="form-group col-md-4">
                        <label for="comprobante">Comprobante</label>
                        <input type="text" class="form-control" name="comprobante" v-model="comprobante">
                        <form-error v-if="errors.comprobante" :errors="errors" style="float:right;">
                            {{ errors.comprobante[0] }}
                        </form-error>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>



                </div>
            </form>
        </div>
    </div>
</template>
<script>
import FormError from './FormError.vue';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
    props: ['invoice'],
    data() {
        return {
            amount: '',
            modoPago: '',
            date: '',
            comprobante: '',
            errors: [],
            configDate: {
                dateFormat: 'Y-m-d'
            },
        };
    },
    components: {
        FormError,
        flatPickr
    },

    methods: {

        addPayment() {
            axios.post(`/invoices/${this.invoice.id}/payments`, {
                amount: this.amount,
                modoPago: this.modoPago,
                comprobante: this.comprobante,
                date: this.date ? this.date : null,
            })
                .then(({ data }) => {
                    this.amount = '';
                    this.modoPago = '';
                    this.comprobante = '';
                    this.date = '';
                    flash('El Abono ha sido creado.');
                    this.$emit('created', data);
                })
                .catch(error => {
                    //flash(error.response.data.errors, 'danger');
                    this.errors = error.response.data ? error.response.data.errors : [];
                });
        },


    },

};
</script>

