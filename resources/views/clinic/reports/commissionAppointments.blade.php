@extends('layouts.clinics.app')

@section('content')
    <section class="content-header">
      <h1>Comisión Doctor Blue (por cita atendida)</h1>
    
    </section>

    <section class="content">
         
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-header">
                    <form action="/clinic/commission/appointments" method="GET" autocomplete="off">
                      <div class="form-group">
                       
                          
                          <div class="col-sm-3 flatpickr">
                                           
                              
                              <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Desde" value="{{ $search['start'] }}">
                                    
                                
                            
                        
                            
                        </div>
                        <div class="col-sm-3 flatpickr">
                            
                                
                              <input data-input type="text" class="date form-control" placeholder="Fecha Hasta" name="end" value="{{ $search['end'] }}">
                                   
                            
                        </div>
                        <div class="col-sm-3">
                            <select name="medic" id="medic" class="form-control">
                                <option value="">-- Filtro por medico --</option>
                                @foreach($officeMedics as $medic)
                                <option value="{{  $medic->id }}" {{ (isset($search) && $search['medic'] == $medic->id) ? 'selected' : '' }}>{{  $medic->name }}</option>
                                @endforeach
                                
                            </select>
                        
                        </div>
                        <button type="submit" class="btn btn-primary">Generar</button>
                        
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
                        <th>Consultas Atendidas</th>
                        <th>Monto</th>
                        
                        
                        
                      </tr>
                      </thead>
                      <tbody>
                        @foreach($attended['medics'] as $medic)
                          <tr>
                                <td>{{ $medic['name'] }}</td>
                                <td>{{ $medic['attented'] }}</td>
                                <td>{{ money($medic['attented_amount']) }}</td>
                                
                                   
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


