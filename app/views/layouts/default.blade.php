<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8" />
		<title> 
			@section('title') 
			@show 
		</title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
		<link href="{{ asset('css/bootstrap-responsive.css') }}" rel="stylesheet">
		<link href="{{ asset('css/bootstrapSwitch.css') }}" rel="stylesheet"><!-- Bootstrap switch from https://github.com/nostalgiaz/bootstrap-switch.git -->
		<style>
		@section('styles')
			body {
				padding-top: 60px;
			}
		@show
		</style>

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

	
	</head>

	<body>
		<!-- Navbar -->
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>

					<div class="nav-collapse collapse">
						<ul class="nav">
							<li {{ (Request::is('/') ? 'class="active"' : '') }}><a href="{{ URL::to('') }}">{{ Lang::get('messages.inicio') }}</a></li>
							@if (Sentry::check() && Sentry::getUser()->hasAccess('admin'))
								<li {{ (Request::is('users*') ? 'class="active"' : '') }}><a href="{{ URL::to('/users') }}">{{ Lang::get('messages.usuarios') }}</a></li>
								<li {{ (Request::is('groups*') ? 'class="active"' : '') }}><a href="{{ URL::to('/groups') }}">{{ Lang::get('messages.grupos') }}</a></li>
							@endif
						</ul>

						<ul class="nav pull-right">
							@if (Sentry::check())
							<li class="navbar-text">{{ Sentry::getUser()->email }}</li>
							<li class="divider-vertical"></li>
							<li {{ (Request::is('users/show/' . Sentry::getUser()->id) ? 'class="active"' : '') }}><a href="{{ URL::to('/users/show/'.Sentry::getUser()->id) }}">{{ Lang::get('messages.cuenta') }}</a></li>
							<li><a href="{{ URL::to('users/logout') }}">{{ Lang::get('messages.logout') }}</a></li>
							@else
							<li {{ (Request::is('users/login') ? 'class="active"' : '') }}><a href="{{ URL::to('users/login') }}">{{ Lang::get('messages.login') }}</a></li>
							<li {{ (Request::is('users/register') ? 'class="active"' : '') }}><a href="{{ URL::to('users/register') }}">{{ Lang::get('messages.regístrate') }}</a></li>
							@endif
						</ul>
					</div>
					<!-- ./ nav-collapse -->
				</div>
			</div>
		</div>
		<!-- ./ navbar -->

		<!-- Container -->
		<div class="container">
			<!-- Notifications -->
			@include('notifications')
			<!-- ./ notifications -->

			<!-- Content -->
			@yield('content')
			<!-- ./ content -->
		</div>

		<!-- ./ container -->

		<!-- Javascripts
		================================================== -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="{{ asset('js/bootstrap.min.js') }}"></script>
		<script src="{{ asset('js/restfulizer.js') }}"></script> <!-- Thanks to Zizaco for this script:  http://zizaco.net  -->
		<script src="{{ asset('js/bootstrapSwitch.js') }}"></script> <!-- Bootstrap switch from https://github.com/nostalgiaz/bootstrap-switch.git -->

	</body>
</html>
