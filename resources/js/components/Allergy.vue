<template>
    <div>
        <form @submit.prevent="save()">

            <div class="row">

                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="text" name="name" class="form-control" v-model="name" placeholder="Alergia" required>
                        <form-error v-if="errors.name" :errors="errors" style="float:right;">
                            {{ errors.name[0] }}
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
            errors: [],
            limit: 10,
            loader: false
        };
    },

    methods: {

        save() {

            axios.post(`/patients/${this.patient.id}/allergies`, {
                name: this.name,

            })
                .then(({ data }) => {
                    this.loader = false;
                    this.errors = [];

                    this.name = '';


                    flash('Alergia Guardada.');
                    this.$emit('created', data);
                })
                .catch(error => {
                    flash(error.response.data.message, 'danger');
                    this.errors = error.response.data.errors;
                });


        }
    }
};
</script>
