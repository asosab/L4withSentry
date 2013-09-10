@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
{{ trans('messages.Perfil de usuario') }}
@stop

{{-- Content --}}
@section('content')

  @if (Sentry::check())
	<h4>{{ trans('messages.Perfil de usuario') }}</h4>
	
  	<div class="well clearfix">
	    <div class="span7">
		    @if ($user->first_name)
		    	<p><strong>{{ trans('messages.nombre(s)') }}:</strong> {{ $user->first_name }} </p>
			@endif
			@if ($user->last_name)
		    	<p><strong>{{ trans('messages.apellido(s)') }}:</strong> {{ $user->last_name }} </p>
			@endif
		    <p><strong>{{ trans('messages.Correo') }}:</strong> {{ $user->email }}</p>
		    <button class="btn btn-info" onClick="location.href='{{ URL::to('users/edit') }}/{{ $user->id}}'">{{ trans('messages.Editar perfil') }}</button>
		</div>
		<div class="span4">
			<p><em>{{ trans('messages.Cuenta creada') }}: {{ $user->created_at }}</em></p>
			<p><em>{{ trans('messages.Última actualización') }}: {{ $user->updated_at }}</em></p>
		</div>
	</div>

	<h4>{{ trans('messages.Grupos a los que pertenece') }}:</h4>
	<div class="well">
	    <ul>
	    	@if (count($myGroups) >= 1)
		    	@foreach ($myGroups as $group)
					<li>{{ $group['name'] }}</li>
				@endforeach
			@else 
				<li>{{ trans('messages.Sin grupos') }}.</li>
			@endif
	    </ul>
	</div>

	<h4>{{ trans('messages.Objeto usuario') }}</h4>
	<div>
		<p>{{ var_dump($user) }}</p>
	</div>
  @endif


@stop
