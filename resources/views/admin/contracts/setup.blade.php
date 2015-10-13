@extends('layouts.admin-ibox')

@section('header', 'Настройка поиска')

@section('breadcrumbs')
    <li><a href="{{ route('admin.contracts.index') }}">Контракты</a></li>
    <li>Настройка поиска</li>
@endsection

@section('ibox-title', 'Настройка поиска')

@section('ibox-content')
    {!! Form::model($criterias, ['route' => 'admin.contracts.setup', 'class' => 'form-horizontal']) !!}
    <!-- Выборка по регионам Form Input -->
    <div class="form-group {{ $errors->has('regions') ? 'has-error' : '' }}">
      {!! Form::label('regions', 'Выборка по регионам:', ['class' => 'col-sm-2 control-label']) !!}
      <div class="col-sm-10">
        {!! Form::select('regions[]', $regions, null, ['id' => 'regions', 'class' => 'form-control', 'multiple']) !!}
        {!! $errors->first('regions', '<span class="help-block">:message</span>') !!}
      </div>
    </div>

    <div class="hr-line-dashed"></div>

    <!-- Искать слова и фразы Form Input -->
    <div class="form-group {{ $errors->has('match') ? 'has-error' : '' }}">
      {!! Form::label('match', 'Искать слова и фразы в названии контракта:', ['class' => 'col-sm-2 control-label']) !!}
      <div class="col-sm-10">
        {!! Form::text('match', null, ['class' => 'form-control']) !!}
        {!! $errors->first('match', '<span class="help-block">:message</span>') !!}
      </div>
    </div>

    <!-- Исключить слова и фразы Form Input -->
    <div class="form-group {{ $errors->has('exclude') ? 'has-error' : '' }}">
      {!! Form::label('exclude', 'Исключить слова и фразы в названии контракта:', ['class' => 'col-sm-2 control-label']) !!}
      <div class="col-sm-10">
        {!! Form::text('exclude', null, ['class' => 'form-control']) !!}
        {!! $errors->first('exclude', '<span class="help-block">:message</span>') !!}
      </div>
    </div>

    <div class="hr-line-dashed"></div>

    <!-- Искать слова и фразы в названии организации Form Input -->
    <div class="form-group {{ $errors->has('match_org') ? 'has-error' : '' }}">
      {!! Form::label('match_org', 'Искать слова и фразы в названии организации:', ['class' => 'col-sm-2 control-label']) !!}
      <div class="col-sm-10">
        {!! Form::text('match_org', null, ['class' => 'form-control']) !!}
        {!! $errors->first('match_org', '<span class="help-block">:message</span>') !!}
      </div>
    </div>

    <!-- Исключить слова и фразы в названии организации Form Input -->
    <div class="form-group {{ $errors->has('exclude_org') ? 'has-error' : '' }}">
      {!! Form::label('exclude_org', 'Исключить слова и фразы в названии организации:', ['class' => 'col-sm-2 control-label']) !!}
      <div class="col-sm-10">
        {!! Form::text('exclude_org', null, ['class' => 'form-control']) !!}
        {!! $errors->first('exclude_org', '<span class="help-block">:message</span>') !!}
      </div>
    </div>

    <div class="hr-line-dashed"></div>

    <div class="form-group">
        <div class="col-sm-10 col-sm-offset-2">
            {!! Form::submit('Сохранить', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('head_css')
    <style>
        #regions {
            min-height: 150px;
        }
    </style>

    <link href="css/plugins/tagsinput/tagsinput.css" rel="stylesheet">
@endsection

@section('footer_js')

    <!-- Tags -->
    <script src="js/autocomplete-ui.min.js"></script>
    <script src="js/plugins/tagsinput/tagsinput.js"></script>
    <script>
        $('#match, #exclude, #match_org, #exclude_org').tagsInput({
            autocomplete_url: '/api/tags',
            defaultText: 'Добавьте слова',
            width: 'auto',
            height: '82px',
            minInputWidth: '130px',
            minChars: 2
        });
    </script>

@endsection