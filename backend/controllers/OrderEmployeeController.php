<?php

namespace backend\controllers;

use Yii;
use common\models\OrderEmployee;
use backend\models\OrderEmployeeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderEmployeeController implements the CRUD actions for OrderEmployee model.
 */
class OrderEmployeeController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all OrderEmployee models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderEmployeeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderEmployee model.
     * @param integer $OrderID
     * @param integer $EmployeeID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($OrderID, $EmployeeID)
    {
        return $this->render('view', [
            'model' => $this->findModel($OrderID, $EmployeeID),
        ]);
    }

    /**
     * Creates a new OrderEmployee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderEmployee();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'OrderID' => $model->OrderID, 'EmployeeID' => $model->EmployeeID]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderEmployee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $OrderID
     * @param integer $EmployeeID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($OrderID, $EmployeeID)
    {
        $model = $this->findModel($OrderID, $EmployeeID);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'OrderID' => $model->OrderID, 'EmployeeID' => $model->EmployeeID]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing OrderEmployee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $OrderID
     * @param integer $EmployeeID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($OrderID, $EmployeeID)
    {
        $this->findModel($OrderID, $EmployeeID)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the OrderEmployee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $OrderID
     * @param integer $EmployeeID
     * @return OrderEmployee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($OrderID, $EmployeeID)
    {
        if (($model = OrderEmployee::findOne(['OrderID' => $OrderID, 'EmployeeID' => $EmployeeID])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
