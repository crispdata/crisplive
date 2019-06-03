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
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
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
                        'actions' => ['logout', 'index', 'stats', 'items', 'getprice', 'getpricewithparams'],
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
            /* if (isset($_REQUEST['tendertype']) && $_REQUEST['tendertype'] != '') {
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
              } else {
              if ($user->group_id != 4 && $user->group_id != 5) {
              $tenders = \common\models\Tender::find()->where(['status' => '1'])->orWhere(['status' => '0']);
              } else {
              $tenders = \common\models\Tender::find()->where(['status' => '1']);
              }
              } */
            $authtype = @$_REQUEST['authtype'];
            if (@$authtype) {
                if ($authtype == 1) {
                    $make = $_REQUEST['cables'];
                    if (@$make) {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $authtype])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make]);
                    } else {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'items.tenderfour' => $authtype]);
                    }
                } elseif ($authtype == 2) {
                    $make = $_REQUEST['lighting'];
                    if (@$make) {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $authtype])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make]);
                    } else {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'items.tenderfour' => $authtype]);
                    }
                } elseif ($authtype == 3) {
                    $make = $_REQUEST['wires'];
                    $authtype = 5;
                    if (@$make) {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $authtype])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make]);
                    } else {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'items.tenderfour' => $authtype]);
                    }
                } elseif ($authtype == 4) {
                    $make = $_REQUEST['cement'];
                    $authtype = 14;
                    if (@$make) {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tendertwo' => $authtype])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make]);
                    } else {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'items.tendertwo' => $authtype]);
                    }
                } elseif ($authtype == 5) {
                    $make = $_REQUEST['rsteel'];
                    $authtype = 15;
                    if (@$make) {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tendertwo' => $authtype])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make]);
                    } else {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'items.tendertwo' => $authtype]);
                    }
                } elseif ($authtype == 6) {
                    $make = $_REQUEST['ssteel'];
                    $authtype = 16;
                    if (@$make) {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tendertwo' => $authtype])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make]);
                    } else {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'items.tendertwo' => $authtype]);
                    }
                } elseif ($authtype == 7) {
                    $make = $_REQUEST['nsteel'];
                    $authtype = 17;
                    if (@$make) {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tendertwo' => $authtype])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make]);
                    } else {
                        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'items.tendertwo' => $authtype]);
                    }
                }
            } else {
                if ($user->group_id == 6) {
                    $type = @$user->authtype;
                    if ($type == 1) {
                        $make = $user->cables;
                    } elseif ($type == 2) {
                        $make = $user->lighting;
                    } else {
                        $make = $user->cables;
                    }
                    $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $type])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make]);
                } else {
                    $tenders = \common\models\Tender::find()->where(['status' => '1'])->orWhere(['status' => 0]);
                }
            }

            $fromdate = @$_REQUEST['fromdate'];
            $todate = @$_REQUEST['todate'];
            if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                $tenders->andWhere(['and',
                    ['>=', 'tenders.bid_end_date', $fromdate],
                    ['<=', 'tenders.bid_end_date', $todate],
                ]);
            } elseif (isset($fromdate) && $fromdate != '') {
                $tenders->andWhere(['and',
                    ['>=', 'tenders.bid_end_date', $fromdate],
                ]);
            } elseif (isset($todate) && $todate != '') {
                $tenders->andWhere(['and',
                    ['<=', 'tenders.bid_end_date', $todate],
                ]);
            }

            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                $tenders->andWhere(['or',
                    ['like', 'tenders.work', '%' . @$_REQUEST['keyword'] . '%', false],
                    ['like', 'tenders.reference_no', '%' . @$_REQUEST['keyword'] . '%', false],
                    ['like', 'tenders.tender_id', '%' . @$_REQUEST['keyword'] . '%', false]
                ]);
            }
            if (isset($_REQUEST['contype']) && $_REQUEST['contype'] != '') {
                $tenders->andWhere(['and',
                    ['contractor' => $_REQUEST['contype']]
                ]);
            }
            if (isset($_REQUEST['department']) && $_REQUEST['department'] != '') {
                $tenders->andWhere(['and',
                    ['department' => $_REQUEST['department']]
                ]);
            }
            if (isset($_REQUEST['command']) && $_REQUEST['command'] != '' && $_REQUEST['command'] != 0) {
                $tenders->andWhere(['and',
                    ['command' => $_REQUEST['command']]
                ]);
            }
            if ((isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') && (@$_REQUEST['cwengineer'] == '') && (@$_REQUEST['gengineer'] == '')) {
                $tenders->andWhere(['and',
                    ['cengineer' => $_REQUEST['cengineer']],
                    ['cwengineer' => null],
                    ['gengineer' => null]
                ]);
            }
            if ((isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') && (isset($_REQUEST['cwengineer']) && $_REQUEST['cwengineer'] != '') && (@$_REQUEST['gengineer'] == '')) {
                $tenders->andWhere(['and',
                    ['cengineer' => $_REQUEST['cengineer']],
                    ['cwengineer' => $_REQUEST['cwengineer']],
                    ['gengineer' => null]
                ]);
            }
            if ((isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') && (isset($_REQUEST['cwengineer']) && $_REQUEST['cwengineer'] != '') && (isset($_REQUEST['gengineer']) && $_REQUEST['gengineer'] != '')) {
                $tenders->andWhere(['and',
                    ['cengineer' => $_REQUEST['cengineer']],
                    ['cwengineer' => $_REQUEST['cwengineer']],
                    ['gengineer' => $_REQUEST['gengineer']]
                ]);
            }
            if ((!isset($_REQUEST['cengineer'])) && (!isset($_REQUEST['cwengineer'])) && (isset($_REQUEST['gengineer']) && $_REQUEST['gengineer'] != '')) {
                $tenders->andWhere(['and',
                    ['cengineer' => null],
                    ['cwengineer' => null],
                    ['gengineer' => $_REQUEST['gengineer']]
                ]);
            }

            $tenders->orderBy(['id' => SORT_DESC])->groupBy(['id']);

            $countQuery = clone $tenders;

            if ($val && $page) {
                $items_per_page = $val;
                $pages = new Pagination(['totalCount' => $countQuery->count(), 'defaultPageSize' => $val, 'pageSize' => $val]);
                $offset = $pages->offset;
            } else {
                if ($filter) {
                    $fval = $filter;
                } else {
                    $fval = '5';
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
                $cables = \common\models\Make::find()->where(['mtype' => 1])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
                $lights = \common\models\Make::find()->where(['mtype' => 2])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
                $wires = \common\models\Make::find()->where(['mtype' => 5])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
                $cements = \common\models\Make::find()->where(['mtype' => 14])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
                $rsteel = \common\models\Make::find()->where(['mtype' => 15])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
                $ssteel = \common\models\Make::find()->where(['mtype' => 16])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
                $nsteel = \common\models\Make::find()->where(['mtype' => 17])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
                return $this->render('index', [
                            'tenders' => $models,
                            'pages' => $pages,
                            'total' => $countQuery->count(),
                            'type' => 'All',
                            'url' => 'index',
                            'cables' => $cables,
                            'lights' => $lights,
                            'wires' => $wires,
                            'cements' => $cements,
                            'rsteel' => $rsteel,
                            'ssteel' => $ssteel,
                            'nsteel' => $nsteel,
                ]);
            }
        } else {
            $cables = \common\models\Make::find()->where(['mtype' => 1])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $lights = \common\models\Make::find()->where(['mtype' => 2])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $wires = \common\models\Make::find()->where(['mtype' => 5])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $cements = \common\models\Make::find()->where(['mtype' => 14])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $rsteel = \common\models\Make::find()->where(['mtype' => 15])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $ssteel = \common\models\Make::find()->where(['mtype' => 16])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $nsteel = \common\models\Make::find()->where(['mtype' => 17])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            return $this->render('index', [
                        'cables' => $cables,
                        'lights' => $lights,
                        'wires' => $wires,
                        'cements' => $cements,
                        'rsteel' => $rsteel,
                        'ssteel' => $ssteel,
                        'nsteel' => $nsteel,
            ]);
        }
    }

    public function actionGetprice() {
        $user = Yii::$app->user->identity;
        $ptype = @$_REQUEST['ptype'];
        $type = @$_REQUEST['type'];
        $quantity = @$_REQUEST['quantity'];
        $key = @$_REQUEST['key'];

        $tids = [];
        $iids = [];
        $iidsone = [];
        $iidstwo = [];
        $iidsthree = [];
        $iidsfour = [];
        $iidsfive = [];
        $iidssix = [];
        $eprice = 0;
        $epricelight = 0;

        if ($user->group_id == 6) {
            $authtype = @$user->authtype;
            if ($authtype == 1) {
                $make = $user->cables;
            } elseif ($authtype == 2) {
                $make = $user->lighting;
            } else {
                $make = $user->cables;
            }
            if ($ptype == 1) {
                if ($type == 1) {
                    $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                    $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                    $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
                    //Ht
                    $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                } else {
                    $epricelight = ($quantity * 500);
                }
            } elseif ($ptype == 2) {
                if ($type == 1) {
                    $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                    $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                    $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
                    //Ht
                    $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                } else {
                    $epricelight = ($quantity * 500);
                }
            } else {
                if ($type == 1) {
                    $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                    $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                    $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
                    //Ht
                    $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                } else {
                    $epricelight = ($quantity * 500);
                }
            }
        } else {
            if ($ptype == 1) {
                if ($type == 1) {
                    $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                    $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                    $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
                    //Ht
                    $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                } else {
                    $epricelight = ($quantity * 500);
                }
            } elseif ($ptype == 2) {
                if ($type == 1) {
                    $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                    $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                    $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
                    //Ht
                    $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                } else {
                    $epricelight = ($quantity * 500);
                }
            } else {
                if ($type == 1) {
                    $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                    $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                    $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
                    //Ht
                    $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                } else {
                    $epricelight = ($quantity * 500);
                }
            }
        }


        if (isset($itemsone)) {
            foreach ($itemsone as $_item) {
                $iidsone[] = $_item->id;
            }
        }

        if ($user->group_id == 6 && $key == 1) {
            $itemdetailone = \common\models\ItemDetails::find()->where(['item_id' => $iidsone])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();
        } else {
            $itemdetailone = \common\models\ItemDetails::find()->where(['item_id' => $iidsone])->all();
        }

        $marray = [];
        if (isset($itemdetailone)) {
            foreach ($itemdetailone as $_detail) {
                if ($user->group_id == 6 && $key == 2) {
                    $marray = explode(',', $_detail->make);
                    if (!in_array($make, $marray)) {
                        if ($type == 1) {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                        } else {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                        }
                        if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                            $eprice += ($_detail->quantity * $price->price);
                        }
                    }
                } else {
                    if ($type == 1) {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    } else {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                    }
                    if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                        $eprice += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        if (isset($itemstwo)) {
            foreach ($itemstwo as $_item) {
                $iidstwo[] = $_item->id;
            }
        }

        if ($user->group_id == 6 && $key == 1) {
            $itemdetailtwo = \common\models\ItemDetails::find()->where(['item_id' => $iidstwo])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();
        } else {
            $itemdetailtwo = \common\models\ItemDetails::find()->where(['item_id' => $iidstwo])->all();
        }

        $marray = [];
        if (isset($itemdetailtwo)) {
            foreach ($itemdetailtwo as $_detail) {
                if ($user->group_id == 6 && $key == 2) {
                    $marray = explode(',', $_detail->make);
                    if (!in_array($make, $marray)) {
                        if ($type == 1) {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                        } else {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                        }
                        if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                            $eprice += ($_detail->quantity * $price->price);
                        }
                    }
                } else {
                    if ($type == 1) {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    } else {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                    }
                    if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                        $eprice += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        if (isset($itemsthree)) {
            foreach ($itemsthree as $_item) {
                $iidsthree[] = $_item->id;
            }
        }

        if ($user->group_id == 6 && $key == 1) {
            $itemdetailthree = \common\models\ItemDetails::find()->where(['item_id' => $iidsthree])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();
        } else {
            $itemdetailthree = \common\models\ItemDetails::find()->where(['item_id' => $iidsthree])->all();
        }

        $marray = [];
        if (isset($itemdetailthree)) {
            foreach ($itemdetailthree as $_detail) {
                if ($user->group_id == 6 && $key == 2) {
                    $marray = explode(',', $_detail->make);
                    if (!in_array($make, $marray)) {
                        if ($type == 1) {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                        } else {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                        }
                        if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                            $eprice += ($_detail->quantity * $price->price);
                        }
                    }
                } else {
                    if ($type == 1) {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    } else {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                    }
                    if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                        $eprice += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        if (isset($itemsfour)) {
            foreach ($itemsfour as $_item) {
                $iidsfour[] = $_item->id;
            }
        }

        if ($user->group_id == 6 && $key == 1) {
            $itemdetailfour = \common\models\ItemDetails::find()->where(['item_id' => $iidsfour])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();
        } else {
            $itemdetailfour = \common\models\ItemDetails::find()->where(['item_id' => $iidsfour])->all();
        }

        $marray = [];
        if (isset($itemdetailfour)) {
            foreach ($itemdetailfour as $_detail) {
                if ($user->group_id == 6 && $key == 2) {
                    $marray = explode(',', $_detail->make);
                    if (!in_array($make, $marray)) {
                        if ($type == 1) {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                        } else {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                        }
                        if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                            $eprice += ($_detail->quantity * $price->price);
                        }
                    }
                } else {
                    if ($type == 1) {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    } else {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                    }
                    if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                        $eprice += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        if (isset($itemsfive)) {
            foreach ($itemsfive as $_item) {
                $iidsfive[] = $_item->id;
            }
        }

        if ($user->group_id == 6 && $key == 1) {
            $itemdetailfive = \common\models\ItemDetails::find()->where(['item_id' => $iidsfive])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();
        } else {
            $itemdetailfive = \common\models\ItemDetails::find()->where(['item_id' => $iidsfive])->all();
        }

        $marray = [];
        if (isset($itemdetailfive)) {
            foreach ($itemdetailfive as $_detail) {
                if ($user->group_id == 6 && $key == 2) {
                    $marray = explode(',', $_detail->make);
                    if (!in_array($make, $marray)) {
                        if ($type == 1) {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                        } else {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                        }
                        if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                            $eprice += ($_detail->quantity * $price->price);
                        }
                    }
                } else {
                    if ($type == 1) {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    } else {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                    }
                    if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                        $eprice += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        if (isset($itemssix)) {
            foreach ($itemssix as $_item) {
                $iidssix[] = $_item->id;
            }
        }

        if ($user->group_id == 6 && $key == 1) {
            $itemdetailsix = \common\models\ItemDetails::find()->where(['item_id' => $iidssix])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();
        } else {
            $itemdetailsix = \common\models\ItemDetails::find()->where(['item_id' => $iidssix])->all();
        }

        $marray = [];
        if (isset($itemdetailsix)) {
            foreach ($itemdetailsix as $_detail) {
                if ($user->group_id == 6 && $key == 2) {
                    $marray = explode(',', $_detail->make);
                    if (!in_array($make, $marray)) {
                        if ($type == 1) {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                        } else {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                        }
                        if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                            $eprice += ($_detail->quantity * $price->price);
                        }
                    }
                } else {
                    if ($type == 1) {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    } else {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                    }
                    if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                        $eprice += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        if ($type == 1) {
            echo $this->actionConvertnumber(round($eprice));
        } else {
            echo $this->actionConvertnumber(round($epricelight));
        }
        die();
    }

    public function actionGetpricewithparams() {
        $user = Yii::$app->user->identity;
        $ptype = @$_REQUEST['ptype'];
        $type = @$_REQUEST['type'];
        $command = @$_REQUEST['command'];
        $fromdate = @$_REQUEST['fromdate'];
        $todate = @$_REQUEST['todate'];
        $make = @$_REQUEST['make'];
        $quantity = @$_REQUEST['quantity'];
        $key = @$_REQUEST['key'];


        $tids = [];
        $iids = [];
        $iidsone = [];
        $iidstwo = [];
        $iidsthree = [];
        $iidsfour = [];
        $iidsfive = [];
        $iidssix = [];
        $eprice = 0;

        if ($user->group_id == 6) {
            $authtype = @$user->authtype;
            if ($authtype == 1) {
                $make = $user->cables;
            } elseif ($authtype == 2) {
                $make = $user->lighting;
            } else {
                $make = $user->cables;
            }

            if (isset($command) && $command != '' && $command != 15) {
                if ($command == 1) {
                    $commall = [1, 3, 4, 5, 13, 14];
                } else {
                    $commall = $command;
                }
                if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {

                    if ($type == 1) {
                        $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
//Ht
                        $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                    } else {
                        $epricelight = ($quantity * 500);
                    }
                } else {

                    if ($type == 1) {
                        $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                        $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                        $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                        $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
//Ht
                        $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                        $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                    } else {
                        $epricelight = ($quantity * 500);
                    }
                }
            } else {
                if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {

                    if ($type == 1) {
                        $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
//Ht
                        $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                    } else {
                        $epricelight = ($quantity * 500);
                    }
                } else {
                    if ($type == 1) {
//Lt
                        $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                        $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                        $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                        $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
//Ht
                        $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                        $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                    } else {
                        $epricelight = ($quantity * 500);
                    }
                }
            }
        } else {
            if (isset($command) && $command != '' && $command != 15) {
                if ($command == 1) {
                    $commall = [1, 3, 4, 5, 13, 14];
                } else {
                    $commall = $command;
                }
                if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                    if ($ptype == 1 || $ptype == 4) {
                        if ($type == 1) {
                            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
//Ht
                            $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        } else {
                            $epricelight = ($quantity * 500);
                        }
                    } elseif ($ptype == 2) {
                        if ($type == 1) {
                            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
//Ht
                            $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        } else {
                            $epricelight = ($quantity * 500);
                        }
                    } else {
                        if ($type == 1) {
                            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
//Ht
                            $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        } else {
                            $epricelight = ($quantity * 500);
                        }
                    }
                } else {
                    if ($ptype == 1 || $ptype == 4) {
                        if ($type == 1) {
                            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                            $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                            $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                            $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
//Ht
                            $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                            $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                        } else {
                            $epricelight = ($quantity * 500);
                        }
                    } elseif ($ptype == 2) {
                        if ($type == 1) {
                            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                            $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                            $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                            $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
//Ht
                            $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                            $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                        } else {
                            $epricelight = ($quantity * 500);
                        }
                    } else {
                        if ($type == 1) {
                            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                            $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                            $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                            $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'tenders.command' => $commall, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
//Ht
                            $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                            $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'tenders.command' => $commall, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                        } else {
                            $epricelight = ($quantity * 500);
                        }
                    }
                }
            } else {
                if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                    if ($ptype == 1 || $ptype == 4) {
                        if ($type == 1) {
                            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
//Ht
                            $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        } else {
                            $epricelight = ($quantity * 500);
                        }
                    } elseif ($ptype == 2) {
                        if ($type == 1) {
                            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
//Ht
                            $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        } else {
                            $epricelight = ($quantity * 500);
                        }
                    } else {
                        if ($type == 1) {
                            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
//Ht
                            $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                            $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                        } else {
                            $epricelight = ($quantity * 500);
                        }
                    }
                } else {
                    if ($ptype == 1 || $ptype == 4) {
                        if ($type == 1) {
//Lt
                            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                            $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                            $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                            $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
//Ht
                            $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                            $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                        } else {
                            $epricelight = ($quantity * 500);
                        }
                    } elseif ($ptype == 2) {
                        if ($type == 1) {
//Lt
                            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                            $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                            $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                            $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
//Ht
                            $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                            $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                        } else {
                            $epricelight = ($quantity * 500);
                        }
                    } else {
                        if ($type == 1) {
//Lt
                            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                            $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                            $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                            $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
//Ht
                            $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                            $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.is_archived' => null, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                        } else {
                            $epricelight = ($quantity * 500);
                        }
                    }
                }
            }
        }


        if (isset($itemsone)) {
            foreach ($itemsone as $_item) {
                $iidsone[] = $_item->id;
            }
        }

        if (($user->group_id == 6 && $key == 1) || $ptype == 4) {
            $itemdetailone = \common\models\ItemDetails::find()->where(['item_id' => $iidsone])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();
        } else {
            $itemdetailone = \common\models\ItemDetails::find()->where(['item_id' => $iidsone])->all();
        }

        $marray = [];
        if (isset($itemdetailone)) {
            foreach ($itemdetailone as $_detail) {
                if ($user->group_id == 6 && $key == 2) {
                    $marray = explode(',', $_detail->make);
                    if (!in_array($make, $marray)) {
                        if ($type == 1) {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                        } else {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                        }
                        if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                            $eprice += ($_detail->quantity * $price->price);
                        }
                    }
                } else {
                    if ($type == 1) {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    } else {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                    }
                    if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                        $eprice += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        if (isset($itemstwo)) {
            foreach ($itemstwo as $_item) {
                $iidstwo[] = $_item->id;
            }
        }

        if (($user->group_id == 6 && $key == 1) || $ptype == 4) {
            $itemdetailtwo = \common\models\ItemDetails::find()->where(['item_id' => $iidstwo])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();
        } else {
            $itemdetailtwo = \common\models\ItemDetails::find()->where(['item_id' => $iidstwo])->all();
        }

        $marray = [];
        if (isset($itemdetailtwo)) {
            foreach ($itemdetailtwo as $_detail) {
                if ($user->group_id == 6 && $key == 2) {
                    $marray = explode(',', $_detail->make);
                    if (!in_array($make, $marray)) {
                        if ($type == 1) {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                        } else {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                        }
                        if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                            $eprice += ($_detail->quantity * $price->price);
                        }
                    }
                } else {
                    if ($type == 1) {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    } else {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                    }
                    if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                        $eprice += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        if (isset($itemsthree)) {
            foreach ($itemsthree as $_item) {
                $iidsthree[] = $_item->id;
            }
        }

        if (($user->group_id == 6 && $key == 1) || $ptype == 4) {
            $itemdetailthree = \common\models\ItemDetails::find()->where(['item_id' => $iidsthree])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();
        } else {
            $itemdetailthree = \common\models\ItemDetails::find()->where(['item_id' => $iidsthree])->all();
        }

        $marray = [];
        if (isset($itemdetailthree)) {
            foreach ($itemdetailthree as $_detail) {
                if ($user->group_id == 6 && $key == 2) {
                    $marray = explode(',', $_detail->make);
                    if (!in_array($make, $marray)) {
                        if ($type == 1) {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                        } else {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                        }
                        if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                            $eprice += ($_detail->quantity * $price->price);
                        }
                    }
                } else {
                    if ($type == 1) {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    } else {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                    }
                    if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                        $eprice += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        if (isset($itemsfour)) {
            foreach ($itemsfour as $_item) {
                $iidsfour[] = $_item->id;
            }
        }

        if (($user->group_id == 6 && $key == 1) || $ptype == 4) {
            $itemdetailfour = \common\models\ItemDetails::find()->where(['item_id' => $iidsfour])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();
        } else {
            $itemdetailfour = \common\models\ItemDetails::find()->where(['item_id' => $iidsfour])->all();
        }

        $marray = [];
        if (isset($itemdetailfour)) {
            foreach ($itemdetailfour as $_detail) {
                if ($user->group_id == 6 && $key == 2) {
                    $marray = explode(',', $_detail->make);
                    if (!in_array($make, $marray)) {
                        if ($type == 1) {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                        } else {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                        }
                        if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                            $eprice += ($_detail->quantity * $price->price);
                        }
                    }
                } else {
                    if ($type == 1) {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    } else {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                    }
                    if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                        $eprice += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        if (isset($itemsfive)) {
            foreach ($itemsfive as $_item) {
                $iidsfive[] = $_item->id;
            }
        }

        if (($user->group_id == 6 && $key == 1) || $ptype == 4) {
            $itemdetailfive = \common\models\ItemDetails::find()->where(['item_id' => $iidsfive])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();
        } else {
            $itemdetailfive = \common\models\ItemDetails::find()->where(['item_id' => $iidsfive])->all();
        }

        $marray = [];
        if (isset($itemdetailfive)) {
            foreach ($itemdetailfive as $_detail) {
                if ($user->group_id == 6 && $key == 2) {
                    $marray = explode(',', $_detail->make);
                    if (!in_array($make, $marray)) {
                        if ($type == 1) {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                        } else {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                        }
                        if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                            $eprice += ($_detail->quantity * $price->price);
                        }
                    }
                } else {
                    if ($type == 1) {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    } else {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                    }
                    if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                        $eprice += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        if (isset($itemssix)) {
            foreach ($itemssix as $_item) {
                $iidssix[] = $_item->id;
            }
        }

        if (($user->group_id == 6 && $key == 1) || $ptype == 4) {
            $itemdetailsix = \common\models\ItemDetails::find()->where(['item_id' => $iidssix])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();
        } else {
            $itemdetailsix = \common\models\ItemDetails::find()->where(['item_id' => $iidssix])->all();
        }

        $marray = [];
        if (isset($itemdetailsix)) {
            foreach ($itemdetailsix as $_detail) {
                if ($user->group_id == 6 && $key == 2) {
                    $marray = explode(',', $_detail->make);
                    if (!in_array($make, $marray)) {
                        if ($type == 1) {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                        } else {
                            $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                        }
                        if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                            $eprice += ($_detail->quantity * $price->price);
                        }
                    }
                } else {
                    if ($type == 1) {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    } else {
                        $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                    }
                    if (@$price->price && $_detail->quantity != '' && is_numeric($_detail->quantity)) {
                        $eprice += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        if ($type == 1) {
            echo $this->actionConvertnumber(round($eprice));
        } else {
            echo $this->actionConvertnumber(round($epricelight));
        }
        die();
    }

    public function actionConvertnumber($num) {
        $num = str_replace(',', '', $num);
        $ext = ""; //thousand,lac, crore
        $number_of_digits = strlen($num); //this is call :)
        if ($number_of_digits > 3) {
            if ($number_of_digits % 2 != 0) {
                $tens = "1";
                $number = $number_of_digits - 1;
                if ($number > 8)
                    return 10000000;

                while (($number - 1) > 0) {
                    $tens .= "0";
                    $number--;
                }
                $divider = $tens;
            } else {
                $tens = "1";
                $number = $number_of_digits;
                if ($number > 8)
                    return 10000000;

                while (($number - 1) > 0) {
                    $tens .= "0";
                    $number--;
                }
                $divider = $tens;
            }
        } else {
            $divider = 1;
        }
        $fraction = $num / $divider;
        $fraction = number_format($fraction, 2);
        if ($number_of_digits == 4 || $number_of_digits == 5)
            $ext = "k";
        if ($number_of_digits == 6 || $number_of_digits == 7)
            $ext = "Lac";
        if ($number_of_digits == 8 || $number_of_digits == 9)
            $ext = "Cr";
        return $fraction . " " . $ext;
    }

    public function actionStats() {
        $user = Yii::$app->user->identity;
        $type = @$_POST['type'];
        $finalarr = [];
        $head = '';
        $makes = [];
        $sizes = [];
        $labelsone = '';
        $valuesone = '';

        if (isset($type) && $type != '') {
            $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'items.tenderfour' => $type])->all();
            $archivetenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.is_archived' => 1, 'items.tenderfour' => $type])->all();

            $finalgraph[] = ['Command', 'All Tenders'];
            //commands
            $comm = ['1', '2', '6', '7', '8', '9', '10', '11', '12'];
            foreach ($comm as $i) {
                $tidsc = [];
                $iidsc = [];
                $archivetidsc = [];
                $archiveiidsc = [];
                if ($i == 1) {
                    $tenderscommand = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'tenders.command' => [1, 3, 4, 5, 13, 14], 'items.tenderfour' => $type])->all();
                    $archivetenderscommand = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.is_archived' => 1, 'tenders.command' => [1, 3, 4, 5, 13, 14], 'items.tenderfour' => $type])->all();
                } else {
                    $tenderscommand = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'tenders.command' => $i, 'items.tenderfour' => $type])->all();
                    $archivetenderscommand = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.is_archived' => 1, 'tenders.command' => $i, 'items.tenderfour' => $type])->all();
                }
                $command = $this->actionGetcommandgraph($i);

                if (isset($tenderscommand) && count($tenderscommand)) {
                    foreach ($tenderscommand as $_tender) {
                        $tidsc[] = $_tender->id;
                    }
                }
                $itemsc = \common\models\Item::find()->where(['tender_id' => $tidsc, 'tenderfour' => $type])->all();
                if (isset($itemsc) && count($itemsc)) {
                    foreach ($itemsc as $_item) {
                        $iidsc[] = $_item->id;
                    }
                }

                $graphonequantity = 0;
                $idetails = \common\models\ItemDetails::find()->where(['item_id' => $iidsc])->all();
                if (isset($idetails) && count($idetails)) {
                    foreach ($idetails as $_idetail) {
                        if ($_idetail->quantity != '') {
                            $graphonequantity += $_idetail->quantity;
                        }
                    }
                }



                //archive
                if (isset($archivetenderscommand) && count($archivetenderscommand)) {
                    foreach ($archivetenderscommand as $_tender) {
                        $archivetidsc[] = $_tender->id;
                    }
                }
                $items = \common\models\Item::find()->where(['tender_id' => $archivetidsc, 'tenderfour' => $type])->all();
                if (isset($items) && count($items)) {
                    foreach ($items as $_item) {
                        $archiveiidsc[] = $_item->id;
                    }
                }

                $graphtwoquantity = 0;
                $idetails = \common\models\ItemDetails::find()->where(['item_id' => $archiveiidsc])->all();
                if (isset($idetails) && count($idetails)) {
                    foreach ($idetails as $_idetail) {
                        if ($_idetail->quantity != '') {
                            $graphtwoquantity += $_idetail->quantity;
                        }
                    }
                }

                $finalgraph[] = [$command, $graphonequantity];
            }

            if (isset($type)) {
                if (isset($tenders) && count($tenders)) {
                    foreach ($tenders as $_tender) {
                        $tids[] = $_tender->id;
                    }
                }
                $items = \common\models\Item::find()->where(['tender_id' => $tids, 'tenderfour' => $type])->all();
                if (isset($items) && count($items)) {
                    foreach ($items as $_item) {
                        $iids[] = $_item->id;
                    }
                }
                $idetails = \common\models\ItemDetails::find()->where(['item_id' => $iids])->all();
                if (isset($idetails) && count($idetails)) {
                    foreach ($idetails as $_idetail) {
                        $allquantity[] = ['quantity' => $_idetail->quantity];
                    }
                }

                $archivetids = [];
                if (isset($archivetenders) && count($archivetenders)) {
                    foreach ($archivetenders as $_tender) {
                        $archivetids[] = $_tender->id;
                    }
                }
                $items = \common\models\Item::find()->where(['tender_id' => $archivetids, 'tenderfour' => $type])->all();

                $archiveiids = [];
                if (isset($items) && count($items)) {
                    foreach ($items as $_item) {
                        $archiveiids[] = $_item->id;
                    }
                }
                $idetails = \common\models\ItemDetails::find()->where(['item_id' => $archiveiids])->all();
                if (isset($idetails) && count($idetails)) {
                    foreach ($idetails as $_idetail) {
                        if ($_idetail->quantity != '') {
                            $archivequantity[] = ['quantity' => $_idetail->quantity];
                        }
                    }
                }

                $sizes = [];
                $onequantity = 0;
                if (isset($allquantity) && count($allquantity)) {
                    foreach ($allquantity as $_quantity) {
                        if ($_quantity['quantity'] != '' && is_numeric($_quantity['quantity'])) {
                            $onequantity += $_quantity['quantity'];
                        }
                    }
                }

                $sizes = [];
                $twoquantity = 0;
                if (isset($archivequantity) && count($archivequantity)) {
                    foreach ($archivequantity as $_quantity) {
                        $twoquantity += $_quantity['quantity'];
                    }
                }

                if ($type == 1) {
                    $unit = 'RM';
                    $head = 'Quantity in Meter';
                } elseif ($type == 2) {
                    $unit = 'NOS';
                    $head = 'No. of Fixtures';
                } else {
                    $unit = 'RM';
                    $head = 'Quantity in Meter';
                }

                if ($type == 2) {
                    $gettlights = [];
                    $lightchart = [];
                    $lightchart[] = ['type', 'value'];
                    $lightmakechart = [];
                    $lightmakechart[] = ['type', 'value'];
                    $typelight = \common\models\Fitting::find()->where(['type' => 1, 'status' => 1])->orderBy(['text' => SORT_ASC])->all();

                    if (isset($typelight) && count($typelight)) {
                        foreach ($typelight as $_tlight) {
                            $ilightsone = \common\models\ItemDetails::find()->leftJoin('items', 'itemdetails.item_id = items.id')->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['items.tenderfour' => $type])->andWhere(['tenders.status' => 1, 'itemdetails.typefitting' => $_tlight->id]);
                            $lquantity = $ilightsone->sum('quantity');
                            $lightchart[] = [(string) $_tlight->text, (int) $lquantity];
                            //$ilightsmakeone = \common\models\ItemDetails::find()->leftJoin('items', 'itemdetails.item_id = items.id')->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['items.tenderfour' => $type])->andWhere(['tenders.status' => 1, 'itemdetails.typefitting' => $_tlight->id])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make]);
                            //$lmakequantity = $ilightsmakeone->sum('quantity');
                            //$lightmakechart[] = [(string) $_tlight->text, (int) $lmakequantity];
                        }
                    }
                }



                $makes = \common\models\Make::find()->where(['mtype' => $type, 'status' => 1])->orderBy(['make' => SORT_ASC])->all();
                if (isset($makes) && count($makes)) {
                    foreach ($makes as $_make) {
                        $tendersmake = \common\models\ItemDetails::find()->leftJoin('items', 'itemdetails.item_id = items.id')->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['items.tenderfour' => $type])->andWhere(['tenders.status' => 1])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $_make->id]);
                        $makequantity = $tendersmake->sum('quantity');
                        $piemakes[] = [(string) $_make->make, (int) $makequantity];
                    }
                }
                usort($piemakes, function($a, $b) {
                    return $b['1'] - $a['1'];
                });
                $title[] = ['type', 'value'];

                $finalarrmakes = array_merge($title, $piemakes);

                $labelsone = 'ALL MAKES';
                $valuesone = $onequantity;

                $balanced = (count($tenders) - count($archivetenders));
                $balancedq = ($onequantity - $twoquantity);

                $sizes = \common\models\Size::find()->where(['mtypeone' => 1, 'mtypetwo' => 1, 'mtypethree' => 1, 'status' => 1])->all();
                $finalarr[] = ['title' => 'All Tenders', 'total' => count($tenders), 'quantity' => $onequantity, 'value' => ''];
                $finalarr[] = ['title' => 'Archived Tenders', 'total' => count($archivetenders), 'quantity' => $twoquantity, 'value' => ''];
                $finalarr[] = ['title' => 'Balance Tenders', 'total' => $balanced, 'quantity' => $balancedq, 'value' => ''];
            }

            return $this->render('stats', [
                        'details' => $finalarr,
                        'makes' => $makes,
                        'head' => $head,
                        'sizes' => $sizes,
                        'labels' => $labelsone,
                        'values' => $valuesone,
                        'graphs' => $finalgraph,
                        'lightchart' => @$lightchart,
                        'piemakes' => @$finalarrmakes,
                        'lightmakechart' => @$lightmakechart,
                        'type' => $type,
                        'graphsce' => ''
            ]);
        } else {
            return $this->render('stats', [
            ]);
        }
    }

    public function actionGetcommandgraph($id) {
        switch ($id) {
            case "1":
                return "ADG (CG and PROJECT) CHENNAI";
                break;
            case "2":
                return "ADG DESIGN PUNE";
                break;
            case "3":
                return "CE (FY) HYDERABAD";
                break;
            case "4":
                return "CE (R and D) DELHI";
                break;
            case "5":
                return "CE (R and D) SECUNDERABAD";
                break;
            case "13":
                return "CE (CG) Visakhapatnam";
                break;
            case "14":
                return "ADG (Project) Chennai";
                break;
            case "6":
                return "CENTRAL";
                break;
            case "7":
                return "EASTERN";
                break;
            case "8":
                return "NORTHERN";
                break;
            case "9":
                return "SOUTHERN";
                break;
            case "10":
                return "SOUTH WESTERN";
                break;
            case "11":
                return "WESTERN";
                break;
            case "12":
                return "DGNP MUMBAI";
                break;
            default:
                return "";
        }
    }

    public function actionMoneyformat($number) {
        $decimal = (string) ($number - floor($number));
        $money = floor($number);
        $length = strlen($money);
        $delimiter = '';
        $money = strrev($money);

        for ($i = 0; $i < $length; $i++) {
            if (( $i == 3 || ($i > 3 && ($i - 1) % 2 == 0) ) && $i != $length) {
                $delimiter .= ',';
            }
            $delimiter .= $money[$i];
        }

        $result = strrev($delimiter);
        $decimal = preg_replace("/0\./i", ".", $decimal);
        $decimal = substr($decimal, 0, 3);

        if ($decimal != '0') {
            $result = $result . $decimal;
        }

        return $result;
    }

    public function actionItems() {
        $user = Yii::$app->user->identity;

        require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
        $s3paths = [];
        try {
            // You may need to change the region. It will say in the URL when the bucket is open
            // and on creation.
            $s3 = S3Client::factory(
                            array(
                                'credentials' => array(
                                    'key' => Yii::$app->params['IAM_KEY'],
                                    'secret' => Yii::$app->params['IAM_SECRET']
                                ),
                                'version' => 'latest',
                                'region' => 'us-east-2',
                            )
            );
        } catch (Exception $e) {
            // We use a die, so if this fails. It stops here. Typically this is a REST call so this would
            // return a json object.
            die("Error: " . $e->getMessage());
        }

        $contractors = [];
        if (isset($_GET['submit'])) {

            $val = @$_REQUEST['sort'];
            $page = @$_REQUEST['page'];
            $filter = @$_REQUEST['filter'];
            if ($filter) {
                $fval = $filter;
            } else {
                $fval = '5';
            }
            $offset = ($page - 1) * $fval;

            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                $tendersall = \common\models\Tender::find()->select('tenderfiles.file as file, tenders.tender_id,tenders.id')->leftJoin('tenderfiles', 'tenders.id = tenderfiles.tender_id')->where(['tenders.status' => '1', 'tenders.aoc_status' => 1, 'tenderfiles.type' => 2]);
                $fromdate = @$_REQUEST['fromdate'];
                $todate = @$_REQUEST['todate'];
                if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                    $tendersall->andWhere(['and',
                        ['>=', 'tenders.bid_end_date', $fromdate],
                        ['<=', 'tenders.bid_end_date', $todate],
                    ]);
                } elseif (isset($fromdate) && $fromdate != '') {
                    $tendersall->andWhere(['and',
                        ['>=', 'tenders.bid_end_date', $fromdate],
                    ]);
                } elseif (isset($todate) && $todate != '') {
                    $tendersall->andWhere(['and',
                        ['<=', 'tenders.bid_end_date', $todate],
                    ]);
                }


                if (isset($_REQUEST['command']) && $_REQUEST['command'] != '' && $_REQUEST['command'] != 0) {
                    $tendersall->andWhere(['and',
                        ['command' => $_REQUEST['command']]
                    ]);
                }
                if ((isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') && (@$_REQUEST['cwengineer'] == '') && (@$_REQUEST['gengineer'] == '')) {
                    $tendersall->andWhere(['and',
                        ['cengineer' => $_REQUEST['cengineer']],
                        ['cwengineer' => null],
                        ['gengineer' => null]
                    ]);
                }
                if ((isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') && (isset($_REQUEST['cwengineer']) && $_REQUEST['cwengineer'] != '') && (@$_REQUEST['gengineer'] == '')) {
                    $tendersall->andWhere(['and',
                        ['cengineer' => $_REQUEST['cengineer']],
                        ['cwengineer' => $_REQUEST['cwengineer']],
                        ['gengineer' => null]
                    ]);
                }
                if ((isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') && (isset($_REQUEST['cwengineer']) && $_REQUEST['cwengineer'] != '') && (isset($_REQUEST['gengineer']) && $_REQUEST['gengineer'] != '')) {
                    $tendersall->andWhere(['and',
                        ['cengineer' => $_REQUEST['cengineer']],
                        ['cwengineer' => $_REQUEST['cwengineer']],
                        ['gengineer' => $_REQUEST['gengineer']]
                    ]);
                }
                if ((!isset($_REQUEST['cengineer'])) && (!isset($_REQUEST['cwengineer'])) && (isset($_REQUEST['gengineer']) && $_REQUEST['gengineer'] != '')) {
                    $tendersall->andWhere(['and',
                        ['cengineer' => null],
                        ['cwengineer' => null],
                        ['gengineer' => $_REQUEST['gengineer']]
                    ]);
                }

                $alltenders = $tendersall->orderBy(['tenderfiles.id' => SORT_DESC])->asArray()->all();
                $tids = [];
                $items = [];

                if (isset($alltenders) && count($alltenders)) {
                    foreach ($alltenders as $_tender) {
                        $files = explode('/', $_tender['file']);
                        $xls_obj = array(
                            'Bucket' => $files[3],
                            'Key' => $files[4] . '/' . $files[5],
                            'SaveAs' => $_SERVER['DOCUMENT_ROOT'] . "/admin/" . $files[4] . '/' . $files[5]
                        );

                        $file = '"s3"://' . $files[3] . '/' . $files[4] . '/' . $files[5];
                        $s3->getObject($xls_obj);

                        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($files[4] . '/' . $files[5]);
                        $spreadSheet = $reader->setReadDataOnly(true);
                        $data = $reader->load($files[4] . '/' . $files[5]);
                        $worksheetData = $reader->listWorksheetInfo($files[4] . '/' . $files[5]);
                        /* echo '<h3>Worksheet Information</h3>';
                          echo '<ol>';
                          foreach ($worksheetData as $worksheet) {
                          echo '<li>', $worksheet['worksheetName'], '<br />';
                          echo 'Rows: ', $worksheet['totalRows'],
                          ' Columns: ', $worksheet['totalColumns'], '<br />';
                          echo 'Cell Range: A1:',
                          $worksheet['lastColumnLetter'], $worksheet['totalRows'];
                          echo '</li>';
                          }
                          echo '</ol>'; */
                        foreach ($worksheetData as $worksheet) {
                            $range = 'A7:' . $worksheet['lastColumnLetter'] . $worksheet['totalRows'];
                            break;
                        }
                        $dataArray = $data->getActiveSheet()
                                ->rangeToArray(
                                $range, // The worksheet range that we want to retrieve
                                NULL, // Value that should be returned for empty cells
                                TRUE, // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                                TRUE, // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                                TRUE         // Should the array be indexed by cell row and cell column
                        );

                        $dataArray = array_map('array_values', $dataArray);

                        if ($this->actionInarray($_REQUEST['keyword'], $dataArray)) {
                            $tids[] = $_tender['id'];
                            $items[$_tender['id']] = $this->actionInarray($_REQUEST['keyword'], $dataArray);
                        }
                    }
                }
            }

            $session = Yii::$app->session;

            if (!isset($page)) {
                $alltids = $tids;
                $session->set('tids', $tids);
            } else {
                $alltids = $session->get('tids');
            }

            $filestodelete = glob($_SERVER['DOCUMENT_ROOT'] . "/admin/files/*"); // get all file names
            foreach ($filestodelete as $filez) { // iterate files
                if (is_file($filez))
                    unlink($filez); // delete file
            }

            $tenders = \common\models\Tender::find()->where(['status' => '1', 'aoc_status' => 1, 'id' => $alltids]);

            $fromdate = @$_REQUEST['fromdate'];
            $todate = @$_REQUEST['todate'];
            if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                $tenders->andWhere(['and',
                    ['>=', 'tenders.bid_end_date', $fromdate],
                    ['<=', 'tenders.bid_end_date', $todate],
                ]);
            } elseif (isset($fromdate) && $fromdate != '') {
                $tenders->andWhere(['and',
                    ['>=', 'tenders.bid_end_date', $fromdate],
                ]);
            } elseif (isset($todate) && $todate != '') {
                $tenders->andWhere(['and',
                    ['<=', 'tenders.bid_end_date', $todate],
                ]);
            }


            if (isset($_REQUEST['command']) && $_REQUEST['command'] != '' && $_REQUEST['command'] != 0) {
                $tenders->andWhere(['and',
                    ['command' => $_REQUEST['command']]
                ]);
            }
            if ((isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') && (@$_REQUEST['cwengineer'] == '') && (@$_REQUEST['gengineer'] == '')) {
                $tenders->andWhere(['and',
                    ['cengineer' => $_REQUEST['cengineer']],
                    ['cwengineer' => null],
                    ['gengineer' => null]
                ]);
            }
            if ((isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') && (isset($_REQUEST['cwengineer']) && $_REQUEST['cwengineer'] != '') && (@$_REQUEST['gengineer'] == '')) {
                $tenders->andWhere(['and',
                    ['cengineer' => $_REQUEST['cengineer']],
                    ['cwengineer' => $_REQUEST['cwengineer']],
                    ['gengineer' => null]
                ]);
            }
            if ((isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') && (isset($_REQUEST['cwengineer']) && $_REQUEST['cwengineer'] != '') && (isset($_REQUEST['gengineer']) && $_REQUEST['gengineer'] != '')) {
                $tenders->andWhere(['and',
                    ['cengineer' => $_REQUEST['cengineer']],
                    ['cwengineer' => $_REQUEST['cwengineer']],
                    ['gengineer' => $_REQUEST['gengineer']]
                ]);
            }
            if ((!isset($_REQUEST['cengineer'])) && (!isset($_REQUEST['cwengineer'])) && (isset($_REQUEST['gengineer']) && $_REQUEST['gengineer'] != '')) {
                $tenders->andWhere(['and',
                    ['cengineer' => null],
                    ['cwengineer' => null],
                    ['gengineer' => $_REQUEST['gengineer']]
                ]);
            }

            $tenders->orderBy(['id' => SORT_DESC])->groupBy(['id']);

            $countQuery = clone $tenders;

            if ($val && $page) {
                $items_per_page = $val;
                $pages = new Pagination(['totalCount' => $countQuery->count(), 'defaultPageSize' => $val, 'pageSize' => $val]);
                $offset = $pages->offset;
            } else {
                if ($filter) {
                    $fval = $filter;
                } else {
                    $fval = '5';
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
                return $this->redirect(array('/search/items?' . $string . '&filter=' . $val . ''));
            } else {
                return $this->render('items', [
                            'tenders' => $models,
                            'srnos' => $items,
                            'pages' => $pages,
                            'total' => $countQuery->count(),
                            'type' => 'All',
                            'url' => 'items',
                ]);
            }
        } else {
            return $this->render('items', [
            ]);
        }
    }

    public function actionInarray($needle, $haystack, $strict = false) {
        $itemnos = [];
        foreach ($haystack as $key => $item) {

            if (stripos($item[1], $needle) !== false) {
                $itemnos[] = $item[0];
            }
        }
        return $itemnos;
    }

}
