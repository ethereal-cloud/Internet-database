<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Pet;

/**
 * PetSearch represents the model behind the search form of `common\models\Pet`.
 */
class PetSearch extends Pet
{
    public $Type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['PetID', 'CustomerID', 'AgeYears', 'AgeMonths'], 'integer'],
            [['PetName', 'Gender', 'HealthStatus', 'Type'], 'safe'],
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
        $query = Pet::find();

        // 行级过滤
        $user = \Yii::$app->user->identity;
        if ($user && isset($user->role)) {
            if ($user->role === 'customer') {
                // 通过 user_id 反向查询获取 CustomerID
                $query->andWhere(['CustomerID' => $user->getCustomerId()]);
            } else if ($user->role === 'employee') {
                // employee 只能查看匹配的宠物
                // 表名和字段名已根据数据库结构调整
                $query->alias('p')
                    ->innerJoin('fosterorder o', 'o.PetID = p.PetID')
                    ->innerJoin('order_employee oe', 'oe.OrderID = o.OrderID')
                    ->andWhere(['oe.EmployeeID' => $user->getEmployeeId()])
                    ->groupBy('p.PetID');
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
            'PetID' => $this->PetID,
            'CustomerID' => $this->CustomerID,
            'AgeYears' => $this->AgeYears,
            'AgeMonths' => $this->AgeMonths,
        ]);

        $query->andFilterWhere(['like', 'PetName', $this->PetName])
            ->andFilterWhere(['like', 'Gender', $this->Gender])
            ->andFilterWhere(['like', 'HealthStatus', $this->HealthStatus]);

        // 类型筛选（猫/狗）
        if ($this->Type === 'cat') {
            $query->innerJoinWith('cat');
        } elseif ($this->Type === 'dog') {
            $query->innerJoinWith('dog');
        }

        return $dataProvider;
    }
}
