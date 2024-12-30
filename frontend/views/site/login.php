<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Login';
?>
<div class="site-login" style="margin: 20vh auto; width: 300px;" >

    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Құпия сөз'])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Кіру', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
