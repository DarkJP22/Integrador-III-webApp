

 <table style="width:100%;font-size:12px;text-align:center;">
       	
		<tr>
			<td >
						
				
				
			</td>
			<td style="text-align:center;">
							
			
               
                <img src="{{ ($appointment->office) ? config('app.url').$appointment->office->logo_path : config('app.url'). '/img/logo.png' }}" alt="logo" style="height: 90px;">
                
                    
				
			</td>
			<td>
					
						
				
			</td>
		</tr>
</table>
<hr style="height: 0; border:0; border-top: 1px solid #c3c3c3;" />
 <table style="width:100%;font-size:12px;">
		<tr>
			<td>
						
				<div class="col-sm-4 invoice-col" > 
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
				 @if($appointment->office)  
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
							
				<div class="col-xs-4 invoice-col invoice-left" >     
						<b>Paciente:</b> {{ $appointment->patient_id }}. {{ trans('utils.gender.'.$appointment->patient->gender) }}.<br>
						<b>Fecha Nacimiento:</b> {{ age($appointment->patient->birth_date) }}<br>
						
						<b>Fecha Consulta:</b> {{ $appointment->date }}<br>
				</div>
					
			</td>
			<td>
				
			</td>
			<td>
					
				<div class="col-xs-4 invoice-col invoice-right" style="text-align:right;">
					<b>Médico:</b> {{ $appointment->user?->name ?? $appointment->medic_name }}<br>
					@if($appointment->user)
						<b>Código de Médico:</b> {{ $appointment->user->medic_code }}<br>
					@foreach( $appointment->user->specialities as $speciality)
						{{ $speciality->name }} 
					@endforeach
					@endif
					
				</div>
			</td>
		</tr>
</table>
<hr style="height: 0; border:0; border-top: 1px solid #c3c3c3;">
<table style="width:100%;font-size:12px;">
		<tr>
			<td  colspan="2" style="text-align:center;">
				<strong>Historia Clínica del Paciente</strong>
			</td>
		</tr>
		<tr>
				<td colspan="2">

				

								<h4 style="text-decoration:underline;"><strong>Medicamentos Activos</strong></h4>
							
									@foreach($appointment->patient->medicines as $item)
										<span>{{ $item->name }}</span><br>
									@endforeach
						
						
							<h4 style="text-decoration:underline;"><strong>Notas de padecimiento</strong></h4>
								
									@if($appointment->diseaseNotes->reason)
									<span><strong>Razón de la visita: </strong>{{ $appointment->diseaseNotes->reason }}</span><br><br>
									@endif
									@if($appointment->diseaseNotes->symptoms)
									<span><strong>Síntomas subjetivos: </strong>{{ $appointment->diseaseNotes->symptoms }}</span><br><br>
									@endif
									@if($appointment->diseaseNotes->phisical_review)
									<span><strong >Exploración Física: </strong>{{ $appointment->diseaseNotes->phisical_review }}</span><br><br>
									@endif
									@if($appointment->diseaseNotes->revalorizacion)
									<span><strong >Revalorización: </strong>{{ $appointment->diseaseNotes->revalorizacion }}</span><br><br>
									@endif
							
						
							<h4 style="text-decoration:underline;"><strong>Examen Físico</strong></h4>
						
								@if($appointment->physicalExams->cardio)
								<span><strong>Cardiaco y Vascular: </strong>{{ $appointment->physicalExams->cardio }} </span><br><br>
								@endif
								@if($appointment->physicalExams->linfatico)
								<span><strong >Sistema Linfático: </strong>{{ $appointment->physicalExams->linfatico }} </span><br><br>
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
					<h4 style="text-decoration:underline;"><strong>Examenes Laboratorio</strong></h4>
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
			<tr>
				<td colspan="2">
				
							<h4 style="text-decoration:underline;"><strong>Historial</strong></h4>
							
								@if($history->allergies)	
								<strong>Alergias: </strong><br>@foreach($history->allergies as $item)<span>- {{ $item->name }}</span><br>@endforeach
								@endif
								@if($history->pathologicals)
								
								<strong>Ant. Patológicos: </strong><br>@foreach($history->pathologicals as $item)<span>- {{ $item->name }} </span><br>@endforeach 
								@endif
								@if($history->nopathologicals)
									
								<strong>Ant. No Patológicos: </strong><br>@foreach($history->nopathologicals as $item)<span>- {{ $item->name }} </span><br>@endforeach 
								@endif
								@if($history->heredos)
									
								<strong>Ant. Heredofamiliares: </strong><br>@foreach($history->heredos as $item)<span>- {{ $item->name }} </span><br>@endforeach
								@endif
								@if($history->ginecos)
									
								<strong>Ant. Gineco-obstetricios: </strong><br>@foreach($history->ginecos as $item)<span>- {{ $item->name }} </span>@endforeach 
								@endif
								
						
							
							
					
				</td>
		</tr>
	
</table>
