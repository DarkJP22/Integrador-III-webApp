@extends('layouts.laboratories.app')

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
                        <form action="/lab/patients" method="GET" autocomplete="off">
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
                                <div class="col-sm-3">
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

                                </div>
                            </div>
                        </form>
                        <div class="box-tools">

                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding" id="no-more-tables">
                        <form action="/patients/marketing" method="POST" id="send-marketing"
                              enctype="multipart/form-data">
                            @csrf
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" name="select_all_patients" id="select_all_patients"/>
                                        <input type="hidden" name="select_action" id="select_action"/>
                                    </th>
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
                                        <td data-title="Marketing">
                                            <input type="checkbox" name="patients[]" value="{{ $patient->id }}"
                                                   class="chk-item">
                                        </td>
                                        <!-- <td data-title="ID">{{ $patient->id }}</td> -->

                                        <td data-title="Nombre">

                                            <a href="{{ url('/general/patients/' . $patient->id) }}"
                                               title="{{ $patient->first_name }}">{{ $patient->first_name }} {{ $patient->last_name }}</a>


                                        </td>
                                        <td data-title="Teléfono">{{ $patient->phone_number }}</td>
                                        <td data-title="Email">{{ $patient->email }}</td>
                                        <td data-title="Dirección">{{ $patient->provinceAddress }}</td>
                                        <td data-title="" style="padding-left: 5px;">
                                            <div class="tw-flex tw-space-x-2 tw-flex-wrap">
                                                @can('update', $patient)
                                                    <a href="{{ url('/general/patients/' . $patient->id) }}"
                                                       class="btn btn-primary" title="Información básica"><i
                                                                class="fa fa-eye"></i> Información básica</a>
                                                @endcan
                                                <share-app-link :patient="{{ $patient }}"
                                                                default-message="Hola {{ $patient->first_name }}, le saludamos de {{ $office->name }}. Hemos cargado el resultado de su análisis en Doctor Blue, puede consultarlo en https://play.google.com/store/apps/details?id=com.cittacr.app NOTA: Debe registrarse como paciente. Si no es usuaria de Android, puede acceder a la web: https://mobile.cittacr.com"></share-app-link>
                                            </div>


                                        </td>
                                    </tr>
                                @endforeach
                                @if ($patients)
                                    <td colspan="6"
                                        class="pagination-container">{!! $patients->appends(['q' => $search['q']])->render() !!}</td>
                                @endif
                            </table>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <b>Marketing:</b> <small>Selecciona los paciente que deseas enviarles
                                        información</small>
                                </div>
                                <div class="panel-body">

                                    <div class="marketing-info row">
                                        <div class="form-group">

                                            <div class="col-sm-3">


                                                <input type="text" name="title" class="form-control"
                                                       placeholder="Titulo: Nuevo anuncio..." value="">


                                            </div>
                                            <div class="col-sm-8">


                                                <input type="text" name="body" class="form-control"
                                                       placeholder="Descripción: Te ha llegado una nueva notificacion de informacion de interes, revisala en el panel de notificaciones!!"
                                                       value="">


                                            </div>
                                        </div>
                                    </div>
                                    <div></div>
                                    <div class="marketing row">
                                        <div class="col-sm-12">
                                            <div class="marketing-flex">
                                                <div class="marketing-file">

                                                    <input type="file" name="file" id="file"
                                                           class="inputfile inputfile-1"
                                                           data-multiple-caption="{count} files selected"/>
                                                    <label for="file">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17"
                                                             viewBox="0 0 20 17">
                                                            <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/>
                                                        </svg>
                                                        <span>Escoger archivo&hellip;</span></label>


                                                </div>
                                                <div class="button-file-send">

                                                    <button type="submit"
                                                            class="btn-multiple btn btn-danger btn-sm btn-flat btn-block"
                                                            data-action="send-marketing" title="Enviar"
                                                            form="send-marketing" formaction="/patients/marketing"><i
                                                                class="fa fa-send"></i> Enviar anuncio
                                                    </button>


                                                </div>
                                            </div>

                                        </div>
                                    </div>


                                </div>

                            </div>


                        </form>
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
@endsection
@push('scripts')
    <script>
        $(function () {
            var input = document.querySelector('.inputfile');
            var label = input.nextElementSibling,
                labelVal = label.innerHTML;

            input.addEventListener('change', function (e) {
                var fileName = '';
                if (this.files && this.files.length > 1)
                    fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                else
                    fileName = e.target.value.split('\\').pop();

                if (fileName)
                    label.querySelector('span').innerHTML = fileName;
                else
                    label.innerHTML = labelVal;
            });

            // Firefox bug fix
            input.addEventListener('focus', function () {
                input.classList.add('has-focus');
            });
            input.addEventListener('blur', function () {
                input.classList.remove('has-focus');
            });


            $('#province').on('change', function (e) {


                $(this).parents('form').submit();

            });

            var chkItem = $('.chk-item'),
                chkSelectAll = $('#select_all_patients');

            chkSelectAll.on('click', function (e) {
                var c = this.checked;
                $(':checkbox').prop('checked', c);
            });

            $('.btn-multiple').on('click', function (e) {

                //if($('input[name="file"]').val()){

                var action = $(this).data('action');

                chkSelectAll.val(action);
                $('#select-action').val(action);

                (verificaChkActivo(chkItem)) ? $('#send-marketing').submit() : alert('Seleccione al menos un paciente de la lista');

                // }else {
                //   alert('Seleccione un archivo');

                // }

                e.preventDefault();

            });

            function verificaChkActivo(chks) {
                var state = false;

                chks.each(function () {

                    if (this.checked) {

                        state = true;


                    }

                });

                return state;
            }
        });
    </script>
@endpush
