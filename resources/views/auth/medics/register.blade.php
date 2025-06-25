@extends('layouts.login')

@section('header')
    <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
    <link rel="stylesheet" href="/vendor/magnific-popup/magnific-popup.css">
@endsection
@section('content')
    <div class="">
        <!-- <div class="register-logo">
         <a href="/"><img src="/img/logo.png" alt="{{ config('app.name', 'Laravel') }}"></a>
      </div> -->

        <div class="register-box-body">
            <p class="login-box-msg">Registra una nueva cuenta como Médico</p>

            <form role="form" method="POST" action="{{ url('/medic/register/medic') }}">
                {{ csrf_field() }}
                <!-- component -->
                <plan-selection :plans="{{ $plans }}" :old-selection="{{ json_encode(old('plan_id')) }}"></plan-selection>
                @if ($errors->has('plan_id'))
                    <span class="help-block">
                                            <strong>{{ $errors->first('plan_id') }}</strong>
                                        </span>
                @endif
                <div class="form-group has-feedback">

                    <select id="type_of_health_professional" class="form-control" style="width: 100%;" name="type_of_health_professional" required>
                        <option value="">Tipo de profesional</option>
                        @foreach ($typesOfHealthProfessional as $type)
                            <option value="{{ $type['id'] }}" {{ old('type_of_health_professional') ? ((old('type_of_health_professional') === $type['id']) ? 'selected' : '') : '' }}>{{ $type['name'] }}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('type_of_health_professional'))
                        <span class="help-block">
                            <strong>{{ $errors->first('type_of_health_professional') }}</strong>
                        </span>
                    @endif

                </div>
                <div class="form-group has-feedback" id="speciality-container">

                    <select id="speciality" class="form-control select2" style="width: 100%;" name="speciality[]" multiple>
                        <!-- <option value="">Especialidad</option> -->
                        @foreach ($specialities as $speciality)
                            <option value="{{ $speciality->id }}" {{ old('speciality') ? (in_array($speciality->id, old('speciality')) ? 'selected' : '') : '' }}>{{ $speciality->name }}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('speciality'))
                        <span class="help-block">
                            <strong>{{ $errors->first('speciality') }}</strong>
                        </span>
                    @endif

                </div>
                <div class="form-group has-feedback">
                    <input id="medic_code" type="text" class="form-control" name="medic_code"
                           value="{{ old('medic_code') }}" required autofocus placeholder="Número de colegiado">
                    @if ($errors->has('medic_code'))
                        <span class="help-block">
                            <strong>{{ $errors->first('medic_code') }}</strong>
                        </span>
                    @endif
                    <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input id="ide" type="text" class="form-control" name="ide" value="{{ old('ide') }}" required
                           autofocus placeholder="Cédula">
                    <small>Formato: rellernar con 0. Ej: 505550555</small>
                    @if ($errors->has('ide'))
                        <span class="help-block">
                            <strong>{{ $errors->first('ide') }}</strong>
                        </span>
                    @endif
                    <span class="glyphicon glyphicon-credit-card form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required
                           autofocus placeholder="Nombre y Apellidos">
                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required
                           placeholder="Email">

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input id="password" type="password" class="form-control" name="password" required
                           placeholder="Contraseña">

                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                           required placeholder="Confirmación de contraseña">
                    <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                </div>
                <div class="form-group row">
                    <div class="col-sm-4">
                        <select class="form-control" style="width: 100%;" name="phone_country_code" required>

                            <option value="+506" {{ old('phone_country_code') == '+506' ? 'selected' : '' }}>+506
                            </option>

                        </select>

                        @if ($errors->has('phone_country_code'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone_country_code') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="col-sm-8 has-feedback">
                        <input id="phone" type="text" class="form-control" name="phone_number"
                               value="{{ old('phone_number') }}" required placeholder="Teléfono">
                        @if ($errors->has('phone_number'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone_number') }}</strong>
                            </span>
                        @endif
                        <!-- <span class="glyphicon glyphicon-phone form-control-feedback"></span> -->
                    </div>

                </div>

                {{--                <div class="form-group has-feedback">--}}
                {{--                    <input id="general_cost_appointment" type="text" class="form-control" name="general_cost_appointment" value="{{ old('general_cost_appointment') }}" required--}}
                {{--                           autofocus placeholder="{{ getDefaultCurrency()->symbol  }} Costo Aproximado de la consulta ">--}}
                {{--                    @if ($errors->has('general_cost_appointment'))--}}
                {{--                        <span class="help-block">--}}
                {{--                            <strong>{{ $errors->first('general_cost_appointment') }}</strong>--}}
                {{--                        </span>--}}
                {{--                    @endif--}}
                {{--                    <span class="glyphicon glyphicon-cash form-control-feedback"></span>--}}
                {{--                </div>--}}

                <div class="form-group row">
                    <div class="col-xs-12 col-sm-12">
                        <input type="checkbox" value="1" name="terms" required> <a
                                href="https://cittacr.com/terminos-y-condiciones/" target="_blank"> Aceptar Términos y
                            Condiciones.</a>
                    </div>
                </div>

                <div class="row">

                    <!-- /.col -->
                    <div class="col-xs-12 col-sm-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Registrar</button>
                    </div>
                    <!-- /.col -->
                </div>

            </form>


            <a href="{{ url('/login') }}" class="text-center">Ya tengo una cuenta</a>
        </div>
        <!-- /.form-box -->
    </div>
    <div id="conditions-popup" class="conditions-popup white-popup mfp-hide mfp-with-anim">
        <h3 style="text-align:center">CONDICIONES DE AFILIACIÓN:</h3>

        <p>Estimado Dr(a): Doctor Blue Versión 1.0 le da la bienvenida y pone a su disposición nuestro Expediente Clínico
            Virtual, Agenda Clínica, Citas en Línea y Gestor para Clínicas.</p>

        <p>Es importante entender que para poder acceder a la plataforma se requiere comprobar la veracidad de su
            información, de ahí que usted va a requerir su <b>firma digital</b>. En caso de no tenerla, le recomendamos
            solicitarla llamando a las oficinas del Banco Nacional más cercano.</p>

        <p>Doctor Blue cargará un monto de <b>$1</b> por cada cita que sea atraída a la plataforma y reservada por el paciente
            (o usuario general). En caso de no ser atendida por el médico, esta no será sujeto de cobro. Tampoco serán
            sujetos de cobro las citas reservadas directamente por el médico u asistente (secretaria) en la plataforma.
        </p>

        <p>Si el médico desea hacer uso del Expediente Clínico deberá cancelar un monto fijo de acuerdo al paquete
            escogido.</p>


        <p>Para más información, le invitamos consultar nuestros nuestros <a
                    href="https://cittacr.com/terminos-y-condiciones/" target="_blank">Términos y Condiciones.</a></p>
        <p class="text-center"><a href="#" class="btn btn-primary" id="btn-aceptar-terms">Aceptar Términos y
                Condiciones</a> <a href="#" class="btn btn-danger" id="btn-cancel-terms">Cancelar</a></p>

    </div>
@endsection
@push('scripts')
    <script src="/vendor/select2/js/select2.full.min.js"></script>
    <script src="/vendor/magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="/vendor/inputmask/dist/jquery.inputmask.bundle.js"></script>
    <script>
        $(function () {
            const specialities = @json($specialities);
            let filteredSpecialities = [];
            const specialitiesSelect = $('#speciality-container .select2');

            $('#type_of_health_professional').on('change', function () {

                filteredSpecialities = specialities.filter((s) => s.professional === $(this).val());
                specialitiesSelect.empty();
                filteredSpecialities.forEach((s) => {
                    specialitiesSelect.append(`<option value="${s.id}">${s.name}</option>`);
                });
                specialitiesSelect.select2({
                    placeholder: "Tu Especialidad",
                    allowClear: true
                });

            }).trigger('change'); // Trigger change event on page load to set initial state


            $("[data-mask]").inputmask();

            //Initialize Select2 Elements
            $(".select2").select2({
                placeholder: "Selecciona",
                allowClear: true
            });


            // $(window).on('load', function() {
            //   if ($('#conditions-popup').length && !sessionStorage.getItem("terms") ) {
            //       $.magnificPopup.open({
            //         items: {
            //           src: '#conditions-popup'
            //         },
            //         type: 'inline',
            //         modal:true
            //         });
            //      $('#btn-aceptar-terms').on('click', function(e){
            //         sessionStorage.setItem("terms",1);
            //         $.magnificPopup.close();
            //      });
            //      $('#btn-cancel-terms').on('click', function(e){
            //        sessionStorage.removeItem("terms");
            //         $.magnificPopup.close();
            //         window.location.href = "https://cittacr.com/";
            //      });

            //     }
            // });


        });
    </script>
@endpush
