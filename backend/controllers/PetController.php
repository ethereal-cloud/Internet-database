<?php

namespace backend\controllers;

use Yii;
use common\models\Pet;
use backend\models\PetSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * PetController implements the CRUD actions for Pet model.
 */
class PetController extends Controller
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
                        // employee 只能查看匹配的宠物
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && isset($user->role) && $user->role === 'employee';
                        },
                    ],
                    [
                        // customer 可以 CRUD 自己的宠物
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
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
     * Lists all Pet models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PetSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Pet model.
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
     * Creates a new Pet model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Pet();
        $user = Yii::$app->user->identity;

        if ($model->load(Yii::$app->request->post())) {
            // customer 只能给自己创建宠物
            if ($user->role === 'customer') {
                // 通过 user_id 反向查询获取 CustomerID
                $model->CustomerID = $user->getCustomerId();
            }
            // admin 可以为任何客户创建
            
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->PetID]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Pet model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;

        if ($model->load(Yii::$app->request->post())) {
            if ($user->role === 'customer') {
                // customer 不能修改 PetID, CustomerID
                $safeAttributes = array_diff($model->attributes(), ['PetID', 'CustomerID']);
                if ($model->save(true, $safeAttributes)) {
                    return $this->redirect(['view', 'id' => $model->PetID]);
                }
            } else if ($user->role === 'admin') {
                // admin 不能修改 PetID
                $safeAttributes = array_diff($model->attributes(), ['PetID']);
                if ($model->save(true, $safeAttributes)) {
                    return $this->redirect(['view', 'id' => $model->PetID]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Pet model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;
        
        if ($user->role === 'customer') {
            // 通过 user_id 反向查询获取 CustomerID
            if ($model->CustomerID != $user->getCustomerId()) {
                throw new ForbiddenHttpException('您只能删除自己的宠物。');
            }
        }
        
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Pet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $user = Yii::$app->user->identity;
        
        if ($user->role === 'customer') {
            // 通过 user_id 反向查询获取 CustomerID
            $model = Pet::find()
                ->where(['PetID' => $id])
                ->andWhere(['CustomerID' => $user->getCustomerId()])
                ->one();
        } else if ($user->role === 'employee') {
            // employee 只能查看匹配的宠物：通过 order_employee -> fosterorder -> pet
            // 表名和字段名已根据数据库结构调整
            $model = Pet::find()
                ->alias('p')
                ->innerJoin('fosterorder o', 'o.PetID = p.PetID')
                ->innerJoin('order_employee oe', 'oe.OrderID = o.OrderID')
                ->where(['p.PetID' => $id])
                ->andWhere(['oe.EmployeeID' => $user->getEmployeeId()])
                ->one();
        } else {
            // admin 可以查看所有
            $model = Pet::findOne($id);
        }
        
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
