@php
    $user_settings = auth()->user()->getAllSettings();
@endphp
<div class="modal fade" id="initAppointment" role="dialog" aria-labelledby="initAppointment">
    <div class="modal-dialog " role="document">
      <div class="modal-content">
        <div class="modal-header">
        
        <h4 class="modal-title" id="initAppointmentLabel">Iniciando la cita</h4>
        </div>
        <div class="modal-body" data-modaldate data-modalpatient data-slotDuration="{{ $user_settings['slotDuration'] }}">
            <div class="callout callout-info">
              <p>Selecciona un consultorio o clínica del calendario para ver los horarios disponibles correspondiente </p>
            </div>
            <div class="content form-horizontal">
                
               
                <div class="row">
                  <div class="col-xs-12 col-sm-8 calendar-popup" >

                    <div id="miniCalendar" data-slotDuration="{{ $user_settings['slotDuration'] }}" data-minTime="{{ $user_settings['minTime'] }}" data-maxTime="{{ $user_settings['maxTime'] }}" data-freeDays="{{ $user_settings['freeDays'] }}"></div>
                  </div>
                  <div class="col-xs-12 col-sm-4">
                      
                          
                          <div class="form-group">
                            <h4>Horario Disponible</h4>
                            <span class="label label-warning schedule-clinic"></span>
                              <select name="schedule-list" id="schedule-list" class="form-control ">
                                
                              </select>
                              
                          </div>
                          <!-- <div class="schedule-list">
                          
                          </div> -->
                          <input type="hidden" name="office_id" >
                          <input type="hidden" name="date" >
                          <input type="hidden" name="start" >
                          <input type="hidden" name="end">
                          <div class="form-group">
                      
                            <button type="button" class="btn btn-primary add-cita">Iniciar la cita</button>
                          </div>
                      
                      
                  </div>
                
                </div>
               
              
            </div>
            

             
        </div>
         <div class="modal-footer" >
         
         
          <button type="button" class="btn btn-secondary btn-cancel" data-dismiss="modal">Cancelar</button>
         
        </div>
      </div>
    </div>
  </div>