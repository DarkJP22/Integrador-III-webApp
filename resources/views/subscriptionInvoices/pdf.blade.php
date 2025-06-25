<div class="content">


    <table style="width:100%;font-size:12px;text-align:center;">

        <tr>
            <td>


            </td>
            <td style="text-align:center;">


                <img src="{{ config('app.url'). '/img/logo.png' }}" alt="logo" style="height: 90px;">


            </td>
            <td>


            </td>
        </tr>
    </table>

    <hr style="height: 0; border:0; border-top: 1px solid #c3c3c3;"/>
    <table style="width:100%;font-size:12px;">

        <tr>
            <td>

                <div class="col-sm-4 invoice-col" style="text-align:left;">
                    <div class="invoice-number" style="display:inline-block;">

                        <b>Factura de Subscripción</b>


                    </div>

                    <div><b>Consecutivo:</b>{{ $invoice->invoice_number }} </div>

                    <div class="invoice-date">
                        <b>Fecha emisión:</b> {{ $invoice->invoice_date }}
                    </div>

                </div>


            </td>
            <td>


            </td>
            <td>

                <div class="col-sm-4 invoice-col" style="text-align:right;">


                </div>


            </td>
        </tr>
    </table>

    <hr style="height: 0; border:0; border-top: 1px solid #c3c3c3;"/>
    <table style="width:100%;font-size:12px;">
        <tr>
            <td>

                <div class="col-xs-4 invoice-col invoice-left">
                    <b>Cliente:</b> {{ $invoice->customer ? $invoice->customer->name : 'No suministrado' }}<br>

                    @if($invoice->customer?->ide)
                        <b>Identificacion:</b> {{ $invoice->customer?->ide }}<br>
                    @endif

                    {{ $invoice->email }}<br>

                </div>

            </td>
            <td>

            </td>
            <td>

                <div class="col-xs-4 invoice-col invoice-right" style="text-align:right;">

                </div>
            </td>
        </tr>
    </table>
    <hr style="height: 0; border:0; border-top: 1px solid #c3c3c3;"/>
    <div class="table-responsive col-sm-12">
        <table class="table" style="width:100%;font-size:12px;">
            <thead>
            <tr>
                <th>#</th>
                <th>Item &amp; Descripción</th>
                <th class="text-right" style="text-align:right;">Precio Unitario</th>
                <th class="text-right" style="text-align:right;">Cantidad</th>
                <th class="text-right" style="text-align:right;">Total</th>

            </tr>
            </thead>
            <tbody>
            @foreach($invoice->items as $index => $line)
                <tr>
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>
                        {{ $line->name }}

                    </td>
                    <td class="text-right"
                        style="text-align:right;">{{ money($line->price,'') }} {{ $invoice->currency?->code }}</td>
                    <td class="text-right" style="text-align:right;">{{ $line->quantity }}</td>
                    <td class="text-right"
                        style="text-align:right;">{{ money($line->total,'') }} {{ $invoice->currency?->code }}</td>


                </tr>
            @endforeach
            <tr>
                <td colspan="5">
                    <hr style="height: 0; border:0; border-top: 1px solid #c3c3c3;">
                </td>
            </tr>
            <tr>
                <td colspan="4" style="font-size:12px;">
                    <div style="margin-bottom: 1rem;">
                        <h4>Notas</h4>
                        {{ $invoice->notes }}
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
                                    <td class="text-right"
                                        style="text-align:right;">{{ money($invoice->sub_total,'') }} {{ $invoice->currency?->code }}</td>
                                </tr>
                                <tr>
                                    <td>Descuentos</td>
                                    <td class="pink text-right text-danger"
                                        style="text-align:right;    color: #dc3545 !important;">
                                        (-) {{ money($invoice->discount_val,'') }} {{ $invoice->currency?->code }}</td>
                                </tr>


                                <tr>
                                    <td class="text-bold-800">Total</td>
                                    <td class="text-bold-800 text-right"
                                        style="text-align:right;"> {{ money($invoice->total,'') }} {{ $invoice->currency?->code }}</td>
                                </tr>


                                </tbody>
                            </table>


                        </div>


                    </div>

                </td>
            </tr>
            </tbody>
        </table>
    </div>


</div>

