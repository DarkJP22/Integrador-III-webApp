<form method="POST" action="{{  url('pharmacy/affiliations/request/affiliate/'.$profileUser->id.'/accepted-discount')}}" class="form-horizontal">
    @csrf
    @method('POST')

    <div class="form-group">
        <label class="col-sm-2 control-label">Descuento a afiliados</label>
        <div class="col-sm-10">
            <p class="text-muted">
                Se aplicará automáticamente un <strong>10% de descuento</strong> a todos los pacientes que estén registrados como afiliados.
            </p>

            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="accept_affiliates"
                       name="accept_affiliates" value="1"
                       {{ $profileUser->accept_affiliates ? 'checked' : '' }}>
                <label class="custom-control-label" for="accept_affiliates">
                    Acepto aplicar el descuento a pacientes afiliados
                </label>
            </div>

            @if ($errors->has('accept_affiliates'))
                <span class="help-block text-danger">
                    <strong>{{ $errors->first('accept_affiliates') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary btn-lg">Guardar</button>
        </div>
    </div>
</form>
