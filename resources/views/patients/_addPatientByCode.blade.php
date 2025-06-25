<form action="{{ url('/patients/'.$patient->id.'/add') }" method="post" id="form-addToYourPatients" data-confirm="Estas Seguro?" class="form-horizontal">
    {{ csrf_field() }}
    <div class="input-group">
        <div class="input-group-btn">
        <button type="submit" class="btn btn-danger"><i class="fa fa-plus"></i> Agregar a tu lista</button>
        </div>
      
        <input type="text" name="code" placeholder="Código de confirmación" class="form-control" required="required" />
        
    </div>
      @if ($errors->has('code'))
            <span class="help-block">
                <strong>{{ $errors->first('code') }}</strong>
            </span>
        @endif
        
</form> 
