<template>

    <div class="box box-default ">

        <div class="box-header with-border">
            <h3 class="box-title">Laboratorios y pruebas complementarias</h3>

            <!-- /.box-tools -->
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="form-group" v-show="!finishedRevalorizar">
                <div class="input-group flatpickr">
                    <input type="text" class="form-control" id="datetimepickerLabExam" v-model="date" @blur="onBlurDatetime" placeholder="Fecha" tabindex="1" data-input>

                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>

            </div>
            <div class="form-group" v-show="!finishedRevalorizar">

                <input type="text" class="form-control" v-model="name" placeholder="Examen a realizar" @keyup.enter="hit" tabindex="2">




            </div>

            <div class="form-group">
                <button @click="hit" class="btn btn-primary" v-show="!finishedRevalorizar" v-bind:disabled="loader">Agregar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader" tabindex="3">
            </div>
            <div class="box box-solid" v-for="(itemDate, index) in items" :key="index">
                <div class="box-header with-border">
                    <h3 class="box-title"> {{ formatDate(itemDate.date) }}</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <ul id="medicines-list" class="todo-list ui-sortable" v-show="itemDate.exams.length">

                        <li v-for="item in itemDate.exams" :key="item.id">
                            <!-- todo text -->
                            <span><span class="text"> {{ item.name }}</span></span>
                            <!-- General tools such as edit or delete-->
                            <div class="tools">

                                <i class="fa fa-trash-o delete" @click="remove(item)" v-show="!finishedRevalorizar" v-bind:disabled="loader"></i>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>



        </div>
    </div>




</template>

<script>

export default {
    //props: ['medicines','patient_id'],
    props: {
        patientId: {
            type: Number

        },
        appointmentId: {
            type: Number

        },
        exams: {
            type: Array


        },
        url: {
            type: String,
            default: '/medic/patients'
        },
        read: {
            type: Boolean,
            default: false
        },
        appointment: {
            type: Object,

        },
    },
    data() {
        return {
            date: '',
            file: '',
            name: '',
            items: [],
            loader: false


        };

    },

    computed: {
        finished() {


            if (this.appointment) {

                if (this.appointment.finished || this.read) {

                    return true;

                }

            }

            return false;


        },
        finishedRevalorizar() {


            if (this.appointment) {

                if (this.appointment.finished) {

                    if ((this.appointment.revalorizar && this.read) || !this.appointment.revalorizar) {
                        return true;
                    }

                }

            }

            return false;
        }
    },
    methods: {
        formatDate(date) {
            return moment(date).format('YYYY-MM-DD');
        },
        onBlurDatetime(e) {
            const value = e.target.value;
            console.log('onInput fired', value);

            //Add this line


            this.date = value;
            this.$emit('input');
        },
        hit() {
            console.log('hit');
            if (!this.date || !this.name)
                return;

            this.loader = true;
            this.add();

        },

        add() {

            const form = new FormData();

            form.append('date', this.date);
            form.append('name', this.name);
            form.append('patient_id', this.patientId);
            form.append('appointment_id', this.appointmentId);

            axios.post('/patients/' + this.patientId + '/labexams', form)

                .then(({ data }) => {
                    this.loader = false;
                    flash('Examen Agregado');
                    this.emitter.emit('actSummaryLabexams', data);
                    this.name = '';
                    this.loadResults();
                })
                .catch(() => {
                    flash('Error al guardar el Examen', 'danger');

                });


        },

        remove(item) {

            if (this.loader) return;

            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {

                this.loader = true;

                axios.delete(`/appointments/${this.appointmentId}/labexams/${item.id}`)
                    .then(() => {
                        this.loader = false;
                        flash('Examen Eliminado!');

                        this.loadResults();


                    }).catch(() => {
                        this.loader = false;
                        flash('Error al eliminar el Examen', 'danger');

                    });
            }


        },
        loadResults() {

            axios.get('/patients/' + this.patientId + '/labexams')
                .then(({ data }) => {

                    this.items = data;
                    this.loader = false;


                });


        },


    },
    created() {
        console.log('Component ready. Lab exams.');

        this.loadResults();

        this.date = moment().format('YYYY-MM-DD');

    }

};
</script>