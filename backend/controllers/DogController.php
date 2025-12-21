<?php

namespace backend\controllers;

use Yii;
use common\models\Dog;
use common\models\Pet;
use backend\models\DogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * DogController implements the CRUD actions for Dog model.
 */
class DogController extends Controller
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
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            // user 表的 role 字段：'admin' | 'employee' | 'customer'
                            $userRole = Yii::$app->user->identity->role ?? null;
                            $actionId = $action->id;
                            
                            // admin 全开
                            if ($userRole === 'admin') {
                                return true;
                            }
                            
                            // employee 只允许 index/view
                            if ($userRole === 'employee') {
                                return in_array($actionId, ['index', 'view']);
                            }
                            
                            // customer 允许 index/view/create/update/delete（但需要行级过滤）
                            if ($userRole === 'customer') {
                                return in_array($actionId, ['index', 'view', 'create', 'update', 'delete']);
                            }
                            
                            return false;
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    throw new ForbiddenHttpException('您没有权限访问此资源。');
                },
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
     * Lists all Dog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Dog model.
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
     * Creates a new Dog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Dog();

        if ($model->load(Yii::$app->request->post())) {
            // user 表的 role 字段；通过 customer.user_id 反查 CustomerID
            $userRole = Yii::$app->user->identity->role ?? null;
            $userId = Yii::$app->user->id;
            
            // customer 创建前必须校验 PetID 属于自己
            if ($userRole === 'customer') {
                // 通过 customer.user_id 查找 CustomerID
                $customer = \common\models\Customer::findOne(['user_id' => $userId]);
                if (!$customer) {
                    throw new NotFoundHttpException('未找到对应的客户信息。');
                }
                
                $petId = $model->PetID;
                $pet = Pet::findOne(['PetID' => $petId, 'CustomerID' => $customer->CustomerID]);
                if (!$pet) {
                    throw new NotFoundHttpException('您没有权限操作此宠物。');
                }
                // 使用字段白名单保存
                if ($model->save(false, ['PetID', 'DogBreedType', 'TrainingLevel'])) {
                    return $this->redirect(['view', 'id' => $model->PetID]);
                }
            } elseif ($userRole === 'admin') {
                // admin 全开
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->PetID]);
                }
            }
            // employee 不能 create（已由 AccessControl 阻止）
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Dog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            // user 表的 role 字段
            $userRole = Yii::$app->user->identity->role ?? null;
            
            // customer 和 employee 的 update 必须使用白名单保存（禁止修改 PetID）
            if (in_array($userRole, ['customer', 'employee'])) {
                if ($model->save(true, ['DogBreedType', 'TrainingLevel'])) {
                    return $this->redirect(['view', 'id' => $model->PetID]);
                }
            } else {
                // admin 全开
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->PetID]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Dog model.
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
     * Finds the Dog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Dog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        // user 表的 role 字段；通过 employee.user_id 和 customer.user_id 反查 ID
        $userRole = Yii::$app->user->identity->role ?? null;
        $userId = Yii::$app->user->id;
        
        if ($userRole === 'admin') {
            // admin 不加过滤
            $model = Dog::findOne($id);
        } elseif ($userRole === 'employee') {
            // 通过 employee.user_id 查找 EmployeeID
            $employee = \common\models\Employee::findOne(['user_id' => $userId]);
            if (!$employee) {
                throw new NotFoundHttpException('未找到对应的员工信息。');
            }
            
            // employee 只能找到自己匹配的 PetID
            // 通过 Order_Employee -> FosterOrder -> Pet -> Dog
            $model = Dog::find()
                ->alias('d')
                ->innerJoin('pet p', 'p.PetID = d.PetID')
                ->innerJoin('fosterorder fo', 'fo.PetID = p.PetID')
                ->innerJoin('order_employee oe', 'oe.OrderID = fo.OrderID')
                ->where(['d.PetID' => $id, 'oe.EmployeeID' => $employee->EmployeeID])
                ->one();
        } elseif ($userRole === 'customer') {
            // 通过 customer.user_id 查找 CustomerID
            $customer = \common\models\Customer::findOne(['user_id' => $userId]);
            if (!$customer) {
                throw new NotFoundHttpException('未找到对应的客户信息。');
            }
            
            // customer 只能找到属于自己的 PetID
            $model = Dog::find()
                ->alias('d')
                ->innerJoin('pet p', 'p.PetID = d.PetID')
                ->where(['d.PetID' => $id, 'p.CustomerID' => $customer->CustomerID])
                ->one();
        } else {
            $model = null;
        }
        
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
