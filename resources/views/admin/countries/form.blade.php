<!-- Название страны Form Input -->
<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
  {!! Form::label('name', 'Название страны:', ['class' => 'col-sm-2 control-label']) !!}
  <div class="col-sm-10">
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
    {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
  </div>
</div>

<!-- Код страны Form Input -->
<div class="form-group {{ $errors->has('phonecode') ? 'has-error' : '' }}">
  {!! Form::label('phonecode', 'Код страны:', ['class' => 'col-sm-2 control-label']) !!}
  <div class="col-sm-10">
    {!! Form::text('phonecode', null, ['class' => 'form-control']) !!}
    <span class="help-block">Код страны вводится без "+"</span>
    {!! $errors->first('phonecode', '<span class="help-block">:message</span>') !!}
  </div>
</div>

<div class="form-group">
  <div class="col-sm-offset-2 col-sm-10">
    <button type="submit" class="btn btn-primary">{{ $submitButtonText }}</button>
  </div>
</div>