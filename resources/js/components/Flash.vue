<template>
    <div class="alert alert-flash" :class="'alert-'+level" role="alert" v-show="show" v-text="body">
    </div>
</template>

<script>
export default {
    props: ['message', 'type'],
    data() {
        return {
            body: this.message,
            level: this.type ? this.type : 'success',
            show: false
        };
    },
    created() {
        if (this.message) {
            this.flash();
        }
        this.emitter.on(
            'flash', data => this.flash(data)
        );
    },
    methods: {
        flash(data) {
            if (data) {
                this.body = data.message;
                this.level = data.level;
            }
            this.show = true;
            this.hide();
        },
        hide() {
            setTimeout(() => {
                this.show = false;
            }, 4000);
        }
    }
};
</script>

<style>
.alert-flash {
    position: fixed;
    right: 25px;
    bottom: 25px;
    z-index: 9999;
}
</style>