@extends('layouts.pharmacies.app')

@section('content')
    <section class="content">
        <div class="row">

            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fa fa-users"></i></span>

                    <div class="info-box-content">
                        <a href="/pharmacy/patients" class="info-box-text">Pacientes</a>
                        <a href="/pharmacy/patients" class="info-box-number">{{ auth()->user()->patients->count() }}</a>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fa fa-medkit"></i></span>

                    <div class="info-box-content">
                        <a href="/pharmacy/medicines/reminders" class="info-box-text">Recordatorios Medicamentos</a>
                        <a href="/pharmacy/medicines/reminders" class="info-box-number">{{ $pharmacy->medicineRemiders->count() }}</a>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

        </div>
    </section>
@endsection
