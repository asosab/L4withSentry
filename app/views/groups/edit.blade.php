@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
{{ trans('messages.Editar grupo') }}
@stop

{{-- Content --}}
@section('content')
<h4>{{ trans('messages.Editar grupo') }}</h4>
<div class="well">
	<form class="form-horizontal" action="{{ URL::to('groups') }}/{{ $group['id'] }}" method="POST">   
        {{ Form::token() }}
        <input type="hidden" name="_method" value="PUT">
    
        <div class="control-group {{ ($errors->has('name')) ? 'error' : '' }}" for="name">
            <label class="control-label" for="name">{{ Lang::choice('messages.Nombre', 1)}}</label>
            <div class="controls">
                <input name="name" value="{{ (Request::old('name')) ? Request::old('name') : $group->name }}" type="text" class="input-xlarge" placeholder="{{ trans('messages.Nombre') }}">
                {{ ($errors->has('name') ? $errors->first('name') : '') }}
            </div>
        </div>

        <div class="control-group" for="permissions">
            <label class="control-label" for="permissions">{{ Lang::choice('messages.Permiso', 2)}}</label>
            <div class="controls">
                <label class="checkbox inline">
                    <input type="checkbox" value="1" name="adminPermissions" @if ( isset($group['permissions']['admin']) ) checked @endif> {{ trans('messages.Administrador') }}
                </label>
                <label class="checkbox inline">
                    <input type="checkbox" value="1" name="userPermissions" @if ( isset($group['permissions']['users']) ) checked @endif> {{ trans('messages.Usuario') }}
                </label>
            </div>
        </div>
        
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">{{ trans('messages.Guardar cambios') }}</button>
        </div>
  </form>
</div>

@stop