<template>
    <div class="">

        <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" :data-target="'#sharelocationModal'+ appointmentRequest.id"><i class="fa fa-address"></i> Compartir Ubicación
        </button>
        <div class="modal fade" :id="'sharelocationModal'+ appointmentRequest.id" tabindex="-1" role="dialog" aria-labelledby="sharelocationModalLabel">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="sharelocationModalLabel">Compartir Ubicación</h4>
                         <a :href="`http://maps.google.com/?saddr=Current+Location&daddr=${ appointmentRequest.coords }`"  target="_blank" class="btn btn btn-app"> <i class="fa fa-map-marker"></i> <strong>Google Maps</strong></a>
                        <a :href="`waze://?ll=${ appointmentRequest.coords }&amp;navigate=yes`"  target="_blank" class="btn btn btn-app"><i class="fa fa-map-marker"></i> <strong>Abrir en Waze</strong></a>
                    </div>
                    <div class="modal-body">
                       
                        <input type="text" v-model="phone_number" class="form-control" placeholder="Número de teléfono de whatsapp">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="share">Compartir</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    props: {
        appointmentRequest: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            phone_number: ''
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
                'Google Maps: https://www.google.com/maps/search/?api=1&query=' + this.appointmentRequest.coords + 
                ', Waze: https://waze.com/ul?ll=' + this.appointmentRequest.coords + '&amp;navigate=yes'
            );
        }
    },
    methods: {
        share() {
            if(this.whatsappPhoneNumber){
                window.open('https://api.whatsapp.com/send/?phone=' +
                this.whatsappPhoneNumber +
                '&text=' +
                this.whatsappMessage +
                '&app_absent=0', '_blank');
            }
            
        }
    }
};
</script>