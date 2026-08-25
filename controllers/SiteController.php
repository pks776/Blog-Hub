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

    /**
     * Controller behaviors.
     */
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

    /**
     * Display published blogs.
     */
    public function actionBlogs()
    {
        $posts = Post::find()
            ->where([
                'status' => Post::STATUS_PUBLISHED,
            ])
            ->orderBy([
                'created_at' => SORT_DESC,
            ])
            ->all();

        return $this->render('blogs', [
            'posts' => $posts,
        ]);
    }

    /**
     * Application actions.
     */
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

    /**
     * Display all users.
     */
    public function actionUsers(): string
    {
        $users = User::find()->all();

        return $this->render('users', [
            'users' => $users,
        ]);
    }

    /**
     * Home page.
     *
     * Guest:
     *      Redirects to published blogs.
     *
     * Blogger:
     *      Displays personal blogger dashboard.
     *
     * Admin / Moderator:
     *      Displays the existing dashboard.
     */
public function actionIndex()
{
    // Guest users
    if (Yii::$app->user->isGuest) {
        return $this->redirect(['site/blogs']);
    }

    $role = Yii::$app->user->identity->role;

    // ==========================================
    // BLOGGER DASHBOARD
    // ==========================================
    if ($role === 'blogger') {

        $userId = Yii::$app->user->id;

        $myPosts = Post::find()
            ->where([
                'author_id' => $userId,
            ])
            ->count();

        $publishedPosts = Post::find()
            ->where([
                'author_id' => $userId,
                'status' => Post::STATUS_PUBLISHED,
            ])
            ->count();

        $pendingPosts = Post::find()
            ->where([
                'author_id' => $userId,
                'status' => Post::STATUS_PENDING,
            ])
            ->count();

        $rejectedPosts = Post::find()
            ->where([
                'author_id' => $userId,
                'status' => Post::STATUS_REJECTED,
            ])
            ->count();

        return $this->render('blogger-dashboard', [
            'myPosts' => $myPosts,
            'publishedPosts' => $publishedPosts,
            'pendingPosts' => $pendingPosts,
            'rejectedPosts' => $rejectedPosts,
        ]);
    }

    // ==========================================
    // ADMIN / MODERATOR DASHBOARD
    // ==========================================

    $totalUsers = User::find()->count();

    $totalPosts = Post::find()->count();

    $publishedPosts = Post::find()
        ->where([
            'status' => Post::STATUS_PUBLISHED,
        ])
        ->count();

    $pendingPosts = Post::find()
        ->where([
            'status' => Post::STATUS_PENDING,
        ])
        ->count();

    $rejectedPosts = Post::find()
        ->where([
            'status' => Post::STATUS_REJECTED,
        ])
        ->count();

    return $this->render('index', [
        'totalUsers' => $totalUsers,
        'totalPosts' => $totalPosts,
        'publishedPosts' => $publishedPosts,
        'pendingPosts' => $pendingPosts,
        'rejectedPosts' => $rejectedPosts,
    ]);
}

    /**
     * Admin page.
     */
    public function actionAdmin()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        if (!Yii::$app->user->can('manageUsers')) {
            throw new \yii\web\ForbiddenHttpException(
                'Access Denied'
            );
        }

        return $this->render('admin');
    }

    /**
     * Login.
     */
    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm($this->security);

        if (
            $model->load($this->request->post()) &&
            $model->login()
        ) {
            return $this->redirect(['site/dashboard']);
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Management dashboard.
     */
    public function actionDashboard()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }

        /*
         * Default:
         * Show all posts.
         */
        $posts = Post::find()
            ->orderBy([
                'created_at' => SORT_DESC,
            ])
            ->all();

        /*
         * Moderator:
         * Show only pending posts.
         */
        if (
            Yii::$app->user->identity->role === 'moderator'
        ) {
            $posts = Post::find()
                ->where([
                    'status' => Post::STATUS_PENDING,
                ])
                ->orderBy([
                    'created_at' => SORT_DESC,
                ])
                ->all();
        }

        return $this->render('dashboard', [
            'posts' => $posts,
        ]);
    }

    /**
     * Logout.
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Contact page.
     */
    public function actionContact(): Response|string
    {
        $model = new ContactForm();

        $contact = $model->load($this->request->post())
            && $model->contact(
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

    /**
     * About page.
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }

    /**
     * Signup.
     */
    public function actionSignup()
    {
        $model = new \app\models\SignupForm();

        if (
            $model->load(Yii::$app->request->post()) &&
            $model->signup()
        ) {

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