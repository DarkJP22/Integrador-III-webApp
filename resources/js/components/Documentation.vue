<template>
  <div class="content">
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <div class="input-group">
            <input type="text" class="form-control" id="datetimepickerDocumentation" v-model="date" @blur="onBlurDatetime" />

            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
          </div>
        </div>
        <div class="form-group">
          <photo-upload @input="handleFileUpload"></photo-upload>
          <form-error v-if="errors.file" :errors="errors" style="float: right">
            {{ errors.file[0] }}
          </form-error>
        </div>
        <div class="form-group">
          <button @click="hit" class="btn btn-primary" v-bind:disabled="loader">
            Agregar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader" />
        </div>
      </div>
      <div class="col-md-8 documentation-gallery">
        <h3 v-show="!items.length" class="text-center">No hay elementos que mostrar</h3>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3" v-for="(photo, index) in items" :key="index">
          <button type="button" @click="remove(photo, index)" class="btn bg-red btn-sm" style="position: absolute; top: 0; right: 15px">
            <i class="fa fa-trash"></i>
          </button>
          <a :href="photo.file_path" data-sub-html="Demo Description" class="documentation-item" :title="formatDate(photo.date)">
            <img class="img-responsive thumbnail" :src="photo.file_path" />
            <small style="position: absolute; bottom: 25px; right: 15px; z-index: 2" class="badge bg-primary">{{ formatDate(photo.date) }}</small>
          </a>



        </div>
      </div>
    </div>
    <div class="row">

      <!-- <a href="#recomendations" data-toggle="tab" @click="next('recomendations')" class="btn btn-primary">Siguiente</a> -->
    </div>
  </div>
</template>

<script>
import FormError from './FormError.vue';
import PhotoUpload from './PhotoUpload.vue';

export default {
    props: ['appointment', 'documentations'],
    data() {
        return {
            date: '',
            file: '',
            items: [],
            loader: false,
            loader_message: '',
            errors: [],
        };
    },
    components: {
        FormError,
        PhotoUpload,
    },
    methods: {
        next() {
            $('a[href="#recomendations"]').tab('show');
        },
        hit() {
            console.log('hit');
            if (!this.date) return;

            this.loader = true;
            this.save();
        },
        save() {
            this.loader = true;
            this.loader_message = 'Guardando...';

            const form = new FormData();

            form.append('date', this.date);

            if (this.file) {
                form.append('file', this.file);
            }

            const config = {
                headers: {
                    'content-type': 'multipart/form-data',
                },
            };

            axios
                .post('/appointments/' + this.appointment.id + '/documentations', form, config)

                .then(({ data }) => {
                    this.loader = false;
                    flash('Archivo Agregado');
                    this.items.unshift(data);
                    this.file = '';
                    this.errors = [];
                })
                .catch((error) => {
                    this.loader = false;
                    flash('Error al guardar el Archivo', 'danger');
                    this.errors = error.response.data.errors
                        ? error.response.data.errors
                        : [];
                });

            this.emitter.emit('clearImage');
        },
        remove(item) {
            if (this.loader) return;

            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                this.loader = true;

                axios
                    .delete(`/documentations/${item.id}`)
                    .then(() => {
                        this.loader = false;
                        flash('Archivo Eliminado!');
                        var index = this.items.indexOf(item);
                        this.items.splice(index, 1);
                    })
                    .catch(() => {
                        this.loader = false;
                        flash('Error al eliminar el archivo', 'danger');
                    });
            }
        },
        handleFileUpload(file) {
            console.log(file);
            this.file = file;
        },
        formatDate(date) {
            return moment(date).format('YYYY-MM-DD');
        },
        onBlurDatetime(e) {
            const value = e.target.value;
            console.log('onInput fired', value);

            //Add this line

            this.date = value;
            this.$emit('input');
        },
    },
    created() {
        console.log('Component ready. Documentations');

        if (this.documentations.length) {
            this.items = this.documentations;
        }

        this.date = moment().format('YYYY-MM-DD');
    },
};
</script>