<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title> {{ config('app.site_title') }} - @yield('err-status') </title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="{{ asset("css/error.css") }}" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<section id="content" class="animate-fade-up">
    <div class="page-err">
        <div class="text-center">
            <div class="err-status">
                <h1>@yield('err-status')</h1>
            </div>
            <div class="err-message">
                <h2>@yield('err-message')</h2>
            </div>
            <div class="err-body">
                <a href="{{ url('/admin') }}" class="btn btn-lg btn-goback">
                    <i class="fa fa-home"></i>
                    <span class="space"></span>
                    返回主页
                </a>
            </div>
        </div>
    </div>
</section>
</body>
</html>