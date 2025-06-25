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
        <button type="button" class="btn btn-danger" v-if="credential.id" @click="removeCredential()">Eliminar</button>
        <img src="/img/loading.gif" alt="Cargando..." v-show="loader">
      </div>
    </div>
  </form>
</template>
<script>
import Loading from './Loading.vue';
export default {
    props: ['pharmacy'],
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
        removeCredential() {
            if (this.credential.id) {
                const r = confirm('¿Deseas Eliminar las credenciales? Ninguno de tus pacientes podra accesar al historial de compras desde la aplicación movil!!');

                if (r == true) {
                    axios.delete('/pharmacredentials/' + this.credential.id)

                        .then(({ data }) => {
                            this.loader = false;
                            this.errors = [];
                            this.clear();

                            flash('Credencial Eliminado.');
                            this.$emit('deleted', data);
                        })
                        .catch(error => {
                            this.loader = false;
                            flash('Error al eliminar credencial', 'danger');
                            this.errors = error.response.data.errors ? error.response.data.errors : [];
                        });
                }
            }
        },
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


                axios.post('/pharmacies/' + this.pharmacy.id + '/pharmacredentials', this.credential)

                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        //this.clear();
                        this.credential = data;
                        flash('Credencial Creado.');
                        this.$emit('created', data);
                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar credencial', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            } else {

                axios.put('/pharmacredentials/' + this.credential.id, this.credential)
                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        //this.clear();
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
                    'pharmacy_id': credencial.pharmacy_id,


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

        if (this.pharmacy.pharmacredential) {
            this.fill(this.pharmacy.pharmacredential);
        }

    }

};
</script>

