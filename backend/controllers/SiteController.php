<?php
namespace backend\controllers;

use backend\models\Apple;
use backend\models\SettingsType;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

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
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'generate','index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
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
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionGenerate()
    {
        $lock_delete = 1;
        $apples = [];

        return $this->render('index', [
            // Можно и всё сразу выводить со справочника, но если будут добавляться новые объекты, то лишний данные придётся перебирать.
            // разумней считаю делать строгую выорку по объекту
            'colors_apple' => SettingsType::find()->where(['object_name'=>'color_apple'])->all(),
            'states_apple' => SettingsType::find()->where(['object_name'=>'state_apple'])->all(),
            'count_apples' => Apple::find()->count(),
            'lock_delete' => $lock_delete,
            'apples' => $apples,
        ]);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
