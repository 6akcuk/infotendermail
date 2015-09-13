<!DOCTYPE html>
<html>
    <head>
        @include('layouts.partials.head')

        <!-- Toastr style -->
        <link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">

        <!-- Gritter -->
        <link href="js/plugins/gritter/jquery.gritter.css" rel="stylesheet">

        <!-- Sweet Alert -->
        <link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">

        <link href="css/plugins/chosen/chosen.css" rel="stylesheet">
    </head>

    <body>

        @include('admin.layouts.sidebar')

        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="nav navbar-static-top">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                        <form role="search" class="navbar-form-custom" action="">
                            <div class="form-group">
                                <input type="text" placeholder="Искать..." class="form-control" name="top-search" id="top-search">
                            </div>
                        </form>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <a href="/auth/logout">
                                <i class="fa fa-sign-out"></i>
                                Выйти
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-9">
                    <h2>@yield('header')</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/admin">Главная</a>
                        </li>
                        @yield('breadcrumbs')
                    </ol>
                </div>
            </div>

            @yield('content')
        </div>

        @include('layouts.partials.footer')

        <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
        <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

        <!-- Toastr script -->
        <script src="js/plugins/toastr/toastr.min.js"></script>
        @include('layouts.utils.flash')

        <!-- Sweet alert -->
        <script src="js/plugins/sweetalert/sweetalert.min.js"></script>

        <script>
            function confirmDelete(btn)
            {
                swal({
                    title: "Вы уверены?",
                    text: "Вы не сможете восстановить данные!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Да, удалить!",
                    closeOnConfirm: false
                }, function () {
                    swal("Удалено!", "Успешно удалено.", "success");

                    $(btn).parent().submit();
                });
            }
        </script>

    </body>
</html>