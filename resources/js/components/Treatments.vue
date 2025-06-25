<template>


  <div class="box box-default">

    <div class="box-header with-border">
      <h3 class="box-title">Tratamiento</h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
        </button>
      </div>
      <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body">


      <div class="form-group">
        <input type="text" name="search" class="form-control" v-model="query" placeholder="Nombre..." :readonly="finishedRevalorizar">
      </div>
      <div class="form-group">
        <input type="text" name="search" class="form-control" v-model="comments" placeholder="Recomendación (Dosis)..." :readonly="finishedRevalorizar">
      </div>
      <div class="form-group">
        <button @click="hit" class="btn btn-primary" v-show="!finishedRevalorizar">Agregar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
      </div>
      <ul id="diagnostics-list" class="todo-list ui-sortable" v-show="items.length">

        <li v-for="item in items" :key="item.id">
          <!-- todo text -->
          <span><span class="text"> <b>{{ item.name }}:</b></span> {{ item.comments }}</span>
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
    //props: ['treatments','appointment_id'],
    props: {
        treatments: {
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
            comments: '',
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
            this.add(this.query, this.comments);
            this.query = '';
            this.comments = '';
        },
        add(treatment, comments) {


            axios.post('/treatments', { appointment_id: this.appointmentId, name: treatment, comments: comments })
                .then(({ data }) => {
                    this.loader = false;
                    flash('Examen Agregado');
                    this.items.push(data);
                    this.emitter.emit('actSummaryTreatments', data);

                })
                .catch(() => {
                    this.loader = false;
                    flash('Error al guardar el Tratamiento', 'danger');

                });




        },
        remove(item) {
            if (this.loader) return;
            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                this.loader = true;
                axios.delete(`/treatments/${item.id}`)
                    .then(() => {
                        this.loader = false;
                        flash('Tratamiento Eliminado!');

                        var index = this.items.indexOf(item);
                        this.items.splice(index, 1);

                        this.emitter.emit('actSummaryTreatments', this.items);


                    }).catch(() => {
                        this.loader = false;
                        flash('Error al eliminar el Tratamiento', 'danger');

                    });
            }




        }

    },
    created() {
        console.log('Component ready. Tratamiento.');

        if (this.treatments) {

            this.items = this.treatments;
        }

    }

};
</script>