@extends('layouts.admin-ibox')

@section('title', 'Добавить город')

@section('breadcrumbs')
  <li><a href="{{ route('admin.cities.index') }}">Список городов</a></li>
  <li class="active">Добавить город</li>
@endsection

@section('ibox-title', 'Добавить новый город')

@section('ibox-content')
  {!! Form::open(['route' => 'admin.cities.store', 'class' => 'form-horizontal']) !!}
  @include('admin.cities.form', ['submitButtonText' => 'Добавить город'])
  {!! Form::close() !!}
@stop