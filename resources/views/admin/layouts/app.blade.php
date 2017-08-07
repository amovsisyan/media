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
	<link href="/css/admin/leftnav.css" rel="stylesheet">
	<link href="/css/admin/admin-control-panel.css" rel="stylesheet">
	<link href="/css/admin/helpers.css" rel="stylesheet">

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
	<div id="lefnavbar">
		<ul id="slide-out" class="side-nav">
			@if (!empty($response) && !empty($response['leftNav']))
				@foreach ($response['leftNav'] as $navbar)
					<li class="no-padding">
						<ul class="collapsible collapsible-accordion">
							<li data-id="{{ $navbar['nav']['id'] }}">
								<a class="collapsible-header">{{ $navbar['nav']['name'] }}<i class="material-icons">arrow_drop_down</i></a>
								<div class="collapsible-body">
									<ul>
										@if (!empty($navbar['part']))
											@foreach ($navbar['part'] as $part)
												<li data-id="{{ $part['id'] }}">
													<a href="{{ url('/qwentin/' . $navbar['nav']['alias'] . '/' . $part['alias']) }}">{{ $part['name'] }}</a>
												</li>
											@endforeach
										@endif
									</ul>
								</div>
							</li>
						</ul>
					</li>
				@endforeach
			@endif
		</ul>
		<a href="#" data-activates="slide-out" class="button-collapse"><i class="material-icons">menu</i></a>
	</div>
</header>
	@yield('content')
</body>
<script src="/js/admin/leftnav.js"></script>
<script src="/js/admin/helpers.js"></script>
	@yield('script')
</html>
