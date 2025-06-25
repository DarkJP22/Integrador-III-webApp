<template>
    <span style="font-family: sans-serif">{{ formattedAmount }}</span>
</template>

<script setup>
import useUtilities from '@/composables/useUtilities';
import { computed } from 'vue';
const { moneyFormat } = useUtilities();
const props = defineProps({
    amount: {
        type: [Number, String],
        required: true,
    },
    currency: {
        type: Object,
        default: () => {
            return null;
        },
    },

});

const formattedAmount = computed(() => {
    return moneyFormat(
        props.amount,
        props.currency,
    );
});
</script>
