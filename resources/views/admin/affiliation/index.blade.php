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
                    <div class="box-toolsdd filters">
                  <form action="/admin/affiliations/request/affiliate/state" method="GET" autocomplete="off">
  <div class="row">
    <!-- Buscar por nombre -->
    <div class="col-xs-12 col-sm-3">
      <div class="input-group">
        <input type="text" name="nombre" class="form-control pull-right" placeholder="Buscar por nombre..." value="{{ request()->get('nombre') }}">
        <div class="input-group-btn">
          <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
        </div>
      </div>
    </div>

    <!-- Filtrar por estado -->
    <div class="col-xs-12 col-sm-3">
      <div class="input-group">
        <select name="estado" class="form-control pull-right">
          <option value="">Seleccionar estado...</option>
          <option value="Pending" {{ request()->get('estado') == 'Pending' ? 'selected' : '' }}>Pendiente</option>
          <option value="Approved" {{ request()->get('estado') == 'Approved' ? 'selected' : '' }}>Aprobado</option>
          <option value="Denied" {{ request()->get('estado') == 'Denied' ? 'selected' : '' }}>Denegado</option>
        </select>
        <div class="input-group-btn">
          <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
        </div>
      </div>
    </div>
  </div>
</form>

                </div>
                </div>
                <div class="box-body table-responsive no-padding" id="no-more-tables">

                    @if(!empty($affiliationUsers) && $affiliationUsers->count())
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario ID</th>
                                <th>Nombre Usuario</th>
                                <th>Fecha</th>
                                <th>Descuento</th>
                                <th>Tipo de Afiliación</th>
                                 <th>Precio</th>
                                <th>Voucher</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($affiliationUsers as $affiliation)
                            <tr>
                                <td>{{ $affiliation->id ?? '' }}</td>
                                <td>{{ $affiliation->user_id ?? '' }}</td>
                                <td>{{ $affiliation->user->name ?? '' }}</td>
                                <td>{{ $affiliation->created_at ?? '' }}</td>
                                <td>{{ $affiliation->discount ?? '' }}%</td>
                                <td>
                                    @if ($affiliation->type_affiliation ?? false)
                                    {{
            $affiliation->type_affiliation == 'Monthly' ? 
                'Mensual' : 
                ($affiliation->type_affiliation == 'Semi-Annually' ? 'Semestral' : 'Anual') 
        }}
                                    @endif
                                </td>
                                <td>{{ $affiliation->priceToAffiliation ?? '' }}</td>
                                <td>
                                    @if(!empty($affiliation->voucher))
                                    <a href="{{ asset('storage/'.$affiliation->voucher) }}" target="_blank">Ver Voucher</a>
                                    @else
                                    Sin voucher
                                    @endif
                                </td>
                              <td>
    @if($affiliation->active == "Pending")
        Pendiente
    @elseif($affiliation->active == "Approved")
        Aprobado
    @elseif($affiliation->active == "Denied")
        Denegado
    @else
        No definido
    @endif
</td>
                                <td>
                                    @if ($affiliation->active == 'Pending')
                                    <button type="submit" class="btn btn-success btn-xs" form="form-update" formaction="{!! URL::route('requestAffiliation.active', [$affiliation->id]) !!}">Activar</button>
                                    <button type="submit" class="btn btn-danger btn-xs " form="form-update" formaction="{!! URL::route('requestAffiliation.inactive', [$affiliation->id]) !!}">Inactivar</button>
                                    @elseif($affiliation->active == 'Approved')
                                        <button type="submit" class="btn btn-danger btn-xs " form="form-update" formaction="{!! URL::route('requestAffiliation.inactive', [$affiliation->id]) !!}">Inactivar</button>
                                    @else
                                        <button type="submit" class="btn btn-success btn-xs" form="form-update" formaction="{!! URL::route('requestAffiliation.active', [$affiliation->id]) !!}">Activar</button>
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
<form method="post" id="form-update" data-confirm="Estas Seguro?">
    <input name="_method" type="hidden" value="POST">{{ csrf_field() }}
</form>
<form method="post" id="form-active-inactive">
    {{ csrf_field() }}
</form>
@endsection

@push('scripts')
@endpush