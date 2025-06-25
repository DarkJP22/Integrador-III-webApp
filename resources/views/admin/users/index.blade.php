@extends('layouts.admins.app')

@section('content')
    <section class="content-header">
        <h1>Usuarios</h1>

    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">


                        <div class="box-toolsdd filters">
                            <a href="/admin/users/create" class="btn btn-primary">Crear</a>
                            <form action="/admin/users" method="GET" autocomplete="off">

                                <div class="form-group">

                                    <div class="col-xs-12 col-sm-2">
                                        <div class="form-group">
                                            <select class="form-control select2" style="width: 100%;" id="role" name="role" placeholder="-- Selecciona Tipo --">
                                                <option value=""></option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}" @if (isset($search['role']) && $search['role'] == $role->name) {{ 'selected' }} @endif> {{ $role->name }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="input-group input-group">


                                            <input type="text" name="q" class="form-control pull-right" placeholder="Buscar..." value="{{ isset($search) ? $search['q'] : '' }}">
                                            <div class="input-group-btn">

                                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                            </div>


                                        </div>
                                    </div>

                                </div>






                            </form>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding" id="no-more-tables">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cédula</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol(tipo)</th>
                                    <th>Estatus</th>
{{--                                    <th>Comisiona Lab</th>--}}
                                    <th>Subscripción</th>
                                    <th>Tipo de profesional</th>
                                    <th>Titulos</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @foreach ($users as $user)
                                <tr>

                                    <td data-title="ID">{{ $user->id }}</td>

                                    <td data-title="Cédula">{{ $user->ide }} </td>
                                    <td data-title="Nombre">{{ $user->name }} </td>

                                    <td data-title="Email">{{ $user->email }}</td>
                                    <td data-title="Email">
                                        {{ $user->roles->first()->name }} <br>

                                        @foreach ($user->offices as $office)
                                            <span class="label label-warning">{{ $office->name }}</span>
                                        @endforeach
                                    </td>
                                    <td data-title="Estatus">

                                        @if ($user->active)
                                            <button type="submit" class="btn btn-success btn-xs" form="form-active-inactive" formaction="{!! URL::route('users.inactive', [$user->id]) !!}">Active</button>
                                        @else
                                            <button type="submit" class="btn btn-danger btn-xs " form="form-active-inactive" formaction="{!! URL::route('users.active', [$user->id]) !!}">Inactive</button>
                                        @endif

                                    </td>

{{--                                    <td data-title="Comisiona">--}}

{{--                                        <button @class([--}}
{{--                                            'btn',--}}
{{--                                            'btn-xs',--}}
{{--                                            'btn-success' => $user->commission_affiliation,--}}
{{--                                            'btn-danger' => !$user->commission_affiliation,--}}
{{--                                        ]) type="submit" form="form-commission" formaction="{!! URL::route('users.commission', [$user->id]) !!}">--}}
{{--                                            {{ $user->commission_affiliation ? 'Si' : 'No' }}--}}
{{--                                        </button>--}}





{{--                                    </td>--}}
                                    <td data-title="Subscripción">


                                        <current-subscription :user="{{ $user }}" :plans="{{ $plans }}"></current-subscription>




                                    </td>
                                    <td data-title="Tipo de Profesional">
                                        {{ $user->type_of_health_professional?->label() }}
                                    </td>
                                    <td data-title="Titulos">


                                            <a data-target="#modalTitulosDetail" class="btn btn-xs btn-success"
                                               data-toggle="modal"
                                               data-user="{{ $user->id }}"
                                               href="#"
                                               title="Detalle">Ver Títulos
                                            </a>


                                    </td>
                                    <td data-title="" style="padding-left: 5px;">



                                        <a href="{{ url('/admin/users/' . $user->id) }}" class="btn btn-primary" title="Editar"><i class="fa fa-edit"></i> Editar</a>
                                        <button type="submit" class="btn btn-warning" form="form-close-account" formaction="{!! url('/admin/users/' . $user->id.'/cancel-account') !!}">Dar de baja</button>
                                        <button type="submit" class="btn btn-danger" form="form-delete" formaction="{!! url('/admin/users/' . $user->id) !!}"><i class="fa fa-remove"></i></button>



                                    </td>
                                </tr>
                            @endforeach
                            <tr>

                                @if ($users)
                                    <td colspan="7" class="pagination-container">{!! $users->appends(['q' => $search['q'], 'role' => $search['role']])->render() !!}</td>
                                @endif


                            </tr>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

    </section>
    <div id="modalTitulosDetail" aria-labelledby="modalTitulosDetail" class="modal fade" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header tw-bg-teal-500 tw-text-white">
                    <div class="tw-flex tw-justify-between tw-items-center">

                        <h4 id="modalTitulosDetailLabel" class="modal-title">Detalle Títulos
                        </h4>
                        <div class="">

                        </div>
                    </div>

                </div>

                <div class="modal-body ">
                    sss
                </div>
                <div class="modal-footer">


                    <button class="btn btn-default" data-dismiss="modal" type="button">
                        Cerrar
                    </button>


                </div>
            </div>
        </div>
    </div>

    <form method="post" id="form-delete" data-confirm="Estas Seguro?">
        <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
    </form>
    <form method="post" id="form-close-account" data-confirm="Estas Seguro?">
        <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
    </form>
    <form method="post" id="form-active-inactive">
        {{ csrf_field() }}
    </form>
    <form method="post" id="form-commission">
        {{ csrf_field() }}
    </form>
    <form method="post" id="form-agenda-gps">
        {{ csrf_field() }}
    </form>
    <!-- <form method="post" id="form-addToYourPatients" data-confirm="Estas Seguro?">
          {{ csrf_field() }}
        </form> -->
@endsection
@push('scripts')
    <script>
        $(function() {
            $('#role').on('change', function(e) {


                $(this).parents('form').submit();

            });
            $('#modalTitulosDetail').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var user = button.data('user');
                var modal = $(this);
                modal.find('.modal-body').html('Cargando...');
                $.get('/admin/users/'+user+'/titles', function(data) {
                    let div = document.createElement('div');
                    div.classList.add('tw-grid', 'tw-grid-cols-3', 'tw-gap-4');
                    data.forEach(function(item){
                        let a = document.createElement('a');
                        a.href = item.url;
                        a.target = '_blank';
                        a.innerHTML = `<img src="${item.url}" alt="" class="tw-w-full" />`;
                        div.appendChild(a);
                    })
                   // div.innerHTML = data;
                    modal.find('.modal-body').html(div);
                });
            });
        });
    </script>
@endpush
