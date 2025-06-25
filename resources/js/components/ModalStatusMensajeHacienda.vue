<template>
  <div class="modal fade" id="modalRespHacienda" role="dialog" aria-labelledby="modalRespHacienda">
    <div class="modal-dialog " role="document">
      <div class="modal-content">

        <div class="modal-header">

          <h4 class="modal-title" id="modalRespHaciendaLabel">Estatus de Mensaje de Receptor</h4>

        </div>

        <div class="modal-body">
          <loading :show="loader"></loading>
          <ul>
            <li><b>Consecutivo Receptor: </b> <span id="resp-consecutivo"> {{ consecutivo }}</span></li>
            <li><b>Clave: </b> <span id="resp-clave"> {{ clave }}</span></li>
            <li><b>Cedula Emisor: </b><span id="resp-emisorCedula"> {{ emisorCedula }} </span></li>
            <li><b>Cedula Receptor: </b><span id="resp-receptorCedula"> {{ receptorCedula }} </span></li>
            <li><b>Mensaje: </b><span id="resp-mensaje"> {{ mensaje }}</span></li>
            <li><b>Detalle Mensaje: </b><span id="resp-detalle"> {{ detalle }}</span></li>
          </ul>


        </div>
        <div class="modal-footer">

          <a :href="'/hacienda/mensaje/'+this.receptorId +'/xml'" class="btn btn-default">Descargar xml Respuesta</a>

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
            receptorId: ''
        };
    },
    components: {
        Loading
    },
    methods: {
        fetch() {
            this.loader = true;
            axios.get('/hacienda/mensaje/' + this.receptorId + '/recepcion')
                .then(({ data }) => {

                    this.loader = false;
                    this.clear();
                    var respHacienda = JSON.parse(data.resp_hacienda);

                    if (respHacienda) {
                        this.consecutivo = data.NumeroConsecutivoReceptor;
                        this.clave = respHacienda.Clave;
                        this.emisorCedula = respHacienda.NumeroCedulaEmisor;
                        this.receptorCedula = respHacienda.NumeroCedulaReceptor;
                        this.mensaje = respHacienda.Mensaje;
                        this.detalle = respHacienda.DetalleMensaje;
                    }

                })
                .catch(error => {

                    // console.log(error.response)

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
            this.emisorCedula = '';
            this.receptorCedula = '';
            this.mensaje = '';
            this.detalle = '';
        }
    },
    created() {
        this.emitter.on('showStatusHaciendaModal', (data) => {

            this.receptorId = data;
            this.fetch();
        });
    }
};
</script>
