<template>
    <div>
        <form @submit.prevent="save()">
            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="input-group">
                            <input v-model="date_control" class="form-control datepicker" type="text"
                                   @blur="onBlurDate">

                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div>
                        <form-error v-if="errors.date_control" :errors="errors" style="float:right;">
                            {{ errors.date_control[0] }}
                        </form-error>
                    </div>
                </div>


                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="input-group">
                            <input v-model="time_control" class="form-control timepicker" type="text"
                                   @blur="onBlurHour">

                            <div class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                        </div>
                        <form-error v-if="errors.time_control" :errors="errors" style="float:right;">
                            {{ errors.time_control[0] }}
                        </form-error>
                    </div>

                </div>


            </div>

            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <input v-model="ps" class="form-control" name="ps" placeholder="P.S" type="text">
                        <form-error v-if="errors.ps" :errors="errors" style="float:right;">
                            {{ errors.ps[0] }}
                        </form-error>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <input v-model="pd" class="form-control" name="pd" placeholder="P.D" type="text">
                        <form-error v-if="errors.pd" :errors="errors" style="float:right;">
                            {{ errors.pd[0] }}
                        </form-error>
                    </div>

                </div>


            </div>
            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <input v-model="heart_rate" class="form-control" name="heart_rate" placeholder="Frecuencia Cardiaca"
                               type="text">
                        <form-error v-if="errors.heart_rate" :errors="errors" style="float:right;">
                            {{ errors.heart_rate[0] }}
                        </form-error>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <select id="condition" v-model="measurement_place" class="form-control" name="measurement_place">
                            <option value="">-- Lugar de la muestra</option>
                            <option value="Brazo Izquierdo">Brazo Izquierdo</option>
                            <option value="Brazo Derecho">Brazo Derecho</option>
                        </select>
                        <form-error v-if="errors.measurement_place" :errors="errors" style="float:right;">
                            {{ errors.measurement_place[0] }}
                        </form-error>
                    </div>
                </div>


            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <textarea v-model="observations" class="form-control" name="observations" placeholder="Observaciones"
                                  type="text"></textarea>

                        <form-error v-if="errors.observations" :errors="errors" style="float:right;">
                            {{ errors.observations[0] }}
                        </form-error>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <button :disabled="itemsLength >= this.limit" class="btn btn-primary" type="submit">Agregar
                        </button>
                        <img v-show="loader" alt="Cargando..." src="/img/loading.gif">
                        <span v-show="itemsLength >= this.limit" class="label label-warning">Haz alcanzado el limite de {{
                                limit
                            }} registros. Si deseas agregar más registros elimina los más antiguos</span>
                    </div>
                </div>
            </div>

        </form>
    </div>
</template>
<script>
export default {
    props: ['patient', 'itemsLength', 'url'],
    data() {
        return {
            date_control: moment().format('YYYY-MM-DD'),
            time_control: moment().format('HH:mm'),
            ps: '',
            pd: '',
            heart_rate: '',
            observations: '',
            measurement_place: '',
            errors: [],
            limit: 20,
            loader: false,
            endpoint: this.url ? this.url + '/patients' : '/patients'
        };
    },

    methods: {
        onBlurDate(e) {

            this.date_control = e.target.value;
            this.$emit('input');
        },
        onBlurHour(e) {
            this.time_control = e.target.value;
            this.$emit('input');
        },
        save() {

            axios.post(`${this.endpoint}/${this.patient.id}/pressures`, {
                date_control: this.date_control,
                time_control: this.time_control,
                ps: this.ps,
                pd: this.pd,
                heart_rate: this.heart_rate,
                measurement_place: this.measurement_place,
                observations: this.observations

            })
                .then(({data}) => {
                    this.loader = false;
                    this.errors = [];

                    this.ps = '';
                    this.pd = '';
                    this.heart_rate = '';
                    this.measurement_place = '';
                    this.observations = '';

                    flash('Presión Guardada.');
                    this.$emit('created', data);
                })
                .catch(error => {
                    flash(error.response.data.message, 'danger');
                    this.errors = error.response.data.errors;
                });

        }
    }
};
</script>
