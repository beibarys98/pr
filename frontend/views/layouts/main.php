<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\models\Year;
use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <link rel="icon" href="<?= Yii::getAlias('@web') ?>/logo.png" type="image/x-icon">

    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>

    <title><?= Html::encode($this->title) ?></title>

    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => Html::img('@web/logo.png', ['alt' => 'Logo', 'style' => 'height:30px; margin-right:10px;'])
            . Yii::t('app', 'Baishev 2029'),
        'brandUrl' => \yii\helpers\Url::to(['site/index', 'y' => date('Y')]),
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark fixed-top shadow-sm',
            'style' => 'background-color: #3d52a0',
        ],
    ]);

    $menuItems = [];
    if (!Yii::$app->user->isGuest) {
        // Fetch all years from the database
        $years = Year::find()->orderBy(['year' => SORT_ASC])->all();

        // Loop through each year and add it as a separate item
        foreach ($years as $model) {
            $menuItems[] = [
                'label' => $model->year,
                'url' => ['/site/index', 'y' => $model->year]
            ];
        }
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav me-auto mb-2 mb-md-0'],
        'items' => $menuItems,
    ]);
    if (Yii::$app->user->isGuest) {
        //echo Html::tag('div',Html::a('Signup',['/site/signup'],['class' => ['btn btn-link login text-decoration-none']]),['class' => ['d-flex']]);
        //echo Html::tag('div',Html::a('Login',['/site/login'],['class' => ['btn btn-link login text-decoration-none']]),['class' => ['d-flex']]);
    } else {
        echo Html::beginForm(['/site/logout'], 'post', ['class' => 'd-flex'])
            . Html::submitButton(
                'Шығу',
                ['class' => 'btn btn-link logout text-decoration-none']
            )
            . Html::endForm();
    }
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>
 
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
