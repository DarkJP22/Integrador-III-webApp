@extends('layouts.medics.app')

@section('content')
    <section class="content-header">
      <h1>Comision Doctor Blue</h1>
    
    </section>

    <section class="content">
          <div class="row">
            <div class="col-md-12">
              <div class="panel">
                <div class="panel-body">
                @include('agenda._buttons')
                
                </div>
              
              </div>
              
            </div>
          </div>
        <div class="row">
          <div class="col-xs-12">
            <div class="box">
              <div class="box-header">
             
              
                  <form action="/incomes" method="GET" autocomplete="off">
                      <div class="form-group">
                       
                          
                          <div class="col-sm-3 flatpickr">
                                           
                              
                              <input data-input type="text" name="start" class="date form-control" placeholder="Fecha Desde" value="{{ $search['start'] }}">
                                    
                                
                            
                        
                            
                        </div>
                        <div class="col-sm-3 flatpickr">
                            
                                
                              <input data-input type="text" class="date form-control" placeholder="Fecha Hasta" name="end" value="{{ $search['end'] }}">
                                   
                            
                        </div>
                         <button type="submit" class="btn btn-primary">Buscar</button>
                        
                      </div>
                   
                  </form>
                  <div class="box-tools">
                  </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body table-responsive">
                <table class="table table-hover">
                   <thead>
                    <tr>
                        
                        <th>Rubro</th>
                        <th>Cantidad</th>
                        <th>Comisión</th>
                        <th>Total</th>
                    
                        
                        
                    </tr>
                    </thead>
                    <tbody>
                   @foreach($data as $item)
                    <tr>
                    
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ money($item['price']) }}</td>
                        <td>{{ money($item['amount']) }}</td>
                    
                        
                    </tr>
                   @endforeach
                
                    
                    </tbody>
                 
                </table>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
        </div>

    </section>


@endsection

