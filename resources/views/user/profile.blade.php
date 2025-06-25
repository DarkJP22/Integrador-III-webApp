@extends('layouts.users.app')
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
                <ul class="nav nav-tabs">
                    <li class="{{ isset($tab) ? ($tab =='profile') ? 'active' : '' : 'active' }}"><a href="#profile" data-toggle="tab">Perfil</a></li>
                    <li class="{{ isset($tab) ? ($tab =='patients') ? 'active' : '' : '' }}"><a href="#patients" data-toggle="tab">Pacientes</a></li>
                    <li class="{{ isset($tab) ? ($tab =='authorizations') ? 'active' : '' : '' }}"><a href="#authorizations" data-toggle="tab">Autorizaciones</a></li>
                </ul>
                <div class="tab-content">
                    <div class="{{ isset($tab) ? ($tab =='profile') ? 'active' : '' : 'active' }} tab-pane" id="profile">
                        <form method="POST" action="{{ url('/profiles/'. $profileUser->id) }}" class="form-horizontal">
                            {{ csrf_field() }}<input name="_method" type="hidden" value="PUT">

                            <div class="form-group">
                                <label for="ide" class="col-sm-2 control-label">Identificación</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="ide" name="ide" placeholder="Identificación" value="{{ old('ide') ?: $profileUser->ide }}" required>
                                    @if ($errors->has('ide'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ide') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Nombre</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="{{ old('name') ?: $profileUser->name }}" required>
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone_number" class="col-sm-2 control-label">Teléfono</label>
                                <div class="col-sm-3">
                                    <select class="form-control" style="width: 100%;" name="phone_country_code" required>

                                        <option value="+506" {{  $profileUser->phone_country_code == '+506' ? 'selected' : (old('phone_country_code') == '+506' ? 'selected' : '') }}>+506</option>


                                    </select>

                                    @if ($errors->has('phone_country_code'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone_country_code') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Teléfono" value="{{ old('phone_number') ?: $profileUser->phone_number }}" data-mask required>
                                    @if ($errors->has('phone_number'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone_number') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-2 control-label">Email</label>

                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ old('email') ?: $profileUser->email }}">
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-sm-2 control-label">Cambiar contraseña</label>

                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Escribe la nueva contraseña">
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </div>
                        </form>
                        @include('layouts._cancelAccountForm')
                    </div>
                    <!-- /.tab-pane -->
                    <div class="{{ isset($tab) ? ($tab =='patients') ? 'active' : '' : '' }} tab-pane" id="patients">

                        <patients></patients>

                    </div>
                    <!-- /.tab-pane -->
                    <div class="{{ isset($tab) ? ($tab =='authorizations') ? 'active' : '' : '' }} tab-pane" id="authorizations">
                        
                        <authorizations-medics></authorizations-medics>

                    </div>


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
<script src="/vendor/inputmask/dist/jquery.inputmask.bundle.js"></script>
<script src="/vendor/select2/js/select2.full.min.js"></script>
<script src="/vendor/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script>
    $(function() {


        $("[data-mask]").inputmask();


    });
</script>

@endpush