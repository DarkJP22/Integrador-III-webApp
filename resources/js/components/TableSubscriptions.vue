<template>
  <div>

    <table class="table table-bordered">
      <tbody>
        <tr>

          <th class="text-center">Subscripción</th>
          <th class="text-center">Costo</th>
          <th class="text-center">Acción</th>

        </tr>

        <!-- <tr>
                      <td colspan="3"><label for="fe">¿Desear utilizar Factura Electronica? </label><br>
                      <div class="form-group radios-fe">
                        <div class="radio">
                          <label>
                            <input type="radio" name="fe" id="fe-no" value="0" v-model="fe">
                            No
                          </label>
                        </div>
                        <div class="radio">
                          <label>
                            <input type="radio" name="fe" id="fe-si" value="1" v-model="fe">
                            Si
                          </label>
                        </div>
                        
                      </div>
                      </td>
                    </tr> -->

        <tr v-for="subscription in subscriptionsPackages" :key="subscription.id">

          <td>{{ subscription.title }}</td>
          <td>{{ subscription.costName }}</td>

          <td>

            <a :href="(currentPlan == subscription.id) ? '#' : getUrl(subscription, 'change')" class="btn" :class=" currentPlan == subscription.id ? 'btn-secondary' : 'btn-primary'" :disabled="currentPlan == subscription.id" v-if="subscription.cost > 0">{{ currentPlan == subscription.id ? 'Actual' : 'Seleccionar' }}</a>
            <form :action="getUrl(subscription, 'changefree')" method="POST" v-else data-confirm="Esta a punto de cambiarte al plan gratuito. ¿Estas Seguro que desear realizar esta accion?">
              <input type="hidden" name="_token" :value="token">
              <input type="hidden" name="_method" value="PUT">
              <button type="submit" class="btn" :class=" currentPlan == subscription.id ? 'btn-secondary' : 'btn-primary'" :disabled="currentPlan == subscription.id">{{ currentPlan == subscription.id ? 'Actual' : 'Seleccionar' }}</button>
            </form>
          </td>


        </tr>




      </tbody>
    </table>



  </div>
</template>

<script>



export default {

    props: ['change', 'currentPlan'],

    data() {
        return {
            loader: false,
            subscriptionsPackages: [],
            fe: 0,
            token: ''
        };
    },

    methods: {
        getUrl(subscription, changeFree) {

            if (subscription.for_medic == 1) {

                return (this.change && this.change == 1) ? '/medic/subscriptions/' + subscription.id + '/' + changeFree : '/medic/subscriptions/' + subscription.id + '/buy';
            }

            if (subscription.for_clinic == 1) {
                return (this.change && this.change == 1) ? '/clinic/subscriptions/' + subscription.id + '/' + changeFree : '/clinic/subscriptions/' + subscription.id + '/buy';
            }

            if (subscription.for_pharmacy == 1) {
                return (this.change && this.change == 1) ? '/pharmacy/subscriptions/' + subscription.id + '/' + changeFree : '/pharmacy/subscriptions/' + subscription.id + '/buy';
            }



        },
        money(n) {
            return n.toLocaleString();//toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
        },

        getSubscriptionsPackages() {


            axios.get('/plans').then(this.refresh);

        },

        refresh({ data }) {

            this.subscriptionsPackages = data;

        },


    }, //methods

    created() {
        this.token = document.head.querySelector('meta[name="csrf-token"]').content;
        console.log('Component ready. Modal Appointments');

        this.getSubscriptionsPackages();
    }
};
</script>