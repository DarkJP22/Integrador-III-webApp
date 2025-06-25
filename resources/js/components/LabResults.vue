<template>

  <div class="box box-default ">
    <div class="box-header with-border">
      <h3 class="box-title">
        <slot>Resultados</slot>
      </h3>

      <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div v-if="isLab" class="form-group">
        <label for="user_id">Médico</label>
        <select-medic :url="'/lab/medics'" @selectedMedic="selectedMedic" :medic="currentUser"></select-medic>
        <form-error v-if="errors.medic_id" :errors="errors">
          {{ errors.medic_id[0] }}
        </form-error>
      </div>
      <div v-else v-show="medics" class="form-group">
        <label for="user_id">Médico</label>
        <select id="medic_id" v-model="medic_id" class="form-control" name="medic_id" required>
          <option value=""></option>
          <option value="0">Ninguno</option>
          <option v-for="(item, index) in medics" :key="item.id" :selected="index === 0 ? 'selected' : '' " :value="item.id"> {{ item.name }}</option>

        </select>
        <form-error v-if="errors.medic_id" :errors="errors" style="float:right;">
          {{ errors.medic_id[0] }}
        </form-error>
      </div>
      <div v-show="!finishedRevalorizar" class="form-group">
        <div class="input-group flatpickr">
          <input id="datetimepickerLabResult" v-model="date" class="form-control" data-input type="text" @blur="onBlurDatetime">

          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
        </div>

      </div>
      <div v-show="!finishedRevalorizar" class="form-group">
        <textarea id="description" v-model="description" class="form-control" cols="30" name="description" placeholder="Resultado..." rows="2"></textarea>



      </div>
      <div v-show="!finishedRevalorizar" class="form-group">
        <photo-upload @input="handleFileUpload"></photo-upload>
        <form-error v-if="errors.file" :errors="errors" style="float:right;">
          {{ errors.file[0] }}
        </form-error>
      </div>
      <div class="form-group">
        <button v-show="!finishedRevalorizar" class="btn btn-primary" v-bind:disabled="loader" @click="hit">Agregar</button><img v-show="loader" alt="Cargando..." src="/img/loading.gif">
      </div>
      <ul v-show="items.length" id="medicines-list" class="todo-list ui-sortable">

        <li v-for="item in items" :key="item.id">
          <!-- todo text -->
          <span><span class="text"> {{ formatDate(item.date) }}</span> <a target="_blank" v-bind:href="item.file_path"><i v-if="item.file_path" class="fa fa-file"></i> {{ (item.description) ? item.description : item.name }}</a></span>
          <!-- General tools such as edit or delete-->
          <div class="tools">

            <i v-show="!finishedRevalorizar" class="fa fa-trash-o delete" v-bind:disabled="loader" @click="remove(item)"></i>
          </div>
        </li>

      </ul>
    </div>



  </div>

</template>

<script>
import FormError from './FormError.vue';
import PhotoUpload from './PhotoUpload.vue';
import SelectMedic from './SelectMedic.vue';
export default {
    //props: ['medicines','patient_id'],
    props: {
        patientId: {
            type: Number

        },
        results: {
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
        medics: {
            type: Array,
        }
    },
    data() {
        return {
            date: '',
            description: '',
            medic_id: '',
            file: '',
            items: [],
            loader: false,
            errors: []

        };

    },
    components: {
        FormError,
        PhotoUpload,
        SelectMedic

    },
    computed: {
        isMedic() {
            return window.App.isMedic;
        },
        isLab() {
            return window.App.isLab;
        },
        currentUser() {
            return window.App.user;
        },
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
        selectedMedic(medic) {
            this.medic_id = medic?.id ?? '';
        },
        slug(str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim
            str = str.toLowerCase();

            // remove accents, swap ñ for n, etc
            var from = 'ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;';
            var to = 'aaaaaeeeeeiiiiooooouuuunc------';
            for (var i = 0, l = from.length; i < l; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

            return str;
        },
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
            if (!this.date)
                return;

            this.loader = true;
            this.add();

        },
        add() {

            const config = {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            };
            const form = new FormData();

            form.append('date', this.date);
            form.append('description', this.description);
            form.append('medic_id', this.appointment?.user_id ?? this.medic_id);
            if (this.file) {
                form.append('file', this.file);
            }

            axios.post('/patients/' + this.patientId + '/labresults', form, config)

                .then(({ data }) => {
                    this.loader = false;
                    flash('Archivo Agregado');
                    this.items.unshift(data);
                    this.file = '';
                    this.description = '';
                    this.errors = [];

                })
                .catch(error => {
                    this.loader = false;
                    flash('Error al guardar el Archivo', 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];

                });

            this.emitter.emit('clearImage');



        },
        remove(item) {
            if (this.loader) return;

            const r = confirm('¿Deseas Eliminar este registro?');

            if (r === true) {
                this.loader = true;

                axios.delete(`/labresults/${item.id}`)
                    .then(() => {
                        this.loader = false;
                        flash('Resultado Eliminado!');
                        var index = this.items.indexOf(item);
                        this.items.splice(index, 1);




                    }).catch(() => {
                        this.loader = false;
                        flash('Error al eliminar el Resultado', 'danger');

                    });
            }




        },
        handleFileUpload(file) {
            console.log(file);
            this.file = file;

        }

    },
    created() {
        console.log('Component ready. Lab Results.');

        if (this.results.length) {

            this.items = this.results;
        }

        this.date = moment().format('YYYY-MM-DD');

        if(this.isMedic){
            this.medic_id = window.App.user.id;
        }

    }

};
</script>