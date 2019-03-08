<?php

namespace backend\controllers;

use Yii;
use yii\web\Session;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\helpers\ArrayHelper;
use common\models\User;
use yii\db\Query;
use yii\db\ActiveQuery;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use backend\controllers\SiteController;
use Twilio\Rest\Client;
use yii\data\Pagination;
use yii\widgets\LinkPager;

/**
 * Products controller
 */
class SearchController extends Controller {

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
                        'actions' => ['logout', 'index'],
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

    public function actionIndex() {
        $user = Yii::$app->user->identity;

        $contractors = [];
        if (isset($_GET['submit'])) {

            $val = @$_REQUEST['sort'];
            $page = @$_REQUEST['page'];
            $filter = @$_REQUEST['filter'];
            if (isset($_REQUEST['tendertype']) && $_REQUEST['tendertype'] != '') {
                if ($_REQUEST['tendertype'] == 1) {
                    $tenders = \common\models\Tender::find()->where(['status' => '1'])->orWhere(['status' => '0']);
                } elseif ($_REQUEST['tendertype'] == 2) {
                    $tenders = \common\models\Tender::find()->where(['status' => 1, 'aoc_status' => null]);
                } elseif ($_REQUEST['tendertype'] == 3) {
                    $tenders = \common\models\Tender::find()->where(['status' => '0']);
                } elseif ($_REQUEST['tendertype'] == 4) {
                    $tenders = \common\models\Tender::find()->where(['aoc_status' => 1]);
                } elseif ($_REQUEST['tendertype'] == 5) {
                    $tenders = \common\models\Tender::find()->where(['on_hold' => null, 'aoc_status' => 1, 'is_archived' => null]);
                } elseif ($_REQUEST['tendertype'] == 6) {
                    $tenders = \common\models\Tender::find()->where(['on_hold' => 1, 'aoc_status' => 1, 'is_archived' => null]);
                } else {
                    $tenders = \common\models\Tender::find()->where(['aoc_status' => 1, 'is_archived' => 1]);
                }
            }
            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                $tenders->andWhere(['or',
                    ['like', 'work', '%' . @$_REQUEST['keyword'] . '%', false],
                    ['like', 'reference_no', '%' . @$_REQUEST['keyword'] . '%', false],
                    ['like', 'tender_id', '%' . @$_REQUEST['keyword'] . '%', false]
                ]);
            }
            if (isset($_REQUEST['contype']) && $_REQUEST['contype'] != '') {
                $tenders->andWhere(['and',
                    ['contractor' => $_REQUEST['contype']]
                ]);
            }
            if (isset($_REQUEST['command']) && $_REQUEST['command'] != '') {
                $tenders->andWhere(['and',
                    ['command' => $_REQUEST['command']]
                ]);
            }
            if (isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') {
                $tenders->andWhere(['and',
                    ['cengineer' => $_REQUEST['cengineer']]
                ]);
            }
            if (isset($_REQUEST['cwengineer']) && $_REQUEST['cwengineer'] != '') {
                $tenders->andWhere(['and',
                    ['cwengineer' => $_REQUEST['cwengineer']]
                ]);
            }
            if (isset($_REQUEST['gengineer']) && $_REQUEST['gengineer'] != '') {
                $tenders->andWhere(['and',
                    ['gengineer' => $_REQUEST['gengineer']]
                ]);
            }
            $tenders->orderBy(['id' => SORT_DESC]);
            $countQuery = clone $tenders;
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

            $models = $tenders->offset($offset)->limit($items_per_page)->all();

            if ($val) {
                $urlnew = str_replace('/admin', '', Yii::$app->request->url);
                $parsed = parse_url($urlnew);
                $query = $parsed['query'];

                parse_str($query, $params);

                unset($params['page']);
                unset($params['filter']);
                $string = http_build_query($params);
                return $this->redirect(array('/search/index?' . $string . '&filter=' . $val . ''));
            } else {
                return $this->render('index', [
                            'tenders' => $models,
                            'pages' => $pages,
                            'total' => $countQuery->count(),
                            'type' => 'All',
                            'url' => 'index'
                ]);
            }
        } else {
            return $this->render('index', [
            ]);
        }
    }

}
