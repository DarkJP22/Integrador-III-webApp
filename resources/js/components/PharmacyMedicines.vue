<template>
    <div class="content">

        <pharmacy-medicine :patient="patient" @created="add" :presentations="presentations"></pharmacy-medicine>
        <h3>Lista de medicamentos</h3>


        <ul v-show="items.length" id="pmedicines-list" class="todo-list ui-sortable" style="overflow:visible;">

            <li v-for="(item, index) in items" :key="item.id">


                <div class="row">
                    <div class="col-sm-3">
                        <span> <span class="text">{{ item.name }}</span> <span class="date pull-right"> </span></span>
                        <div class="tw-mt-4">

                            <button class="btn btn-primary btn-sm" data-target="#dosisModal"
                                    data-toggle="modal" title="Dosis"
                                    type="button"
                                    @click="showModalDosis(item, index)">
                                Dosis
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-9 tw-flex tw-gap-2 tw-flex-wrap">
                        <div>
                            <span class="text">Recordar</span>
                            <select v-model="item.remember" class="form-control" @change="saveRemember(item, index)">
                                <option value="">-- Recordatorio --</option>
                                <option :value="true">Si</option>
                                <option :value="false">No</option>
                                <!-- <option value="14">Recordatorio en 14 dias</option>
                            <option value="28">Recordatorio en 28 dias</option> -->
                            </select>
                        </div>
                        <div class="callout callout-light tw-flex-1">
                            <p>Agregue recordatorios a los medicamentos que requiere tener a disposición del paciente en la fecha indicada</p>
                        </div>
                        <div v-if="item.remember" class="tw-flex tw-gap-2 tw-flex-wrap">
                            <div>
                                <span class="text">Próxima compra</span>
                                <flat-pickr
                                    v-model="item.date_purchase"
                                    class="form-control"
                                    name="date_purchase"
                                    placeholder="Selecione una fecha"
                                    @change="saveRemember(item, index)">
                                </flat-pickr>

                                <!--                            <select v-model="item.remember_days" class="form-control" @change="saveRemember(item, index)">-->
                                <!--                                <option value="0">&#45;&#45; Repetir &#45;&#45;</option>-->
                                <!--                                <option value="30">Repetir Cada Mes</option>-->


                                <!--                            </select>-->

                                <form-error v-if="errors.date_purchase" :errors="errors" style="float:right;">
                                    {{ errors.date_purchase[0] }}
                                </form-error>
                            </div>
                            <div>
                                <span class="text">Unid. Encargadas</span>
                                <input v-model="item.requested_units" class="form-control" min="1" type="number" @change="saveRemember(item, index)">
                                <form-error v-if="errors.requested_units" :errors="errors" style="float:right;">
                                    {{ errors.requested_units[0] }}
                                </form-error>
                            </div>
                            <div>
                                <span class="text">Recordar cada: (dias)</span>
<!--                                <input v-model="item.remember_days" class="form-control" type="number" @change="saveRemember(item, index)">-->
                                <select v-model="item.remember_days" class="form-control" @change="saveRemember(item, index)">
                                    <option :value="1">Una única vez</option>
                                    <option :value="7">7 días</option>
                                    <option :value="14">14 días</option>
                                    <option :value="28">28 días</option>
                                    <option :value="30">30 días</option>
                                    <option :value="60">60 días</option>
                                    <option :value="90">90 días</option>




                                </select>
                                <form-error v-if="errors.remember_days" :errors="errors" style="float:right;">
                                    {{ errors.remember_days[0] }}
                                </form-error>
                            </div>
                            <div>
                                <span class="text">Duración tratamiento (Meses)</span>
                                <select v-model="item.active_remember_for_days" :disabled="+item.remember_days === 1" class="form-control" @change="saveRemember(item, index)">
                                    <option :value="30">1 Mes</option>
                                    <option :value="60">2 Meses</option>
                                    <option :value="90">3 Meses</option>
                                    <option :value="-1">Crónico</option>



                                </select>
                                <form-error v-if="errors.active_remember_for_days" :errors="errors" style="float:right;">
                                    {{ errors.active_remember_for_days[0] }}
                                </form-error>
                            </div>
                        </div>


                    </div>
                    <div class="col-sm-4">
                        <span class="text">Acciones</span>
                        <div class="form-group">
                            <!-- <button href="#" class="btn btn-primary btn-sm" @click="dosis(item, index)">Dosis</button> -->
                            <button class="btn btn-primary btn-sm" @click="saveRemember(item, index)"><i
                                class="fa fa-save"></i></button>
                            <button v-if="authorize('owns', item)" class="btn btn-danger btn-sm"
                                    @click="destroy(item, index)"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="tools">
                    <!-- <i class="fa fa-trash-o delete" @click="destroy(item, index)" v-if="authorize('owns', item)"></i> -->
                </div>
            </li>

        </ul>
        <paginator :dataSet="dataSet" :no-update-url="true" @changed="fetch"></paginator>

        <dose-reminder @dose-updated="fetch"></dose-reminder>
    </div>
</template>
<script>
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import collection from '../mixins/collection';
import PharmacyMedicine from './PharmacyMedicine.vue';
import DoseReminder from './DoseReminder.vue';

export default {
    props: ['medicines', 'patient', 'presentations'],
    data() {
        return {
            items: this.medicines.data,
            dataSet: this.medicines,
            errors: []
        };
    },
    components: {PharmacyMedicine, flatPickr, DoseReminder},
    mixins: [collection],
    methods: {
        showModalDosis(line) {
            const data = {
                ...line.dosesreminder,
                medicine_id: line.id,
                // index: index,
            };
            this.emitter.emit('showDosisModal', data);
        },
        // dosis(item, index) {
        //
        //     Swal.fire({
        //         title: 'Receta / Dosis',
        //         input: 'textarea',
        //         inputPlaceholder: 'Receta / Dosis',
        //         showCancelButton: true,
        //         confirmButtonText: 'Guardar',
        //         confirmButtonColor: '#67BC9A',
        //         cancelButtonColor: '#dd4b39',
        //         showLoaderOnConfirm: true,
        //         inputValue: item.receta,
        //         // inputValidator: (value) => {
        //         //     return new Promise((resolve) => {
        //         //         if (value === '') {
        //         //             resolve('Necesitas agregar al menos un correo')
        //         //         } else {
        //         //             resolve()
        //         //         }
        //         //     })
        //         // },
        //         preConfirm: (message) => {
        //
        //
        //             return axios.put(`/pharmacy/medicines/${item.id}/receta`, {receta: message})
        //                 .then(({data}) => {
        //
        //                     this.update(data, index);
        //                 })
        //                 .catch(error => {
        //
        //                     Swal.showValidationError(
        //                         `Request failed: ${error}`
        //                     );
        //
        //                     flash('Error al actualizar!!', 'danger');
        //                 });
        //         },
        //         allowOutsideClick: () => !Swal.isLoading()
        //
        //     })
        //         .then((result) => {
        //
        //
        //             if (result.value) {
        //
        //                 Swal.fire({
        //                     title: 'Receta Actualizada Correctamente',
        //
        //                 }).then(() => {
        //
        //
        //                 });
        //
        //             }
        //
        //
        //         });
        //
        //
        // },

        saveRemember(medicine) {


            axios.put(`/pharmacy/medicines/${medicine.id}`, {
                remember: medicine.remember,
                remember_days: medicine.remember_days,
                date_purchase: medicine.date_purchase,
                active_remember_for_days: medicine.active_remember_for_days,
                requested_units: medicine.requested_units,

            })
                .then(() => {
                    this.loader = false;
                    this.errors = [];


                    flash('Recordatorio Actualizado');
                    //this.$emit('created', data);
                })
                .catch(error => {
                    flash(error.response.data.message, 'danger');

                });
        },
        destroy(item, index) {
            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                axios.delete(`/pharmacy/medicines/${item.id}`)
                    .then(() => {

                        this.remove(index);

                        flash('Medicamento Eliminado!');

                    }).catch(error => {

                        flash(error.response.data.message, 'danger');

                    });
            }

        },

        addTransferedMedicines(medicines) {


            this.items = medicines;

            // eslint-disable-next-line no-undef
            /* Vue.nextTick(function () {
                 $('.datepicker').datetimepicker({
                     format: 'YYYY-MM-DD',
                     locale: 'es',
                     //useCurrent: true,
                     //defaultDate: new Date(),
                 });
             });*/

        },
        fetch(page) {
            axios.get(this.url(page)).then(this.refresh);
        },
        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            return `/pharmacy/patients/${this.patient.id}/medicines?page=${page}`;
        },
        refresh({data}) {

            this.dataSet = data;
            this.items = data.data;

        },


    },
    created() {
        this.fetch();
        this.emitter.on('transferedMedicines', this.addTransferedMedicines);
        this.emitter.on('added', function () {

            // eslint-disable-next-line no-undef
            /* Vue.nextTick(function () {
                 $('.datepicker').datetimepicker({
                     format: 'YYYY-MM-DD',
                     locale: 'es',
                     //useCurrent: true,
                     //defaultDate: new Date(),
                 });
             });*/

        });
    }

};
</script>
