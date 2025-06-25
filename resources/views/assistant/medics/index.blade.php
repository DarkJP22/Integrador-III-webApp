@extends('layouts.assistants.app')

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
              
                <form action="/assistant/medics" method="GET" autocomplete="off">
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
                      <th>Especialidades</th>
                      <th></th>
                    </tr>
                  </thead>
                  @foreach($medics as $medic)
                    <tr>
                     
                      <td data-title="ID">{{ $medic->id }}</td>
                      <td data-title="Nombre">
                        
                        {{ $medic->name }}
                        @if(!$medic->verifyOffice($office->id))
                            <button type="submit"  class="btn btn-danger btn-xs" form="form-active-inactive" formaction="/medics/{{$medic->id }}/offices/{{ $office->id }}/verify">Pendiente de confirmación</button>
                          @endif
                     
                      </td>
                      <td data-title="Teléfono">{{ $medic->phone_number }}</td>
                      <td data-title="Email">{{ $medic->email }}</td>
                     <td data-title="Especialidades"> 
                       @foreach($medic->specialities as $speciality)
                            <span class="btn btn-warning btn-xs">{{ $speciality->name }}</span>
                       @endforeach

                       </td>
                      <td data-title="" style="padding-left: 5px;">
                       <a href="/invoices/create?m={{ $medic->id }}" class="btn btn-primary">Crear Factura</a>
                        
                       <a href="/assistant/medics/{{$medic->id }}/schedules/create" class="btn btn-secondary">Programar Agenda</a>
                       
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
<form method="post" id="form-delete" data-confirm="Estas Seguro?">
  <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
</form>
<!-- <form method="post" id="form-addToYourPatients" data-confirm="Estas Seguro?">
  {{ csrf_field() }}
</form> -->
@endsection
@push('scripts')

@endpush
