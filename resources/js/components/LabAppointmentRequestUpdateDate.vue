<template>
    <div class="tw-flex">
        <flat-pickr v-model="appointment_date" :config="configDate" class="form-control" placeholder="Selecione una fecha">
                                    </flat-pickr>
        <button type="button" class="btn btn-secondary btn-geo" @click="save">Guardar</button>
    </div>
</template>
<script>
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
    props:{
        appointmentRequest:{
            type:Object,
            required:true
        }
    },
    components:{
        flatPickr
    },
    data(){
        return{
            appointment_date: this.appointmentRequest.appointment_date,
            configDate: {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
            },
            loader:false,
            errors:[]
        };
    },
    methods:{
        save(){
            if(this.loader){
                return;
            }
            axios.put('/lab/appointment-requests/'+ this.appointmentRequest.id + '/update-appointment-date', { appointment_date: this.appointment_date })

                .then(() => {
                    this.loader = false;
                    this.errors = [];
                    // this.clear();
                    
                    flash('Cita Actualizada');
                    window.location.reload();
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