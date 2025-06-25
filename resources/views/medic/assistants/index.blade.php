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
                
                  <div>
                    <div class="row">
                      <div class="col-sm-12 col-sm-6">
                        <a href="{{ url('/medic/assistants/create') }}" class="btn btn-primary">Crear Asistente o Secretaria</a>
                      </div>
                      <div class="col-sm-12 col-sm-6">
                       
                      </div>
                    </div>
                  
                  
                  
                  </div>

                

                <div class="box-tools">
                  <form action="/medic/assistants" method="GET" autocomplete="off">
                        <div class="input-group input-group-sm" style="width: 150px;">
                          
                            
                            <input type="text" name="q" class="form-control pull-right" placeholder="Buscar..." value="{{ isset($search) ? $search['q'] : '' }}">
                            <div class="input-group-btn">

                              <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                          
                          
                        </div>
                      </form>
                </div>
                
              </div>
              <!-- /.box-header -->
              <div class="box-body table-responsive no-padding" id="no-more-tables">
                
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <!-- <th>ID</th> -->
                      <th>Nombre</th>
                      <th>Teléfono</th>
                      <th>Consultorio</th>
                      
                      <th></th>
                    </tr>
                  </thead>
                  @foreach($assistants as $assistant)
                    <tr>
                     
                      <!-- <td data-title="ID">{{ $assistant->id }}</td> -->
                     
                      <td data-title="Nombre">
                   
                        <a href="{{ url('/medic/assistants/'.$assistant->id) }}" title="{{ $assistant->name }}">{{ $assistant->name }} </a>
                    
                      
                      </td>
                     
                        <td data-title="Teléfono">{{ $assistant->fullPhone }}</td>

                        <td data-title="Consultorio">
                          @foreach($assistant->clinicsAssistants as $office)
                            {{ $office->name }}
                          @endforeach
                        </td>
                        
                        <td data-title="" style="padding-left: 5px;" class="flex-container-ce">
                        
                       
                        
                        
                        <button type="submit" class="btn btn-danger" form="form-delete" formaction="{!! url('/assistants/'.$assistant->id) !!}" title="Eliminar Consultorio o clínicia"><i class="fa fa-remove"></i></button>
                          
                
                        </td>
                     
                    </tr>
                  @endforeach
                    @if ($assistants)
                        <td  colspan="5" class="pagination-container">{!!$assistants->appends(['q' => $search['q']])->render()!!}</td>
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
 

@endpush
