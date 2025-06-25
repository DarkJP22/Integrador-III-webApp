@extends('layouts.medics.app')
@section('content')
<div class="content">

    
    @include('proformas._proformaDataPrint')
    
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
