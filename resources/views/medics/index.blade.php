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

                    <form method="GET" action="{{ url('/medics') }}" class="form-horizontal">


                        <input type="hidden" name="typeOfSearch" value="{{ $search['typeOfSearch'] }}">

                        <div class="row">
                            @if($search['typeOfSearch'] == 'specialist')
                            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                                @else
                                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                                    @endif
                                    <div class="form-group">
                                        <label for="q" class="control-label col-sm-2">Médico</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="q" class="form-control pull-right" placeholder="Nombre del médico" value="{{ isset($search['q']) ? $search['q'] : ''}}">
                                        </div>
                                    </div>
                                </div>
                                @if($search['typeOfSearch'] == 'specialist')
                                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                                    <div class="form-group">
                                        <label for="speciality" class="control-label col-sm-2 col-md-3">Especialidad</label>
                                        <div class="col-sm-10 col-md-9">

                                            <select class="form-control select2" style="width: 100%;" name="speciality" required>
                                                <option value=""></option>
                                                @foreach ($specialities as $speciality)
                                                <option value="{{ $speciality->id }}" {{ isset($search['speciality']) ?  $search['speciality'] == $speciality->id ? 'selected' : '' : '' }}>{{ $speciality->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endif

                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="box box-default  box-search-filters box-solid">
                                        <div class="box-header with-border ">
                                            <h3 class="box-title">Parámetros de busqueda</h3>

                                            <div class="box-tools">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                            <!-- /.box-tools -->
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body ">
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
                                                                <label for="" class="control-label col-xs-12 col-sm-5 col-md-5 col-lg-6">Cerca de aquí <img class="loader-geo hide" src="/img/loading.gif" alt="Cargando" /></label>
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
                                        <!-- /.box-body -->
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary btn-search" style="width: 100%;margin-top: 1rem;"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding" id="no-more-tables">
                    @if ($items)
                    @if (!count($items))
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

                            </tr>
                        </thead>
                        @php($phoneCallCenter = getCallCenterPhone())
                        @foreach($items as $item)
                    
                        <tr>

                            <td data-title="Nombre">
                                <div class="box box-default">
                                    <div class="box-body box-profile text-center">
                                        <img src="{{ $item->avatar_path }}" alt="{{ $item->name }}" class="profile-user-img img-responsive img-circle">

                                        <h3 class="profile-username text-center"> Dr(a). {{ $item->name }}</h3>

                                        <p class="text-muted text-center">
                                            @if($item->specialities->count())
                                            @foreach($item->specialities as $speciality) <small class="center text-center"><b>{{ $speciality->name }}</b></small> @endforeach
                                            @else
                                            <small class="center text-center"><b>Médico General</b></small>
                                            @endif

                                        </p>
                                        <h3>Teléfono: {{ $phoneCallCenter }} </h3>

                                    </div>
                                    <!-- /.box-body -->
                                </div>


                            </td>
                            <td data-title="Lugar">
                                @forelse($item->offices as $office)
                                <div class="box box-default">
                                    <div class="box-body box-profile text-center">
                                        <div class="td-lugar">
                                            <div class="td-lugar-name">
                                                <span>{{ $office->name }}</span> <br>
                                                <b>Horario Semana Actual:</b><br>

                                                @foreach($item->schedules as $schedule)

                                                @if($schedule->office_id == $office->id && Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $schedule->date) >= Carbon\Carbon::now()->startOfWeek() && Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $schedule->date) <= Carbon\Carbon::now()->endOfWeek())

                                                    <span class="label label-warning"> {{ dayName(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $schedule->date)->dayOfWeek) }} - {{ Carbon\Carbon::parse($schedule->start)->toTimeString() }} - {{ Carbon\Carbon::parse($schedule->end)->toTimeString() }}</span>

                                                    @endif
                                                    @endforeach


                                            </div>

                                            <div class="td-lugar-info">
                                                <p>
                                                    <span>{{ $office->province_name }}. {{ $office->address }}</span>
                                                </p>
                                                <p>
                                                    <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#myModal" data-address="{{ $item->name }} - Direccion: {{ $office->province }}, {{ $office->canton }}. {{ $office->address }} - Tel: {{ $item->phone }}" data-lat="{{ $office->lat }}" data-lon="{{ $office->lon }}">
                                                        <i class="fa fa-address"></i> Compartir ubicación
                                                    </button> <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#locationModal" data-lat="{{ $office->lat }}" data-lon="{{ $office->lon }}"><i class="fa fa-address"></i> Abrir ubicación
                                                    </button>
                                                    @if($office->active)
                                                    <a href="{{ url('/medics/'.$item->id.'/offices/'.$office->id .'/reservation') }}" class="btn btn-primary btn-xs"><i class="fa fa-calendar"></i> Reservar cita</a>
                                                    @endif
                                                </p>
                                            </div>

                                        </div>



                                    </div>
                                </div>
                                @empty
                                <div class="text-center">

                                    <h3 class="text-danger">No tiene Consultorio Disponible</h3>
                                </div>
                                @endforelse


                            </td>
                            @if(isset($search['lat']) && $search['lat'] != '')
                            <td data-title="Distancia">
                                Aprox. {{ number_format($item->distance, 2, '.', ',')  }} Km
                            </td>
                            @endif

                        </tr>
                  
                        @endforeach
                        <tr>


                            <td colspan="4" class="pagination-container">{!!$items->appends(['q' => $search['q'],'speciality' => $search['speciality'] , 'province' => $search['province'],'canton' => $search['canton'],'district' => $search['district'],'lat' => $search['lat'],'lon' => $search['lon'], 'typeOfSearch' => $search['typeOfSearch'], 'date' => $search['date']])->render()!!}</td>



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
    $(function() {
        //Initialize Select2 Elements
        $(".select2").select2();

    });
</script>



@endpush