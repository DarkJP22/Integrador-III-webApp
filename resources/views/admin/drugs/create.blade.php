@extends('layouts.admins.app')

@section('content')
    <section class="content-header">
        <h1>Crear medicamento</h1>

    </section>
    <section class="content">

        <div class="row">

            <div class="col-md-8">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#basic" data-toggle="tab">Información del medicamento</a></li>

                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="basic">
                            <form method="POST" action="{{ url('/drugs') }}" class="form-horizontal"  enctype="multipart/form-data">

                                {{ csrf_field() }}
                                @include('admin/drugs/_form')

                            </form>


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

@endpush
