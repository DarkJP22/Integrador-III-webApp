@extends('layouts.operators.app')
@section('header')
<link rel="stylesheet" href="/vendor/select2/css/select2.min.css">  
@endsection
@section('content')


    <section class="content">
      
      <div class="row">
        <div class="col-md-4">

            <avatar-form :user="{{ $profileUser }}"></avatar-form>
         
        </div>
        <!-- /.col -->
        <div class="col-md-8">
         
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs" id="tabs-profile">
              <li class="{{ isset($tab) ? ($tab =='profile') ? 'active' : '' : 'active' }}"><a href="#profile" data-toggle="tab" class="tab-profile">Perfil</a></li>
              
            </ul>
            <div class="tab-content">
              <div class="{{ isset($tab) ? ($tab =='profile') ? 'active' : '' : 'active' }} tab-pane" id="profile">

                 @include('operator._profileForm')

              </div>
              <!-- /.tab-pane -->
              
              
             
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>


@endsection
@push('scripts')
<script src="/vendor/select2/js/select2.full.min.js"></script>

<script>
  $(function () {

    


   
   

    $(".select2").select2();




});
</script>
@endpush
