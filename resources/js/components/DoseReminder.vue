<template>
    <div id="dosisModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="dosisModalLabel" class="modal-title">Dosis</h4>
                    <div v-if="!dataLine.active" class="callout callout-danger">
                        <p>Notificación inactiva de parte del paciente</p>
                    </div>

                </div>
                <div class="modal-body">
                    <form autocomplete="off" class="form" @submit.prevent="save()">
                        <loading :show="loader"></loading>


                        <div class="row">


                            <div class="form-group col-md-4">
                                <label class="control-label" for="dose_schema">Esquema Dosis</label>


                                <select v-model="dataLine.schema" autocomplete="off" class="form-control" name="dose_schema" required @change="onChangeSchema(dataLine)">
                                    <option value="1">1 vez al día</option>
                                    <option value="2">2 veces al día</option>
                                    <option value="3">3 veces al día</option>
                                    <option value="4">4 veces al día</option>
                                    <option value="5">5 veces al día</option>
                                    <option value="6">6 veces al día</option>
                                </select>


                                <form-error v-if="errors.schema" :errors="errors" style="float:right;">
                                    {{ errors.schema[0] }}
                                </form-error>

                            </div>


                            <div class="form-group col-md-4">
                                <label class="control-label" for="dose_days">Dias de tratamiento</label>

                                <input v-model="dataLine.days" autocomplete="off" class="form-control" min="1" name="start_at" placeholder="" required type="number"/>


                                <form-error v-if="errors.days" :errors="errors" style="float:right;">
                                    {{ errors.days[0] }}
                                </form-error>

                            </div>


                            <div class="form-group col-md-4">
                                <label class="control-label" for="dose_start_at">Fecha de inicio</label>


                                <!-- <input type="text" class="form-control datepicker" name="start_at" placeholder="" v-model="dataLine.start_at" @blur="onBlurDate($event)" autocomplete="off" required /> -->
                                <flat-pickr
                                    v-model="dataLine.start_at"
                                    class="form-control"
                                    name="dose_start_at"
                                    placeholder="Select date">
                                </flat-pickr>


                                <form-error v-if="errors.start_at" :errors="errors" style="float:right;">
                                    {{ errors.start_at[0] }}
                                </form-error>

                            </div>


                        </div>

                        <div class="row">


                            <div v-for="(n, index) in fieldsHours(dataLine)" :key="n" class="form-group col-md-4">
                                <label class="control-label" for="dose_hours">Hora {{ index + 1 }}</label>
                                <!-- <input type="text" class="form-control timepicker" name="medicine" placeholder="00:00" v-model="dataLine.hours[index]" autocomplete="off" required /> -->
                                <flat-pickr
                                    v-model="dataLine.hours[index]"
                                    :config="configTimePicker"
                                    class="form-control"
                                    placeholder="00:00"


                                >
                                </flat-pickr>
                            </div>


                        </div>


                    </form>
                </div>
                <div class="modal-footer">

                    <button class="btn btn-link waves-effect btn-close-modal" data-dismiss="modal" type="button">Cerrar</button>
                    <button class="btn btn-primary" type="button" @click="save()">Guardar</button>
                </div>
            </div>


        </div>
    </div>
</template>

<script>

import collection from '../mixins/collection';
import Loading from './Loading.vue';
import FormError from './FormError.vue';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
    //props:['patient'],
    components: {Loading, flatPickr, FormError},
    mixins: [collection],
    emits: ['doseUpdated'],
    data() {
        return {
            errors: [],
            loader: false,
            dataLine: {
                id: null,
                medicine_id: null,
                schema: 1,
                start_at: moment().format('YYYY-MM-DD'),
                days: 1,
                hours: ['06:00'],
                active: 1
            },
            configTimePicker: {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                static: true,
                defaultDate: '06:00'
            },


        };
    },
    mounted() {
        console.log('mounted');
    },
    created() {
        this.emitter.on('showDosisModal', (dataLine) => {
            this.dataLine.id = dataLine.id ?? null;
            this.dataLine.medicine_id = dataLine.medicine_id ?? null;
            this.dataLine.schema = dataLine.schema ?? 1;
            this.dataLine.start_at = dataLine.start_at ?? moment().format('YYYY-MM-DD');
            this.dataLine.days = dataLine.days ?? 1;
            this.dataLine.hours = dataLine.hours ?? ['06:00'];
            this.dataLine.active = dataLine.active;

        });
        // this.fetch();
    },
    computed: {
        // fieldsHorus(){
        //     return this.schema
        // }
    },
    methods: {
        save() {
            this.loader = true;
            if (this.dataLine.id) {
                axios.put(`/pharmacy/medicines/${this.dataLine.medicine_id}/dosereminders/${this.dataLine.id}`, this.dataLine)
                    .then(() => {
                        this.loader = false;
                        this.$emit('doseUpdated');

                        flash('Dosis guardada correctamente');
                        $('#dosisModal .btn-close-modal').click();

                    })
                    .catch((error) => {
                        this.loader = false;
                        flash('Error al guardar dosis', 'danger');
                        this.errors = error.response.data.errors;
                    });
            } else {
                axios.post(`/pharmacy/medicines/${this.dataLine.medicine_id}/dosereminders`, this.dataLine)
                    .then(({ data }) => {
                        this.loader = false;
                        this.$emit('doseUpdated', data);
                        flash('Dosis guardada correctamente');

                        $('#dosisModal .btn-close-modal').click();


                    })
                    .catch((error) => {
                        this.loader = false;
                        flash('Error al guardar dosis', 'danger');
                        this.errors = error.response.data.errors;
                    });
            }
        },
        fieldsHours(reminder) {
            const fields = [];

            for (let index = 0; index < reminder.schema; index++) {
                fields.push(index);
                //this.dataLine.hours[index] = '06:00';
            }
            return fields;
        },

        onChangeSchema(reminder) {
            reminder.hours = ['06:00'];
            this.fieldsHours(reminder);

        },


    }
};
</script>
