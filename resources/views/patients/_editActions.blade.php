 <div class="btn-group">
    <a href="{{ url('/general/patients/'.$patient->id.'?tab=history') }}" class="btn btn-secondary" title="Ver Historial"><i class="fa fa-edit"></i> Historial</a>
    
    @can('delete', $patient)
        <button type="submit" class="btn btn-danger" form="form-delete" formaction="{!! url('/general/patients/'.$patient->id) !!}" title="Eliminar Paciente"><i class="fa fa-remove"></i></button>
    @endcan
    
</div>

{{--@if(auth()->user()->hasAuthorizationOf($patient->id))--}}
{{--      @include('patients._buttonInitAppointment')--}}
{{--@else--}}
{{--    <authorization-patient-by-code :patient="{{ $patient }}"></authorization-patient-by-code>--}}
{{--@endif--}}
