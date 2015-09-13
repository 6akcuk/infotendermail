<!DOCTYPE html>
<html>

<head>
    @include ('layouts.partials.head')
</head>

<body class="top-navigation">

    <div id="busy"><div class="sk-spinner sk-spinner-rotating-plane"></div></div>

    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom white-bg">
                <nav class="navbar navbar-static-top" role="navigation">
                    <div class="navbar-header">
                        <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                            <i class="fa fa-reorder"></i>
                        </button>
                        <a href="/" class="navbar-brand">Tenders</a>
                    </div>
                    <div class="navbar-collapse collapse" id="navbar">

                    </div>
                </nav>
            </div>

            <div class="wrapper wrapper-content">
                @yield('content')
            </div>
        </div>

    </div>

    @include('layouts.partials.footer')
</body>

</html>
