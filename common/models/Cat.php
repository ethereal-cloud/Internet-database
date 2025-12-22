<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cat".
 *
 * @property int $PetID
 * @property string $FurLength
 * @property string $Personality
 *
 * @property Pet $pet
 */
class Cat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['PetID', 'FurLength', 'Personality'], 'required'],
            [['PetID'], 'integer'],
            [['FurLength'], 'string'],
            [['Personality'], 'string', 'max' => 45],
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
            'PetID' => '宠物编号',
            'FurLength' => '毛长',
            'Personality' => '性格',
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
