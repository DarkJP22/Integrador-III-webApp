<template>
  <div>
    <div class="col-sm-12">
      <div class="row">
        <div class="col-sm-12 col-md-4">
          <a href="#" :class="{ 'btn-primary': abdomen, 'btn-secondary': !abdomen }" class="btn btn-lg btn-block" style="margin-bottom: 1rem" @click="selectContent('abdomen')">Abdomen</a>
        </div>
        <div class="col-sm-12 col-md-4">
          <a href="#" :class="{ 'btn-primary': brazos, 'btn-secondary': !brazos }" class="btn btn-lg btn-block" style="margin-bottom: 1rem" @click="selectContent('brazos')">Brazos</a>
        </div>
        <div class="col-sm-12 col-md-4">
          <a href="#" :class="{ 'btn-primary': piernas, 'btn-secondary': !piernas }" class="btn btn-lg btn-block" style="margin-bottom: 1rem" @click="selectContent('piernas')">Piernas</a>
        </div>
      </div>
    </div>
    <button type="button" class="btn btn-primary btn-sm" @click="newColumn()">
      Agregar Semana
    </button>
    <table class="table table-striped">
      <tbody>
        <tr>
          <th>--</th>
          <th v-for="(item, index) in data.items.height" :key="index">
            S{{ index + 1 }}
          </th>
        </tr>
        <tr>
          <td>Altura</td>
          <td v-for="(item, index) in data.items.height" :key="index">
            <input type="text" class="form-control" v-model="item.value" @keydown="keydown()" />
          </td>
        </tr>

        <tr>
          <td>Peso</td>
          <td v-for="(item, index) in data.items.weight" :key="index">
            <input type="text" class="form-control" v-model="item.value" @keydown="keydown()" />
          </td>
        </tr>
        <tr>
          <td>IMC</td>
          <td v-for="(item, index) in data.items.mass" :key="index">
            <input type="text" class="form-control" v-model="item.value" @keydown="keydown()" />
          </td>
        </tr>
        <tr>
          <td>P.A</td>
          <td v-for="(item, index) in data.items.pa" :key="index">
            <input type="text" class="form-control" v-model="item.value" @keydown="keydown()" />
          </td>
        </tr>
        <tr v-show="abdomen">
          <td>Abdomen Alto</td>
          <td v-for="(item, index) in data.items.abdomen_alto" :key="index">
            <input type="text" class="form-control" v-model="item.value" @keydown="keydown()" />
          </td>
        </tr>
        <tr v-show="abdomen">
          <td>Ombligo</td>
          <td v-for="(item, index) in data.items.ombligo" :key="index">
            <input type="text" class="form-control" v-model="item.value" @keydown="keydown()" />
          </td>
        </tr>
        <tr v-show="abdomen">
          <td>Abdomen Bajo</td>
          <td v-for="(item, index) in data.items.abdomen_bajo" :key="index">
            <input type="text" class="form-control" v-model="item.value" @keydown="keydown()" />
          </td>
        </tr>
        <tr v-show="abdomen">
          <td>Abdomen Bajo Bajo</td>
          <td v-for="(item, index) in data.items.abdomen_bajo_bajo" :key="index">
            <input type="text" class="form-control" v-model="item.value" @keydown="keydown()" />
          </td>
        </tr>
        <tr v-show="piernas">
          <td>Piernas Altas</td>
          <td v-for="(item, index) in data.items.piernas_alta" :key="index">
            <input type="text" class="form-control" v-model="item.value" @keydown="keydown()" />
          </td>
        </tr>
        <tr v-show="piernas">
          <td>Piernas Bajas</td>
          <td v-for="(item, index) in data.items.piernas_baja" :key="index">
            <input type="text" class="form-control" v-model="item.value" @keydown="keydown()" />
          </td>
        </tr>
        <tr v-show="brazos">
          <td>Brazos Alto</td>
          <td v-for="(item, index) in data.items.brazos_alto" :key="index">
            <input type="text" class="form-control" v-model="item.value" @keydown="keydown()" />
          </td>
        </tr>
        <tr v-show="brazos">
          <td>Brazos Medio</td>
          <td v-for="(item, index) in data.items.brazos_medio" :key="index">
            <input type="text" class="form-control" v-model="item.value" @keydown="keydown()" />
          </td>
        </tr>
        <tr v-show="brazos">
          <td>Brazos Bajo</td>
          <td v-for="(item, index) in data.items.brazos_bajo" :key="index">
            <input type="text" class="form-control" v-model="item.value" @keydown="keydown()" />
          </td>
        </tr>
      </tbody>
    </table>
    <div class="row">
      <div class="col-sm-12">
        <small class="pull-right">{{ loader_message }}</small>
      </div>
    </div>
  </div>
</template>

<script>
export default {
    props: ['appointment', 'anthropometry'],
    data() {
        return {
            abdomen: true,
            brazos: false,
            piernas: false,
            data: {
                items: {
                    height: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    weight: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    mass: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    pa: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    abdomen_alto: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    ombligo: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    abdomen_bajo: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    abdomen_bajo_bajo: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    piernas_alta: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    piernas_baja: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    brazos_alto: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    brazos_medio: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    brazos_bajo: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                },
            },
            loader: false,
            loader_message: '',
            errors: [],
        };
    },
    components: {

    },
    methods: {
        selectContent(content) {
            (this.abdomen = false),
            (this.brazos = false),
            (this.piernas = false),
            (this[content] = true);
        },
        newColumn() {
            Object.keys(this.data.items).forEach((key) => {
                this.data.items[key].push({ value: '' });
            });
        },
        next() {
            $('a[href="#treatment"]').tab('show');
        },
        keydown: _.debounce(function () {
            this.save();
        }, 500),

        save() {
            this.loader = true;
            this.loader_message = 'Guardando...';

            if (this.data.id) {
                axios
                    .put(
                        '/appointments/' +
            this.appointment.id +
            '/anthropometries/' +
            this.data.id,
                        this.data
                    )
                    .then(() => {
                        this.emitter.emit('actSummaryAnthropometry', this.data);
                        this.loader_message = 'Cambios Guardados';
                        this.loader = false;
                    })
                    .catch(() => {
                        this.loader = false;
                        flash('Error al guardar cambios', 'danger');
                    });
            } else {
                axios
                    .post(
                        '/appointments/' + this.appointment.id + '/anthropometries',
                        this.data
                    )
                    .then(({ data }) => {
                        this.emitter.emit('actSummaryAnthropometry', this.data);
                        this.loader_message = 'Cambios Guardados';
                        this.loader = false;
                        this.data = data;
                    })
                    .catch(() => {
                        this.loader = false;
                        flash('Error al guardar cambios', 'danger');
                    });
            }
        },
    },
    created() {
        console.log('Component ready. Vital Signs');

        if (this.anthropometry) {
            this.data = this.anthropometry;
            if (!('height' in this.anthropometry.items)) {
                this.data.items = {
                    height: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    weight: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    mass: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    pa: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    abdomen_alto: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    ombligo: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    abdomen_bajo: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    abdomen_bajo_bajo: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    piernas_alta: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    piernas_baja: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    brazos_alto: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    brazos_medio: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                    brazos_bajo: [
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                        {
                            value: '',
                        },
                    ],
                };
            }
        }
    },
};
</script>