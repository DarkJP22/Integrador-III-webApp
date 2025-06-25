<template>
    <div>
        ACTUALIZAR UBICACIÓN FARMACIA {{ pharmacy.name }}
        <button type="submit" class="btn btn-secondary btn-sm" @click="update()">Actualizar con tu ubicación actual</button>

    </div>


</template>
<script>
export default {
    props: ['pharmacy'],
    data() {
        return {
            lat: '',
            lon: '',
            loader: false
        };
    },
    methods: {
        update() {

            window.navigator.geolocation.getCurrentPosition((geo) => {

                this.lat = geo.coords.latitude;
                this.lon = geo.coords.longitude;
                axios.put('/pharmacies/' + this.pharmacy.id + '/notifications', { lat: this.lat, lon: this.lon })
                    .then(() => {
                        this.loader = false;
                        flash('Ubicacion Actualizada.');

                        this.emitter.emit('removeSliderNotifications', $(this.$el).parent('.notification-app-item').data('slick-index'));
                    })
                    .catch(() => {
                        flash('Error al actualizar ubicacion de farmacia', 'danger');

                    });
            });
        }
    }
};
</script>
