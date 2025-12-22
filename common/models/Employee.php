<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "employee".
 *
 * @property int $EmployeeID
 * @property int|null $user_id
 * @property string $Name
 * @property string $Gender
 * @property string $Position
 * @property string $Contact
 * @property string $HireDate
 *
 * @property User $user
 * @property OrderEmployee[] $orderEmployees
 * @property Fosterorder[] $orders
 */
class Employee extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['EmployeeID', 'Name', 'Gender', 'Position', 'Contact'], 'required'],
            [['EmployeeID', 'user_id'], 'integer'],
            [['Gender'], 'string'],
            [['HireDate'], 'safe'],
            [['Name', 'Position', 'Contact'], 'string', 'max' => 45],
            [['user_id'], 'unique'],
            [['EmployeeID'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'EmployeeID' => '员工编号',
            'user_id' => '用户ID',
            'Name' => '姓名',
            'Gender' => '性别',
            'Position' => '职位',
            'Contact' => '联系方式',
            'HireDate' => '入职日期',
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
     * Gets query for [[OrderEmployees]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderEmployees()
    {
        return $this->hasMany(OrderEmployee::className(), ['EmployeeID' => 'EmployeeID']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Fosterorder::className(), ['OrderID' => 'OrderID'])->viaTable('order_employee', ['EmployeeID' => 'EmployeeID']);
    }
}
