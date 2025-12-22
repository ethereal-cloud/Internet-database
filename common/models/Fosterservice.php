<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "fosterservice".
 *
 * @property int $ServiceID
 * @property string $ServiceType
 * @property string $PetCategory
 * @property float $Price
 * @property int $Duration
 *
 * @property Fosterorder[] $fosterorders
 */
class Fosterservice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fosterservice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ServiceID', 'ServiceType', 'PetCategory', 'Price', 'Duration'], 'required'],
            [['ServiceID', 'Duration'], 'integer'],
            [['ServiceType', 'PetCategory'], 'string'],
            [['Price'], 'number'],
            [['ServiceID'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ServiceID' => '服务编号',
            'ServiceType' => '服务类型',
            'PetCategory' => '适用宠物',
            'Price' => '价格',
            'Duration' => '持续天数',
        ];
    }

    /**
     * Gets query for [[Fosterorders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFosterorders()
    {
        return $this->hasMany(Fosterorder::className(), ['ServiceID' => 'ServiceID']);
    }
}
