<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Fosterorder;

/**
 * FosterorderSearch represents the model behind the search form of `common\models\Fosterorder`.
 */
class FosterorderSearch extends Fosterorder
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['OrderID', 'CustomerID', 'PetID', 'ServiceID'], 'integer'],
            [['StartTime', 'EndTime', 'OrderStatus'], 'safe'],
            [['PaymentAmount'], 'number'],
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
        $query = Fosterorder::find();

        // 行级过滤
        $user = \Yii::$app->user->identity;
        if ($user && isset($user->role)) {
            if ($user->role === 'customer') {
                // 通过 user_id 反向查询获取 CustomerID
                $query->andWhere(['CustomerID' => $user->getCustomerId()]);
            } else if ($user->role === 'employee') {
                // employee 只能查看分配给自己的订单
                // 表名和字段名已根据数据库结构调整
                $query->alias('o')
                    ->innerJoin('order_employee oe', 'oe.OrderID = o.OrderID')
                    ->andWhere(['oe.EmployeeID' => $user->getEmployeeId()])
                    ->groupBy('o.OrderID');
            }
            // admin 可以看所有
        }

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
            'OrderID' => $this->OrderID,
            'CustomerID' => $this->CustomerID,
            'PetID' => $this->PetID,
            'ServiceID' => $this->ServiceID,
            'StartTime' => $this->StartTime,
            'EndTime' => $this->EndTime,
            'PaymentAmount' => $this->PaymentAmount,
        ]);

        $query->andFilterWhere(['like', 'OrderStatus', $this->OrderStatus]);

        return $dataProvider;
    }
}
