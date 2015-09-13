<!-- Название города Form Input -->
<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
  {!! Form::label('name', 'Название города:', ['class' => 'col-sm-2 control-label']) !!}
  <div class="col-sm-10">
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
    {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
  </div>
</div>

<!-- Страна Form Input -->
<div class="form-group {{ $errors->has('country_id') ? 'has-error' : '' }}">
  {!! Form::label('country_id', 'Страна:', ['class' => 'col-sm-2 control-label']) !!}
  <div class="col-sm-10">
    {!! Form::select('country_id', $countriesList, null, ['class' => 'form-control chosen-select']) !!}
    {!! $errors->first('country_id', '<span class="help-block">:message</span>') !!}
  </div>
</div>

<!-- Код города Form Input -->
<div class="form-group {{ $errors->has('phonecode') ? 'has-error' : '' }}">
  {!! Form::label('phonecode', 'Код города:', ['class' => 'col-sm-2 control-label']) !!}
  <div class="col-sm-10">
    {!! Form::text('phonecode', null, ['class' => 'form-control']) !!}
    <span class="help-block">Код города вводится без "+"</span>
    {!! $errors->first('phonecode', '<span class="help-block">:message</span>') !!}
  </div>
</div>

<div class="form-group">
  <div class="col-sm-offset-2 col-sm-10">
    <button type="submit" class="btn btn-primary">{{ $submitButtonText }}</button>
  </div>
</div>

@section('footer_js')
  <!-- Chosen -->
  <script src="js/plugins/chosen/chosen.jquery.js"></script>
  <script>$('#country_id').chosen();</script>
@stop