@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
{{ trans('messages.edita perfil') }}
@stop

{{-- Content --}}
@section('content')

<h4>{{ trans('messages.edita') }}
@if ($user->email == Sentry::getUser()->email)
    {{ trans('messages.tu') }}
@else 
	{{ $user->email }}'s 
@endif

{{ trans('messages.perfil') }}</h4>
<div class="well">
	<form class="form-horizontal" action="{{ URL::to('users/edit') }}/{{ $user->id }}" method="post">
        {{ Form::token() }}
        
        <div class="control-group {{ ($errors->has('firstName')) ? 'error' : '' }}" for="firstName">
        	<label class="control-label" for="firstName">{{ trans('messages.nombre(s)') }}</label>
    		<div class="controls">
				<input name="firstName" value="{{ (Request::old('firstName')) ? Request::old("firstName") : $user->first_name }}" type="text" class="input-xlarge" placeholder="{{ trans('messages.nombre(s)') }}">
    			{{ ($errors->has('firstName') ? $errors->first('firstName') : '') }}
    		</div>
    	</div>

        <div class="control-group {{ $errors->has('lastName') ? 'error' : '' }}" for="lastName">
        	<label class="control-label" for="lastName">{{ trans('messages.apellido(s)') }}</label>
    		<div class="controls">
				<input name="lastName" value="{{ (Request::old('lastName')) ? Request::old("lastName") : $user->last_name }}" type="text" class="input-xlarge" placeholder="{{ trans('messages.apellido(s)') }}">
    			{{ ($errors->has('lastName') ?  $errors->first('lastName') : '') }}
    		</div>
    	</div>

    	<div class="form-actions">
	    	<input class="btn-primary btn" type="submit" value="{{ trans('messages.realizar cambios') }}">
	    	<input class="btn-inverse btn" type="reset" value="{{ trans('messages.reset') }}">
	    </div>
    </form>
</div>

<h4>{{ trans('messages.cambio de clave') }}</h4>
<div class="well">
	<form class="form-horizontal" action="{{ URL::to('users/changepassword') }}/{{ $user->id }}" method="post">
        {{ Form::token() }}
        
        <div class="control-group {{ $errors->has('oldPassword') ? 'error' : '' }}" for="oldPassword">
        	<label class="control-label" for="oldPassword">{{ trans('messages.clave antigua') }}</label>
    		<div class="controls">
				<input name="oldPassword" value="" type="password" class="input-xlarge" placeholder="{{ trans('messages.clave antigua') }}">
    			{{ ($errors->has('oldPassword') ? $errors->first('oldPassword') : '') }}
    		</div>
    	</div>

        <div class="control-group {{ $errors->has('newPassword') ? 'error' : '' }}" for="newPassword">
        	<label class="control-label" for="newPassword">{{ trans('messages.nueva clave') }}</label>
    		<div class="controls">
				<input name="newPassword" value="" type="password" class="input-xlarge" placeholder="{{ trans('messages.nueva clave') }}">
    			{{ ($errors->has('newPassword') ?  $errors->first('newPassword') : '') }}
    		</div>
    	</div>

    	<div class="control-group {{ $errors->has('newPassword_confirmation') ? 'error' : '' }}" for="newPassword_confirmation">
        	<label class="control-label" for="newPassword_confirmation">{{ trans('messages.confirmar nueva clave') }}</label>
    		<div class="controls">
				<input name="newPassword_confirmation" value="" type="password" class="input-xlarge" placeholder="{{ trans('messages.nueva clave de nuevo') }}">
    			{{ ($errors->has('newPassword_confirmation') ? $errors->first('newPassword_confirmation') : '') }}
    		</div>
    	</div>
	        	
	    <div class="form-actions">
	    	<input class="btn-primary btn" type="submit" value="{{ trans('messages.cambiar clave') }}">
	    	<input class="btn-inverse btn" type="reset" value="{{ trans('messages.reset') }}">
	    </div>
      </form>
  </div>

@if (Sentry::check() && Sentry::getUser()->hasAccess('admin'))
<h4>{{ trans('messages.membresía de usuario en grupo') }}</h4>
<div class="well">
    <form class="form-horizontal" action="{{ URL::to('users/updatememberships') }}/{{ $user->id }}" method="post">
        {{ Form::token() }}

        <table class="table">
            <thead>
                <th>{{ trans('messages.grupos') }}</th>
                <th>{{ trans('messages.Estatus de miembro') }}</th>
            </thead>
            <tbody>
                @foreach ($allGroups as $group)
                    <tr>
                        <td>{{ $group->name }}</td>
                        <td>
                            <div class="switch" data-on-label="{{ trans('messages.dentro') }}" data-on='info' data-off-label="{{ trans('messages.fuera') }}">
                                <input name="permissions[{{ $group->id }}]" type="checkbox" {{ ( $user->inGroup($group)) ? 'checked' : '' }} >
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="form-actions">
            <input class="btn-primary btn" type="submit" value="{{ trans('messages.actualizar membresía') }}">
        </div> 
    </form>
</div>
@endif    

@stop