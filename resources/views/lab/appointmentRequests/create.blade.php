@extends('layouts.laboratories.app')

@section('content')
    <section class="content-header">
        <h1>Nueva visita</h1>

    </section>
    <section class="content">

        <div class="row">


            <div class="col-md-8">
                <lab-appointment-request-form action-url="/lab/appointment-requests"></lab-appointment-request-form>

            </div>

        </div>
    </section>
@endsection