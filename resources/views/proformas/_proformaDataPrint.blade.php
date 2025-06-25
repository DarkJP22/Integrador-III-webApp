<section class="invoice" style="font-size:12px;">
        
       <div class="row">
           <div class="col-sm-4 invoice-col" style="text-align:center;">
           
            </div>
            <div class="col-sm-4 invoice-col" style="text-align:center;">
            <div class="logo">
                <img src="{{ ($proforma->clinic) ? $proforma->clinic->logo_path : '/img/logo.png' }}" alt="logo">
            </div>
            </div>
            <div class="col-sm-4 invoice-col" style="text-align:center;">
            
            </div>
       </div>
        <hr>
        <div class="row invoice-info">
        <div class="col-sm-4 invoice-col" style="text-align:left;">
            <div class="invoice-number">
             
              <b>Proforma</b> 
            
              
            </div>
            
              <div> <b>Consecutivo:</b>{{ $proforma->consecutivo }} </div>
             

             
             <div> <span><b>Condicion venta:</b> {{ $proforma->CondicionVentaName }}</span>  </div>
             <div> <span><b>Medio Pago:</b> {{ $proforma->MedioPagoName }}</span>  </div>
         
         
          <div class="invoice-date">
          <b>Fecha emisión:</b> {{ $proforma->created_at }}
          </div>
          
        

          
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          
          
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col" style="text-align:right;">
          
            
          <div class="info-clinic">
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
          
          
          
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      <hr>
      <div class="row invoice-patient">
        

           <div class="col-xs-4 invoice-col invoice-left">
             
              <b>Cliente:</b> {{ $proforma->cliente ? $proforma->cliente : 'No suministrado' }}<br>
             
              @if($proforma->identificacion_cliente)
                <b>Identificacion:</b> {{ $proforma->identificacion_cliente }}<br>
              @endif
              
              {{ $proforma->email }}
            
          </div>
          <div class="col-xs-4 invoice-col invoice-right">
              
          </div>
          <div class="col-xs-4 invoice-col invoice-right">
            @if($proforma->customer)
                {{ $proforma->customer->city }}<br>
                {{ $proforma->customer->address }}<br>
            @endif
          </div>

       
      </div>
      <hr>

      <!-- Table row -->
      <div class="row">
        <div class="table-responsive col-sm-12">
            <table class="table">
            <thead>
                <tr>
                <th>#</th>
                <th>Item &amp; Descripción</th>
                <th class="text-right">Precio Unitario</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Total</th>
                <th class="text-right">IVA</th>
                <th class="text-right">Monto IVA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proforma->lines as $index => $line)
                <tr>
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>
                        <p>{{ $line->Detalle }}</p>
                        
                    </td>
                    <td class="text-right">{{ money($line->PrecioUnitario, '') }}</td>
                    <td class="text-right">{{ $line->Cantidad }}</td>
                    <td class="text-right">{{ money($line->SubTotal, '') }}</td>
                    <td class="text-right">
                        @foreach($line->taxes as $tax)
                        <div>{{ money($tax->tarifa,'', 0) }}%</div>
                        @endforeach
                    </td>
                    <td class="text-right">
                        @foreach($line->taxes as $tax)
                        <div>{{ money($tax->amount,'') }}</div>
                        @endforeach
                    </td>
                </tr>
                @endforeach
            
            </tbody>
            </table>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
 <hr>
      <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-6" style="">
            <div style="margin-bottom: 1rem;">
                <h4>Notas</h4>
                  {{ $proforma->observations }}
            </div>
           <div style="max-width: 300px">
                
            </div>
            
        </div>
        <!-- /.col -->
        <div class="col-xs-6">
          

          <div class="table-responsive">
                <table class="table" style="">
                    <tbody>
                    <tr>
                        <td>Sub Total</td>
                        <td class="text-right">{{ money($proforma->TotalVenta,'') }} {{ $proforma->CodigoMoneda }}</td>
                    </tr>
                    <tr>
                        <td>Descuentos</td>
                        <td class="pink text-right text-danger">(-) {{ money($proforma->TotalDescuentos,'') }} {{ $proforma->CodigoMoneda }}</td>
                    </tr>
                    <tr>
                        <td>Impuestos (IVA)</td>
                        <td class="text-right">{{ money($proforma->TotalImpuesto,'') }} {{ $proforma->CodigoMoneda }}</td>
                    </tr>
                    @if($proforma->MedioPago == '02')
                    <tr>
                        <td>IVA Devuelto:</td>
                        <td class="pink text-right text-danger">(-) {{ money($proforma->TotalIVADevuelto) }} </td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-bold-800">Total</td>
                        <td class="text-bold-800 text-right"> {{ money($proforma->TotalComprobante,'') }} {{ $proforma->CodigoMoneda }}</td>
                    </tr>
                    <tr>
                        <td>Pago con</td>
                        <td class="pink text-right">{{ money($proforma->pay_with,'') }} {{ $proforma->CodigoMoneda }}</td>
                    </tr>
                    <tr class="bg-grey bg-lighten-4">
                        <td class="text-bold-800">Cambio</td>
                        <td class="text-bold-800 text-right">{{ money($proforma->change,'') }} {{ $proforma->CodigoMoneda }}</td>
                    </tr>
                    </tbody>
                </table>
                <b> Valor en letras:</b> <span class="uppercase" style="text-transform:uppercase;">{{ $TotalEnLetras }} {{ $proforma->CodigoMoneda }}</span><br>
                </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    

      <div class="row no-print">
        <div class="col-xs-12">
          <a href="#" target="_blank" class="btn btn-secondary" onclick="printSummary();"><i class="fa fa-print"></i> Imprimir</a>
          

           <a href="/proformas" class="btn btn-primary pull-right"><i class="fa fa-credit-card"></i> Regresar a facturación
          </a>
         
         
         
        </div>
      </div>

      
</section>