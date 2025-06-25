<template>

  <div class="box-group" id="accordion">

    <div class="panel box box-default">
      <div class="box-header with-border">
        <h4 class="box-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#patologicos" aria-expanded="false" class="collapsed">
            ANTECEDENTES PATOLÓGICOS: <small><span class="label label-primary activesHistory">{{ pathologicals.length }}</span></small>
          </a>
        </h4>
      </div>
      <div id="patologicos" class="panel-collapse collapse" aria-expanded="false">
        <div class="box-body">

          <ul id="medicines-list" class="todo-list ui-sortable" v-show="pathologicals.length">

            <li v-for="(item, index) in pathologicals" :key="item.id">
              <!-- todo text -->
              <i class="fa fa-trash-o delete" @click="destroy('/pathologicals',item, index, 'pathological')" v-if="authorize('owns', item) && statusAppointment(item)"></i>
              <span><span class="text"> {{ item.name }}</span></span>
              <!-- General tools such as edit or delete-->
              <div class="tools">
                <span>Dr(a). {{ item.user?.name ?? item.medic_name }} - </span>
                <span>{{ item.created_at }}</span>
              </div>
            </li>

          </ul>
          <div v-show="!read">
            <h4>Agregar nuevo:</h4>
            <textarea name="pathological" cols="30" rows="3" class="form-control" v-model="pathological" placeholder="Ej: Hospitalizacion previa, Cirugías, Diabetes, Enfermedades Tiroideas, Hipertensión Arterial, etc."></textarea>
            <form-error v-if="errors.name" :errors="errors">
              {{ errors.name[0] }}
            </form-error>
            <div class="form-group pull-right">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary" @click="saveHistory('/pathologicals',pathological,'pathological')">Guardar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="panel box box-default">
      <div class="box-header with-border">
        <h4 class="box-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#no_patologicos" aria-expanded="false" class="collapsed">
            ANTECEDENTES NO PATOLÓGICOS: <small><span class="label label-primary activesHistory">{{ no_pathologicals.length }}</span></small>
          </a>
        </h4>
      </div>
      <div id="no_patologicos" class="panel-collapse collapse" aria-expanded="false">
        <div class="box-body">

          <ul id="medicines-list" class="todo-list ui-sortable" v-show="no_pathologicals.length">

            <li v-for="(item, index) in no_pathologicals" :key="item.id">
              <!-- todo text -->
              <i class="fa fa-trash-o delete" @click="destroy('/nopathologicals',item, index, 'no_pathological')" v-if="authorize('owns', item) && statusAppointment(item)"></i>
              <span><span class="text"> {{ item.name }}</span></span>
              <!-- General tools such as edit or delete-->
              <div class="tools">
                <span>Dr(a). {{ item.user?.name ?? item.medic_name }} - </span>
                <span>{{ item.created_at }}</span>
              </div>
            </li>

          </ul>
          <div v-show="!read">
            <h4>Agregar nuevo:</h4>
            <textarea name="no_pathological" cols="30" rows="3" class="form-control" v-model="no_pathological" placeholder="Ej: Actividad Física, Tabaquismo, Alcoholismo, Uso de otras sustancias (Drogas), etc."></textarea>
            <form-error v-if="errors.name" :errors="errors">
              {{ errors.name[0] }}
            </form-error>
            <div class="form-group pull-right">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary" @click="saveHistory('/nopathologicals',no_pathological,'no_pathological')">Guardar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="panel box box-default">
      <div class="box-header with-border">
        <h4 class="box-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#heredofamiliares" aria-expanded="false" class="collapsed">
            ANTECEDENTES HEREDOFAMILIARES: <small><span class="label label-primary activesHistory">{{ heredos.length }}</span></small>
          </a>
        </h4>
      </div>
      <div id="heredofamiliares" class="panel-collapse collapse" aria-expanded="false">
        <div class="box-body">

          <ul id="medicines-list" class="todo-list ui-sortable" v-show="heredos.length">

            <li v-for="(item, index) in heredos" :key="item.id">
              <!-- todo text -->
              <i class="fa fa-trash-o delete" @click="destroy('/heredos',item, index, 'heredo')" v-if="authorize('owns', item) && statusAppointment(item)"></i>
              <span><span class="text"> {{ item.name }}</span></span>
              <!-- General tools such as edit or delete-->
              <div class="tools">
                <span>Dr(a). {{ item.user?.name ?? item.medic_name }} - </span>
                <span>{{ item.created_at }}</span>
              </div>
            </li>

          </ul>
          <div v-show="!read">
            <h4>Agregar nuevo:</h4>
            <textarea name="heredo" cols="30" rows="3" class="form-control" v-model="heredo" placeholder="Ej: Diabetes, Cardiopatías, Hipertensión Arterial, etc."></textarea>
            <form-error v-if="errors.name" :errors="errors">
              {{ errors.name[0] }}
            </form-error>
            <div class="form-group pull-right">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary" @click="saveHistory('/heredos',heredo,'heredo')">Guardar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="panel box box-default">
      <div class="box-header with-border">
        <h4 class="box-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#gineco_obstetricios" aria-expanded="false" class="collapsed">
            ANTECEDENTES GINECO-OBSTETRICIOS: <small><span class="label label-primary activesHistory">{{ ginecos.length }}</span></small>
          </a>
        </h4>
      </div>
      <div id="gineco_obstetricios" class="panel-collapse collapse" aria-expanded="false">
        <div class="box-body">

          <ul id="medicines-list" class="todo-list ui-sortable" v-show="ginecos.length">

            <li v-for="(item, index) in ginecos" :key="item.id">
              <!-- todo text -->
              <i class="fa fa-trash-o delete" @click="destroy('/ginecos',item, index, 'gineco')" v-if="authorize('owns', item) && statusAppointment(item)"></i>
              <span><span class="text"> {{ item.name }}</span></span>
              <!-- General tools such as edit or delete-->
              <div class="tools">

                <span>Dr(a). {{ item.user?.name ?? item.medic_name }} - </span>
                <span>{{ item.created_at }}</span>
              </div>
            </li>

          </ul>
          <div v-show="!read">
            <h4>Agregar nuevo:</h4>
            <textarea name="gineco" cols="30" rows="3" class="form-control" v-model="gineco" placeholder="Ej: Fecha de primera menstruación, Fecha de última menstruación, Características menstruación, Embarazos, etc."></textarea>
            <form-error v-if="errors.name" :errors="errors">
              {{ errors.name[0] }}
            </form-error>
            <div class="form-group pull-right">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary" @click="saveHistory('/ginecos',gineco,'gineco')">Guardar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>


  </div>

</template>

<script>


export default {
    //props: ['history','appointments'],
    props: {
        appointments: {
            type: Array
        },
        history: {
            type: Object
        },
        read: {
            type: Boolean,
            default: false
        },
        finishedAppointment: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            allergy: '',
            pathological: '',
            no_pathological: '',
            heredo: '',
            gineco: '',
            allergies: [],
            pathologicals: [],
            no_pathologicals: [],
            heredos: [],
            ginecos: [],
            diagnostics: [],
            diagnosticsToday: [],
            errors: [],
            loader: false

        };

    },

    methods: {
        statusAppointment(item) {
            let res = false;

            if (moment(moment(item.created_at).format('YYYY-MM-DD')).isSame(moment().format('YYYY-MM-DD')) && !this.finishedAppointment) {
                res = true;
            }
            // console.log(moment(item.created_at).format('YYYY-MM-DD') + '-'+ moment().format('YYYY-MM-DD'))
            return res;

        },
        saveHistory(url, item, cat) {


            this.loader = true;

            axios.post(url, { history_id: this.history.id, name: item })
                .then(({ data }) => {
                    this.loader = false;
                    this.errors = [];

                    if (cat == 'allergy') {
                        this.allergies.push(data);
                        this.allergy = '';
                        this.emitter.emit('actSummaryAllergies', this.allergies);
                    }
                    if (cat == 'pathological') {
                        this.pathologicals.push(data);
                        this.pathological = '';
                        this.emitter.emit('actSummaryPathologicals', this.pathologicals);
                    }
                    if (cat == 'no_pathological') {
                        this.no_pathologicals.push(data);
                        this.no_pathological = '';
                        this.emitter.emit('actSummaryNoPathologicals', this.no_pathologicals);
                    }
                    if (cat == 'heredo') {
                        this.heredos.push(data);
                        this.heredo = '';
                        this.emitter.emit('actSummaryHeredos', this.heredos);
                    }
                    if (cat == 'gineco') {
                        this.ginecos.push(data);
                        this.gineco = '';
                        this.emitter.emit('actSummaryGinecos', this.ginecos);
                    }

                })
                .catch(error => {
                    this.loader = false;
                    flash(error.response.data.message, 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });




        },
        destroy(url, item, index, cat) {
            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                axios.delete(`${url}/${item.id}`)
                    .then(() => {

                        if (cat == 'allergy') {

                            this.allergies.splice(index, 1);
                            this.emitter.emit('actSummaryAllergies', this.allergies);
                        }
                        if (cat == 'pathological') {

                            this.pathologicals.splice(index, 1);

                            this.emitter.emit('actSummaryPathologicals', this.pathologicals);
                        }
                        if (cat == 'no_pathological') {

                            this.no_pathologicals.splice(index, 1);
                            this.emitter.emit('actSummaryNoPathologicals', this.no_pathologicals);
                        }
                        if (cat == 'heredo') {

                            this.heredos.splice(index, 1);
                            this.emitter.emit('actSummaryHeredos', this.heredos);
                        }
                        if (cat == 'gineco') {

                            this.ginecos.splice(index, 1);
                            this.emitter.emit('actSummaryGinecos', this.ginecos);
                        }

                        flash('Registro Eliminado!');

                    }).catch(error => {

                        flash(error.response.data.message, 'danger');

                    });
            }
        },
        updateDiagnostics(data) {

            this.diagnosticsToday.push(data);
        }


    },
    created() {
        console.log('Component ready. history.');

        this.emitter.on('actHistoryDiagnostics', this.updateDiagnostics);

        if (this.history.allergies) {

            this.allergies = this.history.allergies;

        }
        if (this.history.pathologicals) {

            this.pathologicals = this.history.pathologicals;

        }
        if (this.history.nopathologicals) {

            this.no_pathologicals = this.history.nopathologicals;

        }
        if (this.history.heredos) {

            this.heredos = this.history.heredos;

        }
        if (this.history.ginecos) {

            this.ginecos = this.history.ginecos;

        }
        if (this.appointments) {
            this.diagnostics = this.appointments;


        }





    }
};
</script>