@extends('layouts.medics.app')

@section('header')
    <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
@endsection
@section('content')
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
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        {{-- <div class="callout callout-info">
                            <h4>Informacion importante!</h4>

                            <p>Agrega los consultorios donde brindarás consulta privada. Si el nombre de la <b>"Clínica privada"</b> no aparece, puedes solicitar integrarla al sistema. Nos pondremos en contacto con el administrador para crear la clínica lo antes posible.</p>
                        </div> --}}

                        <div>
                            <div class="row">
                                <div class="col-sm-12 col-sm-6">
                                    <a href="{{ url('/medic/offices/create') }}" class="btn btn-primary">Agregar consultorio</a>
                                </div>
                                <div class="col-sm-12 col-sm-6">
                                    {{-- <agregar-clinica-privada></agregar-clinica-privada> --}}
                                </div>
                            </div>



                        </div>



                        <div class="box-tools">

                        </div>
                        <!-- <form action="/medic/offices" method="GET" autocomplete="off">
                            <div class="input-group input-group-sm" style="width: 150px;">
                              
                                
                                <input type="text" name="q" class="form-control pull-right" placeholder="Buscar..." value="{{ isset($search) ? $search['q'] : '' }}">
                                <div class="input-group-btn">

                                  <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                </div>
                              
                              
                            </div>
                          </form> -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding" id="no-more-tables">

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <!-- <th>ID</th> -->
                                    <th>Nombre</th>
                                    <th>Teléfono</th>

                                    <th>Dirección</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @foreach ($offices as $office)
                                <tr>

                                    <!-- <td data-title="ID">{{ $office->id }}</td> -->

                                    <td data-title="Nombre">

                                        <a href="{{ url('/medic/offices/' . $office->id) }}" title="{{ $office->name }}">{{ $office->name }} </a>
                                        <div>
                                            @if ($office->type === \App\Enums\OfficeType::MEDIC_OFFICE)
                                                <span class="label label-success">Propio</span>
                                            @else
                                                @if (!auth()->user()->verifyOffice($office->id))
                                                    <span class="label label-danger">Pendiente de confirmación</span>
                                                @else
                                                    <span class="label label-warning">Asignado</span>
                                                @endif
                                            @endif
                                        </div>

                                    </td>

                                    <td data-title="Teléfono">{{ $office->phone }}</td>
                                    <td data-title="Dirección">{{ $office->address }}</td>
                                    <td data-title="" style="padding-left: 5px;" class="flex-container-ce">

                                        <div tabindex="0" class="tippyTooltip" title="{{ !auth()->user()->subscriptionPlanHasFe() && $office->type === \App\Enums\OfficeType::MEDIC_OFFICE ? 'Para configurar debes tener una subscripción de Factura electrónica. ': ($office->type === \App\Enums\OfficeType::CLINIC ? 'La Configuración de Factura Electrónica la hace la clínica': 'Configuración de Factura Electrónica') }}">
                                            <a href="{{ auth()->user()->subscriptionPlanHasFe() && $office->type === \App\Enums\OfficeType::MEDIC_OFFICE ? '/medic/offices/' . $office->id . '/configfactura': '#' }}" class="btn btn-primary btn-sm" {{ auth()->user()->subscriptionPlanHasFe() && $office->type === \App\Enums\OfficeType::MEDIC_OFFICE ? '': 'disabled' }}>Factura Electrónica</a>
                                        </div>

                                        <button type="submit" class="btn btn-danger" form="form-delete" formaction="{!! url('/offices/' . $office->id) !!}" title="Eliminar Consultorio o clínica"><i class="fa fa-remove"></i></button>


                                    </td>

                                </tr>
                            @endforeach
                            @if ($offices)
                                <td colspan="5" class="pagination-container">{!! $offices->appends(['q' => $search['q']])->render() !!}</td>
                            @endif
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

    </section>



    <form method="post" id="form-delete" data-confirm="Estas Seguro?">
        <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
    </form>
@endsection
@push('scripts')
    <script>
        $(function() {
            window.emitter.on('officeToSelect', function(data) {

                window.location.reload();


            });
        });
    </script>
@endpush
