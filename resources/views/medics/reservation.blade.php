@extends('layouts.users.app')
@section('header')
  <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
  <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.min.css">
  <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.print.css" media="print">
  <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="/vendor/hopscotch/css/hopscotch.min.css"> 
@endsection
@section('content')
  
    <section class="content">
       @if(auth()->user()->patients->count())
         @if($office->active)
        <div class="row">
        <div class="col-md-3">
          
          <!-- /. box -->
        
          <div class="box box-solid box-create-appointment">
            <div class="box-header with-border">
              <h4 class="box-title">Tu cita</h4>
            </div>
            <div class="box-body">
              <!-- the events -->
              @if($medic->phone)
                 <p> Reserve su cita en la hora deseada</p>
                  
              @endif
              <div id="external-events">
                <div class="external-event bg-dark" data-patient="{{ (auth()->user()->patients->first()) ? auth()->user()->patients->first()->id : auth()->id() }}" data-doctor="{{ $medic->id }}" data-createdby="{{ auth()->id() }}" data-office="{{ $office->id }}">Cita</div>
              </div>
            
              <p class="text-muted well well-sm no-shadow text-center" style="margin-top: 10px;">
                Arrastra el elemento de arriba llamado cita dentro del calendario en la fecha y hora deseado!
              </p>
             
              
            </div>
            <!-- /.box-body -->
          </div>
          <p class="text-muted well well-sm no-shadow text-center" style="margin-top: 10px;">
                Puedes hacer click en una celda del calendario para crear la cita en la hora deseada!
              </p>
          
          
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="box box-default box-calendar patient-calendar">
            <div class="box-header text-center">
             <h2 class="box-title"><b>{{ $office->name }}</b></h2> 
            </div>
            <div class="box-body no-padding">
              @include('layouts._loading')
              <!-- THE CALENDAR -->
                @php
                    $user_settings = $medic->getAllSettings();
                @endphp
              <div id="calendar" data-slotDuration="{{ $user_settings['slotDuration'] }}" data-minTime="{{ $user_settings['minTime'] }}" data-maxTime="{{ $user_settings['maxTime'] }}" data-appointmentsday="{{ auth()->user()->appointmentsToday() }}" data-freeDays="{{ $user_settings['freeDays'] }}"></div>

          
              <!-- Modal -->
              <modal-reservation 
                :patient="{{ auth()->user()->patients->first() }}" 
                :patients="{{ auth()->user()->patients }}" 
                :phone="{{ json_encode(getCallCenterPhone()) }}">
                </modal-reservation>

               <modal-reminder></modal-reminder>
              
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

         @else
              <div class="callout callout-danger"><h4>Clinica Inactiva!</h4><p>Regresar al buscador y revisa otra opción. <a href="/medics" >Regresar</a></p></div>
         @endif
     @else
          <div class="callout callout-danger"><h4>Recuerda !</h4> <p>Necesitas tener al menos un paciente registrado para poder realizar citas en linea. <a href="/profiles/{{ auth()->id() }}?tab=patients" class="btn btn-sm btn-success">Registre su paciente</a></p></div>
     @endif
    </section>

 
@endsection
@push('scripts')
<script src="/vendor/select2/js/select2.full.min.js"></script>
<script src="/vendor/moment/min/moment.min.js"></script>
<script src="/vendor/fullcalendar/dist/jquery-ui.min.js"></script>
<script src="/vendor/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="/vendor/fullcalendar/dist/locale/es.js"></script>
<script src="/vendor/sweetalert2/sweetalert2.min.js"></script>
<script src="{{ mix('/js/reservations.js') }}"></script>
 <script src="/vendor/hopscotch/js/hopscotch.min.js"></script> 
<script>
  var tour = {
      id: "crear-cita",
      
      i18n: {
          nextBtn: "Siguiente",
          prevBtn: "Atras",
          doneBtn: "Listo"
        },
       
      steps: [
        {
          title: "Crear Cita",
          content: "Selecciona una hora en el calendario",
          target: "#calendar .fc-thu",
          placement: "top",
          
        }
        
      ],
     onEnd: function () {
       
        localStorage.setItem("tour_crear_cita_paciente_viewed", 1)

      }

    };

    if(!localStorage.getItem("tour_crear_cita_paciente_viewed"))
    {
      hopscotch.startTour(tour);
    }

   
    
</script>
@endpush
