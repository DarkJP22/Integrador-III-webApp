@extends('layouts.pharmacies.app')
@section('header')
    <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
    <link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.print.css" media="print">
@endsection
@section('content')
    <section class="content-header">
        <h1>Paciente {{ $patient->fullname }}</h1>

    </section>

    <section class="content">

        <div class="row">
            <div class="col-md-4">

                <avatar-form :user="{{ $patient }}" url="/api/patients/" :read="true"></avatar-form>
                <div class="box box-default">
                    <div class="box-body box-profile ">
                        <emergency-contacts :patient="{{ $patient }}"></emergency-contacts>

                    </div>
                </div>
                <div class="box box-default">
                    <div class="box-body box-profile ">
                        <api-pharmacy-credentials :patient="{{ $patient }}"></api-pharmacy-credentials>

                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="{{ isset($tab) ? ($tab =='basic') ? 'active' : '' : 'active' }}"><a href="#basic"
                                                                                                       data-toggle="tab">Información
                                Básica</a></li>
                        <li class=""><a href="#pressure" data-toggle="tab">Control de presión</a></li>
                        <li class=""><a href="#sugar" data-toggle="tab">Control de azúcar</a></li>
                        <li class=""><a href="#allergies" data-toggle="tab">Alergias</a></li>
                        <li class=""><a href="#medicines" data-toggle="tab">Medicamentos</a></li>
                        {{-- <li class=""><a href="#historialCompras" data-toggle="tab">Historial compra medicamentos</a></li> --}}


                    </ul>
                    <div class="tab-content">
                        <div class="{{ isset($tab) ? ($tab =='basic') ? 'active' : '' : 'active' }} tab-pane"
                             id="basic">
                            <form method="POST" action="{{ url('/general/patients/'.$patient->id) }}"
                                  class="form-horizontal">
                                {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
                                @include('patients._form')

                                @can('update', $patient)
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-primary">Actualizar</button>
                                        </div>
                                    </div>
                                @endcan
                            </form>

                        </div>
                        <!-- /.tab-pane -->


                        <div class="{{ isset($tab) ? ($tab =='pressure') ? 'active' : '' : '' }} tab-pane"
                             id="pressure">
                            <pressure-control :pressures="{{ $pressures }}" :patient="{{ $patient }}" url="/pharmacy"
                                              :alerts="true"></pressure-control>


                        </div>
                        <!-- /.tab-pane -->
                        <div class="{{ isset($tab) ? ($tab =='sugar') ? 'active' : '' : '' }} tab-pane" id="sugar">
                            <sugar-control :sugars="{{ $sugars }}" :patient="{{ $patient }}" url="/pharmacy"
                                           :alerts="true"></sugar-control>

                        </div>
                        <!-- /.tab-pane -->
                        <div class="{{ isset($tab) ? ($tab =='allergies') ? 'active' : '' : '' }} tab-pane"
                             id="allergies">
                            <allergies :allergies="{{ $allergies }}" :patient="{{ $patient }}"></allergies>

                        </div>
                        <div class="{{ isset($tab) ? ($tab =='medicines') ? 'active' : '' : '' }} tab-pane"
                             id="medicines">

                            {{--                            <div class="pharmacy-medicines">--}}
                            {{--                                <h3>Medicamentos Actuales</h3>--}}

                            {{--                                <medicines-availables :medicines="{{ json_encode($medicines) }}"--}}
                            {{--                                                      :patient="{{ $patient }}"></medicines-availables>--}}
                            {{--                            </div>--}}
                            <div class="pharmacy-medicines">
                                <h3>Agregue los medicamentos del paciente</h3>
                                <div class="callout callout-light">
                                    <p>Nota: Los medicamentos agregados seran visibles en la App del paciente</p>
                                </div>
                                <pharmacy-medicines :medicines="{{ json_encode($pmedicines) }}"
                                                    :patient="{{ $patient }}"
                                :presentations="{{ json_encode(\App\Drug::PRESENTATIONS) }}"></pharmacy-medicines>

                            </div>


                        </div>
                        {{-- <div class="{{ isset($tab) ? ($tab =='historialCompras') ? 'active' : '' : '' }} tab-pane" id="historialCompras">


                            <historial-compras-pharmacy :patient="{{ $patient }}" :pharmacy="{{ auth()->user()->pharmacies->first() }}"></historial-compras-pharmacy>




                        </div> --}}


                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->


            </div>

        </div>
    </section>

@endsection
@push('scripts')
    <script src="/vendor/select2/js/select2.full.min.js"></script>
    <script src="/vendor/moment/min/moment.min.js"></script>
    <script src="/vendor/moment/locale/es.js"></script>
    <script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
    <script src="/vendor/inputmask/dist/jquery.inputmask.bundle.js"></script>
    <script src="/vendor/fullcalendar/dist/jquery-ui.min.js"></script>
    <script src="/vendor/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="/vendor/fullcalendar/dist/locale/es.js"></script>

    <script>
        $(function () {

            $("[data-mask]").inputmask();

            $('.datepicker').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'es',
                //useCurrent: true,
                //defaultDate: new Date(),
            });

            $('.timepicker').datetimepicker({
                format: 'LT',
                stepping: 60,
                //format: 'hh:mm',
                locale: 'es',
                //useCurrent: true,
                //defaultDate: new Date(),
            });


        });
    </script>
@endpush