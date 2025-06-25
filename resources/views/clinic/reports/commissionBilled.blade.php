@extends('layouts.clinics.app')

@section('content')
    <section class="content-header">
      <h1>Comisión Clínica (por cita facturada)</h1>
    
    </section>

    <section class="content">
         
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-header">
                    <form action="/clinic/commission/billed" method="GET" autocomplete="off">
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
                        <div class="col-sm-3">
                          <select name="CodigoActividad" id="CodigoActividad" class="form-control" required>
                              <option value="">-- Filtro por Actividad Económica --</option>
                              @foreach($activities as $activity)
                              <option value="{{  $activity->codigo }}" {{ (isset($search) && $search['CodigoActividad'] == $activity->codigo) ? 'selected' : '' }}>{{  $activity->actividad }}</option>
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
                @if($search['CodigoActividad'])
                  <h4>Codigo Actividad Económica {{ $search['CodigoActividad'] }}</h4>
                @endif
                 <table class="table no-margin">
                      <thead>
                      <tr>
                        <th>Médico</th>
                        <th>Facturas</th>
                        <th>Monto Facturado CRC</th>
                        <th>Monto Facturado USD</th>
                        <th>Comision</th>
                        <th>Monto Comision CRC</th>
                        <th>Monto Comision USD</th>
                        
                        
                        
                      </tr>
                      </thead>
                      <tbody>
                        @foreach($billed['medics'] as $medic)
                          <tr>
                                <td>{{ $medic['name'] }}</td>
                                <td>{{ $medic['billed']  }}</td>
                                <td>{{ money($medic['billed_amount'], '')  }} CRC</td>
                                <td>{{ money($medic['billed_amount_dolar'], '')  }} USD</td>
                                 <td>{{ $medic['commission'] }}%</td>
                                 <td>{{ money($medic['billed_commission_amount']) }} CRC</td>
                                 <td>{{ money($medic['billed_commission_amount_dolar']) }} USD</td>
                                
                                   
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


