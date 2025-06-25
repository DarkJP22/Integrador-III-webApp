<template>
  <div class="box box-default">
    <div class="box-header with-border">
      Códigos de Autorización de registro de médicos
    </div>
    <div class="box-body">
      <button type="submit" class="btn btn-primary" @click="generate">Generar Nuevo Código</button>
      <h4>Ultimo código generado</h4>
      <div class="tw-text-6xl tw-text-center tw-mt-4 tw-bg-gray-200 tw-py-4">
        {{ last_code }}
      </div>
    </div>
  </div>
</template>
<script>
export default {
    data() {
        return {
            errors: [],
            loader: false,
            last_code: ''
        };
    },
    methods: {
        async generate() {
            if (this.loader) return;

            const { data } = await axios.post('/admin/register-authorization-codes');
            this.last_code = data.code;

        },
        async getLastCode() {
            const { data } = await axios.get('/admin/register-authorization-codes');
            this.last_code = data.data[0]?.code;
        }
    },
    created() {
        this.getLastCode();
    }
};
</script>
