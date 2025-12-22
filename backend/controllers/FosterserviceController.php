<?php

namespace backend\controllers;

use Yii;
use common\models\Fosterservice;
use backend\models\FosterserviceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * FosterserviceController implements the CRUD actions for Fosterservice model.
 */
class FosterserviceController extends Controller
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
                        // admin 全部允许
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && isset($user->role) && $user->role === 'admin';
                        },
                    ],
                    [
                        // employee 只能查看
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && isset($user->role) && $user->role === 'employee';
                        },
                    ],
                    [
                        // customer 只能查看
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && isset($user->role) && $user->role === 'customer';
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Fosterservice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FosterserviceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Fosterservice model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Fosterservice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Fosterservice();
        
        // 自动生成ServiceID
        $maxId = Fosterservice::find()->max('ServiceID');
        $model->ServiceID = $maxId ? $maxId + 1 : 1;

        if ($model->load(Yii::$app->request->post())) {
            // 防止ID被修改
            $model->ServiceID = $maxId ? $maxId + 1 : 1;
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->ServiceID]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Fosterservice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            // admin 不能修改 ServiceID
            $safeAttributes = array_diff($model->attributes(), ['ServiceID']);
            if ($model->save(true, $safeAttributes)) {
                return $this->redirect(['view', 'id' => $model->ServiceID]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Fosterservice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Fosterservice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Fosterservice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Fosterservice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
