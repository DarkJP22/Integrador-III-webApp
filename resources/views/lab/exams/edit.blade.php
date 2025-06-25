@extends('layouts.laboratories.app')
@section('css')
@endsection
@section('content')
    <section class="content-header">
        <h1>Editar Examen (Producto)</h1>

    </section>
    <section class="content">

        <div class="row">


            <div class="col-md-8">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#basic" data-toggle="tab">Información del Examen</a></li>


                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="basic">


                            @include('lab/exams/_form')


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
    <script>
        $(function() {
            let exam = {!! $exam !!}
            window.emitter.emit('editProduct', exam);
        });
    </script>
@endpush
