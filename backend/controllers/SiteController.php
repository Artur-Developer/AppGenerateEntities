<?php
namespace backend\controllers;

use backend\models\Apple;
use backend\models\EntityFruit;
use backend\models\Generator;
use backend\models\SettingsType;
use Yii;
use yii\helpers\ArrayHelper;
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
                        'actions' => [
                            'logout',
                            'generate',
                            'index',
                            'delete-entity-data',
                        ],
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

    public function actionDeleteEntityData()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $entity_name = Yii::$app->request->post('entity');
        if (isset($entity_name) && $this->checkEntityExists($entity_name)){
            $entity = new EntityFruit($entity_name);
//            $count = $entity->deleteEntityToBatches();
            $count = $entity->getEntity()->deleteAll();
            return $count == 0 ?: ['result' => ['status' => true]];
        }
        return ['result' => ['status' => false]];
    }

    public function actionGenerate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $entity = Yii::$app->request->post('entity');
        $count_entity = Yii::$app->request->post('count');
        $batch_id = Yii::$app->request->post('batch_id');

        if (isset($entity) && $this->checkEntityExists($entity)
            && isset($count_entity) && is_numeric($count_entity)
        ) {
            $batch_id = isset($batch_id) && is_numeric($count_entity) ? intval($batch_id) : 0;
            $entity_fruit = new EntityFruit($entity);
            $entity = $entity_fruit->getEntity();
            $state = SettingsType::getState(SettingsType::STATE_ON_TREE);
            $colors = SettingsType::getColors();
            $column_names = ['color','date_show','size','state','batch'];
            $generate = new Generator($entity,$column_names,$colors,$count_entity,$state,$batch_id);
            $inserted = $generate->buildBranches();
        }
        else {
            $result =  ['result' => ['status' => SettingsType::INVALID_PARAMS]];
        }

        return isset($result)
            ? $result
            : [
                'result' => [
                'status' => Generator::STATUS_GENERATED,
                'inserted' => isset($inserted) ? $inserted : 0,
                'check_inserted' => intval($entity->find()->where(['batch' => $batch_id])->count())
            ]
        ];
    }

    public function actionEat()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $entity = Yii::$app->request->post('entity');
        $id = Yii::$app->request->post('id');

        if (isset($entity) && $this->checkEntityExists($entity)
            && isset($id) && is_numeric($id)
        ){
            $entity = new EntityFruit($entity);
            $entity->findEntity($id);
            if ($entity->eat()){
                $result = ['result' => [
                    'id' => $entity->getFoundObject()->id,
                    'size' => $entity->getSize()
                    ]
                ];
            }
        }

        return isset($result)
            ? $result
            : [
                'result' => [
                    'status' => SettingsType::INVALID_PARAMS,
                    'inserted' => isset($inserted) ? $inserted : 0
                ]
            ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'colors' => ArrayHelper::index(SettingsType::find()->where(['object_name'=>'color_apple'])->all(),'id'),
            'states' => ArrayHelper::index(SettingsType::find()->where(['object_name'=>'state_apple'])->all(),'id'),
            'entities' => $this->getEntitiesInfo(),
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
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    protected function getEntitiesInfo()
    {
        return SettingsType::getEntitiesInfo();
    }

    protected function checkEntityExists($entity)
    {
        $entity = new EntityFruit($entity);
        if (is_object($entity->getEntity())){
            return true;
        }
        return false;
    }
}
