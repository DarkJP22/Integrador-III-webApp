@extends('layouts.medics.app')

@section('content')

    <section class="content-header">
        <h1>Cambio de cuentas</h1>
    
    </section>
	<section class="content">
            <div class="callout callout-info">
                <h4>Informacion importante!</h4>

                <p>Para obtener el modulo de facturación y otro beneficios cambiate de tipo de cuenta
                </p>
                <!-- <p><button type="button" data-toggle="modal" data-target="#contact-modal" data-user="" data-subject="Solicitud de cambio de cuenta centro médico" class="btn btn-secondary"><span>Solicitar Cambio</span></a></p> -->
                
            </div>
            <div class="row">

         
            @foreach($plans as $plan)
            <div class="col-md-3">
            <!-- Widget: user widget style 1 -->
                <div class="box box-widget widget-user-2 widget-plan">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header {{ auth()->user()->subscription && $plan->id == auth()->user()->subscription->plan_id ? 'bg-primary' : 'bg-secondary' }}">
                    <!-- <div class="widget-user-image">
                        <img class="img-circle" src="/img/default-avatar.jpg" alt="User Avatar">
                    </div> -->
                    <!-- /.widget-user-image -->
                    <h3 class="widiget-user-username">{{ $plan->title }}</h3>
                    <h5 class="widiget-user-desc">{{ $plan->costName }}</h5>
                    </div>
                    <div class="box-body">
                        {!! $plan->description !!}
                    </div>
                    <div class="box-footer text-center">
                       
                        @if(auth()->user()->subscription)
                            @if($plan->id == auth()->user()->subscription->plan_id)
                                <a href="#" class="btn btn-secondary">Subcripcion Actual</a>
                            @else
                            
                                <!-- <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modalNewCentroMedico" data-plan="{{ $plan->title }} - {{ $plan->costName }}" data-planId="{{ $plan->id }}">Comprar</a> -->
                                @if($plan->cost > 0)
                                    <a href="{{ url('/medic/subscriptions/'. $plan->id .'/change') }}" class="btn btn-primary" >Cambiar</a>
                                @else
                                    <form action="/medic/subscriptions/{{ $plan->id }}/changefree" method="POST" data-confirm="Esta a punto de cambiarte al plan gratuito. ¿Estas Seguro que desear realizar esta accion?">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-primary">Cambiar</button>
                                    </form>
                                @endif
                            @endif
                        @else
                            <form action="/medic/subscriptions/{{ $plan->id }}/buy" method="POST" >
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-primary">Seleccionar</button>
                            </form>
                            
                        @endif
                    </div>
                </div>
            <!-- /.widget-user -->
            </div>
               
            @endforeach
            </div>
    </section>
    <modal-new-centro-medico :offices="{{ auth()->user()->offices()->where('type', \App\Enums\OfficeType::MEDIC_OFFICE)->get() }}"></modal-new-centro-medico>
@endsection
@push('scripts')
<script>
    $('#contact-modal').on('show.bs.modal', function (e) {

        var button = $(e.relatedTarget)
        var subject = button.attr('data-subject');

        window.emitter.emit('subjectEvent', subject);
    });

    $('#modalNewCentroMedico').on('show.bs.modal', function (e) {

    var button = $(e.relatedTarget)
    var planId = button.attr('data-planId');
    var planName = button.attr('data-plan');

    window.emitter.emit('showNewCentroMedicoModal', {planId, planName});
    });

</script>
@endpush