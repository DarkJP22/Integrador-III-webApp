<template>
    <div class="invoice-form">
        <loading :show="loader"></loading>
        <form @submit.prevent="save()">

            <div class="box box-default">
                <div class="box-header">

                </div>
                <div class="box-body">
                    <div>
                        <div class="form-row">

                            <div class="form-group col-md-4">
                                <label for="cliente">Identificación</label>

                                <input type="text" class="form-control" id="identificacion_cliente" placeholder="" v-model="affiliation.identificacion_cliente" @keydown.prevent.enter="searchCustomer(affiliation.identificacion_cliente)">

                                <form-error v-if="errors.patient_id" :errors="errors" style="float:right;">
                                    {{ errors.patient_id[0] }}
                                </form-error>

                            </div>
                            <div class="form-group col-md-4">
                                <label for="email">Nombre Afiliado Titular</label>

                                <div class="input-group input-group">
                                    <input type="text" class="form-control" name="cliente" v-model="affiliation.name" @keydown.prevent.enter="searchCustomer(affiliation.name)">
                                    <div class="input-group-btn">
                                        <button type="button" data-toggle="modal" data-target="#customersModal" class="btn btn-primary" @click="showModalCustomers()">Buscar</button>
                                    </div>
                                </div>
                                <form-error v-if="errors.patient_id" :errors="errors" style="float:right;">
                                    {{ errors.patient_id[0] }}
                                </form-error>



                            </div>
                            <div class="form-group col-md-4">
                                <label for="affiliation_plan_id">Tipo de plan</label>
                                <select class="form-control custom-select" name="affiliation_plan_id" id="tipo_identificacion_cliente" v-model="plan" @change="changeTipoPlan()">

                                    <option value=""></option>
                                    <option v-for="(plan, index) in tipoPlan" :value="plan" :key="index">
                                        {{ plan.name }}
                                    </option>

                                </select>
                                <form-error v-if="errors.affiliation_plan_id" :errors="errors" style="float:right;">
                                    {{ errors.affiliation_plan_id[0] }}
                                </form-error>
                            </div>
                        </div>

                        <div class="form-row" v-if="affiliation.patient_id">

                            <div class="form-group col-md-4">
                                <label for="inscription">Fecha inscripción</label>

                                <flat-pickr v-model="affiliation.inscription" class="form-control" placeholder="Selecione una fecha" name="inscription">
                                </flat-pickr>

                                <form-error v-if="errors.inscription" :errors="errors" style="float:right;">
                                    {{ errors.inscription[0] }}
                                </form-error>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="period">Periodo (M)</label>

                                <input type="text" class="form-control" name="period" v-model="affiliation.period" disabled>




                            </div>
                            <div class="form-group col-md-4">
                                <label for="period">Cuota</label>

                                <input type="text" class="form-control" name="cuota" v-model="affiliation.cuota" disabled>




                            </div>
                        </div>

                        <div class="form-row" v-if="affiliation.patient_id">

                            <div class="form-group col-md-4">
                                <label for="phone">Telefono</label>

                                <input type="text" class="form-control" name="period" v-model="affiliation.phone" disabled>


                            </div>
                            <div class="form-group col-md-4">
                                <label for="address">Lugar de residencia</label>

                                <input type="text" class="form-control" name="city" v-model="affiliation.address" disabled>




                            </div>
                            <div class="form-group col-md-4">
                                <label for="email">Correo</label>

                                <input type="email" class="form-control" name="email" v-model="affiliation.email" disabled>




                            </div>
                        </div>
                    </div>
                    <div v-if="affiliation.patient_id">
                        <div class="afiliados-header">
                            <h3>Personas Afiliadas</h3>
                            <div class="flex-container-sb">
                                <div>
                                    <button type="button" data-toggle="modal" data-target="#customersModal" class="btn btn-primary btn-sm" @click="showModalCustomersOtras()">Agregar otra</button>
                                </div>



                            </div>

                        </div>


                        <div class="table-responsive">


                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-2 text-left">CEDULA</th>
                                        <th class="py-2 px-2 text-left">NOMBRE</th>
                                        <th class="py-2 px-2 text-left"></th>

                                    </tr>
                                </thead>
                                <tbody>


                                    <tr class="border-t" v-for="(patient, index) in patients" :key="patient.id">
                                        <td class="py-2 px-2">{{ patient.ide }}</td>
                                        <td class="py-2 px-2">{{ patient.fullname }}</td>

                                        <td class="py-2 px-2">

                                            <button type="button" class="btn btn-danger btn-sm" @click="removePatient(patient, index)">Quitar</button>


                                        </td>

                                    </tr>


                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="payments" v-show="affiliation.id">
                        <affiliation-payments :affiliation="affiliation" :medio-pagos="medioPagos"></affiliation-payments>
                    </div>
                    <div class="transactions" v-show="affiliation.id">
                        <affiliation-transactions :affiliation="affiliation"></affiliation-transactions>
                    </div>

                </div>
                <div class="box-footer">
                    <div class="form-row">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <a href="/affiliations" class="btn btn-default">Regresar</a>
                        </div>

                    </div>

                </div>

            </div>

        </form>
        <customers-modal @assigned="addCliente"></customers-modal>
    </div>


</template>

<script>
import AffiliationPayments from './AffiliationPayments.vue';
import AffiliationTransactions from './AffiliationTransactions.vue';
import CustomersModal from './CustomersModal.vue';
import FormError from './FormError.vue';
import Loading from './Loading.vue';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
    props: ['currentAffiliation', 'tipoPlan', 'currentOffice', 'medioPagos'],
    components: {
        FormError,
        Loading,
        flatPickr,
        CustomersModal,
        AffiliationPayments,
        AffiliationTransactions
    },
    data() {
        return {
            loader: false,
            errors: [],
            affiliation: {
                name: '',
                tipo_identificacion_cliente: '',
                identificacion_cliente: '',
                affiliation_plan_id: '',
                patient_id: '',
                office_id: this.currentOffice ? this.currentOffice.id : 0,
                inscription: '',
                period: 0,
                cuota: 0,
                acumulado: 0,
                discount: 0,
                patients: []
            },
            plan: {},
            patients: [],
            otrasAfiliadas: false
        };
    },
    methods: {
        changeTipoPlan() {
            this.affiliation.affiliation_plan_id = this.plan.id;
            this.affiliation.period = this.plan.period;
            this.affiliation.cuota = parseFloat(this.plan.cuota);
            this.affiliation.discount = parseFloat(this.plan.discount);
            this.errors = [];
        },
        searchCustomer(q) {

            this.otrasAfiliadas = false;

            axios.get(`/invoices/patients?q=${q}`)
                .then(({ data }) => {

                    this.affiliation.patient_id = 0;
                    this.affiliation.email = '';
                    //this.invoice.cliente = '';

                    if (data.data) {

                        if (data.data.length == 1) {
                            if (data.data[0].affiliations.length) {
                                flash('Este paciente ya tiene una afiliacion creada. Verifica!', 'danger');
                            } else {
                                this.addCliente(data.data[0]);
                                flash('Paciente Agregado');
                            }

                        } else {
                            //flash('Paciente No encontrado', 'danger');
                            $('#customersModal').modal();
                            this.emitter.emit('showCustomersModal', { searchTerm: q });
                        }

                    } else {
                        flash('Paciente No encontrado', 'danger');
                    }
                });
        },
        addCliente(cliente) {
            this.errors = [];

            if (cliente.affiliations && cliente.affiliations.length) {
                flash('Este paciente ya tiene una afiliacion creada. Verifica!', 'danger');
                return;
            }

            if (this.plan && this.plan.persons <= this.patients.length) {
                this.otrasAfiliadas = false;
                flash('No puedes agregar mas personas limite de plan (' + this.plan.persons + ')!', 'danger');
                return;
            }

            if (!this.otrasAfiliadas) {
                this.affiliation.patient_id = cliente.id;
                this.affiliation.name = cliente.fullname;
                this.affiliation.email = cliente.email;
                this.affiliation.tipo_identificacion_cliente = '01';
                this.affiliation.identificacion_cliente = cliente.ide;
                this.affiliation.address = cliente.city;
                this.affiliation.phone = cliente.phone_number;
            }
            const patientIndexFound = _.findIndex(this.patients, function (o) {
                return o.id === cliente.id;
            });
            const patientFound = _.find(this.patients, function (o) {
                return o.id === cliente.id;
            });

            if (patientFound && patientIndexFound !== -1) {
                return;
            }

            this.patients.push(cliente);
            this.otrasAfiliadas = false;


        },
        removePatient(patient, index) {
            if (patient.id == this.affiliation.patient_id) {
                flash('No se puede quitar la persona titular de la afiliación', 'danger');
                return;
            }

            const indexItem = index ? index : _.findIndex(this.patients, { 'id': patient.id });

            this.patients.splice(indexItem, 1);

        },
        showModalCustomersOtras() {
            // debugger
            this.otrasAfiliadas = true;
            this.emitter.emit('showCustomersModal', '');

        },
        showModalCustomers() {
            this.otrasAfiliadas = false;
            this.emitter.emit('showCustomersModal', '');

        },

        save() {

            if (this.loader) { return; }
            this.loader = true;

            this.affiliation.patients = [];

            this.patients.forEach(element => {
                this.affiliation.patients.push(element.id);
            });

            if (this.affiliation.id) {

                axios.put(`/affiliations/${this.affiliation.id}`, this.affiliation)
                    .then(({ data }) => {
                        this.loader = false;
                        //this.clearForm();
                        flash('Afiliacion Guardada Correctamente.');
                        this.$emit('updated', data);

                        this.prepareData(data);



                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar la afiliacion!!', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            } else {

                axios.post('/affiliations', this.affiliation)
                    .then(({ data }) => {
                        this.loader = false;
                        // this.clearForm();
                        flash('Afiliación Guardada Correctamente.');
                        this.$emit('created', data);

                        this.prepareData(data);




                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar la Afiliación!!', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            }
        },
        prepareData(currentAffiliation) {
            const patientIds = [];
            this.patients = [];

            this.affiliation = currentAffiliation;
            this.affiliation.cuota = parseFloat(currentAffiliation.plan.cuota),
            this.affiliation.period = currentAffiliation.plan.period,
            this.affiliation.name = currentAffiliation.holder.fullname,
            this.affiliation.identificacion_cliente = currentAffiliation.holder.ide;
            this.affiliation.affiliation_plan_id = currentAffiliation.affiliation_plan_id;
            this.plan = currentAffiliation.plan;
            this.affiliation.phone = currentAffiliation.holder.phone_number;
            this.affiliation.address = currentAffiliation.holder.city;
            this.affiliation.email = currentAffiliation.holder.email;
            //this.affiliation.patients = [];
            currentAffiliation.patients.forEach(element => {
                patientIds.push(element.id);
                this.patients.push(element);
            });
            this.affiliation.patients = patientIds;
        }
    },
    created() {
        if (this.currentAffiliation) {
            this.prepareData(this.currentAffiliation);
        }
    }

};
</script>

