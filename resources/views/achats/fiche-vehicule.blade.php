@extends('layouts.app')

@section('title', 'Fiche Véhicule')
@section('page-title', 'Fiche Véhicule')

@section('content')
    @include('partials.fv-module', [
        'fiches' => $fiches,
        'payloads' => $payloads,
        'closeUrl' => route('dashboard'),
    ])
@endsection
