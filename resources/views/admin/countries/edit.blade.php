@extends('layouts.admin-ibox')

@section('title', 'Редактировать страну')

@section('breadcrumbs')
  <li><a href="{{ route('admin.countries.index') }}">Список стран</a></li>
  <li class="active">Редактировать страну</li>
@endsection

@section('ibox-title', 'Редактировать страну')

@section('ibox-content')
  {!! Form::model($country, ['route' => ['admin.countries.update', $country], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}
  @include('admin.countries.form', ['submitButtonText' => 'Сохранить изменения'])
  {!! Form::close() !!}
@stop