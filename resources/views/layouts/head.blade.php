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
<script src="/js/lodash.js"></script>
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/js/materialize.min.js"></script>
<script src="/js/admin/helpers.js"></script>
<script>
    window.Laravel = <?php echo json_encode([
        'csrfToken' => csrf_token(),
    ]); ?>
</script>