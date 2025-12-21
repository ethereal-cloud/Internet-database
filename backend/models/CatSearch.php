<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Cat;

/**
 * CatSearch represents the model behind the search form of `common\models\Cat`.
 */
class CatSearch extends Cat
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['PetID'], 'integer'],
            [['FurLength', 'Personality'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Cat::find()->alias('c');

        // user 表的 role 字段；通过 employee.user_id 和 customer.user_id 反查 ID
        $userRole = \Yii::$app->user->identity->role ?? null;
        $userId = \Yii::$app->user->id;
        
        // 行级权限过滤
        if ($userRole === 'employee') {
            // 通过 employee.user_id 查找 EmployeeID
            $employee = \common\models\Employee::findOne(['user_id' => $userId]);
            if ($employee) {
                // employee 只能看到自己匹配的 Cat（通过 Order_Employee->FosterOrder->Pet->Cat）
                $query->innerJoin('pet p', 'p.PetID = c.PetID')
                      ->innerJoin('fosterorder fo', 'fo.PetID = p.PetID')
                      ->innerJoin('order_employee oe', 'oe.OrderID = fo.OrderID')
                      ->andWhere(['oe.EmployeeID' => $employee->EmployeeID])
                      ->groupBy('c.PetID'); // 避免重复
            } else {
                $query->where('1=0'); // 未找到员工信息，返回空
            }
        } elseif ($userRole === 'customer') {
            // 通过 customer.user_id 查找 CustomerID
            $customer = \common\models\Customer::findOne(['user_id' => $userId]);
            if ($customer) {
                // customer 只能看到属于自己的 Cat（通过 Pet.CustomerID）
                $query->innerJoin('pet p', 'p.PetID = c.PetID')
                      ->andWhere(['p.CustomerID' => $customer->CustomerID]);
            } else {
                $query->where('1=0'); // 未找到客户信息，返回空
            }
        }
        // admin 不加过滤

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'c.PetID' => $this->PetID,
        ]);

        $query->andFilterWhere(['like', 'c.FurLength', $this->FurLength])
            ->andFilterWhere(['like', 'c.Personality', $this->Personality]);

        return $dataProvider;
    }
}
