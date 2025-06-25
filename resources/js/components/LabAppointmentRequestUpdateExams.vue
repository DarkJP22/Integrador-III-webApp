<template>
    <div class="tw-flex">
        <textarea v-model="exams" class="form-control" placeholder="Exámenes" rows="3"></textarea>
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
            exams: this.appointmentRequest.exams,
            loader:false,
            errors:[]
        };
    },
    methods:{
        save(){
            if(this.loader){
                return;
            }
            axios.put('/lab/appointment-requests/'+ this.appointmentRequest.id + '/update-exams', { exams: this.exams })

                .then(() => {
                    this.loader = false;
                    this.errors = [];
                    // this.clear();
                    
                    flash('Cita Actualizada');
                    // window.location.reload();
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