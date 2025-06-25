<h2>Historial de facturas</h2>
<div class="row">
    <div class="col-xs-12 table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
        <th>Numero</th>
        <th>Description</th>
        <th>Total</th>
        <th>Estado</th>
        <th>Comprobante</th>
        </tr>
        </thead>
        <tbody>
        @foreach($subscriptionInvoices as $invoice)
        <tr>
            <td><a href="#" data-toggle="modal"
                   data-target="#modalSubscriptionInvoiceDetail"
                   class="btn btn-primary"
                   title="Detalle"><i
                            class="fa fa-list"></i>
                </a></td>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->notes }}</td>
            <td>{{ money($invoice->total, $invoice->currency->symbol ?? getDefaultCurrency()->symbol) }}</td>
            <td>
                <span @class([
                            'label',
                            'label-success' => $invoice->paid_status === \App\Enums\SubscriptionInvoicePaidStatus::PAID,
                            'label-danger' => $invoice->paid_status === \App\Enums\SubscriptionInvoicePaidStatus::UNPAID,
                        ])>

                {{ $invoice->paid_status->label() }}
                </span>
            </td>
            <td>
                @if(!$invoice->comprobante)
                    <form action="/medic/subscription/invoices/{{ $invoice->id}}/upload-voucher" method="POST" enctype="multipart/form-data">
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
                    <a href="{{ $invoice->comprobante_url }}" target="_blank" download>Descargar Comprobante</a>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->