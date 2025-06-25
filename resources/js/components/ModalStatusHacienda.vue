<template>
  <div class="modal fade" id="modalRespHacienda" role="dialog" aria-labelledby="modalRespHacienda">
    <div class="modal-dialog " role="document">
      <div class="modal-content">

        <div class="modal-header">

          <h4 class="modal-title" id="modalRespHaciendaLabel">Estatus de Factura Hacienda</h4>

        </div>

        <div class="modal-body">
          <loading :show="loader"></loading>
          <ul>
            <li><b>Consecutivo: </b> <span id="resp-consecutivo"> {{ consecutivo }}</span></li>
            <li><b>Clave: </b> <span id="resp-clave"> {{ clave }}</span></li>
            <li><b>Emisor: </b><span id="resp-emisor"> {{ emisor }} </span></li>
            <li><b>Cedula Emisor: </b><span id="resp-emisorCedula"> {{ emisorCedula }} </span></li>
            <li><b>Receptor: </b><span id="resp-receptor"> {{ receptor }}</span></li>
            <li><b>Cedula Receptor: </b><span id="resp-receptorCedula"> {{ receptorCedula }} </span></li>
            <li><b>Mensaje: </b><span id="resp-mensaje"> {{ mensaje }}</span></li>
            <li><b>Detalle Mensaje: </b><span id="resp-detalle"> {{ detalle }}</span></li>
          </ul>


        </div>
        <div class="modal-footer">

          <a :href="'/hacienda/'+this.invoiceId +'/xml'" class="btn btn-default">Descargar xml Respuesta</a>

          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>


        </div>
      </div>
    </div>
  </div>
</template>
<script>

import Loading from './Loading.vue';

export default {
    data() {
        return {
            consecutivo: '',
            clave: '',
            emisor: '',
            emisorTipoCedula: '',
            emisorCedula: '',
            receptor: '',
            receptorTipoCedula: '',
            receptorCedula: '',
            mensaje: '',
            detalle: '',
            loader: false,
            invoiceId: ''
        };
    },
    components: {
        Loading
    },
    methods: {
        fetch() {
            this.loader = true;
            axios.get('/hacienda/' + this.invoiceId + '/recepcion')
                .then(({ data }) => {

                    this.loader = false;
                    this.clear();
                    var respHacienda = JSON.parse(data.resp_hacienda);

                    this.consecutivo = data.NumeroConsecutivo;
                    this.clave = respHacienda.Clave;
                    this.emisor = respHacienda.NombreEmisor;
                    this.emisorTipoCedula = respHacienda.TipoIdentificacionEmisor;
                    this.emisorCedula = respHacienda.NumeroCedulaEmisor;
                    this.receptor = respHacienda.NombreReceptor;
                    this.receptorTipoCedula = respHacienda.TipoIdentificacionReceptor;
                    this.receptorCedula = respHacienda.NumeroCedulaReceptor;
                    this.mensaje = respHacienda.Mensaje;
                    this.detalle = respHacienda.DetalleMensaje;

                })
                .catch(error => {
                    this.loader = false;
                    this.clear();
                    this.detalle = error.response.data.errors;

                    if (error.response.status == 400) {
                        flash(error.response.data.errors, 'danger');
                    } else {
                        flash('Ha ocurrido un error en la conexion con Hacienda', 'danger');
                    }

                });

        },
        clear() {

            this.consecutivo = '';
            this.clave = '';
            this.emisor = '';
            this.emisorTipoCedula = '';
            this.emisorCedula = '';
            this.receptor = '';
            this.receptorTipoCedula = '';
            this.receptorCedula = '';
            this.mensaje = '';
            this.detalle = '';
        }

    },
    created() {
        this.emitter.on('showStatusHaciendaModal', (data) => {

            this.invoiceId = data;
            this.fetch();
        });
    }
};
</script>
