<template>
    <span class="label label-warning pull-right" v-show="badgeCount > 0">{{ badgeCount }}</span>
</template>
<script>
export default {
    props: {
        type: {
            type: String,
            required: true,
            default: 'NewOrderPharmacie'
        }
    },
    data() {
        return {
            notifications: [],
            badgeCount: 0
        };
    },
    computed: {
        // Computed simplificado - no se usa en el template
        orderRequestNotifications() {
            return this.notifications.filter(notification => {
                try {
                    return notification && 
                           notification.type === `App\\Notifications\\${this.type}`;
                } catch (error) {
                    console.error('Error en computed:', error);
                    return false;
                }
            });
        }
    },
    methods: {
        updateBadgeCount() {
            try {
                if (!Array.isArray(this.notifications)) {
                    this.badgeCount = 0;
                    return;
                }
                
                const targetType = `App\\Notifications\\${this.type}`;
                const count = this.notifications.filter(notification => {
                    return notification && notification.type === targetType;
                }).length;
                
                this.badgeCount = count;
            } catch (error) {
                console.error('Error actualizando badge count:', error);
                this.badgeCount = 0;
            }
        },

        clearNotifications(type) {
            if (!type || !window.App || !window.App.user || !window.App.user.id) {
                return;
            }

            try {
                axios.delete('/profiles/' + window.App.user.id + '/' + type + '/notifications');

                this.notifications = this.notifications.filter(notification => {
                    return notification && notification.type !== `App\\Notifications\\${type}`;
                });
                
                this.updateBadgeCount();
            } catch (error) {
                console.error('Error limpiando notificaciones:', error);
            }
        },

        listen() {
            if (!window.App || !window.App.user || !window.App.user.id) {
                return;
            }

            try {
                const userId = window.App.user.id;
                
                // Escuchar notificaciones de nuevas órdenes
                window.Echo.private(`App.User.${userId}`)
                    .notification((notification) => {
                        try {
                            if (notification && typeof notification === 'object') {
                                // Asegurar que la notificación tenga las propiedades mínimas
                                const sanitizedNotification = {
                                    id: notification.id || new Date().getTime(),
                                    type: notification.type || 'App\\Notifications\\NewOrderPharmacie',
                                    data: notification.data || notification,
                                    read_at: notification.read_at || null,
                                    created_at: notification.created_at || new Date().toISOString(),
                                    updated_at: notification.updated_at || new Date().toISOString()
                                };
                                
                                if (!Array.isArray(this.notifications)) {
                                    this.notifications = [];
                                }
                                
                                this.notifications.unshift(sanitizedNotification);
                                this.updateBadgeCount();
                                
                                // Reproducir audio
                                const audio = new Audio('/img/notification.mp3');
                                audio.play().catch(e => console.log('No se pudo reproducir el audio:', e));
                                
                                // Recargar página si estamos en la sección de órdenes de farmacia
                                if (window.location.pathname.includes('/pharmacy/orders')) {
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                }
                            }
                        } catch (error) {
                            console.error('Error procesando notificación:', error);
                        }
                    })
                    .error((error) => {
                        console.error('Error en canal de notificaciones:', error);
                    });

                // Escuchar eventos de actualización de órdenes
                window.Echo.private(`App.User.${userId}`)
                    .listen('PharmacyOrderUpdate', () => {
                        const audio = new Audio('/img/notification.mp3');
                        audio.play().catch(e => console.log('No se pudo reproducir el audio:', e));
                        if (window.location.pathname.includes('/pharmacy/orders')) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    })
                    .error((error) => {
                        console.error('Error en canal de actualización de órdenes:', error);
                    });
                    
            } catch (error) {
                console.error('Error configurando listeners de Echo:', error);
            }
        },

    },

    async created() {
        try {
            this.listen();

            // Configurar listener para limpiar notificaciones
            if (this.emitter) {
                this.emitter.on('clearOrderBadgeNotifications', (data) => {
                    if (data && data.type) {
                        this.clearNotifications(data.type);
                    }
                });
            }

            // Cargar notificaciones existentes
            if (window.App && window.App.user && window.App.user.id) {
                try {
                    const response = await axios.get('/profiles/' + window.App.user.id + '/notifications');
                    
                    if (response.data && Array.isArray(response.data)) {
                        this.notifications = response.data;
                        this.updateBadgeCount();
                    } else {
                        this.notifications = [];
                        this.badgeCount = 0;
                    }
                } catch (error) {
                    console.error('Error cargando notificaciones:', error);
                    this.notifications = [];
                    this.badgeCount = 0;
                }
            } else {
                this.notifications = [];
                this.badgeCount = 0;
            }
        } catch (error) {
            console.error('Error en created():', error);
        }
    }
};
</script>
