<template>
    <div>
        <form @submit.prevent="save()">

            <div class="row">

                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="name">Nombre del medicamento</label>
<!--                        <input v-model="name" class="form-control" name="name" placeholder="Medicamento" required-->
<!--                               type="text">-->
                        <SelectDrug @selected-drug="onSelectDrug" @create-drug="onCreateDrug" v-if="!isCreating"  />
                        <div class="tw-flex tw-flex-wrap tw-gap-2" v-if="isCreating">
                            <div>
                                <label for="date_purchase">Nombre</label>
                                <input v-model="name" class="form-control" name="name" placeholder="Medicamento" required type="text" >

                            </div>
                            <div>
                                <label for="date_purchase">Fuerza / Concentración</label>
                                <input v-model="concentration" class="form-control" name="concentration" placeholder="2ml" type="text" >

                            </div>
                            <div>
                                <label for="date_purchase">Presentación</label>
<!--                                <input v-model="presentation" class="form-control" name="presentation" placeholder="Gotas" type="text" >-->
                                <select v-model="presentation" class="form-control" name="presentation">
                                    <option value=""></option>
                                    <option :value="presentation" v-for="presentation in presentations" :key="presentation">{{ presentation }}</option>
                                </select>

                            </div>
                            <div>
                                <label for="date_purchase">Cantidad / Volumen</label>
                                <input v-model="quantity" class="form-control" name="quantity" placeholder="" type="text" >

                            </div>
                            <div>
                                <label for="date_purchase">Marca</label>
                                <input v-model="brand" class="form-control" name="brand" placeholder="" type="text" >

                            </div>

                        </div>

                        <form-error v-if="errors.name" :errors="errors" style="float:right;">
                            {{ errors.name[0] }}
                        </form-error>
                    </div>
                </div>
<!--                <div class="col-sm-6">-->
<!--                    <div class="form-group">-->
<!--                        <label for="date_purchase">Fecha de próxima compra</label>-->
<!--                        <flat-pickr-->
<!--                            v-model="date_purchase"-->
<!--                            class="form-control"-->
<!--                            placeholder="Selecione una fecha"-->
<!--                            name="date_purchase">-->
<!--                        </flat-pickr>-->
<!--                        &lt;!&ndash;                        <div class="input-group">-->
<!--                                                    <input type="text" class="form-control datepicker" v-model="date_purchase" @blur="onBlurDate">-->
<!--                        -->
<!--                                                    <div class="input-group-addon">-->
<!--                                                        <i class="fa fa-calendar"></i>-->
<!--                                                    </div>-->
<!--                                                </div>&ndash;&gt;-->
<!--                        <form-error v-if="errors.date_purchase" :errors="errors" style="float:right;">-->
<!--                            {{ errors.date_purchase[0] }}-->
<!--                        </form-error>-->
<!--                    </div>-->
<!--                </div>-->


            </div>
            <!-- <div class="row">
             <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" name="receta" class="form-control"  v-model="receta" placeholder="Receta / Dosis">
                    <form-error v-if="errors.receta" :errors="errors" style="float:right;">
                            {{ errors.receta[0] }}
                        </form-error>
                </div>
            </div>
        </div> -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">Agregar</button>
                        <button class="btn btn-secondary" type="button" @click="isCreating = false" v-if="isCreating">Cancelar</button>
                        <img v-show="loader" alt="Cargando..." src="/img/loading.gif">

                    </div>
                </div>
            </div>

        </form>
    </div>
</template>
<script>
//import flatPickr from 'vue-flatpickr-component';
//import 'flatpickr/dist/flatpickr.css';
import SelectDrug from '@/components/SelectDrug.vue';
export default {
    components: {SelectDrug},
    props: ['patient', 'itemsLength', 'presentations'],
    data() {
        return {
            date_purchase: '',//moment().format('YYYY-MM-DD'),
            name: '',
            presentation:'',
            concentration:'',
            brand:'',
            quantity:'',
            receta: '',
            errors: [],
            limit: 10,
            loader: false,
            isCreating:false
        };
    },

    methods: {
        /*onBlurDate(e) {

            this.date_purchase = e.target.value;
            this.$emit('input');
        },*/
        onCreateDrug() {
            console.log('Crear medicamento');
            this.name = '';
            this.presentation = '';
            this.concentration = '';
            this.quantity = '';
            this.brand = '';
            this.isCreating = true;

        },
        onSelectDrug(drug) {
            this.name = drug?.name;
        },
        save() {
            if(this.isCreating){
                this.name = this.name + ' ' + this.concentration + ' ' + this.presentation + ' ' + this.quantity + ' ' + this.brand;
            }
            axios.post(`/pharmacy/patients/${this.patient.id}/medicines`, {
                name: this.name,
                receta: this.receta,
                date_purchase: this.date_purchase,

            })
                .then(({data}) => {
                    this.loader = false;
                    this.errors = [];

                    this.name = '';
                    this.receta = '';
                    this.isCreating = false;

                    flash('Medicamento Guardado.');
                    this.$emit('created', data);
                    this.emitter.emit('createdMedicinePharmacy', data);
                })
                .catch(error => {
                    flash(error.response.data.message, 'danger');
                    this.errors = error.response.data.errors;
                });


        }
    }
};
</script>
