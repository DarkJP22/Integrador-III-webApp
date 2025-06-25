<template>
    <input ref="inputRef" class="border-gray-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-md shadow-sm" type="text" />
</template>

<script setup>
import {computed, watch, ref, onMounted} from 'vue';
import { useCurrencyInput } from 'vue-currency-input';
import { usePage } from '@inertiajs/vue3';

const appCurrency = computed(() => usePage().props.app_currency?.code);
const emit = defineEmits(['update:modelValue', 'change', 'update:currency']);

const props = defineProps({
    modelValue: Number,
    currency: String
});

const options = ref({
    locale: 'en-US',
    currency: props.currency ?? appCurrency.value ?? 'USD',
    currencyDisplay: 'hidden',
    hideGroupingSeparatorOnFocus:false

});

const { inputRef, setValue, setOptions } = useCurrencyInput(options.value);

watch(
    () => props.modelValue, // Vue 2: props.value
    (value) => {
        setValue(value);
        emit('update:modelValue', value);
    }
);
watch(
    () => props.currency,
    (newCurrency) => {
        options.value.currency = newCurrency ?? appCurrency.value ?? 'USD';
        setOptions(options.value);
        emit('update:currency', options.value.currency);
    }
);

onMounted(() => {
    if (inputRef.value.hasAttribute('autofocus')) {
        inputRef.value.focus();
    }
});
defineExpose({ focus: () => inputRef.value.focus() });
</script>
