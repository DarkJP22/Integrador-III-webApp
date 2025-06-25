<template>
  <form class="form-horizontal" @submit.prevent="save()">
    <loading :show="loader"></loading>

    <div class="form-group">
      <label for="name" class="col-sm-2 control-label">Contacto emergencia</label>

      <div class="col-sm-10">
        <input type="text" class="form-control" name="name" placeholder="Nombre del Contacto " v-model="contact.name">
        <form-error v-if="errors.name" :errors="errors" style="float:right;">
          {{ errors.name[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <label for="phone_number" class="col-sm-2 control-label">Teléfono emergencia</label>

      <div class="col-sm-3">
        <select class="form-control " style="width: 100%;" name="phone_country_code" v-model="contact.phone_country_code">

          <option v-for="item in phoneCodes" v-bind:value="item.value" :key="item.id"> {{ item.text }}</option>

        </select>
        <form-error v-if="errors.phone_country_code" :errors="errors" style="float:right;">
          {{ errors.phone_country_code[0] }}
        </form-error>
      </div>
      <div class="col-sm-7">
        <input type="text" class="form-control" name="phone_number" placeholder="Teléfono" v-model="contact.phone_number">
        <form-error v-if="errors.phone_number" :errors="errors" style="float:right;">
          {{ errors.phone_number[0] }}
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
    props: ['contacto', 'patient'],
    data() {
        return {

            phoneCodes: [
                {
                    text: '+506',
                    value: '+506'
                }
            ],
            loader: false,
            contact: {
                name: '',
                phone_country_code: '+506',
                phone_number: '',

            },

            errors: [],

        };
    },
    components: {
        Loading
    },
    methods: {
        clear() {

            this.contact = {
                name: '',
                phone_country_code: '+506',
                phone_number: '',
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

            if (!this.contact.id) {


                axios.post('/patients/' + this.patient.id + '/contacts', this.contact)

                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clear();
                        flash('Contacto Creado.');
                        this.$emit('created', data);
                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar Contacto', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            } else {

                axios.put('/emergency-contacts/' + this.contact.id, this.contact)
                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clear();
                        flash('Contacto Actualizado.');
                        this.$emit('updated', data);
                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar Contacto', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            }

        },
        fill(contacto) {

            this.contact = {

                'id': contacto.id,
                'name': contacto.name,
                'phone_country_code': contacto.phone_country_code,
                'phone_number': contacto.phone_number,
                'patient_id': contacto.patient_id,


            };

        }

    },

    created() {


        this.emitter.on('editContact', this.fill);
        if (this.contacto) {
            this.fill(this.contacto);
        }

    }

};
</script>

