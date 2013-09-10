@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
{{ trans('messages.Ver grupo') }}
@stop

{{-- Content --}}
@section('content')
<div class="span10 well">
	<h1>{{ $group['name'] }} </h1>
    <p>{{ Lang::choice('messages.Permiso', 2)}}:
        <br /> 
        {{ var_dump($groupPermissions) }}</p>

    <p>{{ trans('messages.Variables') }}: <br />
        {{ var_dump($group) }}</p>
</div>

@stop