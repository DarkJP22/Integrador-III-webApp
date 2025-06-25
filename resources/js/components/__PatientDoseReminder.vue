<template>
    <div class="content">
      <form class="form" @submit.prevent="save()" autocomplete="off">
        <loading :show="loader"></loading> 
        <button class="btn btn-primary btn-sm btn-flat mb-1" type="button" @click="crearRecordatorio()"><i class="fa fa-plus"></i> Agregar Recordatorio de dosis </button>
       <div v-for="(reminder, index) in reminders" :key="index" class="item">
           <div class="row">
               <div class="col-md-6">
                   <div class="form-group">
                        <label for="name" class="control-label">Medicamento</label>

                        
                            <input type="text" class="form-control" name="medicine" placeholder="" v-model="reminder.medicine" autocomplete="off" required>
                        
                            <form-error v-if="errors.medicine" :errors="errors" style="float:right;">
                                {{ errors.medicine[0] }}
                            </form-error>
                        
                    </div>

               </div>
               <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="control-label">Esquema Dosis</label>

                        
                            <select name="schema" class="form-control" v-model="reminder.schema" @change="onChangeSchema(reminder)" autocomplete="off" required>
                                <option value="1">1 vez al día</option>
                                <option value="2">2 veces al día</option>
                                <option value="3">3 veces al día</option>
                                <option value="4">4 veces al día</option>
                                <option value="5">5 veces al día</option>
                                <option value="6">6 veces al día</option>
                            </select>
                            
                        
                            <form-error v-if="errors.schema" :errors="errors" style="float:right;">
                                {{ errors.schema[0] }}
                            </form-error>
                        
                    </div>

               </div>
              
           </div>
           <div class="row">
                <div class="col-md-12">
                   <div class="">
                        <label for="name" class="control-label">Hora(s)</label>
                        
                       
                    </div>

                    

               </div>
               <div class="hours">
                  
                    <div v-for="(n, index) in fieldsHours(reminder)" :key="n" class="col-md-4">
                        <div class="form-group">
                            <input type="text" class="form-control timepicker" name="medicine" placeholder="00:00" v-model="reminder.hours[index]" @blur="onBlurTime(reminder.hours, index, $event)" autocomplete="off" required />
                        </div>
                        
                    </div>
               </div>
           </div>
           
           
         
          
         <hr>
       </div>
       
       <div class="row" v-if="reminders.length">
        <div class="col-sm-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
          
          
        </div>
      </div>
      </form>
    </div>
</template>

<script>

    import collection from '../mixins/collection';
    import Loading from "./Loading.vue";
    export default {
        props:['patient', 'medicine'],
        components: { Loading },
        mixins: [collection],
        data() {
            return {
               errors:[],
               loader:false,
               reminders:[],
                
                
            };
        },
        created() {
            
            this.fetch();
        },
        computed:{
            // fieldsHorus(){
            //     return this.schema
            // }
        },
        methods: {
            fieldsHours(reminder){
                let fields = [];
               
                for (let index = 0; index < reminder.schema; index++) {
                    fields.push(index)
                    
                }
                return fields
            },
            crearRecordatorio(){
                let reminder = {
                    medicine:'',
                    schema:1,
                    hours:['']
                }
                this.reminders.unshift(reminder);


            },
            onChangeSchema(reminder){
              reminder.hours = [];
              this.fieldsHours(reminder);
              this.setTimePicker();
            },
            onBlurTime(hours, index, e){
                 
                hours[index] = e.target.value;
                this.$emit('input')
            
            },
             fetch(page) {
                axios.get(this.url(page)).then(this.refresh);
            },
            url(page) {
                if (! page) {
                    // let query = location.search.match(/page=(\d+)/);
                    page = 1; //query ? query[1] : 1;
                }
                return `/patients/${this.patient.id}/dosereminders?page=${page}`;
            },
            refresh({data}) {
               
                this.reminders = data;

                 this.setTimePicker();
               
            },
            setTimePicker(){
                Vue.nextTick(function() {
                  
                    $('.timepicker').datetimepicker({
                        format: 'LT',
                        locale: 'es',
                        stepping:60,
                        
                    });
                });
            },
            save(){
                 if(this.loader){
                    return
                }

                this.loader = true;
                
                
                
                
                axios.post('/patients/'+ this.patient.id +'/dosereminders', { reminders: this.reminders })
                    
                    .then(({data}) => {
                        this.loader = false;
                        this.errors = [];
                        //this.clear();
                        flash('Recordatorio de dosis guardado.');
                        this.$emit('created', data);
                    })
                    .catch(error => {
                        this.loader = false;
                        flash('Error al guardar recordatorio de dosis', 'danger');
                        this.errors = error.response.data.errors ? error.response.data.errors : [];
                    });

                }
        }
    }
</script>
