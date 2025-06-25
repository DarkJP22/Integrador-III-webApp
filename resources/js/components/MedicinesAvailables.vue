<template>
    <div class="content2">

        <p>
            Seleccione los medicamentos que desea agregarle recordatorio de compra y presione el botón Agregar para
            recordatorio de compra
        </p>
        <ul id="medicines-list" class="todo-list ui-sortable" v-show="items.length">

            <li v-for="(item, index) in items" :key="item.id">
                <input type="checkbox" v-model="item.selected">
                <button href="#" class="btn btn-secondary btn-sm" @click="dosis(item, index)">Dosis</button>
                <span> <span class="text">{{ item.name }}</span> <span class="date pull-right"> {{
                        item.created_at
                    }}</span></span>

                <div class="tools">

                    <i class="fa fa-trash-o delete" @click="destroy(item, index)" v-if="authorize('owns', item)"></i>
                </div>
                <div v-show="showSchema == index">
                    <patient-dose-reminder :patient="patient" :medicine="item"></patient-dose-reminder>
                </div>
            </li>

        </ul>
        <p>
            <button @click="add()" class="btn btn-primary" v-if="items.length">Agregar para recordatorio de compra
            </button>
        </p>
        <paginator :dataSet="dataSet" @changed="fetch" :no-update-url="true"></paginator>
        <p v-show="!items.length">Aun no tiene medicamentos este paciente</p>


    </div>
</template>
<script>
import collection from '../mixins/collection';


export default {
    props: ['medicines', 'patient'],
    data() {
        return {
            items: [],
            errors: [],
            dataSet: false,
            loader: false,
            showSchema: -1
        };
    },

    mixins: [collection],
    methods: {
        dosis(item, index) {
            this.showSchema = index;
            //   Swal.fire({
            //         title: 'Receta / Dosis',
            //         input: 'textarea',
            //         inputPlaceholder: 'Receta / Dosis',
            //         showCancelButton: true,
            //         confirmButtonText: 'Guardar',
            //         confirmButtonColor: '#67BC9A',
            //         cancelButtonColor: '#dd4b39',
            //         showLoaderOnConfirm: true,
            //         inputValue: item.receta,
            //         // inputValidator: (value) => {
            //         //     return new Promise((resolve) => {
            //         //         if (value === '') {
            //         //             resolve('Necesitas agregar al menos un correo')
            //         //         } else {
            //         //             resolve()
            //         //         }
            //         //     })
            //         // },
            //         preConfirm: (message) => {


            //             return axios.put(`/medicines/${item.id}/receta`,{ receta: message })
            //                 .then(({data}) => {

            //                     this.update(data, index)
            //                 })
            //                 .catch(error =>{

            //                     Swal.showValidationError(
            //                         `Request failed: ${error}`
            //                         )

            //                     flash('Error al actualizar!!', 'danger');
            //                 })
            //         },
            //         allowOutsideClick: () => !Swal.isLoading()

            //     })
            //     .then( (result) => {


            //         if (result.value) {

            //              Swal.fire({
            //                 title: `Receta Actualizada Correctamente`,

            //                 }).then( (result) => {


            //                 });

            //         }


            //     });


        },
        add() {
            const selectedItems = _.filter(this.items, {'selected': true});

            if (!selectedItems.length) {
                alert('Selecciona al menos un elemento de la lista!!');
                return;
            }

            axios.post(`/pharmacy/patients/${this.patient.id}/medicines/transfer`, {items: selectedItems})
                .then(({data}) => {
                    this.loader = false;
                    this.errors = [];


                    flash('Medicamento Guardado.');
                    this.emitter.emit('transferedMedicines', data);
                })
                .catch(error => {
                    flash(error.response.data.message, 'danger');
                    this.errors = error.response.data.errors;
                });

        },
        destroy(item, index) {
            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                axios.delete(`/medicines/${item.id}`)
                    .then(() => {

                        this.remove(index);
                        this.showSchema = -1;

                        flash('Medicamento Eliminado!');

                    }).catch(error => {

                        flash(error.response.data.message, 'danger');

                    });
            }

        },
        fetch(page) {
            axios.get(this.url(page)).then(this.refresh);
        },
        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            return `/patients/${this.patient.id}/medicines?page=${page}`;
        },
        refresh({data}) {
            this.showSchema = -1;
            this.dataSet = data;
            this.items = data.data;

        },


    },
    created() {
        this.fetch();
        this.emitter.on('createdMedicinePharmacy', this.fetch);
    }

};
</script>
