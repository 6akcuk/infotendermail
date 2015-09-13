@extends('layouts.admin')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>@yield('ibox-title')</h5>
                    </div>
                    <div class="ibox-content">
                        @yield('ibox-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection