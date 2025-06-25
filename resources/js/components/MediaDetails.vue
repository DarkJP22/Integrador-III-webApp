<template>


    <div class="box" v-show="openVideo">
        <div class="box-header">
            <button type="button" class="btn btn-primary" @click="closeVideo">Regresar</button>
            <button type="button" class="btn btn-secondary" @click="editVideo(currentVideo)"
                    v-if="authorize('owns', currentVideo, 'uploaded_by')">Editar
            </button>
            <button type="button" class="btn btn-danger" @click="deleteVideo(currentVideo)"
                    v-if="authorize('owns', currentVideo, 'uploaded_by')">Eliminar
            </button>

        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <h3>{{ currentVideo.title }}</h3>

                    <div class="player">
                        <loading :show="true"></loading>
                        <div class="player-container" v-html="convertEmbedUrl(currentVideo.url)"></div>
                    </div>
                    <div class="media-description">
                        {{ currentVideo.description }}
                    </div>
                    <div class="media-tags">
                        <strong>Etiquetas</strong>
                        <ul>
                            <li v-for="(tag, index) in currentVideo.tags" :key="index">
                                <span class="badge bg-primary">{{ tag }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <button type="button" class="btn btn-primary" @click="shareVideo">Compartir Video</button>
                    <form action="#" method="GET" @submit.prevent="search" autocomplete="off">
                        <div class="form-group">

                            <div class="col-sm-6">
                                <div class="input-group input-group">


                                    <input type="text" name="q" class="form-control pull-right"
                                           placeholder="Buscar por nombre o padecimiento..." v-model="q">
                                    <div class="input-group-btn">

                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i>
                                        </button>
                                    </div>


                                </div>
                            </div>
                            <div class="col-sm-6">
                                <select name="conditions" id="conditions" class="form-control" v-model="conditions"
                                        @change="search">
                                    <option value="">-- Filtro por padecimiento --</option>

                                    <option v-for="padecimiento in padecimientos" :key="padecimiento"
                                            :value="padecimiento">
                                        {{ padecimiento }}
                                    </option>


                                </select>

                            </div>

                        </div>

                    </form>

                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>
                                <!-- <input type="checkbox" name="select_all_patients" id="select_all_patients"/>
                                <input type="hidden" name="select_action" id="select_action"/>  -->
                            </th>

                            <th>Nombre</th>
                            <th>Teléfono</th>

                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(patient) in items" :key="patient.id">
                            <td data-title="Compartir Video">
                                <input type="checkbox" :id="'p-'+ patient.id" :value="patient.id"
                                       v-model="patientsCheck">
                            </td>


                            <td data-title="Nombre">

                                {{ patient.first_name }}


                            </td>
                            <td data-title="Teléfono">{{ patient.phone_number }}</td>

                        </tr>
                        </tbody>


                    </table>


                    <paginator :dataSet="dataSet" @changed="fetch" :no-update-url="true"></paginator>


                </div>
            </div>


        </div>
    </div>


</template>

<script>
import collection from '../mixins/collection';
import transformUrlVideo from '../mixins/transformUrlVideo';
import Loading from './Loading.vue';

export default {
    props: {
        padecimientos: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            patientsCheck: [],
            openVideo: false,
            currentVideo: false,
            q: '',
            conditions: '',
            dataSet: false,
            loader: false
        };
    },
    components: {
        Loading
    },
    mixins: [collection, transformUrlVideo],
    methods: {
        search() {
            this.fetch();
        },
        fetch(page) {
            axios.get(this.url(page)).then(this.refresh);
        },
        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            let url = `/pharmacy/patients?page=${page}`;

            if (this.q) {
                url += `&q=${this.q}`;
            }

            if (this.conditions) {
                url += `&conditions=${this.conditions}`;
            }

            url += '&ofPharmacy=true';

            return url;
        },
        refresh({data}) {
            this.dataSet = data;
            this.items = data.data;

        },
        editVideo(item) {
            $('#mediaModal').modal();
            this.emitter.emit('editMedia', item);

        },
        deleteVideo(item) {

            if (this.loader) {
                return;
            }

            const r = confirm('¿Deseas Eliminar este registro?');

            if (r == true) {
                this.loader = true;
                axios.delete(`/pharmacy/media/${item.id}`)
                    .then(() => {
                        this.loader = false;
                        this.closeVideo();
                        this.$emit('deleted');
                        flash('Video Eliminado Correctamente');
                    }).catch(() => {
                        this.loader = false;
                        flash('Error al eliminar');
                    });
            }

        },
        closeVideo() {
            this.openVideo = false;
            this.currentVideo = false;
            this.$emit('close');
        },
        showVideo(item) {
            this.openVideo = true;
            this.currentVideo = item;
        },

        shareVideo() {
            if (!this.currentVideo) {
                return;
            }
            if (!this.patientsCheck.length) {
                alert('Seleccione al menos un paciente de la lista!!');
            }

            axios.post('/patients/media', {video: this.currentVideo, patients: this.patientsCheck})
                .then(() => {
                    this.loader = false;

                    flash('Video Compartido');

                }).catch(() => {
                    this.loader = false;
                    console.error('Error');
                    flash('Error al enviar video', 'danger');
                });


        }


    },
    created() {
        this.emitter.on('openVideo', this.showVideo);
        this.fetch();
    }
};
</script>


