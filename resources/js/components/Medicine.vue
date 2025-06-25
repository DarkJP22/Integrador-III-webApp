<template>
    <div>
        <form @submit.prevent="save()">

            <div class="row">

                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="text" name="name" class="form-control" v-model="name" placeholder="Medicamento" required>
                        <form-error v-if="errors.name" :errors="errors" style="float:right;">
                            {{ errors.name[0] }}
                        </form-error>
                    </div>
                </div>




            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="text" name="receta" class="form-control" v-model="receta" placeholder="Receta / Dosis">
                        <form-error v-if="errors.receta" :errors="errors" style="float:right;">
                            {{ errors.receta[0] }}
                        </form-error>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Agregar</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">

                    </div>
                </div>
            </div>

        </form>
    </div>
</template>
<script>
export default {
    props: ['patient', 'itemsLength'],
    data() {
        return {
            name: '',
            receta: '',
            errors: [],
            limit: 10,
            loader: false
        };
    },

    methods: {

        save() {

            axios.post(`/patients/${this.patient.id}/medicines`, {
                name: this.name,
                receta: this.receta,
            })
                .then(({ data }) => {
                    this.loader = false;
                    this.errors = [];

                    this.name = '';
                    this.receta = '';


                    flash('Medicamento Guardado.');
                    this.$emit('created', data);
                })
                .catch(error => {
                    this.loader = false;
                    flash(error.response.data.message, 'danger');
                    this.errors = error.response.data.errors;
                });


        }
    }
};
</script>
