<?php
namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirectByRole();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirectByRole();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * 根据用户角色跳转到对应页面
     */
    private function redirectByRole()
    {
        $role = Yii::$app->user->identity->role ?? 'customer';
        
        switch ($role) {
            case 'admin':
                // 管理员跳转到 backend
                // 注意：如果 backend 和 frontend 是不同域名，需要修改为完整 URL
                // 例如：return Yii::$app->response->redirect('http://admin.yoursite.com/site/index');
                return Yii::$app->response->redirect(['/backend/web/index.php']);
                
            case 'employee':
                // 员工跳转到员工工作台（后续实现）
                Yii::$app->session->setFlash('info', '欢迎回来，员工！');
                return $this->redirect(['/site/index']);
                
            case 'customer':
            default:
                // 客户跳转到客户页面
                Yii::$app->session->setFlash('success', '欢迎回来！');
                return $this->redirect(['/site/index']);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->signup();
            if ($user) {
                // 注册成功后自动创建 Customer 记录
                $customer = new \common\models\Customer();
                
                // 生成 CustomerID（查询当前最大值 +1）
                $maxId = \common\models\Customer::find()->max('CustomerID');
                $customer->CustomerID = $maxId ? ($maxId + 1) : 200001; // 如果表为空，从 200001 开始
                
                // 关联用户 ID
                $customer->user_id = $user->id;
                $customer->Name = $model->username; // 使用用户名作为初始姓名

                
                if ($customer->save()) {
                    Yii::$app->session->setFlash('success', '注册成功！请登录。');
                } else {
                    Yii::$app->session->setFlash('warning', '注册成功，但创建客户资料失败，请联系管理员。');
                }
                return $this->redirect(['login']);
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * 员工注册（需要邀请码）
     *
     * @return mixed
     */
    public function actionEmployeeSignup()
    {
        $model = new SignupForm();
        $model->role = 'employee'; // 设置为员工角色
        $inviteCode = Yii::$app->request->get('code'); // 从 URL 获取邀请码
        
        if ($model->load(Yii::$app->request->post())) {
            // 验证邀请码
            $correctCode = Yii::$app->params['employeeInviteCode'];
            if ($model->inviteCode !== $correctCode) {
                Yii::$app->session->setFlash('error', '邀请码错误，无法注册员工账号。');
                return $this->render('employee-signup', [
                    'model' => $model,
                    'inviteCode' => $inviteCode,
                ]);
            }
            
            $user = $model->signup();
            if ($user) {
                // 注册成功后自动创建 Employee 记录
                $employee = new \common\models\Employee();
                
                // 生成 EmployeeID（查询当前最大值 +1）
                $maxId = \common\models\Employee::find()->max('EmployeeID');
                $employee->EmployeeID = $maxId ? ($maxId + 1) : 100001; // 如果表为空，从 100001 开始
                
                // 关联用户 ID
                $employee->user_id = $user->id;
                
                // 填写完整员工信息
                $employee->Name = $model->employeeName;
                $employee->Gender = $model->employeeGender;
                $employee->Position = $model->employeePosition;
                $employee->Contact = $model->employeeContact;
                $employee->HireDate = date('Y-m-d'); // 当前日期
                
                if ($employee->save()) {
                    Yii::$app->session->setFlash('success', '员工账号注册成功！请登录。');
                } else {
                    Yii::$app->session->setFlash('warning', '注册成功，但创建员工资料失败，请联系管理员。');
                }
                return $this->redirect(['login']);
            }
        } else {
            // 预填邀请码（如果 URL 中有）
            if ($inviteCode) {
                $model->inviteCode = $inviteCode;
            }
        }

        return $this->render('employee-signup', [
            'model' => $model,
            'inviteCode' => $inviteCode,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
