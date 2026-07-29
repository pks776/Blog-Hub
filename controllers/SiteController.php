<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\ContactForm;
use app\models\LoginForm;
use app\models\User;
use app\models\Post;
use yii\base\Security;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\mail\MailerInterface;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        private readonly Security $security,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
public function actionBlogs()
{
    $posts = Post::find()
        ->where(['status' => 'published'])
        ->orderBy(['created_at' => SORT_DESC])
        ->all();

    return $this->render('blogs', [
        'posts' => $posts,
    ]);
}
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'transparent' => true,
            ],
        ];
    }

    // ==========================
    // OUR CUSTOM ACTION
    // ==========================
    public function actionUsers(): string
    {
        $users = User::find()->all();

        return $this->render('users', [
            'users' => $users,
        ]);
    }

 public function actionIndex()
{
    if (Yii::$app->user->isGuest) {
        return $this->redirect(['site/blogs']);
    }

    $totalUsers = \app\models\User::find()->count();

    $totalPosts = \app\models\Post::find()->count();

    $publishedPosts = \app\models\Post::find()
        ->where(['status' => \app\models\Post::STATUS_PUBLISHED])
        ->count();

    $draftPosts = \app\models\Post::find()
        ->where(['status' => \app\models\Post::STATUS_DRAFT])
        ->count();

    return $this->render('index', [
        'totalUsers' => $totalUsers,
        'totalPosts' => $totalPosts,
        'publishedPosts' => $publishedPosts,
        'draftPosts' => $draftPosts,
    ]);
}
public function actionAdmin()
{
    if (Yii::$app->user->isGuest) {
        return $this->redirect(['site/login']);
    }

    if (!Yii::$app->user->can('manageUsers')) {
        throw new \yii\web\ForbiddenHttpException('Access Denied');
    }

    return $this->render('admin');
}
    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm($this->security);

       if ($model->load($this->request->post()) && $model->login()) {
    return $this->redirect(['site/dashboard']);
}

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }
public function actionDashboard()
{
    if (Yii::$app->user->isGuest) {
        return $this->redirect(['login']);
    }

$posts = Post::find()
    ->orderBy(['created_at' => SORT_DESC])
    ->all();
    
    if (Yii::$app->user->identity->role == 'moderator') {
        $posts = \app\models\Post::find()
            ->where(['status' => \app\models\Post::STATUS_PENDING])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }

    return $this->render('dashboard', [
        'posts' => $posts,
    ]);
}

public function actionLogout(): Response
{
    Yii::$app->user->logout();

    return $this->goHome();
}

    public function actionContact(): Response|string
    {
        $model = new ContactForm();

        $contact = $model->load($this->request->post()) && $model->contact(
            $this->mailer,
            Yii::$app->params['adminEmail'],
            Yii::$app->params['senderEmail'],
            Yii::$app->params['senderName'],
        );

        if ($contact) {
            Yii::$app->session->setFlash(
                'success',
                'Thank you for contacting us. We will respond to you as soon as possible.'
            );

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout(): string
    {
        return $this->render('about');
    }

    public function actionSignup()
{
    $model = new \app\models\SignupForm();

    if ($model->load(Yii::$app->request->post()) && $model->signup()) {

        Yii::$app->session->setFlash(
            'success',
            'Registration successful! Please login.'
        );

        return $this->redirect(['login']);
    }

    return $this->render('signup', [
        'model' => $model,
    ]);
}
}