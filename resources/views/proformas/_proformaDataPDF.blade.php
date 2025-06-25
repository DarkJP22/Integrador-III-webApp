
 <table style="width:100%;font-size:12px;text-align:center;">
       	
		<tr>
			<td >
						
				
				
			</td>
			<td style="text-align:center;">
							
			
               
                    <img src="{{ ($proforma->clinic) ? $proforma->clinic->logo_path : config('app.url'). '/img/logo.png' }}" alt="logo" style="height: 90px;">
               
                    
               
				
			</td>
			<td>
					
						
				
			</td>
		</tr>
</table>
							
<hr style="height: 0; border:0; border-top: 1px solid #c3c3c3;" />
 <table style="width:100%;font-size:12px;">
       	
		<tr>
			<td >
						
				<div class="col-sm-4 invoice-col" style="text-align:left;"> 
					<div class="invoice-number" style="display:inline-block;">
             
                        <b>Proforma</b>
                     
                
                    </div>
                    
                    <div> <b>Consecutivo:</b>{{ $proforma->consecutivo }} </div>
                   
                    <div> <span><b>Condicion venta:</b> {{ $proforma->CondicionVentaName }}</span>  </div>
                    <div> <span><b>Medio Pago:</b> {{ $proforma->MedioPagoName }}</span>  </div>
                
                
                    <div class="invoice-date">
                    <b>Fecha emisión:</b> {{ $proforma->created_at }}
                    </div>
				
				</div>
						
				
			</td>
			<td>
							
				
				
			</td>
			<td>
						
				<div class="col-sm-4 invoice-col" style="text-align:right;" >
                @if($proforma->clinic)    
                    @if($configFactura = $proforma->clinic->configFactura->first())  
                        <div>
                            <b>{{ $configFactura->nombre }}</b> <br>
                            <b>{{ $configFactura->nombre_comercial }}</b>
                        </div>
                        
                        <address>
                        
                        
                        {{ Illuminate\Support\Str::title($configFactura->otras_senas) }},  {{ $proforma->clinic->provinceName }}<br>
                        
                        <b>Tel:</b> {{ $configFactura->telefono }}<br>
                        
                        
                        @if($proforma->clinic->bill_to == 'C')
                            <b>Ced:</b> {{ $configFactura->identificacion }}<br>
                           
                        @else 
                            <b>Ced:</b> {{ $configFactura->identificacion }}<br>
                          
                        @endif
                        <b>Código Actividad:</b> {{ $configFactura->CodigoActividad }}

                        </address>
                    @else
                        <div>
                            <b>{{ $proforma->clinic->name }}</b>
                        </div>
                        
                        <address>
                        
                        
                        {{ $proforma->clinic->address }},  {{ $proforma->clinic->provinceName }}<br>
                        
                        <b>Tel:</b> {{ $proforma->clinic->phone }}<br>
                        
                        
                        @if($proforma->clinic->bill_to == 'C')
                            <b>Ced:</b> {{ $proforma->clinic->ide }}<br>
                            <b>Nombre:</b> {{ $proforma->clinic->ide_name }}
                        @else 
                            <b>Ced:</b> {{ $proforma->user->ide }}<br>
                            <b>Nombre:</b> {{ $proforma->user->name }}<br>
                        @endif
                        

                        </address>

                    @endif
                @else
					<div>
						No se encontro el consultorio o clínica. Puede que halla sido eliminado.
					</div>
				@endif
					
				</div>
						
				
			</td>
		</tr>
</table>
		
	<hr style="height: 0; border:0; border-top: 1px solid #c3c3c3;" />
 <table style="width:100%;font-size:12px;">
		<tr>
			<td>
							
				<div class="col-xs-4 invoice-col invoice-left" >     
                  <b>Cliente:</b> {{ $proforma->cliente ? $proforma->cliente : 'No suministrado' }}<br>
             
              @if($proforma->identificacion_cliente)
                <b>Identificacion:</b> {{ $proforma->identificacion_cliente }}<br>
              @endif
              
              {{ $proforma->email }}<br>
						
				</div>
					
			</td>
			<td>
				
			</td>
			<td>
					
				<div class="col-xs-4 invoice-col invoice-right" style="text-align:right;">
						@if($proforma->customer)
                            {{ $proforma->customer->city }}<br>
                            {{ $proforma->customer->address }}<br>
                        @endif
				</div>
			</td>
		</tr>
</table>
<hr style="height: 0; border:0; border-top: 1px solid #c3c3c3;" />
<div class="table-responsive col-sm-12">
    <table class="table" style="width:100%;font-size:12px;">
    <thead>
        <tr>
        <th>#</th>
        <th>Item &amp; Descripción</th>
        <th class="text-right" style="text-align:right;">Precio Unitario</th>
        <th class="text-right" style="text-align:right;">Cantidad</th>
        <th class="text-right" style="text-align:right;">Total</th>
        <th class="text-right" style="text-align:right;">IVA</th>
        <th class="text-right" style="text-align:right;">Monto IVA</th>
        </tr>
    </thead>
    <tbody>
        @foreach($proforma->lines as $index => $line)
        <tr>
            <th scope="row">{{ $index + 1 }}</th>
            <td>
                {{ $line->Detalle }}
                
            </td>
            <td class="text-right" style="text-align:right;">{{ money($line->PrecioUnitario,'') }} {{ $proforma->CodigoMoneda }}</td>
            <td class="text-right" style="text-align:right;">{{ $line->Cantidad }}</td>
            <td class="text-right" style="text-align:right;">{{ money($line->SubTotal,'') }} {{ $proforma->CodigoMoneda }}</td>
            <td class="text-right" style="text-align:right;">
                @foreach($line->taxes as $tax)
                <div>{{ money($tax->tarifa,'', 0) }}%</div>
                @endforeach
            </td>
            <td class="text-right" style="text-align:right;">
                @foreach($line->taxes as $tax)
                <div>{{ money($tax->amount,'') }}</div>
                @endforeach
            </td>
        </tr>
        @endforeach
        <tr>
            <td colspan="7">
                <hr style="height: 0; border:0; border-top: 1px solid #c3c3c3;">
            </td>
        </tr>
        <tr>
            <td colspan="6" style="font-size:12px;">
                <div style="margin-bottom: 1rem;">
                 <h4>Notas</h4>
                  {{ $proforma->observations }}
                </div>
                 <div style="max-width: 300px">
                
                 </div>
                 
            </td>
            <td>
                <div class="col-md-5 col-sm-12">
                    <p class="lead">Totales</p>
                    <div class="table-responsive">
                    <table class="table" style="width:100%;">
                        <tbody>
                        <tr>
                            <td>Sub Total</td>
                            <td class="text-right" style="text-align:right;">{{ money($proforma->TotalVenta,'') }} {{ $proforma->CodigoMoneda }}</td>
                        </tr>
                        <tr>
                            <td>Descuentos</td>
                            <td class="pink text-right text-danger" style="text-align:right;    color: #dc3545 !important;">(-) {{ money($proforma->TotalDescuentos,'') }} {{ $proforma->CodigoMoneda }}</td>
                        </tr>
                        <tr>
                            <td>Impuestos (IVA)</td>
                            <td class="text-right" style="text-align:right;">{{ money($proforma->TotalImpuesto,'') }} {{ $proforma->CodigoMoneda }}</td>
                        </tr>
                        @if($proforma->MedioPago == '02')
                        <tr>
                            <td>IVA Devuelto:</td>
                            <td class="pink text-right text-danger">(-) {{ money($proforma->TotalIVADevuelto, '') }} {{ $proforma->CodigoMoneda }} </td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-bold-800">Total</td>
                            <td class="text-bold-800 text-right" style="text-align:right;"> {{ money($proforma->TotalComprobante,'') }} {{ $proforma->CodigoMoneda }}</td>
                        </tr>
                        <tr>
                            <td>Pago con</td>
                            <td class="pink text-right" style="text-align:right;">{{ money($proforma->pay_with,'') }} {{ $proforma->CodigoMoneda }}</td>
                        </tr>
                        <tr class="bg-grey bg-lighten-4">
                            <td class="text-bold-800">Cambio</td>
                            <td class="text-bold-800 text-right" style="text-align:right;">{{ money($proforma->change,'') }} {{ $proforma->CodigoMoneda }}</td>
                        </tr>
                        </tbody>
                    </table>


                    </div>
                    <br>
                    <b> Valor en letras:</b> <span class="uppercase" style="text-transform:uppercase;">{{ $TotalEnLetras }} {{ $proforma->CodigoMoneda }}</span><br>
                
                </div>

            </td>
        </tr>
    </tbody>
    </table>
</div>

