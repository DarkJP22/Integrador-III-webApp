<template>
  <div>
    <div class="callout callout-danger" v-show="!offices.length">
      <h4>No tienes registrado consultorios Independientes!</h4>

      <p>Para agregar secretarias necesitas al menos un consultorio independiente. Crealo <a href="/medic/offices/create">aquí</a> </p>
    </div>
    <form class="form-horizontal" v-show="offices.length" @submit.prevent="save()">

      <div class="form-group">
        <label for="asistente_name" class="col-sm-2 control-label">Nombre</label>

        <div class="col-sm-10">
          <input type="text" class="form-control" name="name" placeholder="Nombre del asistente" v-model="asistente.name">
          <form-error v-if="errors.name" :errors="errors" style="float:right;">
            {{ errors.name[0] }}
          </form-error>
        </div>
      </div>

      <div class="form-group">
        <label for="asistente_email" class="col-sm-2 control-label">Email</label>

        <div class="col-sm-10">
          <input type="email" class="form-control" name="email" placeholder="Email" v-model="asistente.email">
          <form-error v-if="errors.email" :errors="errors" style="float:right;">
            {{ errors.email[0] }}
          </form-error>
        </div>
      </div>
      <div class="form-group">
        <label for="asistente_phone" class="col-sm-2 control-label">Teléfono</label>
        <div class="col-sm-3">
          <select class="form-control " style="width: 100%;" name="phone_country_code" v-model="asistente.phone_country_code" required>

            <option v-for="item in phoneCodes" v-bind:value="item.value" :key="item.id"> {{ item.text }}</option>

          </select>
          <form-error v-if="errors.phone_country_code" :errors="errors" style="float:right;">
            {{ errors.phone_country_code[0] }}
          </form-error>
        </div>
        <div class="col-sm-7">
          <input type="text" class="form-control" name="phone_number" placeholder="Teléfono" v-model="asistente.phone_number">
          <form-error v-if="errors.phone_number" :errors="errors" style="float:right;">
            {{ errors.phone_number[0] }}
          </form-error>
        </div>
      </div>
      <div class="form-group">
        <label for="asistente_password" class="col-sm-2 control-label">Contraseña</label>

        <div class="col-sm-10">
          <input type="password" class="form-control" name="password" placeholder="Contraseña" v-model="asistente.password">
          <form-error v-if="errors.password" :errors="errors" style="float:right;">
            {{ errors.password[0] }}
          </form-error>
        </div>
      </div>
      <div class="form-group">
        <label for="asistente_office_id" class="col-sm-2 control-label">Consultorio</label>

        <div class="col-sm-10">
          <select class="form-control " style="width: 100%;" name="office_id" v-model="asistente.office_id">
            <option></option>
            <option v-for="item in offices" v-bind:value="item.id" :key="item.id"> {{ item.name }}</option>

          </select>
          <form-error v-if="errors.office_id" :errors="errors" style="float:right;">
            {{ errors.office_id[0] }}
          </form-error>
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <button type="button" class="btn btn-secondary" @click="cancel()" v-if="!backUrl">Limpiar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
          <a :href="backUrl" class="btn btn-secondary" v-if="backUrl">Regresar</a>
        </div>
      </div>
    </form>
  </div>
</template>
<script>

export default {
    props: ['backUrl'],
    data() {
        return {

            offices: [],
            loader: false,
            asistente: {
                id: '',
                name: '',
                email: '',
                password: '',
                office_id: '',
                phone_country_code: '+506',
                phone_number: ''
            },
            phoneCodes: [
                {
                    text: '+506',
                    value: '+506'
                }
            ],
            errors: [],

        };
    },

    methods: {
        cancel() {
            this.clearForm();
        },
        save() {

            if (!this.asistente.id) {

                axios.post('/assistants', this.asistente)

                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clearForm();
                        flash('Asistente Creado.');
                        this.$emit('created', data);

                        if (this.backUrl) {
                            window.location.href = this.backUrl;
                        }

                    })
                    .catch(error => {
                        flash(error.response.data.message, 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            } else {

                axios.put('/assistants/' + this.asistente.id, this.asistente)
                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clearForm();
                        flash('Asistente Actualizado.');
                        this.$emit('updated', data);

                        if (this.backUrl) {
                            window.location.href = this.backUrl;
                        }
                    })
                    .catch(error => {
                        flash(error.response.data.message, 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            }

        },
        fill(assistant) {

            this.asistente = {

                'id': assistant.id,
                'name': assistant.name,
                'email': assistant.email,
                'phone_country_code': assistant.phone_country_code,
                'phone_number': assistant.phone_number,
                'office_id': assistant.clinics_assistants[0].id,


            };

        },
        clearForm() {
            this.asistente = {
                id: '',
                name: '',
                email: '',
                password: '',
                office_id: '',
                phone_country_code: '+506',
                phone_number: ''
            };
        },
        fetchOffices(page) {
            axios.get(this.url(page)).then(this.refresh);
        },
        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            return `/offices?medic=1&type=1&page=${page}`;
        },
        refresh({ data }) {
            this.dataSet = data;
            this.offices = data.data;
            window.scrollTo(0, 0);
        },

    },

    created() {
    //this.fetchOffices();

        this.emitter.on('editAssistant', this.fill);
        this.emitter.on('created', (data) => this.offices.push(data));
        this.emitter.on('loadedOffices', (data) => this.offices = data);


    }

};
</script>

