<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>{{ trans('messages.Cambio de clave') }}</h2>

		<p>{{ trans('messages.Para realizar el cambio de clave') }}, <a href="{{  URL::to('users/reset', array('id' => $userId, urlencode($resetCode))) }}">{{ trans('messages.haz click aquí') }}.</a></p>
		<p>{{ trans('messages.O navega a esta dirección') }}: <br /> {{  URL::to('users/reset', array('id' => $userId, urlencode($resetCode))) }}</p>
        <p>{{ trans('messages.Gracias') }}, <br />
            ~{{ trans('messages.Los administradores') }}</p>
	</body>
</html>