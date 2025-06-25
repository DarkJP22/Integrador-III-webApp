<template>
  <div>
    <div class="modal fade" id="affiliationPaymentModal" tabindex="-1" role="dialog" aria-labelledby="affiliationPaymentModalLabel">
      <div class="modal-dialog " role="document">
        <div class="modal-content">
          <form action="#" @submit.prevent="save();" class="form-horizontal" autocomplete="off">
            <div class="modal-header">

              <h4 class="modal-title" id="mediaModalLabel">Abono</h4>
            </div>
            <div class="modal-body">

              <loading :show="loader"></loading>
              <div class="form-group">
                <label for="detail" class="col-sm-2 control-label">Detalle</label>

                <div class="col-sm-10">
                  <input type="text" class="form-control" name="detail" placeholder="" v-model="payment.detail">
                  <form-error v-if="errors.detail" :errors="errors" style="float:right;">
                    {{ errors.detail[0] }}
                  </form-error>
                </div>
              </div>
              <div class="form-group">
                <label for="date" class="col-sm-2 control-label">Fecha</label>

                <div class="col-sm-10">
                  <flat-pickr v-model="payment.date" class="form-control" placeholder="Selecione una fecha" name="date">
                  </flat-pickr>
                  <form-error v-if="errors.date" :errors="errors" style="float:right;">
                    {{ errors.date[0] }}
                  </form-error>
                </div>
              </div>
              <div class="form-group">
                <label for="amount" class="col-sm-2 control-label">Monto Abonado</label>

                <div class="col-sm-10">
                  <input type="text" class="form-control" name="amount" placeholder="" v-model="payment.amount">
                  <form-error v-if="errors.amount" :errors="errors" style="float:right;">
                    {{ errors.amount[0] }}
                  </form-error>
                </div>
              </div>
              <div class="form-group">
                <label for="payment_way" class="col-sm-2 control-label">Forma de pago</label>

                <div class="col-sm-10">
                  <!-- <input type="text" class="form-control" name="payment_way" placeholder="" v-model="payment.payment_way"> -->
                  <select class="form-control custom-select" name="payment_way" id="payment_way" v-model="payment.payment_way">

                    <option v-for="(value, key) in medioPagos" :value="value" :key="key">
                      {{ value }}
                    </option>

                  </select>
                  <form-error v-if="errors.payment_way" :errors="errors" style="float:right;">
                    {{ errors.payment_way[0] }}
                  </form-error>
                </div>
              </div>
              <div class="form-group">
                <label for="code_transaction" class="col-sm-2 control-label">No. Comprobante</label>

                <div class="col-sm-10">
                  <input type="text" class="form-control" name="code_transaction" placeholder="" v-model="payment.code_transaction">
                  <form-error v-if="errors.code_transaction" :errors="errors" style="float:right;">
                    {{ errors.code_transaction[0] }}
                  </form-error>
                </div>
              </div>






            </div>
            <div class="modal-footer">

              <button type="submit" class="btn btn-primary">Guardar</button>
              <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Cerrar</button>
            </div>
          </form>
        </div>
      </div>
    </div>


  </div>
</template>

<script>

import Loading from './Loading.vue';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {

    props: ['currentPayment', 'affiliation', 'medioPagos'],
    data() {
        return {
            payment: {
                detail: 'ABONO MENSUAL',
                date: moment().format('YYYY-MM-DD'),
                amount: this.affiliation ? this.affiliation.cuota : 0,
                payment_way: 'Efectivo'

            },
            loader: false,
            newPayment: false,
            errors: [],




        };
    },
    components: {
        Loading,
        flatPickr

    },
    methods: {

        nuevo() {
            this.newPayment = true;
            this.payment = {
                detail: 'ABONO MENSUAL',
                date: window.moment.now(),
                amount: this.affiliation ? this.affiliation.cuota : 0,
                payment_way: 'Efectivo'
            };
        },

        cancel() {

            this.newPayment = false;

        },

        edit(payment) {

            this.payment = payment;
            this.newPayment = false;

        },
        save() {

            if (this.loader) { return; }
            this.loader = true;
            if (this.payment.id) {

                axios.put('/affiliations/' + this.affiliation.id + '/payments/' + this.payment.id, this.payment)
                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.media = {};

                        flash('Abono Actualizado.');
                        this.$emit('updated', data);
                        $('#affiliationPaymentModal .btn-close').click();
                    })
                    .catch(error => {
                        this.loader = false;
                        flash(error.response.data.message, 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            } else {
                axios.post('/affiliations/' + this.affiliation.id + '/payments', this.payment)

                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.media = {};
                        flash('Abono Creado.');
                        this.$emit('created', data);
                        $('#affiliationPaymentModal .btn-close').click();
                    })
                    .catch(error => {
                        this.loader = false;
                        flash(error.response.data.message, 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            }

        }//save


    }, //methods
    created() {
        console.log('Component ready. office');

        this.emitter.on('editPayment', this.edit);
        this.emitter.on('cancelNewPayment', this.cancel);


        //     this.emitter.on('showMediaModal', (data) => {
        //        this.invoiceId = data;
        //        this.getInvoice()
        //        this.fetch()
        //    });


    }
};
</script>