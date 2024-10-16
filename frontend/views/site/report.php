<?php
/**
 * @var $strategy
 * @var $task
 * @var $progress
 * @var $year
 */
?>

<!DOCTYPE html>
<html lang="en">
<body>
<table class="table table-bordered">
    <tr>
        <th rowspan="3">№<br>п/п</th>
        <th rowspan="3">Целевые индикаторы</th>
        <th rowspan="3">Ед.<br>изм.</th>
        <th colspan="7" style="text-align: center">Год</th>
    </tr>
    <tr>
        <th colspan="7" style="text-align: center"><?= $year?></th>
    </tr>
    <tr>
        <th colspan="2" style="text-align: center">План</th>
        <th colspan="2" style="text-align: center">Факт</th>
        <th colspan="3" style="text-align: center">Отклонение</th>
    </tr>

    <?php foreach ($strategy->query->all() as $str):?>
        <tr>
            <td colspan="10" style="text-align: center"><strong><?= $str->value?></strong></td>
        </tr>

        <?php foreach ($task->query->all() as $tsk):?>
            <?php if ($tsk->strategy_id == $str->id):?>
                <tr>
                    <td colspan="10"><strong><?= $tsk->value?></strong></td>
                </tr>
                <?php $i = 1;?>
                <?php foreach ($progress->query->all() as $prg):?>
                    <?php if ($prg->task_id == $tsk->id):?>
                        <tr>
                            <th><?= $i++;?></th>
                            <td><?= $prg->value?></td>
                            <td><?= $prg->unit?></td>
                            <td colspan="2"><?= $prg->plan?></td>
                            <td colspan="2"><?= $prg->progress?></td>
                            <td colspan="3"><?= $prg->otklon?></td>
                        </tr>
                    <?php endif;?>
                <?php endforeach;?>
            <?php endif;?>
        <?php endforeach;?>
    <?php endforeach;?>
</table>
</body>
</html>