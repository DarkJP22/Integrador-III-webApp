<template>
    <span class="label label-warning pull-right" v-show="appointmentRequestNotifications.length">{{ appointmentRequestNotifications.length }}</span>
</template>
<script>
export default {
    props: ['type'],
    data() {
        return {
            notifications: []
        };
    },
    computed: {
        appointmentRequestNotifications() {
            if (!Array.isArray(this.notifications)) {
                return [];
            }
            
            return this.notifications.filter(notification => {
                try {
                    return notification && 
                           typeof notification === 'object' &&
                           notification.type === `App\\Notifications\\${this.type}`;
                } catch (error) {
                    console.error('Error en BadgeNotifications computed:', error);
                    return false;
                }
            });
        }
    },
    methods: {
        clearNotifications(type) {
            axios.delete('/profiles/' + window.App.user.id + '/' + type +'/notifications');

            this.notifications = this.notifications.filter(notification => {
                return notification.type !== `App\\Notifications\\${type}`;
            });
        },

        listen() {
            if (window.App && window.App.user && window.App.user.id) {
                try {
                    var audio = new Audio('/img/notification.mp3');

                    window.Echo.private(`App.User.${window.App.user.id}`)
                        .notification((notification) => {
                            try {
                                if (notification && typeof notification === 'object') {
                                    if (!Array.isArray(this.notifications)) {
                                        this.notifications = [];
                                    }
                                    this.notifications.unshift(notification);
                                    audio.play().catch(e => console.log('No se pudo reproducir audio:', e));
                                }
                            } catch (error) {
                                console.error('Error procesando notificación:', error);
                            }
                        })
                        .error((error) => {
                            console.error('Error en canal:', error);
                        });
                } catch (error) {
                    console.error('Error configurando listeners:', error);
                }
            }
        },

    },

    async created() {
        try {
            this.listen();

            if (this.emitter) {
                this.emitter.on('clearBadgeNotifications', (data) => {
                    if (data && data.type) {
                        this.clearNotifications(data.type);
                    }
                });
            }

            if (window.App && window.App.user && window.App.user.id) {
                try {
                    const response = await axios.get('/profiles/' + window.App.user.id + '/notifications');
                    if (response.data && Array.isArray(response.data)) {
                        this.notifications = response.data;
                    } else {
                        this.notifications = [];
                    }
                } catch (error) {
                    console.error('Error cargando notificaciones:', error);
                    this.notifications = [];
                }
            }
        } catch (error) {
            console.error('Error en created:', error);
        }
    }
};
</script>

