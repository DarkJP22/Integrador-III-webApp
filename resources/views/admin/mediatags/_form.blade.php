<div class="form-group">
    <label for="name" class="col-sm-2 control-label">Nombre</label>

    <div class="col-sm-10">
        <input type="text" class="form-control" name="name" value="{{ isset($tag) ? $tag->name : old('name') }}" required>
        @if ($errors->has('name'))
        <span class="help-block">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
    </div>
</div>



<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="/mediatags" class="btn btn-default">Regresar</a>
    </div>
</div> 