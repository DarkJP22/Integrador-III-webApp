@extends('layouts.clinics.app')
@section('header')
  <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
  <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.min.css">
  <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.print.css" media="print">
  <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css"> 
  <link rel="stylesheet" href="/vendor/tooltipster.bundle.min.css"> 
  <link rel="stylesheet" href="/vendor/hopscotch/css/hopscotch.min.css"> 
  <style>
    .tooltipster-sidetip.tooltipster-noir.tooltipster-gps .tooltipster-box {
      background: #605ca8;
      border: 3px solid #605ca8;
      border-radius: 6px;
      box-shadow: 5px 5px 2px 0 rgba(0,0,0,0.4);
    }

    .tooltipster-sidetip.tooltipster-noir.tooltipster-gps .tooltipster-content {
      color: white;
      padding: 8px;
    }
  </style>
@endsection
@section('content')
    

    <section class="content">
        
  
      
        <div class="row">
        
        <!-- /.col -->
        <div class="col-md-12">
          <div class="box box-default box-calendar medico-calendar">
            <div class="box-body nfo-padding">
               @include('layouts._loading')
              <!-- THE CALENDAR -->
              
              <form method="POST" action="{{ url('/clinic/schedules/copy') }}" class="form-horizontal">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                      <h4 class="box-title">Copiar Horario de Semana </h4>
                      <div class="form-group">
                              <div class="col-sm-12">
                                  <select name="dateweek" id="dateweek" class="form-control">
                                      @foreach($weeks as $key => $week)
                                      <option value="{{ $week[0] }}">{{ $key }}</option>
                                      @endforeach
                                    
                                  </select>
                              </div>
                      </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <h4>A Semana actual:</h4>
                        <p><b>#{{ Carbon\Carbon::now()->weekOfMonth + 1 }} : {{ Carbon\Carbon::now()->startOfWeek()->toDateString() }} | {{ Carbon\Carbon::now()->endOfWeek()->toDateString() }}</b> <button class="btn btn-secondary" >Copiar</button></p> 
                        
                            
                         
                    
                    </div>
                </div>
              
            
    
               
              </form>
              <div id="calendar" data-office="{{ $office->id }}"></div>
              <modal-schedule-form :office="{{ $office }}" :medics="{{ $medics }}"></modal-schedule-form>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
     
    </section>

  <!-- Modal -->

      
      
      
@endsection

@push('scripts')
<script src="/vendor/select2/js/select2.full.min.js"></script>  
<script src="/vendor/moment/min/moment.min.js"></script>
<script src="/vendor/fullcalendar/dist/jquery-ui.min.js"></script>
<script src="/vendor/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="/vendor/fullcalendar/dist/locale/es.js"></script>
<script src="/vendor/jquery.ui.touch-punch.min.js"></script>
<script src="/vendor/sweetalert2/sweetalert2.min.js"></script>
<script src="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script> 
<script src="/vendor/tooltipster.bundle.min.js"></script> 
<script src="/vendor/hopscotch/js/hopscotch.min.js"></script> 

@vite('resources/js/clinicScheduleMonthly.js')

@endpush

