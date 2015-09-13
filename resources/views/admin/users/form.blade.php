<!-- Name Form Input -->
<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
  {!! Form::label('name', 'Имя:', ['class' => 'col-sm-2 control-label']) !!}
  <div class="col-sm-10">
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
    {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
  </div>
</div>

<!-- Email Form Input -->
<div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
  {!! Form::label('email', 'E-Mail:', ['class' => 'col-sm-2 control-label']) !!}
  <div class="col-sm-10">
    {!! Form::email('email', null, ['class' => 'form-control']) !!}
    {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
  </div>
</div>

<!-- Password Form Input -->
<div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
  {!! Form::label('password', 'Пароль:', ['class' => 'col-sm-2 control-label']) !!}
  <div class="col-sm-10">
    {!! Form::password('password', ['class' => 'form-control']) !!}
    {!! $errors->first('password', '<span class="help-block">:message</span>') !!}
  </div>
</div>

<div class="form-group">
  <div class="col-sm-offset-2 col-sm-10">
    <button type="submit" class="btn btn-primary">{{ $submitButtonText }}</button>
  </div>
</div>

@section('footer_js')

@stop