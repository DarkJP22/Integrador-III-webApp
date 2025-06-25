@extends('layouts.pharmacies.app')

@section('header')

@endsection
@section('content')

    <media :tags="{{ json_encode($tags) }}"></media>

@endsection
@push('scripts')

@endpush