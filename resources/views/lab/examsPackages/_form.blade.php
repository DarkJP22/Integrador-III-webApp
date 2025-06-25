<div class="form-group">
    <label for="name" class="col-sm-2 control-label">Nombre</label>
    <div class="col-sm-10">
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" id="name" name="name" placeholder="" value="{{ isset($examPackage) ? $examPackage->name : old('name') }}">
        @if ($errors->has('name'))
            <div class="error invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </div>
        @endif
    </div>
</div>

<div class="form-group">
    <label for="photo" class="col-sm-2 control-label">Imagen</label>
    <div class="col-sm-10">

        <input type="file" class="form-control" name="photo" placeholder="Logo">
        <span class="">Logo (jpg - png - bmp - jpeg)</span>

        @if ($errors->has('photo'))
            <div class="error invalid-feedback">
                <strong>{{ $errors->first('photo') }}</strong>
            </div>
        @endif
    </div>
</div>
@if(isset($examPackage) && $examPackage->photo_path)
<div>
    <img src="{{ $examPackage->photo_url }}" alt="" class="tw-w-full">
</div>
@endif



<button type="submit" class="btn btn-primary">Guardar</button>

<a href="/lab/exams-packages" class="btn btn-default"> Regresar</a>
