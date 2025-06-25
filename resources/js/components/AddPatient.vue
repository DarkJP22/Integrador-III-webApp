<template>
    <div class="form-horizontal">
    
    <div class="input-group">
        <div class="input-group-btn">
        <button type="button" class="btn btn-danger" @click="add()"><i class="fa fa-plus"></i> Agregar a tu lista</button>
        </div>
        <!-- /btn-group -->
        
        
    </div>
   
        
</div>
</template>
<script>
export default {
    props:['patient'],
    data(){
        return{
            loader: false,
            code:'',
            errors:[],
            endpoint:'/patients/'+ this.patient.id +'/add'
        };
    },
    methods:{
        add(){
            if(this.loader){
                return;
            }
            
            this.loader = true;
            this.errors = [];

            axios.post(this.endpoint)
                .then(() => {
                    this.loader = false;
                    this.errors = [];
                      
                    this.code = '';
                    

                    flash('Paciente Agregado');

                    window.location.reload();
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
