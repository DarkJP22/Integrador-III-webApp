<template>
  <div class="content">

    <allergy :patient="patient" @created="add"></allergy>

    <ul id="medicines-list" class="todo-list ui-sortable" v-show="items.length">

      <li v-for="(item, index) in items" :key="item.id">

        <span> <span class="text">{{ item.name }}</span> <span class="date pull-right"> {{ item.created_at }}</span></span>

        <div class="tools">

          <i class="fa fa-trash-o delete" @click="destroy(item, index)" v-if="authorize('owns', item)"></i>
        </div>
      </li>

    </ul>


  </div>
</template>
<script>
import collection from '../mixins/collection';
import Allergy from './Allergy.vue';

export default {
    props: ['allergies', 'patient'],
    data() {
        return {
            items: this.allergies,

        };
    },
    components: { Allergy },
    mixins: [collection],
    methods: {

        destroy(item, index) {
            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                axios.delete(`/allergies/${item.id}`)
                    .then(() => {

                        this.remove(index);

                        flash('Alergia Eliminada!');

                    }).catch(error => {

                        flash(error.response.data.message, 'danger');

                    });
            }

        },



    }

};
</script>
