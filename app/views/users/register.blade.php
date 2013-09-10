@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
{{ trans('messages.Registro') }}
@stop

{{-- Content --}}
@section('content')
<h4>{{ trans('messages.Regitrar nueva cuenta') }}</h4>
<div class="well">
	<form class="form-horizontal" action="{{ URL::to('users/register') }}" method="post">
        {{ Form::token() }}
        
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

    	<div class="control-group {{ $errors->has('password_confirmation') ? 'error' : '' }}" for="password_confirmation">
        	<label class="control-label" for="password_confirmation">{{ trans('messages.confirmar clave') }}</label>
    		<div class="controls">
				<input name="password_confirmation" value="" type="password" class="input-xlarge" placeholder="{{ trans('messages.Escriba la clave de nuevo') }}">
    			{{ ($errors->has('password_confirmation') ? $errors->first('password_confirmation') : '') }}
    		</div>
    	</div>

		<div class="form-actions">
	    	<input class="btn-primary btn" type="submit" value="{{ trans('messages.Registrar') }}">
	    	<input class="btn " type="reset" value="{{ trans('messages.reset') }}">
	    </div>	
	</form>
</div>


@stop