@extends('layouts.admin-ibox')

@section('title', 'Редактировать пользователя')

@section('breadcrumbs')
    <li><a href="{{ route('admin.users.index') }}">Список пользователей</a></li>
    <li class="active">Редактировать пользователя</li>
@endsection

@section('ibox-title', 'Редактировать пользователя')

@section('ibox-content')
    {!! Form::model($user, ['route' => ['admin.users.update', $user], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}
    @include('admin.users.form', ['submitButtonText' => 'Сохранить изменения'])
    {!! Form::close() !!}
@stop