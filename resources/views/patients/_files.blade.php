
<div class="box box-default">

    <div class="box-header with-border">
      <h3 class="box-title">Archivos o imágenes</h3>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
        </button>
      </div>
      <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <ul id="files-list" class="todo-list ui-sortable">
        @foreach($files as $file)
        <li>
         
          <a href="{{ $file->name }}" title="{{ $file->name }}" target="_blank"><span class="text">{{ $file->name }}</span></a>
          
          <div class="tools">
          @if(! isset($read))
            <i class="fa fa-trash-o delete" data-file="{{ $file->name }}" data-id="{{ $file->id }}"></i>
            @endif
          </div>
        </li>
        @endforeach 
      
      </ul>
        
       
      
        
    </div>
    <!-- /.box-body -->
</div>