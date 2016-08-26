@extends('layouts.admin-ibox')

@section('header', 'Контракты')

@section('breadcrumbs')
    <li>Контракты</li>
@endsection

@section('ibox-title', 'Список контрактов ('. $total .')')

@section('ibox-content')
    <div class="row">
        <div class="col-sm-3 m-b-xs">
            {!! Form::open(['route' => 'admin.contracts.index', 'method' => 'GET']) !!}
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
            <a class="btn btn-sm btn-primary" href="{{ route('admin.contracts.setup') }}">
                <i class="fa fa-plus"></i>
                Настроить поиск
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>Организация</th>
                <th>Адрес</th>
                <th>Сумма</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($contracts as $contract)
                <tr>
                    <td>{{ $contract->id }}</td>
                    <td>{{ $contract->name }}</td>
                    <td>{{ $contract->organization->name }}</td>
                    <td>{{ $contract->organization->address }}</td>
                    <td>{{ $contract->price }}</td>
                    <td>
                        @include('admin.partials.default_actions', [
                            'updateRoute' => 'admin.contracts.edit',
                            'destroyRoute' => 'admin.contracts.destroy',
                            'deleteCondition' => function($model) {
                                return false;
                            },
                            'model' => $contract
                        ])
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {!! $contracts->render() !!}
@endsection