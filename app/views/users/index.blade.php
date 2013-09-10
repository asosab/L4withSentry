@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
{{ trans('messages.inicio') }}
@stop

{{-- Content --}}
@section('content')

  @if (Sentry::check())
  	
    @if($user->hasAccess('admin'))
		<h4>{{ trans('messages.lista de usuarios') }}:</h4>
		<div class="well">
			<table class="table">
				<thead>
					<th>{{ trans('messages.usuario') }}</th>
					<th>{{ trans('messages.estado') }}</th>
					<th>{{ trans('messages.opciones') }}</th>
				</thead>
				<tbody>
					@foreach ($allUsers as $user)
						<tr>
							<td><a href="{{ URL::to('users/show') }}/{{ $user->id }}">{{ $user->email }}</a></td>
							<td>{{ $userStatus[$user->id] }} </td>
							<td><button class="btn" onClick="location.href='{{ URL::to('users/edit') }}/{{ $user->id}}'">{{ trans('messages.editar') }}</button> <button class="btn" onClick="location.href='{{ URL::to('users/suspend') }}/{{ $user->id}}'">{{ trans('messages.suspender') }}</button> <button class="btn action_confirm" href="{{ URL::to('users/delete') }}/{{ $user->id}}" data-token="{{ Session::getToken() }}" data-method="post">{{ trans('messages.borrar') }}</button></td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
    @else 
		<h4>{{ trans('messages.No eres administrador') }}</h4>
    @endif
  @else
    <h4>{{ trans('messages.No estás logueado') }}</h4>
  @endif


@stop
