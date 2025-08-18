<template>

    <li class="dropdown notifications-menu">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="fa fa-bell-o"></i>
            <span v-if="notifications.length" class="label label-warning">{{ notifications.length }}</span>
        </a>
        <ul class="dropdown-menu">
            <li class="header">Tienes {{ notifications.length }} notificaciones</li>
            <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                    <li v-for="notification in notifications" :key="notification.id">
                        <a :title="getNotificationMessage(notification)" href="#" @click="markAsRead(notification)">
                            <i class="fa fa-calendar text-aqua"></i>{{ getNotificationMessage(notification) }}
                        </a>
                    </li>
                </ul>
            </li>
            <!-- <li class="footer"><a href="#">View all</a></li> -->
        </ul>
    </li>
</template>
<script>
export default {
    data() {
        return {
            notifications: []
        };
    },

    methods: {
        getNotificationMessage(notification) {
            try {
                if (!notification) return 'Notificación';
                
                // Intentar obtener el mensaje de diferentes lugares
                if (notification.data && notification.data.message) {
                    return notification.data.message;
                }
                if (notification.message) {
                    return notification.message;
                }
                if (notification.data && notification.data.title) {
                    return notification.data.title;
                }
                
                return 'Nueva notificación';
            } catch (error) {
                console.error('Error obteniendo mensaje de notificación:', error);
                return 'Notificación';
            }
        },

        markAsRead(notification) {
            if (!notification || !notification.id) {
                return;
            }

            try {
                // Primero remover de la lista local
                this.notifications = this.notifications.filter(n => n.id !== notification.id);
                
                // Luego hacer la petición al servidor
                axios.delete('/profiles/' + window.App.user.id + '/notifications/' + notification.id)
                    .then(() => {
                        // Notificación marcada como leída exitosamente
                    })
                    .catch(error => {
                        console.error('Error marcando notificación como leída:', error);
                        
                        // Si falla, volver a agregar la notificación a la lista
                        this.notifications.unshift(notification);
                        
                        // Mostrar mensaje de error al usuario
                        if (window.flash) {
                            window.flash('Error al marcar la notificación como leída', 'danger');
                        }
                    });

                // Navegar a la URL de la notificación
                if (notification.data?.url) {
                    window.location.href = notification.data.url;
                } else if (notification.data?.link) {
                    window.location.href = notification.data.link;
                } else if (notification.url) {
                    window.location.href = notification.url;
                }
            } catch (error) {
                console.error('Error en markAsRead:', error);
            }
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
        }

    },

    created() {
        try {
            this.listen();

            if (window.App && window.App.user && window.App.user.id) {
                axios.get('/profiles/' + window.App.user.id + '/notifications')
                    .then(response => {
                        if (response.data && Array.isArray(response.data)) {
                            this.notifications = response.data;
                        } else {
                            this.notifications = [];
                        }
                    })
                    .catch(error => {
                        console.error('Error cargando notificaciones:', error);
                        this.notifications = [];
                    });
            } else {
                this.notifications = [];
            }
        } catch (error) {
            console.error('Error en created:', error);
            this.notifications = [];
        }
    }
};
</script>

