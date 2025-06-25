
<div class="box box-default">

    <div class="box-header with-border">
      <h3 class="box-title">Medicamentos Prescritos por el médico</h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
        </button>
      </div>
      <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body">
    
      <medicines :medicines="{{ $patient->medicines()->where('user_id', auth()->id())->get() }}" :patient="{{ $patient }}"></medicines>
   
        
    </div>
    <!-- /.box-body -->
</div>