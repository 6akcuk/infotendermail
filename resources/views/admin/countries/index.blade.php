@extends('layouts.admin-ibox')

@section('header', 'Страны')

@section('breadcrumbs')
    <li>Страны</li>
@endsection

@section('ibox-title', 'Список стран')

@section('ibox-content')
    <div class="row">
        <div class="col-sm-3 m-b-xs">
            {!! Form::open(['route' => 'admin.countries.index', 'method' => 'GET']) !!}
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
            <a class="btn btn-sm btn-primary" href="{{ route('admin.countries.create') }}">
                <i class="fa fa-plus"></i>
                Добавить страну
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>Код страны</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse($countries as $country)
                <tr>
                    <td>{{ $country->id }}</td>
                    <td>{{ $country->name }}</td>
                    <td>{{ $country->phonecode }}</td>
                    <td>
                        @include('admin.partials.default_actions', [
                            'updateRoute' => 'admin.countries.edit',
                            'destroyRoute' => 'admin.countries.destroy',
                            'model' => $country
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Страны не найдены.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {!! $countries->render() !!}
    </div>
@stop