@if( ($user->hasRole('medico') || $user->hasRole('clinica') || $user->hasRole('laboratorio') ) && $user->fe)
            <div class="box box-solid box-medics">
            <div class="box-header with-border">
                <h4 class="box-title">Factura Eléctronica</h4>
                
            </div>
            <div class="box-body">
                @if($configFactura)
                    <config-factura 
                        :config-factura="{{ json_encode(isset($configFactura) ? $configFactura : '')  }}"
                        :tipo-identificaciones="{{ json_encode($tipoIdentificaciones) }}"
                        :endpoint="'/configfactura/{{ $configFactura->id }}'"
                        :activities="{{ $activities }}">>
                    </config-factura>
                @else 
                    <config-factura 
                        :config-factura="{{ json_encode(isset($configFactura) ? $configFactura : '')  }}"
                        :tipo-identificaciones="{{ json_encode($tipoIdentificaciones) }}"
                        :endpoint="'/admin/users/{{ $user->id }}/configfactura'"
                        :activities="{{ $activities }}">
                    </config-factura>
                @endif
               
                
            </div>
            <!-- /.box-body -->
        </div>
@endif