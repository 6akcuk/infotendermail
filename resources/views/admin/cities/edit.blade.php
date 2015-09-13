@extends('layouts.admin-ibox')

@section('title', 'Редактировать город')

@section('breadcrumbs')
  <li><a href="{{ route('admin.cities.index') }}">Список городов</a></li>
  <li class="active">Редактировать город</li>
@endsection

@section('ibox-title', 'Редактировать город')

@section('ibox-content')
  {!! Form::model($city, ['route' => ['admin.cities.update', $city], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}
  @include('admin.cities.form', ['submitButtonText' => 'Сохранить изменения'])
  {!! Form::close() !!}
@stop