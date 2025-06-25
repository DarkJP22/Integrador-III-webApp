@extends('layouts.medics.app')

@section('content')
<div class="content">
   
        <div class="row no-print">
            <div class="col-md-12">
                 <button type="button" class="btn btn-default my-1" onclick="printSummary();"><i class="fa fa-paper-plane-o" ></i> Imprimir</button>
                <a href="/reports/invoices" class="btn btn-primary" role="button">Regresar</a>
            </div>
        </div>
        <div class="box-body table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($invoices as $invoice)
                          <tr>
                            <td>{{ $invoice->NumeroConsecutivo ? $invoice->NumeroConsecutivo : $invoice->consecutivo }}</td>
                      
                             <td>
                                {{ $invoice->created_at }}
                            </td>
                           
                            <td data-title="Cliente">
                                {{ $invoice->cliente }}
                            </td>
                           
                            <td data-title="Total">{{ money($invoice->TotalComprobante, '') }} {{ $invoice->CodigoMoneda }}</td>
                          </tr>
                      @endforeach
                        
                        <tr>
                          <td colspan="3"></td>
                          <td>
                            <b>Total CRC: {{ money($totalVentasCRC,'')  }} </b><br>
                            <b>Total USD: {{ money($totalVentasUSD,'') }}</b><br>
                          </td>
                        </tr>
                        
                       
                        

                  </tbody>
                 
                </table>
               
               
              </div>
       
        
       
    

</div>
@endsection
@push('scripts')
<script>
    function printSummary() {
            window.print();
        }
        
        window.onload = printSummary;
</script>
@endpush