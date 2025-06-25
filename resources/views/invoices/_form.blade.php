
 <invoice-form
    :current-invoice="{{ json_encode(isset($invoice) ? $invoice : '')  }}"
    :tipo-documentos="{{ json_encode($tipoDocumentos) }}"
    :tipo-documentos-notas="{{ json_encode($tipoDocumentosNotas) }}"
    :tipo-documentos-exo="{{ json_encode($tipoDocumentosExo) }}"
    :tipo-identificaciones="{{ json_encode($tipoIdentificaciones) }}"
    :codigo-referencias="{{ json_encode($codigoReferencias) }}"
    :medio-pagos="{{ json_encode($medioPagos) }}"
    :condicion-ventas="{{ json_encode($condicionVentas) }}"
    current-tipo-documento="{{ $tipoDocumento }}"
    is-creating-nota="{{ isset($creatingNota) ? '1' : ''  }}"
    :offices="{{ $offices }}"
    :current-office="{{  auth()->user()->isAssistant() ? json_encode(auth()->user()->clinicsAssistants->first()) : json_encode(auth()->user()->offices->first())  }}"
    :patient="{{ json_encode(isset($patient) ? $patient : false) }}"
    :medic="{{ json_encode(isset($medic) ? $medic : false) }}"
    :appointment="{{ json_encode(isset($appointment) ? $appointment : false) }}"
    :currencies="{{ $currencies }}"
    :medics="{{ json_encode($medics) }}"
    :proforma="{{ json_encode(isset($proforma) ? $proforma : '') }}"
    :activities="{{ json_encode($activities) }}"
    :porc_discount_accumulated="{{  \App\Setting::getSetting('porc_discount_accumulated') ?? 0 }}"
    endpoint="{{ $endpoint }}"
    
    
    >
</invoice-form>