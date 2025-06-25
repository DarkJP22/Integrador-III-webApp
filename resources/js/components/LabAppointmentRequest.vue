<template>
    <div class="container tw-max-w-4xl tw-py-8">
        <div class="row">
            <div class="col-sm-12">
                <a class="tw-block" href="/" style="max-width: 100px; margin: 0 auto;"><img alt="Doctor Blue"
                                                                                            class="tw-w-full"
                                                                                            src="/img/logo.png"></a>
                <h2>Solicitud Cita Examenes de laboratorio</h2>
                <div v-if="!submitted" class="form-group">
                    <label class="control-label">
                        ¿Eres el paciente?
                    </label>
                    <select v-model="isPatient" class="form-control " style="width: 100%;">

                        <option value="yes"> Si, soy el paciente</option>
                        <option value="no"> No, el paciente es otra persona</option>

                    </select>
                </div>
                <form v-if="!submitted && isPatient" class="p-10" @submit.prevent="">
                    <div class="tw-flex tw-items-stretch tw-gap-2">
                        <div v-for="step in totalSteps" :key="step" :class="{ 'tw-bg-teal-500 ': step - 1 <= currentStep }"
                             class="tw-h-2 tw-w-full tw-rounded tw-text-teal-500" style="border: 1px solid;"></div>
                    </div>
                    <div v-for="({ title, fieldKeys }, step) in filteredSteps" :key="step">
                        <div v-if="currentStep === step">
                            <h3>{{ title }}</h3>
                            <!--Form Field-->
                            <div v-for="field in fieldKeys" :key="field" class="relative form-group">

                                <label class="control-label">
                                    {{ fields[field].label }}
                                </label>
                                <template v-if="fields[field].type === 'text'">
                                    <input v-model="fields[field].value" class="form-control" type="text"
                                           @blur="fields[field].blurEvent"/>
                                </template>
                                <template v-if="fields[field].type === 'date'">
                                    <flat-pickr v-model="fields[field].value" class="form-control"
                                                placeholder="Selecione una fecha">
                                    </flat-pickr>

                                </template>

                                <template v-if="fields[field].type === 'select'">
                                    <select v-model="fields[field].value" :disabled="fields[field].disabled" class="form-control "
                                            style="width: 100%;" @change="fields[field].changeEvent">

                                        <option v-for="item in fields[field].options" :key="item.value"
                                                v-bind:value="item.value"> {{ item.label }}
                                        </option>

                                    </select>

                                </template>

                                <template v-if="fields[field].type === 'coords'">
                                    <div class="tw-flex">
                                        <input v-model="fields[field].value" class="form-control" readonly type="text"/>
                                        <button class="btn btn-secondary btn-geo" type="button" @click="getGeolocation">
                                            <i class="fa fa-"></i>Tu ubicación Actual
                                        </button>
                                    </div>

                                </template>


                                <form-error v-if="invalids[field] || errors[field]" :errors="invalids || errors">
                                    {{ invalids[field] || errors[field][0] }}
                                </form-error>

                            </div>
                        </div>
                    </div>
                    <div>
                        <button v-if="!isLastStep" class="btn btn-primary" @click="nextStep">
                            Continuar
                        </button>
                        <button v-if="!isFirstStep" class="btn" @click.prevent="previousStep">
                            Atras
                        </button>
                        <button v-if="isLastStep" class="btn btn-primary" @click.prevent="save">
                            Enviar
                        </button>
                    </div>
                </form>
                <!-- v-else-if="submitted" -->
                <div v-else-if="submitted">
                    <h3 class="tw-p-5 text-lg tw-text-teal-900 tw-bg-teal-100">
                        Gracias por registrarse.. lo estaremos contactando para completar la cita.
                    </h3>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
    props: [],
    data() {
        return {
            invalids: {},
            fields: {
                // tipo_identificacion: {
                //     label: 'Tipo de identificación',
                //     value: '01',
                //     type: 'select',
                //     changeEvent: () => {},
                //     options: [],
                //     validations: [
                //         {
                //             message: 'Tipo de identificación es requerido',
                //             test: (value) => value
                //         }
                //     ]
                // },
                ide: {
                    label: 'Cédula',
                    value: '',
                    type: 'text',
                    blurEvent: this.searchPatient,
                    validations: [
                        {
                            message: 'Cédula es requerido',
                            test: (value) => value
                        }
                    ]
                },
                first_name: {
                    label: 'Nombre',
                    value: '',
                    type: 'text',
                    blurEvent: () => {
                    },
                    validations: [
                        {
                            message: 'Nombre es requerido',
                            test: (value) => value
                        }
                    ]
                },
                birth_date: {
                    label: 'Fecha de nacimiento',
                    value: '',
                    type: 'date',
                    validations: [
                        {
                            message: 'Fecha de nacimiento es requerido',
                            test: (value) => value
                        }
                    ]
                },


                gender: {
                    label: 'Sexo',
                    value: '',
                    type: 'select',
                    changeEvent: () => {
                    },
                    options: [
                        {
                            label: 'Masculino',
                            value: 'm'
                        },
                        {
                            label: 'Femenino',
                            value: 'f'
                        },

                    ],
                    validations: [
                        {
                            message: 'Sexo es requerido',
                            test: (value) => value
                        }
                    ]
                },
                phone_number: {
                    label: 'Teléfono',
                    value: '',
                    type: 'text',
                    blurEvent: () => {
                    },
                    validations: [
                        {
                            message: 'Teléfono es requerido',
                            test: (value) => value
                        }
                    ]
                },
                province: {
                    label: 'Provincia',
                    value: '5',
                    type: 'select',

                    options: [],//window.provincias.map(item => { return { value: item.id, label: item.title }; }),
                    changeEvent: this.onChangeProvince,
                    validations: [
                        {
                            message: 'Provincia es requerido',
                            test: (value) => value
                        }
                    ]
                },
                canton: {
                    label: 'Canton',
                    value: '',
                    type: 'select',
                    options: [],
                    changeEvent: this.onChangeCanton,
                    validations: [
                        {
                            message: 'Canton es requerido',
                            test: (value) => value
                        }
                    ]
                },
                district: {
                    label: 'Distrito',
                    value: '',
                    type: 'select',
                    changeEvent: () => {
                    },
                    options: [],
                    validations: [
                        {
                            message: 'Distrito es requerido',
                            test: (value) => value
                        }
                    ]
                },
                visit_location: {
                    label: 'Lugar de visita',
                    value: '',
                    type: 'text',
                    blurEvent: () => {
                    },
                    validations: [
                        {
                            message: 'Lugar de visita es requerido',
                            test: (value) => value
                        }
                    ]
                },
                coords: {
                    label: 'Ubicación en mapa',
                    value: '',
                    type: 'coords',
                    validations: [
                        // {
                        //     message: 'La ubicación es requerido',
                        //     test: (value) => value
                        // }
                    ]
                },
                // responsable_tipo_identificacion: {
                //     label: 'Tipo de identificación',
                //     value: '01',
                //     type: 'select',
                //     changeEvent: () => {},
                //     options: [],
                //     validations: [
                //         {
                //             message: 'Tipo de identificación es requerido',
                //             test: (value) => value
                //         }
                //     ]
                // },
                responsable_ide: {
                    label: 'Cédula',
                    value: '',
                    type: 'text',
                    blurEvent: this.searchResponsable,
                    validations: [
                        {
                            message: 'Cédula es requerido',
                            test: (value) => value
                        }
                    ]
                },
                responsable_name: {
                    label: 'Nombre',
                    value: '',
                    type: 'text',
                    blurEvent: () => {
                    },
                    validations: [
                        {
                            message: 'Nombre del responsable es requerido',
                            test: (value) => value
                        }
                    ]
                },
                responsable_birth_date: {
                    label: 'Fecha de nacimiento',
                    value: '',
                    type: 'date',
                    validations: [
                        {
                            message: 'Fecha de nacimiento es requerido',
                            test: (value) => value
                        }
                    ]
                },
                responsable_gender: {
                    label: 'Sexo',
                    value: '',
                    type: 'select',
                    changeEvent: () => {
                    },
                    options: [
                        {
                            label: 'Masculino',
                            value: 'm'
                        },
                        {
                            label: 'Femenino',
                            value: 'f'
                        },

                    ],
                    validations: [
                        {
                            message: 'Sexo es requerido',
                            test: (value) => value
                        }
                    ]
                },
            },
            steps: [
                {
                    id: 1,
                    title: 'Datos del Paciente',
                    fieldKeys: ['ide', 'first_name', 'birth_date', 'gender', 'province', 'canton', 'district', 'visit_location', 'coords', 'phone_number'],
                },
                {
                    id: 2,
                    title: 'Datos del padre o Responsable (Aplica para menores de edad o adulto mayor)',
                    fieldKeys: ['responsable_ide', 'responsable_name', 'responsable_birth_date', 'responsable_gender'],
                },


            ],
            currentStep: 0,
            submitted: false,
            isPatient: '',
            tipoIdentificaciones: [],
            provincias: window.provincias,
            cantones: [],
            distritos: [],
            loader: false,
            errors: [],
            reference_code: ''


        };
    },
    components: {
        // Loading,
        flatPickr,


    },

    computed: {
        filteredSteps() {

            return this.isPatient === 'yes' ? this.steps.filter(s => s.id != 2) : this.steps;
        },
        filteredProvincias() {
            const availableProvincias = ['Guanacaste', 'Alajuela'];
            return this.provincias.filter(c => availableProvincias.includes(c.title)).map(item => {
                return {value: item.id, label: item.title};
            });
        },
        filteredCantones() {
            const availableCantones = ['Liberia', 'Bagaces', 'Upala'];
            return this.cantones.filter(c => availableCantones.includes(c.title)).map(item => {
                return {value: item.title, label: item.title};
            });
        },
        filteredDistritos() {
            const availableDistritos = ['Liberia', 'Bagaces', 'Mogote', 'Fortuna', 'Upala', 'Aguas Claras', 'San José', 'Bijagua', 'Canalete'];
            return this.distritos.filter(d => availableDistritos.includes(d.title)).map(item => {
                return {value: item.title, label: item.title};
            });
        },
        currentFields() {
            return this.steps[this.currentStep].fieldKeys;
        },
        totalSteps() {
            return this.filteredSteps.length;
        },
        isFirstStep() {
            return this.currentStep === 0;
        },
        isLastStep() {
            return this.currentStep === this.totalSteps - 1;
        },
        isInvalid() {
            return !!Object.values(this.invalids).filter((key) => key).length;
        },
    },
    methods: {
        previousStep() {
            if (this.isFirstStep) return;
            // removes all invalids so doesn't show error messages on back
            this.invalids = {};
            this.currentStep--;
        },
        nextStep() {
            if (this.isLastStep) return;
            this.validate();
            if (this.isInvalid) return;
            this.currentStep++;
        },
        validate() {
            this.invalids = {};
            // validates all the fields on the current page
            this.currentFields.forEach((key) => {
                this.validateField(key);
            });
        },
        validateField(fieldKey) {
            this.invalids[fieldKey] = false;
            const field = this.fields[fieldKey];
            // run through each of the fields validation tests
            field.validations.forEach((validation) => {
                if (!validation.test(field.value)) {
                    this.invalids[fieldKey] = validation.message;
                }
            });
        },
        getGeolocation() {

            navigator.geolocation.getCurrentPosition((geo) => {
                console.log(geo);
                this.fields.coords.value = geo.coords.latitude + ',' + geo.coords.longitude;

            }, (err) => {
                console.error('Error consultando la geolocalización', err);
                alert('Error consultando la geolocalización. Verifica que tienes activada la geolocalización en tu navegador. Error:' + err.message);
            }, {enableHighAccuracy: true});

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
            this.fields.canton.options = this.filteredCantones;
        },
        onChangeCanton: function (event) {

            var dist = [];

            this.cantones.forEach(function (cant) {
                if (event.currentTarget.value === cant.title) {
                    dist = cant.distritos;
                }
            });

            this.distritos = dist;
            this.fields.district.options = this.filteredDistritos;
        },
        clear() {


        },

        // loadTipoIdentificaciones() {

        //     axios.get('/identificaciones/tipos')
        //         .then(({ data }) => {

        //             const tipoIdentificaciones =  Object.entries((data)).map(item => { return { value: item[0], label: item[1] };});

        //             this.fields.tipo_identificacion.options = tipoIdentificaciones;
        //             this.fields.responsable_tipo_identificacion.options = tipoIdentificaciones;

        //             // this.setPatient();


        //         });
        // },

        searchPatient() {


            const instance = axios.create();
            instance.defaults.headers.common = {};
            instance.defaults.headers.common.accept = 'application/json';


            instance.get('https://api.hacienda.go.cr/fe/ae?identificacion=' + this.fields.ide.value)
                .then(({data}) => {

                    console.log(data);
                    this.fields.first_name.value = data.nombre;


                }).catch(error => {

                    flash('Ocurrio un error en la consulta!!', 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });


        },
        searchResponsable() {


            const instance = axios.create();
            instance.defaults.headers.common = {};
            instance.defaults.headers.common.accept = 'application/json';


            instance.get('https://api.hacienda.go.cr/fe/ae?identificacion=' + this.fields.responsable_ide.value)
                .then(({data}) => {

                    console.log(data);

                    this.fields.responsable_name.value = data.nombre;
                }).catch(error => {

                    flash('Ocurrio un error en la consulta!!', 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });


        },
        save() {

            this.validate();

            if (this.isInvalid) return;

            if (this.loader) {
                return;
            }

            this.loader = true;
            //let form = {};
            const form = Object.assign({}, ...Object.keys(this.fields).map(k => ({
                [k]: this.fields[k].value,
                is_patient: this.isPatient
            })));

            form.pharmacy_code = this.reference_code;

            axios.post('/lab/appointment-requests/register', form)

                .then(() => {
                    this.loader = false;
                    this.errors = [];
                    // this.clear();
                    this.submitted = true;
                    flash('Solicitud Enviada.');
                    this.reference_code = '';
                })
                .catch(error => {
                    this.loader = false;
                    this.submitted = false;
                    flash('Error al enviar la solicitud', 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });


        },
        getReferenceCode() {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);

            if (urlParams.has('pharmacy_code')) {
                this.reference_code = urlParams.get('pharmacy_code');
            }
        }


    },

    created() {
        // this.loadTipoIdentificaciones();
        this.getReferenceCode();
        this.fields.province.options = this.filteredProvincias;
        var cant = [];
        this.provincias.forEach((prov) => {
            if (this.fields.province.value === prov.id) {
                cant = prov.cantones;
            }
        });

        this.cantones = cant;
        this.fields.canton.options = this.filteredCantones;

    }

};
</script>
<style>
.error {
    color: #C23321;
}
</style>
  
  