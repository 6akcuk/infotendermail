<a href="{{ route($updateRoute, $model) }}" class="btn btn-sm btn-info pull-left">
    <i class="fa fa-paste"></i>
    Ред-ть
</a>
@if (!isset($deleteCondition) || (isset($deleteCondition) && $deleteCondition($model)))
    <div class="pull-left m-l-xs">
        {!! Form::open(['route' => [$destroyRoute, $model], 'method' => 'DELETE']) !!}
        <a class="btn btn-sm btn-danger" onclick="confirmDelete(this)">
            <i class="fa fa-remove"></i>
            Удалить
        </a>
        {!! Form::close() !!}
    </div>
@endif