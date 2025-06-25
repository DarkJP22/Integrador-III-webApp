<!DOCTYPE html>
<html>
<head>
    <title>User Appointments</title>
</head>
<body>

@foreach($user->patients as $patient)
    <h1>Información Personal</h1>
    <p>Identificación: {{ $patient->ide }}</p>
    <p>Nombre: {{ $patient->first_name }}</p>
    <p>Email: {{ $patient->email }}</p>

    <h2>Control de signos</h2>
    <h3>Presión Arterial</h3>
    @foreach($patient->pressures as $pressure)
        <p>Fecha: {{ $pressure->date_control }} {{ $pressure->time_control }}</p>
        <p>Sistólica: {{ $pressure->ps }}</p>
        <p>Diastólica: {{ $pressure->pd }}</p>
        <p>Pulso: {{ $pressure->heart_rate }}</p>
        <p>Lugar de toma : {{ $pressure->measurement_place }}</p>
        <p>Observaciones : {{ $pressure->observations }}</p>
        <hr>
    @endforeach
    <h3>Glicemia</h3>
    @foreach($patient->sugars as $sugar)
        <p>Fecha: {{ $sugar->date_control }} {{ $sugar->time_control }}</p>
        <p>Glicemia: {{ $sugar->glicemia }}</p>
        <p>Condición: {{ $pressure->condition }}</p>
        <hr>
    @endforeach
    <h2><strong>Medicamentos Activos</strong></h2>

        @foreach($patient->medicines as $item)
            <span>{{ $item->name }}</span><br>
        @endforeach
    <hr>
    <h2><strong>Alergias Medicamentos</strong></h2>

    @foreach($patient->allergies as $allergy)
        <span>{{ $allergy->name }}</span><br>
    @endforeach
    @foreach($patient->labresults as $labresult))
        <span>{{ $labresult->file_path }}</span><br>
    @endforeach
    <hr>
    <h2>Historial de Consultas</h2>
    @foreach($patient->appointments as $appointment)
        <hr style="height: 0; border:0; border-top: 1px solid #c3c3c3;"/>
        <table style="width:100%; font-size:12px;">
            <tr>
                <td>
                    <div class="col-xs-4 invoice-col invoice-left" style="text-align:left;">
                        <b>Fecha Consulta:</b> {{ $appointment->date?->toDateString() }}<br>
                        <b>Médico:</b> {{ $appointment->user?->name ?? $appointment->medic_name }}<br>
                        @if($appointment->user)
                            <b>Código de Médico:</b> {{ $appointment->user->medic_code }}<br>
                            @foreach( $appointment->user->specialities as $speciality)
                                {{ $speciality->name }}
                            @endforeach
                        @endif

                    </div>


                </td>
                <td>

                </td>
                <td>
                    <div class="col-xs-4 invoice-col invoice-right" style="text-align:right;">
                        @if($appointment->office)
                            <strong>{{ $appointment->office->name }}</strong><br>


                            {{ $appointment->office->provinceName }}<br>
                            {{ $appointment->office->address }}<br>
                            <b>Tel:</b> {{ $appointment->office->phone }}<br>

                        @endif
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
                <td colspan="2">

                    <h4 style="text-decoration:underline;"><strong>Notas de padecimiento</strong></h4>

                    @if($appointment->diseaseNotes->reason)
                        <span><strong>Razón de la visita: </strong>{{ $appointment->diseaseNotes->reason }}</span><br><br>
                    @endif
                    @if($appointment->diseaseNotes->symptoms)
                        <span><strong>Síntomas subjetivos: </strong>{{ $appointment->diseaseNotes->symptoms }}</span><br><br>
                    @endif
                    @if($appointment->diseaseNotes->phisical_review)
                        <span><strong>Exploración Física: </strong>{{ $appointment->diseaseNotes->phisical_review }}</span><br><br>
                    @endif
                    @if($appointment->diseaseNotes->revalorizacion)
                        <span><strong>Revalorización: </strong>{{ $appointment->diseaseNotes->revalorizacion }}</span><br><br>
                    @endif


                    <h4 style="text-decoration:underline;"><strong>Examen Físico</strong></h4>

                    @if($appointment->physicalExams->cardio)
                        <span><strong>Cardiaco y Vascular: </strong>{{ $appointment->physicalExams->cardio }} </span><br><br>
                    @endif
                    @if($appointment->physicalExams->linfatico)
                        <span><strong>Sistema Linfático: </strong>{{ $appointment->physicalExams->linfatico }} </span><br><br>
                    @endif
                    @if($appointment->physicalExams->osteoarticular)
                        <span><strong>Osteoarticular: </strong>{{ $appointment->physicalExams->osteoarticular }}</span><br><br>
                    @endif
                    @if($appointment->physicalExams->psiquiatrico)
                        <span><strong>Psiquiátrico y Psicológico: </strong>{{ $appointment->physicalExams->psiquiatrico }} </span><br><br>
                    @endif
                    @if($appointment->physicalExams->digestivo)
                        <span><strong>Aparato Digestivo: </strong>{{ $appointment->physicalExams->digestivo }} </span><br><br>
                    @endif
                    @if($appointment->physicalExams->dermatologico)
                        <span><strong>Dermatológico: </strong>{{ $appointment->physicalExams->dermatologico }} </span><br><br>
                    @endif
                    @if($appointment->physicalExams->otorrinolaringologico)
                        <span><strong>Otorrinolaringológico: </strong>{{ $appointment->physicalExams->otorrinolaringologico }} </span><br><br>
                    @endif
                    @if($appointment->physicalExams->reproductor)
                        <span><strong>Aparato Reproductor: </strong>{{ $appointment->physicalExams->reproductor }} </span><br><br>
                    @endif
                    @if($appointment->physicalExams->urinario)
                        <span><strong>Aparato Urinario: </strong>{{ $appointment->physicalExams->urinario }} </span><br><br>
                    @endif
                    @if($appointment->physicalExams->neurologico)
                        <span><strong>Neurológico: </strong>{{ $appointment->physicalExams->neurologico }}</span><br><br>
                    @endif
                    @if($appointment->physicalExams->pulmonar)
                        <span><strong>Pulmonar o Respiratorio: </strong>{{ $appointment->physicalExams->pulmonar }}</span><br>
                    @endif


                </td>

            </tr>
            <tr>
                <td colspan="2">
                    <h4 style="text-decoration:underline;"><strong>Exámenes Laboratorio</strong></h4>
                    @foreach($appointment->labexams as $item)
                        <span>{{ $item->name }}</span><br>
                    @endforeach
                    <h4 style="text-decoration:underline;"><strong>Diagnóstico</strong></h4>

                    @foreach($appointment->diagnostics as $item)
                        <span>{{ $item->name }}</span><br>
                    @endforeach


                    <h4 style="text-decoration:underline;"><strong>Tratamiento</strong></h4>

                    @foreach($appointment->treatments as $item)

                        <span><strong>{{ $item->name }}:</strong>
								{{ $item->comments }}</span><br>

                    @endforeach
                    @if($appointment->medical_instructions)
                        <span><strong>Recomendaciones  Médicas: </strong>{{ $appointment->medical_instructions }} </span>
                    @endif


                </td>
            </tr>


        </table>
    @endforeach
    <hr>
@endforeach

</body>
</html>