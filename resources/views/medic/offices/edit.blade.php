@extends('layouts.medics.app')
@section('header')

<link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">

@endsection
@section('content')
    <section class="content-header">
        <h1>Editar Consultorio independiente {{ $office->id }}</h1>

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
        <div class="row">


            <div class="col-md-8">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#basic" data-toggle="tab">Información Básica</a></li>

                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="basic">
                            <new-office back-url="/medic/offices"></new-office>
                        </div>
                        <!-- /.tab-pane -->




                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->

            </div>

        </div>
    </section>
@endsection
@push('scripts')
    {{-- <script src="/vendor/inputmask/dist/jquery.inputmask.bundle.js"></script> --}}
    <script src="/vendor/moment/min/moment.min.js"></script>
    <script src="/vendor/moment/locale/es.js"></script>
    <script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script> 
    <script>
        $(function() {
            let office = @json($office); //{!! json_encode($office) !!};
            window.emitter.emit('editOffice', office);

            $('.datepicker').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'es',
                //useCurrent: true,
                //defaultDate: new Date(),
            });

            $('.timepicker').datetimepicker({
                format: 'HH:mm:ss',
                stepping: 30,


            });
        });
    </script>
@endpush
