@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
{{ trans('messages.Acceso') }}
@stop

{{-- Content --}}
@section('content')
<h4>{{ trans('messages.login') }}</h4>
<div class="well">
	<form class="form-horizontal" action="{{ URL::to('users/login') }}" method="post">   
        {{ Form::token(); }}

        <div class="control-group {{ ($errors->has('email')) ? 'error' : '' }}" for="email">
            <label class="control-label" for="email">{{ trans('messages.Correo') }}</label>
            <div class="controls">
                <input name="email" id="email" value="{{ Request::old('email') }}" type="text" class="input-xlarge" placeholder="{{ trans('messages.Correo') }}">
                {{ ($errors->has('email') ? $errors->first('email') : '') }}
            </div>
        </div>
    
       <div class="control-group {{ $errors->has('password') ? 'error' : '' }}" for="password">
            <label class="control-label" for="password">{{ trans('messages.Clave') }}</label>
            <div class="controls">
                <input name="password" value="" type="password" class="input-xlarge" placeholder="{{ trans('messages.Clave') }}">
                {{ ($errors->has('password') ?  $errors->first('password') : '') }}
            </div>
        </div>

        <div class="control-group" for"rememberme">
            <div class="controls">
                <label class="checkbox inline">
                    <input type="checkbox" name="rememberMe" value="1"> {{ trans('messages.Recuérdame') }}
                </label>
            </div>
        </div>
    
        <div class="form-actions">
            <input class="btn btn-primary" type="submit" value="{{ trans('messages.login') }}">
            <a href="{{ URL::to('users/resetpassword') }}" class="btn btn-link">{{ trans('messages.¿Olvidaste tu clave?') }}</a>
        </div>
  </form>
</div>

@stop