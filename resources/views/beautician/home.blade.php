@extends('layouts.beauticians.app')

@section('content')
<section class="content">
   <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fa fa-calendar"></i></span>

            <div class="info-box-content">
              <a href="/beautician/agenda" class="info-box-text">Consultas</a>
              <a href="/beautician/agenda" class="info-box-number">{{ auth()->user()->appointments->count() }}</a>
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
              <a href="/beautician/patients" class="info-box-text">Pacientes</a>
              <a href="/beautician/patients" class="info-box-number">{{ auth()->user()->patients->count() }}</a>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

       
      </div>
</section>
@endsection
