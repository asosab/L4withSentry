@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
{{trans('messages.Suspender usuario')}}
@stop

{{-- Content --}}
@section('content')
<h4>{{trans('messages.Suspender')}} {{ $user->email }}</h4>
<div class="well">
	<form class="form-horizontal" action="{{ URL::to('users/suspend') }}/{{ $user->id }}" method="post">   
    	{{ Form::token() }}
    	
		<div class="control-group {{ ($errors->has('suspendTime')) ? 'error' : '' }}" for="suspendTime">
            <label class="control-label" for="suspendTime">{{trans('messages.Duración')}}</label>
            <div class="controls">
                <input name="suspendTime" id="suspendTime" value="{{ Request::old('suspendTime') }}" type="text" class="input-xlarge" placeholder="{{trans('messages.Minutos')}}">
                {{ ($errors->has('suspendTime') ? $errors->first('suspendTime') : '') }}
            </div>
        </div>

    	<div class="form-actions">
    		<button class="btn btn-primary" type="submit">{{trans('messages.Suspender usuario')}}</button>
    	</div>
  </form>
</div>

@stop