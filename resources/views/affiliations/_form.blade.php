
 <affiliation-form
    :current-affiliation="{{ json_encode(isset($affiliation) ? $affiliation : '')  }}"
    :tipo-plan="{{ json_encode($tipoPlan) }}"
    :medio-pagos="{{ json_encode($medioPagos) }}"
    :current-office="{{  auth()->user()->isAssistant() ? json_encode(auth()->user()->clinicsAssistants->first()) : json_encode(auth()->user()->offices->first())  }}"
    
    
    >
</affiliation-form>