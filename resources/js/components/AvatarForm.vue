<template>
    <div class="box box-default">
        <div class="box-body box-profile text-center">

            <img class="profile-user-img img-responsive img-circle tw-object-cover tw-object-center" :src="avatar" alt="User profile picture" style="width:128px;height:128px;">

            <h3 class="profile-username text-center" v-text="user.name"></h3>

            <p class="text-muted text-center" v-text="user.gender"></p>


            <small class="center text-center">Medidas recomendadas (128 x 128)</small>

            <form method="POST" enctype="multipart/form-data" v-if="!read">
                <image-upload name="avatar" @loaded="onLoad"></image-upload>
                <form-error v-if="errors.avatar" :errors="errors" style="float:right;">
                    {{ errors.avatar[0] }}
                </form-error>
            </form>


        </div>
        <!-- /.box-body -->
    </div>

</template>

<script>
import ImageUpload from './ImageUpload.vue';
export default {
    props: ['user', 'url', 'read'],
    components: { ImageUpload },
    data() {
        return {
            errors: [],
            avatar: this.user.avatar_path,
            endpoint: this.url ? `${this.url}${this.user.id}/avatar` : `/api/users/${this.user.id}/avatar`
        };
    },
    computed: {
        canUpdate() {
            return this.authorize(user => user.id === this.user.id);
        }
    },
    methods: {
        onLoad(avatar) {
            this.errors = [];
            this.avatar = avatar.src;
            this.persist(avatar.file);
        },
        persist(avatar) {

            const data = new FormData();
            data.append('avatar', avatar);
            axios.post(this.endpoint, data)
                .then(() => flash('Avatar subido correctamente!'))
                .catch(error => {
                    flash('Error al guardar el Archivo', 'danger');
                    this.errors = error.response.data.errors ? error.response.data.errors : [];
                    this.avatar = this.user.avatar_path;

                });
        }
    }
};
</script>