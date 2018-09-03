<?php

namespace backend\controllers;

use Yii;
use yii\web\Session;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\helpers\ArrayHelper;
use common\models\ItemTodo;
use common\models\PageItem;
use common\models\User;
use common\models\Page;
use common\models\Role;
use common\models\Project;
use common\models\Report;
use common\models\ProjectPage;
use common\models\Template;
use common\models\DismissTime;
use common\models\TemplatePage;
use common\models\TemplateRole;
use common\models\ItemPage;
use common\models\Setting;
use common\models\Contact;
use common\models\Recentchanges;
use backend\models\CreateProject;
use backend\models\CreateTemplate;
use backend\models\UpdateProject;
use backend\models\UpdateTemplate;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use yii\db\Query;
use yii\db\ActiveQuery;

/**
 * Site controller
 */
class SiteController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'signup', 'request-password-reset', 'reset-password', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'tenders'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                // 'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        return $this->render('index', [
        ]);
    }

    public function actionEditprofile() {
        $userid = @$_POST['uid'];
        $contactid = @$_POST['cid'];
        $allowed_image_extension = array(
            "png",
            "jpg",
            "jpeg"
        );
        if ($userid) {
            if (isset($_POST['password']) && $_POST['password'] != '') {
                $hashpass = \Yii::$app->security->generatePasswordHash($_POST['password']);
                $data = ['username' => $_POST['username'], 'email' => $_POST['email'], 'password_hash' => $hashpass, 'password' => $_POST['password']];
            } else {
                $data = ['username' => $_POST['username'], 'email' => $_POST['email']];
            }

            if (isset($_FILES['LogoImage']['name']) && $_FILES['LogoImage']['name'] != '') {
                $file_extension = pathinfo($_FILES["LogoImage"]["name"], PATHINFO_EXTENSION);
                $logo_target = 'uploads/profile_logo_' . time() . $userid . '.' . pathinfo($_FILES['LogoImage']['name'], PATHINFO_EXTENSION);
                if (in_array($file_extension, $allowed_image_extension)) {
                    if (move_uploaded_file($_FILES["LogoImage"]["tmp_name"], $logo_target)) {
                        $data['Logo'] = $logo_target;
                    }
                } else {
                    Yii::$app->session->setFlash('error', "Upload valid images. Only PNG,JPG and JPEG are allowed.");
                    return $this->redirect(array('site/editprofile'));
                }
            }
            $querydata = \Yii::$app
                    ->db
                    ->createCommand()
                    ->update('tbluser', $data, 'UserId = ' . $userid . '')
                    ->execute();
            if ($querydata) {
                Yii::$app->session->setFlash('success', "Profile has been updated");
            } else {
                Yii::$app->session->setFlash('error', "Profile has not been updated");
            }
            return $this->redirect(array('site/editprofile'));
        } elseif ($contactid) {
            if (isset($_POST['password']) && $_POST['password'] != '') {
                $hashpass = \Yii::$app->security->generatePasswordHash($_POST['password']);
                $data = $_POST['CreateContact'];
                $data['password_hash'] = $hashpass;
                $data['password'] = $_POST['password'];
            } else {
                $data = $_POST['CreateContact'];
            }

            if (isset($_FILES['LogoImage']['name']) && $_FILES['LogoImage']['name'] != '') {
                $file_extension = pathinfo($_FILES["LogoImage"]["name"], PATHINFO_EXTENSION);
                $logo_target = 'uploads/profile_logo_' . time() . $contactid . '.' . pathinfo($_FILES['LogoImage']['name'], PATHINFO_EXTENSION);

                if (in_array($file_extension, $allowed_image_extension)) {
                    if (move_uploaded_file($_FILES["LogoImage"]["tmp_name"], $logo_target)) {
                        $data['Logo'] = $logo_target;
                    }
                } else {
                    Yii::$app->session->setFlash('error', "Upload valid images. Only PNG,JPG and JPEG are allowed.");
                    return $this->redirect(array('site/editprofile'));
                }
            }

            $querydata = \Yii::$app
                    ->db
                    ->createCommand()
                    ->update('tbluser', $data, 'UserId = ' . $contactid . '')
                    ->execute();
            if ($querydata) {
                Yii::$app->session->setFlash('success', "Profile has been updated");
            } else {
                Yii::$app->session->setFlash('error', "Profile has not been updated");
            }
            return $this->redirect(array('site/editprofile'));
        } else {
            return $this->render('editprofile', [
            ]);
        }
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        $model->is_admin = 1;
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }

        $model->password = '';

        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout() {

        $session = Yii::$app->session;
        $user = Yii::$app->user->identity;


        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->admin()) {
                if ($user = $model->signup()) {
                    if (Yii::$app->getUser()->login($user)) {
                        return $this->goHome();
                    }
                }
            }
        }

        return $this->render('signup', [
                    'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset() {
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
    public function actionResetPassword($token) {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
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
     * create-new-contact action.
     *
     * @return mixed
     */
    public function actionCreateUser($id = null) {
        $user = Yii::$app->user->identity;
        $model = new User;


        if (isset($_POST['CreateUser'])) {
            $password = rand(1, 1000000);
            $hashpass = \Yii::$app->security->generatePasswordHash($password);
            $model->ParentId = '0';
            $model->username = str_replace(' ', '', $_POST['CreateUser']['FirstName']) . rand(1, 100);
            $model->FirstName = $_POST['CreateUser']['FirstName'];
            $model->LastName = $_POST['CreateUser']['LastName'];
            $model->Street = $_POST['CreateUser']['Street'];
            $model->City = $_POST['CreateUser']['City'];
            $model->State = $_POST['CreateUser']['State'];
            $model->Zip = $_POST['CreateUser']['Zip'];
            $model->password_hash = $hashpass;
            $model->password = $password;
            $model->email = $_POST['CreateUser']['Email'];
            $model->Mobile = $_POST['CreateUser']['Phone'];
            $model->ContactNumber = $_POST['CreateUser']['Office'];
            $model->Fax = $_POST['CreateUser']['Fax'];
            $model->auth_key = Yii::$app->security->generateRandomString();
            $model->status = '10';
            $model->is_admin = '1';
            $model->is_superadmin = '0';
            $model->view_as_admin = '0';
            $model->created_at = time();
            $model->updated_at = time();
            $model->CreatedOn = date('Y-m-d h:i:s');
            $model->IsActive = 1;

            $useremail = User::find()
                            ->where(['email' => $_POST['CreateUser']['Email']])->all();
            if ($useremail) {
                Yii::$app->session->setFlash('error', "Email is already registered!");
            } else {
                $project = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('tbluser', $model)
                        ->execute();
            }

            return $this->redirect(array('manage-users'));

            die();
        } else {

            if ($id) {

                $contact = User::find()
                                ->where(['UserId' => $id])->all();
            } else {
                $contact = [];
            }

            return $this->render('createNewUser', [
                        'model' => $model,
                        'contact' => $contact,
            ]);
        }
    }

    /**
     * Edit User action.
     *
     * @return mixed
     */
    public function actionEditUser($id = null) {
        $user = Yii::$app->user->identity;
        $model = new User;

        $id = Yii::$app->request->get('id');

        if (isset($_POST['CreateContact'])) {

            $model->FirstName = $_POST['CreateContact']['FirstName'];
            $model->LastName = $_POST['CreateContact']['LastName'];
            $model->Street = $_POST['CreateContact']['Street'];
            $model->City = $_POST['CreateContact']['City'];
            $model->State = $_POST['CreateContact']['State'];
            $model->Zip = $_POST['CreateContact']['Zip'];
            //$model->email = $_POST['CreateContact']['Email'];
            $model->Mobile = $_POST['CreateContact']['Phone'];
            $model->ContactNumber = $_POST['CreateContact']['Office'];
            $model->Fax = $_POST['CreateContact']['Fax'];

            /* if ($_POST['CreateContact']['id']) {
              $useremail = User::find()
              ->where(['email' => $_POST['CreateContact']['Email']])->andWhere(['!=', 'UserId', $_POST['CreateContact']['id']])->all();
              } else {
              $useremail = User::find()
              ->where(['email' => $_POST['CreateContact']['Email']])->all();
              }
              if ($useremail) {
              Yii::$app->session->setFlash('error', "Email is already registered!");
              } else {
              if ($_POST['CreateContact']['id']) {
              $model->UserId = $_POST['CreateContact']['id'];
              $project = User::updateAll($model, 'UserId = ' . $_POST['CreateContact']['id'] . '');

              if ($project) {
              Yii::$app->session->setFlash('success', "User has been updated!");
              }
              }
              } */

            if ($_POST['CreateContact']['id']) {
                $data = ['FirstName' => $_POST['CreateContact']['FirstName'], 'LastName' => $_POST['CreateContact']['LastName'], 'Street' => $_POST['CreateContact']['Street'], 'City' => $_POST['CreateContact']['City'], 'State' => $_POST['CreateContact']['State'], 'Zip' => $_POST['CreateContact']['Zip'], 'Mobile' => $_POST['CreateContact']['Phone'], 'ContactNumber' => $_POST['CreateContact']['Office'], 'Fax' => $_POST['CreateContact']['Fax']];
                $querydata = \Yii::$app
                        ->db
                        ->createCommand()
                        ->update('tbluser', $data, 'UserId = ' . $_POST['CreateContact']['id'] . '')
                        ->execute();
                if ($querydata) {
                    Yii::$app->session->setFlash('success', "User has been updated!");
                }
            }

            return $this->redirect(array('manage-users'));

            die();
        } else {

            if ($id) {

                $contact = User::find()
                                ->where(['UserId' => $id])->all();
            } else {
                $contact = [];
            }

            return $this->render('edituser', [
                        'model' => $model,
                        'contact' => $contact,
            ]);
        }
    }

    /**
     * set session action.
     *
     * @return mixed
     */
    public function actionSetSession() {
        $session = Yii::$app->session;

        if ($_POST['value'] == 'all') {
            $session->remove($_POST['key']);
            echo json_encode('success');
        } else if ($session[$_POST['key']] = $_POST['value']) {
            echo json_encode('success');
        }

        die();
    }

    public function actionManageUsers() {
        $users = User::find()->where(['ParentId' => 0, 'is_superadmin' => 0])->all();
        return $this->render('manageusers', [
                    'contacts' => $users
        ]);
    }

    public function actionPrivacy() {
        return $this->render('privacy', [
        ]);
    }

    public function actionTerms() {
        return $this->render('terms', [
        ]);
    }

    public function actionTenders() {
        return $this->render('tenders', [
        ]);
    }

}
