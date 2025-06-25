<template>
    <form class="inteform form-horizontal" @submit.prevent="save()">
        <div class="form-group">
            <label for="inte_office_name" class="col-sm-2 control-label">Nombre</label>
            <div class="col-sm-10">

                <input type="text" class="form-control" name="name" placeholder="Nombre de la clínica u hospital" v-model="inteOffice.name">
                <form-error v-if="errors.name" :errors="errors" style="float:right;">
                    {{ errors.name[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label for="inte_office_phone" class="col-sm-2 control-label">Teléfono</label>

            <div class="col-sm-10">
                <input type="text" class="form-control" name="phone" placeholder="Teléfono" v-model="inteOffice.phone">
                <form-error v-if="errors.phone" :errors="errors" style="float:right;">
                    {{ errors.phone[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <label for="inte_office_address" class="col-sm-2 control-label">Dirección</label>

            <div class="col-sm-10">
                <input type="text" class="form-control" name="address" placeholder="Dirección" v-model="inteOffice.address">
                <form-error v-if="errors.address" :errors="errors" style="float:right;">
                    {{ errors.address[0] }}
                </form-error>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-danger" :disabled="loader">Enviar Solicitud</button><img src="/img/loading.gif" alt="Cargando..." v-show="loader">
            </div>
        </div>
    </form>
</template>
<script>

export default {
    data() {
        return {
            inteOffice: {
                name: '',
                phone: '',
                address: ''
            },
            errors: [],
            loader: false,
        };
    },
    methods: {

        save() {

            if (this.loader) return;

            this.loader = true;


            axios.post('/offices/requests', this.inteOffice)
                .then(({ data }) => {
                    this.loader = false;
                    this.errors = [];
                    this.inteOffice = {};
                    flash('Su solicitud de integracion de clinic fue enviado correctamente.');
                    this.$emit('created', data);

                })
                .catch(error => {
                    this.loader = false;
                    flash(error.response.data.message, 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });



        }


    }

};
</script>
