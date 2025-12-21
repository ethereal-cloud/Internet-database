<?php

namespace backend\controllers;

use Yii;
use common\models\Employee;
use backend\models\EmployeeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends Controller
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
                            // user.role 字段已存在于数据库
                            return $user && isset($user->role) && $user->role === 'admin';
                        },
                    ],
                    [
                        // employee 只允许 view/index，且只能看自己
                        'allow' => true,
                        'actions' => ['index', 'view', 'update'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && isset($user->role) && $user->role === 'employee';
                        },
                    ],
                    [
                        // customer 全部拒绝
                        'allow' => false,
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && isset($user->role) && $user->role === 'customer';
                        },
                        'denyCallback' => function ($rule, $action) {
                            throw new ForbiddenHttpException('客户无权访问员工信息。');
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
     * Lists all Employee models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmployeeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Employee model.
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
     * Creates a new Employee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Employee();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->EmployeeID]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Employee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;

        // employee 只能改自己的 Contact
        if ($user->role === 'employee') {
            // 通过 user_id 反向查询获取 EmployeeID
            if ($model->EmployeeID != $user->getEmployeeId()) {
                throw new ForbiddenHttpException('您只能修改自己的信息。');
            }
            if ($model->load(Yii::$app->request->post())) {
                // 只保存 Contact 字段
                if ($model->save(true, ['Contact'])) {
                    return $this->redirect(['view', 'id' => $model->EmployeeID]);
                }
            }
        } else {
            // admin 可以修改除主键外的所有字段
            if ($model->load(Yii::$app->request->post())) {
                // 白名单：不允许修改 EmployeeID
                $safeAttributes = array_diff($model->attributes(), ['EmployeeID']);
                if ($model->save(true, $safeAttributes)) {
                    return $this->redirect(['view', 'id' => $model->EmployeeID]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Employee model.
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
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $user = Yii::$app->user->identity;
        
        // employee 只能查看自己
        if ($user->role === 'employee') {
            // 通过 user_id 反向查询获取 EmployeeID
            $model = Employee::find()
                ->where(['EmployeeID' => $id])
                ->andWhere(['EmployeeID' => $user->getEmployeeId()])
                ->one();
        } else {
            // admin 可以查看所有
            $model = Employee::findOne($id);
        }
        
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
