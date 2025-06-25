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
                        <a :title="notification.data.message" href="#" @click="markAsRead(notification)">
                            <i class="fa fa-calendar text-aqua"></i>{{ notification.data.message }}
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
            notifications: false
        };
    },

    methods: {
        markAsRead(notification) {
            axios.delete('/profiles/' + window.App.user.id + '/notifications/' + notification.id);
            if (notification.data?.link) {
                window.location.href = notification.data.link;

            }
        },

        listen() {

            if (window.App.user.id) {

                var audio = new Audio('/img/notification.mp3');

                window.Echo.private(`App.User.${window.App.user.id}`)
                    .notification((notification) => {
                        console.log(notification);
                        this.notifications.unshift(notification);
                        audio.play();
                    });


            }


        }

    },

    created() {
        this.listen();

        axios.get('/profiles/' + window.App.user.id + '/notifications')
            .then(response => this.notifications = response.data);
    }
};
</script>

