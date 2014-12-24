@extends('layout')

@section('header')
<script type="text/javascript">
    var handlesApiUrl = "{{URL::to('api/handle')}}";
    var conversationsApiUrl = "{{URL::to('api/conversation')}}";
    var messagesApiUrl = "{{URL::to('api/message')}}";
</script>
@stop

@section('content')
<div id="side-menu">
    <div id="side-menu-header">Menu</div>
</div>
<div id="blur-overlay" class="overlay-hidden"></div>
<div id="header">
    <div id="btn-back"></div>
    <div id="title">Chat with jon</div>
    <div id="btn-options"></div>
</div>
<div id="screen">
    <div class="info">Loading</div>
</div>
<div id="footer">
    <textarea class="chat-input"></textarea>
    <div id="btn-send">Send</div>
</div>

<audio id="msg-tone" src="{{asset('sound/tone-2.ogg')}}" preload="auto"></audio>

<script type="text/javascript" src="{{asset('js/app.js')}}"></script>
@stop