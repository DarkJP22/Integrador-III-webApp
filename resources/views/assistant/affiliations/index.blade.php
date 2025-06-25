@extends('layouts.assistants.app')
@section('header')
  <link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css"> 
@endsection
@section('content')
    <section class="content-header">
      <h1>Afiliaciones</h1>
    
    </section>

    <section class="content">
          
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-header">
               <a href="/affiliations/create" class="btn btn-primary">Crear Afiliacion</a>
                
                <a href="/invoices" class="btn btn-primary">Facturacion</a>
                  <form action="/assistant/affiliations" method="GET" autocomplete="off">
                      <div class="form-group">
                       
                          <div class="col-sm-2">
                            <div class="input-group input-group-sm">
                          
                                
                                <input type="text" name="q" class="form-control" placeholder="Nombre..." value="{{ isset($search) ? $search['q'] : '' }}">
                                <div class="input-group-btn">

                                  <button type="submit" class="btn btn-primary">Buscar</button>
                                </div>
                              
                              
                            </div>
                           
                          </div>
                         
                      </div>
                   
                  </form>
                  <div class="box-tools">
                  </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Cedula</th>
                        <th scope="col">Tipo Plan</th>
                        <th scope="col">Fecha Inscripción</th>
                        <th scope="col">Periodo (M)</th>
                        <th scope="col">Total Cuota</th>
                        <th scope="col">Acumulado</th>
                        <th scope="col">Estado</th>
                       
                        <th scope="col"></th>
                       
                    </tr>
                  </thead>
                  <tbody>
                     @foreach($affiliations as $affiliation)
                            
                        <tr>
                             <td>
                                {{ $affiliation->id }}
                            </td>
                            <th scope="row">
                                <a href="/affiliations/{{ $affiliation->id }}">
                                    {{ $affiliation->holder->fullname }}
                                </a><br>
                            
                            </th>
                            <td>{{ $affiliation->holder->ide }} </td>
                            <td>{{ $affiliation->plan->name }} </td>
                            <td>{{ $affiliation->inscription }} </td>
                            <td>
                              {{ $affiliation->plan->period }}
                              
                            </td>
                            <td>{{ money($affiliation->plan->cuota,'') }} <small>{{ $affiliation->CodigoMoneda }}</small></td>
                            
                            <td>{{ money($affiliation->acumulado,'') }} <small>{{ $affiliation->CodigoMoneda }}</small></td>
                            <td>
                            @if ($affiliation->active)
                             
                                    <button type="submit"  class="btn btn-success btn-xs" form="form-active-inactive" formaction="{!! URL::route('affiliations.inactive', [$affiliation->id]) !!}">Active</button>
                                

                            @else
                                
                                <button type="submit"  class="btn btn-danger btn-xs " form="form-active-inactive" formaction="{!! URL::route('affiliations.active', [$affiliation->id]) !!}" >Inactive</button>

                            @endif
                              
                            </td>
                           
                            
                            <td>
                               <button type="submit" class="btn btn-danger btn-sm" form="form-delete" formaction="{!! url('/affiliations/'.$affiliation->id) !!}" title="Eliminar Plan"><i class="fa fa-remove"></i></button>
                                
                            </td>
                           
                        </tr>
                    @endforeach
                        @if ($affiliations)
                        <td  colspan="9" class="pagination-container">{!!$affiliations->appends(['q' => $search['q']])->render()!!}</td>
                    @endif
                        

                  </tbody>
                 
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
<form method="post" id="form-active-inactive">
 {{ csrf_field() }}
</form>

@endsection
@push('scripts')
<script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script> 

<script>

</script>
@endpush
