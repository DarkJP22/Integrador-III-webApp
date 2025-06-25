<template>
    <div class="register-box">


        <div class="register-box-body">
            <p class="login-box-msg">Solicitud de Cita</p>

            <form role="form" method="POST" @submit.prevent="save">


                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="paciente" v-model="form.patient_name" required readonly>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>

                </div>
                <div class="form-group has-feedback">
                    <flat-pickr v-model="form.date" class="form-control" placeholder="Selecione fecha y hora" :config="configDate" name="inscription">
                    </flat-pickr>
                    <span class="glyphicon glyphicon-calendar form-control-feedback"></span>
                    <form-error v-if="errors.date" :errors="errors">
                        {{ errors.date[0] }}
                    </form-error>
                </div>


                <div class="row">

                    <div class="col-xs-12" v-if="ok">
                        <div class="tw-text-center tw-text-4xl tw-text-teal-500 tw-font-bold">
                            Cita Creada Correctamente
                        </div>

                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat" v-if="!ok">Registrar</button>
                    </div>
                    <!-- /.col -->
                </div>

            </form>



        </div>
        <!-- /.form-box -->
    </div>

</template>

<script>
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
    components: {
        flatPickr
    },
    props: {
        user: {
            type: Object,
            required: true
        },
        medic: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            loader: false,
            errors: [],
            form: {
                patient_name: this.user ? this.user.name : ''
            },
            configDate: {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
            },
            ok: false
        };
    },
    methods: {
        save() {
            if (this.loader) return;

            this.loader = true;
            this.errors = [];
            axios.post(`/appointments/request/${this.medic.id}/${this.user.id}`, this.form).then(() => {
                this.loader = false;
                this.ok = true;

                flash('Cita creada Correctamente', 'success');
            }).catch((error) => {
                this.loader = false;
                flash('Error enviando la cita', 'danger');
                this.errors = error.response.data.errors ? error.response.data.errors : [];
            });

        }
    }
};
</script>

<style>

</style>