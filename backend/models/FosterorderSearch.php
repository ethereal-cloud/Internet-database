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
    public $PetName;
    public $ServiceType;
    public $ServiceDisplay;
    public $EmployeeName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['OrderID', 'CustomerID', 'PetID', 'ServiceID'], 'integer'],
            [['StartTime', 'EndTime', 'OrderStatus', 'PetName', 'ServiceType', 'ServiceDisplay', 'EmployeeName'], 'safe'],
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
        $query->alias('o')->joinWith(['pet p', 'service s'])->joinWith(['employees e'])->groupBy('o.OrderID');

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
            'o.OrderID' => $this->OrderID,
            'o.CustomerID' => $this->CustomerID,
            'o.PetID' => $this->PetID,
            'o.ServiceID' => $this->ServiceID,
            'o.StartTime' => $this->StartTime,
            'o.EndTime' => $this->EndTime,
            'o.PaymentAmount' => $this->PaymentAmount,
        ]);

        $query->andFilterWhere(['like', 'o.OrderStatus', $this->OrderStatus]);
        $query->andFilterWhere(['like', 'p.PetName', $this->PetName]);
        // 服务过滤：如果填了“类型/适用宠物”两段，分别匹配；否则对类型和适用宠物做模糊匹配
        if ($this->ServiceDisplay) {
            $parts = array_map('trim', explode('/', $this->ServiceDisplay));
            if (count($parts) >= 2) {
                $query->andFilterWhere(['like', 's.ServiceType', $parts[0]]);
                $query->andFilterWhere(['like', 's.PetCategory', $parts[1]]);
            } else {
                $query->andFilterWhere(['or',
                    ['like', 's.ServiceType', $this->ServiceDisplay],
                    ['like', 's.PetCategory', $this->ServiceDisplay],
                ]);
            }
        } else {
            $query->andFilterWhere(['like', 's.ServiceType', $this->ServiceType]);
        }
        $query->andFilterWhere(['like', 'e.Name', $this->EmployeeName]);

        return $dataProvider;
    }
}
