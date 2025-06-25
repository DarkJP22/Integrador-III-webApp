<template>
  <form class="form-horizontal" @submit.prevent="save()">
    <loading :show="loader"></loading>

    <div class="form-group">
      <label class="col-sm-2 control-label" for="tipo_identificacion">Tipo identificación</label>

      <div class="col-sm-10">
        <select v-model="paciente.tipo_identificacion" class="form-control" name="tipo_identificacion" required style="width: 100%;">
          <option value=""></option>
          <option v-for="(value, key) in tipoIdentificaciones" :key="key" :value="key">
            {{ value }}
          </option>
        </select>
        <form-error v-if="errors.tipo_identificacion" :errors="errors" style="float:right;">
          {{ errors.tipo_identificacion[0] }}
        </form-error>
      </div>

    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="ide">Cédula</label>

      <div class="col-sm-10">
        <input v-model="paciente.ide" :disabled="loader" class="form-control" name="ide" placeholder="" type="text" @change="searchCustomer()" @keydown.prevent.enter="searchCustomer()">
        <form-error v-if="errors.ide" :errors="errors" style="float:right;">
          {{ errors.ide[0] }}
        </form-error>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label" for="paciente_name">Nombre Completo</label>

      <div class="col-sm-10">
        <input v-model="paciente.first_name" class="form-control" name="first_name" placeholder="Nombre del paciente" required type="text">
        <form-error v-if="errors.first_name" :errors="errors" style="float:right;">
          {{ errors.first_name[0] }}
        </form-error>
      </div>
    </div>


    <div class="form-group">
      <label class="col-sm-2 control-label" for="paciente_phone">Teléfono</label>

      <div class="col-sm-3">
        <select v-model="paciente.phone_country_code" class="form-control " name="phone_country_code" required style="width: 100%;">

          <option v-for="item in phoneCodes" :key="item.id" v-bind:value="item.value"> {{ item.text }}</option>

        </select>
        <form-error v-if="errors.phone_country_code" :errors="errors" style="float:right;">
          {{ errors.phone_country_code[0] }}
        </form-error>
      </div>
      <div class="col-sm-7">
        <input v-model="paciente.phone_number" class="form-control" name="phone_number" placeholder="Teléfono Celular" required type="text">
        <form-error v-if="errors.phone_number" :errors="errors" style="float:right;">
          {{ errors.phone_number[0] }}
        </form-error>
      </div>
    </div>
      <div class="form-group">
          <label class="col-sm-2 control-label" for="paciente_phone">Teléfono</label>

          <div class="col-sm-3">
              <select v-model="paciente.phone_country_code_2" class="form-control " name="phone_country_code_2" required style="width: 100%;">

                  <option v-for="item in phoneCodes" :key="item.id" v-bind:value="item.value"> {{ item.text }}</option>

              </select>
              <form-error v-if="errors.phone_country_code_2" :errors="errors" style="float:right;">
                  {{ errors.phone_country_code_2[0] }}
              </form-error>
          </div>
          <div class="col-sm-7">
              <input v-model="paciente.phone_number_2" class="form-control" name="phone_number_2" placeholder="Teléfono Celular"  type="text">
              <form-error v-if="errors.phone_number_2" :errors="errors" style="float:right;">
                  {{ errors.phone_number_2[0] }}
              </form-error>
          </div>
      </div>

    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <button class="btn btn-secondary" type="button" @click="cancel()">Cancelar</button><img v-show="loader" alt="Cargando..." src="/img/loading.gif">
      </div>
    </div>
  </form>
</template>
<script>
import Loading from './Loading.vue';
import haciendaApi from '@/services/haciendaApi';
//import vSelect from 'vue-select';
export default {
    props: {
        patient: {
            type: Object,
        },
        endpoint: {
            type: String,
            required: true
        },
        actionUrl: {
            type: String
        }

    },
    data() {
        return {
            tipoIdentificaciones: [],
            provincias: window.provincias,
            phoneCodes: [
                {
                    text: '+506',
                    value: '+506'
                }
            ],
            loader: false,
            paciente: {
                phone_country_code: '+506',
                phone_country_code_2: '+506',
            },
            errors: [],


        };
    },
    components: {
        Loading,
    },
    methods: {
        clear() {

            this.paciente = {
                phone_country_code: '+506',
                phone_country_code_2: '+506',
            };

        },

        cancel() {
            this.clear();
            this.$emit('canceled');
            
            if (this.actionUrl) {
                window.location = this.actionUrl;
            }

        },

        loadTipoIdentificaciones() {

            axios.get('/identificaciones/tipos')
                .then(({ data }) => {

                    this.tipoIdentificaciones = data;

                    // this.setPatient();


                });
        },

        searchCustomer() {

            this.loader = true;
            haciendaApi.get('https://api.hacienda.go.cr/fe/ae?identificacion=' + this.paciente.ide)
                .then(({ data }) => {
                    this.loader = false;
                    console.log(data);
                    this.paciente.first_name = data.nombre;
                    this.paciente.tipo_identificacion = data.tipoIdentificacion;
                }).catch(error => {
                    this.loader = false;
                    flash('Ocurrio un error en la consulta!!', 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });




        },
        save() {

            if (this.loader) {
                return;
            }

            this.loader = true;

            if (!this.paciente.id) {


                axios.post(this.endpoint, this.paciente)

                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clear();
                        flash('Paciente Creado.');
                        this.$emit('created', data);
                        if (this.actionUrl) {
                            window.location = this.actionUrl;
                        }

                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar paciente', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            } else {

                axios.put(`${this.endpoint}/${this.paciente.id}`, this.paciente)
                    .then(({ data }) => {
                        this.loader = false;
                        this.errors = [];
                        this.clear();
                        flash('Paciente Actualizado.');
                        this.$emit('updated', data);
                        if (this.actionUrl) {
                            window.location = this.actionUrl;
                        }


                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar paciente', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });


            }

        },
        fill(patient) {

            this.paciente = {

                'id': patient.id,
                'tipo_identificacion': patient.tipo_identificacion,
                'ide': patient.ide,
                'first_name': patient.first_name,
                'phone_country_code': patient.phone_country_code,
                'phone_number': patient.phone_number,
                'phone_country_code_2': patient.phone_country_code_2,
                'phone_number_2': patient.phone_number_2,


            };

        }

    },

    created() {
        this.loadTipoIdentificaciones();

        if (this.patient) {
            this.fill(this.patient);
        }

    }

};
</script>

