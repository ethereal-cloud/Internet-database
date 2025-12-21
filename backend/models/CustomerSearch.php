<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Customer;

/**
 * CustomerSearch represents the model behind the search form of `common\models\Customer`.
 */
class CustomerSearch extends Customer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['CustomerID', 'user_id'], 'integer'],
            [['Name', 'Gender', 'Contact', 'Address', 'MemberLevel'], 'safe'],
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
        $query = Customer::find();

        // 行级过滤
        $user = \Yii::$app->user->identity;
        if ($user && isset($user->role)) {
            if ($user->role === 'customer') {
                // 通过 user_id 反向查询获取 CustomerID
                $query->andWhere(['CustomerID' => $user->getCustomerId()]);
            } else if ($user->role === 'employee') {
                // employee 只能查看匹配的客户
                // 表名和字段名已根据数据库结构调整
                $query->alias('c')
                    ->innerJoin('fosterorder o', 'o.CustomerID = c.CustomerID')
                    ->innerJoin('order_employee oe', 'oe.OrderID = o.OrderID')
                    ->andWhere(['oe.EmployeeID' => $user->getEmployeeId()])
                    ->groupBy('c.CustomerID');
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
            'CustomerID' => $this->CustomerID,
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Gender', $this->Gender])
            ->andFilterWhere(['like', 'Contact', $this->Contact])
            ->andFilterWhere(['like', 'Address', $this->Address])
            ->andFilterWhere(['like', 'MemberLevel', $this->MemberLevel]);

        return $dataProvider;
    }
}
