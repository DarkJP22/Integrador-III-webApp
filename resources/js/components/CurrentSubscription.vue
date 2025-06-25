<script setup>
import {reactive, ref, watch} from 'vue';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    plans: {
        type: Array,
        required: true,
    },
});
const form = reactive({
    plan_id: props.user.subscription?.plan_id,
    cost: props.user.subscription?.cost,
    ends_at: props.user.subscription?.ends_at,
    previous_billing_date: props.user.subscription?.previous_billing_date,
    errors: null,
});
const subscription = ref(props.user.subscription);

async function save(){
    try {
        const {data} = await axios.put('/subscriptions/'+ subscription.value.id, form);
        subscription.value = data;
        flash('Subscripción actualizada');
    } catch (error) {
        console.log(error);
        flash('ha ocurrido un error al actualizar el plan', 'error');
    }
}
async function onBilling(){
    if(!confirm('¿Está seguro de ejecutar el cobro?. Esto generara una factura a nombre del usuario.')){
        return;
    }

    try {
        const {data} = await axios.post('/subscriptions/'+ subscription.value.id + '/billing');
        subscription.value = data;
        flash('Cobro ejecutado');
    } catch (error) {
        console.log(error);
        flash('ha ocurrido un error al ejecutar el cobro', 'error');
    }

}

watch(
    () => form.plan_id,
    (newPlan) => {
        form.cost = props.plans.find(p => p.id === newPlan)?.cost ?? 0;
    }
);

watch(subscription,
    (newSubscription) => {
        form.plan_id = newSubscription?.plan_id;
        form.cost = newSubscription?.cost;
        form.ends_at = newSubscription?.ends_at;
        form.previous_billing_date = newSubscription?.previous_billing_date;
    }
);

</script>

<template>
    <div>

        <a v-if="subscription" :data-target="'#modalSubscriptionDetail-'+ subscription.id" class="btn btn-xs btn-info"
           data-toggle="modal"
           href="#"
           title="Detalle">{{ subscription?.plan?.title }}
        </a>
    </div>
    <div v-if="subscription" :id="'modalSubscriptionDetail-'+ subscription.id" aria-labelledby="modalSubscriptionDetail" class="modal fade" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header tw-bg-teal-500 tw-text-white">
                    <div class="tw-flex tw-justify-between tw-items-center">

                        <h4 :id="'modalSubscriptionDetailLabel-'+ subscription.id" class="modal-title">Detalle Subscripcion
                        </h4>
                        <div class="">

                        </div>
                    </div>

                </div>

                <div class="modal-body ">
                    <form class="" @submit.prevent="save()">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="plan_id">Plan</label>
                            <div class="col-sm-12 tw-mb-4">
                                <select v-model="form.plan_id" class="form-control" name="plan_id" required style="width: 100%;">

                                    <option v-for="plan in plans" :key="plan.id" :value="plan.id">{{ plan.title }}</option>


                                </select>

                                <form-error v-if="form.errors?.plan_id" :errors="form.errors" style="float:right;">
                                    {{ form.errors.plan_id[0] }}
                                </form-error>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label" for="cost">Costo</label>

                            <div class="col-sm-12 tw-mb-4">
                                <input v-model="form.cost" class="form-control" name="cost" placeholder="" type="text">
                                <form-error v-if="form.errors?.cost" :errors="form.errors" style="float:right;">
                                    {{ form.errors.cost[0] }}
                                </form-error>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-12 control-label" for="cost">Ultima Facturación</label>

                            <div class="col-sm-12 tw-mb-4">
                                <flat-pickr v-model="form.previous_billing_date" class="form-control" disabled>
                                </flat-pickr>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label" for="cost">Próxima Facturación</label>

                            <div class="col-sm-12 tw-mb-4">
                                <flat-pickr v-model="form.ends_at" class="form-control">
                                </flat-pickr>
                            </div>

                        </div>
                        <div class="form-group col-sm-12 tw-flex tw-gap-2">
                            <button class="btn btn-primary" type="submit">
                                Guardar
                            </button>
                            <button class="btn btn-danger" type="button" @click="onBilling">
                                Ejecutar cobro
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">


                    <button class="btn btn-default" data-dismiss="modal" type="button">
                        Cerrar
                    </button>


                </div>
            </div>
        </div>
    </div>
</template>

