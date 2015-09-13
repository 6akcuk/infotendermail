@extends('layouts.admin-ibox')

@section('title', 'Добавить пользователя')

@section('breadcrumbs')
    <li><a href="{{ route('admin.users.index') }}">Список пользователей</a></li>
    <li class="active">Добавить пользователя</li>
@endsection

@section('ibox-title', 'Добавить нового пользователя')

@section('ibox-content')
    {!! Form::open(['route' => 'admin.users.store', 'class' => 'form-horizontal']) !!}
    @include('admin.users.form', ['submitButtonText' => 'Добавить пользователя'])
    {!! Form::close() !!}
@stop