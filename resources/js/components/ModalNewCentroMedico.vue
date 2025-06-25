<template>
    <div class="modal fade" id="modalNewCentroMedico" role="dialog" aria-labelledby="modalNewCentroMedico">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <form @submit.prevent="buy()">


                    <div class="modal-header">

                        <h4 class="modal-title" id="modalNewCentroMedicoLabel">{{ planName }}</h4>
                        <p></p>
                    </div>

                    <div class="modal-body">
                        <loading :show="loader"></loading>

                        <div class="form-row">

                            <div class="form-group col-md-12">
                                <label for="MedioPago">Escoge el consultorio a convertir en centro médico</label>
                                <select class="form-control custom-select" v-model="officeId" required>

                                    <option v-for="(office, index) in offices" :value="office.id" :key="index">
                                        {{ office.name }}
                                    </option>

                                </select>
                            </div>

                        </div>


                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-default btn-close-modal" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Comprar</button>




                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>

import Loading from './Loading.vue';

export default {
    props: ['offices'],
    data() {
        return {
            officeId: false,
            planId: false,
            planName: '',
            loader: false,


        };
    },
    components: {
        Loading
    },

    methods: {


        buy() {
            window.location = `/medic/subscriptions/${this.planId}/buy?office=${this.officeId}`;
        }

    },
    created() {
        this.emitter.on('showNewCentroMedicoModal', (data) => {

            this.planId = data.planId;
            this.planName = data.planName;

        });
    }
};
</script>
