<template>
    <div id="modalRequestCedula" aria-labelledby="modalRequestCedula" class="modal fade" role="dialog">
        <div class="modal-dialog " role="document">
            <div class="modal-content">

                <div class="modal-header">

                    <h4 id="modalRequestCedulaLabel" class="modal-title">Facturación</h4>

                </div>


                <div class="modal-body">
                    <loading :show="loader"></loading>
                    <div v-if="isGPSUser" class="callout callout-info">
                        <h4>Información importante!</h4>

                        <p>Es un usuario de Doctor Blue</p>
                    </div>

                    <div class="form-row">

                        <div class="form-group col-md-12">

                            <label for="TipoDocumento">Tipo Documento</label>
                            <select id="PreviewTipoDocumento" v-model="invoice.TipoDocumento" class="form-control custom-select">

                                <option value="04">
                                    Tiquete Electrónico
                                </option>
                                <option value="01">
                                    Factura Electrónica
                                </option>

                            </select>

                        </div>

                    </div>
                    <div class="form-row">

                        <div class="form-group col-md-12">
                            <label for="identificacion_cliente">Identificacion</label>

                            <div class="input-group input-group">
                                <input v-model="invoice.identificacion_cliente" :disabled="loader" class="form-control" placeholder="" type="text" @keydown.prevent.enter="searchCustomer()">
                                <div class="input-group-btn">
                                    <button :disabled="loader" class="btn btn-outline-primary" type="button" @click="searchCustomer()">Buscar</button>
                                </div>
                            </div>





                        </div>


                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="cliente">Nombre Cliente</label>

                            <input v-model="invoice.cliente" class="form-control" placeholder="" type="text">



                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                        <label for="phone">Teléfono</label>

                        <input v-model="invoice.phone" class="form-control" name="phone" placeholder="" type="tel">
                        <!-- <cleave
                            type="text"
                            class="form-control"
                            placeholder=""
                            v-model="invoice.phone"
                            :options="{ phone: true, phoneRegionCode: 'CR' }"
                        ></cleave> -->
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="email">Email</label>

                            <input v-model="invoice.email" class="form-control" placeholder="" type="text">



                        </div>
                    </div>



                </div>
                <div class="modal-footer">

                    <button class="btn btn-default btn-close-modal" data-dismiss="modal" type="button">Cerrar</button>
                    <button class="btn btn-primary" type="button" @click="next()">Continuar</button>




                </div>
            </div>
        </div>
    </div>
</template>
<script>

import Loading from './Loading.vue';

export default {
    //props:[],
    data() {
        return {
            invoice: {},
            loader: false,
            isGPSUser: false

        };
    },
    components: {
        Loading
    },
    methods: {
        async searchCustomer() {
            this.isGPSUser = false;
            //console.log(this.invoice.identificacion_cliente)

            const respPatient = await this.verifyIsPatientGpsMedica();
            if (respPatient && respPatient.data.data[0]) {
                this.$emit('addClient', respPatient.data.data[0]);
                this.invoice.phone = respPatient.data.data[0].phone_number;
            } else {
                const respHacienda = await this.getInfoHacienda();

                if (respHacienda) {
                    this.invoice.cliente = respHacienda.data.nombre;
                    this.invoice.tipo_identificacion_cliente =
                        respHacienda.data.tipoIdentificacion;
                }
            }


        },
        async getInfoHacienda() {
            const instance = axios.create();
            instance.defaults.headers.common = {};
            instance.defaults.headers.common.accept = 'application/json';

            return await instance
                .get(
                    'https://api.hacienda.go.cr/fe/ae?identificacion=' +
                    this.invoice.identificacion_cliente
                )
                .catch(() => {
                    flash(
                        'Ocurrio un error en la consulta api.hacienda.go.cr!!',
                        'danger'
                    );
                });
        },
        async verifyIsPatientGpsMedica() {

            return await axios.get('/invoices/patients?q=' + this.invoice.identificacion_cliente)
                .catch(() => {
                    console.log('error verifyIsClient');
                });

        },
        next() {
            //this.$emit('saveResumenFactura');
            if (this.invoice.identificacion_cliente) {
                axios
                    .post('/createorupdatepatient', {
                        identificacion: this.invoice.identificacion_cliente,
                        name: this.invoice.cliente,
                        tipo_identificacion: this.invoice.tipo_identificacion_cliente || '01',
                        phone: this.invoice.phone,
                        email: this.invoice.email,
                    })
                    .then(({ data }) => {
                        console.log(data);
                        this.invoice.customer_id = data?.id;
                    });
            }
            $(this.$el).find('.btn-close-modal').click();
        }

    },
    created() {

        this.emitter.on('showRequestCedulaModal', (data) => {

            this.invoice = data;

        });
    }
};
</script>
