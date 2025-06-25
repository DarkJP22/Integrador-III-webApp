<template>


  <div class="box box-default">

    <div class="box-header with-border">
      <h3 class="box-title">Diagnosticos</h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
        </button>
      </div>
      <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body">

      <div class="form-group">
        <input type="text" name="search" class="form-control" @keydown.enter="hit" v-model="query" placeholder="Nombre..." :readonly="finishedRevalorizar">
      </div>
      <div class="form-group">
        <button @click="hit" class="btn btn-primary" v-show="!finishedRevalorizar">Agregar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
      </div>
      <ul id="diagnostics-list" class="todo-list ui-sortable" v-show="items.length">

        <li v-for="item in items" :key="item.id">
          <!-- todo text -->
          <span><span class="text"> {{ item.name }}</span></span>
          <!-- General tools such as edit or delete-->
          <div class="tools">

            <i class="fa fa-trash-o delete" @click="remove(item)" v-show="!finishedRevalorizar"></i>
          </div>
        </li>

      </ul>


    </div>
    <!-- /.box-body -->
  </div>




</template>

<script>
export default {
    //props: ['diagnostics','appointment_id'],
    props: {
        diagnostics: {
            type: Array
        },
        appointmentId: {
            type: Number
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
            query: '',
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

        hit() {
            console.log('hit');
            if (!this.query)
                return;

            this.loader = true;
            this.add(this.query);
            this.query = '';
        },
        add(diagnostic) {


            axios.post('/diagnostics', { appointment_id: this.appointmentId, name: diagnostic })
                .then(({ data }) => {
                    this.loader = false;
                    flash('Diagnóstico Agregado');
                    this.items.push(data);
                    this.emitter.emit('actHistoryDiagnostics', data);
                    this.emitter.emit('actSummaryDiagnostics', this.items);
                })
                .catch(() => {
                    this.loader = false;
                    flash('Error al guardar el diagnostico', 'danger');

                });

        },
        remove(item) {

            this.loader = true;

            axios.delete(`/diagnostics/${item.id}`)
                .then(() => {
                    this.loader = false;
                    flash('Diagnóstico Eliminado!');

                    var index = this.items.indexOf(item);
                    this.items.splice(index, 1);

                    this.emitter.emit('actSummaryDiagnostics', this.items);


                }).catch(() => {
                    this.loader = false;
                    flash('Error al eliminar el diagnostico', 'danger');

                });


        }

    },
    created() {
        console.log('Component ready. diagnostico.');

        if (this.diagnostics) {

            this.items = this.diagnostics;
        }

    }

};
</script>