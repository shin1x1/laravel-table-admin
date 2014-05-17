@extends(Config::get('laravel-table-admin::view.parent_view'))

@section('title')
<?= e(HTML::transform($table)) ?>
@stop

@section('sub_content')
<div>
    <p>
        <?= link_to($backUrl, HTML::transform('back'), ['class' => 'btn btn-default']) ?>
    </p>
</div>

<?php
if (empty($data)) {
    $uri = $storeUrl;
    $method = 'POST';
} else {
    $uri = $updateUrl;
    $method = 'PUT';
}
?>
<?= Form::open(['url' => $uri, 'method' => $method, 'role' => 'form', 'class' => 'form-horizontal']) ?>
<?php foreach ($columns as $column): ?>
    <div class="form-group <?= $errors->has($column->getName()) ? 'has-error' : '' ?>">
        <label class="col-sm-2 control-label">
            <?= Form::label($column->getName(), e(HTML::transform($column->getName()))) ?>
        </label>
        <div class="col-sm-7">
            <?php
            if ($column->isLabel()) {
                if (!empty($data)) {
                    echo '<p class="form-control-static">' . e($data->{$column->getName()}) . '</p>';
                }
            } elseif ($column->isSelect()) {
                if (empty($data)) {
                    $id = null;
                } else {
                    $id = $data->{$column->getName()};
                }
                echo Form::select($column->getName(), $column->getSelectList(), $id, ['class' => 'form-control']);
            } else {
                if (empty($data)) {
                    echo Form::text($column->getName(), null, ['class' => 'form-control']);
                } else {
                    echo Form::text($column->getName(), $data->{$column->getName()}, ['class' => 'form-control']);
                }
            }
            ?>
            <?= $errors->first($column->getName(), '<p class="text-danger">:message</p>') ?>
        </div>
    </div>
<?php endforeach; ?>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-7">
        <?= Form::submit(HTML::transform('submit'), ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?= Form::close() ?>
@stop

