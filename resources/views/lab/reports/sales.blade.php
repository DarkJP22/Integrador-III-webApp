@extends('layouts.laboratories.app')
@section('header') 
@endsection
@section('content')
    <section class="content-header">
      <h1>Reporte de ventas por dia</h1>
    
    </section>

    <section class="content">
         
        <div class="row">
          <div class="col-xs-12">
        
              
              <div class="box">

              <div class="box-header">
                     <div class="filters no-print">
                     
                         <form action="/lab/sales" method="GET" autocomplete="off">
                                <div class="row">
                                    <div class="col-sm-12">
                                    
                                    </div>
                                   
                                    
                                    <div class="col-sm-3 flatpickr">
                                           
                                        <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Hasta" value="{{ $search['start'] }}">
                                      
                                    
                                      
                                    </div>
                                    <div class="col-sm-3 flatpickr">
                                       
                                         <input data-input type="text" name="end" class="date form-control" placeholder="Fecha Hasta" value="{{ $search['end'] }}">
                                        
                                    </div>
                                    <div class="col-sm-3">
                                        <select name="CodigoActividad" id="CodigoActividad" class="form-control" required>
                                            <option value="">-- Filtro por Actividad Económica --</option>
                                            @foreach($activities as $activity)
                                            <option value="{{  $activity->codigo }}" {{ (isset($search) && $search['CodigoActividad'] == $activity->codigo) ? 'selected' : '' }}>{{  $activity->actividad }}</option>
                                            @endforeach
                                            
                                        </select>
                                    
                                    </div>
                                    <div class="col-sm-3">
                                       
                                        <div class="form-group">
                                          <button type="submit" class="btn btn-secondary">Generar</button>
                                          <a href="#" class="btn btn-secondary btn-sm" onclick="printSummary();"><i class="fa fa-print"></i></a>
                                          <!-- <button type="button" class="btn btn-secondary btn-print">Imprimir</button>
                                          <input type="hidden" name="print" value="0">   -->
                                        </div>
                                        
                                    </div>
                                  
                                    
                                </div>
                            </form>
                            
                    </div>
                    <div class="show-print">
                        <h3>Reporte de ventas entre {{ $search['start'] }}  al {{ $search['end'] }}</h3>
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
                            
                            <th scope="col">Fecha</th>
                            <th scope="col">Exonerado</th>
                            <th scope="col">Exento</th>
                            <th scope="col">Gravado</th>
                            {{-- <th scope="col">Clinica</th>
                            <th scope="col">Laboratorio</th> --}}
                            <th scope="col">IVA Devuelto</th>
                            <th scope="col">Total Ventas</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($sales as $sale)
                    
                        <tr>
                            
                            <td><small>{{ $sale->date }}</small></td>
                            <td>{{ money($sale->TotalExonerado, '') }} <small>CRC</small> <br> {{ money($sale->TotalExoneradoDolar, '') }} <small>USD</small></td>
                            <td>{{ money($sale->TotalExento, '') }} <small>CRC</small> <br> {{ money($sale->TotalExentoDolar, '') }} <small>USD</small></td>
                            <td>{{ money($sale->TotalGravado, '') }} <small>CRC</small> <br> {{ money($sale->TotalGravadoDolar, '') }} <small>USD</small></td>
                            {{-- <td>{{ money($sale->TotalClinica, '') }} <small>CRC</small> <br> {{ money($sale->TotalClinicaDolar, '') }} <small>USD</small></td>
                            <td>{{ money($sale->TotalLaboratorio, '') }} <small>CRC</small> <br> {{ money($sale->TotalLaboratorioDolar, '') }} <small>USD</small></td> --}}
                            <td>{{ money($sale->TotalIVADevuelto, '') }} <small>CRC</small> <br> {{ money($sale->TotalIVADevueltoDolar, '') }} <small>USD</small></td>
                            <td>{{ money($sale->TotalVentas, '') }} <small>CRC</small> <br> {{ money($sale->TotalVentasDolar, '') }} <small>USD</small></td>
                           
                          
                        </tr>
                    @endforeach
                        <tr>
                        
                            <td></td>
                            <td><b>{{ money($totales->TotalExonerado, '', 0) }} <small>CRC</small> <br> {{ money($totales->TotalExoneradoDolar, '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales->TotalExento, '', 0) }} <small>CRC</small> <br> {{ money($totales->TotalExentoDolar, '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales->TotalGravado, '', 0) }} <small>CRC</small> <br> {{ money($totales->TotalGravadoDolar, '') }} <small>USD</small></b></td>
                            {{-- <td><b>{{ money($totales->TotalClinica, '', 0) }} <small>CRC</small> <br> {{ money($totales->TotalClinicaDolar, '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales->TotalLaboratorio, '', 0) }} <small>CRC</small> <br> {{ money($totales->TotalLaboratorioDolar, '') }} <small>USD</small></b></td> --}}
                            <td><b>{{ money($totales->TotalIVADevuelto, '', 0) }} <small>CRC</small> <br> {{ money($totales->TotalIVADevueltoDolar, '') }} <small>USD</small></b></td>
                           
                            <td><b>{{ money($totales->TotalVentas, '', 0) }} <small>CRC</small> <br> {{ money($totales->TotalVentasDolar, '', 0) }} <small>USD</small></b></td>
                           
                          
                        </tr>
                   
                    
                    </tbody>    
                  </table>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
            <div class="row">
                <div class="col-md-6">
                        <h3>Reporte Ventas</h3>
                        <ul>
                            @php($TotalVentas = 0)
                            @php($TotalVentasUSD = 0)
                        @foreach($impuestosVentas['impuestosVentas'] as $impuestoVenta)
                            @php($TotalVentas += $impuestoVenta->CodigoMoneda == 'CRC' ? ($impuestoVenta->TotalVentas - $impuestoVenta->TotalIVADevuelto) : 0)
                            @php($TotalVentasUSD += $impuestoVenta->CodigoMoneda == 'USD' ? ($impuestoVenta->TotalVentas - $impuestoVenta->TotalIVADevuelto) : 0)
                            <li><b>{{ trans('utils.codigo_tarifa_name.'. $impuestoVenta->codTarifa) }} {{ $impuestoVenta->CodigoMoneda }}</b>
                                <ul>
                                    <li>
                                        Total Impuestos: {{ ($impuestoVenta->codTarifa != "00" && $impuestoVenta->codTarifa != "01") ? money($impuestoVenta->TotalImpuesto - $impuestoVenta->TotalIVADevuelto, '', 5) : money($impuestoVenta->TotalImpuesto, '', 5) }} <small>{{ $impuestoVenta->CodigoMoneda }}</small>
                                    </li> 
                                    <li>
                                    {{ $impuestoVenta->codTarifa != "00" ? 'Total Gravado' : 'Total Excento'}} ({{ money($impuestoVenta->TotalGravadoDesc, '', 5) }}): {{ ($impuestoVenta->codTarifa != "00" && $impuestoVenta->codTarifa != "01") ? money($impuestoVenta->TotalVentas - $impuestoVenta->TotalIVADevuelto, '', 5) : money($impuestoVenta->TotalVentas, '', 5) }} <small>{{ $impuestoVenta->CodigoMoneda }}</small>
                                   
                                    
                                    </li> 
                                    <li>
                                        IVA Devuelto: {{ money($impuestoVenta->TotalIVADevuelto, '', 5) }}
                                    </li>
                                    
                                    
                                </ul>
                                
                            </li>
                        @endforeach
                        <li>
                             <b>Total Ventas CRC: {{ money($TotalVentas, '', 5) }}</b><br>
                             <b>Total Ventas USD: {{ money($TotalVentasUSD, '', 5) }}</b>
                        </li>
                    
                    </ul>
                </div>
                <div class="col-md-6">
                      
                </div>
            </div>
          </div>
        </div>

    </section>


@endsection
@push('scripts')

<script>
   
  


    // $('.btn-print').on('click', function (e) {
             
    //          $('input[name="print"]').val(1);

    //          $(this).parents('form').submit();
    //      })
 function printSummary() {
          window.print();
      }
</script>
@endpush


