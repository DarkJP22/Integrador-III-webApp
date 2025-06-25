<div class="form-group">
    <label for="tipo_identificacion" class="col-sm-2 control-label">Identificación</label>

    <div class="col-sm-10">
        <select class="form-control select2" style="width: 100%;" name="tipo_identificacion">
            <option value=""></option>
            <option value="01" {{ isset($patient) ? ($patient->tipo_identificacion == '01' ? 'selected' : (old('tipo_identificacion') == '01' ? 'selected' : '')) : (old('tipo_identificacion') == '01' ? 'selected' : '') }}>
                Cédula Física
            </option>
            <option value="02" {{ isset($patient) ? ($patient->tipo_identificacion == '02' ? 'selected' : (old('tipo_identificacion') == '02' ? 'selected' : '')) : (old('tipo_identificacion') == '02' ? 'selected' : '') }}>
                Cédula Jurídica
            </option>
            <option value="03" {{ isset($patient) ? ($patient->tipo_identificacion == '03' ? 'selected' : (old('tipo_identificacion') == '03' ? 'selected' : '')) : (old('tipo_identificacion') == '03' ? 'selected' : '') }}>
                DIMEX
            </option>
            <option value="04" {{ isset($patient) ? ($patient->tipo_identificacion == '04' ? 'selected' : (old('tipo_identificacion') == '04' ? 'selected' : '')) : (old('tipo_identificacion') == '04' ? 'selected' : '') }}>
                NITE
            </option>
            <option value="00" {{ isset($patient) ? ($patient->tipo_identificacion == '00' ? 'selected' : (old('tipo_identificacion') == '00' ? 'selected' : '')) : (old('tipo_identificacion') == '00' ? 'selected' : '') }}>
                No definido
            </option>
        </select>
        @if ($errors->has('tipo_identificacion'))
            <span class="help-block">
            <strong>{{ $errors->first('tipo_identificacion') }}</strong>
        </span>
        @endif

    </div>

</div>
<div class="form-group">
    <label for="ide" class="col-sm-2 control-label">Cédula</label>

    <div class="col-sm-10">

        <input type="text" class="form-control" name="ide" placeholder="Cédula"
               value="{{ isset($patient) ? $patient->ide : old('ide') }}">
        @if ($errors->has('ide'))
            <span class="help-block">
            <strong>{{ $errors->first('ide') }}</strong>
        </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="office_name" class="col-sm-2 control-label">Nombre Completo</label>

    <div class="col-sm-10">
        <input type="text" class="form-control" name="first_name" placeholder="Nombre"
               value="{{ isset($patient) ? $patient->first_name : old('first_name') }}" required>
        @if ($errors->has('first_name'))
            <span class="help-block">
            <strong>{{ $errors->first('first_name') }}</strong>
        </span>
        @endif
    </div>
</div>
<!-- <div class="form-group">
    <label for="last_name" class="col-sm-2 control-label">Apellidos</label>

    <div class="col-sm-10">
        <input type="text" class="form-control" name="last_name" placeholder="Apellidos" value="{{ isset($patient) ? $patient->last_name : old('last_name') }}">
        @if ($errors->has('last_name'))
    <span class="help-block">
        <strong>{{ $errors->first('last_name') }}</strong>
        </span>










@endif
</div>
</div> -->
<div class="form-group">
    <label for="birth_date" class="col-sm-2 control-label">Fecha de Nacimiento</label>

    <div class="col-sm-10 flatpickr">
        <input type="text" class="form-control" name="birth_date" placeholder="Fecha de Nacimiento"
               value="{{ isset($patient) ? $patient->birth_date : old('birth_date') }}"
               data-inputmask="'alias': 'yyyy-mm-dd'" data-input>
        @if ($errors->has('birth_date'))
            <span class="help-block">
            <strong>{{ $errors->first('birth_date') }}</strong>
        </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="gender" class="col-sm-2 control-label">Sexo</label>

    <div class="col-sm-10">
        <select class="form-control select2" style="width: 100%;" name="gender" placeholder="-- Selecciona Genero --"
                >

            <option value="m" {{ isset($patient) ? $patient->gender == 'm' ? 'selected' : '' : '' }}>Masculino</option>
            <option value="f" {{ isset($patient) ? $patient->gender == 'f' ? 'selected' : '' : '' }}>Femenino</option>
        </select>

        @if ($errors->has('gender'))
            <span class="help-block">
            <strong>{{ $errors->first('gender') }}</strong>
        </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="phone" class="col-sm-2 control-label">Teléfono</label>

    <div class="col-sm-3">
        <select class="form-control" style="width: 100%;" name="phone_country_code" required>

            <option value="+506" {{ isset($patient) ? $patient->phone_country_code == '+506' ? 'selected' : '' : (old('phone_country_code') == '+506' ? 'selected' : '') }}>
                +506
            </option>

        </select>

        @if ($errors->has('phone_country_code'))
            <span class="help-block">
            <strong>{{ $errors->first('phone_country_code') }}</strong>
        </span>
        @endif
    </div>
    <div class="col-sm-7">
        <input type="text" class="form-control" name="phone_number" placeholder="Celular"
               value="{{ isset($patient) ? $patient->phone_number : old('phone_number') }}">
        @if ($errors->has('phone_number'))
            <span class="help-block">
            <strong>{{ $errors->first('phone_number') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group">
    <label for="phone" class="col-sm-2 control-label">Teléfono</label>

    <div class="col-sm-3">
        <select class="form-control" style="width: 100%;" name="phone_country_code_2" required>

            <option value="+506" {{ isset($patient) ? $patient->phone_country_code_2 == '+506' ? 'selected' : '' : (old('phone_country_code_2') == '+506' ? 'selected' : '') }}>
                +506
            </option>

        </select>

        @if ($errors->has('phone_country_code_2'))
            <span class="help-block">
            <strong>{{ $errors->first('phone_country_code_2') }}</strong>
        </span>
        @endif
    </div>
    <div class="col-sm-7">
        <input type="text" class="form-control" name="phone_number_2" placeholder="Celular"
               value="{{ isset($patient) ? $patient->phone_number_2 : old('phone_number_2') }}">
        @if ($errors->has('phone_number_2'))
            <span class="help-block">
            <strong>{{ $errors->first('phone_number_2') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group">
    <label for="email" class="col-sm-2 control-label">Email</label>

    <div class="col-sm-10">
        <input type="email" class="form-control" name="email" placeholder="Email"
               value="{{ isset($patient) ? $patient->email : old('email') }}">
        @if ($errors->has('email'))
            <span class="help-block">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group">
    <label for="province" class="col-sm-2 control-label">Provincia</label>

    <div class="col-sm-10">
        <select id="provincia" class="form-control select2" style="width: 100%;" name="province"
                placeholder="-- Selecciona provincia --">

            <option value="5" {{ isset($patient) ? $patient->province == '5' ? 'selected' : '' : '' }}>Guanacaste
            </option>
            <option value="1" {{ isset($patient) ? $patient->province == '1' ? 'selected' : '' : '' }}>San Jose</option>
            <option value="4" {{ isset($patient) ? $patient->province == '4' ? 'selected' : '' : '' }}>Heredia</option>
            <option value="7" {{ isset($patient) ? $patient->province == '7' ? 'selected' : '' : '' }}>Limon</option>
            <option value="3" {{ isset($patient) ? $patient->province == '3' ? 'selected' : '' : '' }}>Cartago</option>
            <option value="6" {{ isset($patient) ? $patient->province == '6' ? 'selected' : '' : '' }}>Puntarenas
            </option>
            <option value="2" {{ isset($patient) ? $patient->province == '2' ? 'selected' : '' : '' }}>Alajuela</option>
        </select>

        @if ($errors->has('province'))
            <span class="help-block">
            <strong>{{ $errors->first('province') }}</strong>
        </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="canton" class="col-sm-2 control-label">Canton</label>

    <div class="col-sm-10">
        <select id="canton" class="form-control select2" style="width: 100%;" name="canton"
                placeholder="-- Selecciona Canton --">

            <option value=""></option>

        </select>

        @if ($errors->has('canton'))
            <span class="help-block">
            <strong>{{ $errors->first('canton') }}</strong>
        </span>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="district" class="col-sm-2 control-label">Distrito</label>

    <div class="col-sm-10">
        <select id="distrito" class="form-control select2" style="width: 100%;" name="district"
                placeholder="-- Selecciona Canton --">

            <option value=""></option>

        </select>

        @if ($errors->has('district'))
            <span class="help-block">
            <strong>{{ $errors->first('district') }}</strong>
        </span>
        @endif
    </div>
</div>
{{--<div class="form-group">--}}
{{--    <label for="office_city" class="col-sm-2 control-label">Ciudad</label>--}}

{{--    <div class="col-sm-10">--}}
{{--        <input type="text" class="form-control" name="city" placeholder="Ciudad"--}}
{{--               value="{{ isset($patient) ? $patient->city : old('city') }}">--}}
{{--        @if ($errors->has('city'))--}}
{{--            <span class="help-block">--}}
{{--            <strong>{{ $errors->first('city') }}</strong>--}}
{{--        </span>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="form-group">--}}
{{--    <label for="address" class="col-sm-2 control-label">Dirección</label>--}}

{{--    <div class="col-sm-10">--}}
{{--        <input type="text" class="form-control" name="address" placeholder="Dirección"--}}
{{--               value="{{ isset($patient) ? $patient->address : old('address') }}">--}}
{{--        @if ($errors->has('address'))--}}
{{--            <span class="help-block">--}}
{{--            <strong>{{ $errors->first('address') }}</strong>--}}
{{--        </span>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--</div>--}}
<div class="form-group">
    <label for="conditions" class="col-sm-2 control-label">Padecimientos</label>

    <div class="col-sm-10">
        <select-media-tags :tags="{{ json_encode($tags) }}"
                           :patient-conditions="{{ json_encode(isset($patient) ? $patient->conditions : []) }}"></select-media-tags>

        @if ($errors->has('conditions'))
            <span class="help-block">
            <strong>{{ $errors->first('conditions') }}</strong>
        </span>
        @endif
    </div>
</div>
@if(auth()->user()->isMedic() || auth()->user()->isClinic() || auth()->user()->isAssistant())
    <div class="form-group">
        <label for="discount_id" class="col-sm-2 control-label">Descuento Empresarial</label>

        <div class="col-sm-10">
            <select class="form-control select2" style="width: 100%;" name="discount_id"
                    placeholder="-- Selecciona Descuento Empresarial --">
                <option value=""></option>
                @foreach($discounts as $discount)
                    <option value="{{ $discount->id }}" {{ isset($patient) ? $patient->hasDiscount($discount->id) ? 'selected' : '' : '' }}>{{ $discount->name }}
                        - {{ $discount->tarifa }}%
                    </option>
                @endforeach

            </select>

            @if ($errors->has('discount_id'))
                <span class="help-block">
            <strong>{{ $errors->first('discount_id') }}</strong>
        </span>
            @endif
        </div>
    </div>
@endif
@if(!isset($patient))
    {{-- <div class="form-group">
        <label for="password" class="col-sm-2 control-label">Contraseña (Accesso a la plataforma): </label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="password" placeholder="Si dejas este espacio en blanco la contraseña sera el numero de teléfono" value="">
            <span class="label label-warning">Recordar al usuario que su perfil queda creado y que esta es su clave genérica.</span>
        </div>

    </div> --}}
    <div class="form-group">
        <label for="contact_name" class="col-sm-2 control-label">Contacto de referencia</label>

        <div class="col-sm-10">
            <input type="text" class="form-control" name="contact_name" placeholder="Nombre contacto"
                   value="{{ old('contact_name') }}">

        </div>
    </div>
    <div class="form-group">
        <label for="phone" class="col-sm-2 control-label">Teléfono referencia</label>

        <div class="col-sm-3">
            <select class="form-control" style="width: 100%;" name="contact_phone_country_code" required>

                <option value="+506" {{ old('contact_phone_country_code') == '+506' ? 'selected' : '' }}>+506</option>

            </select>


        </div>
        <div class="col-sm-7">
            <input type="text" class="form-control" name="contact_phone_number" placeholder="Teléfono"
                   value="{{ old('contact_phone_number') }}">
            @if ($errors->has('contact_phone_number'))
                <span class="help-block">
            <strong>{{ $errors->first('contact_phone_number') }}</strong>
        </span>
            @endif
        </div>
    </div>
    {{-- <div class="accounts-invitation form-group row">

        <div class="col-sm-12 col-md-6">
            <h4>Asignar a cuenta</h4>
            <div class="input-group">
                <span class="input-group-addon" title="En caso de no tener correo propio llenar con correo de un usuario inscrito en la plataforma "><i class="fa fa-question"></i></span>
                <input type="text" class="form-control" name="account" placeholder="Correo de la cuenta de usuario" value="{{ old('account') }}" title="En caso de no tener correo propio llenar con correo de un usuario inscrito en la plataforma ">
            </div>

            @if ($errors->has('account'))
            <span class="help-block">
                <strong>{{ $errors->first('account') }}</strong>
            </span>
            @endif
        </div>
        <div class="col-sm-12 col-md-6">

            <h4>Enviar invitación a encargado</h4>
            <div class="input-group">
                <span class="input-group-addon" title="Tel. de la persona encargada del paciente"><i class="fa fa-question"></i></span>
                <input type="text" class="form-control" name="invitation" placeholder="Tel. del encargado del paciente" value="{{ old('invitation') }}" title="Tel. de la persona encargada del paciente">
            </div>

            @if ($errors->has('invitation'))
            <span class="help-block">
                <strong>{{ $errors->first('invitation') }}</strong>
            </span>
            @endif
        </div>
    </div> --}}

@endif
@push('scripts')
    <script>
        $(function () {

            var provincias = $('#provincia'),
                cantones = $('#canton'),
                distritos = $('#distrito');


            cantones.empty();
            distritos.empty();


            provincias.change(function () {
                var $this = $(this);
                cantones.empty();
                distritos.empty();

                cantones.append('<option value="">-- Canton --</option>');
                $.each(window.provincias, function (index, provincia) {

                    if (provincia.id == $this.val()) {
                        $.each(provincia.cantones, function (index, canton) {

                            cantones.append('<option value="' + canton.title + '">' + canton.title + '</option>');
                        });
                    }
                });

            });
            cantones.change(function () {
                var $this = $(this);
                distritos.empty();
                distritos.append('<option value=""> -- Distrito --</option>');
                $.each(window.provincias, function (index, provincia) {

                    if (provincia.id == provincias.val())
                        $.each(provincia.cantones, function (index, canton) {

                            if (canton.title == $this.val()) {
                                $.each(canton.distritos, function (index, distrito) {

                                    distritos.append('<option value="' + distrito.title + '">' + distrito.title + '</option>');
                                });

                            }
                        });
                });

            });
            // distritos.change(function () {
            //     var $this = $(this);
            //     barrios.empty();
            //     barrios.append('<option value=""> -- Barrio --</option>');
            //     $.each(window.provincias, function (index, provincia) {
            //
            //         if (provincia.id == provincias.val())
            //             $.each(provincia.cantones, function (index, canton) {
            //
            //                 if (canton.id == $this.val()) {
            //                     $.each(canton.distritos, function (index, distrito) {
            //
            //                         if (distrito.id == $this.val()) {
            //                             $.each(distrito.barrios, function (index, barrio) {
            //
            //                                 barrios.append('<option value="' + barrio.id + '">' + barrio.title + '</option>');
            //                             });
            //                         }
            //                     });
            //
            //                 }
            //             });
            //     });
            //
            // });

            @if($patient)
            setTimeout(function () {

                $('#provincia option[value="{{ $patient->provincia }}"]').attr("selected", true);
                $('#provincia').change();
                $('#canton option[value="{{ $patient->canton }}"]').attr("selected", true);
                $('#canton').change();
                $('#distrito option[value="{{ $patient->district }}"]').attr("selected", true);
                $('#distrito').change();

            }, 100);
            @endif
        });
    </script>
@endpush