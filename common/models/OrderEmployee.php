<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_employee".
 *
 * @property int $OrderID
 * @property int $EmployeeID
 *
 * @property Employee $employee
 * @property Fosterorder $order
 */
class OrderEmployee extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_employee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['OrderID', 'EmployeeID'], 'required'],
            [['OrderID', 'EmployeeID'], 'integer'],
            [['OrderID', 'EmployeeID'], 'unique', 'targetAttribute' => ['OrderID', 'EmployeeID']],
            [['EmployeeID'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['EmployeeID' => 'EmployeeID']],
            [['OrderID'], 'exist', 'skipOnError' => true, 'targetClass' => Fosterorder::className(), 'targetAttribute' => ['OrderID' => 'OrderID']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'OrderID' => '订单编号',
            'EmployeeID' => '员工编号',
        ];
    }

    /**
     * Gets query for [[Employee]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::className(), ['EmployeeID' => 'EmployeeID']);
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Fosterorder::className(), ['OrderID' => 'OrderID']);
    }
}
