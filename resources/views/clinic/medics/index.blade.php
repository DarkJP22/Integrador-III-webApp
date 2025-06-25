@extends('layouts.clinics.app')

@section('header')

@endsection
@section('content')
    <section class="content-header">
      <h1>Médicos</h1>
    
    </section>
    <section class="content">
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-header">
                  <a href="{{ url('/clinic/medics/create') }}" class="btn btn-primary">Nuevo Médico</a>
                  <form action="/clinic/medics" method="GET" autocomplete="off">
                    <div class="form-group">
                        <div class="input-group input-group" style="width: 150px;">
                      
                        
                            <input type="text" name="q" class="form-control pull-right" placeholder="Buscar..." value="{{ isset($search) ? $search['q'] : '' }}">
                            <div class="input-group-btn">

                              <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                          
                          
                        </div>
                    
                    </div>
                    
                  </form>
                <div class="box-tools">
                  
                </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body table-responsive no-padding" id="no-more-tables">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Teléfono</th>
                      <th>Email</th>
                      <th>Comisión</th>
                      <th>Especialidades</th>
                      <th></th>
                    </tr>
                  </thead>
                  @foreach($medics as $medic)
                    <tr>
                     
                      <td data-title="ID">{{ $medic->id }}</td>
                      <td data-title="Nombre">
                        
                        <a href="/clinic/medics/{{$medic->id }}">{{ $medic->name }}</a>
                        @if(!$medic->verifyOffice($office->id))
                            <button type="submit"  class="btn btn-danger btn-xs" form="form-active-inactive" formaction="/medics/{{$medic->id }}/offices/{{ $office->id }}/verify">Pendiente de confirmación</button>
                          @endif
                     
                      </td>
                      <td data-title="Teléfono">{{ $medic->phone_number }}</td>
                      <td data-title="Email">{{ $medic->email }}</td>
                       <td data-title="Comisión">
                         <form action="{!! url('/medics/'. $medic->id .'/commission') !!}" method="post" id="form-updateMedicCommission"  class="form-horizontal">
                            {{ csrf_field() }} <input type="hidden" name="_method" value="PUT">
                            <div class="input-group">
                              <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                              </div>
                              
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                                 <input type="text" name="commission" placeholder="% Comisión" class="form-control" required="required" value="{{ $medic->commission }}" />
                              </div>
                             
                            </div>
                             
                          </form>

                      </td>
                     <td data-title="Especialidades"> 
                       @foreach($medic->specialities as $speciality)
                            <span class="btn btn-warning btn-xs">{{ $speciality->name }}</span>
                       @endforeach

                       </td>
                      <td data-title="" >
                        <!-- <div class="input-group">
                            <div class="input-group-btn">
                              <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Más
                                          <span class="fa fa-caret-down"></span></button>
                                  <ul class="dropdown-menu">
                                    <li><button type="submit"  class="btn btn-info " form="form-permission" formaction="/offices/{{ $office->id }}/medics/{{$medic->id }}">Permitir facturacion</button></li>
                                    <li><a href="/clinic/medics/{{$medic->id }}/schedules/create" class="btn btn-secondary ">Programar Agenda</a></li>
                                    <li><button type="submit"  class="btn btn-danger " form="form-delete" formaction="/offices/{{ $office->id }}/medics/{{$medic->id }}">Eliminar de la clínica</button></li>
                                  
                                  </ul>
                            </div>
                        </div> -->

                        <div class="btn-group">
                        @if($medic->hasPermissionFeOffice($office->id))
                            <button type="submit"  class="btn btn-danger btn-xs" form="form-permissionfe" formaction="/offices/{{ $office->id }}/medics/{{$medic->id }}/nopermissionfe">Quitar facturación</button>
                        @else
                            <button type="submit"  class="btn btn-info btn-xs" form="form-permissionfe" formaction="/offices/{{ $office->id }}/medics/{{$medic->id }}/permissionfe">Permitir facturación</button>
                        @endif
                        <br>
                        <a href="/clinic/medics/{{$medic->id }}/schedules/create" class="btn btn-secondary btn-xs">Programar Agenda</a><br>
                          <button type="submit"  class="btn btn-danger btn-xs" form="form-delete" formaction="/offices/{{ $office->id }}/medics/{{$medic->id }}">Eliminar de la clínica</button>
                           
                        </div>
                        
                       
                      </td>
                    </tr>
                  @endforeach
                    @if ($medics)
                        <td  colspan="5" class="pagination-container">{!!$medics->appends(['q' => $search['q']])->render()!!}</td>
                    @endif
                </table>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
        </div>

    </section>


<form method="post" id="form-active-inactive">
 {{ csrf_field() }}
</form>
<form method="post" id="form-permissionfe">
  @method('PUT')
 {{ csrf_field() }}
</form>
<form method="post" id="form-delete" data-confirm="Estas Seguro?">
  <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
</form>
<!-- <form method="post" id="form-addToYourPatients" data-confirm="Estas Seguro?">
  {{ csrf_field() }}
</form> -->
@endsection
@push('scripts')

@endpush
