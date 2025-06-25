@extends('layouts.assistants.app')

@section('content')
<section class="content">
   <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fa fa-calendar"></i></span>

            <div class="info-box-content">
              <a href="/assistant/agenda" class="info-box-text">Consultas</a>
              <a href="/assistant/agenda" class="info-box-number">{{ $office->appointments->count() }}</a>
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
              <a href="/assistant/patients" class="info-box-text">Pacientes</a>
              <a href="/assistant/patients" class="info-box-number">{{ $patients->count() }}</a>
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
              <a href="/assistant/invoices" class="info-box-text">Facturas</a>
              <a href="/assistant/invoices" class="info-box-number">{{ $office->invoices->count() }}</a>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fa fa-building"></i></span>

            <div class="info-box-content">
              <a href="/assistant/medics" class="info-box-text">Medicos</a>
              <a href="/assistant/medics" class="info-box-number">{{ $office->doctors()->count() }}</a>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
</section>
@endsection
