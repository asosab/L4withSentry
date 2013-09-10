@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
{{ trans('messages.Grupos') }}
@stop

{{-- Content --}}
@section('content')
<h4>{{ trans('messages.Grupos disponibles') }}</h4>
<div class="well">
	<table class="table">
		<thead>
			<th>{{ Lang::choice('messages.Nombre', 2)}}</th>
			<th>{{ Lang::choice('messages.Permiso', 2)}}</th>
			<th>{{ Lang::choice('messages.Opción', 2)}}</th>
		</thead>
		<tbody>
		@foreach ($allGroups as $group)
			<tr>
				<td>{{ $group->name }}</td>
				<td>{{ (isset($group['permissions']['admin'])) ? '<i class="icon-ok"></i> Admin' : ''}} {{ (isset($group['permissions']['users'])) ? '<i class="icon-ok"></i> Users' : ''}}</td>
				<td><button class="btn" onClick="location.href='{{ URL::to('groups/'.$group->id.'/edit/') }}'">{{ trans('messages.Editar') }}</button>
				 	<button class="btn action_confirm {{ ($group->id == 2) ? 'disabled' : '' }}" data-method="delete" href="{{ URL::to('groups/'.$group->id) }}}">{{ trans('messages.Borrar') }}</button></td>
			</tr>	
		@endforeach
		</tbody>
	</table> 
	 <button class="btn btn-info" onClick="location.href='{{ URL::to('groups/create') }}'">{{ trans('messages.Nuevo grupo') }}</button>
</div>
<!--  
	The delete button uses Resftulizer.js to restfully submit with "Delete".  The "action_confirm" class triggers an optional confirm dialog.
	Also, I have hardcoded adding the "disabled" class to the Admin group - deleting your own admin access causes problems.
-->
@stop

