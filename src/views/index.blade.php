@extends(Config::get('laravel-table-admin::view.parent_view'))

@section('title')
<?= e(HTML::transform($table)) ?>
@stop

@section('sub_content')
<div>
    <p>
        <?= link_to($newUrl, HTML::transform('new'), ['class' => 'btn btn-primary']) ?>
    </p>
</div>

<div class="table-responsive">
    <table class="table">
        <tr>
            <?php foreach ($columns as $column): ?>
                <th style="white-space: nowrap;"><?= e(HTML::transform($column->getName())) ?></th>
            <?php endforeach; ?>
            <th></th>
            <th></th>
        </tr>
        <?php foreach ($paginator as $record): ?>
            <tr>
                <?php foreach ($columns as $column): ?>
                    <td>
                        <?php
                        $value = null;
                        if ($column->isSelect()) {
                            $id = $record->{$column->getName()};
                            if (array_key_exists($id, $column->getSelectList())) {
                                $value = $column->getSelectList()[$id];
                            }
                        } else {
                            $value = $record->{$column->getName()};
                        }
                        ?>
                        <?= e($value) ?>
                    </td>
                <?php endforeach; ?>
                <td><?= link_to($editUrl . $record->id, HTML::transform('edit'), ['class' => 'btn btn-info']) ?></td>
                <td>
                    <?= Form::open(['url' => $deleteUrl . $record->id, 'method' => 'DELETE', 'onclick' => 'return confirm(\'' . HTML::transform('confirm_delete') . '\')']) ?>
                    <?= Form::submit(HTML::transform('delete'), ['class' => 'btn btn-danger']) ?>
                    <?= Form::close() ?>
                </td>
            </tr>
        <?php endforeach; ?>

    </table>
</div>

<?php echo $paginator->links(); ?>
@stop
