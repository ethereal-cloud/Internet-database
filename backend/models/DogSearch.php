<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Dog;

/**
 * DogSearch represents the model behind the search form of `common\models\Dog`.
 */
class DogSearch extends Dog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['PetID'], 'integer'],
            [['DogBreedType', 'TrainingLevel'], 'safe'],
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
        $query = Dog::find();

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
            'PetID' => $this->PetID,
        ]);

        $query->andFilterWhere(['like', 'DogBreedType', $this->DogBreedType])
            ->andFilterWhere(['like', 'TrainingLevel', $this->TrainingLevel]);

        return $dataProvider;
    }
}
