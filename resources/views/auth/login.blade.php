@extends('layouts.main')

@section('title', 'Авторизация')

@section('content')
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>
                <h1 class="logo-name">CS</h1>
            </div>

            <h3>{{ trans('auth.welcome') }}</h3>

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
                </div>
            @endif

            {!! Form::open(['class' => 'm-t']) !!}
            <form class="m-t" role="form" action="/auth/login">
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="E-Mail" required="">
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Пароль" required="">
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b">Войти</button>

                <a href="/password/forgot"><small>Забыли пароль?</small></a>
                <p class="text-muted text-center"><small>Еще нет аккаунта?</small></p>
                <a class="btn btn-sm btn-white btn-block" href="/auth/register">Создайте</a>
            {!! Form::close() !!}
            <p class="m-t"> <small>тендеры &copy; 2015</small> </p>
        </div>
    </div>

@endsection