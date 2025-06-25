<template>
  <div class="row">
    <div class="col-sm-12">
      <h2>Resumen facturación</h2>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>TRATAMIENTO</th>
              <th>PRECIO</th>
              <th>SESIONES</th>
              <th>DESCUENTO</th>
              <th>IMPUESTO</th>
              <th>TOTAL</th>
            </tr>
          </thead>
          <tbody>
            <tr class="no-items" v-if="!items.length">
              <td colspan="5">No hay tratamientos seleccionados</td>
            </tr>
            <treatment-summary-item :item="item" v-for="item in items" :key="item.id" :appointment="appointment" @updatedLine="onUpdatedLine"></treatment-summary-item>
            <tr>
              <td colspan="5" class="text-right">
                <b>Total:</b>
              </td>
              <td>
                {{ total }}
              </td>
            </tr>
          </tbody>
        </table>
        <!-- <paginator
                :dataSet="dataSet"
                @changed="fetch"
                :noUpdateUrl="true"
            ></paginator> -->
        <div>
          <button type="button" class="btn btn-danger" @click="generateProforma" :disabled="loader">
            Generar Proforma</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader" />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import TreatmentSummaryItem from './TreatmentSummaryItem.vue';
export default {
    props: {
        treatments: {
            type: Array,
            require: true,
        },
        appointment: {
            type: Object,
            require: true,
        },
    },
    components: {
        TreatmentSummaryItem,
    },
    data() {
        return {
            loader: false,
            items: [],
        };
    },
    watch: {
        treatments() {
            this.prepareData();
        },
    },
    computed: {
        total() {
            if (this.items.length === 0) {
                return 0;
            }

            const total = this.items.reduce(
                (total, item) => total + parseFloat(item.subtotal),
                0
            );

            return total.format(0);
        },
    },
    methods: {
        onUpdatedLine() {
            this.emitter.emit('actSummaryTreatment', { data: this.items });
        },
        generateProforma() {
            if (this.loader) {
                return;
            }

            this.loader = true;
            axios
                .post(
                    '/appointments/' + this.appointment.id + '/estreatments/proforma',
                    {
                        items: this.items,
                    }
                )
                .then(() => {
                    this.loader = false;
                    flash('Proforma generada correctamente', 'success');
                })
                .catch(() => {
                    this.loader = false;
                    flash('Error al guardar Proforma', 'danger');
                });
        },
        prepareData() {
            this.items = [];
            this.items = this.treatments.map((treatment) => {
                return {
                    id: treatment.id,
                    optreatment_id: treatment.optreatment_id,
                    product_id: treatment.optreatment.product_id,
                    category: treatment.category,
                    name: treatment.name,
                    price: treatment.optreatment.price,
                    sessions: treatment.sessions ?? 1,
                    discount: treatment.discount ?? treatment.optreatment.discount,
                    tax: treatment.optreatment.tax,
                    subtotal: treatment.optreatment.price,
                };
            });

            this.emitter.emit('actSummaryTreatment', { data: this.items });
        },
    },
    created() {
        this.prepareData();
    },
};
</script>