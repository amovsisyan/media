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
	@foreach ($response['navbar'] as $navbar)
		<ul id="{{$navbar['category']['alias']}}" class="dropdown-content">
			@foreach ($navbar['subcategory'] as $subcat)
				<li><a href="{{ url('/' . $navbar['category']['alias'] . '/' . $subcat['alias'] . '_' . $subcat['id']) }}">{{$subcat['name']}}</a></li>
			@endforeach
		</ul>
	@endforeach
	{{--<ul id="dropdown1" class="dropdown-content">--}}
		{{--<li><a href="#!">one</a></li>--}}
		{{--<li><a href="#!">two</a></li>--}}
		{{--<li class="divider"></li>--}}
		{{--<li><a href="#!">three</a></li>--}}
	{{--</ul>--}}
	{{--<ul id="dropdown2" class="dropdown-content">--}}
		{{--<li><a href="#!">one</a></li>--}}
		{{--<li><a href="#!">two</a></li>--}}
		{{--<li class="divider"></li>--}}
		{{--<li><a href="#!">three</a></li>--}}
	{{--</ul>--}}
	{{--<ul id="dropdown3" class="dropdown-content">--}}
		{{--<li><a href="#!">one</a></li>--}}
		{{--<li><a href="#!">two</a></li>--}}
		{{--<li class="divider"></li>--}}
		{{--<li><a href="#!">three</a></li>--}}
	{{--</ul>--}}
	<nav>
		<div class="nav-wrapper">
			<a href="{{ url('/')}}" class="brand-logo left_5">logo</a>
			<ul class="right margin_5">
				@foreach ($response['navbar'] as $navbar)
					<li><a class="dropdown-button" href="" data-activates="{{$navbar['category']['alias']}}">{{$navbar['category']['name']}}<i class="material-icons right">arrow_drop_down</i></a></li>
				@endforeach
			</ul>
		</div>
	</nav>
</header>
    @yield('content')
<footer class="page-footer">
	<div class="container footer-navbar">
		<div class="row">
			@foreach ($response['navbar'] as $navbar)
				<ul class="col s4">
					<li>{{$navbar['category']['name']}}</li>
					@foreach ($navbar['subcategory'] as $subcat)
						<li><a href="{{ url('/' . $navbar['category']['alias'] . '/' . $subcat['alias'] . '_' . $subcat['id']) }}">{{$subcat['name']}}</a></li>
					@endforeach
				</ul>
			@endforeach
		</div>
	</div>
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
