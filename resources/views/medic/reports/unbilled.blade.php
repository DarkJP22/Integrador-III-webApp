@extends('layouts.medics.app')
@section('header')
  <link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css"> 
@endsection
@section('content')
    <section class="content-header">
      <h1>Citas No Facturadas</h1>
    
    </section>

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
             
              
                  <form action="/unbilled" method="GET" autocomplete="off">
                      <div class="form-group">
                       
                          <div class="col-sm-2">
                            <div class="input-group input-group-sm">
                          
                                
                                <input type="text" name="q" class="form-control" placeholder="Cliente..." value="{{ isset($search) ? $search['q'] : '' }}">
                                <div class="input-group-btn">

                                  <button type="submit" class="btn btn-primary">Buscar</button>
                                </div>
                              
                              
                            </div>
                           
                          </div>
                          <div class="col-sm-3">
                                           
                              
                              <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Desde" value="{{ $search['start'] }}">
                                    
                                
                            
                        
                            
                        </div>
                        <div class="col-sm-3">
                            
                                
                              <input data-input type="text" class="date form-control" placeholder="Fecha Hasta" name="end" value="{{ $search['end'] }}">
                                   
                            
                        </div>
                        <div class="col-sm-3">
                            <select name="office" id="office" class="form-control">
                            <option value="">-- Filtro por consultorio --</option>
                            @foreach(auth()->user()->offices as $userClinic)
                            <option value="{{  $userClinic->id }}" {{ (isset($search) && $search['office'] == $userClinic->id) ? 'selected' : '' }}>{{  $userClinic->name }}</option>
                            @endforeach
                            
                        </select>
                        
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
                        <th>#</th>
                        
                        <th>Paciente</th>
                        <th>Consulta</th>
                        <th>Fecha</th>
                        <th>De</th>
                        <th>a</th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($unbilled as $appointment)
                          <tr>
                            <td>{{ $appointment->id }}</td>
                      
                             <td>
                             {{ ($appointment->patient) ? $appointment->patient->first_name : 'Paciente Eliminado' }}
                            </td>
                           
                            <td data-title="Motivo"><a href="{{ url('/agenda/appointments/'.$appointment->id) }}" title="{{ ($appointment->patient) ? $appointment->patient->first_name : 'Paciente Eliminado' }}">{{ $appointment->title }}</a></td>
                            <td data-title="Fecha">{{ \Carbon\Carbon::parse($appointment->date)->toDateString() }}</td>
                            <td data-title="De">{{ \Carbon\Carbon::parse($appointment->start)->format('h:i:s A') }}</td>
                            <td data-title="a">{{ \Carbon\Carbon::parse($appointment->end)->format('h:i:s A') }}</td>
                          </tr>
                      @endforeach
                        @if ($unbilled)
                        <td  colspan="6" class="pagination-container">{!!$unbilled->appends(['q' => $search['q'],'start' => $search['start'], 'end' => $search['end']])->render()!!}</td>
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


@endsection
@push('scripts')
<script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script> 
<script>

  
 
  $('.date').datetimepicker({
      format:'YYYY-MM-DD',
      locale: 'es',
      
   });
   $('#office').on('change', function (e) {


    $(this).parents('form').submit();

  });
</script>
@endpush
