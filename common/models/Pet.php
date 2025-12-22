<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pet".
 *
 * @property int $PetID
 * @property int $CustomerID
 * @property string $PetName
 * @property string $Gender
 * @property int $AgeYears
 * @property int $AgeMonths
 * @property string $HealthStatus
 *
 * @property Cat $cat
 * @property Dog $dog
 * @property Fosterorder[] $fosterorders
 * @property Customer $customer
 */
class Pet extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['PetID', 'CustomerID', 'PetName', 'Gender', 'HealthStatus'], 'required'],
            [['PetID', 'CustomerID', 'AgeYears', 'AgeMonths'], 'integer'],
            [['Gender'], 'string'],
            [['PetName'], 'string', 'max' => 45],
            [['HealthStatus'], 'string', 'max' => 128],
            [['PetID'], 'unique'],
            [['CustomerID'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['CustomerID' => 'CustomerID']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'PetID' => '宠物编号',
            'CustomerID' => '客户编号',
            'PetName' => '宠物名称',
            'Gender' => '性别',
            'AgeYears' => '年龄（岁）',
            'AgeMonths' => '年龄（月）',
            'HealthStatus' => '健康状况',
        ];
    }

    /**
     * Gets query for [[Cat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(Cat::className(), ['PetID' => 'PetID']);
    }

    /**
     * Gets query for [[Dog]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDog()
    {
        return $this->hasOne(Dog::className(), ['PetID' => 'PetID']);
    }

    /**
     * Gets query for [[Fosterorders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFosterorders()
    {
        return $this->hasMany(Fosterorder::className(), ['PetID' => 'PetID']);
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['CustomerID' => 'CustomerID']);
    }
}
