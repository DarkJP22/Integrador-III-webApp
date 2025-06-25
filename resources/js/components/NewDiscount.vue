<template>
  <div>

    <form class="form-horizontal" @submit.prevent="save()">

      <div class="form-group">
        <label for="discount_name" class="col-sm-2 control-label">Nombre / Empresa</label>

        <div class="col-sm-10">
          <input type="text" class="form-control" name="name" placeholder="Nombre del descuento" v-model="descuento.name">
          <form-error v-if="errors.name" :errors="errors" style="float:right;">
            {{ errors.name[0] }}
          </form-error>
        </div>
      </div>

      <div class="form-group">
        <label for="discount_tarifa" class="col-sm-2 control-label">Tarifa</label>

        <div class="col-sm-10">
          <div class="input-group">
            <input type="text" class="form-control" name="tarifa" id="tarifa" v-model="descuento.tarifa">

            <div class="input-group-addon">
              <i class="fa fa-percent"></i>
            </div>

          </div>

          <form-error v-if="errors.tarifa" :errors="errors" style="float:right;">
            {{ errors.tarifa[0] }}
          </form-error>
        </div>
      </div>


      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <button type="button" class="btn btn-secondary" @click="cancel()">Limpiar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
        </div>
      </div>
    </form>
  </div>
</template>
<script>

export default {
    data() {
        return {

            loader: false,
            descuento: {
                id: '',
                name: '',
                user_id: '',
                tarifa: ''
            },

            errors: [],

        };
    },

    methods: {
        cancel() {
            this.clearForm();
        },
        save() {

            if (!this.descuento.id) {

                axios.post('/discounts', this.descuento)

                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clearForm();
                        flash('Descuento Creado.');
                        this.$emit('created', data);
                    })
                    .catch(error => {
                        flash(error.response.data.message, 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            } else {

                axios.put('/discounts/' + this.descuento.id, this.descuento)
                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clearForm();
                        flash('Descuento Actualizado.');
                        this.$emit('updated', data);
                    })
                    .catch(error => {
                        flash(error.response.data.message, 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            }

        },
        fill(discount) {

            this.descuento = {

                'id': discount.id,
                'name': discount.name,
                'tarifa': discount.tarifa,
                'user_id': discount.user_id


            };

        },
        clearForm() {
            this.descuento = {
                id: '',
                name: '',
                tarifa: '',
                user_id: ''

            };
        },


    },

    created() {

        this.emitter.on('edit', this.fill);




    }

};
</script>

