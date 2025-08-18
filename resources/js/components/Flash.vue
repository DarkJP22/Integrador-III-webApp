<template>
    <div class="alert alert-flash" :class="'alert-'+level" role="alert" v-show="show" v-text="body">
    </div>
</template>

<script>
export default {
    props: ['message', 'type'],
    data() {
        return {
            body: this.message || '',
            level: this.type ? this.type : 'success',
            show: false
        };
    },
    created() {
        if (this.message) {
            this.flash({ message: this.message, level: this.type });
        }
        
        if (this.emitter) {
            this.emitter.on('flash', data => {
                console.log('Flash event received:', data);
                if (data) {
                    this.flash(data);
                } else {
                    console.warn('Flash: evento recibido sin datos');
                }
            });
        } else {
            console.warn('Flash: emitter no está disponible');
        }
    },
    methods: {
        flash(data) {
            if (data && typeof data === 'object') {
                this.body = data.message || '';
                this.level = data.level || 'success';
            } else if (typeof data === 'string') {
                this.body = data;
                this.level = 'success';
            } else {
                console.warn('Flash: datos inválidos recibidos:', data);
                return;
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