<template>
  <tr>
    <td>{{ item.name }}</td>
    <td>{{ priceReadable }}</td>
    <td width="90px">
      <input type="text" inputmode="numeric" min="1" class="form-control" v-model="item.sessions" @blur="updateTreatment" />
    </td>
    <td width="90px">
      <input type="text" class="form-control" v-model="item.discount" @blur="updateTreatment" />
    </td>
    <td width="90px">{{ this.item.tax }}%</td>
    <td>
      {{ total }}
    </td>
  </tr>
</template>
<script>
export default {
    props: {
        item: {
            type: Object,
            required: true,
        },
        appointment: {
            type: Object,
            require: true,
        },
    },
    data() {
        return {};
    },
    computed: {
        priceReadable() {
            return this.item.price.format(0);
        },
        total() {
            const montoTotal = this.item.price * this.item.sessions;
            const subtotal = montoTotal - montoTotal * (this.item.discount / 100);
            const montoTotalLinea = subtotal + subtotal * (this.item.tax / 100);

            this.item.subtotal = montoTotalLinea;

            return montoTotalLinea.format(0);
        },
    },
    methods: {
        updateTreatment() {
            axios
                .put(
                    '/appointments/' +
          this.appointment.id +
          '/estreatments/' +
          this.item.id,
                    {
                        sessions: this.item.sessions,
                        discount: this.item.discount,
                    }
                )
                .then(() => {
                    this.$emit('updatedLine');
                })
                .catch(() => {
                    //this.loader = false;
                    flash('Error al guardar cambios', 'danger');
                });
        },
    },
};
</script>