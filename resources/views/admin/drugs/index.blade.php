@extends('layouts.admins.app')

@section('content')

    <section class="content-header">
        <h1>Medicamentos Globales</h1>

    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <div class="tw-flex tw-justify-between tw-items-center">

                            <a href="{{ url('/drugs/create') }}" class="btn btn-primary">Nuevo Medicamento</a>
                            <form action="/drugs/import" method="POST" enctype="multipart/form-data" class="">
                                @csrf
                                <div class="form-row">
                                    <input type="file" name="file" class="form-control w-auto"/>
                                    @if ($errors->has('file'))
                                        <div class="error invalid-feedback">
                                            <strong>{{ $errors->first('file') }}</strong>
                                        </div>
                                    @endif
                                    <button type="submit" class="btn btn-secondary btn-sm mr-4">Importar</button>
                                </div>


                            </form>
                        </div>
                        <div class="box-toolsdd filters">
                            <form action="/drugs" method="GET" autocomplete="off">

                                <div class="form-group">

                                    <div class="col-xs-12 col-sm-3">
                                        <div class="input-group input-group">


                                            <input type="text" name="q" class="form-control pull-right" placeholder="Buscar por nombre..." value="{{ request('q') }}">
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
                                <th>Nombre</th>
                                <th>

                                    <a href="{{ url('/drugs?sort=image_path&order='. (request('order', 'asc') === 'asc' ? 'desc' : 'asc') ) }}">
                                        Imagen <i @class([
                                                'fa' => true,
                                                'fa-sort' => !request('sort'),
                                                'fa-sort-up' => request('sort') === 'image_path' && request('order') === 'asc',
                                                'fa-sort-down' => request('sort') === 'image_path' && request('order') === 'desc',
                                        ])></i>
                                    </a>
                                </th>
                                <th></th>
                            </tr>
                            </thead>
                            @foreach($drugs as $drug)
                                <tr>

                                    <td data-title="ID">{{ $drug->id }}</td>


                                    <td data-title="Nombre">{{ $drug->name }} </td>

                                    <td data-title="Imagen">
                                        @if($drug->image_url)
                                            <img src="{{ $drug->image_url }}" alt="" width="100" height="100"/>
                                        @endif
                                    </td>


                                    <td data-title="" style="padding-left: 5px;">


                                        <a href="{{ url('/drugs/'.$drug->id. '/edit') }}" class="btn btn-primary" title="Editar"><i class="fa fa-edit"></i> Editar</a>

                                        <button type="submit" class="btn btn-danger" form="form-delete" formaction="{!! url('/drugs/'.$drug->id) !!}"><i class="fa fa-remove"></i>
                                        </button>


                                    </td>
                                </tr>
                            @endforeach
                            <tr>

                                @if ($drugs)
                                    <td colspan="5" class="pagination-container">{!!$drugs->appends(['q' => request('q'), 'sort' => request('sort'), 'order' => request('order') ])->render()!!}</td>
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

    <form method="post" id="form-delete" data-confirm="Estas Seguro?">
        <input name="_method" type="hidden" value="DELETE">{{ csrf_field() }}
    </form>


    <!-- <form method="post" id="form-addToYourPatients" data-confirm="Estas Seguro?">
  {{ csrf_field() }}
    </form> -->
@endsection
@push('scripts')

@endpush