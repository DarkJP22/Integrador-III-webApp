<template>
    <form class="form-horizontal" @submit.prevent="save()">
        <loading :show="loader"></loading>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="tipo_identificacion">Tipo identificación</label>

            <div class="col-sm-10">
                <select v-model="appointmentRequest.tipo_identificacion" class="form-control" name="tipo_identificacion" required style="width: 100%;">
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
                <input v-model="appointmentRequest.ide" :disabled="loader" class="form-control" name="ide" placeholder="" type="text" @change="searchCustomer()"
                       @keydown.prevent.enter="searchCustomer()">
                <form-error v-if="errors.ide" :errors="errors" style="float:right;">
                    {{ errors.ide[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="paciente_name">Nombre Completo</label>

            <div class="col-sm-10">
                <input v-model="appointmentRequest.first_name" class="form-control" name="first_name" placeholder="Nombre del paciente" required type="text">
                <form-error v-if="errors.first_name" :errors="errors" style="float:right;">
                    {{ errors.first_name[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="paciente_name">Provincia</label>

            <div class="col-sm-10">
                <select v-model="appointmentRequest.province" class="form-control "
                        style="width: 100%;" @change="onChangeProvince">

                    <option v-for="item in filteredProvincias" :key="item.value"
                            v-bind:value="item.value"> {{ item.label }}
                    </option>

                </select>
                <form-error v-if="errors.province" :errors="errors" style="float:right;">
                    {{ errors.province[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="paciente_name">Canton</label>

            <div class="col-sm-10">
                <select v-model="appointmentRequest.canton" class="form-control "
                        style="width: 100%;" @change="onChangeCanton">

                    <option v-for="item in filteredCantones" :key="item.value"
                            v-bind:value="item.value"> {{ item.label }}
                    </option>

                </select>
                <form-error v-if="errors.canton" :errors="errors" style="float:right;">
                    {{ errors.canton[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="paciente_name">Distrito</label>

            <div class="col-sm-10">
                <select v-model="appointmentRequest.district" class="form-control "
                        style="width: 100%;">

                    <option v-for="item in filteredDistritos" :key="item.value"
                            v-bind:value="item.value"> {{ item.label }}
                    </option>

                </select>
                <form-error v-if="errors.district" :errors="errors" style="float:right;">
                    {{ errors.district[0] }}
                </form-error>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" for="paciente_phone">Teléfono</label>

            <div class="col-sm-3">
                <select v-model="appointmentRequest.phone_country_code" class="form-control " name="phone_country_code" required style="width: 100%;">

                    <option v-for="item in phoneCodes" :key="item.id" v-bind:value="item.value"> {{ item.text }}</option>

                </select>
                <form-error v-if="errors.phone_country_code" :errors="errors" style="float:right;">
                    {{ errors.phone_country_code[0] }}
                </form-error>
            </div>
            <div class="col-sm-7">
                <input v-model="appointmentRequest.phone_number" class="form-control" name="phone_number" placeholder="Teléfono Celular" required type="text">
                <form-error v-if="errors.phone_number" :errors="errors" style="float:right;">
                    {{ errors.phone_number[0] }}
                </form-error>
            </div>
        </div>
<!--        <div class="form-group">-->
<!--            <label class="col-sm-2 control-label" for="paciente_phone">Teléfono</label>-->

<!--            <div class="col-sm-3">-->
<!--                <select v-model="appointmentRequest.phone_country_code_2" class="form-control " name="phone_country_code_2" required style="width: 100%;">-->

<!--                    <option v-for="item in phoneCodes" :key="item.id" v-bind:value="item.value"> {{ item.text }}</option>-->

<!--                </select>-->
<!--                <form-error v-if="errors.phone_country_code_2" :errors="errors" style="float:right;">-->
<!--                    {{ errors.phone_country_code_2[0] }}-->
<!--                </form-error>-->
<!--            </div>-->
<!--            <div class="col-sm-7">-->
<!--                <input v-model="appointmentRequest.phone_number_2" class="form-control" name="phone_number_2" placeholder="Teléfono Celular" type="text">-->
<!--                <form-error v-if="errors.phone_number_2" :errors="errors" style="float:right;">-->
<!--                    {{ errors.phone_number_2[0] }}-->
<!--                </form-error>-->
<!--            </div>-->
<!--        </div>-->

        <div class="form-group">
            <label class="col-sm-2 control-label" for="paciente_name">Visita</label>

            <div class="col-sm-10">
                <input v-model="appointmentRequest.visit_location" class="form-control" name="visit_location" placeholder="Dirección de visita" required type="text">
                <form-error v-if="errors.visit_location" :errors="errors" style="float:right;">
                    {{ errors.visit_location[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="paciente_name">Examenes a realizar</label>

            <div class="col-sm-10">
                <input v-model="appointmentRequest.exams" class="form-control" name="exams" placeholder="" required type="text">
                <form-error v-if="errors.exams" :errors="errors" style="float:right;">
                    {{ errors.exams[0] }}
                </form-error>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button class="btn btn-primary" type="submit">Guardar</button>
                <button class="btn btn-secondary" type="button" @click="cancel()">Cancelar</button>
                <img v-show="loader" alt="Cargando..." src="/img/loading.gif">
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
        actionUrl: {
            type: String
        }

    },
    data() {
        return {
            tipoIdentificaciones: [],
            provincias: window.provincias,
            cantones: [],
            distritos: [],
            phoneCodes: [
                {
                    text: '+506',
                    value: '+506'
                }
            ],
            loader: false,
            appointmentRequest: {
                ide: '',
                first_name: '',
                phone_number: '',
                tipo_identificacion: '',
                visit_location: '',
                exams: '',
                coords: '',
                province: '',
                canton: '',
                district: '',
                phone_country_code: '+506',
                phone_country_code_2: '+506',

            },
            errors: [],


        };
    },
    components: {
        Loading,
    },
    computed: {
        filteredProvincias() {

            return this.provincias.map(item => {
                return {value: item.id, label: item.title};
            });
        },
        filteredCantones() {

            return this.cantones.map(item => {
                return {value: item.title, label: item.title};
            });
        },
        filteredDistritos() {

            return this.distritos.map(item => {
                return {value: item.title, label: item.title};
            });
        },
    },
    methods: {
        clear() {

            this.appointmentRequest = {
                ide: '',
                first_name: '',
                phone_number: '',
                tipo_identificacion: '',
                visit_location: '',
                exams: '',
                coords: '',
                province: '',
                canton: '',
                district: '',
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
                .then(({data}) => {

                    this.tipoIdentificaciones = data;

                    // this.setPatient();


                });
        },

        searchCustomer() {

            this.loader = true;
            haciendaApi.get('https://api.hacienda.go.cr/fe/ae?identificacion=' + this.appointmentRequest.ide)
                .then(({data}) => {
                    this.loader = false;
                    console.log(data);
                    this.appointmentRequest.first_name = data.nombre;
                    this.appointmentRequest.tipo_identificacion = data.tipoIdentificacion;
                }).catch(error => {
                this.loader = false;
                flash('Ocurrio un error en la consulta!!', 'danger');
                this.errors = error.response.data.errors ? error.response.data.errors : [];
            });


        },
        onChangeProvince: function (event) {

            var cant = [];
            console.log(event.currentTarget.value);
            window.provincias.forEach(function (prov) {

                if (event.currentTarget.value === prov.id) {
                    cant = prov.cantones;
                }
            });

            this.cantones = cant;
            //this.fields.canton.options = this.filteredCantones;
        },
        onChangeCanton: function (event) {

            var dist = [];

            this.cantones.forEach(function (cant) {
                if (event.currentTarget.value === cant.title) {
                    dist = cant.distritos;
                }
            });

            this.distritos = dist;
            // this.fields.district.options = this.filteredDistritos;
        },
        save() {

            if (this.loader) {
                return;
            }

            this.loader = true;

            if (!this.appointmentRequest.id) {


                axios.post('/lab/appointment-requests', this.appointmentRequest)

                    .then(({data}) => {
                        this.loader = false;
                        this.errors = [];
                        this.clear();
                        flash('Solicitud Creada.');
                        this.$emit('created', data);
                        if (this.actionUrl) {
                            window.location = this.actionUrl;
                        }

                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar la solicitud de cita', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            }

        },


    },

    created() {
        this.loadTipoIdentificaciones();


    }

};
</script>

