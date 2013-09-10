@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
{{ trans('messages.Crear grupo') }}
@stop

{{-- Content --}}
@section('content')
<h4>{{ trans('messages.Nuevo grupo') }}</h4>
<div class="well">
	<form class="form-horizontal" action="{{ URL::to('groups') }}" method="post">   
        {{ Form::token() }}
    
        <div class="control-group {{ ($errors->has('newGroup')) ? 'error' : '' }}" for="newGroup">
            <label class="control-label" for="newGroup">{{ trans('messages.Nombre del grupo') }}</label>
            <div class="controls">
                <input name="newGroup" value="{{ Request::old("newGroup") }}" type="text" class="input-xlarge" placeholder="{{ trans('messages.Nombre del grupo') }}">
                {{ ($errors->has('newGroup') ? $errors->first('newGroup') : '') }}
            </div>
        </div>

        <div class="control-group" for="permissions">
            <label class="control-label" for="permissions">{{ Lang::choice('messages.Permiso', 2)}}</label>
            <div class="controls">
                <label class="checkbox inline">
                    <input type="checkbox" value="1" name="adminPermissions"> {{ trans('messages.Administrador') }}
                </label>
                <label class="checkbox inline">
                    <input type="checkbox" value="1" name="userPermissions"> {{ trans('messages.Usuario') }}
                </label>
            </div>
        </div>
        
        <div class="form-actions">
            <input class="btn btn-primary" type="submit" value="{{ trans('messages.Crear nuevo grupo') }}">
        </div>
    </form>
</div>

@stop