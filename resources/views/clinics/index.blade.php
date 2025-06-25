@extends('layouts.users.app')
@section('header')
  <link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/jquery.jssocials/1.4.0/jssocials.css" />
  <link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/jquery.jssocials/1.4.0/jssocials-theme-flat.css" />
  <link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
@endsection
@section('content')
     

    <section class="content">
        <div class="row">
        
          <div class="col-xs-12">
            
            <div class="box">
              <div class="box-header">
                    <form method="GET" action="{{ url('/clinics') }}" class="form-horizontal">
                      <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                           <div class="form-group">
                            <label for="q" class="control-label col-sm-2">Clínica</label>
                            <div class="col-sm-10">
                              <input type="text" name="q" class="form-control pull-right" placeholder="Nombre de la clínica o consultorio" value="{{ isset($search['q']) ? $search['q'] : ''}}">
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="box box-default box-search-filters box-solid">
                              <div class="box-header with-border ">
                                <h3 class="box-title">Parámetros de busqueda</h3>

                                <div class="box-tools">
                                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                  </button>
                                </div>
                                <!-- /.box-tools -->
                              </div>
                              <!-- /.box-header -->
                              <div class="box-body">
                                    <div class="row">
                                      
                                    
                                      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                        
                                           <div class="row">
                                              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                                 <div class="form-group">
                                                     <label for="" class="control-label col-xs-12 col-sm-5 col-md-5 col-lg-6">Provincia</label>
                                                    <div class="col-sm-12">
                                                    <select class="form-control select2" style="width: 100%;" name="province" placeholder="-- Selecciona provincia --">
                                                      <option></option>
                                                      <option value="5" {{ isset($search['province']) ?  $search['province'] == "5" ? 'selected' : '' : '' }}>Guanacaste</option>
                                                      <option value="1" {{ isset($search['province']) ?  $search['province'] == "1" ? "selected" : "" : "" }}>San Jose</option>
                                                      <option value="4" {{ isset($search['province']) ?  $search['province'] == '4' ? 'selected' : '' : '' }}>Heredia</option>
                                                      <option value="7" {{ isset($search['province']) ?  $search['province'] == '7' ? 'selected' : '' : '' }}>Limon</option>
                                                      <option value="3" {{ isset($search['province']) ?  $search['province'] == '3' ? 'selected' : '' : '' }}>Cartago</option>
                                                      <option value="6" {{ isset($search['province']) ?  $search['province'] == '6' ? 'selected' : '' : '' }}>Puntarenas</option>
                                                      <option value="2" {{ isset($search['province']) ?  $search['province'] == '2' ? 'selected' : '' : '' }}>Alajuela</option>
                                                    </select>
                                                     
                                                    </div>
                                                </div>
                                                  
                                                 
                                              </div>
                                              <div class="col-xs-12 col-sm-4 col-lg-4">
                                                 <div class="form-group">
                                                      <label for="canton" class="control-label col-sm-2">Canton</label>
                                                     <div class="col-sm-12">
                                                        <select class="form-control select2" style="width: 100%;" name="canton" placeholder="-- Selecciona canton --">
                                                          <option></option>
                                                          
                                                        </select>
                                                        <input type="hidden" name="selectedCanton" value="{{ isset($search['canton']) ? $search['canton'] : '' }}">
                                                      </div>
                                                  </div>
                                                <!-- /input-group -->
                                              </div>
                                              <!-- /.col-lg-6 -->
                                              <div class="col-xs-12 col-sm-4 col-lg-4">
                                                 <div class="form-group">
                                                    <label for="district" class="control-label col-sm-2">Distrito</label>
                                                     <div class="col-sm-12">
                                                        <select class="form-control select2" style="width: 100%;" name="district" placeholder="-- Selecciona canton --">
                                                          <option></option>
                                                          
                                                        </select>
                                                        <input type="hidden" name="selectedDistrict" value="{{ isset($search['district']) ? $search['district'] : '' }}">
                                                      </div>
                                                  </div>
                                               
                                                <!-- /input-group -->
                                              </div>
                                              <!-- /.col-lg-6 -->
                                            </div>
                                        </div>
                                          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-5 col-md-6 col-lg-6">
                                                    <div class="form-group">
                                                       <label for="" class="control-label col-xs-12 col-sm-5 col-md-5 col-lg-5">Cerca de aquí <img class="loader-geo hide" src="/img/loading.gif" alt="Cargando"  /> </label>
                                                      <div class="col-sm-6">
                                                          <button type="button" class="btn btn-default btn-geo"><i class="fa fa-"></i>Tu ubicación</button>
                                                      </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-3 col-lg-3">
                                                  <div class="form-group">
                                                       <div class="col-sm-12">
                                                          <div class="input-group">
                                                                <span class="input-group-addon">
                                                                  lat
                                                                </span>
                                                            <input type="text" class="form-control" name="lat" value="{{ isset($search['lat']) ? $search['lat'] : ''}}">
                                                          </div>
                                                        </div>
                                                    </div>
                                                  <!-- /input-group -->
                                                </div>
                                                <!-- /.col-lg-6 -->
                                                <div class="col-xs-12 col-sm-3 col-lg-3">
                                                  <div class="form-group">
                                                       <div class="col-sm-12">
                                                          <div class="input-group">
                                                                <span class="input-group-addon">
                                                                  lon
                                                                </span>
                                                            <input type="text" class="form-control" name="lon" value="{{ isset($search['lon']) ? $search['lon'] : ''}}">
                                                          </div>
                                                        </div>
                                                    </div>
                                                  <!-- /input-group -->
                                                </div>
                                                <!-- /.col-lg-6 -->
                                            </div>
             
                                        </div>
                                    </div>

                              </div>
                            </div>
                        </div>

                  
                        
                          
                      </div>
                         
                      
                       <div class="form-group">
                            <div class="col-sm-12">
                              <button type="submit" class="btn btn-primary btn-search" style="width: 100%;margin-top: 1rem;"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                   </form>
              </div>
              <!-- /.box-header -->
              <div class="box-body table-responsive no-padding" id="no-more-tables">
                 @if ($clinics)
                    @if (!count($clinics))
                      <p class="bg-red disabled text-muted well well-sm no-shadow text-center " style="margin-top: 10px;">
                        No se encontraron elementos con esos terminos de busqueda
                      </p>
                    @else
                      <table class="table table-hover">
                        <thead>
                            <tr>
                          
                              <th>Nombre</th>
                              <th>Lugar</th>
                               @if(isset($search['lat']) && $search['lat'] != '')
                              <th>Distancia</th>
                              @endif
                              <th></th>
                            </tr>
                        </thead>
                        
                        @foreach($clinics as $clinic)
                          <tr>
                           
                            <td data-title="Nombre">
                              {{ $clinic->name }} <br>
                              
                            </td>
                            <td data-title="Lugar">
                               {{ $clinic->province_name }}. {{ $clinic->address }} 

                            </td>
                            @if(isset($search['lat']) && $search['lat'] != '')
                            <td data-title="Distancia">
                                 Aprox. {{ number_format($clinic->distance, 2, '.', ',')  }} Km
                            </td>
                             @endif
                            <td data-title="Compartir">
                              <div class="btn-group">
                             
                                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#myModal" data-address="{{ $clinic->name }} - Direccion: {{ $clinic->province }}, {{ $clinic->canton }}. {{ $clinic->address }}" data-lat="{{ $clinic->lat }}" data-lon="{{ $clinic->lon }}">
                                  <i class="fa fa-address"></i> Compartir Ubicación
                                </button>
                               
                             
                               
                                 <a href="{{ url('/clinics/'.$clinic->id.'/reservation') }}" class="btn btn-primary "><i class="fa fa-calendar"></i> Reservar cita</a>
                              </div>
                            </td>
                          </tr>
                        @endforeach
                        <tr>

                         
                              <td  colspan="3" class="pagination-container">{!!$clinics->appends(['q' => $search['q'], 'province' => $search['province'],'canton' => $search['canton'],'district' => $search['district'],'lat' => $search['lat'],'lon' => $search['lon']])->render()!!}</td>
                         


                          </tr>
                      </table>
                     @endif
                    
                @else
                 
                    <p class="text-muted well well-sm no-shadow text-center" style="margin-top: 10px;">
                      Utiliza al menos uno de los filtros de busqueda para mostrar elementos
                    </p>
                  
                @endif
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
        </div>

    </section>


     @include('layouts.users._shareModals')

@endsection
@push('scripts')
  <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery.jssocials/1.4.0/jssocials.min.js"></script>
 
  <script src="{{ mix('js/search.js') }}"></script>
 <script src="/vendor/select2/js/select2.full.min.js"></script>
  <script>
    $(function () {
      //Initialize Select2 Elements
      $(".select2").select2();

    });
  </script>
  
@endpush
