<template>
    <span class="label label-warning pull-right" v-show="orderRequestNotifications.length">{{orderRequestNotifications.length }}</span>
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
        orderRequestNotifications() {
            return this.notifications.filter(notification => {
                return notification.type === `App\\Notifications\\${this.type}`;
            });
        }
    },
    methods: {
        clearNotifications(type) {
            axios.delete('/profiles/' + window.App.user.id + '/' + type + '/notifications');

            this.notifications = this.notifications.filter(notification => {
                return notification.type !== `App\\Notifications\\${type}`;
            });
        },

        listen() {

            if (window.App.user.id) {
                var audio = new Audio('/img/notification.mp3');
                window.Echo.private(`App.User.${window.App.user.id}`)
                    .notification((notification) => {
                        console.log(notification);
                        this.notifications.unshift(notification);
                        audio.play();
                        if (notification.type === 'App\\Events\\PharmacyOrderUpdate') {
                            window.location.reload();
                        }
                    });
            }


        },

    },

    async created() {
        this.listen();

        this.emitter.on('clearOrderBadgeNotifications', (data) => {
            console.log('clearOrderBadgeNotifications', data);
            this.clearNotifications(data.type);
        });

        const response = await axios.get('/profiles/' + window.App.user.id + '/notifications');
        this.notifications = response.data;


    }
};
</script>

