<?php
$seo = (isset($response) && isset($response['seo'])) ? true : false;
?>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{($seo && !empty($response['seo']['title']))
    ? trans('seo.meta.title', ['info' => $response['seo']['title']])
    : trans('seo.meta.titleDefault')}}</title>
<meta name="description" content="{{($seo && !empty($response['seo']['description']))
    ? trans('seo.meta.description', ['info' => $response['seo']['description']])
    : trans('seo.meta.descriptionDefault')}}">
<meta name="keywords" content="{{($seo && !empty($response['seo']['keywords']))
    ? trans('seo.meta.keywords', ['info' => $response['seo']['keywords']])
    : trans('seo.meta.keywordsDefault')}}">
<link rel="shortcut icon" href="/img/logo/panda-logo.jpg" />
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