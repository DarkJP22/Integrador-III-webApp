<div class="row">
       <div class="col-md-6">
              <mensaje-receptor :mensajes-receptor="{{ json_encode($MensajesReceptor)}}" :condicion-impuesto="{{ json_encode($CondicionImpuesto)}}" :config-facturas="{{ json_encode($configFacturas) }}">
              </mensaje-receptor>
       </div>
       <div class="col-md-6">
              <mensaje-receptor-lote :mensajes-receptor="{{ json_encode($MensajesReceptor)}}" :condicion-impuesto="{{ json_encode($CondicionImpuesto)}}" :config-facturas="{{ json_encode($configFacturas) }}">
              </mensaje-receptor-lote>
       </div>
</div>