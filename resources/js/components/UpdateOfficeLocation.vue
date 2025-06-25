<template>
    <div>
        ACTUALIZAR UBICACIÓN CONSULTORIO {{ office.name }}
        <button class="btn btn-secondary btn-sm tw-ml-2" type="submit" @click="update()">Actualizar con tu ubicación actual</button>

    </div>


</template>
<script>
export default {
    props: ['office'],
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

                axios.put('/offices/' + this.office.id + '/notifications', { lat: this.lat, lon: this.lon })
                    .then(() => {
                        this.loader = false;
                        flash('Ubicación Actualizada.');
                        window.location.reload();

                    })
                    .catch(() => {
                        flash('Error al actualizar ubicación de consultorio', 'danger');

                    });

            });

        }
    }
};
</script>
