@extends('layouts.users.app')

@section('content')
    <div class="banner tw-absolute tw-top-1/2 -tw-translate-y-1/2 tw-left-1/2 -tw-translate-x-1/2">
        <div class="banner-logo">
            <img src="/img/logo.png" alt="Doctor Blue" />
        </div>

        {{-- <h3>¿Que deseas hacer?</h3> --}}
    </div>


    {{-- <section class="content">
        <div class="row">

            <div class="row">
                <div class=" col-xs-12 col-sm-12 col-md-4">
                    <!-- small box -->
                    <div class="small-box bg-primary box-search-medic" style="position: relative;">
                        <div class="inner">
                            <h3>Buscar Médico</h3>
                            <div class="row">

                                <div class=" col-xs-12 col-sm-6">
                                    <div class="small-box bg-secondary">
                                        <div class="inner">
                                            <span>General </span>
                                            <a href="/medics?typeOfSearch=general"></a>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xs-12 col-sm-6">
                                    <div class="small-box bg-secondary">
                                        <div class="inner">
                                            <span>Especialista</span>
                                            <a href="/medics?typeOfSearch=specialist"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="icon">
                            <i class="fa fa-user-md"></i>
                        </div>

                    </div>
                </div>
                <!-- ./col -->
                <div class=" col-xs-12 col-sm-12 col-md-4">
                    <!-- small box -->
                    <div class="small-box bg-primary" style="position: relative;">
                        <div class="inner">
                            <h3>Buscar clínica</h3>

                            <p>Hospital</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-hospital-o"></i>
                        </div>
                        <a href="/clinics" class="small-box-footer">Iniciar <i class="fa fa-arrow-circle-right"></i></a>
                        <a href="/clinics" style="position: absolute;left:0;right: 0;top:0; bottom: 0;"></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class=" col-xs-12 col-sm-12 col-md-4">
                    <!-- small box -->
                    <div class="small-box bg-primary" style="position: relative;">
                        <div class="inner">
                            <h3>Mi expediente</h3>

                            <p>Controles, Historial médico, Medicamentos</p>

                        </div>
                        <div class="icon">
                            <i class="fa fa-edit"></i>
                        </div>
                        @if (auth()->check() &&
                            auth()->user()->patients->count())
                            <a href="/patients/{{ auth()->user()->patients->first()->id }}/expedient" class="small-box-footer">Iniciar <i class="fa fa-arrow-circle-right"></i></a>
                            <a href="/patients/{{ auth()->user()->patients->first()->id }}/expedient" style="position: absolute;left:0;right: 0;top:0; bottom: 0;"></a>

                            <!-- <a href="#" data-toggle="modal" data-target="#modalCodeExpedient" title="Click para ingresar codigo" data-user="{{ auth()->user()->id }}" data-patient="{{ auth()->user()->patients->first()->id }}" class="small-box-footer">Iniciar <i class="fa fa-arrow-circle-right"></i> </a>
                          <a href="#" data-toggle="modal" data-target="#modalCodeExpedient" title="Click para ingresar codigo" data-user="{{ auth()->user()->id }}" data-patient="{{ auth()->user()->patients->first()->id }}" style="position: absolute;left:0;right: 0;top:0; bottom: 0;"></a> -->
                        @else
                            <span class="label label-danger">Necesitas tener una cuenta y al menos un paciente para ir a su expediente</span>
                        @endif
                    </div>
                </div>
                <!-- ./col -->

            </div>

        </div>

    </section> --}}
    <modal-code-expedient></modal-code-expedient>
@endsection
@push('scripts')
    <script>
        $('#modalCodeExpedient').on('show.bs.modal', function(e) {

            var button = $(e.relatedTarget)
            var userId = button.attr('data-user');
            var patientId = button.attr('data-patient');
            let data = {
                'userId': userId,
                'patientId': patientId
            }
            window.emitter.emit('showCodeExpedientModal', data);
        })
    </script>
@endpush
