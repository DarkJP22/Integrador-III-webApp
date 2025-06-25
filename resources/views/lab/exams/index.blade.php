@extends('layouts.laboratories.app')

@section('content')
    <section class="content-header">
        <h1>Examenes</h1>

    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <a href="{{ url('/lab/exams/create') }}" class="btn btn-primary">Nuevo Examen</a>
{{--                        <lab-exams-settings></lab-exams-settings>--}}
                        <form action="/lab/exams" method="GET" autocomplete="off">
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
                                    <th>Código</th>
                                    <th>Nombre</th>


                                </tr>
                            </thead>
                            @foreach ($exams as $exam)
                                <tr>

                                    <td data-title="ID">{{ $exam->code }}</td>
                                    <td data-title="Nombre">

                                        <a href="/lab/exams/{{ $exam->id }}"> {{ $exam->name }}</a>


                                    </td>
                                    <td data-title="Nombre">

                                        {{ $exam->description }}


                                    </td>


                                </tr>
                            @endforeach
                            @if ($exams)
                                <td colspan="6" class="pagination-container">{!! $exams->appends(['q' => $search['q']])->render() !!}</td>
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
