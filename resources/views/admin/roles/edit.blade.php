@extends('layouts.admin-ibox')

@section('title', 'Редактировать роль')

@section('breadcrumbs')
    <li><a href="{{ route('admin.roles.index') }}">Список ролей</a></li>
    <li class="active">Редактировать роль</li>
@endsection

@section('ibox-title', 'Редактировать роль')

@section('ibox-content')
    {!! Form::model($role, ['route' => ['admin.roles.update', $role], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}
    @include('admin.roles.form', ['submitButtonText' => 'Сохранить изменения'])
    {!! Form::close() !!}
@stop