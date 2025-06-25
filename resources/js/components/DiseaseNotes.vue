<template>
  <div>

    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">SÍNTOMAS SUBJETIVOS</h3> <small class="pull-right">{{ loader_message }}</small>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <textarea name="symptoms" cols="30" rows="4" class="form-control" v-model="disease_notes.symptoms" @keydown="keydown()" :readonly="finished" tabindex="9"></textarea>
      </div>

    </div>
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">EXPLORACIÓN FÍSICA</h3> <small class="pull-right">{{ loader_message }}</small>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <textarea name="phisical_review" cols="30" rows="4" class="form-control" v-model="disease_notes.phisical_review" @keydown="keydown()" :readonly="finished" tabindex="10"></textarea>
      </div>
      <!-- /.box-body -->

    </div>
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">ANALISIS</h3> <small class="pull-right">{{ loader_message }}</small>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <textarea name="reason" cols="30" rows="4" class="form-control" v-model="disease_notes.reason" @keydown="keydown()" :readonly="finished" tabindex="10"></textarea>
      </div>
      <!-- /.box-body -->

    </div>
    <div class="box box-default" v-if="appointment && appointment.revalorizar && appointment.finished">
      <div class="box-header with-border">
        <h3 class="box-title">REVALUAR</h3> <small class="pull-right">{{ loader_message }}</small>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <textarea name="revalorizacion" cols="30" rows="4" class="form-control" v-model="disease_notes.revalorizacion" @keydown="keydown()" :readonly="finishedRevalorizar" tabindex="10"></textarea>
      </div>
      <!-- /.box-body -->

    </div>

  </div>

</template>

<script>

export default {
    //props: ['notes', read],
    props: {
        notes: {
            type: Object
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
            disease_notes: {
                reason: '',
                symptoms: '',
                phisical_review: ''
            },
            loader: false,
            loader_message: ''


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

                    if (this.appointment.revalorizar && this.read || !this.appointment.revalorizar) {
                        return true;
                    }

                }

            }

            return false;
        }
    },
    methods: {

        keydown: _.debounce(
            function () {
                this.update();
            },
            500
        ),

        update() {
            //this.loader = true;
            this.loader_message = 'Guardando...';

            axios.put('/diseasenotes/' + this.notes.id, this.disease_notes)
                .then(() => {
                    this.emitter.emit('actSummaryNotes', this.disease_notes);
                    this.loader_message = 'Cambios Guardados';
                    this.loader = false;


                })
                .catch(() => {
                    this.loader = false;
                    flash('Error al guardar cambios', 'danger');

                });


        }


    },
    created() {

        console.log('Component ready. DiseaseNotes');

        this.disease_notes = this.notes;



    }
};
</script>