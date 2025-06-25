<template>
    <div class="modal fade" id="modalResumenFactura" role="dialog" aria-labelledby="modalResumenFactura">
        <div class="modal-dialog " role="document">
            <div class="modal-content">

                <div class="modal-header">

                    <h4 class="modal-title" id="modalRespHaciendaLabel">Resumen Factura. Total: {{ moneyFormat(invoice.TotalComprobante) }}</h4>

                </div>

                <div class="modal-body">
                    <loading :show="loader"></loading>

                    <div class="form-row">

                        <div class="form-group col-md-12">
                            <label for="MedioPago">Medio Pago</label>
                            <select class="form-control custom-select" v-model="invoice.MedioPago" :disabled="disableFields()" @change="updateMedioPago()">

                                <option v-for="(value, key, index) in medioPagos" :value="key" :key="index">
                                    {{ value }}
                                </option>

                            </select>
                        </div>

                    </div>
                    <div class="form-row">

                        <div class="form-group col-md-12" v-show="invoice.MedioPago == '01'">
                            <label for="pay_with">Pago Con</label>
                            <input type="text" class="form-control" placeholder="" v-model="invoice.pay_with" @blur="updatePagoCon()">
                        </div>

                    </div>


                    <div class="form-row">

                        <div class="col-md-12">
                            <h4>Total: {{ moneyFormat(invoice.TotalComprobante, '') }} {{ invoice.CodigoMoneda }}</h4>
                            <h6 v-show="invoice.MedioPago == '02'">IVA Devuelto: {{ moneyFormat(invoice.TotalIVADevuelto, '') }} {{ invoice.CodigoMoneda }} </h6>
                        </div>


                    </div>



                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-default btn-close-modal" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" @click="save()">Guardar</button>




                </div>
            </div>
        </div>
    </div>
</template>
<script>

import Loading from './Loading.vue';

export default {
    props: ['medioPagos', 'affiliation'],
    data() {
        return {
            invoice: {},
            loader: false,


        };
    },
    components: {
        Loading
    },
    methods: {

        moneyFormat(n) {

            if (typeof n === 'number') {

                return n.format(2);
            }

            return n;
        },
        disableFields() {

            return (this.invoice.id);
        },
        updatePagoCon() {
            this.$emit('recalculateInvoice');
        },
        updateMedioPago() {

            if (this.invoice.MedioPago == '02') {
                this.invoice.pay_with = 0;
                this.invoice.change = 0;
            }

            this.$emit('recalculateInvoice');
        },
        save() {
            this.$emit('saveResumenFactura');
            $(this.$el).find('.btn-close-modal').click();
        }

    },
    created() {
        this.emitter.on('showResumenFacturaModal', (data) => {

            this.invoice = data;

        });
    }
};
</script>
