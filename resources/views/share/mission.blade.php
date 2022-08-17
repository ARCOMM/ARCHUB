@extends('layouts.share')

@section('title')
    {{ $mission->display_name }}
@endsection

@if ($mission->banner() != '')
    @section('banner')
        '{{ $mission->banner() }}'
    @endsection

    @section('banner-classes')
        banner-fade-top
    @endsection

    @section('header-color')
        transparent
    @endsection
@else
    @section('header-color')
        {{ (['coop' => 'info', 'adversarial' => 'danger', 'arcade' => 'success'])[$mission->mode] }}
    @endsection
@endif

@section('content')
    @livewire('share.show-mission')
@endsection
