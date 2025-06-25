@extends('layouts.clinics.app')

@section('content')
    <section class="content-header">
      <h1>Comisión Doctor Blue (por boleta de laboratorio atendida)</h1>
    
    </section>

    <section class="content">
         
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-header">
                    <form action="/clinic/commission/labs" method="GET" autocomplete="off">
                      <div class="form-group">
                       
                          
                          <div class="col-sm-3 flatpickr">
                                           
                              
                              <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Desde" value="{{ request('start') }}">
                                    
                                
                            
                        
                            
                        </div>
                        <div class="col-sm-3 flatpickr">
                            
                                
                              <input data-input type="text" class="date form-control" placeholder="Fecha Hasta" name="end" value="{{ request('end') }}">
                                   
                            
                        </div>
                        <div class="col-sm-3">
                            <select name="medic" id="medic" class="form-control">
                                <option value="">-- Filtro por medico --</option>
                                @foreach($officeMedics as $medic)
                                <option value="{{  $medic->id }}" {{ (request('medic') == $medic->id) ? 'selected' : '' }}>{{  $medic->name }}</option>
                                @endforeach
                                
                            </select>
                        
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
                        <th>Fecha</th>
                        <th>Médico</th>
                        <th>Boletas Generadas</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                        
                        
                        
                      </tr>
                      </thead>
                      <tbody>
                        @foreach($commissions as $commission)
                          <tr>
                                <td>{{ $commission->created_at }}</td>
                                <td>{{ $commission->user?->name }}</td>
                                <td>{{ $commission->transactions_count }}</td>
                                <td>{{ money($commission->Total) }}</td>
                                <td>
                                  
                                  <span 
                                  @class(['label', 'label-success' => $commission->paid_at, 'label-warning' => !$commission->paid_at])
                                  >
                                    {{ $commission->paid_at ? 'Pagado' : 'Pendiente' }} 
                                  </span>
                                 
                                </td>
                                <td>
                                  @if(!$commission->paid_at)
                                  <form action="/clinic/commissions/{{ $commission->id}}/upload-voucher" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="voucher">
                                    @if ($errors->has('voucher'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('voucher') }}</strong>
                                      </span>
                                    @endif
                                    <button type="submit" class="btn btn-secondary btn-sm">Subir Comprobante</button>
                                  </form>
                                  @else 
                                    <a href="{{ $commission->comprobante_url }}" target="_blank" download>Descargar Comprobante</a>
                                  @endif
                                 
                                </td>
                                
                                   
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


