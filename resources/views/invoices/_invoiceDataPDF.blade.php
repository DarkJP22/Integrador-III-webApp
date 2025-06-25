
 <table style="width:100%;font-size:12px;text-align:center;">
       	
		<tr>
			<td >
						
				
				
			</td>
			<td style="text-align:center;">
							
			
               
                    <img src="{{ ($invoice->clinic) ? $invoice->clinic->logo_path : config('app.url'). '/img/logo.png' }}" alt="logo" style="height: 90px;">
               
                    
               
				
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
             
                        <b>{{ $invoice->TipoDocumentoName }}</b>
                     
                
                    </div>
                     @if($invoice->NumeroConsecutivo)
                    <div> <b>Consecutivo:</b>{{ $invoice->NumeroConsecutivo }} </div>
                    @endif
                    @if($invoice->clave_fe)
                    <div> <b>Clave:</b>{{ $invoice->clave_fe }} </div>
                    @endif
                    <div> <span><b>Condicion venta:</b> {{ $invoice->CondicionVentaName }}</span>  </div>
                    <div> <span><b>Medio Pago:</b> {{ $invoice->MedioPagoName }}</span>  </div>
                
                
                    <div class="invoice-date">
                    <b>Fecha emisión:</b> {{ $invoice->created_at }}
                    </div>
				
				</div>
						
				
			</td>
			<td>
							
				
				
			</td>
			<td>
						
				<div class="col-sm-4 invoice-col" style="text-align:right;" >
                @if($invoice->clinic)    
                    @if($configFactura = $invoice->clinic->configFactura->first())  
                        <div>
                            <b>{{ $configFactura->nombre }}</b> <br>
                            <b>{{ $configFactura->nombre_comercial }}</b>
                        </div>
                        
                        <address>
                        
                        
                        {{ Illuminate\Support\Str::title($configFactura->otras_senas) }},  {{ $invoice->clinic->provinceName }}<br>
                        
                        <b>Tel:</b> {{ $configFactura->telefono }}<br>
                        
                        
                        @if($invoice->clinic->bill_to == 'C')
                            <b>Ced:</b> {{ $configFactura->identificacion }}<br>
                           
                        @else 
                            <b>Ced:</b> {{ $configFactura->identificacion }}<br>
                          
                        @endif
                        <b>Código Actividad:</b> {{ $configFactura->CodigoActividad }}

                        </address>
                    @else
                        <div>
                            <b>{{ $invoice->clinic->name }}</b>
                        </div>
                        
                        <address>
                        
                        
                        {{ $invoice->clinic->address }},  {{ $invoice->clinic->provinceName }}<br>
                        
                        <b>Tel:</b> {{ $invoice->clinic->phone }}<br>
                        
                        
                        @if($invoice->clinic->bill_to == 'C')
                            <b>Ced:</b> {{ $invoice->clinic->ide }}<br>
                            <b>Nombre:</b> {{ $invoice->clinic->ide_name }}
                        @else 
                            <b>Ced:</b> {{ $invoice->user->ide }}<br>
                            <b>Nombre:</b> {{ $invoice->user->name }}<br>
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
                  <b>Cliente:</b> {{ $invoice->cliente ? $invoice->cliente : 'No suministrado' }}<br>
             
              @if($invoice->identificacion_cliente)
                <b>Identificacion:</b> {{ $invoice->identificacion_cliente }}<br>
              @endif
              
              {{ $invoice->email }}<br>
						
				</div>
					
			</td>
			<td>
				
			</td>
			<td>
					
				<div class="col-xs-4 invoice-col invoice-right" style="text-align:right;">
						@if($invoice->customer)
                            {{ $invoice->customer->city }}<br>
                            {{ $invoice->customer->address }}<br>
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
        @foreach($invoice->lines as $index => $line)
        <tr>
            <th scope="row">{{ $index + 1 }}</th>
            <td>
                {{ $line->Detalle }}
                
            </td>
            <td class="text-right" style="text-align:right;">{{ money($line->PrecioUnitario,'') }} {{ $invoice->CodigoMoneda }}</td>
            <td class="text-right" style="text-align:right;">{{ $line->Cantidad }}</td>
            <td class="text-right" style="text-align:right;">{{ money($line->SubTotal,'') }} {{ $invoice->CodigoMoneda }}</td>
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
                  {{ $invoice->observations }}
                </div>
                 <div style="max-width: 300px">
                  @include('invoices._notaHacienda')
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
                            <td class="text-right" style="text-align:right;">{{ money($invoice->TotalVenta,'') }} {{ $invoice->CodigoMoneda }}</td>
                        </tr>
                        <tr>
                            <td>Descuentos</td>
                            <td class="pink text-right text-danger" style="text-align:right;    color: #dc3545 !important;">(-) {{ money($invoice->TotalDescuentos,'') }} {{ $invoice->CodigoMoneda }}</td>
                        </tr>
                        <tr>
                            <td>Impuestos (IVA)</td>
                            <td class="text-right" style="text-align:right;">{{ money($invoice->TotalImpuesto,'') }} {{ $invoice->CodigoMoneda }}</td>
                        </tr>
                        @if($invoice->MedioPago == '02')
                        <tr>
                            <td>IVA Devuelto:</td>
                            <td class="pink text-right text-danger">(-) {{ money($invoice->TotalIVADevuelto, '') }} {{ $invoice->CodigoMoneda }} </td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-bold-800">Total</td>
                            <td class="text-bold-800 text-right" style="text-align:right;"> {{ money($invoice->TotalComprobante,'') }} {{ $invoice->CodigoMoneda }}</td>
                        </tr>
                        <tr>
                            <td>Pago con</td>
                            <td class="pink text-right" style="text-align:right;">{{ money($invoice->pay_with,'') }} {{ $invoice->CodigoMoneda }}</td>
                        </tr>
                        <tr class="bg-grey bg-lighten-4">
                            <td class="text-bold-800">Cambio</td>
                            <td class="text-bold-800 text-right" style="text-align:right;">{{ money($invoice->change,'') }} {{ $invoice->CodigoMoneda }}</td>
                        </tr>
                        </tbody>
                    </table>


                    </div>
                    <br>
                    <b> Valor en letras:</b> <span class="uppercase" style="text-transform:uppercase;">{{ $TotalEnLetras }} {{ $invoice->CodigoMoneda }}</span><br>
                
                </div>

            </td>
        </tr>
    </tbody>
    </table>
</div>
 @if($invoice->referencias->count())
    <div class="row">
        <h2>Documentos de referencia</h2>
        <div class="col-xs-12 table-responsive" style="font-size:12px;">
        @include('invoices._referencias')
        </div>
    </div>
@endif
