<div class="box box-solid box-offices">
    <div class="box-header with-border">
        <h4 class="box-title">Copiar Horario de Semana </h4>
        
    </div>
    <div class="box-body">
        
        <form method="POST" action="{{ url('/schedules/copy') }}" class="form-horizontal">
                {{ csrf_field() }}
            
                <div class="form-group">
                        <div class="col-sm-12">
                            <select name="dateweek" id="dateweek" class="form-control">
                                @foreach($weeks as $key => $week)
                                <option value="{{ $week[0] }}">{{ $key }}</option>
                                @endforeach
                               
                            </select>
                        </div>
                </div>
            
    
                <h4>A Semana actual:</h4>
                <p><b>#{{ Carbon\Carbon::now()->weekOfMonth + 1 }} : {{ Carbon\Carbon::now()->startOfWeek()->toDateString() }} | {{ Carbon\Carbon::now()->endOfWeek()->toDateString() }}</b></p> 
        
            <!-- <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                <label>De la Fecha:</label>
                <div class="date col-sm-12">
                    
                    
                    <input type="text" class="form-control pull-right"  name="dateini1" id="datetimepickerini1">
                </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                <label>Hasta Fecha:</label>
                <div class="date col-sm-12">
                    
                    
                    <input type="text" class="form-control pull-right"  name="dateini2" id="datetimepickerini2">
                </div>
                </div>
            </div>
    
                <h3>copiar a:</h3>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                    <label>De la Fecha:</label>
                    <div class="date col-sm-12">
                        
                        
                        <input type="text" class="form-control "  name="datefin1" id="datetimepickerfin1">
                    </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                    <label>Hasta Fecha:</label>
                    <div class="date col-sm-12">
                        
                        
                        <input type="text" class="form-control "  name="datefin2" id="datetimepickerfin2">
                    </div>
                    </div>
                </div> -->
                
            <div class="form-group">
                <div class="col-sm-12">
                <button class="btn btn-secondary" >Copiar</button>
                </div>
            </div>

    </form>

    </div>
    <!-- /.box-body -->
    </div>
