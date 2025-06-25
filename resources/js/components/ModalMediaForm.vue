<template>
  <div>
    <div class="modal fade" id="mediaModal" tabindex="-1" role="dialog" aria-labelledby="mediaModalLabel">
      <div class="modal-dialog " role="document">
        <div class="modal-content">
          <form action="#" @submit.prevent="save();" class="form-horizontal" autocomplete="off">
            <div class="modal-header">

              <h4 class="modal-title" id="mediaModalLabel">Agrega tu video</h4>
            </div>
            <div class="modal-body">

              <loading :show="loader"></loading>
              <div class="form-group">
                <label for="url" class="col-sm-2 control-label">Url</label>

                <div class="col-sm-10">
                  <input type="text" class="form-control" name="url" placeholder="Youtube, Vimeo.." v-model="media.url">
                  <form-error v-if="errors.url" :errors="errors" style="float:right;">
                    {{ errors.url[0] }}
                  </form-error>
                </div>
              </div>
              <div class="form-group">
                <label for="title" class="col-sm-2 control-label">Titulo</label>

                <div class="col-sm-10">
                  <input type="text" class="form-control" name="title" placeholder="" v-model="media.title">
                  <form-error v-if="errors.title" :errors="errors" style="float:right;">
                    {{ errors.title[0] }}
                  </form-error>
                </div>
              </div>
              <div class="form-group">
                <label for="description" class="col-sm-2 control-label">Descripción</label>

                <div class="col-sm-10">
                  <textarea name="description" id="description" cols="30" rows="2" v-model="media.description" class="form-control"></textarea>

                  <form-error v-if="errors.description" :errors="errors" style="float:right;">
                    {{ errors.description[0] }}
                  </form-error>
                </div>
              </div>
              <div class="form-group">
                <label for="tags" class="col-sm-2 control-label">Etiquetas</label>

                <div class="col-sm-10">
                  <!-- <input-tag placeholder="Agregar Etiqueta..." v-model="media.tags" :add-tag-on-blur="true"></input-tag> -->
                  <v-select :options="mediaTags" placeholder="Buscar Etiqueta..." label="name" multiple v-model="media.tags">

                    <template slot="no-options">
                      Escribe para buscar los etiquetas
                    </template>

                  </v-select>
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
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
export default {


    props: ['tags'],
    data() {
        return {
            media: {},
            loader: false,
            newMedia: false,
            errors: [],
            mediaTags: this.tags



        };
    },
    components: {
        Loading,
        vSelect
    },
    methods: {
        onSearchTags(search, loading) {
            loading(true);
            this.search(loading, search, this);
        },
        search: _.debounce((loading, search, vm) => {

            const url = `/mediatags/tags?q=${search}`;


            axios.get(url)
                .then(response => {

                    vm.mediaTags = response.data;
                    loading(false);

                });

        }, 350),
        nuevo() {
            this.newMedia = true;
            this.media = {};
        },

        cancel() {

            this.newMedia = false;

        },

        edit(media) {

            this.media = media;
            this.newMedia = false;

        },
        save() {

            if (this.loader) { return; }
            this.loader = true;
            if (this.media.id) {

                axios.put('/pharmacy/media/' + this.media.id, this.media)
                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.media = {};

                        flash('Video Actualizado.');
                        this.$emit('updated', data);
                        $('#mediaModal .btn-close').click();
                    })
                    .catch(error => {
                        this.loader = false;
                        flash(error.response.data.message, 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            } else {
                axios.post('/pharmacy/media', this.media)

                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.media = {};
                        flash('Video Creado.');
                        this.$emit('created', data);
                        $('#mediaModal .btn-close').click();
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

        this.emitter.on('editMedia', this.edit);
        this.emitter.on('cancelNewMedia', this.cancel);


        //     this.emitter.on('showMediaModal', (data) => {
        //        this.invoiceId = data;
        //        this.getInvoice()
        //        this.fetch()
        //    });


    }
};
</script>