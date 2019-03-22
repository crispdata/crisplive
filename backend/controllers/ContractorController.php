<?php

namespace backend\controllers;

use Yii;
use yii\web\Session;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\helpers\ArrayHelper;
use common\models\PageItem;
use common\models\User;
use common\models\Page;
use common\models\ItemPage;
use common\models\Setting;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use yii\db\Query;
use yii\db\ActiveQuery;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use yii\data\Pagination;
use yii\widgets\LinkPager;

/**
 * Contractor controller
 */
class ContractorController extends Controller {

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
                        'actions' => ['logout', 'index', 'uploadfile', 'allcontractors', 'updatecontractors', 'getcontractors', 'add-contractor', 'delete-contractor'],
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
        $user = Yii::$app->user->identity;
        return $this->render('index', [
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

    public function actionUploadfile() {
        $user = Yii::$app->user->identity;
        $data = [];
        if (!empty($_FILES['cfile']['name'])) {
            $pathinfo = pathinfo($_FILES["cfile"]["name"]);
            $inputFileName = $_FILES['cfile']['tmp_name'];

            $reader = ReaderFactory::create(Type::XLSX);
            $reader->open($inputFileName);
            $count = 1;

            // Number of sheet in excel file
            foreach ($reader->getSheetIterator() as $sheet) {
                // Number of Rows in Excel sheet
                foreach ($sheet->getRowIterator() as $row) {

                    // It reads data after header. In the my excel sheet, 
                    // header is in the first row. 
                    if ($count > 1) {

                        // Data of excel sheet
                        $data['firm'] = @$row[1];
                        $data['name'] = @$row[2];
                        $data['address'] = @$row[3];
                        $data['contact'] = @$row[4];
                        $data['email'] = @$row[5];

                        $data = ['firm' => $data['firm'], 'name' => $data['name'], 'address' => $data['address'], 'contact' => $data['contact'], 'email' => $data['email'], 'user_id' => $user->UserId, 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];

                        if (@$row[1] != '') {
                            $contractor = \Yii::$app
                                    ->db
                                    ->createCommand()
                                    ->insert('contractors', $data)
                                    ->execute();
                        }
                    }
                    $count++;
                }
            }
            $reader->close();
            Yii::$app->session->setFlash('success', "Contractors successfully added");
            return $this->redirect(array('contractor/index'));
        }
    }

    public function actionAllcontractors() {

        $val = @$_POST['sort'];
        $page = @$_REQUEST['page'];
        $filter = @$_GET['filter'];


        $contractors = \common\models\Contractor::find()->orderBy(['firmname' => SORT_ASC]);
        $countQuery = clone $contractors;
        if ($val && $page) {
            $items_per_page = $val;
            $pages = new Pagination(['totalCount' => $countQuery->count(), 'defaultPageSize' => $val, 'pageSize' => $val]);
            /* if ($page) {
              $offset = ($page - 1) * $items_per_page;
              } else {
              $offset = 0;
              } */
            $offset = $pages->offset;
        } else {
            if ($filter) {
                $fval = $filter;
            } else {
                $fval = '10';
            }
            $pages = new Pagination(['totalCount' => $countQuery->count(), 'defaultPageSize' => $fval, 'pageSize' => $fval]);
            $offset = $pages->offset;
            $items_per_page = $fval;
        }

        $models = $contractors->offset($offset)->limit($items_per_page)->all();


        if ($val) {
            return $this->redirect(array('contractor/allcontractors?filter=' . $val . ''));
        } else {
            return $this->render('allcontractors', [
                        'contractors' => $models,
                        'pages' => $pages,
                        'total' => $countQuery->count()
            ]);
        }
    }

    public function actionAddContractor() {
        $user = Yii::$app->user->identity;
        $id = @$_GET['id'];
        if (isset($_POST['submit'])) {

            if ($_POST['id']) {
                $model = \common\models\Contractor::find()->where(['id' => $_POST['id']])->one();
                $model->firm = @$_POST['firm'];
                $model->firmname = str_replace('M/s ', '', @$_POST['firm']);
                $model->name = @$_POST['name'];
                $model->address = @$_POST['address'];
                $model->contact = @$_POST['contact'];
                $model->email = $_POST['email'];
                $model->save();

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Contractor successfully updated");
                }
            } else {
                $model = new \common\models\Contractor();
                $model->firm = @$_POST['firm'];
                $model->firmname = str_replace('M/s ', '', @$_POST['firm']);
                $model->name = @$_POST['name'];
                $model->address = @$_POST['address'];
                $model->contact = @$_POST['contact'];
                $model->email = $_POST['email'];
                $model->user_id = $user->UserId;
                $model->createdon = date('Y-m-d h:i:s');
                $model->status = 1;

                $contractor = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('contractors', $model)
                        ->execute();

                if ($contractor) {
                    Yii::$app->session->setFlash('success', "Contractor successfully added");
                }
            }

            return $this->redirect(array('contractor/allcontractors'));

            die();
        } else {
            if ($id) {
                $contractor = \common\models\Contractor::find()->where(['id' => $id])->one();
            } else {
                $contractor = [];
            }

            return $this->render('add-contractor', [
                        'contractor' => $contractor
            ]);
        }
    }

    public function actionDeleteContractor() {
        $id = $_GET['id'];
        $ids = [];
        $delete = \common\models\Contractor::deleteAll(['id' => $id]);
        if ($delete) {
            Yii::$app->session->setFlash('success', "Contractor successfully deleted");
            return $this->redirect(array('contractor/allcontractors'));
        }
    }

    public function actionGetcontractors() {
        $page = $_REQUEST['page'];
        $resultCount = 25;
        @$allcon = [];
        $offset = ($page - 1) * $resultCount;

        $contractors = \common\models\Contractor::find()->where(['like', 'firm', '%' . @$_REQUEST['term'] . '%', false])->orWhere(['like', 'address', '%' . @$_REQUEST['term'] . '%', false])->andWhere(['status' => 1])->orderBy(['firmname' => SORT_ASC])->offset($offset)->limit($resultCount)->all();

        if ($contractors) {
            foreach ($contractors as $_contractor) {
                @$allcon[] = ['id' => $_contractor->id, 'text' => $_contractor->firm . ' - ' . $_contractor->address];
            }
        }
        $count = count(\common\models\Contractor::find()->where(['like', 'firm', '%' . @$_REQUEST['term'] . '%', false])->orWhere(['like', 'address', '%' . @$_REQUEST['term'] . '%', false])->andWhere(['status' => 1])->all());
        $endCount = $offset + $resultCount;
        $morePages = $count > $endCount;

        $results = array(
            "results" => $allcon,
            "pagination" => array(
                "more" => $morePages
            )
        );

        echo json_encode($results);
        die();
    }

    public function actionUpdatecontractors() {
        $contractors = \common\models\Contractor::find()->where(['status' => 1])->all();
        if ($contractors) {
            foreach ($contractors as $_contractor) {
                $fname = str_replace('M/s ', '', $_contractor->firm);
                $fname = str_replace('M/S ', '', $fname);
                $_contractor->firmname = trim($fname);
                $_contractor->save();
            }
        }
    }

}
