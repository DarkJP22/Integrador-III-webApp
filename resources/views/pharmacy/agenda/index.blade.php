@extends('layouts.pharmacies.app')
@section('header')
  <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
  <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.min.css">
  <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.print.css" media="print">
  <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css"> 

@endsection
@section('content')

    <section class="content-header">
      <h1>Agenda de {{ $medic->name }} </h1>
      <small>{{ $office->name }}</small>
    </section>
    
    <section class="content">
       
        <div class="row">
        
        <!-- /.col -->
        <div class="col-md-12">
         
         
          <div class="box box-default box-calendar">
            <div class="box-body no-padding">
               @include('layouts._loading')
              <!-- THE CALENDAR -->
              @php
                $user_settings = $medic->getAllSettings();
             @endphp
              <div id="calendar" data-slotDuration="{{ $user_settings['slotDuration'] }}" data-minTime="{{ $user_settings['minTime'] }}" data-maxTime="{{ $user_settings['maxTime'] }}" data-freeDays="{{ $user_settings['freeDays'] }}" data-medic="{{ $medic->id }}" data-office="{{ $office->id }}"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
         
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
     
     <modal-pharmacy-new-appointment 
        :patient="{{ isset($p) ? $p : 'false' }}" 
        :office="{{ $office->id }}"
        endpoint="/pharmacy/patients">
    </modal-pharmacy-new-appointment>
     


<form method="post" id="form-active-inactive">
 {{ csrf_field() }}
</form>

@endsection
@push('scripts')
<!-- <script src="https://unpkg.com/vue-select@1.3.3"></script>cv -->
<script src="/vendor/select2/js/select2.full.min.js"></script>  
<script src="/vendor/moment/min/moment.min.js"></script>
<script src="/vendor/fullcalendar/dist/jquery-ui.min.js"></script>
<script src="/vendor/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="/vendor/fullcalendar/dist/locale/es.js"></script>
<script src="/vendor/jquery.ui.touch-punch.min.js"></script>
<script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script> 
<script src="/vendor/tooltipster.bundle.min.js"></script>


@vite('resources/js/medicAgenda.js')


@endpush
