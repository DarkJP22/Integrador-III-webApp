<template>
    <div class="">

        <button :data-target="'#shareAppModal'+ patient.id" class="btn btn-secondary" data-toggle="modal" type="button"><i class="fa fa-address"></i> Compartir App
        </button>
        <div :id="'shareAppModal'+ patient.id" aria-labelledby="shareAppModalLabel" class="modal fade" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                        <h4 id="sharelocationModalLabel" class="modal-title">Compartir Link App</h4>
                    </div>
                    <div class="modal-body">
                    
                        <input v-model="phone_number" class="form-control" placeholder="Número de teléfono de whatsapp" type="text">

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" @click="share">Compartir</button>
                        <button class="btn btn-default" data-dismiss="modal" type="button">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    props: {
        patient: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            phone_number: this.patient.phone_number || ''
        };
    },
    computed: {
        whatsappPhoneNumber() {
            if (!this.phone_number) return false;

            return this.phone_number.startsWith('506')
                ? this.phone_number
                : '506' + this.phone_number;
        },
        whatsappMessage() {
            return encodeURIComponent(
                'Enfermarse puede suceder en cualquier momento... En Doctor Blue encuentras el médico, odontólogo o especialista que necesites, cerca de tu casa. Descárgala Gratis en Playstore o AppleStore. Android: https://play.google.com/store/apps/details?id=com.avotz.gpsmedica&hl=es'
            );
        }
    },
    methods: {
        share() {
            if(this.whatsappPhoneNumber){

                const patient = {
                    identificacion: this.patient.ide,
                    tipo_identificacion: this.patient.tipo_identificacion,
                    phone: this.phone_number,
                    name: this.patient.first_name,
                    email: this.patient.email,
                };
                axios.post('/general/patients/createaccount', patient).then(()=>{

    
                    window.open(
                        'https://api.whatsapp.com/send/?phone=' + this.whatsappPhoneNumber + '&text=' + this.whatsappMessage + '&app_absent=0',
                        '_blank' // <- This is what makes it open in a new window.
                    );
                      
                      
                }).catch((error)=>{
                    console.log(error);
                    Swal.fire('Error al crear cuenta de Doctor Blue', 'Error al crear cuenta de Doctor Blue' , 'error');
                });

                // window.open('https://api.whatsapp.com/send/?phone=' +
                // this.whatsappPhoneNumber +
                // '&text=' +
                // this.whatsappMessage +
                // '&app_absent=0', '_blank');
            }
            
        }
    }
};
</script>