<?php
namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\Pet;
use common\models\Cat;
use common\models\Dog;
use common\models\Fosterorder;
use common\models\Fosterservice;
use common\models\Customer;
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
                'only' => ['logout', 'signup', 'employee-signup', 'profile', 'pets', 'orders', 'services', 'pet-create', 'pet-update', 'pet-delete'],
                'rules' => [
                    [
                        'actions' => ['signup', 'employee-signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'profile', 'pets', 'orders', 'services', 'pet-create', 'pet-update', 'pet-delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'pet-delete' => ['post'],
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
        $dashboardData = [];

        if (!Yii::$app->user->isGuest && (Yii::$app->user->identity->role ?? null) === 'customer') {
            $customer = Yii::$app->user->identity->customer;

            // 最近的宠物（含猫/狗特有信息）
            $pets = Pet::find()
                ->alias('p')
                ->where(['p.CustomerID' => $customer->CustomerID])
                ->with(['cat', 'dog'])
                ->orderBy(['p.PetID' => SORT_DESC])
                ->limit(2)
                ->all();

            // 最近订单
            $orders = Fosterorder::find()
                ->where(['CustomerID' => $customer->CustomerID])
                ->with(['pet', 'service', 'employees'])
                ->orderBy(['StartTime' => SORT_DESC])
                ->limit(2)
                ->all();

            // 可选服务展示
            $services = Fosterservice::find()
                ->orderBy(['Price' => SORT_ASC])
                ->limit(4)
                ->all();

            $dashboardData = [
                'customer' => $customer,
                'pets' => $pets,
                'orders' => $orders,
                'services' => $services,
            ];
        }

        return $this->render('index', $dashboardData);
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
            // 登录成功后检查角色
            $role = Yii::$app->user->identity->role ?? null;
            
            // 前台只允许 customer 登录
            if (!in_array($role, ['customer'])) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('error', '该账号无法在客户端登录，请访问管理后台。');
                return $this->render('login', ['model' => $model]);
            }
            
            // customer 登录成功，留在 frontend
            return $this->goBack();
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
        
        // admin 和 employee 跳转到 backend
        if (in_array($role, ['admin', 'employee'])) {
            // 注意：如果 backend 和 frontend 是不同域名，需要修改为完整 URL
            // 例如：return Yii::$app->response->redirect('http://admin.yoursite.com/site/index');
            Yii::$app->session->setFlash('info', '正在跳转到管理后台...');
            return Yii::$app->response->redirect(['/backend/web/index.php']);
        }
        
        // customer 留在 frontend
        if ($role === 'customer') {
            Yii::$app->session->setFlash('success', '欢迎回来！');
            return $this->redirect(['/site/index']);
        }
        
        // 默认跳转
        return $this->goHome();
    }

    /**
     * 个人信息页（仅客户）
     */
    public function actionProfile()
    {
        $customer = $this->requireCustomer();

        // 允许客户修改除 CustomerID、MemberLevel 之外的字段
        if ($customer->load(Yii::$app->request->post())) {
            $allowed = ['Name', 'Gender', 'Contact', 'Address'];
            if ($customer->save(true, $allowed)) {
                Yii::$app->session->setFlash('success', '资料已更新。');
                return $this->refresh();
            }
            Yii::$app->session->setFlash('error', '保存失败，请检查输入。');
        }

        return $this->render('profile', [
            'customer' => $customer,
        ]);
    }

    /**
     * 宠物列表（仅客户）
     */
    public function actionPets()
    {
        $customer = $this->requireCustomer();
        $request = Yii::$app->request;
        $type = $request->get('type'); // cat / dog
        $gender = $request->get('gender'); // 公 / 母
        $keyword = trim($request->get('q', ''));
        $health = trim($request->get('health', ''));

        $query = Pet::find()
            ->alias('p')
            ->where(['p.CustomerID' => $customer->CustomerID])
            ->with(['cat', 'dog']);

        if ($type === 'cat') {
            $query->innerJoinWith('cat');
        } elseif ($type === 'dog') {
            $query->innerJoinWith('dog');
        }

        if ($gender) {
            $query->andWhere(['p.Gender' => $gender]);
        }

        if ($keyword !== '') {
            $query->andWhere(['like', 'p.PetName', $keyword]);
        }

        if ($health !== '') {
            $query->andWhere(['like', 'p.HealthStatus', $health]);
        }

        $pets = $query
            ->orderBy(['p.PetID' => SORT_ASC])
            ->all();

        return $this->render('pets', [
            'customer' => $customer,
            'pets' => $pets,
            'filters' => [
                'type' => $type,
                'gender' => $gender,
                'q' => $keyword,
                'health' => $health,
            ],
        ]);
    }

    /**
     * 创建宠物（仅客户自己的名下）
     */
    public function actionPetCreate($type = 'cat')
    {
        $customer = $this->requireCustomer();
        $pet = new Pet();
        $pet->CustomerID = $customer->CustomerID;
        $cat = $type === 'cat' ? new Cat() : null;
        $dog = $type === 'dog' ? new Dog() : null;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $pet->load($post);

            // 生成 PetID
            $maxPetId = Pet::find()->max('PetID');
            $pet->PetID = $maxPetId ? ($maxPetId + 1) : 300001;

            // 仅保存非主键字段（PetID/CustomerID 已设定）
            $pet->CustomerID = $customer->CustomerID;
            $safe = ['PetName', 'Gender', 'AgeYears', 'AgeMonths', 'HealthStatus', 'CustomerID', 'PetID'];

            if ($type === 'cat' && $cat && $cat->load($post) && $pet->save(true, $safe)) {
                $cat->PetID = $pet->PetID;
                if ($cat->save()) {
                    Yii::$app->session->setFlash('success', '宠物（猫）已创建。');
                    return $this->redirect(['pets']);
                }
            } elseif ($type === 'dog' && $dog && $dog->load($post) && $pet->save(true, $safe)) {
                $dog->PetID = $pet->PetID;
                if ($dog->save()) {
                    Yii::$app->session->setFlash('success', '宠物（狗）已创建。');
                    return $this->redirect(['pets']);
                }
            }

            Yii::$app->session->setFlash('error', '保存失败，请检查输入。');
        }

        return $this->render('pet-form', [
            'pet' => $pet,
            'cat' => $cat,
            'dog' => $dog,
            'type' => $type,
        ]);
    }

    /**
     * 更新宠物（仅客户自己的宠物，非主键）
     */
    public function actionPetUpdate($id)
    {
        $customer = $this->requireCustomer();
        $pet = $this->findOwnPet($id, $customer->CustomerID);
        $type = $pet->cat ? 'cat' : ($pet->dog ? 'dog' : 'cat');
        $cat = $pet->cat ?: ($type === 'cat' ? new Cat() : null);
        $dog = $pet->dog ?: ($type === 'dog' ? new Dog() : null);

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $pet->load($post);
            $safe = ['PetName', 'Gender', 'AgeYears', 'AgeMonths', 'HealthStatus'];
            $petSaved = $pet->save(true, $safe);

            $relSaved = true;
            if ($type === 'cat' && $cat) {
                $cat->load($post);
                $cat->PetID = $pet->PetID;
                $relSaved = $cat->save();
            } elseif ($type === 'dog' && $dog) {
                $dog->load($post);
                $dog->PetID = $pet->PetID;
                $relSaved = $dog->save();
            }

            if ($petSaved && $relSaved) {
                Yii::$app->session->setFlash('success', '宠物信息已更新。');
                return $this->redirect(['pets']);
            }

            Yii::$app->session->setFlash('error', '保存失败，请检查输入。');
        }

        return $this->render('pet-form', [
            'pet' => $pet,
            'cat' => $cat,
            'dog' => $dog,
            'type' => $type,
        ]);
    }

    /**
     * 删除宠物（仅客户自己的宠物）
     */
    public function actionPetDelete($id)
    {
        $customer = $this->requireCustomer();
        $pet = $this->findOwnPet($id, $customer->CustomerID);

        // 若存在关联订单，直接拦截
        $hasOrders = Fosterorder::find()->where(['PetID' => $pet->PetID])->exists();
        if ($hasOrders) {
            Yii::$app->session->setFlash('error', '该宠物存在寄养订单，无法删除。');
            return $this->redirect(['pets']);
        }

        try {
            $pet->delete();
            Yii::$app->session->setFlash('success', '宠物已删除。');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', '删除失败：请先删除关联数据或联系管理员。');
        }

        return $this->redirect(['pets']);
    }

    /**
     * 订单列表（仅客户）
     */
    public function actionOrders()
    {
        $customer = $this->requireCustomer();
        $orders = Fosterorder::find()
            ->where(['CustomerID' => $customer->CustomerID])
            ->with(['pet', 'service', 'employees'])
            ->orderBy(['StartTime' => SORT_DESC])
            ->all();

        return $this->render('orders', [
            'customer' => $customer,
            'orders' => $orders,
        ]);
    }

    /**
     * 寄养服务列表（仅客户查看）
     */
    public function actionServices()
    {
        $this->requireCustomer();
        $services = Fosterservice::find()
            ->orderBy(['ServiceType' => SORT_ASC, 'PetCategory' => SORT_ASC])
            ->all();

        return $this->render('services', [
            'services' => $services,
        ]);
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

    /**
     * 仅允许客户访问的检查
     */
    private function requireCustomer(): Customer
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('请先登录。');
        }

        $user = Yii::$app->user->identity;
        if (($user->role ?? null) !== 'customer') {
            throw new ForbiddenHttpException('仅客户账号可访问此页面。');
        }

        $customer = $user->customer;
        if (!$customer) {
            throw new ForbiddenHttpException('未找到您的客户资料，请联系管理员。');
        }

        return $customer;
    }

    /**
     * 获取当前客户自己的宠物
     */
    private function findOwnPet($id, $customerId): Pet
    {
        $pet = Pet::find()
            ->where(['PetID' => $id, 'CustomerID' => $customerId])
            ->with(['cat', 'dog'])
            ->one();

        if (!$pet) {
            throw new ForbiddenHttpException('无权访问该宠物信息。');
        }

        return $pet;
    }
}
