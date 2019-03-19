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
                        'actions' => ['logout', 'index', 'stats'],
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
            } else {
                if ($user->group_id != 4 && $user->group_id != 5) {
                    $tenders = \common\models\Tender::find()->where(['status' => '1'])->orWhere(['status' => '0']);
                } else {
                    $tenders = \common\models\Tender::find()->where(['status' => '1']);
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

    public function actionStats() {
        $user = Yii::$app->user->identity;
        $type = @$_POST['type'];
        $finalarr = [];
        $head = '';
        $makes = [];
        $sizes = [];
        $labelsone = '';
        $valuesone = '';
        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'items.tenderfour' => $type])->all();
        $archivetenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.is_archived' => 1, 'items.tenderfour' => $type])->all();

        $finalgraph[] = ['Command', 'Approved Tenders'];
        //commands
        $comm = ['1', '2', '6', '7', '8', '9', '10', '11', '12'];
        foreach ($comm as $i) {
            $tidsc = [];
            $iidsc = [];
            $archivetidsc = [];
            $archiveiidsc = [];
            if ($i == 1) {
                $tenderscommand = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'tenders.command' => [1, 3, 4, 5, 13], 'items.tenderfour' => $type])->all();
                $archivetenderscommand = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.is_archived' => 1, 'tenders.command' => [1, 3, 4, 5, 13], 'items.tenderfour' => $type])->all();
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
        //values
        $tids = [];
        $iids = [];
        $iidsone = [];
        $iidstwo = [];
        $iidsthree = [];
        $iidsfour = [];
        $iidsfive = [];
        $iidssix = [];
        $eprice = 0;

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
            //Lt
            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type])->all();
            $itemstwo = [];
            $itemsthree = [];
            $itemsfour = [];
            $itemsfive = [];
            $itemssix = [];
        }

        if (isset($itemsone)) {
            foreach ($itemsone as $_item) {
                $iidsone[] = $_item->id;
            }
        }
        $itemdetailone = \common\models\ItemDetails::find()->where(['item_id' => $iidsone])->all();
        if (isset($itemdetailone)) {
            foreach ($itemdetailone as $_detail) {
                if ($type == 1) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                } else {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                }
                if (@$price->price) {
                    $eprice += ($_detail->quantity * $price->price);
                }
            }
        }

        if (isset($itemstwo)) {
            foreach ($itemstwo as $_item) {
                $iidstwo[] = $_item->id;
            }
        }

        $itemdetailtwo = \common\models\ItemDetails::find()->where(['item_id' => $iidstwo])->all();

        if (isset($itemdetailtwo)) {
            foreach ($itemdetailtwo as $_detail) {
                if ($type == 1) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                } else {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                }
                if (@$price->price) {
                    $eprice += ($_detail->quantity * $price->price);
                }
            }
        }

        if (isset($itemsthree)) {
            foreach ($itemsthree as $_item) {
                $iidsthree[] = $_item->id;
            }
        }

        $itemdetailthree = \common\models\ItemDetails::find()->where(['item_id' => $iidsthree])->all();

        if (isset($itemdetailthree)) {
            foreach ($itemdetailthree as $_detail) {
                if ($type == 1) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                } else {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                }
                if (@$price->price) {
                    $eprice += ($_detail->quantity * $price->price);
                }
            }
        }

        if (isset($itemsfour)) {
            foreach ($itemsfour as $_item) {
                $iidsfour[] = $_item->id;
            }
        }

        $itemdetailfour = \common\models\ItemDetails::find()->where(['item_id' => $iidsfour])->all();

        if (isset($itemdetailfour)) {
            foreach ($itemdetailfour as $_detail) {
                if ($type == 1) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                } else {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                }
                if (@$price->price) {
                    $eprice += ($_detail->quantity * $price->price);
                }
            }
        }

        if (isset($itemsfive)) {
            foreach ($itemsfive as $_item) {
                $iidsfive[] = $_item->id;
            }
        }

        $itemdetailfive = \common\models\ItemDetails::find()->where(['item_id' => $iidsfive])->all();

        if (isset($itemdetailfive)) {
            foreach ($itemdetailfive as $_detail) {
                if ($type == 1) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                } else {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                }
                if (@$price->price) {
                    $eprice += ($_detail->quantity * $price->price);
                }
            }
        }

        if (isset($itemssix)) {
            foreach ($itemssix as $_item) {
                $iidssix[] = $_item->id;
            }
        }

        $itemdetailsix = \common\models\ItemDetails::find()->where(['item_id' => $iidssix])->all();

        if (isset($itemdetailsix)) {
            foreach ($itemdetailsix as $_detail) {
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

//values
        $tids = [];
        $iids = [];
        $iidsone = [];
        $iidstwo = [];
        $iidsthree = [];
        $iidsfour = [];
        $iidsfive = [];
        $iidssix = [];
        $epriceone = 0;

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
            $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type])->all();
            $itemstwo = [];
            $itemsthree = [];
            $itemsfour = [];
            $itemsfive = [];
            $itemssix = [];
        }
        if (isset($itemsone)) {
            foreach ($itemsone as $_item) {
                $iidsone[] = $_item->id;
            }
        }
        $itemdetailone = \common\models\ItemDetails::find()->where(['item_id' => $iidsone])->all();
        if (isset($itemdetailone)) {
            foreach ($itemdetailone as $_detail) {
                if ($type == 1) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                } else {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                }
                if (@$price->price) {
                    $epriceone += ($_detail->quantity * $price->price);
                }
            }
        }

        if (isset($itemstwo)) {
            foreach ($itemstwo as $_item) {
                $iidstwo[] = $_item->id;
            }
        }

        $itemdetailtwo = \common\models\ItemDetails::find()->where(['item_id' => $iidstwo])->all();

        if (isset($itemdetailtwo)) {
            foreach ($itemdetailtwo as $_detail) {
                if ($type == 1) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                } else {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                }
                if (@$price->price) {
                    $epriceone += ($_detail->quantity * $price->price);
                }
            }
        }

        if (isset($itemsthree)) {
            foreach ($itemsthree as $_item) {
                $iidsthree[] = $_item->id;
            }
        }

        $itemdetailthree = \common\models\ItemDetails::find()->where(['item_id' => $iidsthree])->all();

        if (isset($itemdetailthree)) {
            foreach ($itemdetailthree as $_detail) {
                if ($type == 1) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                } else {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                }
                if (@$price->price) {
                    $epriceone += ($_detail->quantity * $price->price);
                }
            }
        }

        if (isset($itemsfour)) {
            foreach ($itemsfour as $_item) {
                $iidsfour[] = $_item->id;
            }
        }

        $itemdetailfour = \common\models\ItemDetails::find()->where(['item_id' => $iidsfour])->all();

        if (isset($itemdetailfour)) {
            foreach ($itemdetailfour as $_detail) {
                if ($type == 1) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                } else {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                }
                if (@$price->price) {
                    $epriceone += ($_detail->quantity * $price->price);
                }
            }
        }

        if (isset($itemsfive)) {
            foreach ($itemsfive as $_item) {
                $iidsfive[] = $_item->id;
            }
        }

        $itemdetailfive = \common\models\ItemDetails::find()->where(['item_id' => $iidsfive])->all();

        if (isset($itemdetailfive)) {
            foreach ($itemdetailfive as $_detail) {
                if ($type == 1) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                } else {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                }
                if (@$price->price) {
                    $epriceone += ($_detail->quantity * $price->price);
                }
            }
        }

        if (isset($itemssix)) {
            foreach ($itemssix as $_item) {
                $iidssix[] = $_item->id;
            }
        }

        $itemdetailsix = \common\models\ItemDetails::find()->where(['item_id' => $iidssix])->all();

        if (isset($itemdetailsix)) {
            foreach ($itemdetailsix as $_detail) {
                if ($type == 1) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                } else {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description])->one();
                }
                if (@$price->price) {
                    $epriceone += ($_detail->quantity * $price->price);
                }
            }
        }


        /* $finalgraphce[] = ['Cheif Engineers', 'Approved Tenders'];
          $cengineers = \common\models\Cengineer::find()->where(['command' => [6, 7, 8, 9, 10, 11]])->all();
          //commands
          if (isset($cengineers) && count($cengineers)) {
          foreach ($cengineers as $_cengineer) {
          $tidsc = [];
          $iidsc = [];
          $archivetidsc = [];
          $tenderscommand = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'tenders.cengineer' => $_cengineer->cid, 'items.tenderfour' => $type])->all();
          $cengineer = str_replace(' - MES', '', str_replace('AND ', '', strstr($_cengineer->text, 'AND ')));

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
          if ($_idetail->quantity != '' && is_numeric($_idetail->quantity)) {
          $graphonequantity += $_idetail->quantity;
          }
          }
          }

          $finalgraphce[] = [$cengineer, $graphonequantity];
          }
          } */

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
                $eprice = $onequantity * 500;
                $epriceone = $twoquantity * 500;
                $balancedprice = ($eprice - $epriceone);
            }
           
            $labelsone = 'ALL MAKES';
            $valuesone = $onequantity;

            $balanced = (count($tenders) - count($archivetenders));
            $balancedq = ($onequantity - $twoquantity);
            $balancedprice = ($eprice - $epriceone);
            $makes = \common\models\Make::find()->where(['mtype' => $type, 'status' => 1])->orderBy(['make' => SORT_ASC])->all();
            $sizes = \common\models\Size::find()->where(['mtypeone' => 1, 'mtypetwo' => 1, 'mtypethree' => 1, 'status' => 1])->all();
            $finalarr[] = ['title' => 'All Tenders', 'total' => count($tenders), 'quantity' => $onequantity, 'value' => $this->actionMoneyformat(round($eprice))];
            $finalarr[] = ['title' => 'Archived Tenders', 'total' => count($archivetenders), 'quantity' => $twoquantity, 'value' => $this->actionMoneyformat(round($epriceone))];
            $finalarr[] = ['title' => 'Balance Tenders', 'total' => $balanced, 'quantity' => $balancedq, 'value' => $this->actionMoneyformat(round($balancedprice))];
        }

        return $this->render('stats', [
                    'details' => $finalarr,
                    'makes' => $makes,
                    'head' => $head,
                    'sizes' => $sizes,
                    'labels' => $labelsone,
                    'values' => $valuesone,
                    'graphs' => $finalgraph,
                    'graphsce' => ''
        ]);
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

}
