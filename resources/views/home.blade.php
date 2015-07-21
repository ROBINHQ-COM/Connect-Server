<!DOCTYPE html>
<html>
<head>
    <title>Lumen</title>

    <link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
    <link rel="stylesheet" href="{!! url('style.css') !!}">
    <link rel="stylesheet" href="{!! url('js/node_modules/ladda/dist/ladda-themeless.min.css') !!}">
    <link rel="stylesheet" href="{!! url('js/node_modules/animate.css/animate.min.css') !!}">

    <script src="{!! url('js/node_modules/jquery/dist/jquery.min.js') !!}"></script>
    <script src="{!! url('js/node_modules/ladda/dist/spin.min.js') !!}"></script>
    <script src="{!! url('js/node_modules/ladda/dist/ladda.min.js') !!}"></script>
    <script src="{!! url('js/node_modules/ladda/dist/ladda.jquery.min.js') !!}"></script>
    <script src="{!! url('js/node_modules/noty/js/noty/packaged/jquery.noty.packaged.min.js') !!}"></script>
    <script src="{!! url('js/app.js') !!}"></script>
</head>
<body>
<div class="container">
    <div class="content">
        <div class="title">ROBINHQ Connect-Server</div>
        <div class="quote">{!! $health !!}</div>
        <div class="links">
            <button class="ladda-button pure-button button-success hooks-on" data-style="expand-right">
                <span class="ladda-label">Register Webhooks</span>
            </button>
            <button class="ladda-button pure-button button-error hooks-off" data-style="expand-right">
                <span class="ladda-label">Unregister Webhooks</span>
            </button>
        </div>

    </div>
</div>
</body>
</html>