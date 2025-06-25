@extends('layouts.laboratories.app')

@section('content')
    <section class="content-header">
        <h1>Paquetes de examenes</h1>

    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <a href="{{ url('/lab/exams-packages/create') }}" class="btn btn-primary">Nuevo Paquete</a>
                        <form action="/lab/exams-packages" method="GET" autocomplete="off">
                            <div class="form-group">

                                <div class="col-sm-3">
                                    <div class="input-group input-group">


                                        <input type="text" name="q" class="form-control pull-right" placeholder="Buscar..." value="{{ isset($search) ? $search['q'] : '' }}">
                                        <div class="input-group-btn">

                                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                        </div>


                                    </div>
                                </div>
                               
                                <div class="col-sm-2">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                </div>

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
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    

                                </tr>
                            </thead>
                            @foreach ($examPackages as $examPackage)
                                <tr>

                                    <td data-title="ID">{{ $examPackage->id }}</td>
                                    <td data-title="Nombre">

                                    <a href="/lab/exams-packages/{{ $examPackage->id}}"> {{ $examPackage->name }}</a>   


                                    </td>
                                    

                                </tr>
                            @endforeach
                            @if ($examPackages)
                                <td colspan="6" class="pagination-container">{!! $examPackages->appends(['q' => $search['q']])->render() !!}</td>
                            @endif
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

    </section>
    <form method="post" id="form-status">
        {{ csrf_field() }}
    </form>
@endsection

