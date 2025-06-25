<template>
    <form @submit.prevent="save" class="form-horizontal">
        <div class="form-group">

            <label for="code" class="col-sm-2 control-label">Código</label>
            <div class="col-sm-10">
                <select class="form-control custom-select" name="code" id="code" v-model="form.code">


                    <option v-for="codeTax in codesTaxes" :key="codeTax.code" :value="codeTax.code">{{ codeTax.code }} - {{ codeTax.name }}</option>

                </select>

                <form-error v-if="errors.code" :errors="errors" style="float:right;">
                    {{ errors.code[0] }}
                </form-error>

            </div>
        </div>
        <div class="form-group">

            <label for="CodigoTarifa" class="col-sm-2 control-label">Código de Tarifa</label>
            <div class="col-sm-10">
                <select class="form-control custom-select" name="CodigoTarifa" id="CodigoTarifa" v-model="form.CodigoTarifa" @change="onChangeCodigoTarifa">


                    <option v-for="codeTarifa in codesTarifaIva" :key="codeTarifa.code" :value="codeTarifa.code">{{ codeTarifa.code }} - {{ codeTarifa.name }}</option>

                </select>

                <form-error v-if="errors.CodigoTarifa" :errors="errors" style="float:right;">
                    {{ errors.CodigoTarifa[0] }}
                </form-error>

            </div>
        </div>

        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Nombre</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" name="name" placeholder="" v-model="form.name">
                <form-error v-if="errors.name" :errors="errors" style="float:right;">
                    {{ errors.name[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label for="tarifa" class="col-sm-2 control-label">% Tarifa</label>
            <div class="col-sm-10">

                <input type="text" class="form-control" id="tarifa" name="tarifa" placeholder="Ej: 13" v-model="form.tarifa">


                <form-error v-if="errors.tarifa" :errors="errors" style="float:right;">
                    {{ errors.tarifa[0] }}
                </form-error>
            </div>
        </div>




        <button type="submit" class="btn btn-primary">Guardar</button>

        <a href="/taxes" class="btn btn-default"> Regresar</a>
    </form>
</template>
<script>
export default {
    props: ['tax', 'codesTaxes', 'codesTarifaIva'],

    data() {
        return {
            form: {
                code: this.tax ? this.tax.code : '',
                name: this.tax ? this.tax.name : '',
                tarifa: this.tax ? this.tax.tarifa : 0,
                CodigoTarifa: this.tax ? this.tax.CodigoTarifa : '08',
            },
            loader: false,
            errors: []
            // tarifa: this.tax ? this.tax.tarifa : 0,
            // CodigoTarifa: this.tax ? this.tax.CodigoTarifa : '08',
        };
    },
    methods: {
        clear() {
            this.form = {
                code: '',
                name: '',
                tarifa: 0,
                CodigoTarifa: '08',

            };
        },
        async save() {

            try {
                this.loader = true;
                if (this.tax?.id) {
                    await axios.put('/taxes/' + this.tax.id, this.form);
                } else {
                    await axios.post('/taxes', this.form);
                }
                this.loader = false;
                this.errors = [];
                this.clear();
                flash('Impuesto guardado.');

                window.location.href = '/taxes';

            } catch (error) {
                this.loader = false;
                flash('Error al guardar impuesto', 'danger');
                this.errors = error.response.data.errors ? error.response.data.errors : [];
            }

        },
        onChangeCodigoTarifa() {
  
            const ct = _.find(this.codesTarifaIva, { 'code': this.form.CodigoTarifa });

            this.form.tarifa = ct.value;


        }
    },
    created() {
        if (!this.tax) {
            this.onChangeCodigoTarifa();
        }

    }
};
</script>
