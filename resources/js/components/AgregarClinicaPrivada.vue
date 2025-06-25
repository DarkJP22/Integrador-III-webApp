<template>
  <div>

    <div class="row">

      <div class="col-xs-12 col-sm-8">

        <select-office :office="office" @selectedOffice="select" type="2"></select-office>
      </div>

      <div class="col-xs-12 col-sm-3">




        <a href="#" class="btn btn-primary " @click="assignToMedic()" :class="!office.id ? 'disabled' : ''">Agregar</a><img src="/img/loading.gif" alt="Cargando..." v-show="loader">

      </div>


    </div>


  </div>
</template>

<script>

import collection from '../mixins/collection';
import SelectOffice from './SelectOffice.vue';

export default {
    props: ['offices'],

    data() {
        return {
            dataSet: false,

            newOffice: false,
            integraOffice: false,

            office: false,
            loader: false,



        };
    },
    mixins: [collection],
    components: {
        SelectOffice
    },
    methods: {


        assignToMedic() {
            this.loader = true;
            return axios.post('/offices/' + this.office.id + '/assign', { office: this.office, obligado_tributario: this.office.type == '2' ? 'C' : 'M' })
                .then(({ data }) => {
                    this.loader = false;

                    flash('Consultorio agregado.');
                    this.add(data);
                    this.emitter.emit('officeToSelect');
                    this.office = false;

                })
                .catch(error => {
                    this.loader = false;

                    flash(error.response.data.message, 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });



        },



        select(office) {

            this.office = office;

        },


    },
    created() {
        console.log('Component ready. office');


    }
};
</script>
<style>
.create-buttons {
  margin: 1rem 0;
}
</style>
