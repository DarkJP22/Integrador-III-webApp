@extends('layouts.download')

@section('css')

@endsection
@section('content')
<div class="container tw-max-w-4xl tw-py-8 tw-text-center">
    <div class="row">
        <div class="col-sm-12">
            <a class="tw-block" href="/" style="max-width: 100px; margin: 0 auto;"><img alt="Doctor Blue"
                                                                                        class="tw-w-full"
                                                                                        src="/img/logo.png"></a>

            <h2>Haz click en el siguiente enlace para descargar tu información</h2>
            <form action="{{ route('user.download-data.download', $user->id) }}" method="POST">
                @csrf
                <button class="btn btn-primary">Descargar</button>
            </form>
        </div>
    </div>
</div>
@endsection