<template>
    <div class="content">
        <form class="form" @submit.prevent="save()" autocomplete="off">
            <loading :show="loader"></loading>


            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="control-label">Esquema Dosis</label>


                        <select name="schema" class="form-control" v-model="reminder.schema"
                                @change="onChangeSchema(reminder)" autocomplete="off" required>
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

                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name" class="control-label">Dias de tratamiento</label>

                        <input type="number" class="form-control" name="start_at" min="1" placeholder=""
                               v-model="reminder.days" autocomplete="off" required/>


                        <form-error v-if="errors.days" :errors="errors" style="float:right;">
                            {{ errors.days[0] }}
                        </form-error>

                    </div>

                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name" class="control-label">Fecha de inicio</label>


                        <input type="text" class="form-control datepicker" name="start_at" placeholder=""
                               v-model="reminder.start_at" @blur="onBlurDate($event)" autocomplete="off" required/>


                        <form-error v-if="errors.start_at" :errors="errors" style="float:right;">
                            {{ errors.start_at[0] }}
                        </form-error>

                    </div>

                </div>


            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="">
                        <label for="name" class="control-label">Hora(s)</label>


                    </div>


                </div>
                <div class="hours">

                    <div v-for="(n, index) in fieldsHours(reminder)" :key="n" class="col-md-4">
                        <div class="form-group">
                            <input type="text" class="form-control timepicker" name="medicine" placeholder="00:00"
                                   v-model="reminder.hours[index]" @blur="onBlurTime(reminder.hours, index, $event)"
                                   autocomplete="off" required/>
                        </div>

                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>


                </div>
            </div>
        </form>
    </div>
</template>

<script>

import collection from '../mixins/collection';
import Loading from './Loading.vue';
import {nextTick} from 'vue';

export default {
    props: ['patient', 'medicine'],
    components: {Loading},
    mixins: [collection],
    data() {
        return {
            errors: [],
            loader: false,
            reminder: {
                medicine_id: this.medicine ? this.medicine.id : 0,
                schema: 1,
                start_at: moment().format('YYYY-MM-DD'),
                days: 1,
                hours: ['']
            },


        };
    },
    created() {
        // debugger
        if (this.medicine && this.medicine.dosesreminder) {
            this.reminder = this.medicine.dosesreminder;
            if (!this.reminder.hours) {
                this.reminder.hours = [''];
            }
        }
        this.setTimePicker();
        // this.fetch();
    },
    computed: {
        // fieldsHorus(){
        //     return this.schema
        // }
    },
    methods: {
        fieldsHours(reminder) {
            const fields = [];

            for (let index = 0; index < reminder.schema; index++) {
                fields.push(index);

            }
            return fields;
        },

        onChangeSchema(reminder) {
            reminder.hours = [];
            this.fieldsHours(reminder);
            this.setTimePicker();


        },
        onBlurTime(hours, index, e) {

            hours[index] = e.target.value;
            this.$emit('input');

        },
        onBlurDate(e) {

            this.reminder.start_at = e.target.value;
            this.$emit('input');

        },
        fetch(page) {
            axios.get(this.url(page)).then(this.refresh);
        },
        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            return `/patients/${this.patient.id}/dosereminders?page=${page}`;
        },
        refresh({data}) {

            this.reminders = data;

            this.setTimePicker();

        },
        async setTimePicker() {
            // eslint-disable-next-line no-undef
            await nextTick();

            $('.timepicker').datetimepicker({
                format: 'LT',
                locale: 'es',
                stepping: 60,

            });
        },
        async setDatePicker() {
            // eslint-disable-next-line no-undef
            await nextTick();
            $('.datepicker').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'es',


            });
        },
        save() {
            if (this.loader) {
                return;
            }

            this.loader = true;


            if (this.reminder.id) {

                axios.put('/patients/' + this.patient.id + '/dosereminders/' + this.reminder.id, this.reminder)

                    .then(({data}) => {
                        this.loader = false;
                        this.errors = [];
                        //this.clear();
                        this.reminder = data;
                        flash('Recordatorio de dosis guardado.');
                        this.$emit('updated', data);
                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar recordatorio de dosis', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            } else {

                axios.post('/patients/' + this.patient.id + '/dosereminders', this.reminder)

                    .then(({data}) => {
                        this.loader = false;
                        this.errors = [];
                        //this.clear();
                        this.reminder = data;
                        flash('Recordatorio de dosis guardado.');
                        this.$emit('created', data);
                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar recordatorio de dosis', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

            }

        }

    }
};
</script>
