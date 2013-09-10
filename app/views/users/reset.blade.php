@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
{{ trans('messages.Resetear clave') }}
@stop

{{-- Content --}}
@section('content')
<h4>{{ trans('messages.Resetear clave') }}</h4>
<div class="well">
	<form class="form-horizontal" action="{{ URL::to('users/resetpassword') }}" method="post">   
    	{{ Form::token() }}
    	
		<div class="control-group {{ ($errors->has('email')) ? 'error' : '' }}" for="email">
            <label class="control-label" for="email">{{ trans('messages.Correo') }}</label>
            <div class="controls">
                <input name="email" id="email" value="{{ Request::old('email') }}" type="text" class="input-xlarge" placeholder="{{ trans('messages.Correo') }}">
                {{ ($errors->has('email') ? $errors->first('email') : '') }}
            </div>
        </div>

    	<div class="form-actions">
    		<button class="btn btn-primary" type="submit">{{ trans('messages.Resetear clave') }}</button>
    	</div>
  </form>
</div>

@stop