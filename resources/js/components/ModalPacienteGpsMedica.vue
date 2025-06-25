<template>
    <div id="modalPacienteGpsMedica" aria-labelledby="modalPacienteGpsMedica" class="modal fade" role="dialog">
        <div class="modal-dialog " role="document">
            <div class="modal-content">

                <div class="modal-header">

                    <h4 id="modalPacienteGpsMedicaLabel" class="modal-title">Es Paciente de Doctor Blue</h4>

                </div>

                <div class="modal-body">
                    <loading :show="loader"></loading>
                    <div class="callout callout-warning">

                        <p v-if="possiblePatient.isPatient"> {{ possiblePatient.first_name }} se encuentra en la base de datos de pacientes pero no tiene cuenta asignada. Deseas crearle una cuenta y compartir el link de la aplicación con los siguientes datos:</p>
                        <p v-if="!possiblePatient.isPatient"> {{ possiblePatient.first_name }} no esta registrado como paciente en nuestras bases de datos. Deseas crearle una cuenta y compartir el link de la aplicación con los siguientes datos:</p>
                    </div>

                    <form action="#" @submit.prevent="createAccount()">
                        <div class="row">

                            <div class="form-group">
                                <label class="col-sm-12" for="first_name">Identificación</label>
                                <div class="col-sm-6">
                                    <div class="form-group">

                                        <select v-model="account.tipo_identificacion" class="form-control custom-select" required>

                                            <option v-for="(value, key) in tipoIdentificaciones" :key="key" :value="key">
                                                {{ value }}
                                            </option>

                                        </select>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input v-model="account.ide" class="form-control" placeholder="Cédula" required type="text">
                                    </div>

                                </div>

                            </div>

                        </div>
                        <div class="row">

                            <div class="form-group col-md-12">
                                <label for="first_name">Nombre</label>



                                <div class="form-group">
                                    <input v-model="account.first_name" class="form-control" placeholder="" required type="text">
                                </div>



                            </div>

                        </div>
                        <div class="row">

                            <div class="form-group col-md-12">
                                <label for="pay_with">Correo</label>
                                <input v-model="account.email" class="form-control" placeholder="" required type="email">
                                <form-error v-if="errors.email" :errors="errors">
                                    {{ errors.email[0] }}
                                </form-error>
                            </div>


                        </div>
                        <div class="row">

                            <div class="form-group">
                                <label class="col-sm-12" for="phone_number">Teléfono</label>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <select v-model="account.phone_country_code" class="form-control " name="phone_country_code" required style="width: 100%;">

                                            <option v-for="item in phoneCodes" :key="item.id" v-bind:value="item.value"> {{ item.text }}</option>

                                        </select>
                                        <form-error v-if="errors.phone_country_code" :errors="errors">
                                            {{ errors.phone_country_code[0] }}
                                        </form-error>
                                    </div>

                                </div>
                                <div class="col-sm-7">
                                    <div class="form-group">
                                        <input v-model="account.phone_number" class="form-control" name="phone_number" placeholder="Teléfono Celular" required type="text">
                                        <form-error v-if="errors.phone_number" :errors="errors">
                                            {{ errors.phone_number[0] }}
                                        </form-error>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <div class="form-group col-md-12">
                                <label for="pay_with">Contraseña</label>
                                <input v-model="account.password" class="form-control" placeholder="Si la dejas en blanco es el número de teléfono" type="password">
                            </div>

                        </div>
                        <div class="row">

                            <div class="form-group col-md-12">
                                <button class="btn btn-primary" type="submit">Crear cuenta</button>
                            </div>

                        </div>
                    </form>




                </div>
                <div class="modal-footer">

                    <button class="btn btn-default btn-close-modal" data-dismiss="modal" type="button">Cerrar</button>





                </div>
            </div>
        </div>
    </div>
</template>
<script>

import Loading from './Loading.vue';

export default {
    props: ['tipoIdentificaciones'],
    data() {
        return {
            possiblePatient: {},
            account: {
                tipo_identificacion: '01',
                ide: '',
                first_name: '',
                email: '',
                phone_country_code: '+506',
                phone_number: ''
            },
            phoneCodes: [
                {
                    text: '+506',
                    value: '+506'
                }
            ],
            loader: false,
            errors: []


        };
    },
    components: {
        Loading
    },

    methods: {


        disableFields() {

            return (this.invoice.id);
        },

        createAccount() {
            //this.$emit('saveResumenFactura');
            // $(this.$el).find('.btn-close-modal').click();

            if (this.loader) {
                return;
            }

            this.loader = true;

            if (this.possiblePatient.isPatient) {
                axios.post('/general/patients/' + this.possiblePatient.id + '/account', this.account)

                    .then(() => {

                        this.loader = false;
                        this.errors = [];

                        flash('Cuenta Creada.');

                        $(this.$el).find('.btn-close-modal').click();

                        window.location = '/invoices';

                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar cuenta', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });
            } else {


                this.account.gender = 'm';
                this.account.birth_date = moment().format('YYYY-MM-DD');
                this.account.province = '5';

                axios.post('/general/patients', this.account)

                    .then(() => {

                        this.loader = false;
                        this.errors = [];

                        flash('Paciente y Cuenta Creada.');

                        $(this.$el).find('.btn-close-modal').click();

                        window.location = '/invoices';
                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar paciente y cuenta', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            }


        },

        buildAccountPatient(data) {
            this.possiblePatient = data;

            if (this.possiblePatient.patientHasAccount) return;

            this.account.ide = this.possiblePatient.ide;
            this.account.tipo_identificacion = this.possiblePatient.tipo_identificacion;
            this.account.first_name = this.possiblePatient.first_name;
            this.account.email = this.possiblePatient.email;
            this.account.phone_number = this.possiblePatient.phone_number;
            this.account.phone_country_code = this.possiblePatient.phone_country_code;



        }

    },
    created() {
        this.emitter.on('showPacienteGpsMedicaModal', this.buildAccountPatient);


    }
};
</script>
