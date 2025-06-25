@extends('layouts.admins.app')

@section('header')
    <link rel="stylesheet" href="/vendor/iCheck/flat/_all.css">
@endsection
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="box box-default">
                    <div class="box-header with-border">
                        Link para compartir
                    </div>
                    <div class="box-body">
                        <label>Url de Registro de un médico</label>
                        <div class="input-group">
                            <span class="input-group-addon">@</span>
                            <input type="text" class="form-control" placeholder="Url de Registro de un médico" readonly value="{{ url('medic/register') }}">
                        </div>
                        <br>
                        <label>Url de Registro de una clínica y su administrador</label>
                        <div class="input-group">
                            <span class="input-group-addon">@</span>
                            <input type="text" class="form-control" placeholder="Url de Registro de una clinica y su administrador" readonly value="{{ url('clinic/register') }}">
                        </div>
                        <br>
                        <label>Url de Registro de una farmacia y su administrador</label>
                        <div class="input-group">
                            <span class="input-group-addon">@</span>
                            <input type="text" class="form-control" placeholder="Url de Registro de una farmacia y su administrador" readonly value="{{ url('pharmacy/register') }}">
                        </div>

                    </div>
                </div>

                <div class="box box-default">
                    <div class="box-header with-border">
                        Configuraciones de saldos acumulados
                    </div>
                    <div class="box-body">
                        <form action="/admin/configuration/accumulated" method="POST" autocomplete="off">
                            @csrf
                            @method('put')
                            <label>Porcentaje de compra</label>
                            <div class="form-group">

                                <input type="text" class="form-control" name="porc_accumulated" placeholder="" value="{{ getPorcAccumulated() }}">

                                @error('porc_accumulated')
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <button type="submit" class="btn btn-primary">Guadar</button>
                        </form>

                    </div>
                </div>


                <div class="box box-default">
                    <div class="box-header with-border">
                        Configuraciones de commisiones Lab
                    </div>
                    <div class="box-body">
                        <form action="/admin/configuration/commission" method="POST" autocomplete="off">
                            @csrf
                            @method('put')
                            <label>Porcentaje por defecto</label>
                            <div class="form-group">

                                <input type="text" class="form-control" name="porc_commission" placeholder="" value="{{ getPorcCommission() }}">

                                @error('porc_commission')
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <label>Porcentaje por referencia</label>
                            <div class="form-group">

                                <input type="text" class="form-control" name="porc_reference_commission" placeholder="" value="{{ getPorcReferenceCommission() }}">

                                @error('porc_reference_commission')
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <button type="submit" class="btn btn-primary">Guadar</button>
                        </form>

                    </div>
                </div>

                <register-authorization-code-generator></register-authorization-code-generator>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="box box-default">
                    <div class="box-header with-border">
                        Configuraciones Generales
                    </div>
                    <div class="box-body">



                        <form action="/admin/configuration" method="POST">
                            <label>Meses de prueba para nuevos registros</label>
                            <div class="input-group ">

                                {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
                                <input type="text" name="subscription_months_free" class="form-control" value="{{ $settings['subscription_months_free'] }}">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary btn-flat">Guardar</button>
                                </span>

                            </div>
                        </form>
                        <br>
                        <form action="/admin/configuration" method="POST">
                            <label>Numero Teléfono Call Center</label>
                            <div class="input-group ">

                                {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
                                <input type="text" name="call_center" class="form-control" value="{{ getCallCenterPhone() }}">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary btn-flat">Guardar</button>
                                </span>

                            </div>
                        </form>
                        <br>
                        <form action="/admin/configuration" method="POST">

                            {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">
                            <div class="form-group ">

                                <label>Url de app movil de pacientes Android para compartir </label>
                                <input type="text" name="url_app_pacientes_android" class="form-control" value="{{ getUrlAppPacientesAndroid() }}">

                            </div>
                            <div class="form-group ">

                                <label>Url de app movil de pacientes ios para compartir</label>
                                <input type="text" name="url_app_pacientes_ios" class="form-control" value="{{ getUrlAppPacientesIos() }}">

                            </div>

                            <div class="form-group ">

                                <label>Porcentaje Descuento en exámenes de laboratorio</label>
                                <input type="text" name="lab_exam_discount" class="form-control" value="{{ getLabExamDiscount() }}">

                            </div>

                            <div class="form-group">

                                <button type="submit" class="btn btn-primary btn-flat">Guardar</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>


    </section>
@endsection
@push('scripts')
    <script src="/vendor/iCheck/icheck.min.js"></script>


    <script>
        $(function() {

            $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            })

        });
    </script>
@endpush
