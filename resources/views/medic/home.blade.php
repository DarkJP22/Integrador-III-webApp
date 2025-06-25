@extends('layouts.medics.app')

@section('content')
<section class="content">

   
   <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fa fa-calendar"></i></span>

            <div class="info-box-content">
              <a href="/agenda" class="info-box-text">Consultas</a>
              <a href="/agenda" class="info-box-number">{{ auth()->user()->appointments->count() }}</a>
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
              <a href="/medic/patients" class="info-box-text">Pacientes</a>
              <a href="/medic/patients" class="info-box-number">{{ auth()->user()->patients->count() }}</a>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fa fa-money"></i></span>

            <div class="info-box-content">
              <a href="/medic/invoices" class="info-box-text">Facturas</a>
              <a href="/medic/invoices" class="info-box-number">{{ auth()->user()->invoices->count() }}</a>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fa fa-hospital-o"></i></span>

            <div class="info-box-content">
              <a href="/medic/offices" class="info-box-text">Consultorios</a>
              <a href="/medic/offices" class="info-box-number">{{ auth()->user()->offices->count() }}</a>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
</section>
@endsection
