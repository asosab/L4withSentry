<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>{{ trans('messages.Nueva clave') }}</h2>

		<p>{{ trans('messages.Esta es tu nueva clave') }}:</p>
		<p><blockquote>{{{ $newPassword }}}</blockquote></p>
        <p>{{ trans('messages.Gracias') }}, <br />
            ~{{ trans('messages.Los administradores') }}</p>
	</body>
</html>