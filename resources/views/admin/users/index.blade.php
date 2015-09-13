@extends('layouts.admin-ibox')

@section('header', 'Пользователи')

@section('breadcrumbs')
    <li>Пользователи</li>
@endsection

@section('ibox-title', 'Список пользователей')

@section('ibox-content')
    <div class="row">
        <div class="col-sm-3 m-b-xs">
            {!! Form::open(['route' => 'admin.users.index', 'method' => 'GET']) !!}
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
            <a class="btn btn-sm btn-primary" href="{{ route('admin.users.create') }}">
                <i class="fa fa-plus"></i>
                Добавить пользователя
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Фото</th>
                <th>Имя</th>
                <th>E-Mail</th>
                <th>Телефон</th>
                <th>Соц. сеть</th>
                <th>Город</th>
                <th>Бан</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->photo }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->social_network }}</td>
                    <td>{{ $user->city_id ? $user->city->name : 'Не указан' }}</td>
                    <td>{{ $user->banned }}</td>
                    <td>
                        @include('admin.partials.default_actions', [
                            'updateRoute' => 'admin.users.edit',
                            'destroyRoute' => 'admin.users.destroy',
                            'deleteCondition' => function($model) {
                                return $model->id != 1;
                            },
                            'model' => $user
                        ])
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {!! $users->render() !!}
@endsection