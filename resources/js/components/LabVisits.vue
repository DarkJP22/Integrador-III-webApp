<template>
    <div class="box">
        <div class="box-header">
            <a class="btn btn-primary" href="#" @click="openForm">Nuevo</a>
            <div v-show="showForm" class="tw-mt-4 tw-bg-gray-100 tw-py-2">
                <form autocomplete="off" class="" @submit.prevent="onSave()">
                    <div class="tw-flex tw-flex-col md:tw-flex-row">
                        <div class="form-group">
                            <label class="col-sm-12 control-label" for="location">Lugar</label>

                            <div class="col-sm-12">
                                <input v-model="form.location" class="form-control" name="location" placeholder="" type="text">
                                <form-error v-if="errors.location" :errors="errors">
                                    {{ errors.location[0] }}
                                </form-error>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label" for="location">Dias</label>

                            <div class="col-sm-12 tw-flex tw-flex-col md:tw-flex-row md:tw-space-x-6 tw-flex-wrap">
                                <div v-for="day in days" :key="day" class="form-check">
                                    <input :id="'day-' + day" v-model="form.schedule" :value="day" class="form-check-input" type="checkbox">
                                    <label :for="'day-' + day" class="form-check-label">
                                        {{ day }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tw-pl-6">
                        <button class="btn btn-primary" type="submit">Guardar</button>
                        <button class="btn btn-secondary" type="button" @click="showForm = false">Cerrar</button>
                    </div>
                </form>

                <input v-model="visit_location" type="text">
                <button class="btn btn-secondary" type="button" @click="testVisit()">Save</button>
            </div>
            <div>
                <form autocomplete="off" @submit.prevent="fetch()">
                    <div class="form-group">

                        <div class="col-sm-3">
                            <div class="input-group input-group">


                                <input v-model="q" class="form-control pull-right" name="q" placeholder="Buscar..." type="text">
                                <div class="input-group-btn">

                                    <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                                </div>


                            </div>
                        </div>

                        <div class="col-sm-2">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </div>

                    </div>

                </form>
            </div>

            <div class="box-tools">

            </div>
        </div>
        <!-- /.box-header -->
        <div id="no-more-tables" class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Lugar</th>
                        <th>Dias</th>
                        <th>Acciones</th>

                    </tr>
                </thead>
                <tbody>
                    <tr v-for="visit in visits" :key="visit.id">

                        <td data-title="Lugar">{{ visit.location?.toUpperCase() }}</td>
                        <td data-title="Dias">
                            <ul>
                                <li v-for="day in visit.schedule" :key="day">{{ day }}</li>
                            </ul>


                        </td>
                        <td>
                            <button class="btn btn-primary" type="button" @click="onEdit(visit)">Editar</button>
                            <button class="btn btn-danger" type="button" @click="onDelete(visit.id)">Eliminar</button>
                        </td>

                    </tr>
                </tbody>

                <tfoot>
                    <tr>
                        <td class="pagination-container" colspan="2">
                            <paginator :dataSet="dataSet" :noUpdateUrl="true" @changed="fetch"></paginator>
                        </td>
                    </tr>
                </tfoot>


            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</template>
<script>


export default {
    data() {
        return {
            visits: [],
            dataSet: {},
            q: '',
            form: {
                location: '',
                schedule: []
            },
            days: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
            errors: [],
            showForm: false,
            visit_location: ''
        };
    },
    computed: {
        isEdit() {
            return !!this.form.id;
        }
    },
    methods: {
        async testVisit() {
            console.log(this.visit_location);
            await axios.put('/lab/visits/settings', { visit_location: this.visit_location});
        },
        openForm() {
            this.showForm = true;
            this.form = {
                location: '',
                schedule: []
            };
            this.errors = [];
        },
        onEdit(item) {
            this.openForm();
            Object.assign(this.form, item);
        },
        async onSave() {
            try {
                if (this.isEdit) {
                    await axios.put('/lab/visits/' + this.form.id, this.form);

                    flash('Visita actualizada', 'success');
                } else {
                    await axios.post('/lab/visits', this.form);
                    flash('Visita Creada', 'success');
                }
                this.showForm = false;
                this.fetch();

            } catch (error) {
                flash('Ocurrio un error al realizar la operación', 'danger');
                this.errors = error.response.data.errors ? error.response.data.errors : [];
            }

        },
        async onDelete(visitId) {
            const resp = confirm('¿Desear Eliminar este registro?');
            if (!resp) {
                return;
            }

            try {

                await axios.delete('/lab/visits/' + visitId);

                flash('Visita eliminada', 'success');


                this.fetch();

            } catch (error) {
                flash('Ocurrio un error al realizar la operación', 'danger');
                this.errors = error.response.data.errors ? error.response.data.errors : [];
            }

        },
        async fetch(page) {
            if (!page) {
                page = 1;
            }

            const { data } = await axios.get('/lab/visits', {
                params: { q: this.q, page }
            });
            this.visits = data.data;
            this.dataSet = data;
        }
    },
    async mounted() {
        this.fetch();
    },

};
</script>