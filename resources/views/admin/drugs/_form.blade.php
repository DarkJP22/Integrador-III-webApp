

<div class="form-group">
    <label for="name" class="col-sm-2 control-label">Nombre</label>
    <div class="col-sm-10">
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" id="name" name="name" placeholder="Nombre + Fuerza / Concentración + Presentación + Cantidad / Volumen + Marca" value="{{ isset($drug) ? $drug->name : old('name') }}">
        @if ($errors->has('name'))
            <div class="error invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </div>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="laboratory" class="col-sm-2 control-label">Laboratorio</label>
    <div class="col-sm-10">

        <input type="text" class="form-control {{ $errors->has('laboratory') ? ' is-invalid' : '' }}" id="laboratory" name="laboratory" placeholder="" value="{{ isset($drug) ? $drug->laboratory : old('laboratory') }}">


        @if ($errors->has('laboratory'))
            <div class="error invalid-feedback">
                <strong>{{ $errors->first('laboratory') }}</strong>
            </div>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="presentation" class="col-sm-2 control-label">Presentación</label>
    <div class="col-sm-10">

        <select class="form-control" name="presentation">
            <option value=""></option>
            @foreach(\App\Drug::PRESENTATIONS as $presentation)
                <option value="{{ $presentation }}" {{ isset($drug) && $drug->presentation == $presentation ? 'selected' : '' }}>{{ $presentation }}</option>
            @endforeach

        </select>

        @if ($errors->has('presentation'))
            <div class="error invalid-feedback">
                <strong>{{ $errors->first('presentation') }}</strong>
            </div>
        @endif
    </div>
</div>
<div class="form-group">
    <label for="active_principle" class="col-sm-2 control-label">Principio activo</label>
    <div class="col-sm-10">

        <input type="text" class="form-control {{ $errors->has('active_principle') ? ' is-invalid' : '' }}" id="active_principle" name="active_principle" placeholder="" value="{{ isset($drug) ? $drug->active_principle : old('active_principle') }}">


        @if ($errors->has('active_principle'))
            <div class="error invalid-feedback">
                <strong>{{ $errors->first('active_principle') }}</strong>
            </div>
        @endif
    </div>
</div>

<div class="form-group">
    <label for="image_path" class="col-sm-2 control-label">Imagen</label>
    <div class="col-sm-10">

        <input type="file" class="form-control {{ $errors->has('image_path') ? ' is-invalid' : '' }}" id="image_path" name="image_path" accept="image/*" >


        @if ($errors->has('image_path'))
            <div class="error invalid-feedback">
                <strong>{{ $errors->first('image_path') }}</strong>
            </div>
        @endif
        @isset($drug)
            <img src="{{ $drug->image_url }}" alt="" style="width: 100px;">
        @endisset
    </div>
</div>

<div class="form-group">
    <label for="status" class="col-sm-2 control-label">Estado</label>
    <div class="col-sm-10">

        <select name="status" id="status" class="form-control {{ $errors->has('status') ? ' is-invalid' : '' }}">
            @foreach($statuses as $status)

            <option value="{{ $status['id'] }}" {{ isset($drug) && $drug->status == $status['id'] ? 'selected' : '' }}>{{ $status['name'] }}</option>
            @endforeach

        </select>


        @if ($errors->has('status'))
            <div class="error invalid-feedback">
                <strong>{{ $errors->first('status') }}</strong>
            </div>
        @endif
    </div>
</div>



@if(isset($drug))
    @can('update', $drug)
        <button type="submit" class="btn btn-primary">Guardar</button>
    @endcan
@else
    <button type="submit" class="btn btn-primary">Guardar</button>
@endif
<a href="/drugs" class="btn btn-default"> Regresar</a>

