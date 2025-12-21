<?php

namespace backend\controllers;

use Yii;
use common\models\Customer;
use backend\models\CustomerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends Controller
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
                        // admin 增删改查
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && isset($user->role) && $user->role === 'admin';
                        },
                    ],
                    [
                        // employee 只能查看匹配的客户
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && isset($user->role) && $user->role === 'employee';
                        },
                    ],
                    [
                        // customer 只能 update/view/index 自己
                        'allow' => true,
                        'actions' => ['index', 'view', 'update'],
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
     * Lists all Customer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Customer model.
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
     * Creates a new Customer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customer();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->CustomerID]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;

        if ($user->role === 'customer') {
            // 通过 user_id 反向查询获取 CustomerID
            if ($model->CustomerID != $user->getCustomerId()) {
                throw new ForbiddenHttpException('您只能修改自己的信息。');
            }
            if ($model->load(Yii::$app->request->post())) {
                // 不允许修改 CustomerID, MemberLevel
                $safeAttributes = array_diff($model->attributes(), ['CustomerID', 'MemberLevel']);
                if ($model->save(true, $safeAttributes)) {
                    return $this->redirect(['view', 'id' => $model->CustomerID]);
                }
            }
        } else if ($user->role === 'admin') {
            // admin 不能修改 CustomerID
            if ($model->load(Yii::$app->request->post())) {
                $safeAttributes = array_diff($model->attributes(), ['CustomerID']);
                if ($model->save(true, $safeAttributes)) {
                    return $this->redirect(['view', 'id' => $model->CustomerID]);
                }
            }
        } else {
            // employee 不允许 update
            throw new ForbiddenHttpException('员工不能修改客户信息。');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Customer model.
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
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $user = Yii::$app->user->identity;
        
        if ($user->role === 'customer') {
            // 通过 user_id 反向查询获取 CustomerID
            $model = Customer::find()
                ->where(['CustomerID' => $id])
                ->andWhere(['CustomerID' => $user->getCustomerId()])
                ->one();
        } else if ($user->role === 'employee') {
            // employee 只能查看匹配的客户：通过 order_employee -> fosterorder -> customer
            // 表名和字段名已根据数据库结构调整
            $model = Customer::find()
                ->alias('c')
                ->innerJoin('fosterorder o', 'o.CustomerID = c.CustomerID')
                ->innerJoin('order_employee oe', 'oe.OrderID = o.OrderID')
                ->where(['c.CustomerID' => $id])
                ->andWhere(['oe.EmployeeID' => $user->getEmployeeId()])
                ->one();
        } else {
            // admin 可以查看所有
            $model = Customer::findOne($id);
        }
        
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
