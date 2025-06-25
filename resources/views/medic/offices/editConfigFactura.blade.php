@extends('layouts.medics.app')
@section('header')

@endsection
@section('content')

    <section class="content-header">
      <h1>Configuracion Factura Electrónica de {{ $office->name }}</h1>
      
    </section>

	<section class="content">
        <div class="row">
        <div class="col-md-12">
            <div class="panel">
            <div class="panel-body">
            @include('agenda._buttons')
            
            </div>
            
            </div>
            
            </div>
        </div>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="{{ isset($tab) ? ($tab =='basic') ? 'active' : '' : 'active' }}"><a href="#basic" data-toggle="tab">Información</a></li>
            
            
            
        </ul>
        <div class="tab-content">
            <div class="{{ isset($tab) ? ($tab =='basic') ? 'active' : '' : 'active' }} tab-pane" id="basic">
               
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
                            :endpoint="'/medic/offices/{{ $office->id }}/configfactura'"
                            :activities="{{ $activities }}">
                        </config-factura>
                    @endif

            </div>
        </div>

    </div>
        
	</section>

@endsection