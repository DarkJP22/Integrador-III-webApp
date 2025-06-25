<template>
    <div class="modal fade" id="modalScheduleForm" role="dialog" aria-labelledby="modalScheduleForm">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <form @submit.prevent="save()" autocomplete="off">
                    <div class="modal-header">

                        <h4 class="modal-title" id="modalScheduleFormLabel">Programando Agenda del <span class="label label-primary">{{ form.dates[0] }}</span> al <span class="label label-primary">{{ form.dates[form.dates.length-1] }}</span></h4>
                    </div>
                    <div class="modal-body" data-modaldate data-slotduration="30">
                        <loading :show="loader"></loading>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12">
                                <div class="form-group">
                                    <label for="user_id" class="control-label">Médico</label>

                                    <select class="form-control" v-model="form.user_id" @change="onChangeMedic" required>
                                        <option v-for="(item, index) in medics" :value="item.id" :key="index">
                                            {{ item.name }}
                                        </option>

                                    </select>
                                </div>


                            </div>

                        </div>
                        <div class="row">
                            <!-- <div class="col-xs-12 col-sm-4">
                     <div class="form-group">
                    <label>Fecha:</label>
                    <div class="input-group date col-sm-10">
                        <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                        </div>
                        
                        <input type="text" class="form-control pull-right"  name="date" id="datetimepicker1" v-model="form.date" @blur="onBlurDate" required>
                    </div>
                    </div> 
                </div>-->
                            <div class="col-xs-6 col-sm-3">
                                <div class="form-group">
                                    <label>Hora de inicio:</label>

                                    <div class="input-group col-xs-9 col-sm-10">
                                        <input type="text" class="form-control " name="start" id="datetimepicker2" v-model="ini" @blur="onBlurStart" required>

                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-3">
                                <div class="form-group">
                                    <label>Hora de fin:</label>

                                    <div class="input-group col-xs-9 col-sm-10">
                                        <input type="text" class="form-control " name="end" id="datetimepicker3" v-model="fin" @blur="onBlurEnd" required>

                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>




                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Crear</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar Asistente</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

</template>
<script>
import Loading from './Loading.vue';
//import flatPickr from 'vue-flatpickr-component';
export default {
    props: ['office', 'medics'],
    components: { Loading },
    data() {
        return {
            form: {
                dates: [],
                start: '',
                end: '',
                office_id: this.office ? this.office.id : '',
                user_id: '',
                title: this.office ? this.office.name : '',
                backgroundColor: '#67BC9A',
                borderColor: '#67BC9A'
            },
            medicSchedules: [],
            ini: '',
            fin: '',
            colision: false,
            errors: [],
            loader: false,

        };

    },

    methods: {
        onChangeMedic() {
            this.getSchedulesMedic(this.form.user_id);
        },
        //  onBlurDate(e){

        //   this.form.date = e.target.value;
        //   this.getSchedulesMedic(this.form.user_id);
        //   this.$emit('input')
        // },
        onBlurStart(e) {

            this.ini = e.target.value;
            this.$emit('input');
        },
        onBlurEnd(e) {

            this.fin = e.target.value;
            this.$emit('input');
        },
        save() {
            if (this.loader) {
                return;
            }
            const start = this.form.dates[0] + 'T' + this.ini + ':00';
            const end = this.form.dates[0] + 'T' + ((this.fin) ? this.fin : this.ini) + ':00';

            this.form.start = this.ini;
            this.form.end = (this.fin) ? this.fin : this.ini;

            if (moment(start).isAfter(end)) {
                flash('Fecha invalida. La hora de inicio no puede ser mayor que la hora final!!!', 'danger');

                return false;
            }

            this.isOverlapping(this.form);

            if (this.colision) {

                flash('No se puede agregar el evento por que hay colision de horarios. Por favor revisar!!!', 'danger');
                return false;
            }

            this.loader = true;


            if (this.form.id) {
                axios.put('/schedules/' + this.form.id, this.form)
                    .then(({ data }) => {

                        this.loader = false;
                        flash('Horario Actualizado correctamente!');

                        this.emitter.emit('updatedSchedule', data);
                    }
                    ).catch(error => {

                        this.loader = false;
                        flash('Error al guardar', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];


                    });

            } else {


                axios.post('/clinic/schedules', this.form)
                    .then(({ data }) => {

                        this.loader = false;
                        flash('Horario Guardado correctamente!');

                        this.form.user_id = '';
                        this.form.start = '';
                        this.form.end = '';
                        this.ini = '';
                        this.fin = '';

                        this.emitter.emit('createdSchedule', data);
                    }
                    ).catch(error => {
                        this.loader = false;
                        flash('Error al guardar', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];


                    });

            }




        },
        getSchedulesMedic(medic) {
            if (medic) {
                this.loader = true;
                axios.get('/medics/' + medic + '/calendars/schedules?date1=' + this.form.dates[0] + '&date2=' + this.form.dates[this.form.dates.length - 1])
                    .then(({ data }) => {
                        this.loader = false;
                        this.medicSchedules = data;

                    });
            }
        },
        isOverlapping(event) {

            this.colision = false;

            const array = this.medicSchedules;
            let start = '';
            let end = '';

            array.forEach(element => {
                event.dates.forEach(EventDate => {

                    start = EventDate + 'T' + this.ini + ':00';
                    end = EventDate + 'T' + ((this.fin) ? this.fin : this.ini) + ':00';

                    if (end > element.start && start < element.end) {

                        this.colision = true;
                    }
                });
            });


        },

        // loadSchedule(data){
        //     if(data.generaltag){
        //         this.form.id = data.generaltag.id;
        //         this.form.tag_id = data.generaltag.tag_id;
        //         this.form.date = data.generaltag.date;
        //         this.form.body = data.generaltag.body;
        //     }


        // },
        newSchedule(data) {


            var dates = [];
            var startDate = moment(data.startDate, 'YYYY-MM-DD');
            var endDate = moment(data.endDate, 'YYYY-MM-DD').subtract(1, 'days');
            dates.push(startDate.format('YYYY-MM-DD'));
            while (!startDate.isSame(endDate)) {
                startDate = startDate.add(1, 'days');
                dates.push(startDate.format('YYYY-MM-DD'));
            }



            this.form = {
                dates: dates,
                start: '',
                end: '',
                office_id: this.office ? this.office.id : '',
                user_id: '',
                title: this.office ? this.office.name : '',
                backgroundColor: '#67BC9A',
                borderColor: '#67BC9A'
            };


        }
    },
    created() {
        this.emitter.on('createSchedule', this.newSchedule);
        // this.emitter.on('editSchedule', this.loadTag)
    }
};
</script>


