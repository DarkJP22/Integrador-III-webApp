@extends('layouts.users.app')
@section('header')
  <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
  <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.min.css">
  <link rel="stylesheet" href="/vendor/fullcalendar/dist/fullcalendar.print.css" media="print">
  <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="/vendor/hopscotch/css/hopscotch.min.css"> 
@endsection
@section('content')
   <section class="content-header">
      <h1>
        @if($medic)
          Reserva tu cita con {{ $medic->name }}
        @else
            Reserva tu cita
        @endif
        </h1>
    </section>
   

    <section class="content">
       @if(auth()->user()->patients->count())
         
        <div class="row">
        <div class="col-md-4">
          
          <!-- /. box -->
           <div class="box box-solid box-medics">
            <div class="box-header with-border">
              <h4 class="box-title">Médicos </h4>
              <div><small>(Haz click en un medico para ver su agenda)</small></div>
            </div>
            <div class="box-body">
              <!-- the events -->
                @if($office->phone)
                  <p> Reserve su cita en la hora deseada </p>
                  
                @endif
              <div id="external-medics">
               <ul class="medic-list medic-list-in-box">
                @foreach($medics as $doctor)
                   @if($doctor->verifyOffice($office->id)) 
                   <li class="item {{ (isset($medic) && $doctor->id == $medic->id) ? 'medic-list-selected': '' }}">
                      <div class="medic-img">
                      <!--/img/default-50x50.gif-->
                        <img src="{{ $doctor->avatar_path }}" alt="Medic Image" width="50" height="50">
                      </div>
                      <div class="medic-info">
                        <a href="/clinics/{{ $office->id }}/reservation?medic={{$doctor->id }}{{ (request('page')) ? '&page='.request('page') : '' }}" class="medic-title">{{ $doctor->name }}
                          </a>
                          
                           
                            <a href="/clinics/{{ $office->id }}/reservation?medic={{$doctor->id }}{{ (request('page')) ? '&page='.request('page') : '' }}" class="label  label-primary pull-right">Ver Calendario</a>
                           
                        
                            <span class="medic-description">
                            @if($doctor->specialities->count())
                                  @foreach($doctor->specialities as $speciality) {{ $speciality->name }} @endforeach
                                @else
                                  Médico General
                                @endif 
                             
                            </span>
                      </div>
                    </li>
                    @else
                    <li class="item">
                      <div class="medic-img">
                      <!--/img/default-50x50.gif-->
                       
                        <img src="{{ $doctor->avatar_path }}" alt="Medic Image" width="50" height="50">
                      </div>
                      <div class="medic-info">
                           <div>{{ $doctor->name }}</div>
                         
                          
                           
                              <span class="label  label-default pull-right">No Disponible</span>
                           
                        
                            <span class="medic-description">
                               @if($doctor->specialities->count())
                                  @foreach($doctor->specialities as $speciality) {{ $speciality->name }} @endforeach
                                @else
                                  Médico General
                                @endif 
                             
                            </span>
                      </div>
                    </li>

                    @endif

                @endforeach

                </ul>
                @if ($medics)
                        <div  class="pagination-container">{!!$medics->render()!!}</div>
                    @endif
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          @if($medic)
          <div class="box box-solid box-create-appointment">
            <div class="box-header with-border">
              <h4 class="box-title">Tu cita</h4>
            </div>
            <div class="box-body">
              <!-- the events -->
              <div id="external-events">
                <div class="external-event bg-dark" data-patient="{{ (auth()->user()->patients->first()) ? auth()->user()->patients->first()->id : auth()->id() }}" data-doctor="{{ $medic->id }}" data-createdby="{{ auth()->id() }}" data-office="{{ $office->id }}">Cita</div>
              </div>
            
              <p class="text-muted well well-sm no-shadow text-center" style="margin-top: 10px;">
                Arrastra el elemento de arriba llamado cita dentro del calendario en la fecha y hora deseada!
              </p>
             
              
            </div>
            <!-- /.box-body -->
          </div>
          <p class="text-muted well well-sm no-shadow text-center" style="margin-top: 10px;">
                Puedes hacer click en una celda del calendario para crear la cita en la hora deseada!
              </p>
          
          @endif
        </div>
        <!-- /.col -->
        <div class="col-md-8">
          @if($medic)
          <div class="box box-default box-calendar">
            <div class="box-body no-padding">
               @include('layouts._loading')
              <!-- THE CALENDAR -->
                @php
                    $user_settings = $medic->getAllSettings();
                @endphp
              <div id="calendar" data-slotDuration="{{ $user_settings['slotDuration'] }}" data-minTime="{{ $user_settings['minTime'] }}" data-maxTime="{{ $user_settings['maxTime'] }}" data-appointmentsday="{{ auth()->user()->appointmentsToday() }}" data-freeDays="{{ $user_settings['freeDays'] }}"></div>

          
               <modal-reservation 
                :patient="{{ auth()->user()->patients->first() }}" 
                :patients="{{ auth()->user()->patients }}" 
                :phone="{{ $office->phone ? $office->phone : $medic->phone }}">
                </modal-reservation>

               <modal-reminder></modal-reminder>
              
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
          @else
             <div class="box box-default box-calendar">
              <div class="box-body ">
                <!-- THE CALENDAR -->
                <div class="callout callout-danger">
                    <h4>Informacion importante!</h4>

                    <p>Selecciona un Médico para ver su agenda</p>
                </div>
                
              </div>
              <!-- /.box-body -->
            </div>
          @endif
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
        
     @else
          <div class="callout callout-danger"><h4>Recuerda !</h4> <p>Necesitas tener al menos un paciente registrado para poder realizar citas en linea. <a href="/profiles/{{ auth()->id() }}?tab=patients" class="btn btn-sm btn-primary">Registre su paciente</a></p></div>
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
       
       // localStorage.setItem("tour_viewed", 1)

      }

    };

 
    hopscotch.startTour(tour);

   
    
</script>
@endpush
