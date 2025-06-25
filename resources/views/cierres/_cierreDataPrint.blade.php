

    <section class="invoice" style="font-size:12px;">
         
        <div class="row">
          <div class="col-xs-12">
               <h3>Cierre del dia por médico</h3>
               <h4>Codigo de Actividad Económica: {{ $codigoActividad }}</h4>
              <!-- /.box-header -->
              <div class="box-body table-responsive">
                 <table class="table no-margin" style="font-size:12px;">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Desde</th>
                            <th scope="col">Hasta</th>
                            <th scope="col">Médico / Admin</th>
                            <!-- <th scope="col">Credito</th>
                            <th scope="col">Contado</th>
                            <th scope="col">Efectivo</th>
                            <th scope="col">Tarjeta</th> -->
                            <th scope="col">IVA Dev.</th>
                            <th scope="col">Clínica</th>
                            <th scope="col">Lab</th>
                            <th scope="col">Gravado</th>
                            <th scope="col">Exento</th>
                            <th scope="col">Exonerado</th>
                            <th scope="col">Descuento</th>
                            <th scope="col">Impuesto</th>
                            <th scope="col">Ventas</th>
                            <!-- <th scope="col">Generado</th> -->
                           
                            
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($cierres as $cierre)
                    
                        <tr>
                            <th scope="row">{{ $cierre->id }}</th>
                            <td>{{ $cierre->from }}</td>
                            <td>{{ $cierre->to }}</td>
                            <td>{{ Optional($cierre->user)->name }}</td>
                            <!-- <td>{{ money($cierre->TotalCredito, '') }} CRC <br> {{ money($cierre->TotalCreditoDolar, '') }} USD</td>
                            <td>{{ money($cierre->TotalContado, '') }} CRC <br> {{ money($cierre->TotalContadoDolar, '') }} USD</td>
                            <td>{{ money($cierre->TotalEfectivo, '') }} CRC <br> {{ money($cierre->TotalEfectivoDolar, '') }} USD</td>
                            <td>{{ money($cierre->TotalTarjeta, '') }} CRC <br> {{ money($cierre->TotalTarjetaDolar, '') }} USD</td> -->
                            <td>{{ money($cierre->TotalIVADevuelto, '') }} CRC <br> {{ money($cierre->TotalIVADevueltoDolar, '') }} USD</td>
                            <td>{{ money($cierre->TotalClinica, '') }} <small>CRC</small> <br> {{ money($cierre->TotalClinicaDolar, '') }} <small>USD</small></td>
                            <td>{{ money($cierre->TotalLaboratorio, '') }} <small>CRC</small> <br> {{ money($cierre->TotalLaboratorioDolar, '') }} <small>USD</small></td>
                            <td>{{ money($cierre->TotalGravado, '') }} CRC <br> {{ money($cierre->TotalGravadoDolar, '') }} USD</td>
                            <td>{{ money($cierre->TotalExento, '') }} CRC <br> {{ money($cierre->TotalExentoDolar, '') }} USD</td>
                            <td>{{ money($cierre->TotalExonerado, '') }} CRC <br> {{ money($cierre->TotalExoneradoDolar, '') }} USD</td>
                            <td>{{ money($cierre->TotalDescuento, '') }} CRC <br> {{ money($cierre->TotalDescuentoDolar, '') }} USD</td>
                            <td>{{ money($cierre->TotalImpuesto, '') }} CRC <br> {{ money($cierre->TotalImpuestoDolar, '') }} USD</td>
                            <td>{{ money($cierre->TotalVentas, '') }} CRC <br> {{ money($cierre->TotalVentasDolar, '') }} USD</td>
                            <!-- <td>{{ $cierre->created_at }}</td> -->
                            
                          
                        </tr>
                    @endforeach
                       
                   
                    
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Desde</th>
                            <th scope="col">Hasta</th>
                            <th scope="col">Médico / Admin</th>
                            <!-- <th scope="col">Credito</th>
                            <th scope="col">Contado</th>
                            <th scope="col">Efectivo</th>
                            <th scope="col">Tarjeta</th> -->
                            <th scope="col">IVA Dev.</th>
                            <th scope="col">Clínica</th>
                            <th scope="col">Lab</th>
                            <th scope="col">Gravado</th>
                            <th scope="col">Exento</th>
                            <th scope="col">Exonerado</th>
                            <th scope="col">Descuento</th>
                            <th scope="col">Impuesto</th>
                            <th scope="col">Ventas</th>
                            <!-- <th scope="col">Generado</th> -->
                           
                            
                        </tr>
                        <tr>
                            <th scope="row"></th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <!-- <td><b>{{ money($totales['TotalCredito'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalCreditoDolar'], '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales['TotalContado'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalContadoDolar'], '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales['TotalEfectivo'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalEfectivoDolar'], '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales['TotalTarjeta'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalTarjetaDolar'], '') }} <small>USD</small></b></td> -->
                            <td><b>{{ money($totales['TotalIVADevuelto'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalIVADevueltoDolar'], '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales['TotalClinica'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalClinicaDolar'], '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales['TotalLaboratorio'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalLaboratorioDolar'], '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales['TotalGravado'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalGravadoDolar'], '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales['TotalExento'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalExentoDolar'], '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales['TotalExonerado'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalExoneradoDolar'], '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales['TotalDescuento'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalDescuentoDolar'], '') }} <small>USD</small></b></td>
                            <td><b>{{ money($totales['TotalImpuesto'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalImpuestoDolar'], '') }} <small>USD</small></b></td>
                            
                            <td><b>{{ money($totales['TotalVentas'], '', 0) }} <small>CRC</small> <br> {{ money($totales['TotalVentasDolar'], '', 0) }} <small>USD</small></b></td>
                            <!-- <td></td> -->
                            
                            
                        </tr>
                        @if (count($cierres))
                            <!-- <td  colspan="13" class="pagination-container">{-- $cierres->render() --}</td> -->
                        @endif

                    </tfoot>    
                  </table>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
          <div class="row">
                <div class="col-md-6">
                        <h3>Reporte Ventas</h3>
                        <ul>
                            @php($TotalVentas = 0)
                            @php($TotalVentasUSD = 0)
                        @foreach($impuestosVentasCompras['impuestosVentas'] as $impuestoVenta)
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
        

    </section>
