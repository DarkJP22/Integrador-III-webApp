@extends('layouts.medics.app')
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
                <img src="{{ ($appointment->office) ? $appointment->office->logo_path : '/img/logo.png' }}" alt="logo">
            </div>
            </div>
            <div class="col-sm-4 invoice-col" style="text-align:center;">
            
            </div>
       </div>
        <hr>
				<div class="row invoice-info">
        <div class="col-sm-4 invoice-col" style="text-align:left;">
          
          <div class="invoice-number">
            <b>Nro. Consulta:</b> {{$appointment->id }}
           
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
        <div class="col-sm-4 invoice-col"  style="text-align:right;">
          @if($appointment->office)
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
            <b>Paciente:</b> {{ $appointment->patient_id }}. {{ trans('utils.gender.'.$appointment->patient->gender) }}.<br>
						 <b>Fecha Nacimiento:</b> {{ age($appointment->patient->birth_date) }}<br>
		        
						<b>Fecha Consulta:</b> {{ $appointment->date }}<br>
        </div>
        <div class="col-xs-4 invoice-col invoice-right">
           
        </div>
        <div class="col-xs-4 invoice-col invoice-right">
						<b>Médico:</b> {{ $appointment->user?->name ?? $appointment->medic_name }}<br>
            @if($appointment->user)
						<b>Código de Médico:</b> {{ $appointment->user->medic_code }}<br>
            @foreach( $appointment->user->specialities as $speciality)
              {{ $speciality->name }} 
            @endforeach
            @endif
        </div>
      </div>
      <hr>
		
		    <!-- Table row -->
		    <div class="row">
		      <div class="col-xs-12">
		        <div class="print-summary-page" style="font-size:12px;">
			      	<summary-appointment :history="{{ $history }}" :medicines="{{ $appointment->patient->medicines }}" :notes="{{ $appointment->diseaseNotes }}" :exams="{{ $appointment->physicalExams }}" :diagnostics="{{ $appointment->diagnostics }}" :treatments="{{ $appointment->treatments }}" instructions="{{ $appointment->medical_instructions }}" :labexams="{{ $appointment->labexams }}" :is-current="true">
			      		Historia Clínica del Paciente
			      	</summary-appointment>
		      	</div>
		      </div>
		    </div>
		    <!-- /.row -->

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