<form method="POST" action="{{ url('/account/cancel') }}" class="form-horizontal" data-confirm="Estas Seguro?">
    {{ csrf_field() }}<input name="_method" type="hidden" value="DELETE">
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-danger">Cancelar Cuenta</button>
        </div>
    </div>
</form>