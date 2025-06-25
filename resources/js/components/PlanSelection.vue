<script setup>
import {ref} from 'vue';
import BaseFormatMoney from '@/components/base/BaseFormatMoney.vue';

const props = defineProps({
    plans: {
        type: Array,
        required: true
    },
    oldSelection: {
        type: [Number],
        default: null
    }
});
const selected = ref(props.oldSelection ? props.oldSelection : props.plans[0].id);
</script>

<template>
    <div class="tw-bg-white">
        <div class="tw-container tw-px-0 tw-pb-8 tw-mx-auto">


            <h1 class="tw-mt-4 tw-text-3xl tw-font-semibold tw-text-center tw-text-gray-800 tw-capitalize lg:tw-text-4xl"> Escoge tu plan</h1>


            <div class="tw-mt-6 tw-space-y-8 xl:tw-mt-12">

                <div v-for="plan in plans" :key="plan.id" :class="{ 'tw-border-blue-500' : selected === plan.id }" class="tw-flex tw-items-center tw-justify-between tw-max-w-2xl tw-px-4 tw-py-6 tw-mx-auto tw-border-solid tw-border tw-text-gray-300 tw-cursor-pointer tw-rounded-xl" @click="selected = plan.id">
                    <div class="tw-flex tw-items-center">
                        <svg :class="{ 'tw-text-blue-500' : selected === plan.id }" class="tw-w-5 tw-h-5 tw-text-gray-400 sm:tw-h-9 sm:tw-w-9" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" fill-rule="evenodd" />
                        </svg>

                        <div class="tw-flex tw-flex-col tw-items-start tw-mx-2 tw-space-y-2">
                            <h2 :class="{ 'tw-text-blue-500' : selected === plan.id }" class="tw-my-0 tw-text-lg tw-font-medium tw-text-gray-700 sm:tw-text-2xl">{{ plan.title  }}</h2>
                            <div v-if="selected === plan.id && plan.commission_by_appointment" class="tw-px-2 tw-text-sm tw-text-blue-500 tw-bg-blue-100 tw-rounded-full sm:tw-px-4 sm:tw-py-1 ">
                                Visible para recibir citas.
                            </div>


                        </div>
                    </div>
                    <div class="tw-flex tw-flex-col tw-space-y-2">
                        <h2 :class="{ 'tw-text-blue-500' : selected === plan.id }" class="tw-my-0 tw-text-2xl tw-font-semibold tw-text-gray-500 sm:tw-text-xl"><base-format-money :amount="plan.cost"></base-format-money> <span class="tw-text-base tw-font-medium">/Mes</span></h2>
                        <div v-if="selected === plan.id" class="tw-px-2 tw-text-sm tw-text-blue-500 tw-bg-blue-100 tw-rounded-full sm:tw-px-4 sm:tw-py-1 ">
                            Uso de plataforma
                        </div>
                        <div v-if="selected === plan.id && plan.commission_by_appointment" class="tw-px-2 tw-text-sm tw-text-blue-500 tw-bg-blue-100 tw-rounded-full sm:tw-px-4 sm:tw-py-1 ">
                            {{ plan.commission_by_appointment ? '+ Comisión x cita' : '' }}
                        </div>
                    </div>


                </div>




               <input :value="selected" name="plan_id" type="hidden" />
            </div>
        </div>
    </div>
</template>
