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
                        <input v-model="glicemia" class="form-control" name="glicemia" placeholder="Glicemia"
                               type="text">
                        <form-error v-if="errors.glicemia" :errors="errors" style="float:right;">
                            {{ errors.glicemia[0] }}
                        </form-error>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <select id="condition" v-model="condition" class="form-control" name="condition">
                            <option value="">-- Condición de la muestra</option>
                            <option value="En Ayunas">En Ayunas</option>
                            <option value="Al Azar">Al Azar</option>
                        </select>
                        <form-error v-if="errors.condition" :errors="errors" style="float:right;">
                            {{ errors.condition[0] }}
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
            glicemia: '',
            condition: '',
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

            axios.post(`${this.endpoint}/${this.patient.id}/sugars`, {
                date_control: this.date_control,
                time_control: this.time_control,
                glicemia: this.glicemia,
                condition: this.condition,

            })
                .then(({data}) => {
                    this.loader = false;
                    this.errors = [];

                    this.glicemia = '';
                    this.condition = '';


                    flash('Glicemia Guardada.');
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
