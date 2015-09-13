@extends('layouts.admin-ibox')

@section('header', 'Роли')

@section('breadcrumbs')
    <li>Роли</li>
@endsection

@section('ibox-title', 'Список ролей')

@section('ibox-content')
    <div class="row">
        <div class="col-sm-3 m-b-xs">
            {!! Form::open(['route' => 'admin.roles.index', 'method' => 'GET']) !!}
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
            <a class="btn btn-sm btn-primary" href="{{ route('admin.roles.create') }}">
                <i class="fa fa-plus"></i>
                Добавить роль
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>
                        @include('admin.partials.default_actions', [
                            'updateRoute' => 'admin.roles.edit',
                            'destroyRoute' => 'admin.roles.destroy',
                            'deleteCondition' => function($model) {
                                return $model->id != 1;
                            },
                            'model' => $role
                        ])
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection