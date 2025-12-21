<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Employee;

/**
 * EmployeeSearch represents the model behind the search form of `common\models\Employee`.
 */
class EmployeeSearch extends Employee
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['EmployeeID', 'user_id'], 'integer'],
            [['Name', 'Gender', 'Position', 'Contact', 'HireDate'], 'safe'],
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
        $query = Employee::find();

        // 行级过滤：employee 只能看自己
        $user = \Yii::$app->user->identity;
        if ($user && isset($user->role) && $user->role === 'employee') {
            // 通过 user_id 反向查询获取 EmployeeID
            $query->andWhere(['EmployeeID' => $user->getEmployeeId()]);
        }
        // admin 可以看所有

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
            'EmployeeID' => $this->EmployeeID,
            'user_id' => $this->user_id,
            'HireDate' => $this->HireDate,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Gender', $this->Gender])
            ->andFilterWhere(['like', 'Position', $this->Position])
            ->andFilterWhere(['like', 'Contact', $this->Contact]);

        return $dataProvider;
    }
}
