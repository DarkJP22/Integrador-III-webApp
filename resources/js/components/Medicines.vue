<template>
    <div class="content">

        <!-- <medicine :patient="patient" @created="add"></medicine> -->

        <ul id="medicines-list" class="todo-list ui-sortable" v-show="items.length">

            <li v-for="(item, index) in items" :key="item.id">
                <button href="#" class="btn btn-secondary btn-sm" @click="dosis(item, index)">Dosis</button>
                <span> <span class="text">{{ item.name }}</span> <span class="date pull-right"> {{ item.created_at }}</span></span>

                <div class="tools">

                    <i class="fa fa-trash-o delete" @click="destroy(item, index)" v-if="authorize('owns', item)"></i>
                </div>
            </li>

        </ul>


    </div>
</template>
<script>
import collection from '../mixins/collection';


export default {
    props: ['medicines', 'patient'],
    data() {
        return {
            items: this.medicines,

        };
    },
    mixins: [collection],
    methods: {
        dosis(item, index) {

            Swal.fire({
                title: 'Receta / Dosis',
                input: 'textarea',
                inputPlaceholder: 'Receta / Dosis',
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                confirmButtonColor: '#67BC9A',
                cancelButtonColor: '#dd4b39',
                showLoaderOnConfirm: true,
                inputValue: item.receta,
                // inputValidator: (value) => {
                //     return new Promise((resolve) => {
                //         if (value === '') {
                //             resolve('Necesitas agregar al menos un correo')
                //         } else {
                //             resolve()
                //         }
                //     })
                // },
                preConfirm: (message) => {



                    return axios.put(`/medicines/${item.id}/receta`, { receta: message })
                        .then(({ data }) => {

                            this.update(data, index);
                        })
                        .catch(error => {

                            Swal.showValidationError(
                                `Request failed: ${error}`
                            );

                            flash('Error al actualizar!!', 'danger');
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()

            })
                .then((result) => {


                    if (result.value) {

                        Swal.fire({
                            title: 'Receta Actualizada Correctamente',

                        }).then(() => {



                        });

                    }


                });


        },
        destroy(item, index) {
            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                axios.delete(`/medicines/${item.id}`)
                    .then(() => {

                        this.remove(index);

                        flash('Medicamento Eliminado!');

                    }).catch(error => {

                        flash(error.response.data.message, 'danger');

                    });
            }

        },



    }

};
</script>
