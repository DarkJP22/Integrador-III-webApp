@extends('layouts.clinics.app')

@section('content')
<div class="content">
   
        <div class="row no-print">
            <div class="col-md-12">
                 <button type="button" class="btn btn-default my-1" onclick="printSummary();"><i class="fa fa-paper-plane-o" ></i> Imprimir</button>
                <a href="/clinic/cierres" class="btn btn-primary" role="button">Regresar a Cierres</a>
            </div>
        </div>
       
        @include('cierres._cierreDataPrint')
       
    

</div>
@endsection
@push('scripts')
<script>
    function printSummary() {
            window.print();
        }
        
        window.onload = printSummary;
</script>
@endpush