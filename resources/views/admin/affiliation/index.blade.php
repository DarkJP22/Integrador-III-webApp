@extends('layouts.admins.app')

@section('content')
<section class="content-header">
    <h1>Solicitudes de Afiliación</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Lista de solicitudes</h3>
                </div>
                <div class="box-body table-responsive no-padding" id="no-more-tables">

                @if(!empty($affiliationUsers) && $affiliationUsers->count())
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario ID</th>
                                    <th>Fecha</th>
                                    <th>Tipo de Afiliación</th>
                                    <th>Voucher</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($affiliationUsers as $affiliation)
                                    <tr>
                                        <td>{{ $affiliation->id ?? '' }}</td>
                                        <td>{{ $affiliation->user_id ?? '' }}</td>
                                        <td>{{ $affiliation->date ?? '' }}</td>
                                        <td>{{ $affiliation->type_affiliation ?? '' }}</td>
                                        <td>
                                            @if(!empty($affiliation->voucher))
                                                <a href="{{ asset('storage/'.$affiliation->voucher) }}" target="_blank">Ver Voucher</a>
                                            @else
                                                Sin voucher
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($affiliation->active))
                                                <span class="label label-success">Activo</span>
                                            @else
                                                <span class="label label-danger">Inactivo</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center">No hay solicitudes de afiliación registradas.</p>
                    @endif

                </div> <!-- /.box-body -->
            </div> <!-- /.box -->
        </div>
    </div>
</section>
@endsection

@push('scripts')
@endpush


