<template>
  <div class="modal fade" id="modalCodeExpedient" role="dialog" aria-labelledby="modalCodeExpedient">
    <div class="modal-dialog " role="document">
      <div class="modal-content">
        <form action="#" @submit.prevent="save();" class="form-horizontal" autocomplete="off">
          <div class="modal-header">

            <h4 class="modal-title" id="modalCodeExpedientLabel">Acceso Expediente</h4>

          </div>

          <div class="modal-body">
            <loading :show="loader"></loading>

            <p>Ingresa el código de autorización para poder acceder a tu expediente y sus funcionalidades. </p>


            <div class="form-group">

              <div class="col-sm-12">
                <input type="text" class="form-control" name="code" placeholder="Código" v-model="form.code">
                <form-error v-if="errors.code" :errors="errors" style="float:right;">
                  {{ errors.code[0] }}
                </form-error>
              </div>
            </div>



            <p> Si aun no lo tienen, puedes obtenerlo en alguna de nuestras farmacias afiliadas o por medio del siguiente <a href="/getexpedientcode" class="btn btn-secondary">Obtener Código</a></p>

          </div>
          <div class="modal-footer">

            <button type="submit" class="btn btn-primary">Ingresar</button>

            <button type="button" class="btn btn-default btn-close" data-dismiss="modal">Cerrar</button>


          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script>

import Loading from './Loading.vue';

export default {
    data() {
        return {

            loader: false,
            userId: '',
            patientId: '',
            user: {},
            form: {
                code: '',
            },
            errors: []
        };
    },
    components: {
        Loading
    },
    methods: {
        fetch() {
            this.loader = true;
            axios.get('/users/' + this.userId)
                .then(({ data }) => {

                    this.loader = false;
                    this.clear();


                    this.user = data;


                })
                .catch(error => {
                    this.loader = false;
                    this.clear();
                    this.detalle = error.response.data.errors;

                    if (error.response.status == 400) {
                        flash(error.response.data.errors, 'danger');
                    } else {
                        flash('Ha ocurrido un error', 'danger');
                    }

                });

        },
        save() {
            if (this.loader) { return; }
            this.loader = true;

            axios.post('/users/' + this.userId + '/authorization/expedient', this.form)

                .then(({ data }) => {
                    this.loader = false;
                    this.errors = [];
                    this.clear();
                    flash('Codigo Enviado');
                    this.$emit('created', data);

                    $('#modalCodeExpedient .btn-close').click();

                    if (this.patientId) {
                        window.location = '/patients/' + this.patientId + '/expedient';
                    } else {
                        window.location.reload();
                    }

                })
                .catch(error => {
                    this.loader = false;
                    flash(error.response.data.message, 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });
        },
        clear() {

            this.form = {
                code: '',

            };

        }

    },
    created() {
        this.emitter.on('showCodeExpedientModal', (data) => {

            this.userId = data.userId;
            this.patientId = data.patientId;
            //this.fetch()
        });
    }
};
</script>
