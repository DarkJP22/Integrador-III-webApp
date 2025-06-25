@extends('layouts.operators.app')

@section('content')
<section class="content">
   <div class="row">
       
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fa fa-user-md"></i></span>

            <div class="info-box-content">
              <a href="/operator/medics" class="info-box-text">Medicos</a>
              <a href="/operator/medics" class="info-box-number">{{ $medics->total() }}</a>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fa fa-users"></i></span>

            <div class="info-box-content">
              <a href="/operator/patients" class="info-box-text">Pacientes</a>
              <a href="/operator/patients" class="info-box-number">{{ $patients->total() }}</a>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>

       
      </div>
</section>
@endsection
