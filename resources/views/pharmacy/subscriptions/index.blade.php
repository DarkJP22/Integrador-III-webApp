@extends('layouts.pharmacies.app')

@section('content')

    <section class="content-header">
        <h1>Cambio de cuentas</h1>
    
    </section>
	<section class="content">
            <div class="callout callout-info">
                <h4>Informacion importante!</h4>

                <p>Para obtener el modulo de punto de venta cambiate al tipo de cuenta de <b>Farmacia + Punto de venta</b>
                </p>
                <!-- <p><button type="button" data-toggle="modal" data-target="#contact-modal" data-user="" data-subject="Solicitud de cambio de cuenta centro médico" class="btn btn-secondary"><span>Solicitar Cambio</span></a></p> -->
                
            </div>
            <div class="row">

         
            @foreach($plans as $plan)
            <div class="col-md-4">
            <!-- Widget: user widget style 1 -->
                <div class="box box-widget widget-user-2 widget-plan">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header {{ auth()->user()->subscription && $plan->id == auth()->user()->subscription->plan_id ? 'bg-primary' : 'bg-secondary' }}">
                    <!-- <div class="widget-user-image">
                        <img class="img-circle" src="/img/default-avatar.jpg" alt="User Avatar">
                    </div> -->
                    <!-- /.widget-user-image -->
                    <h3 class="widgets-user-username">{{ $plan->title }}</h3>
                    <h5 class="widgets-user-desc">{{ $plan->costName }}</h5>
                    </div>
                    <div class="box-body">
                        {!! $plan->description !!}
                    </div>
                    <div class="box-footer text-center">
                       
                        @if(auth()->user()->subscription && $plan->id == auth()->user()->subscription->plan_id)
                            <a href="#" class="btn btn-secondary">Subcripcion Actual</a>
                        @else
                           <a href="{{ url('/pharmacy/subscriptions/'. $plan->id .'/change') }}" class="btn btn-primary" >Cambiar</a>
                           
                        @endif
                    </div>
                </div>
            <!-- /.widget-user -->
            </div>
               
            @endforeach
            </div>
    </section>
    
@endsection
@push('scripts')


@endpush