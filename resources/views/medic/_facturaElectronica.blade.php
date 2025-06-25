@if($profileUser->fe)
    @if($configFactura)
        <config-factura 
            :config-factura="{{ json_encode(isset($configFactura) ? $configFactura : '')  }}"
            :tipo-identificaciones="{{ json_encode($tipoIdentificaciones) }}"
            :endpoint="'/configfactura/{{ $configFactura->id }}'"
            :activities="{{ $activities }}">
        </config-factura>
    @else 
        <config-factura 
            :config-factura="{{ json_encode(isset($configFactura) ? $configFactura : '')  }}"
            :tipo-identificaciones="{{ json_encode($tipoIdentificaciones) }}"
            :endpoint="'/medic/profiles/{{ $profileUser->id }}/configfactura'"
            :activities="{{ $activities }}">
        </config-factura>
    @endif
 
@else
    <div class="callout callout-warning">
    <h4>Información importante!</h4>
    
        <p>Parece que no tienes activado la factura electronica todavia</a>              </p>
    </div>

@endif