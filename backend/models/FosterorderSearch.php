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

        // add conditions that should always apply here

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
