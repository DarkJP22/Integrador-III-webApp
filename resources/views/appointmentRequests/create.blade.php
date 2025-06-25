@extends('layouts.login')

@section('content')
    <appointment-request-form :user="{{ $user }}" :medic="{{ $medic }}"></appointment-request-form>
@endsection
