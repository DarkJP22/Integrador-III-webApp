 <div class="modal fade" id="modalAuthorizationPatient-{{ $patient->id }}" role="dialog" aria-labelledby="modalAuthorizationPatient-{{ $patient->id }}">
      <div class="modal-dialog " role="document">
        <div class="modal-content">
          <div class="modal-header">
          
         
           <h4 class="modal-title">Se requiere autorización. Se le enviará un SMS al paciente con el código de activación o puede solicitarlo a Soporte</h4>
          </div>
          <div class="modal-body" >
              
            <div class="text-center">
                <authorization-patient-by-code :patient="{{ $patient }}" callback="{{ isset($callback) ? $callback : '' }}"></authorization-patient-by-code>
            </div>
              
                
          </div>
            <div class="modal-footer" >
            
            
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            
          </div>
        </div>
      </div>
    </div>