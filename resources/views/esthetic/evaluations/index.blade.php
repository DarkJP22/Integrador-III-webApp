@extends('layouts.clinics.app')

@section('header')

@endsection
@section('content')
    <section class="content-header">
      <h1>Opciones de evaluaciones</h1>
    
    </section>
    <section class="content">
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-header">
                  <a href="{{ url('/clinic/esthetic/evaluations/create') }}" class="btn btn-primary">Nuevo Evaluación</a>
                  <form action="/clinic/esthetic/evaluations" method="GET" autocomplete="off">
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
                      <th>Categoria</th>
                      <th></th>
                    </tr>
                  </thead>
                  @foreach($evaluations as $evaluation)
                    <tr>
                     
                      <td data-title="ID">{{ $evaluation->id }}</td>

                      <td data-title="Nombre">
                        
                        <a href="/clinic/esthetic/evaluations/{{$evaluation->id }}">{{ $evaluation->name }}</a>
                        
                      </td>

                      <td data-title="Categoria">
                        
                       {{ Illuminate\Support\Str::ucfirst($evaluation->category) }}
                        
                      </td>
                     
                     
                      <td data-title="" >
                      
                    
                        <button type="submit"  class="btn btn-danger btn-xs" form="form-delete" formaction="/clinic/esthetic/evaluations/{{$evaluation->id }}">Eliminar</button>
                           
                       
                        
                       
                      </td>
                    </tr>
                  @endforeach
                    @if ($evaluations)
                        <td  colspan="5" class="pagination-container">{!!$evaluations->appends(['q' => $search['q']])->render()!!}</td>
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
