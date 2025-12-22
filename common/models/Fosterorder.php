<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "fosterorder".
 *
 * @property int $OrderID
 * @property int $CustomerID
 * @property int $PetID
 * @property int $ServiceID
 * @property string $StartTime
 * @property string $EndTime
 * @property string $OrderStatus
 * @property float $PaymentAmount
 *
 * @property Customer $customer
 * @property Pet $pet
 * @property Fosterservice $service
 * @property OrderEmployee[] $orderEmployees
 * @property Employee[] $employees
 */
class Fosterorder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fosterorder';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['OrderID', 'CustomerID', 'PetID', 'ServiceID', 'StartTime', 'EndTime', 'OrderStatus', 'PaymentAmount'], 'required'],
            [['OrderID', 'CustomerID', 'PetID', 'ServiceID'], 'integer'],
            [['StartTime', 'EndTime'], 'safe'],
            [['OrderStatus'], 'string'],
            [['PaymentAmount'], 'number'],
            [['OrderID'], 'unique'],
            [['CustomerID'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['CustomerID' => 'CustomerID']],
            [['PetID'], 'exist', 'skipOnError' => true, 'targetClass' => Pet::className(), 'targetAttribute' => ['PetID' => 'PetID']],
            [['ServiceID'], 'exist', 'skipOnError' => true, 'targetClass' => Fosterservice::className(), 'targetAttribute' => ['ServiceID' => 'ServiceID']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'OrderID' => '订单编号',
            'CustomerID' => '客户编号',
            'PetID' => '宠物编号',
            'ServiceID' => '服务编号',
            'StartTime' => '开始时间',
            'EndTime' => '结束时间',
            'OrderStatus' => '订单状态',
            'PaymentAmount' => '支付金额',
        ];
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

    /**
     * Gets query for [[Pet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPet()
    {
        return $this->hasOne(Pet::className(), ['PetID' => 'PetID']);
    }

    /**
     * Gets query for [[Service]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Fosterservice::className(), ['ServiceID' => 'ServiceID']);
    }

    /**
     * Gets query for [[OrderEmployees]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderEmployees()
    {
        return $this->hasMany(OrderEmployee::className(), ['OrderID' => 'OrderID']);
    }

    /**
     * Gets query for [[Employees]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmployees()
    {
        return $this->hasMany(Employee::className(), ['EmployeeID' => 'EmployeeID'])->viaTable('order_employee', ['OrderID' => 'OrderID']);
    }
}
