<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dog".
 *
 * @property int $PetID
 * @property string $DogBreedType
 * @property string $TrainingLevel
 *
 * @property Pet $pet
 */
class Dog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['PetID', 'DogBreedType', 'TrainingLevel'], 'required'],
            [['PetID'], 'integer'],
            [['DogBreedType', 'TrainingLevel'], 'string'],
            [['PetID'], 'unique'],
            [['PetID'], 'exist', 'skipOnError' => true, 'targetClass' => Pet::className(), 'targetAttribute' => ['PetID' => 'PetID']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'PetID' => 'Pet ID',
            'DogBreedType' => 'Dog Breed Type',
            'TrainingLevel' => 'Training Level',
        ];
    }

    /**
     * Gets query for [[Pet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPet()
    {
        return $this->hasOne(Pet::className(), ['PetID' => 'PetID']);
    }
}
