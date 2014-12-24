@extends('layout')

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('css/home.css')}}">
@stop

@section('content')
<div id="index-auth-buttons">
    <a class="index-button" href="{{URL::to('account/register')}}">Register</a>
    <a class="index-button" href="{{URL::to('account/login')}}">Login</a>
</div>c

<div class="clear-float"></div>

<div id="index-header-container">
    <div id="index-header">
        Whisper It
        <div id="index-tag-line">
            Because your messages shouldn't be sniffed, mined or archived.
        </div>
    </div>
    <div id="marketing-images">
        <img class="marketing-image" src="{{ asset('img/tmp-marketing-icon.svg') }}">
        <img class="marketing-image" src="{{ asset('img/tmp-marketing-icon.svg') }}">
        <img class="marketing-image" src="{{ asset('img/tmp-marketing-icon.svg') }}">
    </div>
</div>


@stop