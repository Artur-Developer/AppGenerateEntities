<?php
namespace backend\controllers;

use backend\models\Apple;
use backend\models\Generator;
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
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $count_apple = intval(Yii::$app->request->post('count_apple'));
        $lock_file = fopen(\Yii::getAlias('@app') . "/runtime/generate-apple-file.lock", 'w');
        if(isset($count_apple) && is_numeric($count_apple)){
            if (!flock($lock_file, LOCK_EX | LOCK_NB)) {
                return "Already runninng\n";
            }
            $state = SettingsType::getStateApple(Apple::STATE_ON_TREE);
            $colors = SettingsType::getColors();
            $column_names = ['color','date_show','size','state'];
            $apple = new Apple();
            $generate = new Generator($apple,$column_names,$colors,$count_apple,$state);
            $generate->buildBranches();
        }

        flock($lock_file, LOCK_UN);
        fclose($lock_file);

        return ['result' => ['generate' => 1]];

    }

    public function actionIndex()
    {
        $lock_delete = 1;
        $apples = [];
        $count_apple = Apple::find()->count();

        return $this->render('index', [
            // Можно и всё сразу выводить со справочника, но если будут добавляться новые объекты, то лишний данные придётся перебирать.
            // разумней считаю делать строгую выборку по объекту
            'colors_apple' => SettingsType::find()->where(['object_name'=>'color_apple'])->all(),
            'states_apple' => SettingsType::find()->where(['object_name'=>'state_apple'])->all(),
            'count_apples' => Apple::find()->count(),
            'lock_delete' => $lock_delete,
            'apples' => $apples,
            'count_apple' => $count_apple,
        ]);
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
