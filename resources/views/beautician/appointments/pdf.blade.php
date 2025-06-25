<table style="width:100%;font-size:12px;text-align:center;">

    <tr>
        <td>



        </td>
        <td style="text-align:center;">



            <img src="{{ $appointment->office ? env('APP_URL') . $appointment->office->logo_path : env('APP_URL') . '/img/logo.png' }}"
                alt="logo" style="height: 90px;">



        </td>
        <td>



        </td>
    </tr>
</table>
<hr style="height: 0; border:0; border-top: 1px solid #c3c3c3;" />
<table style="width:100%;font-size:12px;">
    <tr>
        <td>

            <div class="col-sm-4 invoice-col">
                <div class="invoice-number">
                    <b>Nro. Consulta:</b> {{ $appointment->id }}

                </div>
                <div class="invoice-date">
                    <b>Fecha:</b> {{ \Carbon\Carbon::parse($appointment->date)->toDateString() }}
                </div>
                <div class="invoice-date">
                    <b>Fecha Impresión:</b> {{ \Carbon\Carbon::now()->toDateString() }}
                </div>

            </div>


        </td>
        <td>



        </td>
        <td>
            <div class="col-sm-4 invoice-col" style="text-align:right;">
                @if ($appointment->office)
                    <strong>{{ $appointment->office->name }}</strong><br>


                    {{ $appointment->office->provinceName }}<br>
                    {{ $appointment->office->address }}<br>
                    <b>Tel:</b> {{ $appointment->office->phone }}<br>
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
<table style="width:100%; font-size:12px;">
    <tr>
        <td>

            <div class="col-xs-4 invoice-col invoice-left">
                <b>Paciente:</b> {{ $appointment->patient->first_name }}.
                {{ trans('utils.gender.' . $appointment->patient->gender) }}.<br>
                <b>Fecha Nacimiento:</b> {{ age($appointment->patient->birth_date) }}<br>

                <b>Fecha Consulta:</b> {{ $appointment->date }}<br>
            </div>

        </td>
        <td>

        </td>
        <td>

            <div class="col-xs-4 invoice-col invoice-right" style="text-align:right;">
                <b>Médico:</b> {{ auth()->user()->name }}<br>
                <b>Código de Médico:</b> {{ auth()->user()->medic_code }}<br>
                @foreach (auth()->user()->specialities as $speciality)
                    {{ $speciality->name }}
                @endforeach
            </div>
        </td>
    </tr>
</table>
<hr style="height: 0; border:0; border-top: 1px solid #c3c3c3;">
<table style="width:100%;font-size:12px;">
    <tr>
        <td colspan="2" style="text-align:center;">
            <strong>Historia Clínica del Paciente</strong>
        </td>
    </tr>
    <tr>
        <td>




            <h4 style="text-decoration:underline;"><strong>Evaluación Física</strong></h4>


            @foreach ($evaluations as $cat => $collections)
                <b>{{ $cat }}:</b>
                @foreach ($collections as $item)
                    <div>{{ $item->name }} {{ $item->zone }}</div>
                @endforeach
            @endforeach
            <b>Notas:</b>
            @foreach ($evaluationNotes as $cat => $collections)

                @foreach ($collections as $item)
                    <div><i>{{ $item->notes }}</i> </div>
                @endforeach

            @endforeach





        </td>
        <td>
            <h4 style="text-decoration:underline;"><strong>Antropometria</strong></h4>
            @if ($anthropometry && $anthropometry->items['height'])
                <b>Altura:</b><br>
                @foreach ($anthropometry->items['height'] as $index => $item)
                    @if ($item['value'])
                        <span class="margin">S{{ $index + 1 }}: {{ $item['value'] }}</span>
                    @endif
                @endforeach

            @endif
            @if ($anthropometry && $anthropometry->items['weight'])
                <br><b>Peso:</b><br>
                @foreach ($anthropometry->items['weight'] as $index => $item)
                    @if ($item['value'])
                        <span class="margin">S{{ $index + 1 }}: {{ $item['value'] }}</span>
                    @endif
                @endforeach

            @endif

            @if ($anthropometry && $anthropometry->items['mass'])
                <br><b>IMC:</b><br>
                @foreach ($anthropometry->items['mass'] as $index => $item)
                    @if ($item['value'])
                        <span class="margin">S{{ $index + 1 }}: {{ $item['value'] }}</span>
                    @endif
                @endforeach

            @endif

            @if ($anthropometry && $anthropometry->items['pa'])
                <br><b>P.A:</b><br>
                @foreach ($anthropometry->items['pa'] as $index => $item)
                    @if ($item['value'])
                        <span class="margin">S{{ $index + 1 }}: {{ $item['value'] }}</span>
                    @endif
                @endforeach

            @endif

            @if ($anthropometry && $anthropometry->items['abdomen_alto'])
                <br><b>Abdoment Alto:</b><br>
                @foreach ($anthropometry->items['abdomen_alto'] as $index => $item)
                    @if ($item['value'])
                        <span class="margin">S{{ $index + 1 }}: {{ $item['value'] }}</span>
                    @endif
                @endforeach

            @endif

            @if ($anthropometry && $anthropometry->items['ombligo'])
                <br><b>Ombligo:</b><br>
                @foreach ($anthropometry->items['ombligo'] as $index => $item)
                    @if ($item['value'])
                        <span class="margin">S{{ $index + 1 }}: {{ $item['value'] }}</span>
                    @endif
                @endforeach

            @endif

            @if ($anthropometry && $anthropometry->items['abdomen_bajo'])
                <br><b>Abdomen Bajo:</b><br>
                @foreach ($anthropometry->items['abdomen_bajo'] as $index => $item)
                    @if ($item['value'])
                        <span class="margin">S{{ $index + 1 }}: {{ $item['value'] }}</span>
                    @endif
                @endforeach

            @endif
            @if ($anthropometry && $anthropometry->items['abdomen_bajo_bajo'])
                <br><b>Abdomen Bajo Bajo:</b><br>
                @foreach ($anthropometry->items['abdomen_bajo_bajo'] as $index => $item)
                    @if ($item['value'])
                        <span class="margin">S{{ $index + 1 }}: {{ $item['value'] }}</span>
                    @endif
                @endforeach

            @endif

            @if ($anthropometry && $anthropometry->items['piernas_alta'])
                <br><b>Piernas Altas:</b><br>
                @foreach ($anthropometry->items['piernas_alta'] as $index => $item)
                    @if ($item['value'])
                        <span class="margin">S{{ $index + 1 }}: {{ $item['value'] }}</span>
                    @endif
                @endforeach

            @endif

            @if ($anthropometry && $anthropometry->items['piernas_baja'])
                <br><b>Piernas Bajas:</b><br>
                @foreach ($anthropometry->items['piernas_baja'] as $index => $item)
                    @if ($item['value'])
                        <span class="margin">S{{ $index + 1 }}: {{ $item['value'] }}</span>
                    @endif
                @endforeach

            @endif

            @if ($anthropometry && $anthropometry->items['brazos_alto'])
                <br><b>Brazos Alto:</b><br>
                @foreach ($anthropometry->items['brazos_alto'] as $index => $item)
                    @if ($item['value'])
                        <span class="margin">S{{ $index + 1 }}: {{ $item['value'] }}</span>
                    @endif
                @endforeach

            @endif

            @if ($anthropometry && $anthropometry->items['brazos_medio'])
                <br><b>Brazos Medio:</b><br>
                @foreach ($anthropometry->items['brazos_medio'] as $index => $item)
                    @if ($item['value'])
                        <span class="margin">S{{ $index + 1 }}: {{ $item['value'] }}</span>
                    @endif
                @endforeach

            @endif

            @if ($anthropometry && $anthropometry->items['brazos_bajo'])
                <br><b>Brazos Bajo:</b><br>
                @foreach ($anthropometry->items['brazos_bajo'] as $index => $item)
                    @if ($item['value'])
                        <span class="margin">S{{ $index + 1 }}: {{ $item['value'] }}</span>
                    @endif
                @endforeach

            @endif




        </td>

    </tr>

    <tr>
        <td>

            <h4 style="text-decoration:underline;"><strong>Tratamiento</strong></h4>


            @foreach ($treatments as $cat => $collections)
                <b>{{ $cat }}:</b>
                @foreach ($collections as $item)
                    <div>{{ $item->sessions }} x {{ $item->name }}</div>
                @endforeach
            @endforeach
            <b>Notas:</b>
            @foreach ($treatmentNotes as $cat => $collections)

                @foreach ($collections as $item)
                    <div><i>{{ $item->notes }}</i> </div>
                @endforeach

            @endforeach




        </td>
        <td>

            <h4 style="text-decoration:underline;"><strong>Recomendaciones</strong></h4>


            @foreach ($recomendations as $cat => $collections)
                <b>{{ $cat }}:</b>
                @foreach ($collections as $item)
                    <div>{{ $item->name }}</div>
                @endforeach

            @endforeach
            <b>Notas:</b>
            @foreach ($recomendationNotes as $cat => $collections)

                @foreach ($collections as $item)
                    <div><i>{{ $item->notes }}</i> </div>
                @endforeach

            @endforeach




        </td>
    </tr>

</table>
