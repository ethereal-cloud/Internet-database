<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Fosterservice;

/**
 * FosterserviceSearch represents the model behind the search form of `common\models\Fosterservice`.
 */
class FosterserviceSearch extends Fosterservice
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ServiceID', 'Duration'], 'integer'],
            [['ServiceType', 'PetCategory'], 'safe'],
            [['Price'], 'number'],
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
        $query = Fosterservice::find();

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
            'ServiceID' => $this->ServiceID,
            'Price' => $this->Price,
            'Duration' => $this->Duration,
        ]);

        $query->andFilterWhere(['like', 'ServiceType', $this->ServiceType])
            ->andFilterWhere(['like', 'PetCategory', $this->PetCategory]);

        return $dataProvider;
    }
}
