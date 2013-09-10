<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Welcome</h2>

		<p><b>{{ trans('messages.Cuenta') }}:</b> {{{ $email }}}</p>
		<p>{{ trans('messages.Para activar tu cuenta') }}, <a href="{{  URL::to('users/activate', array('id' => $userId, urlencode($activationCode))) }}">{{ trans('messages.haz click aquí') }}.</a></p>
		<p>{{ trans('messages.O navega a esta dirección') }}: <br /> {{  URL::to('users/activate', array('id' => $userId, urlencode($activationCode))) }}</p>
		<p>{{ trans('messages.Gracias') }}, <br />
			~{{ trans('messages.Los administradores') }}</p>
	</body>
</html>