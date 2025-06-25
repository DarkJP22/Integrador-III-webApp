<template>
  <div class="content">
    <div class="row">
      <div class="col-sm-12">
        <div class="row">
          <div class="col-sm-12 col-md-4">
            <a href="#" :class="{ 'btn-primary': facial, 'btn-secondary': !facial }" class="btn btn-lg btn-block" style="margin-bottom: 1rem" @click="selectContent('facial')">Facial</a>
          </div>
          <div class="col-sm-12 col-md-4">
            <a href="#" :class="{ 'btn-primary': corp, 'btn-secondary': !corp }" class="btn btn-lg btn-block" style="margin-bottom: 1rem" @click="selectContent('corp')">Corporal</a>
          </div>
          <div class="col-sm-12 col-md-4">
            <a href="#" :class="{ 'btn-primary': depi, 'btn-secondary': !depi }" class="btn btn-lg btn-block" style="margin-bottom: 1rem" @click="selectContent('depi')">Depilación</a>
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div>
          <!-- <div class="clearfix">
            <div class="pull-right">
              <button type="button" class="btn btn-primary" @click="showForm">
                <i class="fa fa-plus"></i> Opción
              </button>
              <modal-option-form
                @created="add"
                url="/beautician/recomendations/options"
                name="recomendationOptionModal"
              ></modal-option-form>
            </div>
          </div> -->
          <div v-show="facial">
            <div class="row">
              <div class="col-sm-6">
                <h4>Opciones:</h4>
                <div class="list-group">
                  <button type="button" v-for="item in optionsFacialComputed" :key="item.id" class="list-group-item" :class="{ disabled: item.checked }" @click="selectOption(item)" :disabled="item.checked || loader">
                    <div class="checkbox">
                      <label @click.prevent>
                        <input type="checkbox" :checked="item.checked" :disabled="item.checked" />
                        {{ item.name }}
                      </label>
                    </div>
                  </button>
                </div>
              </div>
              <div class="col-sm-6">
                <h4>Seleccionado:</h4>
                <small v-show="!facialCurrentItems.length">No tienes opciones seleccionadas</small>
                <div class="list-group">
                  <button type="button" class="list-group-item" v-for="(item, index) in facialCurrentItems" :key="item.id" @click="removeOption(item, index)" :disabled="loader">
                    <span class="badge bg-red"><span class="fa fa-close"></span></span>
                    {{ item.name }}
                  </button>
                </div>
                <div>
                  <label for="notes">Notas</label>
                  <textarea name="notes" v-model="facialNotes.notes" cols="30" rows="3" class="form-control" placeholder="" @keydown="saveNotes(facialNotes)"></textarea>
                </div>
              </div>
            </div>
          </div>
          <div v-show="corp">
            <div class="row">
              <div class="col-sm-6">
                <h4>Opciones:</h4>
                <div class="list-group">
                  <button type="button" v-for="item in optionsCorporalComputed" :key="item.id" class="list-group-item" :class="{ disabled: item.checked }" @click="selectOption(item)" :disabled="item.checked || loader">
                    <div class="checkbox">
                      <label @click.prevent>
                        <input type="checkbox" :checked="item.checked" :disabled="item.checked" />
                        {{ item.name }}
                      </label>
                    </div>
                  </button>
                </div>
              </div>
              <div class="col-sm-6">
                <h4>Seleccionado:</h4>
                <small v-show="!corporalCurrentItems.length">No tienes opciones seleccionadas</small>
                <div class="list-group">
                  <button type="button" class="list-group-item" v-for="(item, index) in corporalCurrentItems" :key="item.id" @click="removeOption(item, index)" :disabled="loader">
                    <span class="badge bg-red"><span class="fa fa-close"></span></span>
                    {{ item.name }}
                  </button>
                </div>
                <div>
                  <label for="notes">Notas</label>
                  <textarea name="notes" v-model="corporalNotes.notes" cols="30" rows="3" class="form-control" placeholder="" @keydown="saveNotes(corporalNotes)"></textarea>
                </div>
              </div>
            </div>
          </div>
          <div v-show="depi">
            <div class="row">
              <div class="col-sm-6">
                <h4>Opciones:</h4>
                <div class="list-group">
                  <button type="button" v-for="item in optionsDepilacionComputed" :key="item.id" class="list-group-item" :class="{ disabled: item.checked }" @click="selectOption(item)" :disabled="item.checked || loader">
                    <div class="checkbox">
                      <label @click.prevent>
                        <input type="checkbox" :checked="item.checked" :disabled="item.checked" />
                        {{ item.name }}
                      </label>
                    </div>
                  </button>
                </div>
              </div>
              <div class="col-sm-6">
                <h4>Seleccionado:</h4>
                <small v-show="!depilacionCurrentItems.length">No tienes opciones seleccionadas</small>
                <div class="list-group">
                  <button type="button" class="list-group-item" v-for="(item, index) in depilacionCurrentItems" :key="item.id" @click="removeOption(item, index)" :disabled="loader">
                    <span class="badge bg-red"><span class="fa fa-close"></span></span>
                    {{ item.name }}
                  </button>
                </div>
                <div>
                  <label for="notes">Notas</label>
                  <textarea name="notes" v-model="depilacionNotes.notes" cols="30" rows="3" class="form-control" placeholder="" @keydown="saveNotes(depilacionNotes)"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <small class="pull-right">{{ loader_message }}</small>
      </div>
    </div>
  </div>
</template>

<script>

export default {
    props: {
        read: {
            type: Boolean,
            default: false,
        },
        appointment: {
            type: Object,
        },
        recomendations: {
            type: Array,
        },
        options: {
            type: Object,
        },
        notes: {
            type: Array,
        },
    },
    data() {
        return {
            facial: true,
            corp: false,
            depi: false,
            facialCurrentItems: [],
            corporalCurrentItems: [],
            depilacionCurrentItems: [],
            facialNotes: {
                category: 'facial',
                notes: '',
            },
            corporalNotes: {
                category: 'corporal',
                notes: '',
            },
            depilacionNotes: {
                category: 'depilacion',
                notes: '',
            },
            optionsFacial: [],
            optionsCorporal: [],
            optionsDepilacion: [],
            loader: false,
            loader_message: '',
        };
    },
    computed: {
        optionsFacialComputed() {
            const items = [];

            this.optionsFacial.forEach((element) => {
                items.push({
                    id: element.id,
                    name: element.name,
                    category: element.category,
                    checked: this.facialCurrentItems.find(
                        (item) => item.oprecomendation_id === element.id
                    )
                        ? true
                        : false,
                });
            });

            return items;
        },
        optionsCorporalComputed() {
            const items = [];

            this.optionsCorporal.forEach((element) => {
                items.push({
                    id: element.id,
                    name: element.name,
                    category: element.category,
                    checked: this.corporalCurrentItems.find(
                        (item) => item.oprecomendation_id === element.id
                    )
                        ? true
                        : false,
                });
            });

            return items;
        },
        optionsDepilacionComputed() {
            const items = [];

            this.optionsDepilacion.forEach((element) => {
                items.push({
                    id: element.id,
                    name: element.name,
                    category: element.category,
                    checked: this.depilacionCurrentItems.find(
                        (item) => item.oprecomendation_id === element.id
                    )
                        ? true
                        : false,
                });
            });

            return items;
        },
    },
    methods: {
        showForm() {
            $('#recomendationOptionModal').modal();
        },

        selectContent(content) {
            (this.facial = false),
            (this.corp = false),
            (this.depi = false),
            (this[content] = true);
        },
        selectOption(option) {
            const itemFound = _.find(
                this[`${option.category}CurrentItems`],
                function (o) {
                    return o.oprecomendation_id === option.id;
                }
            );

            if (!itemFound) {
                //this[cat + 'Items'].push(option);
                this.save(option);
            }
        },
        removeOption(option, index) {
            this[`${option.category}CurrentItems`].splice(index, 1);
            this.remove(option);
        },
        saveNotes: _.debounce(function (note) {
            if (!note.id) {
                axios
                    .post(
                        '/appointments/' + this.appointment.id + '/recomendations-notes',
                        note
                    )
                    .then(({ data }) => {
                        this[`${note.category}Notes`] = data;

                        this.loader_message = 'Cambios Guardados';
                        this.loader = false;
                        this.emitter.emit('actSummaryRecomendationNotes', { data: this[`${note.category}Notes`], category: note.category });
                    })
                    .catch(() => {
                        this.loader = false;
                        flash('Error al guardar cambios', 'danger');
                    });
            } else {
                axios
                    .put(
                        '/appointments/' +
            this.appointment.id +
            '/recomendations-notes/' +
            note.id,
                        note
                    )
                    .then(() => {

                        this.loader_message = 'Cambios Guardados';
                        this.loader = false;
                        this.emitter.emit('actSummaryRecomendationNotes', { data: this[`${note.category}Notes`], category: note.category });
                    })
                    .catch(() => {
                        this.loader = false;
                        flash('Error al guardar cambios', 'danger');
                    });
            }
        }, 500),

        keydown: _.debounce(function (option) {
            const itemFound = _.find(
                this[`${option.category}CurrentItems`],
                function (o) {
                    return o.oprecomendation_id === option.id;
                }
            );

            if (!itemFound) {
                this.save(option);
            }
        }, 500),
        remove(option) {
            axios
                .delete(
                    '/appointments/' +
          this.appointment.id +
          '/recomendations/' +
          option.id
                )
                .then(() => {

                    this.loader_message = 'Cambios Guardados';
                    this.loader = false;

                    this.emitter.emit('actSummaryRecomendation', { data: this[`${option.category}CurrentItems`], category: option.category });
                })
                .catch(() => {
                    this.loader = false;
                    flash('Error al guardar cambios', 'danger');
                });
        },

        save(option) {
            this.loader = true;
            this.loader_message = 'Guardando...';

            axios
                .post('/appointments/' + this.appointment.id + '/recomendations', {
                    oprecomendation_id: option.id,
                    name: option.name,
                    category: option.category,
                })
                .then(({ data }) => {
                    this[`${option.category}CurrentItems`].push(data);

                    this.loader_message = 'Cambios Guardados';
                    this.loader = false;

                    this.emitter.emit('actSummaryRecomendation', { data: this[`${option.category}CurrentItems`], category: option.category });
                })
                .catch(() => {
                    this.loader = false;
                    flash('Error al guardar cambios', 'danger');
                });
        },
        prepareData() {
            if (this.recomendations.length) {
                this.facialCurrentItems = this.recomendations.filter(
                    (eva) => eva.category == 'facial'
                );
                this.corporalCurrentItems = this.recomendations.filter(
                    (eva) => eva.category == 'corporal'
                );
                this.depilacionCurrentItems = this.recomendations.filter(
                    (eva) => eva.category == 'depilacion'
                );
            }

            if (this.notes.length) {
                this.facialNotes = this.notes.filter(
                    (eva) => eva.category == 'facial'
                )[0] ?? { category: 'facial', notes: '' };
                this.corporalNotes = this.notes.filter(
                    (eva) => eva.category == 'corporal'
                )[0] ?? { category: 'corporal', notes: '' };
                this.depilacionNotes = this.notes.filter(
                    (eva) => eva.category == 'depilacion'
                )[0] ?? { category: 'depilacion', notes: '' };
            }

            this.optionsFacial = [];
            if (this.options.facial) {
                this.options.facial.forEach((element) => {
                    this.optionsFacial.push(element);
                });
            }
            this.optionsCorporal = [];
            if (this.options.corporal) {
                this.options.corporal.forEach((element) => {
                    this.optionsCorporal.push(element);
                });
            }
            this.optionsDepilacion = [];
            if (this.options.depilacion) {
                this.options.depilacion.forEach((element) => {
                    this.optionsDepilacion.push(element);
                });
            }
        },
    },

    created() {
        console.log('Component ready. recomendation.');

        this.prepareData();
    },
};
</script>
