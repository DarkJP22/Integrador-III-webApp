<template>
    <div class="tw-flex">
        <input type="text" v-model="visit_location" class="form-control" placeholder="Lugar de visita">
        <button type="button" class="btn btn-secondary" @click="save">Guardar</button>
    </div>
</template>
<script>
export default {
    props:{
        appointmentRequest:{
            type:Object,
            required:true
        }
    },
    data(){
        return{
            visit_location: this.appointmentRequest.visit_location,
            loader:false,
            errors:[]
        };
    },
    methods:{
        save(){
            if(this.loader){
                return;
            }
            axios.put('/lab/appointment-requests/'+ this.appointmentRequest.id + '/update-visit-location', { visit_location: this.visit_location })

                .then(() => {
                    this.loader = false;
                    this.errors = [];
                    // this.clear();
                    
                    flash('Cita Actualizada');
                    //window.location.reload();
                })
                .catch(error => {
                    this.loader = false;
                  
                    flash('Error al actualizar', 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                });
          
        }
    }
};
</script>