<?php

namespace frontend\controllers;

use common\models\LoginForm;
use common\models\Progress;
use common\models\Strategy;
use common\models\Task;
use common\models\User;
use common\models\Year;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use kartik\mpdf\Pdf;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex($strategyID = '', $taskID = '', $y = null)
    {
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/site/login']);
        }

        if ($y === null) {
            $y = date('Y');
        }

        $strategyDP = new ActiveDataProvider([
            'query' => Strategy::find()
        ]);
        $strategy = Strategy::findOne(['id' => $strategyID]);

        $taskDP = new ActiveDataProvider([
            'query' => Task::find()
                ->andWhere(['strategy_id' => $strategyID])
        ]);
        $task  = Task::findOne(['id' => $taskID]);

        $year = Year::find()->andWhere(['year' => $y])->one();
        $progressDP = new ActiveDataProvider([
            'query' => Progress::find()
                ->andWhere(['year_id' => $year->id])
                ->andWhere(['task_id' => $taskID])
        ]);

        //update
        $dataArray = Yii::$app->request->post('data');

        $progress = Progress::find()
            ->andWhere(['year_id' => $year->id])
            ->andWhere(['task_id' => $taskID])
            ->all();

        $i = 0;
        if (!empty($dataArray)){
            foreach ($dataArray as $value){
                $progress[$i]->progress = $value;

                //bar
                if($progress[$i]->plan == '+'){
                    if($progress[$i]->progress == '+'){
                        $progress[$i]->bar = 100;
                        $progress[$i]->otklon = 0;
                    }else{
                        $progress[$i]->bar = 0;
                        $progress[$i]->progress = '-';
                        $progress[$i]->otklon = -1;
                    }
                }elseif($progress[$i]->plan == '-'){
                    if($progress[$i]->progress == '+'){
                        $progress[$i]->bar = 100;
                        $progress[$i]->otklon = 1;
                    }else{
                        $progress[$i]->bar = 0;
                        $progress[$i]->progress = '-';
                        $progress[$i]->otklon = 0;
                    }
                }else{
                    if($progress[$i]->progress == ''){
                        $progress[$i]->progress = 0;
                    }
                    $progressValue = str_replace(',', '.', $progress[$i]->progress);
                    $planValue = str_replace(',', '.', $progress[$i]->plan);
                    if (!is_numeric($progressValue) || !is_numeric($planValue)) {
                        Yii::$app->session->setFlash('danger', 'Введите цифру!');
                        return $this->redirect(['index', 'strategyID' => $strategyID, 'taskID' => $taskID]);
                    }
                    $progress[$i]->progress = $progressValue;
                    $progress[$i]->plan = $planValue;
                    $progress[$i]->otklon = $progressValue - $planValue;
                    if($progressValue >= $planValue){
                        $progress[$i]->bar = 100;
                    }else{
                        $progress[$i]->bar = $progressValue * 100 / $planValue;
                    }
                }

                $progress[$i]->save(false);
                $i++;
            }
        }

        return $this->render('index', [
            'strategyDP' => $strategyDP,
            'strategy' => $strategy,
            'taskDP' => $taskDP,
            'task' => $task,
            'progressDP' => $progressDP,
            'year' => $year,
            'y' => $y
        ]);
    }

    public function actionReport($y = '')
    {
        $strategy = new ActiveDataProvider([
            'query' => Strategy::find()
        ]);
        $task = new ActiveDataProvider([
            'query' => Task::find()
        ]);
        $year = Year::find()->andWhere(['year' => $y])->one();
        $progress = new ActiveDataProvider([
            'query' => Progress::find()
                ->andWhere(['year_id' => $year->id])
        ]);

        $content = $this->renderPartial('report', [
            'strategy' => $strategy,
            'task' => $task,
            'progress' => $progress,
            'year' => $y
        ]);
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}'
        ]);
        return $pdf->render();
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {

            $model->username = 'odmin';
            $model->login();

            return $this->redirect(['index']);
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(['login']);
    }

    public function actionSignup()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {

            $model->generateAuthKey();
            $model->setPassword($model->password);
            $model->save();

            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }
}
