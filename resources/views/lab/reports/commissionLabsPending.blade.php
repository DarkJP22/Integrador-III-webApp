@extends('layouts.laboratories.app')
@section('content')
    <section class="content-header">
      <h1>Comisión Doctor Blue (por boleta de laboratorio atendida)</h1>
    
    </section>

    <section class="content">
         
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-header">
                    <form action="/lab/commission/labs/pending" method="GET" autocomplete="off">
                      <div class="form-group">
                       
                          
                          <div class="col-sm-3 flatpickr">
                                           
                              
                              <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Desde" value="{{ request('start') }}">
                                    
                                
                            
                        
                            
                        </div>
                        <div class="col-sm-3 flatpickr">
                            
                                
                              <input data-input type="text" class="date form-control" placeholder="Fecha Hasta" name="end" value="{{ request('end') }}">
                                   
                            
                        </div>
                        <div class="col-sm-3">
                            <select-medic url='/lab/medics' :medic="{{ json_encode($selectedMedic) }}" hidden-field="medic"></select-medic>
                        
                        </div>
                        <button type="submit" class="btn btn-primary">Buscar</button>
                        
                      </div>
                   
                  </form>
              
                  
                  <div class="box-tools">
                  </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body table-responsive">
                 <table class="table no-margin">
                      <thead>
                      <tr>
                       
                        <th>Médico</th>
                        <th>Boletas Generadas</th>
                        <th>Monto</th>
                     
                        
                        
                      </tr>
                      </thead>
                      <tbody>
                        @foreach($commissions as $commission)
                          <tr>
                             
                                <td>{{ $commission->name }}</td>
                                <td>{{ $commission->transactions_count }}</td>
                                <td>{{ money($commission->Total) }}</td>
                               
                                
                                
                                   
                          </tr>
                        
                      @endforeach
                      
                      </tbody>
                     
                    </table>
                    <div class="pagination-container">{!!$commissions->appends(['start' => request('start'), 'end' => request('end'), 'medic' => request('medic')])->render()!!}
                    </div>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
        </div>

    </section>


@endsection


