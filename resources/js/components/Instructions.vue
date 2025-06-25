<template>

  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Recomendaciones Médicas</h3> <small class="pull-right">{{ loader_message }}</small>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <textarea name="medical_instructions" cols="30" rows="4" class="form-control" v-model="medical_instructions" @keydown="keydown()" :readonly="read"></textarea>
    </div>
    <!-- /.box-body -->
  </div>



</template>

<script>

export default {
    //props: ['appointment'],
    props: {
        appointment: {
            type: Object
        },
        read: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            medical_instructions: '',
            loader: false,
            loader_message: ''


        };
    },
    methods: {

        keydown: _.debounce(
            function () {
                this.update();
            },
            500
        ),

        update() {
            this.loader_message = 'Guardando...';

            axios.put('/agenda/appointments/' + this.appointment.id, { medical_instructions: this.medical_instructions })
                .then(() => {
                    this.emitter.emit('actSummaryInstructions', this.medical_instructions);
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

        console.log('Component ready. Instructions');

        this.medical_instructions = this.appointment.medical_instructions;



    }
};
</script>