<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $role = 'customer'; // 默认角色为客户
    public $inviteCode; // 员工注册邀请码
    
    // 员工专用字段
    public $employeeName;
    public $employeeGender;
    public $employeePosition;
    public $employeeContact;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            
            ['role', 'in', 'range' => ['customer', 'employee']],
            
            // 员工注册时需要邀请码
            ['inviteCode', 'required', 'when' => function($model) {
                return $model->role === 'employee';
            }, 'message' => '员工注册需要邀请码'],
            
            // 员工信息字段验证
            ['employeeName', 'required', 'when' => function($model) {
                return $model->role === 'employee';
            }, 'message' => '请填写姓名'],
            ['employeeName', 'string', 'max' => 45],
            
            ['employeeGender', 'required', 'when' => function($model) {
                return $model->role === 'employee';
            }, 'message' => '请选择性别'],
            ['employeeGender', 'in', 'range' => ['男', '女']],
            
            ['employeePosition', 'required', 'when' => function($model) {
                return $model->role === 'employee';
            }, 'message' => '请填写职位'],
            ['employeePosition', 'string', 'max' => 45],
            
            ['employeeContact', 'required', 'when' => function($model) {
                return $model->role === 'employee';
            }, 'message' => '请填写联系方式'],
            ['employeeContact', 'string', 'max' => 45],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        
        // 自动生成user ID
        $maxId = User::find()->max('id');
        $user->id = $maxId ? $maxId + 1 : 1;
        
        $user->username = $this->username;
        $user->email = $this->email;
        $user->role = $this->role; // 设置角色
        $user->status = User::STATUS_ACTIVE; // 直接激活（如需邮箱验证可改为 STATUS_INACTIVE）
        $user->setPassword($this->password);
        $user->generateAuthKey();
        
        // 如果需要邮箱验证，取消下面注释
        // $user->generateEmailVerificationToken();
        // return $user->save() && $this->sendEmail($user);
        
        return $user->save() ? $user : null;
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
