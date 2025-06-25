<template>
  <div>
    <div class="modal fade" :id="name ? name : 'optionModal'" tabindex="-1" role="dialog" aria-labelledby="optionModallLabel">
      <div class="modal-dialog " role="document">
        <div class="modal-content">
          <form action="#" @submit.prevent="save();" class="form-horizontal" autocomplete="off">
            <div class="modal-header">

              <h4 class="modal-title" id="optionModallLabel">Agregar Opción</h4>
            </div>
            <div class="modal-body">

              <loading :show="loader"></loading>

              <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Nombre</label>

                <div class="col-sm-10">
                  <input type="text" class="form-control" name="name" placeholder="" v-model="option.name" required>
                  <form-error v-if="errors.name" :errors="errors" style="float:right;">
                    {{ errors.name[0] }}
                  </form-error>
                </div>
              </div>

              <div class="form-group">
                <label for="tags" class="col-sm-2 control-label">Categoria</label>

                <div class="col-sm-10">

                  <select name="category" id="category" class="form-control" v-model="option.category" required>
                    <option value="facial">Facial</option>
                    <option value="corporal">Corporal</option>
                    <option value="depilacion">Depilación</option>
                  </select>
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
export default {

    props: ['name', 'url'],
    data() {
        return {
            option: {},
            loader: false,
            newOption: false,
            errors: [],



        };
    },
    components: {
        Loading,
    },
    methods: {

        nuevo() {
            this.newOption = true;
            this.option = {};
        },

        cancel() {

            this.newOption = false;

        },


        save() {

            if (this.loader) { return; }
            this.loader = true;

            axios.post(this.url, this.option)

                .then(({ data }) => {
                    this.loader = false;
                    this.errors = [];
                    this.option = {};
                    flash('Opcion Creada.');
                    this.$emit('created', data);

                    $(this.name ? '#' + this.name + ' .btn-close' : '#optionModal .btn-close').click();
                })
                .catch(error => {
                    this.loader = false;
                    flash(error.response.data.message, 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });




        }//save


    }, //methods
    created() {
        console.log('Component ready. Option Form');


        this.emitter.on('cancelNewOption', this.cancel);





    }
};
</script>