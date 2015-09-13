@extends('layouts.admin-ibox')

@section('title', 'Добавить роль')

@section('breadcrumbs')
    <li><a href="{{ route('admin.roles.index') }}">Список ролей</a></li>
    <li class="active">Добавить роль</li>
@endsection

@section('ibox-title', 'Добавить новую роль')

@section('ibox-content')
    {!! Form::open(['route' => 'admin.roles.store', 'class' => 'form-horizontal']) !!}
    @include('admin.roles.form', ['submitButtonText' => 'Добавить роль'])
    {!! Form::close() !!}
@stop