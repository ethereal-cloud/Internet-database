<?php

namespace backend\controllers;

use Yii;
use common\models\Fosterorder;
use common\models\Pet;
use backend\models\FosterorderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * FosterorderController implements the CRUD actions for Fosterorder model.
 */
class FosterorderController extends Controller
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
                        // admin 允许 index/view/create/delete，但禁止 update
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'delete'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && isset($user->role) && $user->role === 'admin';
                        },
                    ],
                    [
                        // employee 只能查看匹配的订单
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && isset($user->role) && $user->role === 'employee';
                        },
                    ],
                    [
                        // customer 可以 create/view/index/update(支付)
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update'],
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
     * Lists all Fosterorder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FosterorderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->with(['pet', 'service', 'employees']);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Fosterorder model.
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
     * Creates a new Fosterorder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Fosterorder();
        $user = Yii::$app->user->identity;
        
        // 自动生成OrderID
        $maxId = Fosterorder::find()->max('OrderID');
        $model->OrderID = $maxId ? $maxId + 1 : 1;

        if ($model->load(Yii::$app->request->post())) {
            // 防止ID被修改
            $model->OrderID = $maxId ? $maxId + 1 : 1;
            if ($user->role === 'customer') {
                // 通过 user_id 反向查询获取 CustomerID
                // 检查 PetID 是否属于当前客户
                $pet = Pet::findOne($model->PetID);
                if (!$pet || $pet->CustomerID != $user->getCustomerId()) {
                    Yii::$app->session->setFlash('error', '您只能为自己的宠物下单。');
                    return $this->render('create', ['model' => $model]);
                }
                // 强制设置 CustomerID
                $model->CustomerID = $user->getCustomerId();
            }
            // admin 可以为任何客户/宠物创建订单
            
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->OrderID]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Fosterorder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;

        // admin 禁止 update
        if ($user->role === 'admin') {
            throw new ForbiddenHttpException('管理员不能修改订单。');
        }
        
        // employee 禁止 update
        if ($user->role === 'employee') {
            throw new ForbiddenHttpException('员工不能修改订单。');
        }

        // customer 只能支付：从 '待支付' -> '已支付但未开始'
        if ($user->role === 'customer') {
            // 通过 user_id 反向查询获取 CustomerID
            if ($model->CustomerID != $user->getCustomerId()) {
                throw new ForbiddenHttpException('您只能修改自己的订单。');
            }
            
            // 检查当前状态（数据库中 OrderStatus 为 '未支付' 或 '已支付'）
            if ($model->OrderStatus !== '未支付') {
                throw new ForbiddenHttpException('只有待支付状态的订单才能支付。');
            }
            
            if ($model->load(Yii::$app->request->post())) {
                // TODO: 如果有 PayCode 字段，请取消注释下面的代码
                // if (empty($model->PayCode) || $model->PayCode !== Yii::$app->request->post('paycode')) {
                //     Yii::$app->session->setFlash('error', '支付码不正确。');
                //     return $this->render('update', ['model' => $model]);
                // }
                
                // 只允许修改 OrderStatus (和 PaidAt 如果存在)
                $model->OrderStatus = '已支付';
                // 如果有 PaidAt 字段
                // $model->PaidAt = date('Y-m-d H:i:s');
                
                $safeAttributes = ['OrderStatus'];
                // 如果有 PaidAt 字段，加入白名单
                // $safeAttributes[] = 'PaidAt';
                
                if ($model->save(true, $safeAttributes)) {
                    Yii::$app->session->setFlash('success', '支付成功！');
                    return $this->redirect(['view', 'id' => $model->OrderID]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Fosterorder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $user = Yii::$app->user->identity;
        
        // 只有 admin 可以删除
        if ($user->role !== 'admin') {
            throw new ForbiddenHttpException('您没有权限删除订单。');
        }
        
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Fosterorder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Fosterorder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $user = Yii::$app->user->identity;
        
        if ($user->role === 'customer') {
            // 通过 user_id 反向查询获取 CustomerID
            $model = Fosterorder::find()
                ->where(['OrderID' => $id])
                ->with(['pet', 'service', 'employees'])
                ->andWhere(['CustomerID' => $user->getCustomerId()])
                ->one();
        } else if ($user->role === 'employee') {
            // employee 只能查看分配给自己的订单
            // 表名和字段名已根据数据库结构调整
            $model = Fosterorder::find()
                ->alias('o')
                ->innerJoin('order_employee oe', 'oe.OrderID = o.OrderID')
                ->where(['o.OrderID' => $id])
                ->with(['pet', 'service', 'employees'])
                ->andWhere(['oe.EmployeeID' => $user->getEmployeeId()])
                ->one();
        } else {
            // admin 可以查看所有
            $model = Fosterorder::find()->with(['pet', 'service', 'employees'])->where(['OrderID' => $id])->one();
        }
        
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
