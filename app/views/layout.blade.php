<html>
	<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Chat</title>
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
        <link rel="stylesheet/less" type="text/css" href="{{asset('css/light-theme.less')}}">

        @yield('header')

        <!-- This is a hack to get chrome to refresh my css changes. -->
        <script type="text/javascript">var less=less||{};less.env='development';</script>

        <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/1.7.3/less.min.js"></script>
	</head>
    <body>
        @yield('content')
    </body>
</html>
