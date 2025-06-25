<template>
  <form class="form-horizontal" @submit.prevent="save()">
    <loading :show="loader"></loading>

    <div class="form-group">
      <label for="name" class="col-sm-2 control-label">Url Api</label>

      <div class="col-sm-10">
        <input type="text" class="form-control" name="api_url" placeholder="Url del api farmacia" v-model="credential.api_url">
        <small>Ej: http://prueba.com</small>
        <form-error v-if="errors.api_url" :errors="errors" style="float:right;">
          {{ errors.api_url[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <label for="name" class="col-sm-2 control-label">Access Token</label>

      <div class="col-sm-10">
        <input type="text" class="form-control" name="access_token" placeholder="Token de acceso" v-model="credential.access_token">
        <form-error v-if="errors.access_token" :errors="errors" style="float:right;">
          {{ errors.access_token[0] }}
        </form-error>
      </div>
    </div>

    <div class="form-group">
      <label for="name" class="col-sm-2 control-label">Nombre</label>

      <div class="col-sm-10">
        <input type="text" class="form-control" name="name" placeholder="Nombre de farmacia" v-model="credential.name">
        <form-error v-if="errors.name" :errors="errors" style="float:right;">
          {{ errors.name[0] }}
        </form-error>
      </div>
    </div>

    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" @click="cancel()">Cerrar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
      </div>
    </div>
  </form>
</template>
<script>
import Loading from './Loading.vue';
export default {
    props: ['credencial', 'patient'],
    data() {
        return {


            loader: false,
            credential: {
                name: '',
                access_token: '',
                api_url: '',

            },

            errors: [],

        };
    },
    components: {
        Loading
    },
    methods: {
        clear() {

            this.credential = {
                name: '',
                access_token: '',
                api_url: '',
            };

        },

        cancel() {
            this.clear();
            this.$emit('canceled');

        },

        save() {

            if (this.loader) {
                return;
            }

            this.loader = true;

            if (!this.credential.id) {


                axios.post('/patients/' + this.patient.id + '/apipharmacredentials', this.credential)

                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clear();
                        flash('Credencial Creado.');
                        this.$emit('created', data);
                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar credencial', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            } else {

                axios.put('/apipharmacredentials/' + this.credential.id, this.credential)
                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clear();
                        flash('Credencial Actualizado.');
                        this.$emit('updated', data);
                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar Credencial', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            }

        },
        fill(credencial) {

            if (credencial) {
                this.credential = {

                    'id': credencial.id,
                    'name': credencial.name,
                    'api_url': credencial.api_url,
                    'access_token': credencial.access_token,
                    'patient_id': credencial.patient_id,


                };
            } else {
                this.credential = {

                    name: '',
                    access_token: '',
                    api_url: '',


                };
            }

        }

    },

    created() {

        this.emitter.on('createCredential', this.fill);
        this.emitter.on('editCredential', this.fill);
        if (this.credencial) {
            this.fill(this.credencial);
        }

    }

};
</script>

