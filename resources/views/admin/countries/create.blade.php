@extends('layouts.admin-ibox')

@section('title', 'Добавить страну')

@section('breadcrumbs')
    <li><a href="{{ route('admin.countries.index') }}">Список стран</a></li>
    <li class="active">Добавить страну</li>
@endsection

@section('ibox-title', 'Добавить новую страну')

@section('ibox-content')
  {!! Form::open(['route' => 'admin.countries.store', 'class' => 'form-horizontal']) !!}
  @include('admin.countries.form', ['submitButtonText' => 'Добавить страну'])
  {!! Form::close() !!}
@stop