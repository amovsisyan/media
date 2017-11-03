<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Media</title>

    <!-- Styles -->
    {{--<link href="/css/app.css" rel="stylesheet">--}}
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href="/css/materialize.min.css" rel="stylesheet">
	<link href="/css/my.css" rel="stylesheet">


    <!-- Scripts -->
	{{--<script src="/js/app.js"></script>--}}
	<script src="/js/jquery-3.2.1.min.js"></script>
	<script src="/js/materialize.min.js"></script>
    <script>
        window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
        ]); ?>
    </script>

</head>
<body>
<header>
	@if (!empty($response))
		@foreach ($response['navbar'] as $navbar)
			@if(!empty($navbar['subcategory']))
				<ul id="{{$navbar['category']['alias']}}" class="dropdown-content">
					@foreach ($navbar['subcategory'] as $subcat)
						<li><a href="{{ url('/' . Request::segment(1) . '/' . $navbar['category']['alias'] . '/' . $subcat['alias'])}}">{{$subcat['name']}}</a></li>
					@endforeach
				</ul>
			@endif
		@endforeach
	@endif
		@if (Auth::guest())
			@if (false)
				{{--delete false when you need user login--}}
				<ul id="guest-login" class="dropdown-content">
					<li><a href="{{ url("/login") }}">Login</a></li>
					<li><a href="{{ url("/register") }}">Register</a></li>
				</ul>
			@endif
		@else
			<ul id="loged-logout" class="dropdown-content">
				<li>
					<a href="{{ url('/logout') }}"
					   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
						Logout
					</a>
					<form id="logout-form" action="{{ url('/logout') }}" method="POST"
						  style="display: none;">
						{{ csrf_field() }}
					</form>
				</li>
			</ul>
		@endif
		<nav>
			<div class="nav-wrapper">
				<a href="{{ url('/')}}" class="brand-logo left_5">logo</a>
				<ul class="right margin_5">
					@if (!empty($response))
						@foreach ($response['navbar'] as $navbar)
							@if(!empty($navbar['subcategory']))
								<li><a class="dropdown-button" href="" data-activates="{{$navbar['category']['alias']}}">{{$navbar['category']['name']}}<i class="material-icons right">arrow_drop_down</i></a></li>
							@endif
						@endforeach
					@endif
						@if (Auth::guest())
							@if (false)
								{{--delete false when you need user login--}}
								<li><a class="dropdown-button" href="" data-activates="guest-login">Login<i class="material-icons right">arrow_drop_down</i></a></li>
							@endif
						@else
							<li><a class="dropdown-button" href="" data-activates="loged-logout">{{ Auth::user()->name }}<i class="material-icons right">arrow_drop_down</i></a></li>
						@endif
				</ul>
			</div>
		</nav>
</header>
	@yield('content')
<footer class="page-footer">
	@if (!empty($response))
		<div class="container footer-navbar">
			<div class="row">
				@foreach ($response['navbar'] as $navbar)
					@if(!empty($navbar['subcategory']))
						<ul class="col s4">
							<li>{{$navbar['category']['name']}}</li>
							@foreach ($navbar['subcategory'] as $subcat)
								<li><a href="{{ url('/' . Request::segment(1) . '/' . $navbar['category']['alias'] . '/' . $subcat['alias']) }}">{{$subcat['name']}}</a></li>
							@endforeach
						</ul>
					@endif
				@endforeach
			</div>
		</div>
	@endif
	<div class="footer-copyright">
		<div class="container">
			© 2017 Copyright NoCoffee Solutions
			<a class="grey-text text-lighten-4 right" href="#!">Page Owner A. Movsisyan</a>
		</div>
	</div>
</footer>
</body>
<script src="/js/my.js"></script>
</html>
