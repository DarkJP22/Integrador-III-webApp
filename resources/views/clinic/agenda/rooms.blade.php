@extends('layouts.clinics.app')
@section('header')
  <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
  <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.min.css">
  <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.print.css" media="print">
  <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css"> 

@endsection
@section('content')

     <section class="content-header">
      <h1>
        Agenda del dia  @if($room) <small> {{ $room->name }}</small> @endif
      </h1>
    
    </section>
    
    <section class="content">
       
        <div class="row">
        <div class="col-md-4">
          
          
         
       
          <div class="box box-solid box-medics">
            <div class="box-header with-border">
              <h4 class="box-title">Salas </h4> <a href="/clinic/agenda/" class="pull-right"> Vista Médicos</a>
              <div><small>(Haz click en una sala para ver su agenda de citas)</small></div>
              <div>
                  <form action="/clinic/agenda/rooms" method="GET">
                    

                    <input type="text" name="q" placeholder="Buscar..." class="form-control" value="{{ isset($q) ? $q : '' }}">
                  </form>
                 
              </div>
            </div>
            <div class="box-body">
              <!-- the events -->
              <div id="external-medics">
                <ul class="medic-list medic-list-in-box">
                  @foreach($rooms as $sala)
                    <li class="item {{ (isset($room) && $sala->id == $room->id) ? 'medic-list-selected': '' }}">
                      
                      <div class="room-info">
                        <a href="/clinic/agenda/rooms?room={{$sala->id }}{{ (request('page')) ? '&page='.request('page') : '' }}&q={{ isset($q) ? $q : '' }}" class="medic-title">{{ $sala->name }}
                          </a>
                         
                          
                           
                            <a href="/clinic/agenda/rooms?room={{ $sala->id }}{{ (request('page')) ? '&page='.request('page') : '' }}" class="btn btn-primary btn-sm pull-right" title="Ver Calendario"><i class="fa fa-calendar"></i></a>
                           

                      </div>
                    </li>
                   
                  @endforeach
                  
                </ul>
                @if ($rooms)
                        <div  class="pagination-container">{!! $rooms->render() !!}</div>
                  @endif
               
              </div>
            </div>
            <!-- /.box-body -->
          </div>
       
          
          
        </div>
        <!-- /.col -->
        <div class="col-md-8">
         
          @if($room)
          <div class="box box-default box-calendar">
            <div class="box-body no-padding">
               @include('layouts._loading')
              <!-- THE CALENDAR -->

              <div id="calendar" data-slotDuration="00:30:00" data-minTime="06:00:00" data-maxTime="22:00:00" data-freeDays="{{ json_encode(['0']) }}" data-room="{{ $room->id }}" data-office="{{ $office->id }}"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
          @else
          <div class="box box-default box-calendar">
            <div class="box-body ">
             @include('layouts._loading')
              <!-- THE CALENDAR -->
               <div class="callout callout-info">
                    <h4>Información importante!</h4>

                    <p>Selecciona una sala para ver su agenda</p>
                </div>
               

            </div>
            <!-- /.box-body -->
          </div>
          
          @endif
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
     
     <modal-clinic-new-appointment-room 
        :patient="{{ isset($p) ? $p : 'false' }}" 
        :office="{{ $office->id }}"
        :optreatments="{{ $optreatments }}"
        :is-esthetic="{{ $office->is_esthetic ?? 'false' }}"
        endpoint="/clinic/patients"
        >
    </modal-clinic-new-appointment-room>
     


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

  @vite('resources/js/roomAgenda.js')

@endpush
