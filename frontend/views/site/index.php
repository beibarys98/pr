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
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;


$this->title = 'Baishev 2029';
?>

<?php Pjax::begin();?>

<div class="site-index">

    <?= Alert::widget() ?>
    <div class="row shadow-sm" style="border: 1px solid black; border-radius: 10px;">
        <div class="col-1 p-2" style="margin-top: 3px;">
            Год: <?= $y?>
        </div>
        <div class="col-1 p-2" style="margin-top: 3px;">
            <?= number_format($year->topBar, 2).' / 100%'?>
        </div>
        <div class="col-9 p-2" style="margin-top: 3px;">
            <div class="progress" style="height: 25px;">
                <div class="progress-bar" role="progressbar"
                     style="width: <?= $year->topBar?>%; background-color: #7091e6;"
                     aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
        <div class="col-1 p-1 d-flex justify-content-center">
            <?= Html::a('Отчет', ['/site/report', 'y' => $y],
                ['class' => 'btn btn-secondary', 'target' => '_blank', 'data-pjax' => '0'])?>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-4">
            <div class="shadow-sm" style="padding: 10px; border: 1px solid black; border-radius: 10px;">

                <label>Стратегическое направление</label>

                <div class="list-group shadow-sm">
                    <?php foreach ($strategyDP->query->all() as $str): ?>
                        <button type="button"
                                class="list-group-item list-group-item-action
                                <?= ($strategy && $str->id == $strategy->id) ? 'active' : '' ?>"
                                onclick="window.location='<?= Url::to(['/site/index', 'strategyID' => $str->id, 'y' => $y]) ?>'"
                                title="<?= htmlspecialchars($str->value, ENT_QUOTES) ?>">
                            <span style="display: inline-block; max-width: 100%; white-space: nowrap; overflow: hidden;
                            text-overflow: ellipsis;">
                                <?= $str->value ?>
                            </span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <br>
                <label>Задачи</label>
                <div class="list-group shadow-sm">

                    <?php foreach ($taskDP->query->all() as $tsk): ?>
                        <a href="<?= Url::to(['/site/index', 'strategyID' => $strategy->id, 'taskID' => $tsk->id, 'y' => $y]) ?>"
                           class="list-group-item list-group-item-action <?= ($tsk == $task) ? 'active' : '' ?>"
                            <?= ($tsk == $task) ? 'aria-current="true"' : '' ?>>
                            <?= $tsk->value ?>
                        </a>
                    <?php endforeach; ?>
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
                                    ->textarea(['disabled' => false, 'rows' => 4, 'style' => 'resize: none'])
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

<?php Pjax::end();?>
