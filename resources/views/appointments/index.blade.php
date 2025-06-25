@extends('layouts.users.app')

@section('content')
    
<section class="content">
    <div class="row" style="text-align:  center;">
        @include('appointments._historical')
    </div>

</section>

<form method="post" id="form-delete" data-confirm="Estas Seguro?">
  <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
</form>
@endsection
@push('scripts')

@endpush