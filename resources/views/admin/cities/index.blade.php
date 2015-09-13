@extends('layouts.admin-ibox')

@section('header', 'Города')

@section('breadcrumbs')
  <li>Города</li>
@endsection

@section('ibox-title', 'Список городов')

@section('ibox-content')
  <div class="row">
    <div class="col-sm-3 m-b-xs">
      {!! Form::open(['route' => 'admin.cities.index', 'method' => 'GET']) !!}
      <div class="input-group">
        @include('layouts.utils.form_params', ['exclude' => ['q']])
        <input type="text" name="q" placeholder="Найти" class="input-sm form-control">
                <span class="input-group-btn">
                    <button class="btn btn-sm btn-primary">Go!</button>
                </span>
      </div>
      {!! Form::close() !!}
    </div>
    <div class="col-sm-3 pull-right text-right">
      <a class="btn btn-sm btn-primary" href="{{ route('admin.cities.create') }}">
        <i class="fa fa-plus"></i>
        Добавить город
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
      <tr>
        <th>#</th>
        <th>Название</th>
        <th>Страна</th>
        <th>Код города</th>
        <th>Действия</th>
      </tr>
      </thead>
      <tbody>
      @forelse($cities as $city)
        <tr>
          <td>{{ $city->id }}</td>
          <td>{{ $city->name }}</td>
          <td>{{ $city->country->name }}</td>
          <td>{{ $city->phonecode }}</td>
          <td>
            @include('admin.partials.default_actions', [
                'updateRoute' => 'admin.cities.edit',
                'destroyRoute' => 'admin.cities.destroy',
                'model' => $city
            ])
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center">Города не найдены.</td>
        </tr>
      @endforelse
      </tbody>
    </table>

    {!! $cities->render() !!}
  </div>
@stop