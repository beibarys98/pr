<?php

namespace frontend\controllers;

use common\models\LoginForm;
use common\models\Progress;
use common\models\Strategy;
use common\models\Task;
use common\models\Year;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\VerifyEmailForm;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;
use Yii;
use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use kartik\mpdf\Pdf;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex($strategyID = '', $taskID = '', $y = '2024')
    {
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/site/login']);
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

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['index']);
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(['login']);
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
