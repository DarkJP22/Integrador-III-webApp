@extends('layouts.admins.app')

@section('content')

<section class="content-header">
    <h1>Descuentos para usuario de Doctor Blue</h1>

</section>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">

                    <a href="{{ url('/admin/discounts/create') }}" class="btn btn-primary">Nuevo Descuento</a>
                    <div class="box-toolsdd filters">
                        <form action="/admin/discounts" method="GET" autocomplete="off">

                            <div class="form-group">

                                <div class="col-xs-12 col-sm-3">
                                    <div class="input-group input-group">


                                        <input type="text" name="q" class="form-control pull-right" placeholder="Buscar por nombre..." value="{{ isset($search) ? $search['q'] : '' }}">
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
                                <th>Descuento</th>

                                <th></th>
                            </tr>
                        </thead>
                        @foreach($discounts as $discount)
                        <tr>

                            <td data-title="ID">{{ $discount->id }}</td>


                            <td data-title="Nombre">{{ $discount->name }} </td>

                            <td data-title="Descuento">{{ $discount->tarifa }} </td>


                            <td data-title="" style="padding-left: 5px;">



                                <a href="{{ url('/admin/discounts/'.$discount->id) }}" class="btn btn-primary" title="Editar"><i class="fa fa-edit"></i> Editar</a>

                                <button type="submit" class="btn btn-danger" form="form-delete" formaction="{!! url('/admin/discounts/'.$discount->id) !!}"><i class="fa fa-remove"></i></button>



                            </td>
                        </tr>
                        @endforeach
                        <tr>

                            @if ($discounts)
                            <td colspan="5" class="pagination-container">{!!$discounts->appends(['q' => $search['q'] ])->render()!!}</td>
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