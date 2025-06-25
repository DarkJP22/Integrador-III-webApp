@extends('layouts.admins.app')

@section('content')
    <section class="content-header">
      <h1>Reporte Ingresos por Médicos</h1>
    
    </section>

    <section class="content">
         
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-header">
                    <form action="/admin/incomes" method="GET" autocomplete="off">
                      <div class="form-group">
                       
                          
                          <div class="col-sm-3 flatpickr">
                                           
                              
                              <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Desde" value="{{ $search['start'] }}">
                                    
                                
                            
                        
                            
                        </div>
                        <div class="col-sm-3 flatpickr">
                            
                                
                              <input data-input type="text" class="date form-control" placeholder="Fecha Hasta" name="end" value="{{ $search['end'] }}">
                                   
                            
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Generar</button>
                        
                      </div>
                   
                  </form>
              
                  
                  <div class="box-tools">
                  </div>
                  <h3>Medico por paquetes</h3>
              </div>
              <!-- /.box-header -->
              <div class="box-body table-responsive">
              
                <table class="table no-margin">
                    <thead>
                    <tr>
                      <th>Plan</th>
                      <th>Médicos</th>
                      <th>Costo de Plan</th>
                      <th>Total</th>
                      
                    </tr>
                    </thead>
                    <tbody>
                     @foreach($statistics['medicsPlans'] as $plan)
                    <tr>
                        <td>{{ $plan['title'] }}</td>
                        <td>{{ $plan['medics'] }}</td>
                        <td>{{ money($plan['cost']) }}</td>
                        <td>
                          {{  money($plan['total']) }}
                        </td>
                        
                    </tr>
                    @endforeach
                    
                    
                    </tbody>
                  </table>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->

            <div class="box">
              <div class="box-header">
                  <h3>Medico por consultas</h3>
              </div>
              <!-- /.box-header -->
              <div class="box-body table-responsive">
                 <table class="table no-margin">
                      <thead>
                      <tr>
                        <th>Médico</th>
                        <th>Consultas Atendidas (Pacientes)</th>
                        <th>Monto</th>
                        <th>Consultas pedientes</th>
                        <th>Monto</th>
                        
                        
                        
                      </tr>
                      </thead>
                      <tbody>
                        @foreach($statistics['individualByAppointmentAttended']['medics'] as $medic)
                          <tr>
                                <td>{{ $medic['name'] }}</td>
                                <td>{{ $medic['attented'] }}</td>
                                <td>{{ money($medic['attented_amount']) }}</td>
                                <td>{{ $medic['pending'] }}</td>
                                <td>{{ money($medic['pending_amount']) }}</td>
                                
                                   
                          </tr>
                        
                      @endforeach
                      
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


