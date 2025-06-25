
<div class="form-group">
    <label for="category" class="col-sm-2 control-label">Categoria</label>
    <div class="col-sm-10">   
      <select class="form-control" style="width: 100%;" name="category" required>
        
          <option value="facial"  {{ isset($evaluation) && $evaluation->category == 'facial' ? 'selected' : (old('category') == 'facial' ? 'selected' : '') }}>Facial</option>
          <option value="corporal"  {{ isset($evaluation) && $evaluation->category == 'corporal' ? 'selected' : (old('category') == 'corporal' ? 'selected' : '') }}>Corporal</option>
          <option value="depilacion"  {{ isset($evaluation) && $evaluation->category == 'depilacion' ? 'selected' : (old('category') == 'depilacion' ? 'selected' : '') }}>Depilación</option>
      
      </select>
      @if ($errors->has('category'))
          <span class="help-block">
              <strong>{{ $errors->first('category') }}</strong>
          </span>
      @endif
  
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label">Nombre</label>

    <div class="col-sm-10">
      <input type="text" class="form-control" name="name" placeholder="Nombre" value="{{ isset($evaluation) ? $evaluation->name : old('name') }}" required >
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
    </div>
</div>
 
  
 