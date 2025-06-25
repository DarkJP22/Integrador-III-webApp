@extends('layouts.assistants.app')
@section('header')
  <link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css"> 
@endsection
@section('content')
    <section class="content-header">
      <h1>Tipos de plan de afiliaciones</h1>
    
    </section>

    <section class="content">
          
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-header">
               <a href="/affiliationplans/create" class="btn btn-primary">Crear Tipo de plan</a>
               <a href="/assistant/affiliations" class="btn btn-primary">Afiliaciones</a>
                <a href="/invoices" class="btn btn-primary">Facturacion</a>
                  <form action="/assistant/invoices" method="GET" autocomplete="off">
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
                        <th scope="col">Cuota</th>
                        <th scope="col">Descuento</th>
                        <th scope="col">Periodo (M)</th>
                        <th scope="col">Personas</th>
                        <th scope="col"></th>
                       
                    </tr>
                  </thead>
                  <tbody>
                     @foreach($plans as $plan)
                            
                        <tr>
                             <td>
                                {{ $plan->id }}
                            </td>
                            <th scope="row">
                                <a href="/affiliationplans/{{ $plan->id }}">
                                    {{ $plan->name }}
                                </a>

                            </th>
                            
                            <td>{{ money($plan->cuota,'') }} <small>{{ $plan->CodigoMoneda }}</small></td>
                            <td>{{ $plan->discount }} </td>
                            <td>
                              {{ $plan->period }}
                                
                            </td>
                            <td>
                              {{ $plan->persons }}
                                
                            </td>
                            <td>
                            <button type="submit" class="btn btn-danger" form="form-delete" formaction="{!! url('/affiliationplans/'.$plan->id) !!}" title="Eliminar Plan"><i class="fa fa-remove"></i></button>
                                
                            </td>
                           
                        </tr>
                    @endforeach
                        @if ($plans)
                        <td  colspan="7" class="pagination-container">{!!$plans->appends(['q' => $search['q']])->render()!!}</td>
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
@endsection
@push('scripts')
<script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script> 

<script>

</script>
@endpush
