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
                        'actions' => ['logout', 'index', 'create-new-project', 'create-contact', 'create-contact-modal', 'delete-contact', 'current-projects', 'recent-changes', 'manage-contacts', 'to-do', 'settings', 'set-session', 'update-project', 'project-details', 'contact-details', 'duplicate-project', 'create-project-by-template', 'manage-templates', 'create-template', 'update-template', 'drawing-tool', 'drawingimagesave', 'change-isdismiss','editprofile','deleterole'],
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
        $query = new \yii\db\Query;
        $session = Yii::$app->session;
        $user = Yii::$app->user->identity;
        $projects = Project::find()->where(['UserId' => $user->UserId])->andWhere(['IsActive' => '1'])->count();
        $users = User::find()->where(['ParentId' => $user->UserId])->andWhere(['IsActive' => '1'])->count();
        $reports = Report::find()->Where(['IsActive' => '1'])->count();
        if ($user->ParentId == 0) {
            $query->select('*')
                    ->from('projects')
                    ->leftJoin('tbluser', 'tbluser.UserId = projects.UserId')
                    ->where(['=', 'projects.UserId', $user->UserId])
                    ->orderBy(['projects.ProjectId' => SORT_DESC])
                    ->limit(5);
            $command = $query->createCommand();
            $topprojects = $command->queryAll();
        } else {
            $roleprojects = Role::find()->where(['=', 'UserId', $user->UserId])->all();
            foreach ($roleprojects as $_project) {
                $pids[] = $_project->ProjectId;
                $query = new \yii\db\Query;
                $query->select('*')
                        ->from('projects')
                        ->leftJoin('tbluser', 'tbluser.UserId = projects.UserId')
                        ->where(['=', 'projects.ProjectId', $_project->ProjectId])
                        ->orderBy(['projects.ProjectId' => SORT_DESC]);
                $command = $query->createCommand();
                $topprojects[] = $command->queryOne();
            }
            //$projects = Project::find()->where(['ProjectId' => $pids]);
        }
        $resp = $this->actionRecentChangesData();
        $contacts = $this->actionManageContactsData();
        $todos = $this->actionToDoData();

        return $this->render('index', [
                    'projects' => $projects,
                    'users' => $users,
                    'reports' => $reports,
                    'topprojects' => $topprojects,
                    'recentchanges' => $resp,
                    'contacts' => $contacts,
                    'todos' => $todos
        ]);
    }

    public function actionEditprofile() {
        $userid = @$_POST['uid'];
        $contactid = @$_POST['cid'];
        if ($userid) {
            if (isset($_POST['password']) && $_POST['password'] != '') {
                $hashpass = \Yii::$app->security->generatePasswordHash($_POST['password']);
                $data = ['username' => $_POST['username'], 'email' => $_POST['email'], 'password_hash' => $hashpass];
            } else {
                $data = ['username' => $_POST['username'], 'email' => $_POST['email']];
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
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays Drawing Tool.
     *
     * @return string
     */
    public function actionDrawingTool() {

        return $this->render('drawing-tool');
    }

    /**
     * Displays Save Drawing image.
     *
     * @return string
     */
    public function actionDrawingimagesave() {

        $image = imagecreatefrompng($_POST['image']);
        $id = uniqid();
        $user = Yii::$app->user->identity;

        $baseURL = Yii::$app->params['BASE_URL'];
        $appname = Yii::$app->name;

        imagealphablending($image, false);
        imagesavealpha($image, true);
        if (imagepng($image, $_SERVER['DOCUMENT_ROOT'] . '/' . $appname . '/backend/web/uploads/img-' . $id . '.png')) {
            $query = new Query;
            $query->select('*')
                    ->from('documents')
                    ->where(['UserId' => $user->UserId, 'ProjectId' => $_POST['pid']])
                    ->orderBy(['ItemOrder' => SORT_ASC]);
            $command = $query->createCommand();
            $items = $command->queryAll();

            foreach ($items as $_item) {
                $data = ['ItemOrder' => ($_item['ItemOrder'] + 1)];
                $querydata = \Yii::$app
                        ->db
                        ->createCommand()
                        ->update('documents', $data, 'ItemDocumentId = ' . $_item['ItemDocumentId'] . '')
                        ->execute();
            }

            $dataz = ['ProjectId' => $_POST['pid'], 'Title' => $_POST['title'], 'ItemOrder' => '1', 'FileName' => 'uploads/img-' . $id . '.png', 'FileExtension' => 'image', 'UserId' => $user->UserId, 'CreatedOn' => date('Y-m-d H:i:s'), 'IsActive' => 1];
            $querydata = \Yii::$app
                    ->db
                    ->createCommand()
                    ->insert('documents', $dataz)
                    ->execute();
            if ($querydata) {
                $array = ['success' => 'true', 'img' => $baseURL . 'uploads/img-' . $id . '.png'];
                echo json_encode($array);
            }
        } else {
            $array = ['success' => 'false'];
            echo json_encode($array);
        }

        die();
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
     * create-new-project action.
     *
     * @return mixed
     */
    public function actionCreateNewProject() {
        $model = new CreateProject();

        if (isset($_POST['CreateProject'])) {

            $start_date = $_POST['CreateProject']['EventStartDate'];
            $end_date = $_POST['CreateProject']['EventEndDate'];

            $model->EventName = $_POST['CreateProject']['EventName'];
            $model->TemplateId = isset($_POST['CreateProject']['TemplateId']) ? $_POST['CreateProject']['TemplateId'] : '';
            $model->ProjectId = isset($_POST['CreateProject']['ProjectId']) ? $_POST['CreateProject']['ProjectId'] : '';
            $model->IsDuplicated = isset($_POST['CreateProject']['IsDuplicated']) ? $_POST['CreateProject']['IsDuplicated'] : 0;

            $model->DuplicatedFromProject = null;

            if ($model->ProjectId && $model->IsDuplicated) {
                $model->DuplicatedFromProject = $model->ProjectId;
            }

            $model->ClientId = $_POST['CreateProject']['ClientId'];
            $model->ClientName = $_POST['CreateProject']['ClientName'];
            $model->EventStartDate = $start_date ? date('Y-m-d H:i:s', strtotime($start_date)) : '';
            $model->EventEndDate = $end_date ? date('Y-m-d H:i:s', strtotime($end_date)) : '';
            $model->EventLocation = $_POST['CreateProject']['EventLocation'];
            $model->EventLatitude = $_POST['CreateProject']['EventLatitude'];
            $model->EventLongtitude = $_POST['CreateProject']['EventLongtitude'];
            /* response in array */
            $model->PageName = isset($_POST['CreateProject']['PageName']) ? $_POST['CreateProject']['PageName'] : [];
            $model->ContactId = isset($_POST['CreateProject']['ContactId']) ? $_POST['CreateProject']['ContactId'] : [];
            $model->RoleName = isset($_POST['CreateProject']['RoleName']) ? $_POST['CreateProject']['RoleName'] : [];

            if ($project = $model->create()) {

                return 'success';

                //Yii::$app->session->setFlash('success', "Project Has Created inserted");
            } else {
                return 'error';
            }
            die();
        }

        return $this->render('createNewProject', [
                    'model' => $model,
        ]);
    }

    /**
     * update-project action.
     *
     * @return mixed
     */
    public function actionUpdateProject() {
        if (isset($_POST['CreateProject'])) {

            $model = new UpdateProject();

            $start_date = $_POST['CreateProject']['EventStartDate'];
            $end_date = $_POST['CreateProject']['EventEndDate'];

            $model->ProjectId = $_POST['CreateProject']['ProjectId'];
            $model->TemplateId = isset($_POST['CreateProject']['TemplateId']) ? $_POST['CreateProject']['TemplateId'] : '';

            $model->IsDuplicated = isset($_POST['CreateProject']['IsDuplicated']) ? $_POST['CreateProject']['IsDuplicated'] : 0;

            $model->EventName = $_POST['CreateProject']['EventName'];
            $model->ClientId = $_POST['CreateProject']['ClientId'];
            $model->ClientName = $_POST['CreateProject']['ClientName'];
            $model->EventStartDate = $start_date ? date('Y-m-d H:i:s', strtotime($start_date)) : '';
            $model->EventEndDate = $end_date ? date('Y-m-d H:i:s', strtotime($end_date)) : '';
            $model->EventLocation = $_POST['CreateProject']['EventLocation'];
            $model->EventLatitude = $_POST['CreateProject']['EventLatitude'];
            $model->EventLongtitude = $_POST['CreateProject']['EventLongtitude'];
            /* response in array */
            $model->PageName = isset($_POST['CreateProject']['PageName']) ? $_POST['CreateProject']['PageName'] : [];
            $model->PageID = isset($_POST['CreateProject']['PageID']) ? $_POST['CreateProject']['PageID'] : [];
            $model->ContactId = isset($_POST['CreateProject']['ContactId']) ? $_POST['CreateProject']['ContactId'] : [];
            $model->RoleName = isset($_POST['CreateProject']['RoleName']) ? $_POST['CreateProject']['RoleName'] : [];
            $model->RoleId = isset($_POST['CreateProject']['RoleId']) ? $_POST['CreateProject']['RoleId'] : [];

            if ($project = $model->create()) {

                return 'success';

                //Yii::$app->session->setFlash('success', "Project Has Created inserted");
            } else {
                return 'error';
            }
            die();
        }

        if (isset($_GET['project'])) {

            $model = Project::find()->where(['ProjectId' => $_GET['project']])->one();
            $templates = Template::find()->all();

            return $this->render('updateProject', [
                        'model' => $model,
                        'templates' => $templates,
            ]);
        }
    }

    /**
     * create-new-contact action.
     *
     * @return mixed
     */
    public function actionCreateContact($id = null) {
        $user = Yii::$app->user->identity;
        $model = new User;

        $id = Yii::$app->request->get('id');

        if (isset($_POST['CreateContact'])) {
            $password = rand(1, 1000000);
            $hashpass = \Yii::$app->security->generatePasswordHash($password);
            $model->ParentId = $user->UserId;
            $model->username = $_POST['CreateContact']['FirstName'].rand(1, 100);
            $model->FirstName = $_POST['CreateContact']['FirstName'];
            $model->LastName = $_POST['CreateContact']['LastName'];
            $model->Street = $_POST['CreateContact']['Street'];
            $model->City = $_POST['CreateContact']['City'];
            $model->State = $_POST['CreateContact']['State'];
            $model->Zip = $_POST['CreateContact']['Zip'];
            $model->password_hash = $hashpass;
            $model->password = $password;
            $model->email = $_POST['CreateContact']['Email'];
            $model->Mobile = $_POST['CreateContact']['Phone'];
            $model->ContactNumber = $_POST['CreateContact']['Office'];
            $model->Fax = $_POST['CreateContact']['Fax'];
            $model->auth_key = Yii::$app->security->generateRandomString();
            $model->status = '10';
            $model->is_admin = '1';
            $model->created_at = time();
            $model->updated_at = time();
            $model->CreatedOn = date('Y-m-d h:i:s');
            $model->IsActive = 1;

            if ($_POST['CreateContact']['id']) {
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
                        Yii::$app->session->setFlash('success', "Contact has been updated!");
                    }
                } else {
                    $project = \Yii::$app
                            ->db
                            ->createCommand()
                            ->insert('tbluser', $model)
                            ->execute();
                    if ($project) {
                        /* Yii::$app->mailer->compose()
                          ->setFrom('info@pmsoftware.com')
                          ->setTo($_POST['CreateContact']['Email'])
                          ->setSubject('Account Created')
                          ->setHtmlBody('<b>Your Password is ' . $password . '</b>')
                          ->send(); */
                        Yii::$app->session->setFlash('success', "Contact has been created!");
                    }
                }
            }

            return $this->redirect(array('manage-contacts'));

            die();
        } else {

            if ($id) {

                $contact = User::find()
                                ->where(['UserId' => $id])->all();
            } else {
                $contact = [];
            }

            return $this->render('createNewContact', [
                        'model' => $model,
                        'contact' => $contact,
            ]);
        }
    }

    /**
     * create-new-contact-modal action.
     *
     * @return mixed
     */
    public function actionCreateContactModal() {
        $user = Yii::$app->user->identity;
        $model = new User();

        if (isset($_POST['CreateContact'])) {
            $password = rand(1, 1000000);
            $hashpass = \Yii::$app->security->generatePasswordHash($password);
            $model->ParentId = $user->UserId;
            $model->username = $_POST['CreateContact']['FirstName'].rand(1, 100);
            $model->FirstName = $_POST['CreateContact']['FirstName'];
            $model->LastName = $_POST['CreateContact']['LastName'];
            $model->Street = $_POST['CreateContact']['Street'];
            $model->City = $_POST['CreateContact']['City'];
            $model->State = $_POST['CreateContact']['State'];
            $model->Zip = $_POST['CreateContact']['Zip'];
            $model->password_hash = $hashpass;
            $model->password = $password;
            $model->email = $_POST['CreateContact']['Email'];
            $model->Mobile = $_POST['CreateContact']['Phone'];
            $model->ContactNumber = $_POST['CreateContact']['Office'];
            $model->Fax = $_POST['CreateContact']['Fax'];
            $model->auth_key = Yii::$app->security->generateRandomString();
            $model->status = '10';
            $model->is_admin = '1';
            $model->created_at = time();
            $model->updated_at = time();
            $model->CreatedOn = date('Y-m-d h:i:s');
            $model->IsActive = 1;

            $useremail = User::find()
                            ->where(['email' => $_POST['CreateContact']['Email']])->all();

            if ($useremail) {
                echo "false";
            } else {
                $project = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('tbluser', $model)
                        ->execute();

                if ($project) {
                    /* Yii::$app->mailer->compose()
                      ->setFrom('info@pmsoftware.com')
                      ->setTo($_POST['CreateContact']['Email'])
                      ->setSubject('Account Created')
                      ->setHtmlBody('<b>Your Password is ' . $password . '</b>')
                      ->send(); */
                    echo "success";
                }
            }
            die();
        }
    }

    public function actionDeleteContact() {
        $id = Yii::$app->request->get('id');

        if ($id) {
            $connection = \Yii::$app->db;
            $model = $connection->createCommand('SELECT * FROM projectrole WHERE UserId = ' . $id . '');
            $proles = $model->queryAll();

            if ($proles) {
                $cdelete = \Yii::$app
                        ->db
                        ->createCommand()
                        ->delete('projectrole', ['UserId' => $id])
                        ->execute();
            }

            /* $projects = Project::find()->where(['ContactId' => $id])->all();
              foreach($projects as $project){

              Page::deleteAll(['ProjectId'=>$project->ProjectId]);
              Recentchanges::deleteAll(['ProjectId'=>$project->ProjectId]);

              Project::deleteAll(['ProjectId'=>$project->ProjectId]);
              } */

            $contact = User::deleteAll(['UserId' => $id]);
            if ($contact) {
                Yii::$app->session->setFlash('success', "Contact has been deleted!");
                return $this->redirect(array('manage-contacts'));
            }
        } else {
            return $this->redirect(array('manage-contacts'));
        }
    }

    /**
     * currentProjects action.
     *
     * @return mixed
     */
    public function actionCurrentProjects() {
        $pids = [];
        $user = Yii::$app->user->identity;
        if ($user->ParentId == 0) {
            $projects = Project::find()->where(['UserId' => $user->UserId])->orWhere(['UserId' => $user->ParentId]);
        } else {
            $roleprojects = Role::find()->where(['=', 'UserId', $user->UserId])->all();
            foreach ($roleprojects as $_project) {
                $pids[] = $_project->ProjectId;
            }
            $projects = Project::find()->where(['ProjectId' => $pids]);
        }
        $sort_by = NULL;
        $message = '';


        if (isset($_POST['action']) && ( $_POST['action'] == 'delete' )) {

            $ProjectId = $_POST['ProjectId'];

            $delete_pages = Page::deleteAll('ProjectId = :ProjectId', [':ProjectId' => $ProjectId]);
            $delete_roles = Role::deleteAll('ProjectId = :ProjectId', [':ProjectId' => $ProjectId]);
            $delete_recentchanges = Recentchanges::deleteAll('ProjectId = :ProjectId', [':ProjectId' => $ProjectId]);
            if ($delete_project = Project::deleteAll('ProjectId = :ProjectId', [':ProjectId' => $ProjectId])) {
                $message = "Project has deleted successfully.";
            }
        }

        if (isset($_POST['sort_by']) && !empty($_POST['sort_by'])) {

            $sort_by = $_POST['sort_by'];

            if ($sort_by == '1') {

                $projects->orderBy('EventStartDate ASC');
            } else if ($sort_by == '2') {

                $projects->orderBy('EventStartDate ASC');
            } else if ($sort_by == '3') {

                $projects->orderBy('EventName ASC');
            } else if ($sort_by == '4') {

                $projects->orderBy('CreatedOn DESC');
            }
        } else {
            $projects->orderBy('CreatedOn DESC');
        }

        $resp = $projects->all();

        return $this->render('currentProjects', [
                    'projects' => $resp,
                    'sort_by' => $sort_by,
                    'message' => $message
        ]);
    }

    /**
     * recentChanges action.
     *
     * @return mixed
     */
    public function actionRecentChanges() {
        $session = Yii::$app->session;
        $user = Yii::$app->user->identity;

        $recentchanges = Recentchanges::find()->where(['UserId' => $user->UserId]);

        if (isset($session['header_project'])) {

            $recentchanges->andWhere(['ProjectId' => $session['header_project']]);
        }
        $resp = $recentchanges->orderBy('CreatedOn DESC')->all();

        return $this->render('recentChanges', [
                    'recentchanges' => $resp,
        ]);
    }
	
	/**
     * recentChanges action.
     *
     * @return mixed
     */
    public function actionRecentChangesData() {
        $session = Yii::$app->session;
        $user = Yii::$app->user->identity;

        $recentchanges = Recentchanges::find()->where(['UserId' => $user->UserId]);

        if (isset($session['header_project'])) {

            $recentchanges->andWhere(['ProjectId' => $session['header_project']]);
        }
        $resp = $recentchanges->orderBy('CreatedOn DESC')->limit(5)->all();

        return $resp;
    }

    /**
     * manageContacts action.
     *
     * @return mixed
     */
    public function actionManageContacts() {
        $user = Yii::$app->user->identity;
        $contacts = User::find()->where(['ParentId' => $user->UserId, 'is_admin' => 1, 'IsActive' => 1])->orderBy('CreatedOn DESC')->all();

        return $this->render('manageContacts', [
                    'contacts' => $contacts,
        ]);
    }
	
	/**
     * manageContacts action.
     *
     * @return mixed
     */
    public function actionManageContactsData() {
        $user = Yii::$app->user->identity;
        $contacts = User::find()->where(['ParentId' => $user->UserId, 'is_admin' => 1, 'IsActive' => 1])->orderBy('CreatedOn DESC')->limit(5)->all();

        return $contacts;
    }

     /**
     * toDo action.
     *
     * @return mixed
     */
    public function actionToDo() {
        $session = Yii::$app->session;
        $user = Yii::$app->user->identity;

        $todos = ItemTodo::find()->where(['UserId' => $user->UserId, 'IsActive' => 1])->all();

        $i = 0;
        foreach ($todos as $_todo) {
            $project = [];
            $page = [];

            $pagesitem = PageItem::find()->where(['ItemID' => $_todo->PageItemId, 'UserId' => $user->UserId, 'IsActive' => 1])->one();
            if ($pagesitem) {
                $page = ProjectPage::find()->where(['PageID' => $pagesitem->PageId, 'IsActive' => 1])->one();
                if ($page) {
                    $project = Project::find()->where(['ProjectId' => $page->ProjectId, 'UserId' => $user->UserId, 'IsActive' => 1])->one();
                } else {
                    $itempages = ItemPage::find()->where(['PageItemId' => $_todo->PageItemId])->andWhere(['UserId' => $user->UserId])->one();
					if($itempages){
                    $page = ProjectPage::find()->where(['PageID' => $itempages->PageId, 'IsActive' => 1])->one();
                    if ($page) {
                        $project = Project::find()->where(['ProjectId' => $page->ProjectId, 'UserId' => $user->UserId, 'IsActive' => 1])->one();
                    } else {
                        $project = [];
                    }
					}
                }
            }

            ArrayHelper::setValue($todos, '' . $i . '.project', ['project' => $project]);
            ArrayHelper::setValue($todos, '' . $i . '.page', ['page' => $page]);
            $i++;
        }


        return $this->render('toDo', [
                    'todos' => $todos,
        ]);
    }

    /**
     * toDo action.
     *
     * @return mixed
     */
    public function actionToDoData() {
        $session = Yii::$app->session;
        $user = Yii::$app->user->identity;

        $todos = ItemTodo::find()->where(['UserId' => $user->UserId, 'IsActive' => 1])->limit(5)->all();

        $i = 0;
        foreach ($todos as $_todo) {
            $project = [];
            $page = [];

            $pagesitem = PageItem::find()->where(['ItemID' => $_todo->PageItemId, 'UserId' => $user->UserId, 'IsActive' => 1])->one();
            if ($pagesitem) {
                $page = ProjectPage::find()->where(['PageID' => $pagesitem->PageId, 'IsActive' => 1])->one();
                if ($page) {
                    $project = Project::find()->where(['ProjectId' => $page->ProjectId, 'UserId' => $user->UserId, 'IsActive' => 1])->one();
                } else {
                    $itempages = ItemPage::find()->where(['PageItemId' => $_todo->PageItemId])->andWhere(['UserId' => $user->UserId])->one();
					if($itempages){
                    $page = ProjectPage::find()->where(['PageID' => $itempages->PageId, 'IsActive' => 1])->one();
                    if ($page) {
                        $project = Project::find()->where(['ProjectId' => $page->ProjectId, 'UserId' => $user->UserId, 'IsActive' => 1])->one();
                    } else {
                        $project = [];
                    }
					}
                }
            }

            ArrayHelper::setValue($todos, '' . $i . '.project', ['project' => $project]);
            ArrayHelper::setValue($todos, '' . $i . '.page', ['page' => $page]);
            $i++;
        }
        return $todos;
    }
    /**
     * settings action.
     *
     * @return mixed
     */
    public function actionSettings() {
		
		$user_id = Yii::$app->user->identity->UserId;
		
		$setting = Setting::find()->where(['UserId' => $user_id])->One();
		
		
		
		if(isset($_POST) && !empty($_POST)){
			
			$isInserted = false;
			
			if(!$setting){
				$setting = new Setting();
				$isInserted = true;
			}
			
			$setting->UserId = $user_id;
			$setting->CompanyName = $_POST['CompanyName'];
			$setting->Address = $_POST['Address'];
			$setting->Version = $_POST['Version'];
			
			if($setting->save()){
				if($isInserted){
					Yii::$app->session->setFlash('success', "Setting has created.");
				}else{
					Yii::$app->session->setFlash('success', "Setting has updated.");
				}
			}else{
				echo "<pre>";
				print_r($setting);
				die();
			}
		}
		
        return $this->render('settings', [
				'Setting' => $setting
		]);
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

    /**
     * Project Details action.
     *
     * @return mixed
     */
    public function actionProjectDetails() {
        $user = Yii::$app->user->identity;

        $ProjectId = $_GET['project'];

        $project = Project::find()
                ->where(['ProjectId' => $ProjectId])
                ->andWhere(['or', ['UserId' => $user->UserId], ['UserId' => $user->ParentId]])
                ->one();

        return $this->render('projectDetails', [
                    "project" => $project,
        ]);
    }

    /**
     * Contact Details action.
     *
     * @return mixed
     */
    public function actionContactDetails() {
        $user = Yii::$app->user->identity;

        $ContactId = $_GET['contact'];

        $project = User::find()
                ->where(['UserId' => $ContactId])
                ->one();

        return $this->render('contactDetails', [
                    "contact" => $project,
        ]);
    }

    /**
     * create-project-by-template action.
     *
     * @return mixed
     */
    public function actionCreateProjectByTemplate() {
        $model = new CreateProject();
        $templates = Template::find()->all();

        return $this->render('createProjectByTemplate', [
                    'model' => $model,
                    'templates' => $templates,
        ]);
    }

    /**
     * template-pages action.
     *
     * @return mixed
     */
    public function actionManageTemplates() {
        $message = "";

        if (isset($_POST['action']) && ( $_POST['action'] == 'delete' )) {

            $TemplateId = $_POST['TemplateId'];
            $projects = Project::find()->where(['TemplateId' => $TemplateId])->all();


            $delete_template_pages = TemplatePage::deleteAll('TemplateId = :TemplateId', [':TemplateId' => $TemplateId]);
            $delete_template_roles = TemplateRole::deleteAll('TemplateId = :TemplateId', [':TemplateId' => $TemplateId]);

            foreach ($projects as $project) {
                $delete_pages = Page::deleteAll('ProjectId = :ProjectId', [':ProjectId' => $project->ProjectId]);
                $delete_roles = Role::deleteAll('ProjectId = :ProjectId', [':ProjectId' => $project->ProjectId]);
                $delete_recentchanges = Recentchanges::deleteAll('ProjectId = :ProjectId', [':ProjectId' => $project->ProjectId]);
            }

            $delete_project = Project::deleteAll('TemplateId = :TemplateId', [':TemplateId' => $TemplateId]);
            if ($delete_template = Template::deleteAll('TemplateId = :TemplateId', [':TemplateId' => $TemplateId])) {
                $message = "Template has deleted successfully.";
            }
        }

        $templates = Template::find()->orderBy('CreatedOn DESC')->all();

        return $this->render('manageTemplates', [
                    'templates' => $templates,
                    'message' => $message
        ]);
    }

    /**
     * template-pages action.
     *
     * @return mixed
     */
    public function actionCreateTemplate() {
        $model = new CreateTemplate();

        if (isset($_POST['CreateProject'])) {

            $model->TemplateName = $_POST['CreateProject']['TemplateName'];
            $model->PageName = isset($_POST['CreateProject']['PageName']) ? $_POST['CreateProject']['PageName'] : '';
            $model->ContactId = isset($_POST['CreateProject']['ContactId']) ? $_POST['CreateProject']['ContactId'] : [];
            $model->RoleName = isset($_POST['CreateProject']['RoleName']) ? $_POST['CreateProject']['RoleName'] : [];

            if ($template = $model->create()) {

                return 'success';

                //Yii::$app->session->setFlash('success', "Project Has Created inserted");
            } else {
                return 'error';
            }
            die();
        }


        return $this->render('createTemplate', [
                    'model' => $model,
        ]);
    }

    /**
     * update-template action.
     *
     * @return mixed
     */
    public function actionUpdateTemplate() {
        if (isset($_POST['CreateProject'])) {

            $model = new UpdateTemplate();

            $model->TemplateId = $_POST['CreateProject']['TemplateId'];
            $model->TemplateName = $_POST['CreateProject']['TemplateName'];

            /* response in array */
            $model->PageName = isset($_POST['CreateProject']['PageName']) ? $_POST['CreateProject']['PageName'] : [];
            $model->TemplatePageId = isset($_POST['CreateProject']['TemplatePageId']) ? $_POST['CreateProject']['TemplatePageId'] : [];
            $model->ContactId = isset($_POST['CreateProject']['ContactId']) ? $_POST['CreateProject']['ContactId'] : [];
            $model->RoleName = isset($_POST['CreateProject']['RoleName']) ? $_POST['CreateProject']['RoleName'] : [];
            $model->TemplateRoleId = isset($_POST['CreateProject']['TemplateRoleId']) ? $_POST['CreateProject']['TemplateRoleId'] : [];

            if ($project = $model->create()) {

                return 'success';

                //Yii::$app->session->setFlash('success', "Project Has Created inserted");
            } else {
                return 'error';
            }
            die();
        }


        if (isset($_GET['template'])) {

            $model = Template::find()->where(['TemplateId' => $_GET['template']])->one();

            return $this->render('updateTemplate', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * duplicate-project action.
     *
     * @return mixed
     */
    public function actionDuplicateProject() {
        $model = new CreateProject();
        $projects = Project::find()->orderBy('CreatedOn DESC')->all();

        return $this->render('duplicateProject', [
                    'model' => $model,
                    'projects' => $projects,
        ]);
    }

    /**
     * Change IsComplete action.
     *
     * @return mixed
     */
    public function actionChangeIsdismiss() {
        $RecentChangeId = $_POST['id'];

        $recentchanges = ItemTodo::find()->where(['ItemTodoId' => $RecentChangeId])->one();
        $recentchanges->IsComplete = 1;

        if ($recentchanges->save()) {
            echo json_encode($recentchanges->ItemTodoId);
        }
        die();
    }
	
	
    public function actionDeleterole() {
        $user = Yii::$app->user->identity;
        if ($_GET['id']) {
            $role = Role::deleteAll('RoleId = :RoleId', [':RoleId' => $_GET['id']]);

            Yii::$app->session->setFlash('success', "Role has been deleted!");
            return $this->redirect(array('site/update-project?project=' . $_GET['pid'] . ''));
        }
    }


}
