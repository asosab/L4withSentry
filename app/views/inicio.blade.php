@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
Justine
@stop

{{-- Content --}}
@section('content')

<h1>{{ Lang::get('messages.Hola Mundo!') }}</h1>
<div class="well">
	@if (Sentry::check())
        {{ Lang::get('messages.estas conectado') }}
	@endif
	</p>
</div>

@if (Sentry::check() && Sentry::getUser()->hasAccess('admin'))
	<h4>{{ Lang::get('messages.opciones de admin') }}</h4>
	<div class="well">
		 <button class="btn btn-info" onClick="location.href='{{ URL::to('users') }}'">{{ Lang::get('messages.usuarios') }}</button>
		 <button class="btn btn-info" onClick="location.href='{{ URL::to('groups') }}'">{{ Lang::get('messages.grupos') }}</button>
	</div>
@endif 
 
 
@stop