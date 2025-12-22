<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property int $CustomerID
 * @property int|null $user_id
 * @property string $Name
 * @property string $Gender
 * @property string $Contact
 * @property string $Address
 * @property string|null $MemberLevel
 *
 * @property User $user
 * @property Fosterorder[] $fosterorders
 * @property Pet[] $pets
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['CustomerID'], 'required'],
            [['CustomerID', 'user_id'], 'integer'],
            [['Gender', 'MemberLevel'], 'string'],
            [['Name', 'Contact', 'Address'], 'string', 'max' => 45],
            [['user_id'], 'unique'],
            [['CustomerID'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'CustomerID' => '客户编号',
            'user_id' => '用户ID',
            'Name' => '姓名',
            'Gender' => '性别',
            'Contact' => '联系方式',
            'Address' => '地址',
            'MemberLevel' => '会员等级',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Fosterorders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFosterorders()
    {
        return $this->hasMany(Fosterorder::className(), ['CustomerID' => 'CustomerID']);
    }

    /**
     * Gets query for [[Pets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPets()
    {
        return $this->hasMany(Pet::className(), ['CustomerID' => 'CustomerID']);
    }
}
