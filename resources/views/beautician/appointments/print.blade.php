@extends('layouts.beauticians.app')
@section('header')

@endsection
@section('content')
    <section class="content">

        <!-- Main content -->
        <section class="invoice" style="font-size:12px;">
            <!-- title row -->
            <!-- info row -->
            <div class="row">
                <div class="col-sm-4 invoice-col" style="text-align:center;">

                </div>
                <div class="col-sm-4 invoice-col" style="text-align:center;">
                    <div class="logo">
                        <img src="{{ $appointment->office ? $appointment->office->logo_path : '/img/logo.png' }}"
                            alt="logo">
                    </div>
                </div>
                <div class="col-sm-4 invoice-col" style="text-align:center;">

                </div>
            </div>
            <hr>
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col" style="text-align:left;">

                    <div class="invoice-number">
                        <b>Nro. Consulta:</b> {{ $appointment->id }}

                    </div>
                    <div class="invoice-date">
                        <b>Fecha:</b>: {{ \Carbon\Carbon::parse($appointment->date)->toDateString() }}
                    </div>
                    <div class="invoice-date">
                        <b>Fecha impresión:</b>: {{ \Carbon\Carbon::now()->toDateString() }}
                    </div>



                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">

                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col" style="text-align:right;">
                    @if ($appointment->office)
                        <div>

                            <b>{{ $appointment->office->name }}</b>
                        </div>


                        {{ $appointment->office->address }}, {{ $appointment->office->provinceName }}<br>

                        <b>Tel:</b> {{ $appointment->office->phone }}<br>
                    @else
                        <div>
                            No se encontro el consultorio o clínica. Puede que halla sido eliminado.
                        </div>
                    @endif

                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            <hr>
            <div class="row invoice-patient">
                <div class="col-xs-4 invoice-col invoice-left">
                    <b>Paciente:</b> {{ $appointment->patient->first_name }}.
                    {{ trans('utils.gender.' . $appointment->patient->gender) }}.<br>
                    <b>Fecha Nacimiento:</b> {{ age($appointment->patient->birth_date) }}<br>

                    <b>Fecha Consulta:</b> {{ $appointment->date }}<br>
                </div>
                <div class="col-xs-4 invoice-col invoice-right">

                </div>
                <div class="col-xs-4 invoice-col invoice-right">
                    <b>Médico:</b> {{ auth()->user()->name }}<br>
                    <b>Código de Médico:</b> {{ auth()->user()->medic_code }}<br>
                    @foreach (auth()->user()->specialities as $speciality)
                        {{ $speciality->name }}
                    @endforeach
                </div>
            </div>
            <hr>

            <table style="width:100%;font-size:12px;">
                {{-- <tr>
          <td  colspan="2" style="text-align:center;">
            <strong>Historia Clínica del Paciente</strong>
          </td>
        </tr> --}}
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

            <div class="row">
                <!-- accepted payments column -->
                <div class="col-xs-6">
                    <p class="lead"></p>

                    <hr style="margin-top: 40px; margin-bottom: 0;">
                    <div style="text-align: center;">
                        <small style="text-transform: uppercase;">{{ auth()->user()->name }}</small>
                    </div>

                </div>
                <!-- /.col -->
                <div class="col-xs-6">
                    <!-- <img src="/img/logo.png" alt="{{ config('app.name', 'Laravel') }}" width="150" style="float: right;"> -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->


        </section>
        <!-- /.content -->



    </section>
@endsection
@push('scripts')
    <script>
        function printSummary() {
            window.print();
        }
        window.onload = printSummary;
    </script>
@endpush
