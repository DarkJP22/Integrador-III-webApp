<template>
    <div>
        <section class="content-header">
            <h1>Biblioteca Educativa</h1>
            <button type="button" class="btn btn-primary" @click="showForm">Agregar Video</button>
            <modal-media-form @created="add" :tags="tags"></modal-media-form>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="box" v-show="!openVideo">
                        <div class="box-header">
                            <form action="/pharmacy/media" method="GET" @submit.prevent="search" autocomplete="off">
                                <div class="form-group">


                                    <div class="input-group input-group">


                                        <input type="text" name="q" class="form-control pull-right"
                                               placeholder="Buscar..." v-model="q">
                                        <div class="input-group-btn">

                                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i>
                                            </button>
                                        </div>


                                    </div>


                                </div>
                                <div class="form-group" v-show="tagsSearch.length">

                                    <label for="">Filtrado por: </label> <span
                                    v-for="(selectedTag, index) in tagsSearch" :key="index"
                                    class="badge bg-primary selectedtag"
                                    @click="removeTagFromSearch(selectedTag, index)">{{ selectedTag }}</span> <a
                                    href="#" @click="clearFiltro()">Limpiar Filtro</a>


                                </div>
                                <div class="form-group">

                                    <div class="media-items">
                                        <a class="btn btn-app" v-for="(item, index) in tags" :key="index"
                                           @click="filterByTags(item)">
                                            <i class="fa fa-tag"></i> {{ item }}
                                        </a>

                                    </div>

                                </div>

                            </form>


                        </div>
                        <div class="box-body relative">


                            <div class="media-items">
                                <loading :show="loader"></loading>
                                <div class="col-xl-3 col-lg-4 col-md-6 col-xs-12" v-for="item in items" :key="item.id">
                                    <div class="info-box bg-secondary" @click="showVideo(item)">
                                        <!-- <span class="info-box-icon bg-primary"><i class="fa fa-play"></i></span> -->
                                        <div class="player-overlay"></div>
                                        <div class="player" @click="showVideo(item)">
                                            <loading :show="true"></loading>
                                            <div class="player-container" v-html="convertEmbedUrl(item.url)"></div>
                                        </div>
                                        <div class="info-box-content2" style="padding:6px;">
                                            <span class="info-box-text">{{ item.description }}</span>
                                            <span class="info-box-number tw-truncate">{{ item.title }}</span>
                                        </div>
                                        <!-- /.info-box-content -->

                                    </div>
                                    <!-- /.info-box -->
                                    <!-- <div v-html="convertEmbedUrl(item.url)"></div> -->


                                </div>
                            </div>


                        </div>
                        <div class="box-footer">
                            <div class="col-md-12">

                                <paginator :dataSet="dataSet" @changed="fetch" :no-update-url="true"></paginator>
                            </div>
                        </div>
                    </div> <!-- box search -->
                    <media-details @close="openVideo = false" @deleted="fetch"
                                   :padecimientos="tags"></media-details>
                    <!-- <div class="box" v-show="openVideo">
                        <div class="box-header">
                             <button type="button" class="btn btn-primary" @click="closeVideo">Regresar</button>
                        </div>
                        <div class="box-body">
                           
                            <div v-html="convertEmbedUrl(currentVideo.url)">
                                Cargando...
                            </div>
                           
                           
                            
                        </div>
                    </div> -->


                </div>
            </div>
        </section>
    </div>
</template>
<script>
import ModalMediaForm from './ModalMediaForm.vue';
import MediaDetails from './MediaDetails.vue';
import collection from '../mixins/collection';
import transformUrlVideo from '../mixins/transformUrlVideo';
import Loading from './Loading.vue';

export default {
    props: ['tags', 'padecimientos'],
    data() {
        return {
            openVideo: false,
            q: '',
            tagsSearch: [],
            dataSet: false,
            loader: false
        };
    },
    components: {
        MediaDetails,
        ModalMediaForm,
        Loading
    },
    mixins: [collection, transformUrlVideo],
    methods: {
        clearFiltro() {
            this.tagsSearch = [];
            this.fetch();
        },
        removeTagFromSearch(item, index) {
            this.tagsSearch.splice(index, 1);
            this.fetch();
        },
        filterByTags(item) {
            console.log(item);
            this.tagsSearch = [];
            // let itemFound = _.find( this.tagsSearch, function(o) {
            //         return o === item;
            //  });

            //  if(!itemFound){
            this.tagsSearch.push(item);
            this.fetch();
            // }


        },
        search() {
            this.fetch();
        },
        fetch(page) {
            this.loader = true;
            axios.get(this.url(page)).then(this.refresh, () => {
                this.loader = false;
            });
        },
        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            let url = `/pharmacy/media/videos?page=${page}`;

            if (this.q) {
                url += `&q=${this.q}`;
            }
            if (this.tagsSearch.length) {

                const tags = this.tagsSearch.join(',');

                url += `&tags=${tags}`;
            }


            return url;
        },
        refresh({data}) {
            this.loader = false;
            this.dataSet = data;
            this.items = data.data;

        },

        showVideo(item) {
            this.openVideo = true;
            this.emitter.emit('openVideo', item);

        },
        editVideo(item) {
            $('#mediaModal').modal();
            this.emitter.emit('editMedia', item);

        },
        showForm() {
            $('#mediaModal').modal();
            this.emitter.emit('showMediaModal');
        }


    },
    created() {
        this.fetch();
    }
};
</script>

