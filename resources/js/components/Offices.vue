<template>
    <div>

        <div class="row">

            <div class="col-xs-12 col-sm-8">

                <select-office :office="office" type="2" @selectedOffice="select"></select-office>
            </div>

            <div class="col-xs-12 col-sm-3">




                <a :class="!office.id ? 'disabled' : ''" class="btn btn-success " href="#" @click="assignToMedic()">Agregar</a><img v-show="loader" alt="Cargando..." src="/img/loading.gif">

            </div>


        </div>

        <div class="form-inline create-buttons">
            <div class="form-group">
                <a class="btn btn-secondary " href="#" title="Selecciona esta opción si usted va a ser el único médico que trabajará en su consultorio" @click="nuevo()">Crear Consultorio Independiente</a>

            </div>
            <div class="form-group">
                <a class="btn btn-secondary " href="#" title="Selecciona esta opción si va a trabajar con otros médicos en sus instalaciones" @click="integrar()">Integrar Clínica Privada</a>

            </div>
        </div>








        <integration-office v-show="integraOffice"></integration-office>

        <new-office v-show="newOffice" @created="add" @updated="update"></new-office>


        <h3>Tus Consultorios o clínicas</h3>
        <ul v-show="items.length" id="offices-list" class="todo-list ui-sortable">

            <li v-for="(office, index) in items" :key="office.id">
                <!-- todo text -->
                <a href="#clinics" @click.prevent="edit(office)"><i class="fa fa-building"></i><span><span class="text">{{ office.name }}</span></span></a>
                <!-- General tools such as edit or delete-->
                <div class="tools">
                    <!-- <i class="fa fa-edit" @click="edit(item)"></i> -->
                    <i class="fa fa-trash-o delete" @click="destroy(office, index)"></i>
                </div>
            </li>

        </ul>
        <paginator :dataSet="dataSet" :no-update-url="true" @changed="fetch"></paginator>
    </div>
</template>

<script>
import IntegrationOffice from './IntegrationOffice.vue';
import NewOffice from './NewOffice.vue';
import collection from '../mixins/collection';
import SelectOffice from './SelectOffice.vue';

export default {
    props: ['offices'],

    data() {
        return {
            dataSet: false,

            newOffice: false,
            integraOffice: false,

            office: false,
            loader: false,



        };
    },
    mixins: [collection],
    components: {
        NewOffice,
        IntegrationOffice,
        SelectOffice
    },
    methods: {
        integrar() {

            this.integraOffice = !this.integraOffice;
            this.newOffice = false;

        },
        nuevo() {


            this.newOffice = !this.newOffice;
            this.integraOffice = false;

            this.emitter.emit('newOffice', {});



        },

        assignToMedic() {
            this.loader = true;
            return axios.post('/offices/' + this.office.id + '/assign', { office: this.office, obligado_tributario: this.office.type == '2' ? 'C' : 'M' })
                .then(({ data }) => {
                    this.loader = false;

                    flash('Consultorio agregado.');
                    this.add(data);
                    this.emitter.emit('officeToSelect');
                    this.office = false;

                })
                .catch(error => {
                    this.loader = false;

                    flash(error.response.data.message, 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });

            //  Swal.fire({
            //     title: 'Quien es el responsable tributario?',
            //     text: "A nombre de quien saldran las facturas?",
            //     type: 'warning',
            //     input: 'select',
            //     inputOptions: {
            //         'M': 'Médico',
            //         'C': 'Clínica',

            //     },
            //     //inputValue: 'M',
            //     inputPlaceholder: '¿Médico o Clínica?',
            //     showCancelButton: false,
            //     confirmButtonText: 'Asignar',
            //     showLoaderOnConfirm: true,
            //     cancelButtonText: 'Cancel',
            //     inputValidator: (value) => {
            //         return new Promise((resolve) => {
            //         if (value === '') {
            //             resolve('Necesitas seleccionar un obligado tributario')
            //         } else {
            //             resolve()
            //         }
            //         })
            //     },
            //     preConfirm: (data) => {

            //         return axios.post('/offices/'+ this.office.id+'/assign', { office: this.office, obligado_tributario: data })
            //             .then(({data}) => {
            //                 this.loader = false;

            //                 flash('Consultorio agregado.');
            //                 this.add(data)
            //                 this.emitter.emit('officeToSelect');
            //                 this.office = false

            //             })
            //             .catch(error => {
            //                 this.loader = false;

            //                 flash(error.response.data.message, 'danger');
            //                 this.errors = error.response.data.errors ? error.response.data.errors : [];
            //             });

            //     },
            //     allowOutsideClick: () => !Swal.isLoading()

            // })
            // .then( (result) => {


            //     if (result.value) {

            //          Swal.fire({
            //             title: `Consultorio agregado correctamente`,

            //             })

            //     } 

            // });


        },

        edit(office) {

            this.newOffice = true;
            this.integraOffice = false;
            this.emitter.emit('editOffice', office);

        },

        select(office) {

            this.office = office;

        },


        destroy(item, index) {

            Swal.fire({
                title: 'Deseas eliminar el consultorio o clinica?',
                text: 'Requerda que te desvincularás de la clinica!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#67BC9A',
                cancelButtonColor: '#dd4b39',
                confirmButtonText: 'Eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {

                if (result.value) {

                    axios.delete(`/offices/${item.id}`)
                        .then(() => {

                            this.remove(index);

                            flash('Consultorio Eliminado!');
                            this.newOffice = false;

                        }).catch(error => {

                            flash(error.response.data.message, 'danger');

                        });

                    Swal.fire(
                        'Eliminado!',
                        'Consultorio Eliminado.',
                        'success'
                    );

                }



            });


        },

        fetch(page) {
            axios.get(this.url(page)).then(this.refresh);
        },
        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            return `/offices?medic=1&page=${page}`;
        },
        refresh({ data }) {
            this.dataSet = data;
            this.items = data.data;
            const clinicasIndependientes = _.filter(data.data, { 'type': 1 });
            this.emitter.emit('loadedOffices', clinicasIndependientes);
            window.scrollTo(0, 0);
        },


    },
    created() {
        console.log('Component ready. office');

        this.fetch();
        this.emitter.on('input', this.changeValue);
    }
};
</script>
<style>
.create-buttons {
    margin: 1rem 0;
}
</style>
