<?php

/** @var yii\web\View $this */
/** @var $strategyDP*/
/** @var $strategy*/
/** @var $taskDP*/
/** @var $task*/
/** @var $progressDP*/
/** @var $progress*/
/** @var $year*/
/** @var $y*/

use common\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;


$this->title = 'Baishev 2029';
?>

<?php Pjax::begin();?>

<div class="site-index">

    <header>
        <link rel="stylesheet" type="text/css" href="style.css?v=timestamp">
    </header>

    <?= Alert::widget() ?>
    <div class="row shadow" style="border: 1px solid black; border-radius: 10px;">
        <div class="col-1 p-2" style="margin-top: 3px;">
            Год: <?= $y?>
        </div>
        <div class="col-1 p-2" style="margin-top: 3px;">
            <?= number_format($year->topBar, 2).' / 100%'?>
        </div>
        <div class="col-9 p-2" style="margin-top: 3px;">
            <div class="progress" style="height: 25px;">
                <div class="progress-bar" role="progressbar"
                     style="width: <?= $year->topBar?>%; background-color: #adbbda;"
                     aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
        <div class="col-1 p-1 d-flex justify-content-center">
            <?= Html::a('Отчет', ['/site/report', 'y' => $y],
                ['class' => 'btn btn-secondary', 'target' => '_blank', 'data-pjax' => '0'])?>
        </div>
    </div>

    <br>
    <div class="">
        <div class="row">
            <div class="col-4">
                <div class="shadow" style="padding: 10px; border: 1px solid black; border-radius: 10px;">
                    <div class="dropdown">
                        <label>Стратегическое направление</label>
                        <button class="cstmdrpdwnbtn"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if($strategy):?>
                                <?= $strategy->value?>
                            <?php endif;?>
                        </button>
                        <ul class="dropdown-menu w-100">
                            <?php foreach ($strategyDP->query->all() as $str):?>
                                <li><?= Html::a($str->value,
                                        ['/site/index', 'strategyID' => $str->id, 'y' => $y],
                                        [
                                            'class' => 'cstmdrpdwnitms',
                                            'style' => 'background-color: #ede8f5'
                                        ])?></li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                    <br>
                    <div class="dropdown">
                        <label>Задачи</label>
                        <ul class="list-group w-100 shadow" style="max-height: 440px; overflow: auto;">
                            <?php foreach ($taskDP->query->all() as $tsk):?>
                                <li>
                                    <?= Html::a($tsk->value,
                                        ['/site/index', 'strategyID' => $strategy->id, 'taskID' => $tsk->id, 'y' => $y],
                                        [
                                            'class' => 'cstmdrpdwnitms',
                                            'style' => ['background-color' => $tsk == $task ? '#ede8f5' : ''
                                            ]])?>
                                </li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <?php if($task):?>

                    <div style="border: 1px solid black; border-radius: 10px;" class="shadow p-3 d-flex">
                        <div class="col-6 pe-2">
                            <input class="form-control" value="Наименование мероприятий" disabled>
                        </div>

                        <div class="row">
                            <div class="col-3 pe-2">
                                <input class="form-control" value="Ед изм" disabled>
                            </div>
                            <div class="col-3 pe-2">
                                <input class="form-control" value="План" disabled>
                            </div>
                            <div class="col-3 pe-2">
                                <input class="form-control" value="Факт" disabled>
                            </div>
                            <div class="col-3 pe-2">
                                <input class="form-control" value="Отклон." disabled>
                            </div>
                        </div>

                    </div>

                    <br>

                    <div style="border: 1px solid black; border-radius: 10px;" class="shadow p-3">
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?php $i = 0;?>
                        <?php foreach ($progressDP->query->all() as $prg): ?>
                            <div class="d-flex">
                                <div class="col-6 pe-2">
                                    <?= $form->field($prg, "value")
                                        ->textarea(['disabled' => true, 'rows' => 4, 'style' => 'resize: none'])
                                        ->label(false) ?>
                                </div>
                                <div class="row">
                                    <div class="col-3 pe-2">
                                        <?= $form->field($prg, "unit")
                                            ->textInput(['disabled' => true])
                                            ->label(false) ?>
                                    </div>
                                    <div class="col-3 pe-2">
                                        <?= $form->field($prg, "plan")
                                            ->textInput(['disabled' => true])
                                            ->label(false) ?>
                                    </div>
                                    <div class="col-3 pe-2">
                                        <label>
                                            <input type="text" name="data[]" value="<?= $prg->progress?>" class="form-control" data-pjax="0">
                                        </label>
                                    </div>
                                    <div class="col-3 pe-2">
                                        <?= $form->field($prg, "otklon")
                                            ->textInput(['disabled' => true])
                                            ->label(false) ?>
                                    </div>
                                    <div class="row">
                                        <div class="progress" style="height: 25px; margin-left: 15px; ">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: <?= $prg->bar?>%; background-color: #adbbda;"><?= $prg->bar?>%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>

                        <?php endforeach; ?>

                        <div class="form-group mt-3">
                            <?= Html::submitButton(Yii::t('app', 'Сохранить'),
                                ['class' => 'btn btn-secondary w-100']) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                <?php endif;?>
            </div>
        </div>



    </div>

</div>

<?php Pjax::end();?>
