@extends('layouts.pharmacies.app')

@section('content')
    <section class="content-header">
        <h1>Pacientes</h1>

    </section>

    <section class="content">

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">

                        <a href="{{ url('/general/patients/create') }}" class="btn btn-primary">Nuevo paciente</a>
                        <form action="/pharmacy/patients" method="GET" autocomplete="off">
                            <div class="form-group">


                                <div class="col-sm-3">
                                    <div class="input-group input-group">


                                        <input type="text" name="q" class="form-control pull-right"
                                               placeholder="Buscar..." value="{{ isset($search) ? $search['q'] : '' }}">
                                        <div class="input-group-btn">

                                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i>
                                            </button>
                                        </div>


                                    </div>
                                </div>
                                {{--<div class="col-sm-3">
                                    <select name="province" id="province" class="form-control">
                                        <option value="">-- Filtro por Provincia --</option>
                                        <option value="1" {{ isset($search['province']) ? ($search['province'] == '1' ? 'selected' : '') : '' }}>
                                            San Jose
                                        </option>
                                        <option value="2" {{ isset($search['province']) ? ($search['province'] == '2' ? 'selected' : '') : '' }}>
                                            Alajuela
                                        </option>
                                        <option value="3" {{ isset($search['province']) ? ($search['province'] == '3' ? 'selected' : '') : '' }}>
                                            Cartago
                                        </option>
                                        <option value="4" {{ isset($search['province']) ? ($search['province'] == '4' ? 'selected' : '') : '' }}>
                                            Heredia
                                        </option>
                                        <option value="5" {{ isset($search['province']) ? ($search['province'] == '5' ? 'selected' : '') : '' }}>
                                            Guanacaste
                                        </option>
                                        <option value="6" {{ isset($search['province']) ? ($search['province'] == '6' ? 'selected' : '') : '' }}>
                                            Puntarenas
                                        </option>
                                        <option value="7" {{ isset($search['province']) ? ($search['province'] == '7' ? 'selected' : '') : '' }}>
                                            Limon
                                        </option>

                                    </select>

                                </div>--}}
                            </div>
                        </form>
                        <div class="box-tools">

                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding" id="no-more-tables">

                        <table class="table table-hover">
                            <thead>
                            <tr>

                                <!-- <th>ID</th> -->
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Dirección</th>
                                <th></th>
                            </tr>
                            </thead>
                            @foreach ($patients as $patient)
                                <tr>
                                    <!-- <td data-title="Marketing">
                              <input type="checkbox" name="patients[]" value="{{ $patient->id }}" class="chk-item">
                          </td> -->
                                    <!-- <td data-title="ID">{{ $patient->id }}</td> -->

                                    <td data-title="Nombre">

                                        <a href="{{ url('/pharmacy/patients/' . $patient->id) }}"
                                           title="{{ $patient->first_name }}">{{ $patient->first_name }} {{ $patient->last_name }}</a>


                                    </td>
                                    @if ($patient->isPatientOfPharmacy($pharmacy))
                                        <td data-title="Teléfono">{{ $patient->phone_number }}</td>
                                        <td data-title="Email">{{ $patient->email }}</td>
                                        <td data-title="Dirección">{{ $patient->provinceAddress }}</td>
                                        <td data-title="" style="padding-left: 5px;">
                                            <div class="tw-flex tw-space-x-2 tw-flex-wrap">

                                                <a href="{{ url('/pharmacy/patients/' . $patient->id) }}"
                                                   class="btn btn-primary" title="Ver Facturado"><i
                                                            class="fa fa-eye"></i> Información básica</a>

                                                <share-app-link :patient="{{ $patient }}"
                                                                default-message="Hemos creado un expediente clínico para usted. Acceda desde Play Store: {{ getUrlAppPacientesAndroid() }} ó Apple Store: {{ getUrlAppPacientesIos() }}" ></share-app-link>

                                            </div>
                                            {{-- @if ($patient->phone_number)
                                
                                  <share-link-app :patient="{{ $patient }}"></share-link-app>
                              @endif --}}

                                        </td>
                                    @else
                                        <td data-title="Teléfono"></td>
                                        <td data-title="Email"></td>
                                        <td data-title="Dirección"></td>
                                        <td data-title="" style="padding-left: 5px;">
                                            <add-patient-authorization
                                                    :patient="{{ $patient }}"></add-patient-authorization>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            @if ($patients)
                                <td colspan="6"
                                    class="pagination-container">{!! $patients->appends(['q' => $search['q']])->render() !!}</td>
                            @endif
                        </table>


                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

    </section>


    <form method="post" id="form-delete" data-confirm="Estas Seguro?">
        <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
    </form>

    <form method="post" id="form-share-link" data-confirm="Estas Seguro?">
        {{ csrf_field() }}
    </form>
@endsection
@push('scripts')
    <script>
        $(function () {


            $('#province').on('change', function (e) {


                $(this).parents('form').submit();

            });

        });
    </script>
@endpush
