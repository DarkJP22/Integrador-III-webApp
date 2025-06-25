<script setup>
import {onMounted, ref} from 'vue';

const showForm = ref(false);
const form = ref({
    lab_exam_cash_discount: 0,
});
const errors = ref([]);

function openForm() {
    showForm.value = true;

}

async function onSave() {
    try {

        await axios.post('/lab/settings', form.value);

        flash('Configuración guardada correctamente', 'success');
    } catch (error) {
        flash('Ocurrio un error al realizar la operación', 'danger');
        errors.value = error.response.data.errors ? error.response.data.errors : [];
    }


}

async function fetch() {
    const {data} = await axios.get('/lab/settings');
    form.value.lab_exam_cash_discount = data.lab_exam_cash_discount;

}

onMounted(() => {
    fetch();
});
</script>

<template>
    <a class="btn btn-danger tw-float-end" href="#" @click="openForm">Settings</a>
    <div v-show="showForm" class="tw-mt-4 tw-bg-gray-100 tw-py-2">
        <form autocomplete="off" class="" @submit.prevent="onSave()">
            <div class="tw-flex tw-flex-col md:tw-flex-row">
                <div class="form-group">
                    <label class="col-sm-12 control-label" for="location">% Descuento por pago de contado</label>

                    <div class="col-sm-12">
                        <input v-model="form.lab_exam_cash_discount" class="form-control" name="lab_exam_cash_discount" placeholder="" type="number">
                        <form-error v-if="errors.lab_exam_cash_discount" :errors="errors">
                            {{ errors.lab_exam_cash_discount[0] }}
                        </form-error>
                    </div>
                </div>


            </div>

            <div class="tw-pl-6">
                <button class="btn btn-primary" type="submit">Guardar</button>
                <button class="btn btn-secondary" type="button" @click="showForm = false">Cerrar</button>
            </div>
        </form>

    </div>
</template>
