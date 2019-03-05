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
use common\models\Setting;
use common\models\Contact;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use yii\db\Query;
use yii\db\ActiveQuery;
use Aws\S3\S3Client;
use Aws\Common\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Aws\S3\Exception\S3Exception;
use yii\web\UploadedFile;
use app\models\UploadForm;
use yii\data\Pagination;
use yii\widgets\LinkPager;

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
                        'actions' => ['login', 'error', 'signup'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'approvedtenders', 'tenders', 'movearchive', 'delete-user', 'movearchivetenders', 'searchtenders', 'movetoarchive', 'getmakedetails', 'getsinglelightdata', 'getsingledata', 'on-hold', 'archivetenders', 'aocready', 'aochold', 'dealers', 'manufacturers', 'contractors', 'searchtender', 'gettenders', 'getcities', 'delete-client', 'edit-client', 'change-status-client', 'delete-size', 'delete-fitting', 'delete-tenders', 'getsizes', 'getfittings', 'change-status', 'getgroupbyid', 'edit-user', 'approvetenders', 'approveitem', 'upcomingtenders', 'editprofile', 'create-tender', 'items', 'create-item', 'delete-tender', 'getdata', 'getseconddata', 'getthirddata', 'view-items', 'getfourdata', 'getfivedata', 'getsixdata', 'e-m', 'civil', 'create-make-em', 'create-make-civil', 'create-size', 'create-fitting', 'delete-make', 'getmakes', 'delete-item', 'delete-items', 'edit-item', 'json', 'approvetender', 'getcengineer', 'getcwengineer', 'getgengineer', 'getcommand', 'getcebyid', 'getcwebyid', 'getcengineerbycommand', 'getcengineerbycommandview', 'getcwengineerbyce', 'getcwengineerbyceview', 'getgengineerbycwe', 'getgengineerbycweview', 'changecommand', 'getitemdesc', 'gettendertwo', 'gettenderthree', 'gettenderfour', 'gettenderfive', 'gettendersix', 'tenderone', 'tendertwo', 'tenderthree', 'tenderfour', 'tenderfive', 'tendersix', 'technicalstatus', 'financialstatus', 'aocstatus', 'technicaltenders', 'financialtenders', 'aoctenders', 'utenders', 'atenders', 'create-user', 'users', 'sizes', 'fittings', 'clients'],
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

    public function actionIndex() {
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
        for ($i = 1; $i <= 13; $i++) {
            $tidsc = [];
            $iidsc = [];
            $archivetidsc = [];
            $archiveiidsc = [];
            $tenderscommand = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'tenders.command' => $i, 'items.tenderfour' => $type])->all();
            $archivetenderscommand = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.is_archived' => 1, 'tenders.command' => $i, 'items.tenderfour' => $type])->all();
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

        //Lt
        $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
        $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
        $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
        $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
        //Ht
        $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
        $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
        if (isset($itemsone)) {
            foreach ($itemsone as $_item) {
                $iidsone[] = $_item->id;
            }
        }
        $itemdetailone = \common\models\ItemDetails::find()->where(['item_id' => $iidsone])->all();
        if (isset($itemdetailone)) {
            foreach ($itemdetailone as $_detail) {
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                if (@$price->price) {
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

        //Lt
        $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
        $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
        $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
        $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
        //Ht
        $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
        $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
        if (isset($itemsone)) {
            foreach ($itemsone as $_item) {
                $iidsone[] = $_item->id;
            }
        }
        $itemdetailone = \common\models\ItemDetails::find()->where(['item_id' => $iidsone])->all();
        if (isset($itemdetailone)) {
            foreach ($itemdetailone as $_detail) {
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                if (@$price->price) {
                    $epriceone += ($_detail->quantity * $price->price);
                }
            }
        }


        $finalgraphce[] = ['Cheif Engineers', 'Approved Tenders'];
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
                        if ($_idetail->quantity != '') {
                            $graphonequantity += $_idetail->quantity;
                        }
                    }
                }

                $finalgraphce[] = [$cengineer, $graphonequantity];
            }
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

            if (isset($archivetenders) && count($archivetenders)) {
                foreach ($archivetenders as $_tender) {
                    $archivetids[] = $_tender->id;
                }
            }
            $items = \common\models\Item::find()->where(['tender_id' => $archivetids, 'tenderfour' => $type])->all();
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
                    $onequantity += $_quantity['quantity'];
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
                $head = 'Quantity in Metres';
            } else {
                $unit = 'NOS';
                $head = 'No. of Fixtures';
            }

            $labelsone = 'ALL MAKES';
            $valuesone = $onequantity;

            $balanced = (count($tenders) - count($archivetenders));
            $balancedq = ($onequantity - $twoquantity);
            $balancedprice = ($eprice - $epriceone);
            $makes = \common\models\Make::find()->where(['mtype' => $type, 'status' => 1])->orderBy(['make' => SORT_ASC])->all();
            $sizes = \common\models\Size::find()->where(['mtypeone' => 1, 'mtypetwo' => 1, 'mtypethree' => 1, 'status' => 1])->all();
            $finalarr[] = ['title' => 'All Tenders', 'total' => count($tenders), 'quantity' => $onequantity, 'value' => $this->actionMoneyformat($eprice)];
            $finalarr[] = ['title' => 'Archived Tenders', 'total' => count($archivetenders), 'quantity' => $twoquantity, 'value' => $this->actionMoneyformat($epriceone)];
            $finalarr[] = ['title' => 'Balance Tenders', 'total' => $balanced, 'quantity' => $balancedq, 'value' => $this->actionMoneyformat($balancedprice)];
        }

        return $this->render('index', [
                    'details' => $finalarr,
                    'makes' => $makes,
                    'head' => $head,
                    'sizes' => $sizes,
                    'labels' => $labelsone,
                    'values' => $valuesone,
                    'graphs' => $finalgraph,
                    'graphsce' => $finalgraphce
        ]);
    }

    public function actionGetmakedetails() {
        $user = Yii::$app->user->identity;
        $make = @$_REQUEST['make'];
        $type = @$_REQUEST['product'];
        $sizeval = @$_REQUEST['sizeval'];
        $command = @$_REQUEST['command'];
        $searchtype = @$_REQUEST['type'];
        $fromdate = @$_REQUEST['fromdate'];
        $todate = @$_REQUEST['todate'];
        $finalarr = [];
        $makes = [];
        $iids = [];
        $tids = [];
        $archivetids = [];
        $lighttids = [];
        $arcitems = [];
        $fullitems = [];
        $arciids = [];
        $fulliids = [];
        $cquantity = 0;
        $caquantity = 0;

        if (isset($command) && $command != '' && $command != 14) {
            if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $lighttenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $archivetenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.is_archived' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
            } else {
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make])->all();
                $lighttenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->all();
                $archivetenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.is_archived' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->all();
            }
        } else {
            if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $type])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $lighttenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $archivetenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.is_archived' => 1, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
            } else {
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $type])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make])->all();
                $lighttenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $type])->all();
                $archivetenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.is_archived' => 1, 'items.tenderfour' => $type])->all();
            }
        }

        //value
        $tids = [];
        $iids = [];
        $iidsone = [];
        $iidstwo = [];
        $iidsthree = [];
        $iidsfour = [];
        $iidsfive = [];
        $iidssix = [];
        $epricemake = 0;

        if (isset($command) && $command != '' && $command != 14) {

            if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                //Approved
                //Lt
                $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                //Ht
                $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $command, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $command, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();

                //Archived
                //Lt
                $itemsonearc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $itemstwoarc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $itemsthreearc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $itemsfourarc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                //Ht
                $itemsfivearc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $command, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $itemssixarc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $command, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
            } else {
                //Approved
                //Lt
                $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
                //Ht
                $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $command, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'tenders.command' => $command, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();

                //Archived
                //Lt
                $itemsonearc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                $itemstwoarc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                $itemsthreearc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                $itemsfourarc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $command, 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
                //Ht
                $itemsfivearc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $command, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                $itemssixarc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'tenders.command' => $command, 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
            }
        } else {
            if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                //Lt
                $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                //Ht
                $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
            } else {
                //Lt
                $itemsone = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                $itemstwo = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                $itemsthree = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                $itemsfour = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
                //Ht
                $itemsfive = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                $itemssix = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.status' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
            }

            if ($searchtype == 1) {

                if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                    //Lt
                    $itemsonearc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                    $itemstwoarc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                    $itemsthreearc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                    $itemsfourarc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                    //Ht
                    $itemsfivearc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                    $itemssixarc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                } else {
                    //Lt
                    $itemsonearc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemstwoarc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '2'])->all();
                    $itemsthreearc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                    $itemsfourarc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 1, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '2'])->all();
                    //Ht
                    $itemsfivearc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '1', 'items.tendersix' => '1'])->all();
                    $itemssixarc = \common\models\Item::find()->leftJoin('tenders', 'items.tender_id = tenders.id')->where(['tenders.is_archived' => '1', 'items.tenderthree' => 2, 'items.tenderfour' => @$type, 'items.tenderfive' => '2', 'items.tendersix' => '1'])->all();
                }
            }
        }

        if (isset($make)) {
            if (isset($itemsone)) {
                foreach ($itemsone as $_item) {
                    $iidsone[] = $_item->id;
                }
            }

            $itemdetailone = \common\models\ItemDetails::find()->where(['item_id' => $iidsone])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();
            if (isset($itemdetailone)) {
                foreach ($itemdetailone as $_detail) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    if (@$price->price) {
                        $epricemake += ($_detail->quantity * $price->price);
                    }
                }
            }

            if (isset($itemstwo)) {
                foreach ($itemstwo as $_item) {
                    $iidstwo[] = $_item->id;
                }
            }

            $itemdetailtwo = \common\models\ItemDetails::find()->where(['item_id' => $iidstwo])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();

            if (isset($itemdetailtwo)) {
                foreach ($itemdetailtwo as $_detail) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    if (@$price->price) {
                        $epricemake += ($_detail->quantity * $price->price);
                    }
                }
            }

            if (isset($itemsthree)) {
                foreach ($itemsthree as $_item) {
                    $iidsthree[] = $_item->id;
                }
            }

            $itemdetailthree = \common\models\ItemDetails::find()->where(['item_id' => $iidsthree])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();

            if (isset($itemdetailthree)) {
                foreach ($itemdetailthree as $_detail) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    if (@$price->price) {
                        $epricemake += ($_detail->quantity * $price->price);
                    }
                }
            }

            if (isset($itemsfour)) {
                foreach ($itemsfour as $_item) {
                    $iidsfour[] = $_item->id;
                }
            }

            $itemdetailfour = \common\models\ItemDetails::find()->where(['item_id' => $iidsfour])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();

            if (isset($itemdetailfour)) {
                foreach ($itemdetailfour as $_detail) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    if (@$price->price) {
                        $epricemake += ($_detail->quantity * $price->price);
                    }
                }
            }

            if (isset($itemsfive)) {
                foreach ($itemsfive as $_item) {
                    $iidsfive[] = $_item->id;
                }
            }

            $itemdetailfive = \common\models\ItemDetails::find()->where(['item_id' => $iidsfive])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();

            if (isset($itemdetailfive)) {
                foreach ($itemdetailfive as $_detail) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    if (@$price->price) {
                        $epricemake += ($_detail->quantity * $price->price);
                    }
                }
            }

            if (isset($itemssix)) {
                foreach ($itemssix as $_item) {
                    $iidssix[] = $_item->id;
                }
            }

            $itemdetailsix = \common\models\ItemDetails::find()->where(['item_id' => $iidssix])->andWhere('find_in_set(:key2, make)', [':key2' => $make])->all();

            if (isset($itemdetailsix)) {
                foreach ($itemdetailsix as $_detail) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    if (@$price->price) {
                        $epricemake += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        $iidsone = [];
        $iidstwo = [];
        $iidsthree = [];
        $iidsfour = [];
        $iidsfive = [];
        $iidssix = [];
        $epriceone = 0;

        if (isset($itemsone)) {
            foreach ($itemsone as $_item) {
                $iidsone[] = $_item->id;
            }
        }

        $itemdetailone = \common\models\ItemDetails::find()->where(['item_id' => $iidsone])->all();
        if (isset($itemdetailone)) {
            foreach ($itemdetailone as $_detail) {
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
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
                $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                if (@$price->price) {
                    $epriceone += ($_detail->quantity * $price->price);
                }
            }
        }

        $iidsone = [];
        $iidstwo = [];
        $iidsthree = [];
        $iidsfour = [];
        $iidsfive = [];
        $iidssix = [];
        $epricearc = 0;

        if ($searchtype == 1) {

            //Archived
            if (isset($itemsonearc)) {
                foreach ($itemsonearc as $_item) {
                    $iidsone[] = $_item->id;
                }
            }
            $itemdetailone = \common\models\ItemDetails::find()->where(['item_id' => $iidsone])->all();
            if (isset($itemdetailone)) {
                foreach ($itemdetailone as $_detail) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    if (@$price->price) {
                        $epricearc += ($_detail->quantity * $price->price);
                    }
                }
            }

            if (isset($itemstwoarc)) {
                foreach ($itemstwoarc as $_item) {
                    $iidstwo[] = $_item->id;
                }
            }

            $itemdetailtwo = \common\models\ItemDetails::find()->where(['item_id' => $iidstwo])->all();

            if (isset($itemdetailtwo)) {
                foreach ($itemdetailtwo as $_detail) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    if (@$price->price) {
                        $epricearc += ($_detail->quantity * $price->price);
                    }
                }
            }

            if (isset($itemsthreearc)) {
                foreach ($itemsthreearc as $_item) {
                    $iidsthree[] = $_item->id;
                }
            }

            $itemdetailthree = \common\models\ItemDetails::find()->where(['item_id' => $iidsthree])->all();

            if (isset($itemdetailthree)) {
                foreach ($itemdetailthree as $_detail) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    if (@$price->price) {
                        $epricearc += ($_detail->quantity * $price->price);
                    }
                }
            }

            if (isset($itemsfourarc)) {
                foreach ($itemsfourarc as $_item) {
                    $iidsfour[] = $_item->id;
                }
            }

            $itemdetailfour = \common\models\ItemDetails::find()->where(['item_id' => $iidsfour])->all();

            if (isset($itemdetailfour)) {
                foreach ($itemdetailfour as $_detail) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    if (@$price->price) {
                        $epricearc += ($_detail->quantity * $price->price);
                    }
                }
            }

            if (isset($itemsfivearc)) {
                foreach ($itemsfivearc as $_item) {
                    $iidsfive[] = $_item->id;
                }
            }

            $itemdetailfive = \common\models\ItemDetails::find()->where(['item_id' => $iidsfive])->all();

            if (isset($itemdetailfive)) {
                foreach ($itemdetailfive as $_detail) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    if (@$price->price) {
                        $epricearc += ($_detail->quantity * $price->price);
                    }
                }
            }

            if (isset($itemssixarc)) {
                foreach ($itemssixarc as $_item) {
                    $iidssix[] = $_item->id;
                }
            }

            $itemdetailsix = \common\models\ItemDetails::find()->where(['item_id' => $iidssix])->all();

            if (isset($itemdetailsix)) {
                foreach ($itemdetailsix as $_detail) {
                    $price = \common\models\Prices::find()->where(['mtypefour' => $_detail->description, 'mtypefive' => $_detail->core])->one();
                    if (@$price->price) {
                        $epricearc += ($_detail->quantity * $price->price);
                    }
                }
            }
        }

        $mnamegraph = '';
        if (isset($make) && $make != '') {
            $makename = \common\models\Make::find()->where(['id' => $make])->one();
            $mnamegraph = $makename->make;
        }
        $finalgraph[] = ['Command', 'Approved Tenders', $mnamegraph];
        //commands
        for ($i = 1; $i <= 13; $i++) {
            $tidsc = [];
            $iidsc = [];
            if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                $tenderscommand = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'tenders.command' => $i, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
            } else {
                $tenderscommand = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'tenders.command' => $i, 'items.tenderfour' => $type])->all();
            }

            $commandname = $this->actionGetcommandgraph($i);

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
                    $graphonequantity += $_idetail->quantity;
                }
            }

            $graphthreequantity = 0;
            $idetails = \common\models\ItemDetails::find()->where(['item_id' => $iidsc])->all();
            if (isset($idetails) && count($idetails)) {
                foreach ($idetails as $_idetail) {
                    $allmakes = explode(',', $_idetail->make);
                    if (in_array($make, $allmakes)) {
                        $graphthreequantity += $_idetail->quantity;
                    }
                }
            }

            $finalgraph[] = [$commandname, $graphonequantity, $graphthreequantity];
        }

        if ($mnamegraph != '') {
            $finalgraphce[] = ['Cheif Engineers', 'Approved Tenders', $mnamegraph];
        } else {
            $finalgraphce[] = ['Cheif Engineers', 'Approved Tenders'];
        }

        if (isset($command) && $command == 14) {
            $cengineers = \common\models\Cengineer::find()->where(['command' => [6, 7, 8, 9, 10, 11]])->all();
        } else {
            $cengineers = \common\models\Cengineer::find()->where(['command' => $command])->all();
        }


        //commands
        if (isset($cengineers) && count($cengineers)) {
            foreach ($cengineers as $_cengineer) {
                $tidsc = [];
                $iidsc = [];
                $archivetidsc = [];
                if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                    $tenderscommand = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'tenders.cengineer' => $_cengineer->cid, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                } else {
                    $tenderscommand = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->where(['tenders.status' => 1, 'tenders.cengineer' => $_cengineer->cid, 'items.tenderfour' => $type])->all();
                }

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
                        $graphonequantity += $_idetail->quantity;
                    }
                }

                if ($mnamegraph != '') {
                    $graphthreequantity = 0;
                    $idetails = \common\models\ItemDetails::find()->where(['item_id' => $iidsc])->all();
                    if (isset($idetails) && count($idetails)) {
                        foreach ($idetails as $_idetail) {
                            $allmakes = explode(',', $_idetail->make);
                            if (in_array($make, $allmakes)) {
                                $graphthreequantity += $_idetail->quantity;
                            }
                        }
                    }
                }

                if ($mnamegraph != '') {
                    $finalgraphce[] = [$cengineer, $graphonequantity, $graphthreequantity];
                } else {
                    $finalgraphce[] = [$cengineer, $graphonequantity];
                }
            }
        }

        if (isset($type)) {
            if (isset($tenders) && count($tenders)) {
                foreach ($tenders as $_tender) {
                    $tids[] = $_tender->id;
                }
            }
            if (isset($lighttenders) && count($lighttenders)) {
                foreach ($lighttenders as $_tender) {
                    $lighttids[] = $_tender->id;
                }
            }


            $items = \common\models\Item::find()->where(['tender_id' => $tids, 'tenderfour' => $type])->all();
            if (isset($items) && count($items)) {
                foreach ($items as $_item) {
                    $iids[] = $_item->id;
                }
            }

            $allquantity = 0;
            $allmakes = [];
            $idetails = \common\models\ItemDetails::find()->where(['item_id' => $iids])->all();
            if (isset($idetails) && count($idetails)) {
                foreach ($idetails as $_idetail) {
                    $allmakes = explode(',', $_idetail->make);
                    if (in_array($make, $allmakes)) {
                        $allquantity += $_idetail->quantity;
                    }
                }
            }

            if (isset($archivetenders) && count($archivetenders)) {
                foreach ($archivetenders as $_tender) {
                    $archivetids[] = $_tender->id;
                }
            }

            if (isset($command) && $command != '') {
                $arcitems = \common\models\Item::find()->where(['tender_id' => $archivetids, 'tenderfour' => $type])->all();
                $fullitems = \common\models\Item::find()->where(['tender_id' => $lighttids, 'tenderfour' => $type])->all();

                if (isset($arcitems) && count($arcitems)) {
                    foreach ($arcitems as $_item) {
                        $arciids[] = $_item->id;
                    }
                }

                if (isset($fullitems) && count($fullitems)) {
                    foreach ($fullitems as $_item) {
                        $fulliids[] = $_item->id;
                    }
                }

                $commandfullquantity = [];
                $idetails = \common\models\ItemDetails::find()->where(['item_id' => $fulliids])->all();
                if (isset($idetails) && count($idetails)) {
                    foreach ($idetails as $_idetail) {
                        if ($type == 1) {
                            $commandfullquantity[] = ['quantity' => $_idetail->quantity];
                        } elseif ($type == 2) {
                            $commandfullquantity[] = ['quantity' => $_idetail->quantity];
                        }
                    }
                }

                $commandarcquantity = [];
                $idetails = \common\models\ItemDetails::find()->where(['item_id' => $arciids])->all();
                if (isset($idetails) && count($idetails)) {
                    foreach ($idetails as $_idetail) {
                        if ($type == 1) {
                            $commandarcquantity[] = ['quantity' => $_idetail->quantity];
                        } elseif ($type == 2) {
                            $commandarcquantity[] = ['quantity' => $_idetail->quantity];
                        }
                    }
                }

                $cquantity = 0;
                if (isset($commandfullquantity) && count($commandfullquantity)) {
                    foreach ($commandfullquantity as $_quantity) {
                        $cquantity += $_quantity['quantity'];
                    }
                }

                $caquantity = 0;
                if (isset($commandarcquantity) && count($commandarcquantity)) {
                    foreach ($commandarcquantity as $_quantity) {
                        $caquantity += $_quantity['quantity'];
                    }
                }
            }

            /* $ltitems = \common\models\Item::find()->where(['tender_id' => $archivetids, 'tenderthree' => 1, 'tenderfour' => $type])->all();
              $cpitems = \common\models\Item::find()->where(['tender_id' => $archivetids, 'tenderthree' => 1, 'tenderfive' => 1, 'tenderfour' => $type])->all();
              $aritems = \common\models\Item::find()->where(['tender_id' => $archivetids, 'tenderthree' => 1, 'tenderfive' => 1, 'tendersix' => 1, 'tenderfour' => $type])->all();
              $allitems = \common\models\Item::find()->where(['tender_id' => $archivetids, 'tenderfour' => $type])->all();
              $aallitems = \common\models\Item::find()->where(['tender_id' => $lighttids, 'tenderfour' => $type])->all();

              $archivelt = [];
              $archivecp = [];
              $archivear = [];
              $allsizes = [];
              $aallsizes = [];


              if (isset($ltitems) && count($ltitems)) {
              foreach ($ltitems as $_item) {
              $archivelt[] = $_item->id;
              }
              }
              if (isset($cpitems) && count($cpitems)) {
              foreach ($cpitems as $_item) {
              $archivecp[] = $_item->id;
              }
              }
              if (isset($aritems) && count($aritems)) {
              foreach ($aritems as $_item) {
              $archivear[] = $_item->id;
              }
              }
              if (isset($allitems) && count($allitems)) {
              foreach ($allitems as $_item) {
              $allsizes[] = $_item->id;
              }
              }
              if (isset($aallitems) && count($aallitems)) {
              foreach ($aallitems as $_item) {
              $aallsizes[] = $_item->id;
              }
              }


              $quantityltwith = 0;
              $quantityltwithout = 0;

              $idetails = \common\models\ItemDetails::find()->where(['item_id' => $archivelt])->all();
              if (isset($idetails) && count($idetails)) {
              foreach ($idetails as $_idetail) {
              $allmakes = [];
              $allmakes = explode(',', $_idetail->make);
              if (in_array($make, $allmakes)) {
              $quantityltwith += $_idetail->quantity;
              } else {
              $quantityltwithout += $_idetail->quantity;
              }
              }
              }


              $quantitycpwith = 0;
              $quantitycpwithout = 0;

              $idetails = \common\models\ItemDetails::find()->where(['item_id' => $archivecp])->all();
              if (isset($idetails) && count($idetails)) {
              foreach ($idetails as $_idetail) {
              $allmakes = [];
              $allmakes = explode(',', $_idetail->make);
              if (in_array($make, $allmakes)) {
              $quantitycpwith += $_idetail->quantity;
              } else {
              $quantitycpwithout += $_idetail->quantity;
              }
              }
              }


              $quantityarwith = 0;
              $quantityarwithout = 0;
              $idetails = \common\models\ItemDetails::find()->where(['item_id' => $archivear])->all();
              if (isset($idetails) && count($idetails)) {
              foreach ($idetails as $_idetail) {
              $allmakes = [];
              $allmakes = explode(',', $_idetail->make);
              if (in_array($make, $allmakes)) {
              $quantityarwith += $_idetail->quantity;
              } else {
              $quantityarwithout += $_idetail->quantity;
              }
              }
              }


              $quantityallwith = 0;
              $quantityallwithout = 0;
              $idetails = \common\models\ItemDetails::find()->where(['item_id' => $allsizes])->all();
              if (isset($idetails) && count($idetails)) {
              foreach ($idetails as $_idetail) {
              $allmakes = [];
              $allmakes = explode(',', $_idetail->make);
              if (in_array($make, $allmakes)) {
              if ($_idetail->description == $sizeval) {
              $quantityallwith += $_idetail->quantity;
              }
              } else {
              if ($_idetail->description == $sizeval) {
              $quantityallwithout += $_idetail->quantity;
              }
              }
              }
              } */

            $allitems = \common\models\Item::find()->where(['tender_id' => $archivetids, 'tenderfour' => $type])->all();
            $aallitems = \common\models\Item::find()->where(['tender_id' => $lighttids, 'tenderfour' => $type])->all();

            $allsizes = [];
            $aallsizes = [];

            $allsizes = [];
            if (isset($allitems) && count($allitems)) {
                foreach ($allitems as $_item) {
                    $allsizes[] = $_item->id;
                }
            }

            $aallsizes = [];
            if (isset($aallitems) && count($aallitems)) {
                foreach ($aallitems as $_item) {
                    $aallsizes[] = $_item->id;
                }
            }

            $quantitytotallight = 0;
            $quantitywithlight = 0;
            $quantitywithoutlight = 0;
            $idetails = \common\models\ItemDetails::find()->where(['item_id' => $allsizes])->all();

            if (isset($idetails) && count($idetails)) {
                foreach ($idetails as $_idetail) {
                    $allmakes = [];
                    $allmakes = explode(',', $_idetail->make);
                    if (in_array($make, $allmakes)) {
                        if ($_idetail->typefitting == 4) {
                            $quantitywithlight += $_idetail->quantity;
                        }
                    } else {
                        if ($_idetail->typefitting == 4) {
                            $quantitywithoutlight += $_idetail->quantity;
                        }
                    }
                }
            }

            $aquantitytotallight = 0;
            $aquantitywithlight = 0;
            $aquantitywithoutlight = 0;
            $idetails = \common\models\ItemDetails::find()->where(['item_id' => $aallsizes])->all();

            if (isset($idetails) && count($idetails)) {
                foreach ($idetails as $_idetail) {
                    $allmakes = [];
                    $allmakes = explode(',', $_idetail->make);
                    if (in_array($make, $allmakes)) {
                        if ($_idetail->typefitting == 4) {
                            $aquantitywithlight += $_idetail->quantity;
                        }
                    } else {
                        if ($_idetail->typefitting == 4) {
                            $aquantitywithoutlight += $_idetail->quantity;
                        }
                    }
                }
            }



            /* $quantitytotalclight = 0;
              $quantitywithclight = 0;
              $quantitywithoutclight = 0;
              $idetails = \common\models\ItemDetails::find()->where(['item_id' => $allsizes])->all();

              if (isset($idetails) && count($idetails)) {
              foreach ($idetails as $_idetail) {
              $allmakes = [];
              $allmakes = explode(',', $_idetail->make);
              if (in_array($make, $allmakes)) {
              if ($_idetail->capacityfitting == 12) {
              $quantitywithclight += $_idetail->quantity;
              }
              } else {
              if ($_idetail->capacityfitting == 12) {
              $quantitywithoutclight += $_idetail->quantity;
              }
              }
              }
              } */
            //echo $quantitywithoutlight;


            if ($type == 1) {
                $unit = 'RM';
            } else {
                $unit = 'NOS';
            }
            $getsizes = [];
            $gettlights = [];
            $getclights = [];
            /* $sizes = \common\models\Size::find()->where(['mtypetwo' => 1, 'mtypethree' => 1])->orderBy(['size' => SORT_ASC])->all();
              if (isset($sizes) && count($sizes)) {
              foreach ($sizes as $_size) {
              $getsizes[$_size->id] = $_size->size;
              }
              } */
            $typelight = \common\models\Fitting::find()->where(['type' => 1, 'status' => 1])->orderBy(['text' => SORT_ASC])->all();
            //$capacitylight = \common\models\Fitting::find()->where(['type' => 2])->orderBy(['text' => SORT_ASC])->all();

            if (isset($typelight) && count($typelight)) {
                foreach ($typelight as $_tlight) {
                    $gettlights[$_tlight->id] = $_tlight->text;
                }
            }

            /* if (isset($capacitylight) && count($capacitylight)) {
              foreach ($capacitylight as $_clight) {
              $getclights[$_clight->id] = $_clight->text;
              }
              } */
            $mname = '';
            if (isset($make) && $make != '') {
                $makename = \common\models\Make::find()->where(['id' => $make])->one();
                $quantities['headtwo'] = 'Without ' . $makename->make . '';
                $quantities['headthree'] = 'With ' . $makename->make . '';
                $mname = $makename->make;
            }

            /* $quantities['archivedlt'] = $quantityltwith + $quantityltwithout . ' ' . $unit;
              $quantities['archivedcp'] = $quantitycpwith + $quantitycpwithout . ' ' . $unit;
              $quantities['archivedar'] = $quantityarwith + $quantityarwithout . ' ' . $unit;
              $quantities['archivedsize'] = $quantityallwith + $quantityallwithout . ' ' . $unit; */
            $quantities['headone'] = 'Archived';
            $quantities['headfour'] = 'Approved';

            /* $quantities['withlt'] = $quantityltwith . ' ' . $unit;
              $quantities['withcp'] = $quantitycpwith . ' ' . $unit;
              $quantities['withar'] = $quantityarwith . ' ' . $unit;
              $quantities['withsize'] = $quantityallwith . ' ' . $unit;


              $quantities['withoutlt'] = $quantityltwithout . ' ' . $unit;
              $quantities['withoutcp'] = $quantitycpwithout . ' ' . $unit;
              $quantities['withoutar'] = $quantityarwithout . ' ' . $unit;
              $quantities['withoutsize'] = $quantityallwithout . ' ' . $unit; */
            $quantities['atotallight'] = $aquantitywithlight + $aquantitywithoutlight . ' ' . $unit;
            $quantities['totallight'] = $quantitywithlight + $quantitywithoutlight . ' ' . $unit;
            $quantities['withlight'] = $quantitywithlight . ' ' . $unit;
            $quantities['withoutlight'] = $quantitywithoutlight . ' ' . $unit;
            /* $quantities['totalclight'] = $quantitywithclight + $quantitywithoutclight . ' ' . $unit;
              $quantities['withclight'] = $quantitywithclight . ' ' . $unit;
              $quantities['withoutclight'] = $quantitywithoutclight . ' ' . $unit; */


            $balanced = (count($lighttenders) - count($archivetenders));
            $balancedq = ($cquantity - $caquantity);

            $finalarr['total'] = count($tenders);
            $finalarr['quantity'] = $allquantity;
            $finalarr['value'] = $this->actionMoneyformat($epricemake);
            $finalarr['aptenderstotal'] = count($lighttenders);
            $finalarr['artenderstotal'] = count($archivetenders);
            $finalarr['bltenderstotal'] = $balanced;
            $finalarr['aptendersquantity'] = $cquantity;
            $finalarr['artendersquantity'] = $caquantity;
            $finalarr['bltendersquantity'] = $balancedq;
            $finalarr['aptendersprice'] = $this->actionMoneyformat($epriceone);
            $finalarr['artendersprice'] = $this->actionMoneyformat($epricearc);
            $finalarr['bltendersprice'] = $this->actionMoneyformat(($epriceone - $epricearc));
            $finalarr['artenders'] = $caquantity;

            $othersone = ($cquantity - $allquantity);

            $labelsone = ['OTHERS', $mname];
            $valuesone = [$othersone, $allquantity];

            /* $labels = ['Without ' . $mname . '', 'With ' . $mname . ''];
              $valuestwo = [$quantityltwithout, $quantityltwith];

              $valuesthree = [$quantitycpwithout, $quantitycpwith];

              $valuesfour = [$quantityarwithout, $quantityarwith];

              $valuesfive = [$quantityallwithout, $quantityallwith];

              $valuessix = [$quantitywithoutlight, $quantitywithlight]; */


            echo json_encode(['first' => $finalarr, 'second' => $quantities, 'sizes' => $getsizes, 'tlights' => $gettlights, 'clights' => $getclights, 'makename' => $mname, 'labelsone' => $labelsone, 'valuesone' => $valuesone, 'graph' => $finalgraph, 'graphce' => $finalgraphce]);

            die();
        }
    }

    public function actionGetsingledata() {
        $user = Yii::$app->user->identity;
        $val = @$_REQUEST['val'];
        $valtype = @$_REQUEST['type'];
        $type = @$_REQUEST['product'];
        $make = @$_REQUEST['make'];
        $command = @$_REQUEST['command'];
        $typetwo = @$_REQUEST['typetwo'];
        $typethree = @$_REQUEST['typethree'];
        $typeone = @$_REQUEST['typeone'];
        $typefour = @$_REQUEST['typefour'];
        $fromdate = @$_REQUEST['fromdate'];
        $todate = @$_REQUEST['todate'];
        $finalarr = [];
        $makes = [];
        $iids = [];
        $tids = [];
        $archivetids = [];
        $alltids = [];
        $sizes = [];
        $getsizes = [];

        if (isset($command) && $command != '' && $command != 14) {
            if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                $archivetenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.is_archived' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $alltenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
            } else {
                $archivetenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.is_archived' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->all();
                $alltenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->all();
            }
        } else {
            if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                $archivetenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.is_archived' => 1, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $alltenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
            } else {
                $archivetenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.is_archived' => 1, 'items.tenderfour' => $type])->all();
                $alltenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $type])->all();
            }
        }


        if (isset($type)) {
            if (isset($archivetenders) && count($archivetenders)) {
                foreach ($archivetenders as $_tender) {
                    $archivetids[] = $_tender->id;
                }
            }

            if (isset($alltenders) && count($alltenders)) {
                foreach ($alltenders as $_tender) {
                    $alltids[] = $_tender->id;
                }
            }

            if ($valtype == 1) {
                $items = \common\models\Item::find()->where(['tender_id' => $archivetids, 'tenderthree' => $val, 'tenderfour' => $type])->all();
                $aitems = \common\models\Item::find()->where(['tender_id' => $alltids, 'tenderthree' => $val, 'tenderfour' => $type])->all();
            } elseif ($valtype == 2) {
                $items = \common\models\Item::find()->where(['tender_id' => $archivetids, 'tenderthree' => $typeone, 'tenderfive' => $val, 'tenderfour' => $type])->all();
                $aitems = \common\models\Item::find()->where(['tender_id' => $alltids, 'tenderthree' => $typeone, 'tenderfive' => $val, 'tenderfour' => $type])->all();
                $sizes = \common\models\Size::find()->where(['mtypetwo' => $val, 'mtypethree' => $typethree])->all();
            } elseif ($valtype == 3) {
                $items = \common\models\Item::find()->where(['tender_id' => $archivetids, 'tenderthree' => $typeone, 'tenderfive' => $typetwo, 'tendersix' => $val, 'tenderfour' => $type])->all();
                $aitems = \common\models\Item::find()->where(['tender_id' => $alltids, 'tenderthree' => $typeone, 'tenderfive' => $typetwo, 'tendersix' => $val, 'tenderfour' => $type])->all();
                $sizes = \common\models\Size::find()->where(['mtypetwo' => $typetwo, 'mtypethree' => $val])->all();
            } else {
                $items = \common\models\Item::find()->where(['tender_id' => $archivetids, 'tenderthree' => $typeone, 'tenderfive' => $typetwo, 'tendersix' => $typethree, 'tenderfour' => $type])->all();
                $aitems = \common\models\Item::find()->where(['tender_id' => $alltids, 'tenderthree' => $typeone, 'tenderfive' => $typetwo, 'tendersix' => $typethree, 'tenderfour' => $type])->all();
            }

            if (isset($sizes) && count($sizes)) {
                foreach ($sizes as $_size) {
                    $getsizes[$_size->id] = $_size->size;
                }
            }

            $itemone = [];
            $aitemone = [];
            $allsizes = [];
            $aallsizes = [];

            $aquantityallwith = 0;
            $aquantityallwithout = 0;

            $quantityallwith = 0;
            $quantityallwithout = 0;

            $quantitywith = 0;
            $quantitywithout = 0;

            $aquantitywith = 0;
            $aquantitywithout = 0;
            if ($valtype == 1 || $valtype == 2 || $valtype == 3) {
                if (isset($items) && count($items)) {
                    foreach ($items as $_item) {
                        $itemone[] = $_item->id;
                    }
                }
                if (isset($aitems) && count($aitems)) {
                    foreach ($aitems as $_item) {
                        $aitemone[] = $_item->id;
                    }
                }


                $idetails = \common\models\ItemDetails::find()->where(['item_id' => $itemone])->all();
                if (isset($idetails) && count($idetails)) {
                    foreach ($idetails as $_idetail) {
                        $allmakes = [];
                        $allmakes = explode(',', $_idetail->make);
                        if (in_array($make, $allmakes)) {
                            $quantitywith += $_idetail->quantity;
                        } else {
                            $quantitywithout += $_idetail->quantity;
                        }
                    }
                }


                $idetails = \common\models\ItemDetails::find()->where(['item_id' => $aitemone])->all();
                if (isset($idetails) && count($idetails)) {
                    foreach ($idetails as $_idetail) {
                        $allmakes = [];
                        $allmakes = explode(',', $_idetail->make);
                        if (in_array($make, $allmakes)) {
                            $aquantitywith += $_idetail->quantity;
                        } else {
                            $aquantitywithout += $_idetail->quantity;
                        }
                    }
                }
            } else {
                if (isset($items) && count($items)) {
                    foreach ($items as $_item) {
                        $allsizes[] = $_item->id;
                    }
                }

                if (isset($aitems) && count($aitems)) {
                    foreach ($aitems as $_item) {
                        $aallsizes[] = $_item->id;
                    }
                }

                $idetails = \common\models\ItemDetails::find()->where(['item_id' => $allsizes])->all();
                if (isset($idetails) && count($idetails)) {
                    foreach ($idetails as $_idetail) {
                        $allmakes = [];
                        $allmakes = explode(',', $_idetail->make);
                        if (in_array($make, $allmakes)) {
                            if ($_idetail->description == $val) {
                                $quantityallwith += $_idetail->quantity;
                            }
                        } else {
                            if ($_idetail->description == $val) {
                                $quantityallwithout += $_idetail->quantity;
                            }
                        }
                    }
                }


                $idetails = \common\models\ItemDetails::find()->where(['item_id' => $aallsizes])->all();
                if (isset($idetails) && count($idetails)) {
                    foreach ($idetails as $_idetail) {
                        $allmakes = [];
                        $allmakes = explode(',', $_idetail->make);
                        if (in_array($make, $allmakes)) {
                            if ($_idetail->description == $val) {
                                $aquantityallwith += $_idetail->quantity;
                            }
                        } else {
                            if ($_idetail->description == $val) {
                                $aquantityallwithout += $_idetail->quantity;
                            }
                        }
                    }
                }
            }


            if ($type == 1) {
                $unit = 'RM';
            } else {
                $unit = 'NOS';
            }

            $makename = \common\models\Make::find()->where(['id' => $make])->one();

            $quantities['approved'] = $aquantitywith + $aquantitywithout . ' ' . $unit;
            $quantities['archived'] = $quantitywith + $quantitywithout . ' ' . $unit;
            $quantities['approvedsize'] = $aquantityallwith + $aquantityallwithout . ' ' . $unit;
            $quantities['archivedsize'] = $quantityallwith + $quantityallwithout . ' ' . $unit;

            $quantities['with'] = $aquantitywith . ' ' . $unit;
            $quantities['withsize'] = $aquantityallwith . ' ' . $unit;

            $quantities['without'] = $aquantitywithout . ' ' . $unit;
            $quantities['withoutsize'] = $aquantityallwithout . ' ' . $unit;

            $quantities['sizes'] = $getsizes;

            $labels = ['Without ' . $makename->make . '', 'With ' . $makename->make . ''];
            $values = [$aquantitywithout, $aquantitywith];

            $valuessize = [$aquantityallwithout, $aquantityallwith];

            echo json_encode(['quantities' => $quantities, 'labels' => $labels, 'values' => $values, 'valuessize' => $valuessize]);

            die();
        }
    }

    public function actionGetsinglelightdata() {
        $user = Yii::$app->user->identity;
        $val = @$_REQUEST['val'];
        $valtype = @$_REQUEST['type'];
        $type = @$_REQUEST['product'];
        $make = @$_REQUEST['make'];
        $command = @$_REQUEST['command'];
        $fromdate = @$_REQUEST['fromdate'];
        $todate = @$_REQUEST['todate'];
        $finalarr = [];
        $makes = [];
        $iids = [];
        $tids = [];
        $atids = [];
        $archivetids = [];
        $sizes = [];
        $getsizes = [];

        if (isset($command) && $command != '' && $command != 14) {
            if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                $arctenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.is_archived' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
            } else {
                $arctenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.is_archived' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->all();
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'tenders.command' => $command, 'items.tenderfour' => $type])->all();
            }
        } else {
            if (isset($fromdate) && isset($todate) && $fromdate != '' && $todate != '') {
                $arctenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.is_archived' => 1, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $type])->andWhere(['>=', 'tenders.bid_end_date', $fromdate])->andWhere(['<=', 'tenders.bid_end_date', $todate])->all();
            } else {
                $arctenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.is_archived' => 1, 'items.tenderfour' => $type])->all();
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $type])->all();
            }
        }

        if (isset($type)) {
            if (isset($tenders) && count($tenders)) {
                foreach ($tenders as $_tender) {
                    $tids[] = $_tender->id;
                }
            }

            if (isset($arctenders) && count($arctenders)) {
                foreach ($arctenders as $_tender) {
                    $atids[] = $_tender->id;
                }
            }

            $allitems = \common\models\Item::find()->where(['tender_id' => $tids, 'tenderfour' => $type])->all();
            $aallitems = \common\models\Item::find()->where(['tender_id' => $atids, 'tenderfour' => $type])->all();

            $allsizes = [];
            $aallsizes = [];


            if (isset($allitems) && count($allitems)) {
                foreach ($allitems as $_item) {
                    $allsizes[] = $_item->id;
                }
            }

            if (isset($aallitems) && count($aallitems)) {
                foreach ($aallitems as $_item) {
                    $aallsizes[] = $_item->id;
                }
            }

            $quantityallwith = 0;
            $quantityallwithout = 0;
            $idetails = \common\models\ItemDetails::find()->where(['item_id' => $allsizes])->all();
            if (isset($idetails) && count($idetails)) {
                foreach ($idetails as $_idetail) {
                    $allmakes = [];
                    $allmakes = explode(',', $_idetail->make);
                    if (in_array($make, $allmakes)) {
                        if ($_idetail->typefitting == $val) {
                            $quantityallwith += $_idetail->quantity;
                        }
                    } else {
                        if ($_idetail->typefitting == $val) {
                            $quantityallwithout += $_idetail->quantity;
                        }
                    }
                }
            }

            $aquantityallwith = 0;
            $aquantityallwithout = 0;
            $idetails = \common\models\ItemDetails::find()->where(['item_id' => $aallsizes])->all();
            if (isset($idetails) && count($idetails)) {
                foreach ($idetails as $_idetail) {
                    $allmakes = [];
                    $allmakes = explode(',', $_idetail->make);
                    if (in_array($make, $allmakes)) {
                        if ($_idetail->typefitting == $val) {
                            $aquantityallwith += $_idetail->quantity;
                        }
                    } else {
                        if ($_idetail->typefitting == $val) {
                            $aquantityallwithout += $_idetail->quantity;
                        }
                    }
                }
            }

            $quantityallcwith = 0;
            $quantityallcwithout = 0;
            $idetails = \common\models\ItemDetails::find()->where(['item_id' => $allsizes])->all();
            if (isset($idetails) && count($idetails)) {
                foreach ($idetails as $_idetail) {
                    $allmakes = [];
                    $allmakes = explode(',', $_idetail->make);
                    if (in_array($make, $allmakes)) {
                        if ($_idetail->capacityfitting == $val) {
                            $quantityallcwith += $_idetail->quantity;
                        }
                    } else {
                        if ($_idetail->capacityfitting == $val) {
                            $quantityallcwithout += $_idetail->quantity;
                        }
                    }
                }
            }


            if ($type == 1) {
                $unit = 'RM';
            } else {
                $unit = 'NOS';
            }

            $makename = \common\models\Make::find()->where(['id' => $make])->one();
            $quantities['archivedsize'] = $aquantityallwith + $aquantityallwithout . ' ' . $unit;
            $quantities['approvedsize'] = $quantityallwith + $quantityallwithout . ' ' . $unit;

            $quantities['withsize'] = $quantityallwith . ' ' . $unit;

            $quantities['withoutsize'] = $quantityallwithout . ' ' . $unit;

            $quantities['archivedcsize'] = $quantityallcwith + $quantityallcwithout . ' ' . $unit;

            $quantities['withcsize'] = $quantityallcwith . ' ' . $unit;

            $quantities['withoutcsize'] = $quantityallcwithout . ' ' . $unit;

            $quantities['labels'] = ['Without ' . $makename->make . '', 'With ' . $makename->make . ''];
            $quantities['graph'] = [$quantityallwithout, $quantityallwith];

            echo json_encode($quantities);

            die();
        }
    }

    public function actionUsers() {
        $user = Yii::$app->user->identity;
        $users = User::find()->where(['!=', 'group_id', '1'])->orderBy(['group_id' => SORT_ASC])->all();
        return $this->render('users', [
                    'users' => $users
        ]);
    }

    public function actionAddress($code, $type) {
        $user = Yii::$app->user->identity;
        $result = '';
        if ($type == 1) {
            $result = \common\models\Cities::find()->where(['id' => $code])->one();
        } else {
            $result = \common\models\States::find()->where(['id' => $code])->one();
        }
        return $result->name;
    }

    public function actionProducts($code, $type) {
        $user = Yii::$app->user->identity;
        $result = '';
        $fresult = '';
        $nresult = '';
        $data = [];
        if (isset($code)) {
            $data = explode(',', $code);
        }
        $datacount = count($data);
        if ($type == 1) {
            if (@$data) {
                $i = 1;
                foreach ($data as $_data) {
                    $result = \common\models\Make::find()->where(['id' => $_data, 'mtype' => $type])->one();
                    if ($datacount != $i) {
                        $fresult .= $result->make . ' , ';
                    } else {
                        $fresult .= $result->make;
                    }

                    $i++;
                }
            } else {
                $fresult = 'No Cables Selected';
            }
        } elseif ($type == 2) {
            if (@$data) {
                $i = 1;
                foreach ($data as $_data) {
                    $result = \common\models\Make::find()->where(['id' => $_data, 'mtype' => $type])->one();
                    if ($datacount != $i) {
                        $fresult .= $result->make . ' , ';
                    } else {
                        $fresult .= $result->make;
                    }

                    $i++;
                }
            } else {
                $fresult = 'No Lighting Selected';
            }
        } elseif ($type == 14) {
            if (@$data) {
                $i = 1;
                foreach ($data as $_data) {
                    $result = \common\models\Make::find()->where(['id' => $_data, 'mtype' => $type])->one();
                    if ($datacount != $i) {
                        $fresult .= $result->make . ' , ';
                    } else {
                        $fresult .= $result->make;
                    }

                    $i++;
                }
            } else {
                $fresult = 'No Cement Selected';
            }
        } elseif ($type == 15) {
            if (@$data) {
                $i = 1;
                foreach ($data as $_data) {
                    $result = \common\models\Make::find()->where(['id' => $_data, 'mtype' => $type])->one();
                    if ($datacount != $i) {
                        $fresult .= $result->make . ' , ';
                    } else {
                        $fresult .= $result->make;
                    }

                    $i++;
                }
            } else {
                $fresult = 'No Reinforement Steel Selected';
            }
        } elseif ($type == 16) {
            if (@$data) {
                $i = 1;
                foreach ($data as $_data) {
                    $result = \common\models\Make::find()->where(['id' => $_data, 'mtype' => $type])->one();
                    if ($datacount != $i) {
                        $fresult .= $result->make . ' , ';
                    } else {
                        $fresult .= $result->make;
                    }

                    $i++;
                }
            } else {
                $fresult = 'No Structural Steel Selected';
            }
        } else {
            if (@$data) {
                $i = 1;
                foreach ($data as $_data) {
                    $result = \common\models\Make::find()->where(['id' => $_data, 'mtype' => $type])->one();
                    if ($datacount != $i) {
                        $fresult .= $result->make . ' , ';
                    } else {
                        $fresult .= $result->make;
                    }

                    $i++;
                }
            } else {
                $fresult = 'No Non Structural Steel Selected';
            }
        }
        $nresult = rtrim($fresult, ",");

        return $nresult;
    }

    public function actionDealers() {
        $user = Yii::$app->user->identity;
        $clients = \common\models\Clients::find()->where(['type' => 3])->orderBy(['id' => SORT_DESC])->all();
        return $this->render('dealers', [
                    'clients' => $clients
        ]);
    }

    public function actionContractors() {
        $user = Yii::$app->user->identity;
        $clients = \common\models\Clients::find()->where(['type' => 2])->orderBy(['id' => SORT_DESC])->all();
        return $this->render('contractors', [
                    'clients' => $clients
        ]);
    }

    public function actionManufacturers() {
        $user = Yii::$app->user->identity;
        $clients = \common\models\Clients::find()->where(['type' => 1])->orderBy(['id' => SORT_DESC])->all();
        return $this->render('manufacturers', [
                    'clients' => $clients
        ]);
    }

    public function actionChangeStatus() {
        $id = @$_GET['id'];
        $user = User::find()->where(['UserId' => $id])->one();
        if ($user->status == 10) {
            $user->status = 0;
        } else {
            $user->status = 10;
        }
        $user->save();
        Yii::$app->session->setFlash('success', "Status successfully changed");
        return $this->redirect(array('site/users'));
    }

    public function actionOnHold() {
        $id = @$_REQUEST['value'];
        $tender = \common\models\Tender::find()->where(['id' => $id])->one();
        if ($tender->on_hold == 1) {
            $hold = '';
        } else {
            $hold = 1;
        }
        $data = ['on_hold' => $hold];
        $querydata = \Yii::$app
                ->db
                ->createCommand()
                ->update('tenders', $data, 'id = ' . $tender->id . '')
                ->execute();
        if ($querydata) {
            echo json_encode(['status' => 1, 'hold' => $hold]);
        } else {
            echo json_encode(['status' => 0, 'hold' => $hold]);
        }
    }

    public function actionChangeStatusClient() {
        $id = @$_GET['id'];
        $user = \common\models\Clients::find()->where(['id' => $id])->one();
        if ($user->status == 1) {
            $user->status = 0;
        } else {
            $user->status = 1;
        }
        $user->save();
        Yii::$app->session->setFlash('success', "Status successfully changed");
        if ($user['type'] == 2) {
            return $this->redirect(array('site/contractors'));
        } elseif ($user['type'] == 3) {
            return $this->redirect(array('site/dealers'));
        } else {
            return $this->redirect(array('site/manufacturers'));
        }
    }

    public function actionEditClient() {

        $user = Yii::$app->user->identity;
        $id = @$_GET['id'];

        if (isset($_POST['submit'])) {

            if ($_POST['id']) {
                $client = \common\models\Clients::find()->where(['id' => $_POST['id']])->one();
                $data = $_POST;
                $client->firm = $data['firm'];
                $client->address = $data['address'];
                $client->state = $data['state'];
                if (isset($data['contracttype'])) {
                    $client->contracttype = @$data['contracttype'];
                }
                $client->city = $data['city'];
                $client->pcode = $data['pcode'];
                $client->cperson = $data['cperson'];
                $client->cnumber = $data['cnumber'];
                $client->phone = $data['phone'];
                $client->cemail = $data['cemail'];
                if (isset($data['cables']) && count($data['cables'])) {
                    $client->cables = implode(',', @$data['cables']);
                }
                if (isset($data['lighting']) && count($data['lighting'])) {
                    $client->lighting = implode(',', @$data['lighting']);
                }
                if (isset($data['cement']) && count($data['cement'])) {
                    $client->cements = implode(',', @$data['cement']);
                }
                if (isset($data['rsteel']) && count($data['rsteel'])) {
                    $client->rsteel = implode(',', @$data['rsteel']);
                }
                if (isset($data['ssteel']) && count($data['ssteel'])) {
                    $client->ssteel = implode(',', @$data['ssteel']);
                }
                if (isset($data['nsteel']) && count($data['nsteel'])) {
                    $client->nsteel = implode(',', @$data['nsteel']);
                }

                if ($client->save()) {
                    Yii::$app->session->setFlash('success', "Client successfully updated");
                }
            }

            if ($data['type'] == 2) {
                return $this->redirect(array('site/contractors'));
            } elseif ($data['type'] == 3) {
                return $this->redirect(array('site/dealers'));
            } else {
                return $this->redirect(array('site/manufacturers'));
            }

            die();
        } else {
            if ($id) {
                $client = \common\models\Clients::find()->where(['id' => $id])->one();
            } else {
                $client = [];
            }
            $states = \common\models\States::find()->where(['!=', 'name', ''])->andWhere(['country_id' => '101'])->all();
            $cities = \common\models\Cities::find()->Where(['state_id' => $client->state])->all();
            $cables = \common\models\Make::find()->where(['mtype' => 1])->andWhere(['status' => '1'])->all();
            $lights = \common\models\Make::find()->where(['mtype' => 2])->andWhere(['status' => '1'])->all();
            $cements = \common\models\Make::find()->where(['mtype' => 14])->andWhere(['status' => '1'])->all();
            $rsteel = \common\models\Make::find()->where(['mtype' => 15])->andWhere(['status' => '1'])->all();
            $ssteel = \common\models\Make::find()->where(['mtype' => 16])->andWhere(['status' => '1'])->all();
            $nsteel = \common\models\Make::find()->where(['mtype' => 17])->andWhere(['status' => '1'])->all();

            return $this->render('editclient', [
                        'client' => $client,
                        'states' => $states,
                        'cities' => $cities,
                        'cables' => $cables,
                        'lights' => $lights,
                        'cements' => $cements,
                        'rsteel' => $rsteel,
                        'ssteel' => $ssteel,
                        'nsteel' => $nsteel
            ]);
        }
    }

    public function actionGetcities() {
        $id = $_REQUEST['val'];
        $arr = [];
        $cities = \common\models\Cities::find()->Where(['state_id' => $id])->all();
        if (@$cities) {
            foreach ($cities as $city) {
                $arr[$city->id] = $city->name;
            }
        }
        echo json_encode(['cities' => $arr]);
        die();
    }

    public function actionUpcomingtenders() {
        $user = Yii::$app->user->identity;
        $idetails = \common\models\ItemDetails::find()->leftJoin('items', 'itemdetails.item_id = items.id')->orderBy(['itemdetails.id' => SORT_DESC])->all();
        $tenders = \common\models\Tender::find()->where(['>=', 'bid_end_date', date('d-m-Y')])->orderBy(['bid_end_date' => SORT_ASC])->all();
        return $this->render('upcomingtenders', [
                    'tenders' => $tenders,
                    'items' => $idetails
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

    public function actionEditUser() {
        $userid = @$_POST['id'];
        $allowed_image_extension = array(
            "png",
            "jpg",
            "jpeg"
        );
        if ($userid) {
            if (isset($_POST['password']) && $_POST['password'] != '') {
                $hashpass = \Yii::$app->security->generatePasswordHash($_POST['password']);
                $data = ['username' => $_POST['username'], 'group_id' => $_POST['group_id'], 'email' => $_POST['email'], 'password_hash' => $hashpass, 'password' => $_POST['password']];
            } else {
                $data = ['username' => $_POST['username'], 'group_id' => $_POST['group_id'], 'email' => $_POST['email']];
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
                Yii::$app->session->setFlash('success', "User has been updated");
            } else {
                Yii::$app->session->setFlash('error', "User has not been updated");
            }
            return $this->redirect(array('site/edit-user/' . $userid . ''));
        } else {
            if (@$_GET['id']) {
                $userdetail = User::find()->where(['UserId' => $_GET['id']])->one();
            } else {
                $userdetail = [];
            }
            return $this->render('edituser', [
                        'user' => $userdetail
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

        return $this->redirect(array('site/login'));
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
            $hashpass = \Yii::$app->security->generatePasswordHash($_POST['CreateUser']['password']);
            $model->username = $_POST['CreateUser']['username'];
            $model->password_hash = $hashpass;
            $model->password = $_POST['CreateUser']['password'];
            $model->email = $_POST['CreateUser']['Email'];
            $model->group_id = $_POST['CreateUser']['group_id'];
            $model->auth_key = Yii::$app->security->generateRandomString();
            $model->status = '10';
            $model->is_admin = '1';
            $model->created_at = time();
            $model->updated_at = time();

            $useremail = User::find()
                            ->where(['email' => $_POST['CreateUser']['Email']])->all();
            $username = User::find()
                            ->where(['username' => $_POST['CreateUser']['username']])->all();
            if ($useremail) {
                Yii::$app->session->setFlash('error', "Email is already registered!");
            } elseif ($username) {
                Yii::$app->session->setFlash('error', "Username is already registered!");
            } else {
                $project = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('tbluser', $model)
                        ->execute();
                if ($project) {
                    Yii::$app->session->setFlash('success', "User successfully registered!");
                }
            }

            return $this->redirect(array('users'));

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

    public function actionSearchtender() {
        $val = $_REQUEST['val'];
        $tenders = \common\models\Tender::find()->where(['like', 'tender_id', '%' . $val . '%', false])->orderBy(['id' => SORT_DESC])->all();
        $contractors = \common\models\Contractor::find()->orderBy(['firm' => SORT_ASC])->all();
        $pages = [];




        /* $tenders = (new \yii\db\Query())
          ->select('*')
          ->from('tenders')
          ->where(['like', 'tender_id', '%'.$val.'%',false])
          ->orderBy(['id' => SORT_DESC])
          ->indexBy('tender_id')
          ->all(); */


        echo $this->renderPartial('stenders', [
            'tenders' => $tenders,
            'contractors' => $contractors,
            'type' => 'All',
            'url' => 'tenders'
        ]);
        die();
    }

    public function actionTenders() {
        $val = @$_POST['sort'];
        $page = @$_REQUEST['page'];
        $filter = @$_GET['filter'];

        $tenders = \common\models\Tender::find()->orderBy(['id' => SORT_DESC]);
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

        //$tenders=[];
        $contractors = [];

        if ($val) {
            return $this->redirect(array('site/tenders?filter=' . $val . ''));
        } else {
            return $this->render('tenders', [
                        'tenders' => $models,
                        'contractors' => $contractors,
                        'pages' => $pages,
                        'total' => $countQuery->count(),
                        'type' => 'All',
                        'url' => 'tenders'
            ]);
        }
    }

    public function actionSearchtenders() {

        if (isset($_POST['download'])) {
            $authtype = $_POST['authtype'];
            if ($authtype == 1) {
                $make = $_POST['cables'];
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $authtype])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make])->orderBy(['tenders.id' => SORT_DESC])->all();
            } elseif ($authtype == 2) {
                $make = $_POST['lighting'];
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tenderfour' => $authtype])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make])->orderBy(['tenders.id' => SORT_DESC])->all();
            } elseif ($authtype == 3) {
                $make = $_POST['cement'];
                $authtype = 14;
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tendertwo' => $authtype])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make])->orderBy(['tenders.id' => SORT_DESC])->all();
            } elseif ($authtype == 4) {
                $make = $_POST['rsteel'];
                $authtype = 15;
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tendertwo' => $authtype])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make])->orderBy(['tenders.id' => SORT_DESC])->all();
            } elseif ($authtype == 5) {
                $make = $_POST['ssteel'];
                $authtype = 16;
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tendertwo' => $authtype])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make])->orderBy(['tenders.id' => SORT_DESC])->all();
            } else {
                $make = $_POST['nsteel'];
                $authtype = 17;
                $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.status' => 1, 'items.tendertwo' => $authtype])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $make])->orderBy(['tenders.id' => SORT_DESC])->all();
            }


            //$tenders=[];
            $contractors = [];

            $cables = \common\models\Make::find()->where(['mtype' => 1])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $lights = \common\models\Make::find()->where(['mtype' => 2])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $cements = \common\models\Make::find()->where(['mtype' => 14])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $rsteel = \common\models\Make::find()->where(['mtype' => 15])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $ssteel = \common\models\Make::find()->where(['mtype' => 16])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $nsteel = \common\models\Make::find()->where(['mtype' => 17])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            return $this->render('searchtenders', [
                        'tenders' => $tenders,
                        'contractors' => $contractors,
                        'type' => 'All',
                        'url' => 'tenders',
                        'cables' => $cables,
                        'lights' => $lights,
                        'cements' => $cements,
                        'rsteel' => $rsteel,
                        'ssteel' => $ssteel,
                        'nsteel' => $nsteel,
            ]);
        } else {
            $cables = \common\models\Make::find()->where(['mtype' => 1])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $lights = \common\models\Make::find()->where(['mtype' => 2])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $cements = \common\models\Make::find()->where(['mtype' => 14])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $rsteel = \common\models\Make::find()->where(['mtype' => 15])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $ssteel = \common\models\Make::find()->where(['mtype' => 16])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            $nsteel = \common\models\Make::find()->where(['mtype' => 17])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
            return $this->render('searchtenders', [
                        'type' => 'All',
                        'url' => 'searchtenders',
                        'cables' => $cables,
                        'lights' => $lights,
                        'cements' => $cements,
                        'rsteel' => $rsteel,
                        'ssteel' => $ssteel,
                        'nsteel' => $nsteel,
            ]);
        }
    }

    public function actionGettenders() {


        $params = $columns = $totalRecords = $data = array();
        $params = $_REQUEST;
        $where = $sqlTot = $sqlRec = "";

// check search value exist
        if (!empty($params['search']['value'])) {
            $where .= " WHERE ";
            $where .= " ( tender_id LIKE '" . $params['search']['value'] . "%' ";
//$where .=" OR employee_salary LIKE '".$params['search']['value']."%' ";
//$where .=" OR employee_age LIKE '".$params['search']['value']."%' )";
        }

// getting total number records without any search
        $sql = "SELECT * FROM `tenders` ";
        $sqlTot .= $sql;
        $sqlRec .= $sql;
//concatenate search sql if value exist
        if (isset($where) && $where != '') {

            $sqlTot .= $where;
            $sqlRec .= $where;
        }

        $sqlRec .= " ORDER BY id desc LIMIT " . $params['start'] . " ," . $params['length'] . " ";

        $connection = Yii::$app->getDb();
        $commandone = $connection->createCommand($sqlTot);
        $resultone = $commandone->queryAll();

        $totalRecords = count($resultone);

        $commandtwo = $connection->createCommand($sqlRec);
        $resulttwo = $commandtwo->queryAll();

        if (@$resulttwo) {
            foreach ($resulttwo as $_two) {
                $data[] = $_two;
            }
        }

        $json_data = array(
            "draw" => intval($params['draw']),
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalRecords),
            "data" => $data   // total data array
        );

        echo json_encode($json_data);  // send data as json format
        die();
    }

    public function actionTechnicaltenders() {

        $val = @$_POST['sort'];
        $page = @$_REQUEST['page'];
        $filter = @$_GET['filter'];

        $tenders = \common\models\Tender::find()->where(['technical_status' => 1, 'financial_status' => null, 'aoc_status' => null])->orderBy(['id' => SORT_DESC]);
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

//$tenders=[];
        $contractors = [];

        if ($val) {
            return $this->redirect(array('site/technicaltenders?filter=' . $val . ''));
        } else {
            return $this->render('tenders', [
                        'tenders' => $models,
                        'contractors' => $contractors,
                        'pages' => $pages,
                        'total' => $countQuery->count(),
                        'type' => 'Technical',
                        'url' => 'technicaltenders'
            ]);
        }
    }

    public function actionFinancialtenders() {

        $val = @$_POST['sort'];
        $page = @$_REQUEST['page'];
        $filter = @$_GET['filter'];

        $tenders = \common\models\Tender::find()->where(['financial_status' => 1, 'aoc_status' => null])->orderBy(['id' => SORT_DESC]);
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

//$tenders=[];
        $contractors = \common\models\Contractor::find()->orderBy(['firm' => SORT_ASC])->all();

        if ($val) {
            return $this->redirect(array('site/financialtenders?filter=' . $val . ''));
        } else {
            return $this->render('tenders', [
                        'tenders' => $models,
                        'contractors' => $contractors,
                        'pages' => $pages,
                        'total' => $countQuery->count(),
                        'type' => 'Financial',
                        'url' => 'financialtenders'
            ]);
        }
    }

    public function actionAoctenders() {

        $val = @$_POST['sort'];
        $page = @$_REQUEST['page'];
        $filter = @$_GET['filter'];

        $tenders = \common\models\Tender::find()->where(['aoc_status' => 1])->orderBy(['id' => SORT_DESC]);
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

//$tenders=[];
        $contractors = [];

        if ($val) {
            return $this->redirect(array('site/aoctenders?filter=' . $val . ''));
        } else {
            return $this->render('aoctenders', [
                        'tenders' => $models,
                        'contractors' => $contractors,
                        'pages' => $pages,
                        'total' => $countQuery->count(),
                        'type' => 'AOC',
                        'url' => 'aoctenders'
            ]);
        }
    }

    public function actionArchivetenders() {

        $val = @$_POST['sort'];
        $page = @$_REQUEST['page'];
        $filter = @$_GET['filter'];

        $tenders = \common\models\Tender::find()->where(['is_archived' => 1])->orderBy(['id' => SORT_DESC]);
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

//$tenders=[];
        $contractors = [];

        if ($val) {
            return $this->redirect(array('site/archivetenders?filter=' . $val . ''));
        } else {
            return $this->render('aoctenders', [
                        'tenders' => $models,
                        'contractors' => $contractors,
                        'pages' => $pages,
                        'total' => $countQuery->count(),
                        'type' => 'Archived',
                        'url' => 'archivetenders'
            ]);
        }
    }

    public function actionAocready() {

        $val = @$_POST['sort'];
        $page = @$_REQUEST['page'];
        $filter = @$_GET['filter'];

        $tenders = \common\models\Tender::find()->where(['on_hold' => null, 'aoc_status' => 1, 'is_archived' => null])->orderBy(['id' => SORT_DESC]);
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

//$tenders=[];
        $contractors = [];

        if ($val) {
            return $this->redirect(array('site/aocready?filter=' . $val . ''));
        } else {
            return $this->render('aoctenders', [
                        'tenders' => $models,
                        'contractors' => $contractors,
                        'pages' => $pages,
                        'total' => $countQuery->count(),
                        'type' => 'Aoc Ready',
                        'url' => 'aocready'
            ]);
        }
    }

    public function actionAochold() {

        $val = @$_POST['sort'];
        $page = @$_REQUEST['page'];
        $filter = @$_GET['filter'];

        $tenders = \common\models\Tender::find()->where(['on_hold' => 1, 'aoc_status' => 1, 'is_archived' => null])->orderBy(['id' => SORT_DESC]);
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

//$tenders=[];
        $contractors = [];

        if ($val) {
            return $this->redirect(array('site/aochold?filter=' . $val . ''));
        } else {
            return $this->render('aoctenders', [
                        'tenders' => $models,
                        'contractors' => $contractors,
                        'pages' => $pages,
                        'total' => $countQuery->count(),
                        'type' => 'Aoc OnHold',
                        'url' => 'aochold'
            ]);
        }
    }

    public function actionMovetoarchive() {

        if (isset($_POST['submit'])) {
            $val = @$_POST['sort'];
            $page = @$_REQUEST['page'];
            $filter = @$_GET['filter'];
            $fromdate = $_POST['fromdate'];
            $todate = $_POST['todate'];

            $tenders = \common\models\Tender::find()->where(['on_hold' => 1, 'aoc_status' => 1, 'is_archived' => null])->andWhere(['>=', 'bid_end_date', $fromdate])->andWhere(['<=', 'bid_end_date', $todate])->orderBy(['id' => SORT_DESC]);
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

//$tenders=[];
            $contractors = [];

            if ($val) {
                return $this->redirect(array('site/movetoarchive?filter=' . $val . ''));
            } else {
                return $this->render('movetoarchive', [
                            'tenders' => $models,
                            'contractors' => $contractors,
                            'pages' => $pages,
                            'total' => $countQuery->count(),
                            'type' => 'Aoc OnHold',
                            'url' => 'movetoarchive'
                ]);
            }
        } else {
            return $this->render('movetoarchive', [
                        'url' => 'movetoarchive'
            ]);
        }
    }

    public function actionUtenders() {

        $val = @$_POST['sort'];
        $page = @$_REQUEST['page'];
        $filter = @$_GET['filter'];

        $tenders = \common\models\Tender::find()->where(['status' => 0])->orderBy(['id' => SORT_DESC]);
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

//$tenders=[];
        $contractors = [];

        if ($val) {
            return $this->redirect(array('site/utenders?filter=' . $val . ''));
        } else {
            return $this->render('tenders', [
                        'tenders' => $models,
                        'contractors' => $contractors,
                        'pages' => $pages,
                        'total' => $countQuery->count(),
                        'type' => 'Unapproved',
                        'url' => 'utenders'
            ]);
        }
    }

    public function actionAtenders() {

        $val = @$_POST['sort'];
        $page = @$_REQUEST['page'];
        $filter = @$_GET['filter'];


        $tenders = \common\models\Tender::find()->where(['status' => 1, 'aoc_status' => null])->orderBy(['id' => SORT_DESC]);
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

//$tenders=[];
        $contractors = \common\models\Contractor::find()->orderBy(['firm' => SORT_ASC])->all();

        if ($val) {
            return $this->redirect(array('site/atenders?filter=' . $val . ''));
        } else {
            return $this->render('tenders', [
                        'tenders' => $models,
                        'contractors' => $contractors,
                        'pages' => $pages,
                        'total' => $countQuery->count(),
                        'type' => 'Approved',
                        'url' => 'atenders'
            ]);
        }
    }

    public function actionApprovedtenders() {

        $val = @$_POST['sort'];
        $page = @$_REQUEST['page'];
        $filter = @$_GET['filter'];


        $tenders = \common\models\Tender::find()->where(['status' => 1, 'is_archived' => null])->orderBy(['id' => SORT_DESC]);
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

//$tenders=[];
        $contractors = [];

        if ($val) {
            return $this->redirect(array('site/approvedtenders?filter=' . $val . ''));
        } else {
            return $this->render('tenders', [
                        'tenders' => $models,
                        'contractors' => $contractors,
                        'pages' => $pages,
                        'total' => $countQuery->count(),
                        'type' => 'Approved',
                        'url' => 'approvedtenders'
            ]);
        }
    }

    public function actionApprovetenders() {

        $val = @$_POST['sort'];
        $page = @$_REQUEST['page'];
        $filter = @$_GET['filter'];
        $cid = @$_POST['commandid'];

        $command = @$_GET['c'];
        if ($command) {
            $tenders = \common\models\Tender::find()->where(['command' => $command])->orderBy(['id' => SORT_DESC]);
        } else {
            $tenders = \common\models\Tender::find()->orderBy(['id' => SORT_DESC]);
        }
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

//$tenders=[];
        $contractors = [];
        $commandname = $this->actionGetcommand($command);

        if ($val) {
            return $this->redirect(array('site/approvetenders/' . $cid . '?filter=' . $val . ''));
        } else {
            return $this->render('approvetenders', [
                        'tenders' => $models,
                        'commandname' => $commandname,
                        'contractors' => $contractors,
                        'pages' => $pages,
                        'total' => $countQuery->count(),
                        'type' => 'All',
                        'url' => 'approvetenders'
            ]);
        }
    }

    public function actionApproveitem() {
        $id = $_REQUEST['value'];
        $itemdetail = \common\models\ItemDetails::find()->where(['id' => $id])->one();
        $makedetail = \common\models\MakeDetails::find()->where(['item_detail_id' => $id])->one();
        $itemz = \common\models\Item::find()->where(['id' => $itemdetail->item_id])->one();

        $itemz->status = 1;
        $makedetail->status = 1;
        $itemdetail->status = 1;
        if ($itemdetail->save()) {
            $makedetail->save();
            $itemz->save();
            $checkstatus = \common\models\Item::find()->where(['tender_id' => $itemz->tender_id, 'status' => 0])->all();

            if (!$checkstatus) {
                $tmodel = \common\models\Tender::find()->where(['id' => $itemz->tender_id])->one();
                $tmodel->status = 1;
                $tmodel->save();
            }
            echo json_encode(['status' => '1']);
        } else {
            echo json_encode(['status' => '0']);
        }
        die;
    }

    public function actionItems() {
        $items = \common\models\Item::find()->all();

        foreach ($items as $item) {
            $idetails = \common\models\ItemDetails::find()->where(['item_id' => $item->id])->all();
            $data['items'] = $item;
            $data['details'][] = $idetails;
        }
        return $this->render('items', [
                    'items' => $items
        ]);
    }

    public function actionCreateTender() {

        $user = Yii::$app->user->identity;
        $id = @$_GET['id'];
        require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
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



        if (isset($_POST['submit'])) {

            if ($_POST['id']) {
                $model = \common\models\Tender::find()->where(['id' => $_POST['id']])->one();
                $model->command = @$_POST['command'];
                $model->cengineer = @$_POST['cengineer'];
                $model->cwengineer = @$_POST['cwengineer'];
                $model->gengineer = @$_POST['gengineer'];
                $model->work = $_POST['work'];
                $model->reference_no = $_POST['refno'];
                $model->tender_id = $_POST['tid'];
                $model->published_date = @$_POST['pdate'];
                $model->document_date = @$_POST['ddate'];
                $model->bid_sub_date = @$_POST['subdate'];
                $model->bid_end_date = @$_POST['enddate'];
                $model->cvalue = $_POST['costvalue'];
                $model->bid_opening_date = @$_POST['odate'];

                if ($_FILES['tfile']['name']) {
                    $file_name = time() . $_FILES['tfile']['name'];
                    $file_tmp = $_FILES['tfile']['tmp_name'];
                    move_uploaded_file($file_tmp, "assets/files/" . $file_name);

                    $keyName = 'files/' . $file_name;
                    $pathInS3 = 'http://s3.us-east-2.amazonaws.com/' . Yii::$app->params['bucketName'] . '/' . $keyName;

                    $file = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/files/" . $file_name;
                    /* $fileupload = $s3->putObject(
                      array(
                      'Bucket' => Yii::$app->params['bucketName'],
                      'Key' => $keyName,
                      'SourceFile' => $file,
                      'ACL' => 'public-read-write'
                      )
                      ); */

                    $uploader = new MultipartUploader($s3, $file, [
                        'bucket' => Yii::$app->params['bucketName'],
                        'key' => $keyName,
                        'ACL' => 'public-read-write'
                    ]);

                    $fileupload = $uploader->upload();

                    if ($fileupload) {
                        $model->tfile = $pathInS3;
                        unlink('assets/files/' . $file_name);
                    }
                }

                $model->save();

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Tender successfully updated");
                }
            } else {
                $model = new \common\models\Tender();
                $model->command = @$_POST['command'];
                $model->cengineer = @$_POST['cengineer'];
                $model->cwengineer = @$_POST['cwengineer'];
                $model->gengineer = @$_POST['gengineer'];
                $model->work = $_POST['work'];
                $model->reference_no = $_POST['refno'];
                $model->tender_id = $_POST['tid'];
                $model->published_date = @$_POST['pdate'];
                $model->document_date = @$_POST['ddate'];
                $model->bid_sub_date = @$_POST['subdate'];
                $model->bid_end_date = @$_POST['enddate'];
                $model->cvalue = $_POST['costvalue'];
                $model->bid_opening_date = @$_POST['odate'];
                $model->user_id = $user->UserId;
                $model->createdon = date('Y-m-d h:i:s');
                if ($user->group_id == 3) {
                    $model->status = 0;
                } else {
                    $model->status = 1;
                }

                if ($_FILES['tfile']['name']) {
                    $file_name = time() . $_FILES['tfile']['name'];
                    $file_tmp = $_FILES['tfile']['tmp_name'];
                    move_uploaded_file($file_tmp, "assets/files/" . $file_name);

                    $keyName = 'files/' . $file_name;
                    $pathInS3 = 'http://s3.us-east-2.amazonaws.com/' . Yii::$app->params['bucketName'] . '/' . $keyName;

                    $file = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/files/" . $file_name;
                    /* $fileupload = $s3->putObject(
                      array(
                      'Bucket' => Yii::$app->params['bucketName'],
                      'Key' => $keyName,
                      'SourceFile' => $file,
                      'ACL' => 'public-read-write'
                      )
                      ); */

                    $uploader = new MultipartUploader($s3, $file, [
                        'bucket' => Yii::$app->params['bucketName'],
                        'key' => $keyName,
                        'ACL' => 'public-read-write'
                    ]);

                    $fileupload = $uploader->upload();

                    if ($fileupload) {
                        $model->tfile = $pathInS3;
                        unlink('assets/files/' . $file_name);
                    }
                }

                $tender = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('tenders', $model)
                        ->execute();

                if ($tender) {
                    Yii::$app->session->setFlash('success', "Tender successfully added");
                }
            }

            return $this->redirect(array('site/tenders'));

            die();
        } else {
            if ($id) {
                $tender = \common\models\Tender::find()->where(['id' => $id])->one();
            } else {
                $tender = [];
            }

            return $this->render('createtender', [
                        'tender' => $tender
            ]);
        }
    }

    public function actionCreateItem() {

        $user = Yii::$app->user->identity;
        $model = new \common\models\Item();
        $detail = new \common\models\ItemDetails();
        $mdetail = new \common\models\MakeDetails();
        $tid = @$_GET['id'];
        $newarr = [];

        if (isset($_POST['submit'])) {

            $_POST['makeids'] = json_decode($_POST['makeids']);
            if (@$_POST['makeids']) {
                foreach ($_POST['makeids'] as $allmakes) {
                    if (array_key_exists($allmakes->key, $newarr)) {
                        $newarr[$allmakes->key] = $newarr[$allmakes->key] . ',' . $allmakes->value;
                    } else {
                        $newarr[$allmakes->key] = $allmakes->value;
                    }
                }
            }
            if ($_POST['itemtender']) {
                foreach ($_POST['itemtender'] as $k => $v) {
                    $model->tender_id = $_POST['tender_id'];
                    $model->tenderone = @$_POST['tenderone'];
                    $model->tendertwo = @$_POST['tendertwo'];
                    $model->tenderthree = @$_POST['tenderthree'];
                    $model->tenderfour = @$_POST['tenderfour'];
                    $model->tenderfive = @$_POST['tenderfive'];
                    $model->tendersix = @$_POST['tendersix'];
                    $model->user_id = $user->UserId;
                    $model->createdon = date('Y-m-d h:i:s');
                    if ($user->group_id == 3) {
                        $model->status = 0;
                    } else {
                        $model->status = 1;
                    }

                    $tender = \Yii::$app
                            ->db
                            ->createCommand()
                            ->insert('items', $model)
                            ->execute();
                    $id = Yii::$app->db->getLastInsertID();


                    $detail->description = @$_POST['desc'][$k];
                    $detail->itemtender = $_POST['itemtender'][$k];
                    $detail->quantity = $_POST['quantity'][$k];
                    $detail->units = $_POST['units'][$k];
                    $detail->core = @$_POST['core'][$k];
                    $detail->typefitting = @$_POST['type'][$k];
                    $detail->capacityfitting = @$_POST['text'][$k];
                    $detail->accessoryone = @$_POST['accessoryone'][$k];
                    $detail->accessorytwo = @$_POST['accessorytwo'][$k];
                    //$detail->make = @$newarr[$k];
                    $detail->make = implode(',', $_POST['makes']);
                    $detail->makeid = @$_POST['makeid'][$k];
                    $detail->user_id = $user->UserId;
                    $detail->item_id = $id;
                    $detail->createdon = date('Y-m-d h:i:s');
                    if ($user->group_id == 3) {
                        $detail->status = 0;
                    } else {
                        $detail->status = 1;
                    }

                    $details = \Yii::$app
                            ->db
                            ->createCommand()
                            ->insert('itemdetails', $detail)
                            ->execute();
                    $lastid = Yii::$app->db->getLastInsertID();

                    $makesids = explode(',', @$newarr[$k]);
                    if (@$makesids) {
                        foreach ($makesids as $makes_ids) {
                            $mdetail->description = @$v;
                            $mdetail->itemtender = $_POST['itemtender'][$k];
                            $mdetail->quantity = $_POST['quantity'][$k];
                            $mdetail->units = $_POST['units'][$k];
                            $mdetail->core = @$_POST['core'][$k];
                            $mdetail->typefitting = @$_POST['type'][$k];
                            $mdetail->capacityfitting = @$_POST['text'][$k];
                            $mdetail->accessoryone = @$_POST['accessoryone'][$k];
                            $mdetail->accessorytwo = @$_POST['accessorytwo'][$k];
                            //$mdetail->make = $makes_ids;
                            $mdetail->make = implode(',', $_POST['makes']);
                            $mdetail->makeid = @$_POST['makeid'][$k];
                            $mdetail->user_id = $user->UserId;
                            $mdetail->item_id = $id;
                            $mdetail->item_detail_id = $lastid;
                            $mdetail->createdon = date('Y-m-d h:i:s');
                            if ($user->group_id == 3) {
                                $mdetail->status = 0;
                            } else {
                                $mdetail->status = 1;
                            }

                            $mdetails = \Yii::$app
                                    ->db
                                    ->createCommand()
                                    ->insert('makedetails', $mdetail)
                                    ->execute();
                        }
                    }
                }
            }

            if ($user->group_id != 3) {
                $checkstatus = \common\models\Item::find()->where(['tender_id' => $_POST['tender_id'], 'status' => 0])->all();
                if (!$checkstatus) {
                    $tmodel = \common\models\Tender::find()->where(['id' => $_POST['tender_id']])->one();
                    $tmodel->status = 1;
                    $tmodel->save();
                }
            }

            Yii::$app->session->setFlash('success', "Item successfully added");

            return $this->redirect(array('site/create-item/' . $_POST['tender_id'] . ''));

            die();
        } else {

            $makes = \common\models\Make::find()->where(['status' => 1])->all();
            $tenders = \common\models\Tender::find()->all();
            $idetails = \common\models\ItemDetails::find()->leftJoin('items', 'itemdetails.item_id = items.id')->where(['items.tender_id' => $tid])->orderBy(['itemdetails.id' => SORT_DESC])->all();
            if (@$idetails) {
                foreach ($idetails as $idetail) {
                    $descfull = '';
                    $items = [];
                    $items = \common\models\Item::find()->where(['id' => $idetail->item_id])->one();
                    $makeids = explode(',', $idetail->make);
                    $makenameall = '';
                    if (@$makeids) {
                        foreach ($makeids as $mid) {
                            $makename = \common\models\Make::find()->where(['id' => $mid])->one();
                            if (@$makename) {
                                $makenameall .= '<span class="viewmake">' . $makename->make . '</span>';
                            }
                        }
                    }
                    $idetail->make = rtrim($makenameall, ',');
                    if ($items->tenderone != '' && $items->tenderone != 0) {
                        $one = $this->actionTenderone($items->tenderone);
                        $descfull .= $one . ',';
                    }
                    if ($items->tendertwo != '' && $items->tendertwo != 0) {
                        $two = $this->actionTendertwo($items->tendertwo);
                        $descfull .= $two . ',';
                    }
                    if ($items->tenderthree != '' && $items->tenderthree != 0) {
                        $three = $this->actionTenderthree($items->tenderthree);
                        $descfull .= $three . ',';
                    }
                    if ($items->tenderfour != '' && $items->tenderfour != 0) {
                        $four = $this->actionTenderfour($items->tenderfour);
                        $descfull .= $four . ',';
                    }
                    if ($items->tenderfive != '' && $items->tenderfive != 0) {
                        $five = $this->actionTenderfive($items->tenderfive);
                        $descfull .= $five . ',';
                    }
                    if ($items->tendersix != '' && $items->tendersix != 0) {
                        $six = $this->actionTendersix($items->tendersix);
                        $descfull .= $six . ',';
                    }
                    $descfull = rtrim($descfull, ',');
                    $size = \common\models\Size::find()->where(['id' => $idetail->description])->one();
                    $core = $this->actionGetcore($idetail->core);
                    $type = $this->actionGetfit($idetail->typefitting);
                    $capacity = $this->actionGetfit($idetail->capacityfitting);
                    $accone = $this->actionGetaccessory($idetail->accessoryone);
                    if ($items->tenderfour == 1) {
                        $idetail->description = @$size->size . ' ' . $core . ' (' . $descfull . ')';
                    } elseif ($items->tenderfour == 2) {
                        $idetail->description = @$type . ' ' . @$capacity . ' (' . $descfull . ')';
                    } elseif ($items->tenderfour == 4) {
                        $idetail->description = @$accone . ' ' . @$idetail->accessorytwo . ' (' . $descfull . ')';
                    } elseif ($items->tenderfour == 5) {
                        $idetail->description = @$size->size . ' (' . $descfull . ')';
                    } else {
                        $idetail->description = '(' . $descfull . ')';
                    }
                }
            }
            $tenderdetail = \common\models\Tender::find()->where(['id' => $tid])->one();
            return $this->render('createitem', [
                        'tenders' => $tenders,
                        'idetails' => $idetails,
                        'makes' => $makes,
                        'tender' => $tenderdetail
            ]);
        }
    }

    public function actionDeleteTender() {
        $id = $_GET['id'];
        $ids = [];
        $delete = \common\models\Tender::deleteAll(['id' => $id]);
        if ($delete) {
            $idetails = \common\models\Item::find()->where(['tender_id' => $id])->all();
            if (@$idetails) {
                foreach ($idetails as $idet) {
                    $ids[] = $idet->id;
                }
            }
            $itemid = \common\models\Item::deleteAll(['tender_id' => $id]);
            $deleteone = \common\models\ItemDetails::deleteAll(['item_id' => $ids]);
            $deletetwo = \common\models\MakeDetails::deleteAll(['item_id' => $ids]);
            Yii::$app->session->setFlash('success', "Tender successfully deleted");
            return $this->redirect(array('site/tenders'));
        }
    }

    public function actionMovearchive() {
        $id = @$_REQUEST['value'];
        $tender = \common\models\Tender::find()->where(['id' => $id])->one();
        if ($tender->is_archived == 1) {
            $archived = '';
        } else {
            $archived = 1;
        }
        $data = ['is_archived' => $archived];
        $querydata = \Yii::$app
                ->db
                ->createCommand()
                ->update('tenders', $data, 'id = ' . $tender->id . '')
                ->execute();
        if ($querydata) {
            echo json_encode(['status' => 1, 'arc' => $archived]);
        } else {
            echo json_encode(['status' => 0, 'arc' => $archived]);
        }
    }

    public function actionDeleteTenders() {
        if (count(@$_POST["selected_id"]) > 0) {
            $all = implode(",", $_POST["selected_id"]);
            $delete = \common\models\Tender::deleteAll(['id' => $_POST["selected_id"]]);
            if ($delete) {
                $idetails = \common\models\Item::find()->where(['tender_id' => $_POST["selected_id"]])->all();
                if (@$idetails) {
                    foreach ($idetails as $idet) {
                        $ids[] = $idet->id;
                    }
                }
                $itemid = \common\models\Item::deleteAll(['tender_id' => $_POST["selected_id"]]);
                $deleteone = \common\models\ItemDetails::deleteAll(['item_id' => $ids]);
                $deletetwo = \common\models\MakeDetails::deleteAll(['item_id' => $ids]);
                Yii::$app->session->setFlash('success', "Tenders successfully deleted");
                return $this->redirect(array('site/tenders'));
            }
        } else {
            Yii::$app->session->setFlash('error', "Please select the checkbox to perform action");
            return $this->redirect(array('site/tenders'));
        }
    }

    public function actionMovearchivetenders() {
        if (count(@$_POST["selected_id"]) > 0) {
            $all = implode(",", $_POST["selected_id"]);
            $tenders = \common\models\Tender::find()->where(['id' => $_POST["selected_id"]])->all();
            if ($tenders) {
                foreach ($tenders as $_tender) {
                    $_tender->is_archived = '1';
                    $_tender->save();
                }
                Yii::$app->session->setFlash('success', "Tenders successfully archived");
                return $this->redirect(array('site/movetoarchive'));
            }
        } else {
            Yii::$app->session->setFlash('error', "Please select the checkbox to perform action");
            return $this->redirect(array('site/movetoarchive'));
        }
    }

    public function actionDeleteUser() {
        $id = $_GET['id'];
        $user = User::deleteAll(['UserId' => $id]);
        if ($user) {
            Yii::$app->session->setFlash('success', "User successfully deleted");
            return $this->redirect(array('site/users'));
        }
    }

    public function actionDeleteClient() {
        $id = $_GET['id'];
        $client = \common\models\Clients::find()->where(['id' => $id])->one();
        $user = \common\models\Clients::deleteAll(['id' => $id]);
        if ($user) {
            if ($client->type == 3) {
                Yii::$app->session->setFlash('success', "Dealer successfully deleted");
                return $this->redirect(array('site/dealers'));
            } elseif ($client->type == 2) {
                Yii::$app->session->setFlash('success', "Contractor successfully deleted");
                return $this->redirect(array('site/contractors'));
            } else {
                Yii::$app->session->setFlash('success', "Manufacturer successfully deleted");
                return $this->redirect(array('site/manufacturers'));
            }
        }
    }

    public function actionGetcengineer() {
        $value = $_REQUEST['value'];
        $data = '<option value="" selected>Select</option>';
        if ($value == 6) {
            $data .= '<option value="1">CE CC AND CE (AF) ALLAHABAD - MES</option>';
            $data .= '<option value="2">CE CC AND CE BAREILLY ZONE - MES</option>';
            $data .= '<option value="3">CE CC AND CE JABALPUR ZONE - MES</option>';
            $data .= '<option value="4">CE CC AND CE LUCKNOW ZONE - MES</option>';
        } elseif ($value == 7) {
            $data .= '<option value="5">CE EC AND CCE (ARMY) NO 1 DINJAN - MES</option>';
            $data .= '<option value="6">CE EC AND CCE (ARMY ) No 2 MISSAMARI - MES</option>';
            $data .= '<option value="7">CE EC AND CCE (ARMY) NO 3 NARANGI - MES</option>';
            $data .= '<option value="8">CEEC AND CCE (NEP) NEW DELHI - MES</option>';
            $data .= '<option value="9">CE EC AND CE (AF) SHILLONG - MES</option>';
            $data .= '<option value="10">CE EC AND CE KOLKATA ZONE - MES</option>';
            $data .= '<option value="11">CE EC AND CE (NAVY) VIZAG - MES</option>';
            $data .= '<option value="12">CE EC AND CE SHILLONG ZONE - MES</option>';
            $data .= '<option value="13">CE EC AND CE SILIGURI ZONE - MES</option>';
            $data .= '<option value="14">CE EC AND DGNP (VIZAG) - MES</option>';
        } elseif ($value == 8) {
            $data .= '<option value="15">CE NC AND CE 31 ZONE - MES</option>';
            $data .= '<option value="16">CE NC AND CE (AF) UDHAMPUR - MES</option>';
            $data .= '<option value="17">CE NC AND CE LEH ZONE - MES</option>';
            $data .= '<option value="18">CE NC AND CE UDHAMPUR ZONE - MES</option>';
        } elseif ($value == 9) {
            $data .= '<option value="19">CE SC AND CE (A and N) ZONE - MES</option>';
            $data .= '<option value="20">CESC AND CE (AF) BANGALORE - MES</option>';
            $data .= '<option value="21">CESC AND CE (AF) NAGPUR - MES</option>';
            $data .= '<option value="22">CESC AND CE BHOPAL ZONE  - MES</option>';
            $data .= '<option value="23">CE SC AND CE CHENNAI ZONE  - MES</option>';
            $data .= '<option value="24">CESC AND CE JODHPUR ZONE - MES</option>';
            $data .= '<option value="25">CESC AND CE (NAVY) KOCHI - MES</option>';
            $data .= '<option value="26">CESC AND CE( NAVY )MUMBAI - MES</option>';
            $data .= '<option value="27">CE SC AND CE PUNE ZONE - MES</option>';
        } elseif ($value == 10) {
            $data .= '<option value="28">CE SWC AND CE (AF) GANDHINAGAR - MES</option>';
            $data .= '<option value="29">CE SWC AND CE BATHINDA ZONE - MES</option>';
            $data .= '<option value="30">CE SWC AND CE JAIPUR JAIPUR - MES</option>';
        } elseif ($value == 11) {
            $data .= '<option value="31">CE WC and CE(AF) WAC PALAM-MES</option>';
            $data .= '<option value="32">CE WC AND CE CHANDIGARH ZONE - MES</option>';
            $data .= '<option value="33">CE WC and CE DELHI ZONE-MES</option>';
            $data .= '<option value="34">CE WC AND CE JALANDHAR ZONE - MES</option>';
            $data .= '<option value="35">CE WC AND CE PATHANKOT ZONE - MES</option>';
        } elseif ($value == 1) {
            $data .= '<option value="36">AGE (I) B/R JAKHAU - MES</option>';
            $data .= '<option value="37">GE (CG) KOCHI - MES</option>';
            $data .= '<option value="57">GE (CG) PORBANDAR - MES</option>';
            $data .= '<option value="38">GE DAMAN - MES</option>';
        } elseif ($value == 3) {
            $data .= '<option value="58">GE (I)(P) Fy AMBAJHARI - MES</option>';
            $data .= '<option value="59">GE (I)(FY) AVADI - MES</option>';
            $data .= '<option value="39">AGE (I) FY EDDUMAILARAM - MES</option>';
            $data .= '<option value="40">GE (I) (FY) ISHAPORE - MES</option>';
            $data .= '<option value="41">GE (I) (P) (FY) ITARSI - MES</option>';
            $data .= '<option value="42">GE(I)(P) Fy KANPUR - MES</option>';
            $data .= '<option value="43">GE (I) (P) FY KIRKEE - MES</option>';
        } elseif ($value == 4) {
            $data .= '<option value="44">AGE(I) R and D Haldwani - MES</option>';
            $data .= '<option value="60">AGE(I) R and D Jodhpur - MES</option>';
            $data .= '<option value="45">AGE(I) R and D Manali - MES</option>';
            $data .= '<option value="61">AGE(I) R and D Delhi - MES</option>';
            $data .= '<option value="62">GE(I) R and D Chandigarh - MES</option>';
            $data .= '<option value="46">GE(I) R and D Chandipur - MES</option>';
            $data .= '<option value="47">GE(I) R and D Dehradun - MES</option>';
            $data .= '<option value="48">GE(I) R and D Kanpur - MES</option>';
        } elseif ($value == 5) {
            $data .= '<option value="49">AGE (I) RND AVADI - MES</option>';
            $data .= '<option value="50">AGE (I) RND KOCHI - MES</option>';
            $data .= '<option value="51">AGE (I) RND VISHAKHAPATNAM - MES</option>';
            $data .= '<option value="52">GE (I) RND (E) BANGALORE - MES</option>';
            $data .= '<option value="53">GE (I) RND KANCHANBAGH - MES</option>';
            $data .= '<option value="63">GE (I) RND GIRINAGAR - MES</option>';
            $data .= '<option value="54">GE (I) RND PASHAN - MES</option>';
            $data .= '<option value="55">GE (I) RND RCI HYDERABAD - MES</option>';
            $data .= '<option value="56">GE (I) RND (W) BANGALORE - MES</option>';
        } elseif ($value == 13) {
            $data .= '<option value="64">GE (I)(CG) Chennai - MES</option>';
        }
        echo json_encode(['data' => $data]);
        die;
    }

    public function actionGetcwengineer() {
        $value = $_REQUEST['value'];
        $data = '<option value="" selected>Select</option>';
        if ($value == 1) {
            $data .= '<option value="1">CWE (AF) BAMRAULI ALLAHABAD - MES</option>';
            $data .= '<option value="2">CWE (AF) IZATNAGAR - MES</option>';
            $data .= '<option value="3">CWE (AF) KHERIA - MES</option>';
            $data .= '<option value="4">CWE (AF) MAHARAJPUR - MES</option>';
        } elseif ($value == 2) {
            $data .= '<option value="5">CWE BAREILLY - MES</option>';
            $data .= '<option value="6">CWE DEHRADUN - MES</option>';
            $data .= '<option value="7">CWE HILLS DEHRADUN -  MES</option>';
            $data .= '<option value="8">CWE HILLS PITHORAGARH - MES</option>';
            $data .= '<option value="9">CWE MEERUT - MES</option>';
            $data .= '<option value="10">CWE No 2 MEERUT - MES</option>';
        } elseif ($value == 3) {
            $data .= '<option value="11">CWE JABALPUR - MES</option>';
            $data .= '<option value="12">CWE MHOW - MES</option>';
            $data .= '<option value="13">CWE RANCHI - MES</option>';
            $data .= '<option value="14">GE (I) GOS - MES</option>';
        } elseif ($value == 4) {
            $data .= '<option value="15">CWE AGRA - MES</option>';
            $data .= '<option value="16">CWE ALLAHABAD - MES</option>';
            $data .= '<option value="17">CWE KANPUR - MES</option>';
            $data .= '<option value="18">CWE LUCKNOW - MES</option>';
            $data .= '<option value="19">CWE MATHURA</option>';
        } elseif ($value == 9) {
            $data .= '<option value="134">CWE (AF) BAGDOGRA - (AF) Shillong Zone- MES</option>';
            $data .= '<option value="20">CWE (AF) BORJAR - MES</option>';
            $data .= '<option value="21">CWE (AF) JORHAT - MES</option>';
            $data .= '<option value="22">CWE (AF) KALAIKUNDA - MES</option>';
            $data .= '<option value="23">CWE (AF) PANAGARH-MES</option>';
            $data .= '<option value="24">GE (I)(AF) SHILLONG - MES</option>';
            $data .= '<option value="25">GE (I) (P) (AF) TEZPUR - MES</option>';
        } elseif ($value == 10) {
            $data .= '<option value="26">CWE KOLKATA - MES</option>';
            $data .= '<option value="27">CWE (P) Kolkata - MES</option>';
            $data .= '<option value="28">CWE (SUBURB) BARRACKPORE - MES</option>';
        } elseif ($value == 11) {
            $data .= '<option value="29">CWE (Navy) Chennai - MES</option>';
            $data .= '<option value="30">CWE (P) VISHAKHAPATNAM - MES</option>';
            $data .= '<option value="31">CWE VISAKHAPATNAM - MES</option>';
            $data .= '<option value="32">GE (I) (DM) VISAKHAPATNAM - MES</option>';
        } elseif ($value == 12) {
            $data .= '<option value="33">CWE Dinjan - MES</option>';
            $data .= '<option value="34">CWE HQ 137 WE - MES</option>';
            $data .= '<option value="35">CWE Shillong - MES</option>';
            $data .= '<option value="36">CWE Tezpur - MES</option>';
        } elseif ($value == 13) {
            $data .= '<option value="37">CWE BENGDUBI - MES</option>';
            $data .= '<option value="38">CWE BINNAGURI - MES</option>';
            $data .= '<option value="135">CWE TENGA - MES</option>';
            $data .= '<option value="136">GE(I)(P) SILIGURI - MES</option>';
            $data .= '<option value="39">GE(I)(P) GANGTOK - MES</option>';
            $data .= '<option value="40">HQ 136 WORKS ENGINEERS - MES</option>';
        } elseif ($value == 15) {
            $data .= '<option value="41">133 WORKS ENGINEER - MES</option>';
            $data .= '<option value="42">134 WORKS ENGINEER - MES</option>';
        } elseif ($value == 16) {
            $data .= '<option value="43">CWE (AF) JAMMU - MES</option>';
            $data .= '<option value="44">CWE (AF) Leh - MES</option>';
            $data .= '<option value="45">CWE (AF) SRINAGAR - MES</option>';
        } elseif ($value == 17) {
            $data .= '<option value="46">CWE KUMBATHANG - MES</option>';
            $data .= '<option value="137">GE(I) Project No. 1 Leh - MES</option>';
            $data .= '<option value="47">HQ 138 WORKS ENGR - MES</option>';
        } elseif ($value == 18) {
            $data .= '<option value="48">135 WORKS ENGINEER - MES</option>';
            $data .= '<option value="49">CWE DHAR ROAD - MES</option>';
            $data .= '<option value="50">CWE RAJOURI - MES</option>';
            $data .= '<option value="51">CWE UDHAMPUR - MES</option>';
            $data .= '<option value="52">GE I 873 EWS - MES</option>';
            $data .= '<option value="53">GE I ARMY DHAR ROAD - MES</option>';
        } elseif ($value == 19) {
            $data .= '<option value="54">CWE No. 2 PORT BLAIR - MES</option>';
            $data .= '<option value="55">CWE PORTBLAIR - MES</option>';
            $data .= '<option value="56">GE (I) 866 EWS - MES</option>';
            $data .= '<option value="57">GE (I) CAMPBELL BAY - MES</option>';
            $data .= '<option value="58">GE (I) (P) Central-Port Blair - MES</option>';
            $data .= '<option value="59">GE (I) (P) NORTH PORT BLAIR- MES</option>';
        } elseif ($value == 20) {
            $data .= '<option value="60">CWE (AF) (NORTH) BANGALORE - MES</option>';
            $data .= '<option value="61">CWE (AF) SECUNDERABAD - MES</option>';
            $data .= '<option value="62">CWE (AF) (SOUTH) BANGALORE - MES</option>';
            $data .= '<option value="63">CWE (AF) TRIVANDRUM - MES</option>';
            $data .= '<option value="64">GE(I) Field Investigation Pune - MES</option>';
        } elseif ($value == 21) {
            $data .= '<option value="65">CWE (AF) CHAKERI - MES</option>';
            $data .= '<option value="66">CWE (AF) NAGPUR - MES</option>';
            $data .= '<option value="67">CWE (AF) TUGALAKABAD - MES</option>';
            $data .= '<option value="68">GE (I) (AF) NAGPUR - MES</option>';
            $data .= '<option value="69">GE (I) (AF) OZHAR - MES</option>';
        } elseif ($value == 22) {
            $data .= '<option value="70">CWE BHOPAL - MES</option>';
            $data .= '<option value="71">CWE JHANSI - MES</option>';
            $data .= '<option value="72">CWE NAGPUR - MES</option>';
        } elseif ($value == 23) {
            $data .= '<option value="73">CWE (ARMY) BANGALORE - MES</option>';
            $data .= '<option value="74">CWE CHENNAI - MES</option>';
            $data .= '<option value="75">CWE SECUNDERABAD - MES</option>';
            $data .= '<option value="76">CWE WELLINGTON - MES</option>';
            $data .= '<option value="77">GE (I) BELGAUM - MES</option>';
        } elseif ($value == 24) {
            $data .= '<option value="78">CWE AHMEDABAD - MES</option>';
            $data .= '<option value="79">CWE(ARMY) JODHPUR - MES</option>';
            $data .= '<option value="80">CWE JAISALMER - MES</option>';
        } elseif ($value == 25) {
            $data .= '<option value="81">CWE EZHIMALA - MES</option>';
            $data .= '<option value="82">CWE (NB) KOCHI - MES</option>';
            $data .= '<option value="83">CWE NW KOCHI - MES</option>';
            $data .= '<option value="138">GE (I) Navy JAMNAGAR - MES</option>';
            $data .= '<option value="84">GE (I) Navy LAKSHADWEEP - MES</option>';
            $data .= '<option value="85">GE (I) NAVY LONAWALA - MES</option>';
        } elseif ($value == 26) {
            $data .= '<option value="86">CWE NAVY KARANJA - MES</option>';
            $data .= '<option value="87">CWE NAVY VASCO - MES</option>';
            $data .= '<option value="88">CWE (NW) MUMBAI - MES</option>';
            $data .= '<option value="89">CWE (SUBURB) MUMBAI - MES</option>';
            $data .= '<option value="90">GE (I) KARWAR - MES</option>';
            $data .= '<option value="91">GE (I) NAVY PORBANDAR - MES</option>';
            $data .= '<option value="92">GE(I) RATNAGIRI - MES</option>';
        } elseif ($value == 27) {
            $data .= '<option value="93">CWE (ARMY) MUMBAI - MES</option>';
            $data .= '<option value="94">CWE DEOLALI - MES</option>';
            $data .= '<option value="95">CWE KIRKEE - MES</option>';
            $data .= '<option value="96">CWE PUNE - MES</option>';
        } elseif ($value == 28) {
            $data .= '<option value="97">CWE (AF) BHUJ - MES</option>';
            $data .= '<option value="98">CWE (AF) CHILODA - MES</option>';
            $data .= '<option value="99">CWE (AF) Jaisalmer - MES</option>';
            $data .= '<option value="100">CWE (AF) JAMNAGAR - MES</option>';
            $data .= '<option value="101">CWE (AF) JODHPUR - MES</option>';
            $data .= '<option value="102">CWE (AF) LOHOGAON - MES</option>';
            $data .= '<option value="103">GE (I) (AF) BARODA - MES</option>';
        } elseif ($value == 29) {
            $data .= '<option value="104">CWE BATHINDA - MES</option>';
            $data .= '<option value="105">CWE BIKANER - MES</option>';
            $data .= '<option value="106">CWE GANGANAGAR - MES</option>';
            $data .= '<option value="139">GE (I) (P) NO 2 BATHINDA - MES</option>';
        } elseif ($value == 30) {
            $data .= '<option value="107">CWE HISAR - MES</option>';
            $data .= '<option value="108">CWE JAIPUR - MES</option>';
            $data .= '<option value="109">CWE KOTA - MES</option>';
            $data .= '<option value="110">CWE Mathura - MES</option>';
            $data .= '<option value="111">GE(I) JAIPUR - MES</option>';
        } elseif ($value == 31) {
            $data .= '<option value="112">CWE (AF) Ambala-MES</option>';
            $data .= '<option value="113">CWE(AF) BHISIANA - MES</option>';
            $data .= '<option value="114">CWE (AF) Bikaner-MES</option>';
            $data .= '<option value="115">CWE (AF) Chandigarh-MES</option>';
            $data .= '<option value="116">CWE (AF) GURGAON - MES</option>';
            $data .= '<option value="117">CWE (AF) Palam-MES</option>';
            $data .= '<option value="140">GE (I)(AF) Gurgaon-MES</option>';
        } elseif ($value == 32) {
            $data .= '<option value="118">CWE AMBALA - MES</option>';
            $data .= '<option value="119">CWE CHANDIMANDIR - MES</option>';
            $data .= '<option value="120">CWE PATIALA - MES</option>';
            $data .= '<option value="121">CWE SHIMLA HILLS - MES</option>';
        } elseif ($value == 33) {
            $data .= '<option value="122">CWE DELHI CANTT-MES</option>';
            $data .= '<option value="123">CWE NEW DELHI-MES</option>';
            $data .= '<option value="124">CWE NO 2 DELHI - MES</option>';
            $data .= '<option value="125">CWE (P) DELHI CANTT-MES</option>';
            $data .= '<option value="126">CWE (U) DELHI CANTT-MES</option>';
        } elseif ($value == 34) {
            $data .= '<option value="127">CWE AMRITSAR - MES</option>';
            $data .= '<option value="128">CWE FEROZEPUR - MES</option>';
            $data .= '<option value="129">CWE JALANDHAR - MES</option>';
        } elseif ($value == 35) {
            $data .= '<option value="130">CWE JAMMU - MES</option>';
            $data .= '<option value="131">CWE MAMUN - MES</option>';
            $data .= '<option value="132">CWE PATHANKOT - MES</option>';
            $data .= '<option value="133">CWE YOL - MES</option>';
        }
        echo json_encode(['data' => $data]);
        die;
    }

    public function actionGetgengineer() {
        $value = $_REQUEST['value'];
        $data = '<option value="" selected>Select</option>';
        if ($value == 1) {
            $data .= '<option value="1">GE (AF) BAMRAULI - MES</option>';
            $data .= '<option value="2">GE (AF) BIHTA</option>';
            $data .= '<option value="3">GE(AF)BKT - MES</option>';
            $data .= '<option value="4">GE (AF) GORAKHPUR</option>';
        } elseif ($value == 2) {
            $data .= '<option value="5">GE(AF)IZATNAGAR - MES</option>';
            $data .= '<option value="317">GE(P)(AF)BKT - MES</option>';
        } elseif ($value == 3) {
            $data .= '<option value="6">GE (AF) TECH AREA KHERIA - MES</option>';
            $data .= '<option value="318">GE (AF) ADM AREA KHERIA - MES</option>';
            $data .= '<option value="319">AGE (AF) (I) SONEGAON - MES</option>';
        } elseif ($value == 4) {
            $data .= '<option value="7">GE (AF) (ADM AREA) MAHARAJPUR - MES</option>';
            $data .= '<option value="8">GE (AF) (TECH AREA) MAHARAJPUR - MES</option>';
            $data .= '<option value="9">GE (P) AF MAHARAJPUR - MES</option>';
        } elseif ($value == 5) {
            $data .= '<option value="10">GE(EAST) BAREILLY - MES</option>';
            $data .= '<option value="11">GE (P) BAREILLY - MES</option>';
            $data .= '<option value="12">GE(WEST) BAREILLY - MES</option>';
        } elseif ($value == 6) {
            $data .= '<option value="13">GE DEHRADUN - MES</option>';
            $data .= '<option value="320">GE (P)DEHRADUN - MES</option>';
            $data .= '<option value="14">GE PREMNAGAR - MES</option>';
        } elseif ($value == 7) {
            $data .= '<option value="15">AGE(I) Raiwala - MES</option>';
            $data .= '<option value="16">GE LANSDOWNE -MES</option>';
            $data .= '<option value="17">GE (MES) Clement Town - MES</option>';
        } elseif ($value == 8) {
            $data .= '<option value="18">GE Pithoragarh - MES</option>';
            $data .= '<option value="321">GE 871EWS - MES</option>';
            $data .= '<option value="19">GE RANIKHET - MES</option>';
        } elseif ($value == 9) {
            $data .= '<option value="20">GE (N) MEERUT - MES</option>';
            $data .= '<option value="322">GE (S) MEERUT - MES</option>';
            $data .= '<option value="21">GE (U) EM Meerut - MES</option>';
        } elseif ($value == 10) {
            $data .= '<option value="22">GE ROORKEE - MES</option>';
            $data .= '<option value="23">GE (SOUTH) MEERUT - MES</option>';
        } elseif ($value == 11) {
            $data .= '<option value="24">AGE (I) PACHMARHI - MES</option>';
            $data .= '<option value="25">AGE (I) RAIPUR - MES</option>';
            $data .= '<option value="26">GE (E) JABALPUR - MES</option>';
            $data .= '<option value="27">GE (W) JABALPUR - MES</option>';
        } elseif ($value == 12) {
            $data .= '<option value="28">GE AWC - MES</option>';
            $data .= '<option value="29">GE (Maint) Inf School - MES</option>';
            $data .= '<option value="323">GE (P) Inf School - MES</option>';
            $data .= '<option value="324">GE MCTE - MES</option>';
        } elseif ($value == 13) {
            $data .= '<option value="30">GE DANAPUR - MES</option>';
            $data .= '<option value="31">GE DIPATOLI - MES</option>';
            $data .= '<option value="32">GE (P) GAYA - MES</option>';
            $data .= '<option value="33">GE RAMGARH - MES</option>';
            $data .= '<option value="34">GE RANCHI - MES</option>';
        } elseif ($value == 14) {
            $data .= '<option value="35">GE I GOPALPUR ON SEA - MES</option>';
        } elseif ($value == 15) {
            $data .= '<option value="36">GE (E) AGRA - MES</option>';
            $data .= '<option value="37">GE (W) AGRA - MES</option>';
        } elseif ($value == 16) {
            $data .= '<option value="38">GE (E) ALLAHABAD - MES</option>';
            $data .= '<option value="39">GE FAIZABAD - MES</option>';
            $data .= '<option value="40">GE(P) ALLAHABAD - MES</option>';
            $data .= '<option value="41">GE (W) ALLAHABAD - MES</option>';
        } elseif ($value == 17) {
            $data .= '<option value="42">GE FATEHGARH - MES</option>';
            $data .= '<option value="325">GE MES KANPUR - MES</option>';
        } elseif ($value == 18) {
            $data .= '<option value="43">GE(EAST)LUCKNOW - MES</option>';
            $data .= '<option value="44">GE(E/M)LUCKNOW - MES</option>';
            $data .= '<option value="45">GE(WEST)LUCKNOW - MES</option>';
        } elseif ($value == 19) {
            $data .= '<option value="46">GE (E) MATHURA - MES</option>';
            $data .= '<option value="47">GE (W) MATHURA - MES</option>';
        } elseif ($value == 20) {
            $data .= '<option value="48">AGE (I) (AF) DIGARU - MES</option>';
            $data .= '<option value="49">AGE (I) KUMBHIRGRAM - MES</option>';
            $data .= '<option value="50">GE (AF) BAGDOGRA - MES</option>';
            $data .= '<option value="51">GE (AF) BORJAR - MES</option>';
            $data .= '<option value="52">GE (AF) HASIMARA - MES</option>';
        } elseif ($value == 21) {
            $data .= '<option value="53">GE (AF) CHABUA - MES</option>';
            $data .= '<option value="54">GE (AF) JORHAT - MES</option>';
            $data .= '<option value="326">GE (AF) MOHANBARI - MES</option>';
            $data .= '<option value="55">GE (AF) TEZPUR - MES</option>';
        } elseif ($value == 22) {
            $data .= '<option value="56">GE (AF) BARRACKPORE - MES</option>';
            $data .= '<option value="57">GE (AF) KALAIKUNDA - MES</option>';
        } elseif ($value == 23) {
            $data .= '<option value="58">AGE (I) (AF) SINGHARSI-MES</option>';
            $data .= '<option value="59">GE (AF) PURNEA-MES</option>';
            $data .= '<option value="327">GE (AF) PANAGARH-MES</option>';
        } elseif ($value == 26) {
            $data .= '<option value="60">GE ALIPORE - MES</option>';
            $data .= '<option value="61">GE(CENTRAL) KOLKATA - MES</option>';
            $data .= '<option value="62">GE FORT WILLIAM KOLKATA - MES</option>';
        } elseif ($value == 27) {
            $data .= '<option value="63">GE (P) (NAVY AND CG) KOLKATA - MES</option>';
        } elseif ($value == 28) {
            $data .= '<option value="64">GE BARRACKPORE - MES</option>';
            $data .= '<option value="65">GE PANAGARH - MES</option>';
        } elseif ($value == 29) {
            $data .= '<option value="66">GE(MAINT) ARAKKONAM - MES</option>';
            $data .= '<option value="67">GE(NAVY)CHENNAI - MES</option>';
            $data .= '<option value="68">GETHIRUNALVELI - MES</option>';
        } elseif ($value == 30) {
            $data .= '<option value="69">GE (N and CG) Bhubaneshwar - MES</option>';
            $data .= '<option value="70">GE (P) CHILKA - MES</option>';
            $data .= '<option value="71">GE (P) (NAVY) KALINGA - MES</option>';
            $data .= '<option value="72">GE (P) (NAVY) VISAKHAPATNAM - MES</option>';
        } elseif ($value == 31) {
            $data .= '<option value="73">GE NAVAL BASE VISAKHAPATNAM - MES</option>';
            $data .= '<option value="74">GE NAVAL DEPOT VISAKHAPATNAM - MES</option>';
            $data .= '<option value="75">GE NAVAL SERVICES VISAKHAPATNAM - MES</option>';
            $data .= '<option value="76">GE UTILITY -II VISAKHAPATNAM - MES</option>';
        } elseif ($value == 32) {
            $data .= '<option value="78">GE I DM VIZAG - MES</option>';
        } elseif ($value == 33) {
            $data .= '<option value="79">AGE (I) Lekhapani - MES</option>';
            $data .= '<option value="80">GE Dinjan - MES</option>';
            $data .= '<option value="81">GE Jorhat - MES</option>';
        } elseif ($value == 34) {
            $data .= '<option value="82">AGE (I) AGARTALA -MES</option>';
            $data .= '<option value="83">AGE (I) Zakhama - MES</option>';
            $data .= '<option value="84">GE 868 EWS - MES</option>';
            $data .= '<option value="85">GE 869 EWS - MES</option>';
            $data .= '<option value="86">GE 872 EWS - MES</option>';
            $data .= '<option value="87">GE SILCHAR - MES</option>';
        } elseif ($value == 35) {
            $data .= '<option value="88">GE GUWAHATI - MES</option>';
            $data .= '<option value="89">GE NARANGI - MES</option>';
            $data .= '<option value="90">GE (P) SHILLONG  - MES</option>';
            $data .= '<option value="91">GE SHILLONG - MES</option>';
        } elseif ($value == 36) {
            $data .= '<option value="92">AGE (I) Rangia - MES</option>';
            $data .= '<option value="93">AGE I TAWANG - MES</option>';
            $data .= '<option value="94">GE 859 EWS - MES</option>';
            $data .= '<option value="95">GE Missamari - MES</option>';
            $data .= '<option value="96">GE (North) Tezpur - MES</option>';
            $data .= '<option value="97">GE (South) Tezpur - MES</option>';
            $data .= '<option value="98">GE TAWANG - MES</option>';
        } elseif ($value == 37) {
            $data .= '<option value="106">GE BENGDUBI - MES</option>';
        } elseif ($value == 38) {
            $data .= '<option value="107">GE (N) BINNAGURI - MES</option>';
            $data .= '<option value="108">GE (S) BINNAGURI - MES</option>';
            $data .= '<option value="109">GE SEVOKE ROAD - MES</option>';
        } elseif ($value == 135) {
            $data .= '<option value="328">GE MISSAMARI - MES</option>';
        } elseif ($value == 40) {
            $data .= '<option value="110">AGE (I) DARJEELING - MES</option>';
            $data .= '<option value="111">GE 867 EWS - MES</option>';
            $data .= '<option value="112">GEGANGTOK - MES</option>';
            $data .= '<option value="113">GE SUKNA - MES</option>';
        } elseif ($value == 41) {
            $data .= '<option value="114">GE 864 EWS - MES</option>';
            $data .= '<option value="115">GE 874 EWS - MES</option>';
            $data .= '<option value="116">GE 969 EWS - MES</option>';
        } elseif ($value == 42) {
            $data .= '<option value="117">AGE (I) CIF (K) - MES</option>';
            $data .= '<option value="118">GE 861 EWS - MES</option>';
            $data .= '<option value="119">GE 970 EWS - MES</option>';
        } elseif ($value == 43) {
            $data .= '<option value="120">GE (AF) JAMMU - MES</option>';
            $data .= '<option value="121">GE (AF) PATHANKOT - MES</option>';
            $data .= '<option value="122">GE (AF) UDHAMPUR - MES</option>';
        } elseif ($value == 45) {
            $data .= '<option value="123">GE (AF) ARANTIPAR - MES</option>';
            $data .= '<option value="124">GE (AF) LEH - MES</option>';
            $data .= '<option value="329">GE (AF) THOISE - MES</option>';
            $data .= '<option value="125">GE(AF)SRINAGAR - MES</option>';
        } elseif ($value == 46) {
            $data .= '<option value="330">GE KARGIL - MES</option>';
            $data .= '<option value="331">GE KHUMBATHANG - MES</option>';
        } elseif ($value == 47) {
            $data .= '<option value="126">GE 865 EWS - MES</option>';
            $data .= '<option value="332">GE 860 EWS - MES</option>';
            $data .= '<option value="127">GE PARTAPUR - MES</option>';
            $data .= '<option value="128">GE (P) NO 2LEH - MES</option>';
        } elseif ($value == 48) {
            $data .= '<option value="129">GE NAGROTA - MES</option>';
            $data .= '<option value="333">AGE(I)CIF(U) - MES</option>';
            $data .= '<option value="130">GE (N) AKHNOOR - MES</option>';
            $data .= '<option value="131">GE (S) AKHNOOR - MES</option>';
        } elseif ($value == 50) {
            $data .= '<option value="132">GE 862 EWS - MES</option>';
        } elseif ($value == 51) {
            $data .= '<option value="133">GE(NORTH) UDHAMPUR - MES</option>';
            $data .= '<option value="134">GE(SOUTH) UDHAMPUR - MES</option>';
            $data .= '<option value="135">GE (U) UDHAMPUR - MES</option>';
        } elseif ($value == 54) {
            $data .= '<option value="136">GE BRICHGUNJ - MES</option>';
            $data .= '<option value="334">GE (P) CENTRAL - MES</option>';
            $data .= '<option value="137">GE (SOUTH) DIGLIPUR - MES</option>';
        } elseif ($value == 55) {
            $data .= '<option value="138">GE HADDO - MES</option>';
            $data .= '<option value="139">GE MINNIE BAY PORTBLAIR - MES</option>';
        } elseif ($value == 56) {
            $data .= '<option value="138">GE (I) 866 EWS - MES</option>';
        } elseif ($value == 60) {
            $data .= '<option value="139">GE(AF) BANGALORE - MES</option>';
            $data .= '<option value="335">GE (AF) MARATHALLI - MES</option>';
            $data .= '<option value="336">GE (AF)(P) BANGALORE - MES</option>';
            $data .= '<option value="337">GE(AF) SDI and ASTE BANGALORE - MES</option>';
            $data .= '<option value="140">GE (AF) TAMBARAM - MES</option>';
        } elseif ($value == 61) {
            $data .= '<option value="141">GE AFA HYDERABAD - MES</option>';
            $data .= '<option value="142">GE(AF)BIDAR - MES</option>';
            $data .= '<option value="143">GE(AF)HAKIMPET HYDERABAD - MES</option>';
        } elseif ($value == 62) {
            $data .= '<option value="144">AGE(I)(AF) CHIMNEY HILLS BANGALORE - MES</option>';
            $data .= '<option value="145">AGE (I) COIMBATORE - MES</option>';
            $data .= '<option value="146">GE (AF) SAMBRA - BELGAUM - MES</option>';
            $data .= '<option value="147">GE(AF) Yelehanka - MES</option>';
            $data .= '<option value="148">GE (Maint) (AF) Jalahalli - MES</option>';
        } elseif ($value == 63) {
            $data .= '<option value="149">GE (AF) SULUR - MES</option>';
            $data .= '<option value="150">GE (AF) TANJAVUR - MES</option>';
            $data .= '<option value="151">GE(AF)TRIVANDRUM - MES</option>';
            $data .= '<option value="152">GE(P) (AF) SULUR - MES</option>';
        } elseif ($value == 65) {
            $data .= '<option value="153">GE B/R AF CHAKERI - MES</option>';
            $data .= '<option value="154">GE E/M AF CHAKERI - MES</option>';
        } elseif ($value == 66) {
            $data .= '<option value="155">GE (AF) AMLA - MES</option>';
            $data .= '<option value="338">GE (AF) OJHAR - MES</option>';
        } elseif ($value == 67) {
            $data .= '<option value="339">AGE(I) MANAURI - MES</option>';
            $data .= '<option value="156">GE (AF) MC Chandigarh - MES</option>';
            $data .= '<option value="157">GE (AF) TUGHLAKABAD - MES</option>';
        } elseif ($value == 68) {
            $data .= '<option value="158">GE (I) (AF) NAGPUR - MES</option>';
        } elseif ($value == 70) {
            $data .= '<option value="340">AGE(I) DHANA - MES</option>';
            $data .= '<option value="159">GE BHOPAL - MES</option>';
            $data .= '<option value="160">GE DRONACHAL - MES</option>';
            $data .= '<option value="161">GE NASIRABAD - MES</option>';
            $data .= '<option value="162">GE SAUGOR - MES</option>';
        } elseif ($value == 71) {
            $data .= '<option value="163">AGE (I) TALBEHAT - MES</option>';
            $data .= '<option value="164">GE BABINA - MES</option>';
            $data .= '<option value="165">GE GWALIOR - MES</option>';
            $data .= '<option value="166">GE JHANSI - MES</option>';
        } elseif ($value == 72) {
            $data .= '<option value="167">GE KAMPTEE - MES</option>';
            $data .= '<option value="168">GE PULGAON - MES</option>';
        } elseif ($value == 73) {
            $data .= '<option value="169">GE (CENTRAL) BANGALORE - MES</option>';
            $data .= '<option value="170">GE(NORTH) BANGALORE - MES</option>';
            $data .= '<option value="171">GE (P) BANGALORE - MES</option>';
            $data .= '<option value="172">GE (SOUTH) BANGALORE - MES</option>';
        } elseif ($value == 74) {
            $data .= '<option value="173">GE AVADI- MES</option>';
            $data .= '<option value="174">GE CHENNAI - MES</option>';
            $data .= '<option value="175">GE ST THOMAS MOUNT - MES</option>';
        } elseif ($value == 75) {
            $data .= '<option value="179">GE GOLCONDA HYDERABAD - MES</option>';
            $data .= '<option value="180">GE (NORTH) SECUNDERABAD - MES</option>';
            $data .= '<option value="181">GE (SOUTH) SECUNDERABAD - MES</option>';
            $data .= '<option value="182">GE(UTILITY) SECUNDERABAD - MES</option>';
        } elseif ($value == 76) {
            $data .= '<option value="183">AGE(I) CANNANORE - MES</option>';
            $data .= '<option value="184">AGE(I) TRICHY - MES</option>';
            $data .= '<option value="185">GE (ARMY) TRIVANDRUM - MES</option>';
            $data .= '<option value="186">GE DSSC WELLINGTON -MES</option>';
            $data .= '<option value="187">GE WELLINGTON - MES</option>';
        } elseif ($value == 77) {
            $data .= '<option value="188">GE (I) BELGAUM - MES</option>';
        } elseif ($value == 78) {
            $data .= '<option value="189">GE (ARMY ) BARODA - MES</option>';
            $data .= '<option value="190">GE (ARMY)BHUJ - MES</option>';
            $data .= '<option value="191">GE (ARMY) JAMNAGAR - MES</option>';
            $data .= '<option value="341">GE AHMEDABAD - MES</option>';
            $data .= '<option value="342">GE GANDHINAGAR - MES</option>';
        } elseif ($value == 79) {
            $data .= '<option value="192">AGE (I) NAGTALAO - MES</option>';
            $data .= '<option value="193">AGE(I) UDAIPUR - MES</option>';
            $data .= '<option value="194">GE(A) CENTRAL JODHPUR - MES</option>';
            $data .= '<option value="195">GE(A)UTILITY JODHPUR - MES</option>';
            $data .= '<option value="196">GE BANAR - MES</option>';
            $data .= '<option value="197">GE SHIKARGARH - MES</option>';
        } elseif ($value == 80) {
            $data .= '<option value="343">GE (ARMY) BARMER - MES</option>';
            $data .= '<option value="198">GE (ARMY) JAISALMER - MES</option>';
        } elseif ($value == 81) {
            $data .= '<option value="199">GE MAINT EZHIMALA - MES</option>';
            $data .= '<option value="200">GE (P) NO 2 EZHIMALA - MES</option>';
        } elseif ($value == 82) {
            $data .= '<option value="201">GE FORT KOCHI - MES</option>';
            $data .= '<option value="202">GE (P) (NW) KOCHI - MES</option>';
        } elseif ($value == 83) {
            $data .= '<option value="203">AGE (I) AGRANI - MES</option>';
            $data .= '<option value="204">GE FORT KOCHI - MES</option>';
            $data .= '<option value="205">GE NS KOCHI - MES</option>';
            $data .= '<option value="206">GE (NW) KOCHI - MES</option>';
        } elseif ($value == 86) {
            $data .= '<option value="207">GE(NW) KARANJA - MES</option>';
            $data .= '<option value="208">GE (P) NW MUMBAI - MES</option>';
        } elseif ($value == 87) {
            $data .= '<option value="209">AGE(I) MANDOVI - MES</option>';
            $data .= '<option value="210">GE GOMANTAK - MES</option>';
            $data .= '<option value="211">GE (NW) VASCO - MES</option>';
            $data .= '<option value="212">GE (P) VASCO - MES</option>';
        } elseif ($value == 88) {
            $data .= '<option value="213">AGE (I) ASHVINI - MES</option>';
            $data .= '<option value="214">GE (NW) KUNJALI - MES</option>';
            $data .= '<option value="215">GE (NW) NAVY NAGAR - MES</option>';
            $data .= '<option value="216">GE (NW) NOFRA - MES</option>';
        } elseif ($value == 89) {
            $data .= '<option value="217">GE (NW) BHANDUP - MES</option>';
            $data .= '<option value="218">GE (NW) MANKHURD - MES</option>';
        } elseif ($value == 93) {
            $data .= '<option value="219">GE (NORTH) SANTA CRUZ - MES</option>';
            $data .= '<option value="220">GE PANAJI - MES</option>';
            $data .= '<option value="344">GE DEHU ROAD - MES</option>';
            $data .= '<option value="221">GE (WEST) COLABA - MES</option>';
        } elseif ($value == 94) {
            $data .= '<option value="222">GE DEOLALI - MES</option>';
            $data .= '<option value="223">GE (N) AHMEDNAGAR - MES</option>';
            $data .= '<option value="224">GE NASIK ROAD - MES</option>';
            $data .= '<option value="225">GE (S) AHMEDNAGAR - MES</option>';
        } elseif ($value == 95) {
            $data .= '<option value="226">GE (CENTRAL) KIRKEE - MES</option>';
            $data .= '<option value="345">GE (CME) KIRKEE - MES</option>';
            $data .= '<option value="227">GE MH AND RANGE HILLS - MES</option>';
        } elseif ($value == 96) {
            $data .= '<option value="228">GE (C) PUNE - MES</option>';
            $data .= '<option value="229">GE KHADAKVASLA - MES</option>';
            $data .= '<option value="230">GE (N) PUNE - MES</option>';
            $data .= '<option value="231">GE (S) PUNE - MES</option>';
        } elseif ($value == 97) {
            $data .= '<option value="232">GE(AF) BHUJ - MES</option>';
            $data .= '<option value="233">GE (AF) JAMNAGAR - MES</option>';
            $data .= '<option value="346">GE (AF) NALIYA NO. 1 - MES</option>';
        } elseif ($value == 98) {
            $data .= '<option value="232">GE (AF) CHILODA - MES</option>';
            $data .= '<option value="347">GE (AF) BARODA - MES</option>';
        } elseif ($value == 99) {
            $data .= '<option value="233">GE (AF) Phalodi - MES</option>';
        } elseif ($value == 100) {
            $data .= '<option value="234">GE (AF) JAMNAGAR NO.2 - MES</option>';
        } elseif ($value == 101) {
            $data .= '<option value="235">AGE (I) (AF) JAIPUR - MES</option>';
            $data .= '<option value="236">AGE (I) MOUNT ABU - MES</option>';
            $data .= '<option value="237">GE (AF) JAISALMER - MES</option>';
            $data .= '<option value="238">GE (AF) JODHPUR - MES</option>';
            $data .= '<option value="239">GE (AF) No. 2 JODHPUR - MES</option>';
            $data .= '<option value="240">GE (AF) UTTERLAI - MES</option>';
        } elseif ($value == 102) {
            $data .= '<option value="241">GE (AF) LOHOGAON - MES</option>';
            $data .= '<option value="242">GE (AF) THANE - MES</option>';
        } elseif ($value == 104) {
            $data .= '<option value="243">GE (NORTH) BATHINDA - MES</option>';
            $data .= '<option value="244">GE (SOUTH) BATHINDA - MES</option>';
            $data .= '<option value="245">GE (U) BATHINDA - MES</option>';
        } elseif ($value == 105) {
            $data .= '<option value="246">GE (ARMY) SURATGARH - MES</option>';
            $data .= '<option value="247">GE (NORTH) BIKANER - MES</option>';
            $data .= '<option value="248">GE (P) Kanesar - MES</option>';
        } elseif ($value == 106) {
            $data .= '<option value="249">GE ABOHAR -  MES</option>';
            $data .= '<option value="250">GE Faridkot - MES</option>';
            $data .= '<option value="251">GE LALGARH JATTAN - MES</option>';
            $data .= '<option value="252">GE SRIGANGANAGAR - MES</option>';
        } elseif ($value == 107) {
            $data .= '<option value="253">GE HISAR - MES</option>';
        } elseif ($value == 108) {
            $data .= '<option value="254">GE BHARATPUR - MES</option>';
            $data .= '<option value="255">GE JAIPUR - MES</option>';
            $data .= '<option value="256">GE (U) JAIPUR - MES</option>';
        } elseif ($value == 109) {
            $data .= '<option value="257">GE ALWAR - MES</option>';
            $data .= '<option value="258">GE KOTA - MES</option>';
        } elseif ($value == 110) {
            $data .= '<option value="259">GE Hisar - MES</option>';
        } elseif ($value == 112) {
            $data .= '<option value="260">GE (AF) Ambala-MES</option>';
            $data .= '<option value="261">GE (AF) Halwara-MES</option>';
            $data .= '<option value="262">GE (AF) Sarsawa-MES</option>';
        } elseif ($value == 113) {
            $data .= '<option value="263">GE(AF) BHISIANA - MES</option>';
            $data .= '<option value="264">GE (AF) Sirsa-MES</option>';
        } elseif ($value == 114) {
            $data .= '<option value="265">GE (AF) Nal-MES</option>';
            $data .= '<option value="266">GE (AF) Suratgarh-MES</option>';
        } elseif ($value == 115) {
            $data .= '<option value="267">GE (AF) Adampur-MES</option>';
            $data .= '<option value="268">GE (P) (AF) No 2 CHANDIGARH-MES</option>';
        } elseif ($value == 116) {
            $data .= '<option value="269">GE (AF) FARIDABAD - MES</option>';
            $data .= '<option value="270">GE (AF) GURGAON - MES</option>';
        } elseif ($value == 117) {
            $data .= '<option value="271">GE (AF) North Palam-MES</option>';
            $data .= '<option value="272">GE(AF) South Palam-MES</option>';
            $data .= '<option value="348">GE (P)(AF) South Palam-MES</option>';
            $data .= '<option value="273">GE (AF) Subroto Park-MES</option>';
        } elseif ($value == 118) {
            $data .= '<option value="274">GE (N) AMBALA - MES</option>';
            $data .= '<option value="275">GE (P) Ambala - MES</option>';
            $data .= '<option value="276">GE (U) AMBALA - MES</option>';
            $data .= '<option value="349">GE (S) AMBALA - MES</option>';
        } elseif ($value == 119) {
            $data .= '<option value="277">GE CHANDIGARH - MES</option>';
            $data .= '<option value="278">GE CHANDIMANDIR - MES</option>';
            $data .= '<option value="279">GE (P) CHANDIMANDIR - MES</option>';
            $data .= '<option value="350">GE (U) CHANDIMANDIR - MES</option>';
        } elseif ($value == 120) {
            $data .= '<option value="280">GE (P) DAPPAR - MES</option>';
            $data .= '<option value="281">GE (S) PATIALA - MES</option>';
            $data .= '<option value="351">GE (N) PATIALA - MES</option>';
        } elseif ($value == 121) {
            $data .= '<option value="282">GE 863 EWS - MES</option>';
            $data .= '<option value="283">GE JUTOGH - MES</option>';
            $data .= '<option value="352">GE KASAULI - MES</option>';
        } elseif ($value == 122) {
            $data .= '<option value="284">GE (CENTRAL) DELHI CANTT-MES</option>';
            $data .= '<option value="285">GE (EAST) DELHI CANTT-MES</option>';
            $data .= '<option value="286">GE (NORTH) DELHI CANTT-MES</option>';
            $data .= '<option value="287">GE (WEST) DELHI CANTT-MES</option>';
        } elseif ($value == 123) {
            $data .= '<option value="288">GE E/M BASE HOSPITAL DELHI CNATT-MES</option>';
            $data .= '<option value="289">GE E/M (RR) HOSPITAL DELHI CNATT-MES</option>';
            $data .= '<option value="290">GE NEW DELHI-MES</option>';
            $data .= '<option value="291">GE (S) NEW DELHI-MES</option>';
            $data .= '<option value="353">GE (P) WEST DELHI-MES</option>';
        } elseif ($value == 124) {
            $data .= '<option value="354">AGE (I)(U) B and R DELHI CNATT-MES</option>';
            $data .= '<option value="292">GE(U)ELECTRIC SUPPLY DELHI CANTT-MES</option>';
            $data .= '<option value="355">GE(U) P and M DELHI CNATT-MES</option>';
            $data .= '<option value="293">GE(U)WATER SUPPLY DELHI CANTT-MES</option>';
        } elseif ($value == 127) {
            $data .= '<option value="294">GE AMRITSAR - MES</option>';
            $data .= '<option value="295">GE GURDASPUR - MES</option>';
            $data .= '<option value="296">GE (NAMS) AMRITSAR - MES</option>';
        } elseif ($value == 128) {
            $data .= '<option value="297">GE (EAST) FEROZEPUR - MES</option>';
            $data .= '<option value="298">GE LUDHIANA - MES</option>';
            $data .= '<option value="299">GE (WEST) FEROZEPUR - MES</option>';
        } elseif ($value == 129) {
            $data .= '<option value="300">GE (EAST) JALANDHAR CANTT - MES</option>';
            $data .= '<option value="301">GE ENGR PARK JALANDHAR CANTT - MES</option>';
            $data .= '<option value="302">GE KAPURTHLA(P) - MES</option>';
            $data .= '<option value="303">GE (WEST) JALANDHAR CANTT - MES</option>';
        } elseif ($value == 130) {
            $data .= '<option value="304">GE JAMMU - MES</option>';
            $data .= '<option value="305">GE KALUCHAK - MES</option>';
            $data .= '<option value="306">GE SATWARI - MES</option>';
        } elseif ($value == 131) {
            $data .= '<option value="307">GE(NORTH) MAMUN - MES</option>';
            $data .= '<option value="308">GE SAMBA - MES</option>';
            $data .= '<option value="309">GE(SOUTH) MAMUN - MES</option>';
        } elseif ($value == 132) {
            $data .= '<option value="310">GE BASOLI - MES</option>';
            $data .= '<option value="311">GE (SOUTH) PATHANKOT - MES</option>';
            $data .= '<option value="312">GE (WEST) PATHANKOT - MES</option>';
        } elseif ($value == 133) {
            $data .= '<option value="313">AGE (I) DHARAMSHALA - MES</option>';
            $data .= '<option value="314">GE DALHOUSIE - MES</option>';
            $data .= '<option value="315">GE (KH) YOL - MES</option>';
            $data .= '<option value="316">GE PALAMPUR - MES</option>';
        }
        echo json_encode(['data' => $data]);
        die;
    }

    public function actionGetdata() {
        $value = $_REQUEST['value'];
        $data = '<option value="0" selected>Select</option>';
        if ($value == 1) {
            $data .= '<option value="1">Electrical</option>';
            $data .= '<option value="2">Air Conditioning</option>';
            $data .= '<option value="3">Fire Fighting</option>';
            $data .= '<option value="4">Water supply</option>';
            $data .= '<option value="5">Lifts</option>';
            $data .= '<option value="6">Cranes</option>';
            $data .= '<option value="7">DG Set</option>';
        } elseif ($value == 2) {
            $data .= '<option value="14">Cement</option>';
            $data .= '<option value="15">Reinforcement Steel</option>';
            $data .= '<option value="16">Structural Steel</option>';
            $data .= '<option value="17">Non Structural Steel</option>';
        } else {
            $data .= '<option value="8">Building</option>';
            $data .= '<option value="9">Road</option>';
            $data .= '<option value="10">Periodical</option>';
            $data .= '<option value="11">Joinery</option>';
            $data .= '<option value="12">Plumbing</option>';
        }
        echo json_encode(['data' => $data]);
        die;
    }

    public function actionGetseconddata() {
        $value = $_REQUEST['value'];
        $item = 0;
        $data = '<option value="0" selected>Select</option>';
        if ($value == 1) {
            $data .= '<option value="1">LT</option>';
            $data .= '<option value="2">HT</option>';
        } elseif ($value == 2) {
            $data .= '<option value="3">VRV Units</option>';
            $data .= '<option value="4">AC Plants</option>';
        } elseif ($value == 3) {
            $data .= '<option value="5">Pumps</option>';
            $data .= '<option value="6">MS Pipes</option>';
            $data .= '<option value="7">Motors</option>';
        } elseif ($value == 4) {
            $data .= '<option value="8">Pumps</option>';
            $data .= '<option value="9">GI Pipes</option>';
            $data .= '<option value="10">MS Pipes</option>';
            $data .= '<option value="11">Motors</option>';
            $data .= '<option value="12">NP 2</option>';
        } else {
            $item = 1;
        }

        $makes = [];
        $allmakes = [];
        if ($value) {
            $makes = \common\models\Make::find()->where(['mtype' => $value, 'status' => 1])->orderBy(['make' => SORT_ASC])->all();
        } else {
            $makes = \common\models\Make::find()->where(['status' => 1])->orderBy(['make' => SORT_ASC])->all();
        }

        if ($makes) {
            foreach ($makes as $_make) {
                $allmakes[$_make->id] = $_make->make;
            }
        } else {
            $allmakes['0'] = 'No Makes';
        }

        echo json_encode(['data' => $data, 'item' => $item, 'value' => $value, 'select' => $allmakes]);
        die;
    }

    public function actionGetthirddata() {
        $value = $_REQUEST['value'];
        $item = 0;
        $data = '<option value="0" selected>Select</option>';
        if ($value == 1) {
            $data .= '<option value="1">Cables</option>';
            $data .= '<option value="2">Lighting</option>';
            $data .= '<option value="3">Fans</option>';
            $data .= '<option value="4">Accessories</option>';
            $data .= '<option value="5">Wire</option>';
            $data .= '<option value="6">DB/MCB/MCCB/Timers</option>';
            $data .= '<option value="7">Transformers</option>';
            $data .= '<option value="8">Cable Jointing Kits</option>';
            $data .= '<option value="9">Panels</option>';
            $data .= '<option value="10">ACB</option>';
            $data .= '<option value="13">Motors</option>';
        } elseif ($value == 2) {
            $data .= '<option value="1">Cables</option>';
            $data .= '<option value="7">Transformers</option>';
            $data .= '<option value="8">Cable Jointing Kits</option>';
            $data .= '<option value="9">Panels</option>';
            $data .= '<option value="11">VCB</option>';
            $data .= '<option value="12">Substations</option>';
            $data .= '<option value="13">Motors</option>';
        } else {
            $item = 1;
        }
        echo json_encode(['data' => $data, 'item' => $item]);
        die;
    }

    public function actionGetfourdata() {
        $value = $_REQUEST['value'];
        $subone = $_REQUEST['subone'];
        $subtwo = $_REQUEST['subtwo'];
        $item = 0;
        $data = '<option value="0" selected>Select</option>';
        if (($value == 1) || ($value == 12)) {
            $data .= '<option value="1">Copper</option>';
            $data .= '<option value="2">Aluminium</option>';
        } elseif ($value == 3) {
            $data .= '<option value="6">Ceiling fans</option>';
            $data .= '<option value="7">Wall fans</option>';
            $data .= '<option value="8">Exhaust fans</option>';
        } else {
            $item = 1;
        }

        $makes = [];
        $allmakes = [];
        if ($value) {
            $makes = \common\models\Make::find()->where(['mtype' => $value, 'status' => 1])->orderBy(['make' => SORT_ASC])->all();
        } else {
            $makes = \common\models\Make::find()->where(['status' => 1])->orderBy(['make' => SORT_ASC])->all();
        }




        if ($makes) {
            foreach ($makes as $_make) {
                $allmakes[$_make->id] = $_make->make;
            }
        } else {
            $allmakes['0'] = 'No Makes';
        }

        $allsizes = [];
        if ($value == 1) {
            $sizes = [];
            if ($value) {
                $sizes = \common\models\Size::find()->where(['mtypeone' => $value, 'mtypetwo' => $subone, 'mtypethree' => $subtwo, 'status' => 1])->orderBy(['size' => SORT_ASC])->all();
                //$sizes = \common\models\Size::find()->where(['mtypeone' => $value, 'status' => 1])->orderBy(['size' => SORT_ASC])->all();
            } else {
                $sizes = \common\models\Size::find()->where(['status' => 1])->orderBy(['size' => SORT_ASC])->all();
            }


            if ($sizes) {
                foreach ($sizes as $_size) {
                    $allsizes[$_size->id] = $_size->size;
                }
            } else {
                $allsizes['0'] = 'No Sizes';
            }
        } else {
            $sizes = [];
            $sizes = \common\models\Size::find()->where(['mtypeone' => $value, 'status' => 1])->orderBy(['size' => SORT_ASC])->all();
//$sizes = \common\models\Size::find()->where(['status' => 1])->orderBy(['size' => SORT_ASC])->all();

            if ($sizes) {
                foreach ($sizes as $_size) {
                    $allsizes[$_size->id] = $_size->size;
                }
            } else {
                $allsizes['0'] = 'No Sizes';
            }
        }

        $typefit = \common\models\Fitting::find()->where(['status' => 1, 'type' => 1])->orderBy(['text' => SORT_ASC])->all();
        $capacityfit = \common\models\Fitting::find()->where(['status' => 1, 'type' => 2])->orderBy(['text' => SORT_ASC])->all();
        $accessories = \common\models\Accessories::find()->where(['status' => 1])->orderBy(['text' => SORT_ASC])->all();

        $alltypes = [];
        $allcapacities = [];
        $allaccessories = [];
        if ($typefit) {
            foreach ($typefit as $_tfit) {
                $alltypes[$_tfit->id] = $_tfit->text;
            }
        } else {
            $alltypes['0'] = 'No Types';
        }

        if ($capacityfit) {
            foreach ($capacityfit as $_cfit) {
                $allcapacities[$_cfit->id] = $_cfit->text;
            }
        } else {
            $allcapacities['0'] = 'No Capacities';
        }

        if ($accessories) {
            foreach ($accessories as $_acc) {
                $allaccessories[$_acc->id] = $_acc->text;
            }
        } else {
            $allaccessories['0'] = 'No Accessories';
        }

        echo json_encode(['data' => $data, 'item' => $item, 'select' => $allmakes, 'sizes' => $allsizes, 'types' => $alltypes, 'capacities' => $allcapacities, 'accessories' => $allaccessories]);
        die;
    }

    public function actionGetfivedata() {
        $value = $_REQUEST['value'];
        $item = 0;
        $data = '<option value="0" selected>Select</option>';
        if (($value == 1) || ($value == 2)) {
            $data .= '<option value="1">Armoured</option>';
            $data .= '<option value="2">Unarmoured</option>';
        } else {
            $item = 1;
        }
        echo json_encode(['data' => $data, 'item' => $item]);
        die;
    }

    public function actionGetsixdata() {
        $parent = $_REQUEST['parent'];
        $one = $_REQUEST['one'];
        $two = $_REQUEST['two'];
        $allsizes = [];
        $sizes = [];
        $sizes = \common\models\Size::find()->where(['mtypeone' => $parent, 'mtypetwo' => $one, 'mtypethree' => $two, 'status' => 1])->orderBy(['size' => SORT_ASC])->all();

        if ($sizes) {
            foreach ($sizes as $_size) {
                $allsizes[$_size->id] = $_size->size;
            }
        } else {
            $allsizes['0'] = 'No Sizes';
        }

        echo json_encode(['sizes' => $allsizes]);
        die;
    }

    public function actionViewItems() {
        $user = Yii::$app->user->identity;
        $tid = @$_GET['id'];
        $tenderid = '';
        $tdetails = [];
        $tender = \common\models\Tender::find()->where(['id' => $tid])->one();
        if ($tender) {
            $tenderid = $tender->tender_id;
            $tdetails = $tender;
        } else {
            return $this->redirect(array('site/tenders'));
        }

        $idetails = \common\models\ItemDetails::find()->leftJoin('items', 'itemdetails.item_id = items.id')->where(['items.tender_id' => $tid])->orderBy(['itemdetails.id' => SORT_ASC])->all();

        if (@$idetails) {
            foreach ($idetails as $idetail) {
                $descfull = '';
                $items = [];
                $items = \common\models\Item::find()->where(['id' => $idetail->item_id])->one();
                $makeids = explode(',', $idetail->make);
                $makenameall = '';
                if (@$makeids) {
                    foreach ($makeids as $mid) {
                        $makename = \common\models\Make::find()->where(['id' => $mid])->one();
                        if (@$makename) {
                            $makenameall .= '<span class="viewmake">' . $makename->make . '</span>';
                        }
                    }
                }
                $idetail->make = rtrim($makenameall, ',');
                if ($items->tenderone != '' && $items->tenderone != 0) {
                    $one = $this->actionTenderone($items->tenderone);
                    $descfull .= $one . ',';
                }
                if ($items->tendertwo != '' && $items->tendertwo != 0) {
                    $two = $this->actionTendertwo($items->tendertwo);
                    $descfull .= $two . ',';
                }
                if ($items->tenderthree != '' && $items->tenderthree != 0) {
                    $three = $this->actionTenderthree($items->tenderthree);
                    $descfull .= $three . ',';
                }
                if ($items->tenderfour != '' && $items->tenderfour != 0) {
                    $four = $this->actionTenderfour($items->tenderfour);
                    $descfull .= $four . ',';
                }
                if ($items->tenderfive != '' && $items->tenderfive != 0) {
                    $five = $this->actionTenderfive($items->tenderfive);
                    $descfull .= $five . ',';
                }
                if ($items->tendersix != '' && $items->tendersix != 0) {
                    $six = $this->actionTendersix($items->tendersix);
                    $descfull .= $six . ',';
                }
                $descfull = rtrim($descfull, ',');
                $size = \common\models\Size::find()->where(['id' => $idetail->description])->one();
                $core = $this->actionGetcore($idetail->core);
                $type = $this->actionGetfit($idetail->typefitting);
                $capacity = $this->actionGetfit($idetail->capacityfitting);
                $accone = $this->actionGetaccessory($idetail->accessoryone);
                if ($items->tenderfour == 1) {
                    $idetail->description = @$size->size . ' ' . $core . ' (' . $descfull . ')';
                } elseif ($items->tenderfour == 2) {
                    $idetail->description = @$type . ' ' . @$capacity . ' (' . $descfull . ')';
                } elseif ($items->tenderfour == 4) {
                    $idetail->description = @$accone . ' ' . @$idetail->accessorytwo . ' (' . $descfull . ')';
                } elseif ($items->tenderfour == 5) {
                    $idetail->description = @$size->size . ' (' . $descfull . ')';
                } else {
                    $idetail->description = '(' . $descfull . ')';
                }
            }
        }
        return $this->render('viewitem', [
                    'idetails' => $idetails,
                    'tid' => $tid,
                    'tname' => $tenderid,
                    'tdetails' => $tdetails
        ]);
    }

    public function actionEM() {
        $user = Yii::$app->user->identity;
        if (isset($_POST['mtypesort']) && $_POST['mtypesort'] != '') {
            $makes = \common\models\Make::find()->where(['mtype' => $_POST['mtypesort'], 'status' => 1])->orderBy(['make' => SORT_ASC])->all();
        } else {
            $makes = [];
        }
        return $this->render('makesem', [
                    'makes' => $makes
        ]);
    }

    public function actionCivil() {
        $user = Yii::$app->user->identity;
        if (isset($_POST['mtypesort']) && $_POST['mtypesort'] != '') {
            $makes = \common\models\Make::find()->where(['mtype' => $_POST['mtypesort'], 'status' => 1])->orderBy(['make' => SORT_ASC])->all();
        } else {
            $makes = [];
        }
        return $this->render('makescivil', [
                    'makes' => $makes
        ]);
    }

    public function actionSizes() {
        $user = Yii::$app->user->identity;

        if (isset($_POST)) {
            if (isset($_POST['mtypesortone']) && $_POST['mtypesortone'] != '' && $_POST['mtypesortone'] == 1) {
                $sizes = \common\models\Size::find()->where(['mtypeone' => @$_POST['mtypesortone'], 'mtypetwo' => $_POST['mtypesorttwo'], 'mtypethree' => $_POST['mtypesortthree']])->orderBy(['size' => SORT_ASC])->all();
            } else {
                $sizes = \common\models\Size::find()->where(['mtypeone' => @$_POST['mtypesortone']])->orderBy(['size' => SORT_ASC])->all();
            }
        } else {
            $sizes = [];
        }

        return $this->render('sizes', [
                    'sizes' => $sizes
        ]);
    }

    public function actionFittings() {
        $user = Yii::$app->user->identity;

        if (isset($_POST)) {
            $fittings = \common\models\Fitting::find()->where(['type' => @$_POST['mtypesortone'], 'status' => 1])->orderBy(['text' => SORT_ASC])->all();
        } else {
            $fittings = [];
        }

        return $this->render('fittings', [
                    'fittings' => $fittings
        ]);
    }

    public function actionChangecommand() {
        $user = Yii::$app->user->identity;
        if (isset($_POST['c']) && $_POST['c'] != '') {
            $tenders = \common\models\Tender::find()->where(['command' => $_POST['c']])->orderBy(['id' => SORT_DESC])->all();
        } else {
            $tenders = \common\models\Tender::find()->orderBy(['id' => SORT_DESC])->all();
        }
        return $this->redirect(array('site/approvetenders/' . $_POST['c'] . ''));
    }

    public function actionCreateMakeEm() {
        $user = Yii::$app->user->identity;
        $id = @$_GET['id'];

        if (isset($_POST['submit'])) {

            if ($_POST['id']) {
                $model = \common\models\Make::find()->where(['id' => $_POST['id']])->one();
                $model->mtype = $_POST['mtype'];
                $model->make = $_POST['make'];
                $model->email = $_POST['email'];

                $make = \common\models\Make::find()->where(['make' => $_POST['make'], 'email' => $_POST['email'], 'mtype' => $_POST['mtype']])->andWhere(['!=', 'id', $_POST['id']])->one();
                if ($make) {
                    Yii::$app->session->setFlash('error', "Make already existed");
                } else {
                    if ($model->save()) {
                        Yii::$app->session->setFlash('success', "Make successfully updated");
                    }
                }
                return $this->redirect(array('site/e-m'));
            } else {
                $model = new \common\models\Make();
                $model->mtype = $_POST['mtype'];
                $model->make = $_POST['make'];
                $model->email = $_POST['email'];
                $model->user_id = $user->UserId;
                $model->createdon = date('Y-m-d h:i:s');
                $model->status = 1;
                $make = \common\models\Make::find()->where(['make' => $_POST['make'], 'email' => $_POST['email'], 'mtype' => $_POST['mtype']])->one();
                if ($make) {
                    Yii::$app->session->setFlash('error', "Make already existed");
                } else {
                    $tender = \Yii::$app
                            ->db
                            ->createCommand()
                            ->insert('makes', $model)
                            ->execute();


                    if ($tender) {
                        Yii::$app->session->setFlash('success', "Make successfully added");
                    }
                }
                return $this->redirect(array('site/create-make-em'));
            }



            die();
        } else {
            if ($id) {
                $make = \common\models\Make::find()->where(['id' => $id])->one();
            } else {
                $make = [];
            }

            return $this->render('createmakeem', [
                        'make' => $make
            ]);
        }
    }

    public function actionCreateMakeCivil() {
        $user = Yii::$app->user->identity;
        $id = @$_GET['id'];

        if (isset($_POST['submit'])) {

            if ($_POST['id']) {
                $model = \common\models\Make::find()->where(['id' => $_POST['id']])->one();
                $model->mtype = $_POST['mtype'];
                $model->make = $_POST['make'];
                $model->email = @$_POST['email'];

                $make = \common\models\Make::find()->where(['make' => $_POST['make'], 'email' => $_POST['email'], 'mtype' => $_POST['mtype']])->andWhere(['!=', 'id', $_POST['id']])->one();
                if ($make) {
                    Yii::$app->session->setFlash('error', "Make already existed");
                } else {
                    if ($model->save()) {
                        Yii::$app->session->setFlash('success', "Make successfully updated");
                    }
                }
                return $this->redirect(array('site/civil'));
            } else {
                $model = new \common\models\Make();
                $model->mtype = $_POST['mtype'];
                $model->make = $_POST['make'];
                $model->email = @$_POST['email'];
                $model->user_id = $user->UserId;
                $model->createdon = date('Y-m-d h:i:s');
                $model->status = 1;
                $make = \common\models\Make::find()->where(['make' => $_POST['make'], 'email' => $_POST['email'], 'mtype' => $_POST['mtype']])->one();
                if ($make) {
                    Yii::$app->session->setFlash('error', "Make already existed");
                } else {
                    $tender = \Yii::$app
                            ->db
                            ->createCommand()
                            ->insert('makes', $model)
                            ->execute();


                    if ($tender) {
                        Yii::$app->session->setFlash('success', "Make successfully added");
                    }
                }
                return $this->redirect(array('site/create-make-civil'));
            }



            die();
        } else {
            if ($id) {
                $make = \common\models\Make::find()->where(['id' => $id])->one();
            } else {
                $make = [];
            }

            return $this->render('createmakecivil', [
                        'make' => $make
            ]);
        }
    }

    public function actionDeleteMake() {
        $id = $_GET['id'];
        $make = \common\models\Make::find()->where(['id' => $id])->one();
        $delete = \common\models\Make::deleteAll(['id' => $id]);
        if ($delete) {
            Yii::$app->session->setFlash('success', "Make successfully deleted");
            if ($make->mtype < 14) {
                return $this->redirect(array('site/e-m'));
            } else {
                return $this->redirect(array('site/civil'));
            }
        }
    }

    public function actionDeleteSize() {
        $id = $_GET['id'];
        $delete = \common\models\Size::deleteAll(['id' => $id]);
        if ($delete) {
            Yii::$app->session->setFlash('success', "Size successfully deleted");
            return $this->redirect(array('site/sizes'));
        }
    }

    public function actionDeleteFitting() {
        $id = $_GET['id'];
        $delete = \common\models\Fitting::deleteAll(['id' => $id]);
        if ($delete) {
            Yii::$app->session->setFlash('success', "Fitting successfully deleted");
            return $this->redirect(array('site/fittings'));
        }
    }

    public function actionCreateSize() {
        $user = Yii::$app->user->identity;
        $id = @$_GET['id'];

        if (isset($_POST['submit'])) {

            if ($_POST['id']) {
                $model = \common\models\Size::find()->where(['id' => $_POST['id']])->one();
                if (isset($_POST['mtypeone'])) {
                    $model->mtypeone = $_POST['mtypeone'];
                }
                if (isset($_POST['mtypetwo'])) {
                    $model->mtypetwo = $_POST['mtypetwo'];
                }
                if (isset($_POST['mtypethree'])) {
                    $model->mtypethree = $_POST['mtypethree'];
                }
                if (isset($_POST['mtypeone']) && $_POST['mtypeone'] == 1) {
                    $size = \common\models\Size::find()->where(['size' => $_POST['size'], 'mtypeone' => $_POST['mtypeone'], 'mtypetwo' => $_POST['mtypetwo'], 'mtypethree' => $_POST['mtypethree']])->andWhere(['!=', 'id', $_POST['id']])->one();
                } else {
                    $size = \common\models\Size::find()->where(['size' => $_POST['size'], 'mtypeone' => $_POST['mtypeone']])->andWhere(['!=', 'id', $_POST['id']])->one();
                }
                $model->size = $_POST['size'];

                if ($size) {
                    Yii::$app->session->setFlash('error', "Size already existed");
                } else {
                    if ($model->save()) {
                        Yii::$app->session->setFlash('success', "Size successfully updated");
                    }
                }
                return $this->redirect(array('site/sizes'));
            } else {
                $model = new \common\models\Size();
                if (isset($_POST['mtypeone'])) {
                    $model->mtypeone = $_POST['mtypeone'];
                }
                if (isset($_POST['mtypetwo'])) {
                    $model->mtypetwo = $_POST['mtypetwo'];
                }
                if (isset($_POST['mtypethree'])) {
                    $model->mtypethree = $_POST['mtypethree'];
                }

                $model->size = $_POST['size'];
                $model->user_id = $user->UserId;
                $model->createdon = date('Y-m-d h:i:s');
                $model->status = 1;
                if ($_POST['mtypeone'] == 1) {
                    $size = \common\models\Size::find()->where(['size' => $_POST['size'], 'mtypeone' => $_POST['mtypeone'], 'mtypetwo' => $_POST['mtypetwo'], 'mtypethree' => $_POST['mtypethree']])->one();
                } else {
                    $size = \common\models\Size::find()->where(['size' => $_POST['size'], 'mtypeone' => $_POST['mtypeone']])->one();
                }
                if ($size) {
                    Yii::$app->session->setFlash('error', "Size already existed");
                } else {
                    $sizes = \Yii::$app
                            ->db
                            ->createCommand()
                            ->insert('sizes', $model)
                            ->execute();


                    if ($sizes) {
                        Yii::$app->session->setFlash('success', "Size successfully added");
                    }
                }
                return $this->redirect(array('site/create-size'));
            }



            die();
        } else {
            if ($id) {
                $size = \common\models\Size::find()->where(['id' => $id])->one();
            } else {
                $size = [];
            }

            return $this->render('createsize', [
                        'size' => $size
            ]);
        }
    }

    public function actionCreateFitting() {
        $user = Yii::$app->user->identity;
        $id = @$_GET['id'];

        if (isset($_POST['submit'])) {

            if ($_POST['id']) {
                $model = \common\models\Fitting::find()->where(['id' => $_POST['id']])->one();

                $fitting = \common\models\Fitting::find()->where(['text' => $_POST['text'], 'type' => $_POST['mtypeone']])->andWhere(['!=', 'id', $_POST['id']])->one();
                $model->type = $_POST['mtypeone'];
                $model->text = $_POST['text'];

                if ($fitting) {
                    Yii::$app->session->setFlash('error', "Fitting already existed");
                } else {
                    if ($model->save()) {
                        Yii::$app->session->setFlash('success', "Fitting successfully updated");
                    }
                }
                return $this->redirect(array('site/fittings'));
            } else {
                $model = new \common\models\Fitting();
                $model->type = $_POST['mtypeone'];
                $model->text = $_POST['text'];
                $model->user_id = $user->UserId;
                $model->createdon = date('Y-m-d h:i:s');
                $model->status = 1;
                $fitting = \common\models\Fitting::find()->where(['text' => $_POST['text'], 'type' => $_POST['mtypeone']])->one();
                if ($fitting) {
                    Yii::$app->session->setFlash('error', "Fitting already existed");
                } else {
                    $fittings = \Yii::$app
                            ->db
                            ->createCommand()
                            ->insert('fittings', $model)
                            ->execute();


                    if ($fittings) {
                        Yii::$app->session->setFlash('success', "Fitting successfully added");
                    }
                }
                return $this->redirect(array('site/create-fitting'));
            }
            die();
        } else {
            if ($id) {
                $fitting = \common\models\Fitting::find()->where(['id' => $id])->one();
            } else {
                $fitting = [];
            }

            return $this->render('createfitting', [
                        'fitting' => $fitting
            ]);
        }
    }

    public function actionDeleteItem() {
        $id = $_GET['id'];
        $tid = $_GET['tid'];
        $iids = [];
        $items = \common\models\ItemDetails::find()->where(['id' => $id])->all();
        if (count($items)) {
            foreach ($items as $_item) {
                $iids[] = $_item->item_id;
            }
        }
        $delete = \common\models\ItemDetails::deleteAll(['id' => $id]);
        $deleteitems = \common\models\Item::deleteAll(['id' => $iids]);
        if ($delete) {
            $mdelete = \common\models\MakeDetails::deleteAll(['item_detail_id' => $id]);
            $checktender = \common\models\Item::find()->where(['tender_id' => $tid])->all();
            if ($checktender) {
                $checkstatus = \common\models\Item::find()->where(['tender_id' => $tid, 'status' => 0])->all();
                if (!$checkstatus) {
                    $tmodel = \common\models\Tender::find()->where(['id' => $tid])->one();
                    $tmodel->status = 1;
                    $tmodel->save();
                }
            } else {
                $tmodel = \common\models\Tender::find()->where(['id' => $tid])->one();
                $tmodel->status = 0;
                $tmodel->technical_status = 0;
                $tmodel->financial_status = 0;
                $tmodel->aoc_status = 0;
                $tmodel->aoc_date = '';
                $tmodel->save();
            }

            Yii::$app->session->setFlash('success', "Item successfully deleted");
            return $this->redirect(array('site/view-items/' . $tid . ''));
        }
    }

    public function actionDeleteItems() {
        $tid = $_POST['tid'];
        $iids = [];
        if (count(@$_POST["selected_id"]) > 0) {
            $all = implode(",", $_POST["selected_id"]);
            $items = \common\models\ItemDetails::find()->where(['id' => $_POST["selected_id"]])->all();
            if (count($items)) {
                foreach ($items as $_item) {
                    $iids[] = $_item->item_id;
                }
            }

            if (isset($_POST['btn_delete'])) {
                $delete = \common\models\ItemDetails::deleteAll(['id' => $_POST["selected_id"]]);
                $deleteitems = \common\models\Item::deleteAll(['id' => $iids]);
                if ($delete) {
                    $mdelete = \common\models\MakeDetails::deleteAll(['item_detail_id' => $_POST["selected_id"]]);
                    $checktender = \common\models\Item::find()->where(['tender_id' => $tid])->all();
                    if ($checktender) {
                        $checkstatus = \common\models\Item::find()->where(['tender_id' => $tid, 'status' => 0])->all();
                        if (!$checkstatus) {
                            $tmodel = \common\models\Tender::find()->where(['id' => $tid])->one();
                            $tmodel->status = 1;
                            $tmodel->save();
                        }
                    } else {
                        $tmodel = \common\models\Tender::find()->where(['id' => $tid])->one();
                        $tmodel->status = 0;
                        $tmodel->technical_status = 0;
                        $tmodel->financial_status = 0;
                        $tmodel->aoc_status = 0;
                        $tmodel->aoc_date = '';
                        $tmodel->save();
                    }
                    Yii::$app->session->setFlash('success', "Items successfully deleted");
                    return $this->redirect(array('site/view-items/' . $tid . ''));
                }
            } else {
                $ids = $_POST["selected_id"];
                if (isset($ids) && count($ids)) {
                    foreach ($ids as $_id) {
                        $itemdetail = \common\models\ItemDetails::find()->where(['id' => $_id])->one();
                        $makedetail = \common\models\MakeDetails::find()->where(['item_detail_id' => $_id])->one();
                        $itemz = \common\models\Item::find()->where(['id' => $itemdetail->item_id])->one();
                        $itemz->status = 1;
                        $makedetail->status = 1;
                        $itemdetail->status = 1;
                        $itemdetail->save();
                        $makedetail->save();
                        $itemz->save();
                    }
                }

                $checkstatus = \common\models\Item::find()->where(['tender_id' => $tid, 'status' => 0])->all();

                if (!$checkstatus) {
                    $tmodel = \common\models\Tender::find()->where(['id' => $tid])->one();
                    $tmodel->status = 1;
                    $tmodel->save();
                }

                Yii::$app->session->setFlash('success', "Items successfully approved");
                return $this->redirect(array('site/view-items/' . $tid . ''));
            }
        } else {
            Yii::$app->session->setFlash('error', "Please select the checkbox to perform action");
            return $this->redirect(array('site/view-items/' . $tid . ''));
        }
    }

    public function actionEditItem($id) {

        if (isset($_POST['submit'])) {
            $user = Yii::$app->user->identity;
            if ($_POST['id']) {
                $item = \common\models\Item::find()->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['itemdetails.id' => $id])->one();
                $model = \common\models\ItemDetails::find()->where(['id' => $_POST['id']])->one();
                $model->itemtender = @$_POST['itemtender'];
                $model->description = @$_POST['description'];
                $model->units = @$_POST['units'];
                $model->quantity = @$_POST['quantity'];
                $model->core = @$_POST['core'];
                $model->typefitting = @$_POST['typefitting'];
                $model->capacityfitting = @$_POST['capacityfitting'];
                $model->accessoryone = @$_POST['accessoryone'];
                $model->accessorytwo = @$_POST['accessorytwo'];
                $model->make = implode(',', $_POST['makes']);
                $model->makeid = @$_POST['makeid'];

                $parentitem = \common\models\Item::find()->where(['id' => $_POST['item_id']])->one();
                $parentitem->tenderone = @$_POST['tenderone'];
                $parentitem->tendertwo = @$_POST['tendertwo'];
                $parentitem->tenderthree = @$_POST['tenderthree'];
                $parentitem->tenderfour = @$_POST['tenderfour'];
                $parentitem->tenderfive = @$_POST['tenderfive'];
                $parentitem->tendersix = @$_POST['tendersix'];
                $parentitem->save();

                $mitem = \common\models\MakeDetails::deleteAll(['item_detail_id' => $_POST['id']]);

                if (@$_POST['makes']) {
                    foreach ($_POST['makes'] as $make_ids) {
                        $mdetail = new \common\models\MakeDetails();
                        $mdetail->itemtender = @$_POST['itemtender'];
                        $mdetail->description = @$_POST['description'];
                        $mdetail->units = @$_POST['units'];
                        $mdetail->quantity = @$_POST['quantity'];
                        $mdetail->core = @$_POST['core'];
                        $mdetail->typefitting = @$_POST['typefitting'];
                        $mdetail->capacityfitting = @$_POST['capacityfitting'];
                        $mdetail->accessoryone = @$_POST['accessoryone'];
                        $mdetail->accessorytwo = @$_POST['accessorytwo'];
                        $mdetail->make = $make_ids;
                        $mdetail->makeid = @$_POST['makeid'];
                        $mdetail->user_id = $user->id;
                        $mdetail->item_id = @$_POST['item_id'];
                        $mdetail->item_detail_id = @$_POST['id'];
                        $mdetail->createdon = date('Y-m-d h:i:s');
                        if ($user->group_id == 3) {
                            $mdetail->status = 0;
                        } else {
                            $mdetail->status = 1;
                        }
                        $mdetail->save();
                    }
                }


                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Item successfully updated");
                }
            }
            return $this->redirect(array('site/view-items/' . $item->tender_id . ''));
        } else {
            $user = Yii::$app->user->identity;
            $types = [];
            $capacities = [];
            $item = \common\models\Item::find()->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['itemdetails.id' => $id])->one();
            if ($item->tenderone == 1) {
                $makes = \common\models\Make::find()->where(['mtype' => $item->tenderfour, 'status' => 1])->orderBy(['make' => SORT_ASC])->all();
            } else {
                $makes = \common\models\Make::find()->where(['mtype' => $item->tendertwo, 'status' => 1])->orderBy(['make' => SORT_ASC])->all();
            }
            $idetails = \common\models\ItemDetails::find()->where(['id' => $id])->one();
            if ($item->tenderfour == 1) {
                $sizes = \common\models\Size::find()->where(['mtypeone' => $item->tenderfour, 'mtypetwo' => $item->tenderfive, 'mtypethree' => $item->tendersix, 'status' => 1])->orderBy(['LENGTH(size)' => SORT_ASC, 'size' => SORT_ASC])->all();
            } else {
                $sizes = \common\models\Size::find()->where(['mtypeone' => $item->tenderfour, 'status' => 1])->orderBy(['LENGTH(size)' => SORT_ASC, 'size' => SORT_ASC])->all();
            }
            $types = \common\models\Fitting::find()->where(['type' => 1, 'status' => 1])->orderBy(['text' => SORT_ASC])->all();
            $capacities = \common\models\Fitting::find()->where(['type' => 2, 'status' => 1])->orderBy(['LENGTH(text)' => SORT_ASC, 'text' => SORT_ASC])->all();
            $accessories = \common\models\Accessories::find()->where(['status' => 1])->orderBy(['LENGTH(text)' => SORT_ASC, 'text' => SORT_ASC])->all();
            return $this->render('edititem', [
                        'item' => $idetails,
                        'makes' => $makes,
                        'parentitems' => $item,
                        'sizes' => $sizes,
                        'types' => $types,
                        'capacities' => $capacities,
                        'accessories' => $accessories
            ]);
        }
    }

    public function actionGetmakes() {
        $user = Yii::$app->user->identity;
        $makes = [];
        $allmakes = [];
        $id = $_REQUEST['value'];
        if ($id) {
            $makes = \common\models\Make::find()->where(['mtype' => $id, 'status' => 1])->all();
        } else {
            $makes = \common\models\Make::find()->where(['status' => 1])->all();
        }

        if ($makes) {
            foreach ($makes as $_make) {
                $allmakes[$_make->id] = $_make->make;
            }
        } else {
            $allmakes['0'] = 'No Makes';
        }

        echo json_encode($allmakes);
        die;
    }

    public function actionGetsizes() {
        $user = Yii::$app->user->identity;
        $sizes = [];
        $allsizes = [];
        $parent = $_REQUEST['subparent'];
        $one = $_REQUEST['subone'];
        $two = $_REQUEST['subtwo'];
        if ($parent == 1) {
            $sizes = \common\models\Size::find()->where(['mtypeone' => $parent, 'mtypetwo' => $one, 'mtypethree' => $two, 'status' => 1])->all();
        } else {
            $sizes = \common\models\Size::find()->where(['mtypeone' => $parent, 'status' => 1])->all();
        }


        if ($sizes) {
            foreach ($sizes as $_size) {
                $allsizes[$_size->id] = $_size->size;
            }
        } else {
            $allsizes['0'] = 'No Sizes';
        }

        echo json_encode($allsizes);
        die;
    }

    public function actionGetfittings() {
        $user = Yii::$app->user->identity;
        $alltypes = [];
        $allcapacities = [];

        $types = \common\models\Fitting::find()->where(['type' => 1, 'status' => 1])->all();
        $capacities = \common\models\Fitting::find()->where(['type' => 2, 'status' => 1])->all();

        if ($types) {
            foreach ($types as $_type) {
                $alltypes[$_type->id] = $_type->text;
            }
        } else {
            $alltypes['0'] = 'No Types';
        }

        if ($capacities) {
            foreach ($capacities as $_capacity) {
                $allcapacities[$_capacity->id] = $_capacity->text;
            }
        } else {
            $allcapacities['0'] = 'No Capacities';
        }

        echo json_encode(['alltypes' => $alltypes, 'allcapacities' => $allcapacities]);
        die;
    }

    public function actionApprovetender() {
        $id = $_REQUEST['value'];
        $tender = \common\models\Tender::find()->where(['id' => $id])->one();
        $tender->status = 1;
        if ($tender->save()) {
            echo json_encode(['status' => '1']);
        } else {
            echo json_encode(['status' => '0']);
        }
        die;
    }

    public function actionGetcore($value) {
        switch ($value) {
            case "1":
                return "1 Core";
                break;
            case "2":
                return "2 Core";
                break;
            case "3":
                return "3 Core";
                break;
            case "4":
                return "3.5 Core";
                break;
            case "5":
                return "4 Core";
                break;
            case "6":
                return "5 Core";
                break;
            case "7":
                return "6 Core";
                break;
            case "8":
                return "7 Core";
                break;
            case "9":
                return "8 Core";
                break;
            case "10":
                return "10 Core";
                break;
            case "11":
                return "12 Core";
                break;
            case "12":
                return "14 Core";
                break;
            case "13":
                return "16 Core";
                break;
            case "14":
                return "19 Core";
                break;
            case "15":
                return "24 Core";
                break;
            case "16":
                return "27 Core";
                break;
            case "17":
                return "30 Core";
                break;
            case "18":
                return "37 Core";
                break;
            case "19":
                return "44 Core";
                break;
            case "20":
                return "61 Core";
                break;
            default:
                return "";
        }
    }

    public function actionGetfit($value) {
        if ($value) {
            $type = \common\models\Fitting::find()->where(['id' => $value])->one();
        } else {
            $type = [];
        }
        return @$type->text;
    }

    public function actionGetaccessory($value) {
        if ($value) {
            $type = \common\models\Accessories::find()->where(['id' => $value, 'status' => 1])->one();
        } else {
            $type = [];
        }
        return @$type->text;
    }

    public function actionGetcommand($id) {
        switch ($id) {
            case "1":
                return "ADG (CG AND PROJECT) CHENNAI AND CE (CG) GOA - MES";
                break;
            case "2":
                return "ADG (DESIGN and CONSULTANCY) PUNE - MES";
                break;
            case "3":
                return "ADG (OF and DRDO) AND CE (FY) HYDERABAD - MES";
                break;
            case "4":
                return "ADG (OF and DRDO)  AND CE (R and D) DELHI-  MES";
                break;
            case "5":
                return "ADG (OF and DRDO) AND CE (R and D) SECUNDERABAD - MES";
                break;
            case "13":
                return "ADG (Projects) AND CE (CG) Visakhapatnam - MES";
                break;
            case "6":
                return "CENTRAL COMMAND";
                break;
            case "7":
                return "EASTERN COMMAND";
                break;
            case "8":
                return "NORTHERN COMMAND";
                break;
            case "9":
                return "SOUTHERN COMMAND";
                break;
            case "10":
                return "SOUTH WESTERN COMMAND";
                break;
            case "11":
                return "WESTERN COMMAND";
                break;
            case "12":
                return "DGNP MUMBAI - MES";
                break;
            default:
                return "";
        }
    }

    public function actionGetcommandgraph($id) {
        switch ($id) {
            case "1":
                return "ADG CHENNAI";
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

    public function actionGetcebyid($id) {
        $value = $id;
        $data = [];
        $getce = [];
        $data[] = '<option value="1">CE CC AND CE (AF) ALLAHABAD - MES</option>';
        $data[] = '<option value="2">CE CC AND CE BAREILLY ZONE - MES</option>';
        $data[] = '<option value="3">CE CC AND CE JABALPUR ZONE - MES</option>';
        $data[] = '<option value="4">CE CC AND CE LUCKNOW ZONE - MES</option>';
        $data[] = '<option value="5">CE EC AND CCE (ARMY) NO 1 DINJAN - MES</option>';
        $data[] = '<option value="6">CE EC AND CCE (ARMY ) No 2 MISSAMARI - MES</option>';
        $data[] = '<option value="7">CE EC AND CCE (ARMY) NO 3 NARANGI - MES</option>';
        $data[] = '<option value="8">CEEC AND CCE (NEP) NEW DELHI - MES</option>';
        $data[] = '<option value="9">CE EC AND CE (AF) SHILLONG - MES</option>';
        $data[] = '<option value="10">CE EC AND CE KOLKATA ZONE - MES</option>';
        $data[] = '<option value="11">CE EC AND CE (NAVY) VIZAG - MES</option>';
        $data[] = '<option value="12">CE EC AND CE SHILLONG ZONE - MES</option>';
        $data[] = '<option value="13">CE EC AND CE SILIGURI ZONE - MES</option>';
        $data[] = '<option value="14">CE EC AND DGNP (VIZAG) - MES</option>';
        $data[] = '<option value="15">CE NC AND CE 31 ZONE - MES</option>';
        $data[] = '<option value="16">CE NC AND CE (AF) UDHAMPUR - MES</option>';
        $data[] = '<option value="17">CE NC AND CE LEH ZONE - MES</option>';
        $data[] = '<option value="18">CE NC AND CE UDHAMPUR ZONE - MES</option>';
        $data[] = '<option value="19">CE SC AND CE (A and N) ZONE - MES</option>';
        $data[] = '<option value="20">CESC AND CE (AF) BANGALORE - MES</option>';
        $data[] = '<option value="21">CESC AND CE (AF) NAGPUR - MES</option>';
        $data[] = '<option value="22">CESC AND CE BHOPAL ZONE  - MES</option>';
        $data[] = '<option value="23">CE SC AND CE CHENNAI ZONE  - MES</option>';
        $data[] = '<option value="24">CESC AND CE JODHPUR ZONE - MES</option>';
        $data[] = '<option value="25">CESC AND CE (NAVY) KOCHI - MES</option>';
        $data[] = '<option value="26">CESC AND CE( NAVY )MUMBAI - MES</option>';
        $data[] = '<option value="27">CE SC AND CE PUNE ZONE - MES</option>';
        $data[] = '<option value="28">CE SWC AND CE (AF) GANDHINAGAR - MES</option>';
        $data[] = '<option value="29">CE SWC AND CE BATHINDA ZONE - MES</option>';
        $data[] = '<option value="30">CE SWC AND CE JAIPUR JAIPUR - MES</option>';
        $data[] = '<option value="31">CE WC and CE(AF) WAC PALAM-MES</option>';
        $data[] = '<option value="32">CE WC AND CE CHANDIGARH ZONE - MES</option>';
        $data[] = '<option value="33">CE WC and CE DELHI ZONE-MES</option>';
        $data[] = '<option value="34">CE WC AND CE JALANDHAR ZONE - MES</option>';
        $data[] = '<option value="35">CE WC AND CE PATHANKOT ZONE - MES</option>';
        $data[] = '<option value="36">AGE (I) B/R JAKHAU - MES</option>';
        $data[] = '<option value="37">GE (CG) KOCHI - MES</option>';
        $data[] = '<option value="38">GE DAMAN - MES</option>';
        $data[] = '<option value="39">AGE (I) FY EDDUMAILARAM - MES</option>';
        $data[] = '<option value="40">GE (I) (FY) ISHAPORE - MES</option>';
        $data[] = '<option value="41">GE (I) (P) (FY) ITARSI - MES</option>';
        $data[] = '<option value="42">GE(I)(P) Fy KANPUR - MES</option>';
        $data[] = '<option value="43">GE (I) (P) FY KIRKEE - MES</option>';
        $data[] = '<option value="44">AGE(I) R and D Haldwani - MES</option>';
        $data[] = '<option value="45">AGE(I) R and D Manali - MES</option>';
        $data[] = '<option value="46">GE(I) R and D Chandipur - MES</option>';
        $data[] = '<option value="47">GE(I) R and D Dehradun - MES</option>';
        $data[] = '<option value="48">GE(I) R and D Kanpur - MES</option>';
        $data[] = '<option value="49">AGE (I) RND AVADI - MES</option>';
        $data[] = '<option value="50">AGE (I) RND KOCHI - MES</option>';
        $data[] = '<option value="51">AGE (I) RND VISHAKHAPATNAM - MES</option>';
        $data[] = '<option value="52">GE (I) RND (E) BANGALORE - MES</option>';
        $data[] = '<option value="53">GE (I) RND KANCHANBAGH - MES</option>';
        $data[] = '<option value="54">GE (I) RND PASHAN - MES</option>';
        $data[] = '<option value="55">GE (I) RND RCI HYDERABAD - MES</option>';
        $data[] = '<option value="56">GE (I) RND (W) BANGALORE - MES</option>';
        $data[] = '<option value="57">GE (CG) PORBANDAR - MES</option>';
        $data[] = '<option value="58">GE (I)(P) Fy AMBAJHARI - MES</option>';
        $data[] = '<option value="59">GE (I)(FY) AVADI - MES</option>';
        $data[] = '<option value="60">AGE(I) R and D Jodhpur - MES</option>';
        $data[] = '<option value="61">AGE(I) R and D Delhi - MES</option>';
        $data[] = '<option value="62">GE(I) R and D Chandigarh - MES</option>';
        $data[] = '<option value="63">GE (I) RND GIRINAGAR - MES</option>';


        $i = 0;
        if (!empty($data)) {
            foreach ($data as $_data) {
                $i++;
                preg_match_all('/>(.*?)</s', $_data, $matches);
                $getce[$i] = $matches['1']['0'];
            }
        }

        echo @$getce[$value];
    }

    public function actionGetcwebyid($id) {
        $value = $id;
        $data = [];
        $getcwe = [];

        $data[] = '<option value="1">CWE (AF) BAMRAULI ALLAHABAD - MES</option>';
        $data[] = '<option value="2">CWE (AF) IZATNAGAR - MES</option>';
        $data[] = '<option value="3">CWE (AF) KHERIA - MES</option>';
        $data[] = '<option value="4">CWE (AF) MAHARAJPUR - MES</option>';
        $data[] = '<option value="5">CWE BAREILLY - MES</option>';
        $data[] = '<option value="6">CWE DEHRADUN - MES</option>';
        $data[] = '<option value="7">CWE HILLS DEHRADUN -  MES</option>';
        $data[] = '<option value="8">CWE HILLS PITHORAGARH - MES</option>';
        $data[] = '<option value="9">CWE MEERUT - MES</option>';
        $data[] = '<option value="10">CWE No 2 MEERUT - MES</option>';
        $data[] = '<option value="11">CWE JABALPUR - MES</option>';
        $data[] = '<option value="12">CWE MHOW - MES</option>';
        $data[] = '<option value="13">CWE RANCHI - MES</option>';
        $data[] = '<option value="14">GE (I) GOS - MES</option>';
        $data[] = '<option value="15">CWE AGRA - MES</option>';
        $data[] = '<option value="16">CWE ALLAHABAD - MES</option>';
        $data[] = '<option value="17">CWE KANPUR - MES</option>';
        $data[] = '<option value="18">CWE LUCKNOW - MES</option>';
        $data[] = '<option value="19">CWE MATHURA</option>';
        $data[] = '<option value="20">CWE (AF) BORJAR - MES</option>';
        $data[] = '<option value="21">CWE (AF) JORHAT - MES</option>';
        $data[] = '<option value="22">CWE (AF) KALAIKUNDA - MES</option>';
        $data[] = '<option value="23">CWE (AF) PANAGARH-MES</option>';
        $data[] = '<option value="24">GE (I)(AF) SHILLONG - MES</option>';
        $data[] = '<option value="25">GE (I) (P) (AF) TEZPUR - MES</option>';
        $data[] = '<option value="26">CWE KOLKATA - MES</option>';
        $data[] = '<option value="27">CWE (P) Kolkata - MES</option>';
        $data[] = '<option value="28">CWE (SUBURB) BARRACKPORE - MES</option>';
        $data[] = '<option value="29">CWE (Navy) Chennai - MES</option>';
        $data[] = '<option value="30">CWE (P) VISHAKHAPATNAM - MES</option>';
        $data[] = '<option value="31">CWE VISAKHAPATNAM - MES</option>';
        $data[] = '<option value="32">GE (I) (DM) VISAKHAPATNAM - MES</option>';
        $data[] = '<option value="33">CWE Dinjan - MES</option>';
        $data[] = '<option value="34">CWE HQ 137 WE - MES</option>';
        $data[] = '<option value="35">CWE Shillong - MES</option>';
        $data[] = '<option value="36">CWE Tezpur - MES</option>';
        $data[] = '<option value="37">CWE BENGDUBI - MES</option>';
        $data[] = '<option value="38">CWE BINNAGURI - MES</option>';
        $data[] = '<option value="39">GE(I)(P) GANGTOK - MES</option>';
        $data[] = '<option value="40">HQ 136 WORKS ENGINEERS - MES</option>';
        $data[] = '<option value="41">133 WORKS ENGINEER - MES</option>';
        $data[] = '<option value="42">134 WORKS ENGINEER - MES</option>';
        $data[] = '<option value="43">CWE (AF) JAMMU - MES</option>';
        $data[] = '<option value="44">CWE (AF) Leh - MES</option>';
        $data[] = '<option value="45">CWE (AF) SRINAGAR - MES</option>';
        $data[] = '<option value="46">CWE KUMBATHANG - MES</option>';
        $data[] = '<option value="47">HQ 138 WORKS ENGR - MES</option>';
        $data[] = '<option value="48">135 WORKS ENGINEER - MES</option>';
        $data[] = '<option value="49">CWE DHAR ROAD - MES</option>';
        $data[] = '<option value="50">CWE RAJOURI - MES</option>';
        $data[] = '<option value="51">CWE UDHAMPUR - MES</option>';
        $data[] = '<option value="52">GE I 873 EWS - MES</option>';
        $data[] = '<option value="53">GE I ARMY DHAR ROAD - MES</option>';
        $data[] = '<option value="54">CWE No. 2 PORT BLAIR - MES</option>';
        $data[] = '<option value="55">CWE PORTBLAIR - MES</option>';
        $data[] = '<option value="56">GE (I) 866 EWS - MES</option>';
        $data[] = '<option value="57">GE (I) CAMPBELL BAY - MES</option>';
        $data[] = '<option value="58">GE (I) (P) Central-Port Blair - MES</option>';
        $data[] = '<option value="59">GE (I) (P) NORTH PORT BLAIR- MES</option>';
        $data[] = '<option value="60">CWE (AF) (NORTH) BANGALORE - MES</option>';
        $data[] = '<option value="61">CWE (AF) SECUNDERABAD - MES</option>';
        $data[] = '<option value="62">CWE (AF) (SOUTH) BANGALORE - MES</option>';
        $data[] = '<option value="63">CWE (AF) TRIVANDRUM - MES</option>';
        $data[] = '<option value="64">GE(I) Field Investigation Pune - MES</option>';
        $data[] = '<option value="65">CWE (AF) CHAKERI - MES</option>';
        $data[] = '<option value="66">CWE (AF) NAGPUR - MES</option>';
        $data[] = '<option value="67">CWE (AF) TUGALAKABAD - MES</option>';
        $data[] = '<option value="68">GE (I) (AF) NAGPUR - MES</option>';
        $data[] = '<option value="69">GE (I) (AF) OZHAR - MES</option>';
        $data[] = '<option value="70">CWE BHOPAL - MES</option>';
        $data[] = '<option value="71">CWE JHANSI - MES</option>';
        $data[] = '<option value="72">CWE NAGPUR - MES</option>';
        $data[] = '<option value="73">CWE (ARMY) BANGALORE - MES</option>';
        $data[] = '<option value="74">CWE CHENNAI - MES</option>';
        $data[] = '<option value="75">CWE SECUNDERABAD - MES</option>';
        $data[] = '<option value="76">CWE WELLINGTON - MES</option>';
        $data[] = '<option value="77">GE (I) BELGAUM - MES</option>';
        $data[] = '<option value="78">CWE AHMEDABAD - MES</option>';
        $data[] = '<option value="79">CWE(ARMY) JODHPUR - MES</option>';
        $data[] = '<option value="80">CWE JAISALMER - MES</option>';
        $data[] = '<option value="81">CWE EZHIMALA - MES</option>';
        $data[] = '<option value="82">CWE (NB) KOCHI - MES</option>';
        $data[] = '<option value="83">CWE NW KOCHI - MES</option>';
        $data[] = '<option value="84">GE (I) Navy LAKSHADWEEP - MES</option>';
        $data[] = '<option value="85">GE (I) NAVY LONAWALA - MES</option>';
        $data[] = '<option value="86">CWE NAVY KARANJA - MES</option>';
        $data[] = '<option value="87">CWE NAVY VASCO - MES</option>';
        $data[] = '<option value="88">CWE (NW) MUMBAI - MES</option>';
        $data[] = '<option value="89">CWE (SUBURB) MUMBAI - MES</option>';
        $data[] = '<option value="90">GE (I) KARWAR - MES</option>';
        $data[] = '<option value="91">GE (I) NAVY PORBANDAR - MES</option>';
        $data[] = '<option value="92">GE(I) RATNAGIRI - MES</option>';
        $data[] = '<option value="93">CWE (ARMY) MUMBAI - MES</option>';
        $data[] = '<option value="94">CWE DEOLALI - MES</option>';
        $data[] = '<option value="95">CWE KIRKEE - MES</option>';
        $data[] = '<option value="96">CWE PUNE - MES</option>';
        $data[] = '<option value="97">CWE (AF) BHUJ - MES</option>';
        $data[] = '<option value="98">CWE (AF) CHILODA - MES</option>';
        $data[] = '<option value="99">CWE (AF) Jaisalmer - MES</option>';
        $data[] = '<option value="100">CWE (AF) JAMNAGAR - MES</option>';
        $data[] = '<option value="101">CWE (AF) JODHPUR - MES</option>';
        $data[] = '<option value="102">CWE (AF) LOHOGAON - MES</option>';
        $data[] = '<option value="103">GE (I) (AF) BARODA - MES</option>';
        $data[] = '<option value="104">CWE BATHINDA - MES</option>';
        $data[] = '<option value="105">CWE BIKANER - MES</option>';
        $data[] = '<option value="106">CWE GANGANAGAR - MES</option>';
        $data[] = '<option value="107">CWE HISAR - MES</option>';
        $data[] = '<option value="108">CWE JAIPUR - MES</option>';
        $data[] = '<option value="109">CWE KOTA - MES</option>';
        $data[] = '<option value="110">CWE Mathura - MES</option>';
        $data[] = '<option value="111">GE(I) JAIPUR - MES</option>';
        $data[] = '<option value="112">CWE (AF) Ambala-MES</option>';
        $data[] = '<option value="113">CWE(AF) BHISIANA - MES</option>';
        $data[] = '<option value="114">CWE (AF) Bikaner-MES</option>';
        $data[] = '<option value="115">CWE (AF) Chandigarh-MES</option>';
        $data[] = '<option value="116">CWE (AF) GURGAON - MES</option>';
        $data[] = '<option value="117">CWE (AF) Palam-MES</option>';
        $data[] = '<option value="118">CWE AMBALA - MES</option>';
        $data[] = '<option value="119">CWE CHANDIMANDIR - MES</option>';
        $data[] = '<option value="120">CWE PATIALA - MES</option>';
        $data[] = '<option value="121">CWE SHIMLA HILLS - MES</option>';
        $data[] = '<option value="122">CWE DELHI CANTT-MES</option>';
        $data[] = '<option value="123">CWE NEW DELHI-MES</option>';
        $data[] = '<option value="124">CWE NO 2 DELHI - MES</option>';
        $data[] = '<option value="125">CWE (P) DELHI CANTT-MES</option>';
        $data[] = '<option value="126">CWE (U) DELHI CANTT-MES</option>';
        $data[] = '<option value="127">CWE AMRITSAR - MES</option>';
        $data[] = '<option value="128">CWE FEROZEPUR - MES</option>';
        $data[] = '<option value="129">CWE JALANDHAR - MES</option>';
        $data[] = '<option value="130">CWE JAMMU - MES</option>';
        $data[] = '<option value="131">CWE MAMUN - MES</option>';
        $data[] = '<option value="132">CWE PATHANKOT - MES</option>';
        $data[] = '<option value="133">CWE YOL - MES</option>';
        $data[] = '<option value="134">CWE (AF) BAGDOGRA - (AF) Shillong Zone- MES</option>';
        $data[] = '<option value="135">CWE TENGA - MES</option>';
        $data[] = '<option value="136">GE(I)(P) SILIGURI - MES</option>';
        $data[] = '<option value="137">GE(I) Project No. 1 Leh - MES</option>';
        $data[] = '<option value="138">GE (I) Navy JAMNAGAR - MES</option>';



        $i = 0;
        if (!empty($data)) {
            foreach ($data as $_data) {
                $i++;
                preg_match_all('/>(.*?)</s', $_data, $matches);
                $getcwe[$i] = $matches['1']['0'];
            }
        }

        echo @$getcwe[$value];
    }

    public function actionGetcengineerbycommand($id, $vid) {
        $value = $id;
        $finaldata = '';
        $data = [];
        if ($value == 6) {
            $data[] = '<option value="1">CE CC AND CE (AF) ALLAHABAD - MES</option>';
            $data[] = '<option value="2">CE CC AND CE BAREILLY ZONE - MES</option>';
            $data[] = '<option value="3">CE CC AND CE JABALPUR ZONE - MES</option>';
            $data[] = '<option value="4">CE CC AND CE LUCKNOW ZONE - MES</option>';
        } elseif ($value == 7) {
            $data[] = '<option value="5">CE EC AND CCE (ARMY) NO 1 DINJAN - MES</option>';
            $data[] = '<option value="6">CE EC AND CCE (ARMY ) No 2 MISSAMARI - MES</option>';
            $data[] = '<option value="7">CE EC AND CCE (ARMY) NO 3 NARANGI - MES</option>';
            $data[] = '<option value="8">CEEC AND CCE (NEP) NEW DELHI - MES</option>';
            $data[] = '<option value="9">CE EC AND CE (AF) SHILLONG - MES</option>';
            $data[] = '<option value="10">CE EC AND CE KOLKATA ZONE - MES</option>';
            $data[] = '<option value="11">CE EC AND CE (NAVY) VIZAG - MES</option>';
            $data[] = '<option value="12">CE EC AND CE SHILLONG ZONE - MES</option>';
            $data[] = '<option value="13">CE EC AND CE SILIGURI ZONE - MES</option>';
            $data[] = '<option value="14">CE EC AND DGNP (VIZAG) - MES</option>';
        } elseif ($value == 8) {
            $data[] = '<option value="15">CE NC AND CE 31 ZONE - MES</option>';
            $data[] = '<option value="16">CE NC AND CE (AF) UDHAMPUR - MES</option>';
            $data[] = '<option value="17">CE NC AND CE LEH ZONE - MES</option>';
            $data[] = '<option value="18">CE NC AND CE UDHAMPUR ZONE - MES</option>';
        } elseif ($value == 9) {
            $data[] = '<option value="19">CE SC AND CE (A and N) ZONE - MES</option>';
            $data[] = '<option value="20">CESC AND CE (AF) BANGALORE - MES</option>';
            $data[] = '<option value="21">CESC AND CE (AF) NAGPUR - MES</option>';
            $data[] = '<option value="22">CESC AND CE BHOPAL ZONE  - MES</option>';
            $data[] = '<option value="23">CE SC AND CE CHENNAI ZONE  - MES</option>';
            $data[] = '<option value="24">CESC AND CE JODHPUR ZONE - MES</option>';
            $data[] = '<option value="25">CESC AND CE (NAVY) KOCHI - MES</option>';
            $data[] = '<option value="26">CESC AND CE( NAVY )MUMBAI - MES</option>';
            $data[] = '<option value="27">CE SC AND CE PUNE ZONE - MES</option>';
        } elseif ($value == 10) {
            $data[] = '<option value="28">CE SWC AND CE (AF) GANDHINAGAR - MES</option>';
            $data[] = '<option value="29">CE SWC AND CE BATHINDA ZONE - MES</option>';
            $data[] = '<option value="30">CE SWC AND CE JAIPUR JAIPUR - MES</option>';
        } elseif ($value == 11) {
            $data[] = '<option value="31">CE WC and CE(AF) WAC PALAM-MES</option>';
            $data[] = '<option value="32">CE WC AND CE CHANDIGARH ZONE - MES</option>';
            $data[] = '<option value="33">CE WC and CE DELHI ZONE-MES</option>';
            $data[] = '<option value="34">CE WC AND CE JALANDHAR ZONE - MES</option>';
            $data[] = '<option value="35">CE WC AND CE PATHANKOT ZONE - MES</option>';
        } elseif ($value == 1) {
            $data[] = '<option value="36">AGE (I) B/R JAKHAU - MES</option>';
            $data[] = '<option value="37">GE (CG) KOCHI - MES</option>';
            $data[] = '<option value="57">GE (CG) PORBANDAR - MES</option>';
            $data[] = '<option value="38">GE DAMAN - MES</option>';
        } elseif ($value == 3) {
            $data[] = '<option value="58">GE (I)(P) Fy AMBAJHARI - MES</option>';
            $data[] = '<option value="59">GE (I)(FY) AVADI - MES</option>';
            $data[] = '<option value="39">AGE (I) FY EDDUMAILARAM - MES</option>';
            $data[] = '<option value="40">GE (I) (FY) ISHAPORE - MES</option>';
            $data[] = '<option value="41">GE (I) (P) (FY) ITARSI - MES</option>';
            $data[] = '<option value="42">GE(I)(P) Fy KANPUR - MES</option>';
            $data[] = '<option value="43">GE (I) (P) FY KIRKEE - MES</option>';
        } elseif ($value == 4) {
            $data[] = '<option value="44">AGE(I) R and D Haldwani - MES</option>';
            $data[] = '<option value="60">AGE(I) R and D Jodhpur - MES</option>';
            $data[] = '<option value="45">AGE(I) R and D Manali - MES</option>';
            $data[] = '<option value="61">AGE(I) R and D Delhi - MES</option>';
            $data[] = '<option value="62">GE(I) R and D Chandigarh - MES</option>';
            $data[] = '<option value="46">GE(I) R and D Chandipur - MES</option>';
            $data[] = '<option value="47">GE(I) R and D Dehradun - MES</option>';
            $data[] = '<option value="48">GE(I) R and D Kanpur - MES</option>';
        } elseif ($value == 5) {
            $data[] = '<option value="49">AGE (I) RND AVADI - MES</option>';
            $data[] = '<option value="50">AGE (I) RND KOCHI - MES</option>';
            $data[] = '<option value="51">AGE (I) RND VISHAKHAPATNAM - MES</option>';
            $data[] = '<option value="52">GE (I) RND (E) BANGALORE - MES</option>';
            $data[] = '<option value="53">GE (I) RND KANCHANBAGH - MES</option>';
            $data[] = '<option value="63">GE (I) RND GIRINAGAR - MES</option>';
            $data[] = '<option value="54">GE (I) RND PASHAN - MES</option>';
            $data[] = '<option value="55">GE (I) RND RCI HYDERABAD - MES</option>';
            $data[] = '<option value="56">GE (I) RND (W) BANGALORE - MES</option>';
        } elseif ($value == 13) {
            $data[] = '<option value="64">GE (I)(CG) Chennai - MES</option>';
        }

        $i = 0;
        if (!empty($data)) {
            foreach ($data as $_data) {
                $i++;
                preg_match_all('/"(.*?)"/s', $_data, $matches);
                preg_match_all('/>(.*?)</s', $_data, $mvalue);
                if ($matches['1']['0'] == $vid) {
                    $newarr[] = '<option value="' . $vid . '" selected>' . $mvalue['1']['0'] . '</option>';
                } else {
                    $newarr[] = $_data;
                }
            }
        }

        if (!empty($newarr)) {
            foreach ($newarr as $_newarr) {
                $finaldata .= $_newarr;
            }
        }
        echo $finaldata;
    }

    public function actionGetcengineerbycommandview($id, $vid) {
        $user = Yii::$app->user->identity;
        $value = $id;
        $finaldata = '';
        $data = [];
        $valuefinal = '';
        if ($value == 6) {
            $data[] = '<option value="1">CE CC AND CE (AF) ALLAHABAD - MES</option>';
            $data[] = '<option value="2">CE CC AND CE BAREILLY ZONE - MES</option>';
            $data[] = '<option value="3">CE CC AND CE JABALPUR ZONE - MES</option>';
            $data[] = '<option value="4">CE CC AND CE LUCKNOW ZONE - MES</option>';
        } elseif ($value == 7) {
            $data[] = '<option value="5">CE EC AND CCE (ARMY) NO 1 DINJAN - MES</option>';
            $data[] = '<option value="6">CE EC AND CCE (ARMY ) No 2 MISSAMARI - MES</option>';
            $data[] = '<option value="7">CE EC AND CCE (ARMY) NO 3 NARANGI - MES</option>';
            $data[] = '<option value="8">CEEC AND CCE (NEP) NEW DELHI - MES</option>';
            $data[] = '<option value="9">CE EC AND CE (AF) SHILLONG - MES</option>';
            $data[] = '<option value="10">CE EC AND CE KOLKATA ZONE - MES</option>';
            $data[] = '<option value="11">CE EC AND CE (NAVY) VIZAG - MES</option>';
            $data[] = '<option value="12">CE EC AND CE SHILLONG ZONE - MES</option>';
            $data[] = '<option value="13">CE EC AND CE SILIGURI ZONE - MES</option>';
            $data[] = '<option value="14">CE EC AND DGNP (VIZAG) - MES</option>';
        } elseif ($value == 8) {
            $data[] = '<option value="15">CE NC AND CE 31 ZONE - MES</option>';
            $data[] = '<option value="16">CE NC AND CE (AF) UDHAMPUR - MES</option>';
            $data[] = '<option value="17">CE NC AND CE LEH ZONE - MES</option>';
            $data[] = '<option value="18">CE NC AND CE UDHAMPUR ZONE - MES</option>';
        } elseif ($value == 9) {
            $data[] = '<option value="19">CE SC AND CE (A and N) ZONE - MES</option>';
            $data[] = '<option value="20">CESC AND CE (AF) BANGALORE - MES</option>';
            $data[] = '<option value="21">CESC AND CE (AF) NAGPUR - MES</option>';
            $data[] = '<option value="22">CESC AND CE BHOPAL ZONE  - MES</option>';
            $data[] = '<option value="23">CE SC AND CE CHENNAI ZONE  - MES</option>';
            $data[] = '<option value="24">CESC AND CE JODHPUR ZONE - MES</option>';
            $data[] = '<option value="25">CESC AND CE (NAVY) KOCHI - MES</option>';
            $data[] = '<option value="26">CESC AND CE( NAVY )MUMBAI - MES</option>';
            $data[] = '<option value="27">CE SC AND CE PUNE ZONE - MES</option>';
        } elseif ($value == 10) {
            $data[] = '<option value="28">CE SWC AND CE (AF) GANDHINAGAR - MES</option>';
            $data[] = '<option value="29">CE SWC AND CE BATHINDA ZONE - MES</option>';
            $data[] = '<option value="30">CE SWC AND CE JAIPUR JAIPUR - MES</option>';
        } elseif ($value == 11) {
            $data[] = '<option value="31">CE WC and CE(AF) WAC PALAM-MES</option>';
            $data[] = '<option value="32">CE WC AND CE CHANDIGARH ZONE - MES</option>';
            $data[] = '<option value="33">CE WC and CE DELHI ZONE-MES</option>';
            $data[] = '<option value="34">CE WC AND CE JALANDHAR ZONE - MES</option>';
            $data[] = '<option value="35">CE WC AND CE PATHANKOT ZONE - MES</option>';
        } elseif ($value == 1) {
            $data[] = '<option value="36">AGE (I) B/R JAKHAU - MES</option>';
            $data[] = '<option value="37">GE (CG) KOCHI - MES</option>';
            $data[] = '<option value="57">GE (CG) PORBANDAR - MES</option>';
            $data[] = '<option value="38">GE DAMAN - MES</option>';
        } elseif ($value == 3) {
            $data[] = '<option value="58">GE (I)(P) Fy AMBAJHARI - MES</option>';
            $data[] = '<option value="59">GE (I)(FY) AVADI - MES</option>';
            $data[] = '<option value="39">AGE (I) FY EDDUMAILARAM - MES</option>';
            $data[] = '<option value="40">GE (I) (FY) ISHAPORE - MES</option>';
            $data[] = '<option value="41">GE (I) (P) (FY) ITARSI - MES</option>';
            $data[] = '<option value="42">GE(I)(P) Fy KANPUR - MES</option>';
            $data[] = '<option value="43">GE (I) (P) FY KIRKEE - MES</option>';
        } elseif ($value == 4) {
            $data[] = '<option value="44">AGE(I) R and D Haldwani - MES</option>';
            $data[] = '<option value="60">AGE(I) R and D Jodhpur - MES</option>';
            $data[] = '<option value="45">AGE(I) R and D Manali - MES</option>';
            $data[] = '<option value="61">AGE(I) R and D Delhi - MES</option>';
            $data[] = '<option value="62">GE(I) R and D Chandigarh - MES</option>';
            $data[] = '<option value="46">GE(I) R and D Chandipur - MES</option>';
            $data[] = '<option value="47">GE(I) R and D Dehradun - MES</option>';
            $data[] = '<option value="48">GE(I) R and D Kanpur - MES</option>';
        } elseif ($value == 5) {
            $data[] = '<option value="49">AGE (I) RND AVADI - MES</option>';
            $data[] = '<option value="50">AGE (I) RND KOCHI - MES</option>';
            $data[] = '<option value="51">AGE (I) RND VISHAKHAPATNAM - MES</option>';
            $data[] = '<option value="52">GE (I) RND (E) BANGALORE - MES</option>';
            $data[] = '<option value="53">GE (I) RND KANCHANBAGH - MES</option>';
            $data[] = '<option value="63">GE (I) RND GIRINAGAR - MES</option>';
            $data[] = '<option value="54">GE (I) RND PASHAN - MES</option>';
            $data[] = '<option value="55">GE (I) RND RCI HYDERABAD - MES</option>';
            $data[] = '<option value="56">GE (I) RND (W) BANGALORE - MES</option>';
        }

        $i = 0;
        if (!empty($data)) {
            foreach ($data as $_data) {
                $i++;
                preg_match_all('/"(.*?)"/s', $_data, $matches);
                preg_match_all('/>(.*?)</s', $_data, $mvalue);
                $data = ['cid' => $matches['1']['0'], 'command' => $value, 'text' => $mvalue['1']['0'], 'user_id' => $user->UserId, 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];
                $querydata = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('cengineers', $data)
                        ->execute();
            }
        }
        die;
    }

    public function actionGetcwengineerbyce($id, $vid) {
        $value = $id;
        $finaldata = '';
        $data = [];
        if ($value == 1) {
            $data[] = '<option value="1">CWE (AF) BAMRAULI ALLAHABAD - MES</option>';
            $data[] = '<option value="2">CWE (AF) IZATNAGAR - MES</option>';
            $data[] = '<option value="3">CWE (AF) KHERIA - MES</option>';
            $data[] = '<option value="4">CWE (AF) MAHARAJPUR - MES</option>';
        } elseif ($value == 2) {
            $data[] = '<option value="5">CWE BAREILLY - MES</option>';
            $data[] = '<option value="6">CWE DEHRADUN - MES</option>';
            $data[] = '<option value="7">CWE HILLS DEHRADUN -  MES</option>';
            $data[] = '<option value="8">CWE HILLS PITHORAGARH - MES</option>';
            $data[] = '<option value="9">CWE MEERUT - MES</option>';
            $data[] = '<option value="10">CWE No 2 MEERUT - MES</option>';
        } elseif ($value == 3) {
            $data[] = '<option value="11">CWE JABALPUR - MES</option>';
            $data[] = '<option value="12">CWE MHOW - MES</option>';
            $data[] = '<option value="13">CWE RANCHI - MES</option>';
            $data[] = '<option value="14">GE (I) GOS - MES</option>';
        } elseif ($value == 4) {
            $data[] = '<option value="15">CWE AGRA - MES</option>';
            $data[] = '<option value="16">CWE ALLAHABAD - MES</option>';
            $data[] = '<option value="17">CWE KANPUR - MES</option>';
            $data[] = '<option value="18">CWE LUCKNOW - MES</option>';
            $data[] = '<option value="19">CWE MATHURA</option>';
        } elseif ($value == 9) {
            $data[] = '<option value="134">CWE (AF) BAGDOGRA - (AF) Shillong Zone- MES</option>';
            $data[] = '<option value="20">CWE (AF) BORJAR - MES</option>';
            $data[] = '<option value="21">CWE (AF) JORHAT - MES</option>';
            $data[] = '<option value="22">CWE (AF) KALAIKUNDA - MES</option>';
            $data[] = '<option value="23">CWE (AF) PANAGARH-MES</option>';
            $data[] = '<option value="24">GE (I)(AF) SHILLONG - MES</option>';
            $data[] = '<option value="25">GE (I) (P) (AF) TEZPUR - MES</option>';
        } elseif ($value == 10) {
            $data[] = '<option value="26">CWE KOLKATA - MES</option>';
            $data[] = '<option value="27">CWE (P) Kolkata - MES</option>';
            $data[] = '<option value="28">CWE (SUBURB) BARRACKPORE - MES</option>';
        } elseif ($value == 11) {
            $data[] = '<option value="29">CWE (Navy) Chennai - MES</option>';
            $data[] = '<option value="30">CWE (P) VISHAKHAPATNAM - MES</option>';
            $data[] = '<option value="31">CWE VISAKHAPATNAM - MES</option>';
            $data[] = '<option value="32">GE (I) (DM) VISAKHAPATNAM - MES</option>';
        } elseif ($value == 12) {
            $data[] = '<option value="33">CWE Dinjan - MES</option>';
            $data[] = '<option value="34">CWE HQ 137 WE - MES</option>';
            $data[] = '<option value="35">CWE Shillong - MES</option>';
            $data[] = '<option value="36">CWE Tezpur - MES</option>';
        } elseif ($value == 13) {
            $data[] = '<option value="37">CWE BENGDUBI - MES</option>';
            $data[] = '<option value="38">CWE BINNAGURI - MES</option>';
            $data[] = '<option value="135">CWE TENGA - MES</option>';
            $data[] = '<option value="136">GE(I)(P) SILIGURI - MES</option>';
            $data[] = '<option value="39">GE(I)(P) GANGTOK - MES</option>';
            $data[] = '<option value="40">HQ 136 WORKS ENGINEERS - MES</option>';
        } elseif ($value == 15) {
            $data[] = '<option value="41">133 WORKS ENGINEER - MES</option>';
            $data[] = '<option value="42">134 WORKS ENGINEER - MES</option>';
        } elseif ($value == 16) {
            $data[] = '<option value="43">CWE (AF) JAMMU - MES</option>';
            $data[] = '<option value="44">CWE (AF) Leh - MES</option>';
            $data[] = '<option value="45">CWE (AF) SRINAGAR - MES</option>';
        } elseif ($value == 17) {
            $data[] = '<option value="46">CWE KUMBATHANG - MES</option>';
            $data[] = '<option value="137">GE(I) Project No. 1 Leh - MES</option>';
            $data[] = '<option value="47">HQ 138 WORKS ENGR - MES</option>';
        } elseif ($value == 18) {
            $data[] = '<option value="48">135 WORKS ENGINEER - MES</option>';
            $data[] = '<option value="49">CWE DHAR ROAD - MES</option>';
            $data[] = '<option value="50">CWE RAJOURI - MES</option>';
            $data[] = '<option value="51">CWE UDHAMPUR - MES</option>';
            $data[] = '<option value="52">GE I 873 EWS - MES</option>';
            $data[] = '<option value="53">GE I ARMY DHAR ROAD - MES</option>';
        } elseif ($value == 19) {
            $data[] = '<option value="54">CWE No. 2 PORT BLAIR - MES</option>';
            $data[] = '<option value="55">CWE PORTBLAIR - MES</option>';
            $data[] = '<option value="56">GE (I) 866 EWS - MES</option>';
            $data[] = '<option value="57">GE (I) CAMPBELL BAY - MES</option>';
            $data[] = '<option value="58">GE (I) (P) Central-Port Blair - MES</option>';
            $data[] = '<option value="59">GE (I) (P) NORTH PORT BLAIR- MES</option>';
        } elseif ($value == 20) {
            $data[] = '<option value="60">CWE (AF) (NORTH) BANGALORE - MES</option>';
            $data[] = '<option value="61">CWE (AF) SECUNDERABAD - MES</option>';
            $data[] = '<option value="62">CWE (AF) (SOUTH) BANGALORE - MES</option>';
            $data[] = '<option value="63">CWE (AF) TRIVANDRUM - MES</option>';
            $data[] = '<option value="64">GE(I) Field Investigation Pune - MES</option>';
        } elseif ($value == 21) {
            $data[] = '<option value="65">CWE (AF) CHAKERI - MES</option>';
            $data[] = '<option value="66">CWE (AF) NAGPUR - MES</option>';
            $data[] = '<option value="67">CWE (AF) TUGALAKABAD - MES</option>';
            $data[] = '<option value="68">GE (I) (AF) NAGPUR - MES</option>';
            $data[] = '<option value="69">GE (I) (AF) OZHAR - MES</option>';
        } elseif ($value == 22) {
            $data[] = '<option value="70">CWE BHOPAL - MES</option>';
            $data[] = '<option value="71">CWE JHANSI - MES</option>';
            $data[] = '<option value="72">CWE NAGPUR - MES</option>';
        } elseif ($value == 23) {
            $data[] = '<option value="73">CWE (ARMY) BANGALORE - MES</option>';
            $data[] = '<option value="74">CWE CHENNAI - MES</option>';
            $data[] = '<option value="75">CWE SECUNDERABAD - MES</option>';
            $data[] = '<option value="76">CWE WELLINGTON - MES</option>';
            $data[] = '<option value="77">GE (I) BELGAUM - MES</option>';
        } elseif ($value == 24) {
            $data[] = '<option value="78">CWE AHMEDABAD - MES</option>';
            $data[] = '<option value="79">CWE(ARMY) JODHPUR - MES</option>';
            $data[] = '<option value="80">CWE JAISALMER - MES</option>';
        } elseif ($value == 25) {
            $data[] = '<option value="81">CWE EZHIMALA - MES</option>';
            $data[] = '<option value="82">CWE (NB) KOCHI - MES</option>';
            $data[] = '<option value="83">CWE NW KOCHI - MES</option>';
            $data[] = '<option value="138">GE (I) Navy JAMNAGAR - MES</option>';
            $data[] = '<option value="84">GE (I) Navy LAKSHADWEEP - MES</option>';
            $data[] = '<option value="85">GE (I) NAVY LONAWALA - MES</option>';
        } elseif ($value == 26) {
            $data[] = '<option value="86">CWE NAVY KARANJA - MES</option>';
            $data[] = '<option value="87">CWE NAVY VASCO - MES</option>';
            $data[] = '<option value="88">CWE (NW) MUMBAI - MES</option>';
            $data[] = '<option value="89">CWE (SUBURB) MUMBAI - MES</option>';
            $data[] = '<option value="90">GE (I) KARWAR - MES</option>';
            $data[] = '<option value="91">GE (I) NAVY PORBANDAR - MES</option>';
            $data[] = '<option value="92">GE(I) RATNAGIRI - MES</option>';
        } elseif ($value == 27) {
            $data[] = '<option value="93">CWE (ARMY) MUMBAI - MES</option>';
            $data[] = '<option value="94">CWE DEOLALI - MES</option>';
            $data[] = '<option value="95">CWE KIRKEE - MES</option>';
            $data[] = '<option value="96">CWE PUNE - MES</option>';
        } elseif ($value == 28) {
            $data[] = '<option value="97">CWE (AF) BHUJ - MES</option>';
            $data[] = '<option value="98">CWE (AF) CHILODA - MES</option>';
            $data[] = '<option value="99">CWE (AF) Jaisalmer - MES</option>';
            $data[] = '<option value="100">CWE (AF) JAMNAGAR - MES</option>';
            $data[] = '<option value="101">CWE (AF) JODHPUR - MES</option>';
            $data[] = '<option value="102">CWE (AF) LOHOGAON - MES</option>';
            $data[] = '<option value="103">GE (I) (AF) BARODA - MES</option>';
        } elseif ($value == 29) {
            $data[] = '<option value="104">CWE BATHINDA - MES</option>';
            $data[] = '<option value="105">CWE BIKANER - MES</option>';
            $data[] = '<option value="106">CWE GANGANAGAR - MES</option>';
            $data[] = '<option value="139">GE (I) (P) NO 2 BATHINDA - MES</option>';
        } elseif ($value == 30) {
            $data[] = '<option value="107">CWE HISAR - MES</option>';
            $data[] = '<option value="108">CWE JAIPUR - MES</option>';
            $data[] = '<option value="109">CWE KOTA - MES</option>';
            $data[] = '<option value="110">CWE Mathura - MES</option>';
            $data[] = '<option value="111">GE(I) JAIPUR - MES</option>';
        } elseif ($value == 31) {
            $data[] = '<option value="112">CWE (AF) Ambala-MES</option>';
            $data[] = '<option value="113">CWE(AF) BHISIANA - MES</option>';
            $data[] = '<option value="114">CWE (AF) Bikaner-MES</option>';
            $data[] = '<option value="115">CWE (AF) Chandigarh-MES</option>';
            $data[] = '<option value="116">CWE (AF) GURGAON - MES</option>';
            $data[] = '<option value="117">CWE (AF) Palam-MES</option>';
            $data[] = '<option value="140">GE (I)(AF) Gurgaon-MES</option>';
        } elseif ($value == 32) {
            $data[] = '<option value="118">CWE AMBALA - MES</option>';
            $data[] = '<option value="119">CWE CHANDIMANDIR - MES</option>';
            $data[] = '<option value="120">CWE PATIALA - MES</option>';
            $data[] = '<option value="121">CWE SHIMLA HILLS - MES</option>';
        } elseif ($value == 33) {
            $data[] = '<option value="122">CWE DELHI CANTT-MES</option>';
            $data[] = '<option value="123">CWE NEW DELHI-MES</option>';
            $data[] = '<option value="124">CWE NO 2 DELHI - MES</option>';
            $data[] = '<option value="125">CWE (P) DELHI CANTT-MES</option>';
            $data[] = '<option value="126">CWE (U) DELHI CANTT-MES</option>';
        } elseif ($value == 34) {
            $data[] = '<option value="127">CWE AMRITSAR - MES</option>';
            $data[] = '<option value="128">CWE FEROZEPUR - MES</option>';
            $data[] = '<option value="129">CWE JALANDHAR - MES</option>';
        } elseif ($value == 35) {
            $data[] = '<option value="130">CWE JAMMU - MES</option>';
            $data[] = '<option value="131">CWE MAMUN - MES</option>';
            $data[] = '<option value="132">CWE PATHANKOT - MES</option>';
            $data[] = '<option value="133">CWE YOL - MES</option>';
        }
        $i = 0;
        if (!empty($data)) {
            foreach ($data as $_data) {
                $i++;
                preg_match_all('/"(.*?)"/s', $_data, $matches);
                preg_match_all('/>(.*?)</s', $_data, $mvalue);
                if ($matches['1']['0'] == $vid) {
                    $newarr[] = '<option value="' . $vid . '" selected>' . $mvalue['1']['0'] . '</option>';
                } else {
                    $newarr[] = $_data;
                }
            }
        }

        if (!empty($newarr)) {
            foreach ($newarr as $_newarr) {
                $finaldata .= $_newarr;
            }
        }
        echo $finaldata;
    }

    public function actionGetcwengineerbyceview($id, $vid) {
        $user = Yii::$app->user->identity;
        $value = $id;
        $finaldata = '';
        $data = [];
        $valuefinal = '';
        if ($value == 1) {
            $data[] = '<option value="1">CWE (AF) BAMRAULI ALLAHABAD - MES</option>';
            $data[] = '<option value="2">CWE (AF) IZATNAGAR - MES</option>';
            $data[] = '<option value="3">CWE (AF) KHERIA - MES</option>';
            $data[] = '<option value="4">CWE (AF) MAHARAJPUR - MES</option>';
        } elseif ($value == 2) {
            $data[] = '<option value="5">CWE BAREILLY - MES</option>';
            $data[] = '<option value="6">CWE DEHRADUN - MES</option>';
            $data[] = '<option value="7">CWE HILLS DEHRADUN -  MES</option>';
            $data[] = '<option value="8">CWE HILLS PITHORAGARH - MES</option>';
            $data[] = '<option value="9">CWE MEERUT - MES</option>';
            $data[] = '<option value="10">CWE No 2 MEERUT - MES</option>';
        } elseif ($value == 3) {
            $data[] = '<option value="11">CWE JABALPUR - MES</option>';
            $data[] = '<option value="12">CWE MHOW - MES</option>';
            $data[] = '<option value="13">CWE RANCHI - MES</option>';
            $data[] = '<option value="14">GE (I) GOS - MES</option>';
        } elseif ($value == 4) {
            $data[] = '<option value="15">CWE AGRA - MES</option>';
            $data[] = '<option value="16">CWE ALLAHABAD - MES</option>';
            $data[] = '<option value="17">CWE KANPUR - MES</option>';
            $data[] = '<option value="18">CWE LUCKNOW - MES</option>';
            $data[] = '<option value="19">CWE MATHURA</option>';
        } elseif ($value == 9) {
            $data[] = '<option value="134">CWE (AF) BAGDOGRA - (AF) Shillong Zone- MES</option>';
            $data[] = '<option value="20">CWE (AF) BORJAR - MES</option>';
            $data[] = '<option value="21">CWE (AF) JORHAT - MES</option>';
            $data[] = '<option value="22">CWE (AF) KALAIKUNDA - MES</option>';
            $data[] = '<option value="23">CWE (AF) PANAGARH-MES</option>';
            $data[] = '<option value="24">GE (I)(AF) SHILLONG - MES</option>';
            $data[] = '<option value="25">GE (I) (P) (AF) TEZPUR - MES</option>';
        } elseif ($value == 10) {
            $data[] = '<option value="26">CWE KOLKATA - MES</option>';
            $data[] = '<option value="27">CWE (P) Kolkata - MES</option>';
            $data[] = '<option value="28">CWE (SUBURB) BARRACKPORE - MES</option>';
        } elseif ($value == 11) {
            $data[] = '<option value="29">CWE (Navy) Chennai - MES</option>';
            $data[] = '<option value="30">CWE (P) VISHAKHAPATNAM - MES</option>';
            $data[] = '<option value="31">CWE VISAKHAPATNAM - MES</option>';
            $data[] = '<option value="32">GE (I) (DM) VISAKHAPATNAM - MES</option>';
        } elseif ($value == 12) {
            $data[] = '<option value="33">CWE Dinjan - MES</option>';
            $data[] = '<option value="34">CWE HQ 137 WE - MES</option>';
            $data[] = '<option value="35">CWE Shillong - MES</option>';
            $data[] = '<option value="36">CWE Tezpur - MES</option>';
        } elseif ($value == 13) {
            $data[] = '<option value="37">CWE BENGDUBI - MES</option>';
            $data[] = '<option value="38">CWE BINNAGURI - MES</option>';
            $data[] = '<option value="135">CWE TENGA - MES</option>';
            $data[] = '<option value="136">GE(I)(P) SILIGURI - MES</option>';
            $data[] = '<option value="39">GE(I)(P) GANGTOK - MES</option>';
            $data[] = '<option value="40">HQ 136 WORKS ENGINEERS - MES</option>';
        } elseif ($value == 15) {
            $data[] = '<option value="41">133 WORKS ENGINEER - MES</option>';
            $data[] = '<option value="42">134 WORKS ENGINEER - MES</option>';
        } elseif ($value == 16) {
            $data[] = '<option value="43">CWE (AF) JAMMU - MES</option>';
            $data[] = '<option value="44">CWE (AF) Leh - MES</option>';
            $data[] = '<option value="45">CWE (AF) SRINAGAR - MES</option>';
        } elseif ($value == 17) {
            $data[] = '<option value="46">CWE KUMBATHANG - MES</option>';
            $data[] = '<option value="137">GE(I) Project No. 1 Leh - MES</option>';
            $data[] = '<option value="47">HQ 138 WORKS ENGR - MES</option>';
        } elseif ($value == 18) {
            $data[] = '<option value="48">135 WORKS ENGINEER - MES</option>';
            $data[] = '<option value="49">CWE DHAR ROAD - MES</option>';
            $data[] = '<option value="50">CWE RAJOURI - MES</option>';
            $data[] = '<option value="51">CWE UDHAMPUR - MES</option>';
            $data[] = '<option value="52">GE I 873 EWS - MES</option>';
            $data[] = '<option value="53">GE I ARMY DHAR ROAD - MES</option>';
        } elseif ($value == 19) {
            $data[] = '<option value="54">CWE No. 2 PORT BLAIR - MES</option>';
            $data[] = '<option value="55">CWE PORTBLAIR - MES</option>';
            $data[] = '<option value="56">GE (I) 866 EWS - MES</option>';
            $data[] = '<option value="57">GE (I) CAMPBELL BAY - MES</option>';
            $data[] = '<option value="58">GE (I) (P) Central-Port Blair - MES</option>';
            $data[] = '<option value="59">GE (I) (P) NORTH PORT BLAIR- MES</option>';
        } elseif ($value == 20) {
            $data[] = '<option value="60">CWE (AF) (NORTH) BANGALORE - MES</option>';
            $data[] = '<option value="61">CWE (AF) SECUNDERABAD - MES</option>';
            $data[] = '<option value="62">CWE (AF) (SOUTH) BANGALORE - MES</option>';
            $data[] = '<option value="63">CWE (AF) TRIVANDRUM - MES</option>';
            $data[] = '<option value="64">GE(I) Field Investigation Pune - MES</option>';
        } elseif ($value == 21) {
            $data[] = '<option value="65">CWE (AF) CHAKERI - MES</option>';
            $data[] = '<option value="66">CWE (AF) NAGPUR - MES</option>';
            $data[] = '<option value="67">CWE (AF) TUGALAKABAD - MES</option>';
            $data[] = '<option value="68">GE (I) (AF) NAGPUR - MES</option>';
            $data[] = '<option value="69">GE (I) (AF) OZHAR - MES</option>';
        } elseif ($value == 22) {
            $data[] = '<option value="70">CWE BHOPAL - MES</option>';
            $data[] = '<option value="71">CWE JHANSI - MES</option>';
            $data[] = '<option value="72">CWE NAGPUR - MES</option>';
        } elseif ($value == 23) {
            $data[] = '<option value="73">CWE (ARMY) BANGALORE - MES</option>';
            $data[] = '<option value="74">CWE CHENNAI - MES</option>';
            $data[] = '<option value="75">CWE SECUNDERABAD - MES</option>';
            $data[] = '<option value="76">CWE WELLINGTON - MES</option>';
            $data[] = '<option value="77">GE (I) BELGAUM - MES</option>';
        } elseif ($value == 24) {
            $data[] = '<option value="78">CWE AHMEDABAD - MES</option>';
            $data[] = '<option value="79">CWE(ARMY) JODHPUR - MES</option>';
            $data[] = '<option value="80">CWE JAISALMER - MES</option>';
        } elseif ($value == 25) {
            $data[] = '<option value="81">CWE EZHIMALA - MES</option>';
            $data[] = '<option value="82">CWE (NB) KOCHI - MES</option>';
            $data[] = '<option value="83">CWE NW KOCHI - MES</option>';
            $data[] = '<option value="138">GE (I) Navy JAMNAGAR - MES</option>';
            $data[] = '<option value="84">GE (I) Navy LAKSHADWEEP - MES</option>';
            $data[] = '<option value="85">GE (I) NAVY LONAWALA - MES</option>';
        } elseif ($value == 26) {
            $data[] = '<option value="86">CWE NAVY KARANJA - MES</option>';
            $data[] = '<option value="87">CWE NAVY VASCO - MES</option>';
            $data[] = '<option value="88">CWE (NW) MUMBAI - MES</option>';
            $data[] = '<option value="89">CWE (SUBURB) MUMBAI - MES</option>';
            $data[] = '<option value="90">GE (I) KARWAR - MES</option>';
            $data[] = '<option value="91">GE (I) NAVY PORBANDAR - MES</option>';
            $data[] = '<option value="92">GE(I) RATNAGIRI - MES</option>';
        } elseif ($value == 27) {
            $data[] = '<option value="93">CWE (ARMY) MUMBAI - MES</option>';
            $data[] = '<option value="94">CWE DEOLALI - MES</option>';
            $data[] = '<option value="95">CWE KIRKEE - MES</option>';
            $data[] = '<option value="96">CWE PUNE - MES</option>';
        } elseif ($value == 28) {
            $data[] = '<option value="97">CWE (AF) BHUJ - MES</option>';
            $data[] = '<option value="98">CWE (AF) CHILODA - MES</option>';
            $data[] = '<option value="99">CWE (AF) Jaisalmer - MES</option>';
            $data[] = '<option value="100">CWE (AF) JAMNAGAR - MES</option>';
            $data[] = '<option value="101">CWE (AF) JODHPUR - MES</option>';
            $data[] = '<option value="102">CWE (AF) LOHOGAON - MES</option>';
            $data[] = '<option value="103">GE (I) (AF) BARODA - MES</option>';
        } elseif ($value == 29) {
            $data[] = '<option value="104">CWE BATHINDA - MES</option>';
            $data[] = '<option value="105">CWE BIKANER - MES</option>';
            $data[] = '<option value="106">CWE GANGANAGAR - MES</option>';
            $data[] = '<option value="139">GE (I) (P) NO 2 BATHINDA - MES</option>';
        } elseif ($value == 30) {
            $data[] = '<option value="107">CWE HISAR - MES</option>';
            $data[] = '<option value="108">CWE JAIPUR - MES</option>';
            $data[] = '<option value="109">CWE KOTA - MES</option>';
            $data[] = '<option value="110">CWE Mathura - MES</option>';
            $data[] = '<option value="111">GE(I) JAIPUR - MES</option>';
        } elseif ($value == 31) {
            $data[] = '<option value="112">CWE (AF) Ambala-MES</option>';
            $data[] = '<option value="113">CWE(AF) BHISIANA - MES</option>';
            $data[] = '<option value="114">CWE (AF) Bikaner-MES</option>';
            $data[] = '<option value="115">CWE (AF) Chandigarh-MES</option>';
            $data[] = '<option value="116">CWE (AF) GURGAON - MES</option>';
            $data[] = '<option value="117">CWE (AF) Palam-MES</option>';
            $data[] = '<option value="140">GE (I)(AF) Gurgaon-MES</option>';
        } elseif ($value == 32) {
            $data[] = '<option value="118">CWE AMBALA - MES</option>';
            $data[] = '<option value="119">CWE CHANDIMANDIR - MES</option>';
            $data[] = '<option value="120">CWE PATIALA - MES</option>';
            $data[] = '<option value="121">CWE SHIMLA HILLS - MES</option>';
        } elseif ($value == 33) {
            $data[] = '<option value="122">CWE DELHI CANTT-MES</option>';
            $data[] = '<option value="123">CWE NEW DELHI-MES</option>';
            $data[] = '<option value="124">CWE NO 2 DELHI - MES</option>';
            $data[] = '<option value="125">CWE (P) DELHI CANTT-MES</option>';
            $data[] = '<option value="126">CWE (U) DELHI CANTT-MES</option>';
        } elseif ($value == 34) {
            $data[] = '<option value="127">CWE AMRITSAR - MES</option>';
            $data[] = '<option value="128">CWE FEROZEPUR - MES</option>';
            $data[] = '<option value="129">CWE JALANDHAR - MES</option>';
        } elseif ($value == 35) {
            $data[] = '<option value="130">CWE JAMMU - MES</option>';
            $data[] = '<option value="131">CWE MAMUN - MES</option>';
            $data[] = '<option value="132">CWE PATHANKOT - MES</option>';
            $data[] = '<option value="133">CWE YOL - MES</option>';
        }
        $i = 0;
        if (!empty($data)) {
            foreach ($data as $_data) {
                $i++;
                preg_match_all('/"(.*?)"/s', $_data, $matches);
                preg_match_all('/>(.*?)</s', $_data, $mvalue);
                $data = ['cid' => $matches['1']['0'], 'cengineer' => $value, 'text' => $mvalue['1']['0'], 'user_id' => $user->UserId, 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];
                $querydata = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('cwengineers', $data)
                        ->execute();
            }
        }

        die();
    }

    public function actionGetgengineerbycwe($id, $vid) {
        $value = $id;
        $data = [];
        $finaldata = '';
        if ($value == 1) {
            $data[] = '<option value="1">GE (AF) BAMRAULI - MES</option>';
            $data[] = '<option value="2">GE (AF) BIHTA</option>';
            $data[] = '<option value="3">GE(AF)BKT - MES</option>';
            $data[] = '<option value="4">GE (AF) GORAKHPUR</option>';
        } elseif ($value == 2) {
            $data[] = '<option value="5">GE(AF)IZATNAGAR - MES</option>';
            $data[] = '<option value="317">GE(P)(AF)BKT - MES</option>';
        } elseif ($value == 3) {
            $data[] = '<option value="6">GE (AF) TECH AREA KHERIA - MES</option>';
            $data[] = '<option value="318">GE (AF) ADM AREA KHERIA - MES</option>';
            $data[] = '<option value="319">AGE (AF) (I) SONEGAON - MES</option>';
        } elseif ($value == 4) {
            $data[] = '<option value="7">GE (AF) (ADM AREA) MAHARAJPUR - MES</option>';
            $data[] = '<option value="8">GE (AF) (TECH AREA) MAHARAJPUR - MES</option>';
            $data[] = '<option value="9">GE (P) AF MAHARAJPUR - MES</option>';
        } elseif ($value == 5) {
            $data[] = '<option value="10">GE(EAST) BAREILLY - MES</option>';
            $data[] = '<option value="11">GE (P) BAREILLY - MES</option>';
            $data[] = '<option value="12">GE(WEST) BAREILLY - MES</option>';
        } elseif ($value == 6) {
            $data[] = '<option value="13">GE DEHRADUN - MES</option>';
            $data[] = '<option value="320">GE (P)DEHRADUN - MES</option>';
            $data[] = '<option value="14">GE PREMNAGAR - MES</option>';
        } elseif ($value == 7) {
            $data[] = '<option value="15">AGE(I) Raiwala - MES</option>';
            $data[] = '<option value="16">GE LANSDOWNE -MES</option>';
            $data[] = '<option value="17">GE (MES) Clement Town - MES</option>';
        } elseif ($value == 8) {
            $data[] = '<option value="18">GE Pithoragarh - MES</option>';
            $data[] = '<option value="321">GE 871EWS - MES</option>';
            $data[] = '<option value="19">GE RANIKHET - MES</option>';
        } elseif ($value == 9) {
            $data[] = '<option value="20">GE (N) MEERUT - MES</option>';
            $data[] = '<option value="322">GE (S) MEERUT - MES</option>';
            $data[] = '<option value="21">GE (U) EM Meerut - MES</option>';
        } elseif ($value == 10) {
            $data[] = '<option value="22">GE ROORKEE - MES</option>';
            $data[] = '<option value="23">GE (SOUTH) MEERUT - MES</option>';
        } elseif ($value == 11) {
            $data[] = '<option value="24">AGE (I) PACHMARHI - MES</option>';
            $data[] = '<option value="25">AGE (I) RAIPUR - MES</option>';
            $data[] = '<option value="26">GE (E) JABALPUR - MES</option>';
            $data[] = '<option value="27">GE (W) JABALPUR - MES</option>';
        } elseif ($value == 12) {
            $data[] = '<option value="28">GE AWC - MES</option>';
            $data[] = '<option value="29>GE (Maint) Inf School - MES</option>';
            $data[] = '<option value="323>GE (P) Inf School - MES</option>';
            $data[] = '<option value="324>GE MCTE - MES</option>';
        } elseif ($value == 13) {
            $data[] = '<option value="30">GE DANAPUR - MES</option>';
            $data[] = '<option value="31">GE DIPATOLI - MES</option>';
            $data[] = '<option value="32">GE (P) GAYA - MES</option>';
            $data[] = '<option value="33">GE RAMGARH - MES</option>';
            $data[] = '<option value="34">GE RANCHI - MES</option>';
        } elseif ($value == 14) {
            $data[] = '<option value="35">GE I GOPALPUR ON SEA - MES</option>';
        } elseif ($value == 15) {
            $data[] = '<option value="36">GE (E) AGRA - MES</option>';
            $data[] = '<option value="37">GE (W) AGRA - MES</option>';
        } elseif ($value == 16) {
            $data[] = '<option value="38">GE (E) ALLAHABAD - MES</option>';
            $data[] = '<option value="39">GE FAIZABAD - MES</option>';
            $data[] = '<option value="40">GE(P) ALLAHABAD - MES</option>';
            $data[] = '<option value="41">GE (W) ALLAHABAD - MES</option>';
        } elseif ($value == 17) {
            $data[] = '<option value="42">GE FATEHGARH - MES</option>';
            $data[] = '<option value="325">GE MES KANPUR - MES</option>';
        } elseif ($value == 18) {
            $data[] = '<option value="43">GE(EAST)LUCKNOW - MES</option>';
            $data[] = '<option value="44">GE(E/M)LUCKNOW - MES</option>';
            $data[] = '<option value="45">GE(WEST)LUCKNOW - MES</option>';
        } elseif ($value == 19) {
            $data[] = '<option value="46">GE (E) MATHURA - MES</option>';
            $data[] = '<option value="47">GE (W) MATHURA - MES</option>';
        } elseif ($value == 20) {
            $data[] = '<option value="48">AGE (I) (AF) DIGARU - MES</option>';
            $data[] = '<option value="49">AGE (I) KUMBHIRGRAM - MES</option>';
            $data[] = '<option value="50">GE (AF) BAGDOGRA - MES</option>';
            $data[] = '<option value="51">GE (AF) BORJAR - MES</option>';
            $data[] = '<option value="52">GE (AF) HASIMARA - MES</option>';
        } elseif ($value == 21) {
            $data[] = '<option value="53">GE (AF) CHABUA - MES</option>';
            $data[] = '<option value="54">GE (AF) JORHAT - MES</option>';
            $data[] = '<option value="326">GE (AF) MOHANBARI - MES</option>';
            $data[] = '<option value="55">GE (AF) TEZPUR - MES</option>';
        } elseif ($value == 22) {
            $data[] = '<option value="56">GE (AF) BARRACKPORE - MES</option>';
            $data[] = '<option value="57">GE (AF) KALAIKUNDA - MES</option>';
        } elseif ($value == 23) {
            $data[] = '<option value="58">AGE (I) (AF) SINGHARSI-MES</option>';
            $data[] = '<option value="59">GE (AF) PURNEA-MES</option>';
            $data[] = '<option value="327">GE (AF) PANAGARH-MES</option>';
        } elseif ($value == 26) {
            $data[] = '<option value="60">GE ALIPORE - MES</option>';
            $data[] = '<option value="61">GE(CENTRAL) KOLKATA - MES</option>';
            $data[] = '<option value="62">GE FORT WILLIAM KOLKATA - MES</option>';
        } elseif ($value == 27) {
            $data[] = '<option value="63">GE (P) (NAVY AND CG) KOLKATA - MES</option>';
        } elseif ($value == 28) {
            $data[] = '<option value="64">GE BARRACKPORE - MES</option>';
            $data[] = '<option value="65">GE PANAGARH - MES</option>';
        } elseif ($value == 29) {
            $data[] = '<option value="66">GE(MAINT) ARAKKONAM - MES</option>';
            $data[] = '<option value="67">GE(NAVY)CHENNAI - MES</option>';
            $data[] = '<option value="68">GETHIRUNALVELI - MES</option>';
        } elseif ($value == 30) {
            $data[] = '<option value="69">GE (N and CG) Bhubaneshwar - MES</option>';
            $data[] = '<option value="70">GE (P) CHILKA - MES</option>';
            $data[] = '<option value="71">GE (P) (NAVY) KALINGA - MES</option>';
            $data[] = '<option value="72">GE (P) (NAVY) VISAKHAPATNAM - MES</option>';
        } elseif ($value == 31) {
            $data[] = '<option value="73">GE NAVAL BASE VISAKHAPATNAM - MES</option>';
            $data[] = '<option value="74">GE NAVAL DEPOT VISAKHAPATNAM - MES</option>';
            $data[] = '<option value="75">GE NAVAL SERVICES VISAKHAPATNAM - MES</option>';
            $data[] = '<option value="76">GE UTILITY -II VISAKHAPATNAM - MES</option>';
        } elseif ($value == 32) {
            $data[] = '<option value="78">GE I DM VIZAG - MES</option>';
        } elseif ($value == 33) {
            $data[] = '<option value="79">AGE (I) Lekhapani - MES</option>';
            $data[] = '<option value="80">GE Dinjan - MES</option>';
            $data[] = '<option value="81">GE Jorhat - MES</option>';
        } elseif ($value == 34) {
            $data[] = '<option value="82">AGE (I) AGARTALA -MES</option>';
            $data[] = '<option value="83">AGE (I) Zakhama - MES</option>';
            $data[] = '<option value="84">GE 868 EWS - MES</option>';
            $data[] = '<option value="85">GE 869 EWS - MES</option>';
            $data[] = '<option value="86">GE 872 EWS - MES</option>';
            $data[] = '<option value="87">GE SILCHAR - MES</option>';
        } elseif ($value == 35) {
            $data[] = '<option value="88">GE GUWAHATI - MES</option>';
            $data[] = '<option value="89">GE NARANGI - MES</option>';
            $data[] = '<option value="90">GE (P) SHILLONG  - MES</option>';
            $data[] = '<option value="91">GE SHILLONG - MES</option>';
        } elseif ($value == 36) {
            $data[] = '<option value="92">AGE (I) Rangia - MES</option>';
            $data[] = '<option value="93">AGE I TAWANG - MES</option>';
            $data[] = '<option value="94">GE 859 EWS - MES</option>';
            $data[] = '<option value="95">GE Missamari - MES</option>';
            $data[] = '<option value="96">GE (North) Tezpur - MES</option>';
            $data[] = '<option value="97">GE (South) Tezpur - MES</option>';
            $data[] = '<option value="98">GE TAWANG - MES</option>';
        } elseif ($value == 37) {
            $data[] = '<option value="106">GE BENGDUBI - MES</option>';
        } elseif ($value == 38) {
            $data[] = '<option value="107">GE (N) BINNAGURI - MES</option>';
            $data[] = '<option value="108">GE (S) BINNAGURI - MES</option>';
            $data[] = '<option value="109">GE SEVOKE ROAD - MES</option>';
        } elseif ($value == 135) {
            $data[] = '<option value="328">GE MISSAMARI - MES</option>';
        } elseif ($value == 40) {
            $data[] = '<option value="110">AGE (I) DARJEELING - MES</option>';
            $data[] = '<option value="111">GE 867 EWS - MES</option>';
            $data[] = '<option value="112">GEGANGTOK - MES</option>';
            $data[] = '<option value="113">GE SUKNA - MES</option>';
        } elseif ($value == 41) {
            $data[] = '<option value="114">GE 864 EWS - MES</option>';
            $data[] = '<option value="115">GE 874 EWS - MES</option>';
            $data[] = '<option value="116">GE 969 EWS - MES</option>';
        } elseif ($value == 42) {
            $data[] = '<option value="117">AGE (I) CIF (K) - MES</option>';
            $data[] = '<option value="118">GE 861 EWS - MES</option>';
            $data[] = '<option value="119">GE 970 EWS - MES</option>';
        } elseif ($value == 43) {
            $data[] = '<option value="120">GE (AF) JAMMU - MES</option>';
            $data[] = '<option value="121">GE (AF) PATHANKOT - MES</option>';
            $data[] = '<option value="122">GE (AF) UDHAMPUR - MES</option>';
        } elseif ($value == 45) {
            $data[] = '<option value="123">GE (AF) ARANTIPAR - MES</option>';
            $data[] = '<option value="124">GE (AF) LEH - MES</option>';
            $data[] = '<option value="329">GE (AF) THOISE - MES</option>';
            $data[] = '<option value="125">GE(AF)SRINAGAR - MES</option>';
        } elseif ($value == 46) {
            $data[] = '<option value="330">GE KARGIL - MES</option>';
            $data[] = '<option value="331">GE KHUMBATHANG - MES</option>';
        } elseif ($value == 47) {
            $data[] = '<option value="126">GE 865 EWS - MES</option>';
            $data[] = '<option value="332">GE 860 EWS - MES</option>';
            $data[] = '<option value="127">GE PARTAPUR - MES</option>';
            $data[] = '<option value="128">GE (P) NO 2LEH - MES</option>';
        } elseif ($value == 48) {
            $data[] = '<option value="129">GE NAGROTA - MES</option>';
            $data[] = '<option value="333">AGE(I)CIF(U) - MES</option>';
            $data[] = '<option value="130">GE (N) AKHNOOR - MES</option>';
            $data[] = '<option value="131">GE (S) AKHNOOR - MES</option>';
        } elseif ($value == 50) {
            $data[] = '<option value="132">GE 862 EWS - MES</option>';
        } elseif ($value == 51) {
            $data[] = '<option value="133">GE(NORTH) UDHAMPUR - MES</option>';
            $data[] = '<option value="134">GE(SOUTH) UDHAMPUR - MES</option>';
            $data[] = '<option value="135">GE (U) UDHAMPUR - MES</option>';
        } elseif ($value == 54) {
            $data[] = '<option value="136">GE BRICHGUNJ - MES</option>';
            $data[] = '<option value="334">GE (P) CENTRAL - MES</option>';
            $data[] = '<option value="137">GE (SOUTH) DIGLIPUR - MES</option>';
        } elseif ($value == 55) {
            $data[] = '<option value="138">GE HADDO - MES</option>';
            $data[] = '<option value="139">GE MINNIE BAY PORTBLAIR - MES</option>';
        } elseif ($value == 56) {
            $data[] = '<option value="138">GE (I) 866 EWS - MES</option>';
        } elseif ($value == 60) {
            $data[] = '<option value="139">GE(AF) BANGALORE - MES</option>';
            $data[] = '<option value="335">GE (AF) MARATHALLI - MES</option>';
            $data[] = '<option value="336">GE (AF)(P) BANGALORE - MES</option>';
            $data[] = '<option value="337">GE(AF) SDI and ASTE BANGALORE - MES</option>';
            $data[] = '<option value="140">GE (AF) TAMBARAM - MES</option>';
        } elseif ($value == 61) {
            $data[] = '<option value="141">GE AFA HYDERABAD - MES</option>';
            $data[] = '<option value="142">GE(AF)BIDAR - MES</option>';
            $data[] = '<option value="143">GE(AF)HAKIMPET HYDERABAD - MES</option>';
        } elseif ($value == 62) {
            $data[] = '<option value="144">AGE(I)(AF) CHIMNEY HILLS BANGALORE - MES</option>';
            $data[] = '<option value="145">AGE (I) COIMBATORE - MES</option>';
            $data[] = '<option value="146">GE (AF) SAMBRA - BELGAUM - MES</option>';
            $data[] = '<option value="147">GE(AF) Yelehanka - MES</option>';
            $data[] = '<option value="148">GE (Maint) (AF) Jalahalli - MES</option>';
        } elseif ($value == 63) {
            $data[] = '<option value="149">GE (AF) SULUR - MES</option>';
            $data[] = '<option value="150">GE (AF) TANJAVUR - MES</option>';
            $data[] = '<option value="151">GE(AF)TRIVANDRUM - MES</option>';
            $data[] = '<option value="152">GE(P) (AF) SULUR - MES</option>';
        } elseif ($value == 65) {
            $data[] = '<option value="153">GE B/R AF CHAKERI - MES</option>';
            $data[] = '<option value="154">GE E/M AF CHAKERI - MES</option>';
        } elseif ($value == 66) {
            $data[] = '<option value="155">GE (AF) AMLA - MES</option>';
            $data[] = '<option value="338">GE (AF) OJHAR - MES</option>';
        } elseif ($value == 67) {
            $data[] = '<option value="339">AGE(I) MANAURI - MES</option>';
            $data[] = '<option value="156">GE (AF) MC Chandigarh - MES</option>';
            $data[] = '<option value="157">GE (AF) TUGHLAKABAD - MES</option>';
        } elseif ($value == 68) {
            $data[] = '<option value="158">GE (I) (AF) NAGPUR - MES</option>';
        } elseif ($value == 70) {
            $data[] = '<option value="340">AGE(I) DHANA - MES</option>';
            $data[] = '<option value="159">GE BHOPAL - MES</option>';
            $data[] = '<option value="160">GE DRONACHAL - MES</option>';
            $data[] = '<option value="161">GE NASIRABAD - MES</option>';
            $data[] = '<option value="162">GE SAUGOR - MES</option>';
        } elseif ($value == 71) {
            $data[] = '<option value="163">AGE (I) TALBEHAT - MES</option>';
            $data[] = '<option value="164">GE BABINA - MES</option>';
            $data[] = '<option value="165">GE GWALIOR - MES</option>';
            $data[] = '<option value="166">GE JHANSI - MES</option>';
        } elseif ($value == 72) {
            $data[] = '<option value="167">GE KAMPTEE - MES</option>';
            $data[] = '<option value="168">GE PULGAON - MES</option>';
        } elseif ($value == 73) {
            $data[] = '<option value="169">GE (CENTRAL) BANGALORE - MES</option>';
            $data[] = '<option value="170">GE(NORTH) BANGALORE - MES</option>';
            $data[] = '<option value="171">GE (P) BANGALORE - MES</option>';
            $data[] = '<option value="172">GE (SOUTH) BANGALORE - MES</option>';
        } elseif ($value == 74) {
            $data[] = '<option value="173">GE AVADI- MES</option>';
            $data[] = '<option value="174">GE CHENNAI - MES</option>';
            $data[] = '<option value="175">GE ST THOMAS MOUNT - MES</option>';
        } elseif ($value == 75) {
            $data[] = '<option value="179">GE GOLCONDA HYDERABAD - MES</option>';
            $data[] = '<option value="180">GE (NORTH) SECUNDERABAD - MES</option>';
            $data[] = '<option value="181">GE (SOUTH) SECUNDERABAD - MES</option>';
            $data[] = '<option value="182">GE(UTILITY) SECUNDERABAD - MES</option>';
        } elseif ($value == 76) {
            $data[] = '<option value="183">AGE(I) CANNANORE - MES</option>';
            $data[] = '<option value="184">AGE(I) TRICHY - MES</option>';
            $data[] = '<option value="185">GE (ARMY) TRIVANDRUM - MES</option>';
            $data[] = '<option value="186">GE DSSC WELLINGTON -MES</option>';
            $data[] = '<option value="187">GE WELLINGTON - MES</option>';
        } elseif ($value == 77) {
            $data[] = '<option value="188">GE (I) BELGAUM - MES</option>';
        } elseif ($value == 78) {
            $data[] = '<option value="189">GE (ARMY ) BARODA - MES</option>';
            $data[] = '<option value="190">GE (ARMY)BHUJ - MES</option>';
            $data[] = '<option value="191">GE (ARMY) JAMNAGAR - MES</option>';
            $data[] = '<option value="341">GE AHMEDABAD - MES</option>';
            $data[] = '<option value="342">GE GANDHINAGAR - MES</option>';
        } elseif ($value == 79) {
            $data[] = '<option value="192">AGE (I) NAGTALAO - MES</option>';
            $data[] = '<option value="193">AGE(I) UDAIPUR - MES</option>';
            $data[] = '<option value="194">GE(A) CENTRAL JODHPUR - MES</option>';
            $data[] = '<option value="195">GE(A)UTILITY JODHPUR - MES</option>';
            $data[] = '<option value="196">GE BANAR - MES</option>';
            $data[] = '<option value="197">GE SHIKARGARH - MES</option>';
        } elseif ($value == 80) {
            $data[] = '<option value="343">GE (ARMY) BARMER - MES</option>';
            $data[] = '<option value="198">GE (ARMY) JAISALMER - MES</option>';
        } elseif ($value == 81) {
            $data[] = '<option value="199">GE MAINT EZHIMALA - MES</option>';
            $data[] = '<option value="200">GE (P) NO 2 EZHIMALA - MES</option>';
        } elseif ($value == 82) {
            $data[] = '<option value="201">GE FORT KOCHI - MES</option>';
            $data[] = '<option value="202">GE (P) (NW) KOCHI - MES</option>';
        } elseif ($value == 83) {
            $data[] = '<option value="203">AGE (I) AGRANI - MES</option>';
            $data[] = '<option value="204">GE FORT KOCHI - MES</option>';
            $data[] = '<option value="205">GE NS KOCHI - MES</option>';
            $data[] = '<option value="206">GE (NW) KOCHI - MES</option>';
        } elseif ($value == 86) {
            $data[] = '<option value="207">GE(NW) KARANJA - MES</option>';
            $data[] = '<option value="208">GE (P) NW MUMBAI - MES</option>';
        } elseif ($value == 87) {
            $data[] = '<option value="209">AGE(I) MANDOVI - MES</option>';
            $data[] = '<option value="210">GE GOMANTAK - MES</option>';
            $data[] = '<option value="211">GE (NW) VASCO - MES</option>';
            $data[] = '<option value="212">GE (P) VASCO - MES</option>';
        } elseif ($value == 88) {
            $data[] = '<option value="213">AGE (I) ASHVINI - MES</option>';
            $data[] = '<option value="214">GE (NW) KUNJALI - MES</option>';
            $data[] = '<option value="215">GE (NW) NAVY NAGAR - MES</option>';
            $data[] = '<option value="216">GE (NW) NOFRA - MES</option>';
        } elseif ($value == 89) {
            $data[] = '<option value="217">GE (NW) BHANDUP - MES</option>';
            $data[] = '<option value="218">GE (NW) MANKHURD - MES</option>';
        } elseif ($value == 93) {
            $data[] = '<option value="219">GE (NORTH) SANTA CRUZ - MES</option>';
            $data[] = '<option value="220">GE PANAJI - MES</option>';
            $data[] = '<option value="344">GE DEHU ROAD - MES</option>';
            $data[] = '<option value="221">GE (WEST) COLABA - MES</option>';
        } elseif ($value == 94) {
            $data[] = '<option value="222">GE DEOLALI - MES</option>';
            $data[] = '<option value="223">GE (N) AHMEDNAGAR - MES</option>';
            $data[] = '<option value="224">GE NASIK ROAD - MES</option>';
            $data[] = '<option value="225">GE (S) AHMEDNAGAR - MES</option>';
        } elseif ($value == 95) {
            $data[] = '<option value="226">GE (CENTRAL) KIRKEE - MES</option>';
            $data[] = '<option value="345">GE (CME) KIRKEE - MES</option>';
            $data[] = '<option value="227">GE MH AND RANGE HILLS - MES</option>';
        } elseif ($value == 96) {
            $data[] = '<option value="228">GE (C) PUNE - MES</option>';
            $data[] = '<option value="229">GE KHADAKVASLA - MES</option>';
            $data[] = '<option value="230">GE (N) PUNE - MES</option>';
            $data[] = '<option value="231">GE (S) PUNE - MES</option>';
        } elseif ($value == 97) {
            $data[] = '<option value="232">GE(AF) BHUJ - MES</option>';
            $data[] = '<option value="233">GE (AF) JAMNAGAR - MES</option>';
            $data[] = '<option value="346">GE (AF) NALIYA NO. 1 - MES</option>';
        } elseif ($value == 98) {
            $data[] = '<option value="232">GE (AF) CHILODA - MES</option>';
            $data[] = '<option value="347">GE (AF) BARODA - MES</option>';
        } elseif ($value == 99) {
            $data[] = '<option value="233">GE (AF) Phalodi - MES</option>';
        } elseif ($value == 100) {
            $data[] = '<option value="234">GE (AF) JAMNAGAR NO.2 - MES</option>';
        } elseif ($value == 101) {
            $data[] = '<option value="235">AGE (I) (AF) JAIPUR - MES</option>';
            $data[] = '<option value="236">AGE (I) MOUNT ABU - MES</option>';
            $data[] = '<option value="237">GE (AF) JAISALMER - MES</option>';
            $data[] = '<option value="238">GE (AF) JODHPUR - MES</option>';
            $data[] = '<option value="239">GE (AF) No. 2 JODHPUR - MES</option>';
            $data[] = '<option value="240">GE (AF) UTTERLAI - MES</option>';
        } elseif ($value == 102) {
            $data[] = '<option value="241">GE (AF) LOHOGAON - MES</option>';
            $data[] = '<option value="242">GE (AF) THANE - MES</option>';
        } elseif ($value == 104) {
            $data[] = '<option value="243">GE (NORTH) BATHINDA - MES</option>';
            $data[] = '<option value="244">GE (SOUTH) BATHINDA - MES</option>';
            $data[] = '<option value="245">GE (U) BATHINDA - MES</option>';
        } elseif ($value == 105) {
            $data[] = '<option value="246">GE (ARMY) SURATGARH - MES</option>';
            $data[] = '<option value="247">GE (NORTH) BIKANER - MES</option>';
            $data[] = '<option value="248">GE (P) Kanesar - MES</option>';
        } elseif ($value == 106) {
            $data[] = '<option value="249">GE ABOHAR -  MES</option>';
            $data[] = '<option value="250">GE Faridkot - MES</option>';
            $data[] = '<option value="251">GE LALGARH JATTAN - MES</option>';
            $data[] = '<option value="252">GE SRIGANGANAGAR - MES</option>';
        } elseif ($value == 107) {
            $data[] = '<option value="253">GE HISAR - MES</option>';
        } elseif ($value == 108) {
            $data[] = '<option value="254">GE BHARATPUR - MES</option>';
            $data[] = '<option value="255">GE JAIPUR - MES</option>';
            $data[] = '<option value="256">GE (U) JAIPUR - MES</option>';
        } elseif ($value == 109) {
            $data[] = '<option value="257">GE ALWAR - MES</option>';
            $data[] = '<option value="258">GE KOTA - MES</option>';
        } elseif ($value == 110) {
            $data[] = '<option value="259">GE Hisar - MES</option>';
        } elseif ($value == 112) {
            $data[] = '<option value="260">GE (AF) Ambala-MES</option>';
            $data[] = '<option value="261">GE (AF) Halwara-MES</option>';
            $data[] = '<option value="262">GE (AF) Sarsawa-MES</option>';
        } elseif ($value == 113) {
            $data[] = '<option value="263">GE(AF) BHISIANA - MES</option>';
            $data[] = '<option value="264">GE (AF) Sirsa-MES</option>';
        } elseif ($value == 114) {
            $data[] = '<option value="265">GE (AF) Nal-MES</option>';
            $data[] = '<option value="266">GE (AF) Suratgarh-MES</option>';
        } elseif ($value == 115) {
            $data[] = '<option value="267">GE (AF) Adampur-MES</option>';
            $data[] = '<option value="268">GE (P) (AF) No 2 CHANDIGARH-MES</option>';
        } elseif ($value == 116) {
            $data[] = '<option value="269">GE (AF) FARIDABAD - MES</option>';
            $data[] = '<option value="270">GE (AF) GURGAON - MES</option>';
        } elseif ($value == 117) {
            $data[] = '<option value="271">GE (AF) North Palam-MES</option>';
            $data[] = '<option value="272">GE(AF) South Palam-MES</option>';
            $data[] = '<option value="348">GE (P)(AF) South Palam-MES</option>';
            $data[] = '<option value="273">GE (AF) Subroto Park-MES</option>';
        } elseif ($value == 118) {
            $data[] = '<option value="274">GE (N) AMBALA - MES</option>';
            $data[] = '<option value="275">GE (P) Ambala - MES</option>';
            $data[] = '<option value="276">GE (U) AMBALA - MES</option>';
            $data[] = '<option value="349">GE (S) AMBALA - MES</option>';
        } elseif ($value == 119) {
            $data[] = '<option value="277">GE CHANDIGARH - MES</option>';
            $data[] = '<option value="278">GE CHANDIMANDIR - MES</option>';
            $data[] = '<option value="279">GE (P) CHANDIMANDIR - MES</option>';
            $data[] = '<option value="350">GE (U) CHANDIMANDIR - MES</option>';
        } elseif ($value == 120) {
            $data[] = '<option value="280">GE (P) DAPPAR - MES</option>';
            $data[] = '<option value="281">GE (S) PATIALA - MES</option>';
            $data[] = '<option value="351">GE (N) PATIALA - MES</option>';
        } elseif ($value == 121) {
            $data[] = '<option value="282">GE 863 EWS - MES</option>';
            $data[] = '<option value="283">GE JUTOGH - MES</option>';
            $data[] = '<option value="352">GE KASAULI - MES</option>';
        } elseif ($value == 122) {
            $data[] = '<option value="284">GE (CENTRAL) DELHI CANTT-MES</option>';
            $data[] = '<option value="285">GE (EAST) DELHI CANTT-MES</option>';
            $data[] = '<option value="286">GE (NORTH) DELHI CANTT-MES</option>';
            $data[] = '<option value="287">GE (WEST) DELHI CANTT-MES</option>';
        } elseif ($value == 123) {
            $data[] = '<option value="288">GE E/M BASE HOSPITAL DELHI CNATT-MES</option>';
            $data[] = '<option value="289">GE E/M (RR) HOSPITAL DELHI CNATT-MES</option>';
            $data[] = '<option value="290">GE NEW DELHI-MES</option>';
            $data[] = '<option value="291">GE (S) NEW DELHI-MES</option>';
            $data[] = '<option value="353">GE (P) WEST DELHI-MES</option>';
        } elseif ($value == 124) {
            $data[] = '<option value="354">AGE (I)(U) B and R DELHI CNATT-MES</option>';
            $data[] = '<option value="292">GE(U)ELECTRIC SUPPLY DELHI CANTT-MES</option>';
            $data[] = '<option value="355">GE(U) P and M DELHI CNATT-MES</option>';
            $data[] = '<option value="293">GE(U)WATER SUPPLY DELHI CANTT-MES</option>';
        } elseif ($value == 127) {
            $data[] = '<option value="294">GE AMRITSAR - MES</option>';
            $data[] = '<option value="295">GE GURDASPUR - MES</option>';
            $data[] = '<option value="296">GE (NAMS) AMRITSAR - MES</option>';
        } elseif ($value == 128) {
            $data[] = '<option value="297">GE (EAST) FEROZEPUR - MES</option>';
            $data[] = '<option value="298">GE LUDHIANA - MES</option>';
            $data[] = '<option value="299">GE (WEST) FEROZEPUR - MES</option>';
        } elseif ($value == 129) {
            $data[] = '<option value="300">GE (EAST) JALANDHAR CANTT - MES</option>';
            $data[] = '<option value="301">GE ENGR PARK JALANDHAR CANTT - MES</option>';
            $data[] = '<option value="302">GE KAPURTHLA(P) - MES</option>';
            $data[] = '<option value="303">GE (WEST) JALANDHAR CANTT - MES</option>';
        } elseif ($value == 130) {
            $data[] = '<option value="304">GE JAMMU - MES</option>';
            $data[] = '<option value="305">GE KALUCHAK - MES</option>';
            $data[] = '<option value="306">GE SATWARI - MES</option>';
        } elseif ($value == 131) {
            $data[] = '<option value="307">GE(NORTH) MAMUN - MES</option>';
            $data[] = '<option value="308">GE SAMBA - MES</option>';
            $data[] = '<option value="309">GE(SOUTH) MAMUN - MES</option>';
        } elseif ($value == 132) {
            $data[] = '<option value="310">GE BASOLI - MES</option>';
            $data[] = '<option value="311">GE (SOUTH) PATHANKOT - MES</option>';
            $data[] = '<option value="312">GE (WEST) PATHANKOT - MES</option>';
        } elseif ($value == 133) {
            $data[] = '<option value="313">AGE (I) DHARAMSHALA - MES</option>';
            $data[] = '<option value="314">GE DALHOUSIE - MES</option>';
            $data[] = '<option value="315">GE (KH) YOL - MES</option>';
            $data[] = '<option value="316">GE PALAMPUR - MES</option>';
        }
        $i = 0;
        if (!empty($data)) {
            foreach ($data as $_data) {
                $i++;
                preg_match_all('/"(.*?)"/s', $_data, $matches);
                preg_match_all('/>(.*?)</s', $_data, $mvalue);
                if ($matches['1']['0'] == $vid) {
                    $newarr[] = '<option value="' . $vid . '" selected>' . $mvalue['1']['0'] . '</option>';
                } else {
                    $newarr[] = $_data;
                }
            }
        }

        if (!empty($newarr)) {
            foreach ($newarr as $_newarr) {
                $finaldata .= $_newarr;
            }
        }
        echo $finaldata;
    }

    public function actionGetgengineerbycweview($id, $vid) {
        $user = Yii::$app->user->identity;
        $data = [];
        $finaldata = '';
        $valuefinal = '';
        for ($value = 1; $value <= 135; $value++) {
            $data = [];
            if ($value == 1) {
                $data[] = '<option value="1">GE (AF) BAMRAULI - MES</option>';
                $data[] = '<option value="2">GE (AF) BIHTA</option>';
                $data[] = '<option value="3">GE(AF)BKT - MES</option>';
                $data[] = '<option value="4">GE (AF) GORAKHPUR</option>';
            } elseif ($value == 2) {
                $data[] = '<option value="5">GE(AF)IZATNAGAR - MES</option>';
                $data[] = '<option value="317">GE(P)(AF)BKT - MES</option>';
            } elseif ($value == 3) {
                $data[] = '<option value="6">GE (AF) TECH AREA KHERIA - MES</option>';
                $data[] = '<option value="318">GE (AF) ADM AREA KHERIA - MES</option>';
                $data[] = '<option value="319">AGE (AF) (I) SONEGAON - MES</option>';
            } elseif ($value == 4) {
                $data[] = '<option value="7">GE (AF) (ADM AREA) MAHARAJPUR - MES</option>';
                $data[] = '<option value="8">GE (AF) (TECH AREA) MAHARAJPUR - MES</option>';
                $data[] = '<option value="9">GE (P) AF MAHARAJPUR - MES</option>';
            } elseif ($value == 5) {
                $data[] = '<option value="10">GE(EAST) BAREILLY - MES</option>';
                $data[] = '<option value="11">GE (P) BAREILLY - MES</option>';
                $data[] = '<option value="12">GE(WEST) BAREILLY - MES</option>';
            } elseif ($value == 6) {
                $data[] = '<option value="13">GE DEHRADUN - MES</option>';
                $data[] = '<option value="320">GE (P)DEHRADUN - MES</option>';
                $data[] = '<option value="14">GE PREMNAGAR - MES</option>';
            } elseif ($value == 7) {
                $data[] = '<option value="15">AGE(I) Raiwala - MES</option>';
                $data[] = '<option value="16">GE LANSDOWNE -MES</option>';
                $data[] = '<option value="17">GE (MES) Clement Town - MES</option>';
            } elseif ($value == 8) {
                $data[] = '<option value="18">GE Pithoragarh - MES</option>';
                $data[] = '<option value="321">GE 871EWS - MES</option>';
                $data[] = '<option value="19">GE RANIKHET - MES</option>';
            } elseif ($value == 9) {
                $data[] = '<option value="20">GE (N) MEERUT - MES</option>';
                $data[] = '<option value="322">GE (S) MEERUT - MES</option>';
                $data[] = '<option value="21">GE (U) EM Meerut - MES</option>';
            } elseif ($value == 10) {
                $data[] = '<option value="22">GE ROORKEE - MES</option>';
                $data[] = '<option value="23">GE (SOUTH) MEERUT - MES</option>';
            } elseif ($value == 11) {
                $data[] = '<option value="24">AGE (I) PACHMARHI - MES</option>';
                $data[] = '<option value="25">AGE (I) RAIPUR - MES</option>';
                $data[] = '<option value="26">GE (E) JABALPUR - MES</option>';
                $data[] = '<option value="27">GE (W) JABALPUR - MES</option>';
            } elseif ($value == 12) {
                $data[] = '<option value="28">GE AWC - MES</option>';
                $data[] = '<option value="29">GE (Maint) Inf School - MES</option>';
                $data[] = '<option value="323">GE (P) Inf School - MES</option>';
                $data[] = '<option value="324">GE MCTE - MES</option>';
            } elseif ($value == 13) {
                $data[] = '<option value="30">GE DANAPUR - MES</option>';
                $data[] = '<option value="31">GE DIPATOLI - MES</option>';
                $data[] = '<option value="32">GE (P) GAYA - MES</option>';
                $data[] = '<option value="33">GE RAMGARH - MES</option>';
                $data[] = '<option value="34">GE RANCHI - MES</option>';
            } elseif ($value == 14) {
                $data[] = '<option value="35">GE I GOPALPUR ON SEA - MES</option>';
            } elseif ($value == 15) {
                $data[] = '<option value="36">GE (E) AGRA - MES</option>';
                $data[] = '<option value="37">GE (W) AGRA - MES</option>';
            } elseif ($value == 16) {
                $data[] = '<option value="38">GE (E) ALLAHABAD - MES</option>';
                $data[] = '<option value="39">GE FAIZABAD - MES</option>';
                $data[] = '<option value="40">GE(P) ALLAHABAD - MES</option>';
                $data[] = '<option value="41">GE (W) ALLAHABAD - MES</option>';
            } elseif ($value == 17) {
                $data[] = '<option value="42">GE FATEHGARH - MES</option>';
                $data[] = '<option value="325">GE MES KANPUR - MES</option>';
            } elseif ($value == 18) {
                $data[] = '<option value="43">GE(EAST)LUCKNOW - MES</option>';
                $data[] = '<option value="44">GE(E/M)LUCKNOW - MES</option>';
                $data[] = '<option value="45">GE(WEST)LUCKNOW - MES</option>';
            } elseif ($value == 19) {
                $data[] = '<option value="46">GE (E) MATHURA - MES</option>';
                $data[] = '<option value="47">GE (W) MATHURA - MES</option>';
            } elseif ($value == 20) {
                $data[] = '<option value="48">AGE (I) (AF) DIGARU - MES</option>';
                $data[] = '<option value="49">AGE (I) KUMBHIRGRAM - MES</option>';
                $data[] = '<option value="50">GE (AF) BAGDOGRA - MES</option>';
                $data[] = '<option value="51">GE (AF) BORJAR - MES</option>';
                $data[] = '<option value="52">GE (AF) HASIMARA - MES</option>';
            } elseif ($value == 21) {
                $data[] = '<option value="53">GE (AF) CHABUA - MES</option>';
                $data[] = '<option value="54">GE (AF) JORHAT - MES</option>';
                $data[] = '<option value="326">GE (AF) MOHANBARI - MES</option>';
                $data[] = '<option value="55">GE (AF) TEZPUR - MES</option>';
            } elseif ($value == 22) {
                $data[] = '<option value="56">GE (AF) BARRACKPORE - MES</option>';
                $data[] = '<option value="57">GE (AF) KALAIKUNDA - MES</option>';
            } elseif ($value == 23) {
                $data[] = '<option value="58">AGE (I) (AF) SINGHARSI-MES</option>';
                $data[] = '<option value="59">GE (AF) PURNEA-MES</option>';
                $data[] = '<option value="327">GE (AF) PANAGARH-MES</option>';
            } elseif ($value == 26) {
                $data[] = '<option value="60">GE ALIPORE - MES</option>';
                $data[] = '<option value="61">GE(CENTRAL) KOLKATA - MES</option>';
                $data[] = '<option value="62">GE FORT WILLIAM KOLKATA - MES</option>';
            } elseif ($value == 27) {
                $data[] = '<option value="63">GE (P) (NAVY AND CG) KOLKATA - MES</option>';
            } elseif ($value == 28) {
                $data[] = '<option value="64">GE BARRACKPORE - MES</option>';
                $data[] = '<option value="65">GE PANAGARH - MES</option>';
            } elseif ($value == 29) {
                $data[] = '<option value="66">GE(MAINT) ARAKKONAM - MES</option>';
                $data[] = '<option value="67">GE(NAVY)CHENNAI - MES</option>';
                $data[] = '<option value="68">GETHIRUNALVELI - MES</option>';
            } elseif ($value == 30) {
                $data[] = '<option value="69">GE (N and CG) Bhubaneshwar - MES</option>';
                $data[] = '<option value="70">GE (P) CHILKA - MES</option>';
                $data[] = '<option value="71">GE (P) (NAVY) KALINGA - MES</option>';
                $data[] = '<option value="72">GE (P) (NAVY) VISAKHAPATNAM - MES</option>';
            } elseif ($value == 31) {
                $data[] = '<option value="73">GE NAVAL BASE VISAKHAPATNAM - MES</option>';
                $data[] = '<option value="74">GE NAVAL DEPOT VISAKHAPATNAM - MES</option>';
                $data[] = '<option value="75">GE NAVAL SERVICES VISAKHAPATNAM - MES</option>';
                $data[] = '<option value="76">GE UTILITY -II VISAKHAPATNAM - MES</option>';
            } elseif ($value == 32) {
                $data[] = '<option value="78">GE I DM VIZAG - MES</option>';
            } elseif ($value == 33) {
                $data[] = '<option value="79">AGE (I) Lekhapani - MES</option>';
                $data[] = '<option value="80">GE Dinjan - MES</option>';
                $data[] = '<option value="81">GE Jorhat - MES</option>';
            } elseif ($value == 34) {
                $data[] = '<option value="82">AGE (I) AGARTALA -MES</option>';
                $data[] = '<option value="83">AGE (I) Zakhama - MES</option>';
                $data[] = '<option value="84">GE 868 EWS - MES</option>';
                $data[] = '<option value="85">GE 869 EWS - MES</option>';
                $data[] = '<option value="86">GE 872 EWS - MES</option>';
                $data[] = '<option value="87">GE SILCHAR - MES</option>';
            } elseif ($value == 35) {
                $data[] = '<option value="88">GE GUWAHATI - MES</option>';
                $data[] = '<option value="89">GE NARANGI - MES</option>';
                $data[] = '<option value="90">GE (P) SHILLONG  - MES</option>';
                $data[] = '<option value="91">GE SHILLONG - MES</option>';
            } elseif ($value == 36) {
                $data[] = '<option value="92">AGE (I) Rangia - MES</option>';
                $data[] = '<option value="93">AGE I TAWANG - MES</option>';
                $data[] = '<option value="94">GE 859 EWS - MES</option>';
                $data[] = '<option value="95">GE Missamari - MES</option>';
                $data[] = '<option value="96">GE (North) Tezpur - MES</option>';
                $data[] = '<option value="97">GE (South) Tezpur - MES</option>';
                $data[] = '<option value="98">GE TAWANG - MES</option>';
            } elseif ($value == 37) {
                $data[] = '<option value="106">GE BENGDUBI - MES</option>';
            } elseif ($value == 38) {
                $data[] = '<option value="107">GE (N) BINNAGURI - MES</option>';
                $data[] = '<option value="108">GE (S) BINNAGURI - MES</option>';
                $data[] = '<option value="109">GE SEVOKE ROAD - MES</option>';
            } elseif ($value == 135) {
                $data[] = '<option value="328">GE MISSAMARI - MES</option>';
            } elseif ($value == 40) {
                $data[] = '<option value="110">AGE (I) DARJEELING - MES</option>';
                $data[] = '<option value="111">GE 867 EWS - MES</option>';
                $data[] = '<option value="112">GEGANGTOK - MES</option>';
                $data[] = '<option value="113">GE SUKNA - MES</option>';
            } elseif ($value == 41) {
                $data[] = '<option value="114">GE 864 EWS - MES</option>';
                $data[] = '<option value="115">GE 874 EWS - MES</option>';
                $data[] = '<option value="116">GE 969 EWS - MES</option>';
            } elseif ($value == 42) {
                $data[] = '<option value="117">AGE (I) CIF (K) - MES</option>';
                $data[] = '<option value="118">GE 861 EWS - MES</option>';
                $data[] = '<option value="119">GE 970 EWS - MES</option>';
            } elseif ($value == 43) {
                $data[] = '<option value="120">GE (AF) JAMMU - MES</option>';
                $data[] = '<option value="121">GE (AF) PATHANKOT - MES</option>';
                $data[] = '<option value="122">GE (AF) UDHAMPUR - MES</option>';
            } elseif ($value == 45) {
                $data[] = '<option value="123">GE (AF) ARANTIPAR - MES</option>';
                $data[] = '<option value="124">GE (AF) LEH - MES</option>';
                $data[] = '<option value="329">GE (AF) THOISE - MES</option>';
                $data[] = '<option value="125">GE(AF)SRINAGAR - MES</option>';
            } elseif ($value == 46) {
                $data[] = '<option value="330">GE KARGIL - MES</option>';
                $data[] = '<option value="331">GE KHUMBATHANG - MES</option>';
            } elseif ($value == 47) {
                $data[] = '<option value="126">GE 865 EWS - MES</option>';
                $data[] = '<option value="332">GE 860 EWS - MES</option>';
                $data[] = '<option value="127">GE PARTAPUR - MES</option>';
                $data[] = '<option value="128">GE (P) NO 2LEH - MES</option>';
            } elseif ($value == 48) {
                $data[] = '<option value="129">GE NAGROTA - MES</option>';
                $data[] = '<option value="333">AGE(I)CIF(U) - MES</option>';
                $data[] = '<option value="130">GE (N) AKHNOOR - MES</option>';
                $data[] = '<option value="131">GE (S) AKHNOOR - MES</option>';
            } elseif ($value == 50) {
                $data[] = '<option value="132">GE 862 EWS - MES</option>';
            } elseif ($value == 51) {
                $data[] = '<option value="133">GE(NORTH) UDHAMPUR - MES</option>';
                $data[] = '<option value="134">GE(SOUTH) UDHAMPUR - MES</option>';
                $data[] = '<option value="135">GE (U) UDHAMPUR - MES</option>';
            } elseif ($value == 54) {
                $data[] = '<option value="136">GE BRICHGUNJ - MES</option>';
                $data[] = '<option value="334">GE (P) CENTRAL - MES</option>';
                $data[] = '<option value="137">GE (SOUTH) DIGLIPUR - MES</option>';
            } elseif ($value == 55) {
                $data[] = '<option value="138">GE HADDO - MES</option>';
                $data[] = '<option value="139">GE MINNIE BAY PORTBLAIR - MES</option>';
            } elseif ($value == 56) {
                $data[] = '<option value="138">GE (I) 866 EWS - MES</option>';
            } elseif ($value == 60) {
                $data[] = '<option value="139">GE(AF) BANGALORE - MES</option>';
                $data[] = '<option value="335">GE (AF) MARATHALLI - MES</option>';
                $data[] = '<option value="336">GE (AF)(P) BANGALORE - MES</option>';
                $data[] = '<option value="337">GE(AF) SDI and ASTE BANGALORE - MES</option>';
                $data[] = '<option value="140">GE (AF) TAMBARAM - MES</option>';
            } elseif ($value == 61) {
                $data[] = '<option value="141">GE AFA HYDERABAD - MES</option>';
                $data[] = '<option value="142">GE(AF)BIDAR - MES</option>';
                $data[] = '<option value="143">GE(AF)HAKIMPET HYDERABAD - MES</option>';
            } elseif ($value == 62) {
                $data[] = '<option value="144">AGE(I)(AF) CHIMNEY HILLS BANGALORE - MES</option>';
                $data[] = '<option value="145">AGE (I) COIMBATORE - MES</option>';
                $data[] = '<option value="146">GE (AF) SAMBRA - BELGAUM - MES</option>';
                $data[] = '<option value="147">GE(AF) Yelehanka - MES</option>';
                $data[] = '<option value="148">GE (Maint) (AF) Jalahalli - MES</option>';
            } elseif ($value == 63) {
                $data[] = '<option value="149">GE (AF) SULUR - MES</option>';
                $data[] = '<option value="150">GE (AF) TANJAVUR - MES</option>';
                $data[] = '<option value="151">GE(AF)TRIVANDRUM - MES</option>';
                $data[] = '<option value="152">GE(P) (AF) SULUR - MES</option>';
            } elseif ($value == 65) {
                $data[] = '<option value="153">GE B/R AF CHAKERI - MES</option>';
                $data[] = '<option value="154">GE E/M AF CHAKERI - MES</option>';
            } elseif ($value == 66) {
                $data[] = '<option value="155">GE (AF) AMLA - MES</option>';
                $data[] = '<option value="338">GE (AF) OJHAR - MES</option>';
            } elseif ($value == 67) {
                $data[] = '<option value="339">AGE(I) MANAURI - MES</option>';
                $data[] = '<option value="156">GE (AF) MC Chandigarh - MES</option>';
                $data[] = '<option value="157">GE (AF) TUGHLAKABAD - MES</option>';
            } elseif ($value == 68) {
                $data[] = '<option value="158">GE (I) (AF) NAGPUR - MES</option>';
            } elseif ($value == 70) {
                $data[] = '<option value="340">AGE(I) DHANA - MES</option>';
                $data[] = '<option value="159">GE BHOPAL - MES</option>';
                $data[] = '<option value="160">GE DRONACHAL - MES</option>';
                $data[] = '<option value="161">GE NASIRABAD - MES</option>';
                $data[] = '<option value="162">GE SAUGOR - MES</option>';
            } elseif ($value == 71) {
                $data[] = '<option value="163">AGE (I) TALBEHAT - MES</option>';
                $data[] = '<option value="164">GE BABINA - MES</option>';
                $data[] = '<option value="165">GE GWALIOR - MES</option>';
                $data[] = '<option value="166">GE JHANSI - MES</option>';
            } elseif ($value == 72) {
                $data[] = '<option value="167">GE KAMPTEE - MES</option>';
                $data[] = '<option value="168">GE PULGAON - MES</option>';
            } elseif ($value == 73) {
                $data[] = '<option value="169">GE (CENTRAL) BANGALORE - MES</option>';
                $data[] = '<option value="170">GE(NORTH) BANGALORE - MES</option>';
                $data[] = '<option value="171">GE (P) BANGALORE - MES</option>';
                $data[] = '<option value="172">GE (SOUTH) BANGALORE - MES</option>';
            } elseif ($value == 74) {
                $data[] = '<option value="173">GE AVADI- MES</option>';
                $data[] = '<option value="174">GE CHENNAI - MES</option>';
                $data[] = '<option value="175">GE ST THOMAS MOUNT - MES</option>';
            } elseif ($value == 75) {
                $data[] = '<option value="179">GE GOLCONDA HYDERABAD - MES</option>';
                $data[] = '<option value="180">GE (NORTH) SECUNDERABAD - MES</option>';
                $data[] = '<option value="181">GE (SOUTH) SECUNDERABAD - MES</option>';
                $data[] = '<option value="182">GE(UTILITY) SECUNDERABAD - MES</option>';
            } elseif ($value == 76) {
                $data[] = '<option value="183">AGE(I) CANNANORE - MES</option>';
                $data[] = '<option value="184">AGE(I) TRICHY - MES</option>';
                $data[] = '<option value="185">GE (ARMY) TRIVANDRUM - MES</option>';
                $data[] = '<option value="186">GE DSSC WELLINGTON -MES</option>';
                $data[] = '<option value="187">GE WELLINGTON - MES</option>';
            } elseif ($value == 77) {
                $data[] = '<option value="188">GE (I) BELGAUM - MES</option>';
            } elseif ($value == 78) {
                $data[] = '<option value="189">GE (ARMY ) BARODA - MES</option>';
                $data[] = '<option value="190">GE (ARMY)BHUJ - MES</option>';
                $data[] = '<option value="191">GE (ARMY) JAMNAGAR - MES</option>';
                $data[] = '<option value="341">GE AHMEDABAD - MES</option>';
                $data[] = '<option value="342">GE GANDHINAGAR - MES</option>';
            } elseif ($value == 79) {
                $data[] = '<option value="192">AGE (I) NAGTALAO - MES</option>';
                $data[] = '<option value="193">AGE(I) UDAIPUR - MES</option>';
                $data[] = '<option value="194">GE(A) CENTRAL JODHPUR - MES</option>';
                $data[] = '<option value="195">GE(A)UTILITY JODHPUR - MES</option>';
                $data[] = '<option value="196">GE BANAR - MES</option>';
                $data[] = '<option value="197">GE SHIKARGARH - MES</option>';
            } elseif ($value == 80) {
                $data[] = '<option value="343">GE (ARMY) BARMER - MES</option>';
                $data[] = '<option value="198">GE (ARMY) JAISALMER - MES</option>';
            } elseif ($value == 81) {
                $data[] = '<option value="199">GE MAINT EZHIMALA - MES</option>';
                $data[] = '<option value="200">GE (P) NO 2 EZHIMALA - MES</option>';
            } elseif ($value == 82) {
                $data[] = '<option value="201">GE FORT KOCHI - MES</option>';
                $data[] = '<option value="202">GE (P) (NW) KOCHI - MES</option>';
            } elseif ($value == 83) {
                $data[] = '<option value="203">AGE (I) AGRANI - MES</option>';
                $data[] = '<option value="204">GE FORT KOCHI - MES</option>';
                $data[] = '<option value="205">GE NS KOCHI - MES</option>';
                $data[] = '<option value="206">GE (NW) KOCHI - MES</option>';
            } elseif ($value == 86) {
                $data[] = '<option value="207">GE(NW) KARANJA - MES</option>';
                $data[] = '<option value="208">GE (P) NW MUMBAI - MES</option>';
            } elseif ($value == 87) {
                $data[] = '<option value="209">AGE(I) MANDOVI - MES</option>';
                $data[] = '<option value="210">GE GOMANTAK - MES</option>';
                $data[] = '<option value="211">GE (NW) VASCO - MES</option>';
                $data[] = '<option value="212">GE (P) VASCO - MES</option>';
            } elseif ($value == 88) {
                $data[] = '<option value="213">AGE (I) ASHVINI - MES</option>';
                $data[] = '<option value="214">GE (NW) KUNJALI - MES</option>';
                $data[] = '<option value="215">GE (NW) NAVY NAGAR - MES</option>';
                $data[] = '<option value="216">GE (NW) NOFRA - MES</option>';
            } elseif ($value == 89) {
                $data[] = '<option value="217">GE (NW) BHANDUP - MES</option>';
                $data[] = '<option value="218">GE (NW) MANKHURD - MES</option>';
            } elseif ($value == 93) {
                $data[] = '<option value="219">GE (NORTH) SANTA CRUZ - MES</option>';
                $data[] = '<option value="220">GE PANAJI - MES</option>';
                $data[] = '<option value="344">GE DEHU ROAD - MES</option>';
                $data[] = '<option value="221">GE (WEST) COLABA - MES</option>';
            } elseif ($value == 94) {
                $data[] = '<option value="222">GE DEOLALI - MES</option>';
                $data[] = '<option value="223">GE (N) AHMEDNAGAR - MES</option>';
                $data[] = '<option value="224">GE NASIK ROAD - MES</option>';
                $data[] = '<option value="225">GE (S) AHMEDNAGAR - MES</option>';
            } elseif ($value == 95) {
                $data[] = '<option value="226">GE (CENTRAL) KIRKEE - MES</option>';
                $data[] = '<option value="345">GE (CME) KIRKEE - MES</option>';
                $data[] = '<option value="227">GE MH AND RANGE HILLS - MES</option>';
            } elseif ($value == 96) {
                $data[] = '<option value="228">GE (C) PUNE - MES</option>';
                $data[] = '<option value="229">GE KHADAKVASLA - MES</option>';
                $data[] = '<option value="230">GE (N) PUNE - MES</option>';
                $data[] = '<option value="231">GE (S) PUNE - MES</option>';
            } elseif ($value == 97) {
                $data[] = '<option value="232">GE(AF) BHUJ - MES</option>';
                $data[] = '<option value="233">GE (AF) JAMNAGAR - MES</option>';
                $data[] = '<option value="346">GE (AF) NALIYA NO. 1 - MES</option>';
            } elseif ($value == 98) {
                $data[] = '<option value="232">GE (AF) CHILODA - MES</option>';
                $data[] = '<option value="347">GE (AF) BARODA - MES</option>';
            } elseif ($value == 99) {
                $data[] = '<option value="233">GE (AF) Phalodi - MES</option>';
            } elseif ($value == 100) {
                $data[] = '<option value="234">GE (AF) JAMNAGAR NO.2 - MES</option>';
            } elseif ($value == 101) {
                $data[] = '<option value="235">AGE (I) (AF) JAIPUR - MES</option>';
                $data[] = '<option value="236">AGE (I) MOUNT ABU - MES</option>';
                $data[] = '<option value="237">GE (AF) JAISALMER - MES</option>';
                $data[] = '<option value="238">GE (AF) JODHPUR - MES</option>';
                $data[] = '<option value="239">GE (AF) No. 2 JODHPUR - MES</option>';
                $data[] = '<option value="240">GE (AF) UTTERLAI - MES</option>';
            } elseif ($value == 102) {
                $data[] = '<option value="241">GE (AF) LOHOGAON - MES</option>';
                $data[] = '<option value="242">GE (AF) THANE - MES</option>';
            } elseif ($value == 104) {
                $data[] = '<option value="243">GE (NORTH) BATHINDA - MES</option>';
                $data[] = '<option value="244">GE (SOUTH) BATHINDA - MES</option>';
                $data[] = '<option value="245">GE (U) BATHINDA - MES</option>';
            } elseif ($value == 105) {
                $data[] = '<option value="246">GE (ARMY) SURATGARH - MES</option>';
                $data[] = '<option value="247">GE (NORTH) BIKANER - MES</option>';
                $data[] = '<option value="248">GE (P) Kanesar - MES</option>';
            } elseif ($value == 106) {
                $data[] = '<option value="249">GE ABOHAR -  MES</option>';
                $data[] = '<option value="250">GE Faridkot - MES</option>';
                $data[] = '<option value="251">GE LALGARH JATTAN - MES</option>';
                $data[] = '<option value="252">GE SRIGANGANAGAR - MES</option>';
            } elseif ($value == 107) {
                $data[] = '<option value="253">GE HISAR - MES</option>';
            } elseif ($value == 108) {
                $data[] = '<option value="254">GE BHARATPUR - MES</option>';
                $data[] = '<option value="255">GE JAIPUR - MES</option>';
                $data[] = '<option value="256">GE (U) JAIPUR - MES</option>';
            } elseif ($value == 109) {
                $data[] = '<option value="257">GE ALWAR - MES</option>';
                $data[] = '<option value="258">GE KOTA - MES</option>';
            } elseif ($value == 110) {
                $data[] = '<option value="259">GE Hisar - MES</option>';
            } elseif ($value == 112) {
                $data[] = '<option value="260">GE (AF) Ambala-MES</option>';
                $data[] = '<option value="261">GE (AF) Halwara-MES</option>';
                $data[] = '<option value="262">GE (AF) Sarsawa-MES</option>';
            } elseif ($value == 113) {
                $data[] = '<option value="263">GE(AF) BHISIANA - MES</option>';
                $data[] = '<option value="264">GE (AF) Sirsa-MES</option>';
            } elseif ($value == 114) {
                $data[] = '<option value="265">GE (AF) Nal-MES</option>';
                $data[] = '<option value="266">GE (AF) Suratgarh-MES</option>';
            } elseif ($value == 115) {
                $data[] = '<option value="267">GE (AF) Adampur-MES</option>';
                $data[] = '<option value="268">GE (P) (AF) No 2 CHANDIGARH-MES</option>';
            } elseif ($value == 116) {
                $data[] = '<option value="269">GE (AF) FARIDABAD - MES</option>';
                $data[] = '<option value="270">GE (AF) GURGAON - MES</option>';
            } elseif ($value == 117) {
                $data[] = '<option value="271">GE (AF) North Palam-MES</option>';
                $data[] = '<option value="272">GE(AF) South Palam-MES</option>';
                $data[] = '<option value="348">GE (P)(AF) South Palam-MES</option>';
                $data[] = '<option value="273">GE (AF) Subroto Park-MES</option>';
            } elseif ($value == 118) {
                $data[] = '<option value="274">GE (N) AMBALA - MES</option>';
                $data[] = '<option value="275">GE (P) Ambala - MES</option>';
                $data[] = '<option value="276">GE (U) AMBALA - MES</option>';
                $data[] = '<option value="349">GE (S) AMBALA - MES</option>';
            } elseif ($value == 119) {
                $data[] = '<option value="277">GE CHANDIGARH - MES</option>';
                $data[] = '<option value="278">GE CHANDIMANDIR - MES</option>';
                $data[] = '<option value="279">GE (P) CHANDIMANDIR - MES</option>';
                $data[] = '<option value="350">GE (U) CHANDIMANDIR - MES</option>';
            } elseif ($value == 120) {
                $data[] = '<option value="280">GE (P) DAPPAR - MES</option>';
                $data[] = '<option value="281">GE (S) PATIALA - MES</option>';
                $data[] = '<option value="351">GE (N) PATIALA - MES</option>';
            } elseif ($value == 121) {
                $data[] = '<option value="282">GE 863 EWS - MES</option>';
                $data[] = '<option value="283">GE JUTOGH - MES</option>';
                $data[] = '<option value="352">GE KASAULI - MES</option>';
            } elseif ($value == 122) {
                $data[] = '<option value="284">GE (CENTRAL) DELHI CANTT-MES</option>';
                $data[] = '<option value="285">GE (EAST) DELHI CANTT-MES</option>';
                $data[] = '<option value="286">GE (NORTH) DELHI CANTT-MES</option>';
                $data[] = '<option value="287">GE (WEST) DELHI CANTT-MES</option>';
            } elseif ($value == 123) {
                $data[] = '<option value="288">GE E/M BASE HOSPITAL DELHI CNATT-MES</option>';
                $data[] = '<option value="289">GE E/M (RR) HOSPITAL DELHI CNATT-MES</option>';
                $data[] = '<option value="290">GE NEW DELHI-MES</option>';
                $data[] = '<option value="291">GE (S) NEW DELHI-MES</option>';
                $data[] = '<option value="353">GE (P) WEST DELHI-MES</option>';
            } elseif ($value == 124) {
                $data[] = '<option value="354">AGE (I)(U) B and R DELHI CNATT-MES</option>';
                $data[] = '<option value="292">GE(U)ELECTRIC SUPPLY DELHI CANTT-MES</option>';
                $data[] = '<option value="355">GE(U) P and M DELHI CNATT-MES</option>';
                $data[] = '<option value="293">GE(U)WATER SUPPLY DELHI CANTT-MES</option>';
            } elseif ($value == 127) {
                $data[] = '<option value="294">GE AMRITSAR - MES</option>';
                $data[] = '<option value="295">GE GURDASPUR - MES</option>';
                $data[] = '<option value="296">GE (NAMS) AMRITSAR - MES</option>';
            } elseif ($value == 128) {
                $data[] = '<option value="297">GE (EAST) FEROZEPUR - MES</option>';
                $data[] = '<option value="298">GE LUDHIANA - MES</option>';
                $data[] = '<option value="299">GE (WEST) FEROZEPUR - MES</option>';
            } elseif ($value == 129) {
                $data[] = '<option value="300">GE (EAST) JALANDHAR CANTT - MES</option>';
                $data[] = '<option value="301">GE ENGR PARK JALANDHAR CANTT - MES</option>';
                $data[] = '<option value="302">GE KAPURTHLA(P) - MES</option>';
                $data[] = '<option value="303">GE (WEST) JALANDHAR CANTT - MES</option>';
            } elseif ($value == 130) {
                $data[] = '<option value="304">GE JAMMU - MES</option>';
                $data[] = '<option value="305">GE KALUCHAK - MES</option>';
                $data[] = '<option value="306">GE SATWARI - MES</option>';
            } elseif ($value == 131) {
                $data[] = '<option value="307">GE(NORTH) MAMUN - MES</option>';
                $data[] = '<option value="308">GE SAMBA - MES</option>';
                $data[] = '<option value="309">GE(SOUTH) MAMUN - MES</option>';
            } elseif ($value == 132) {
                $data[] = '<option value="310">GE BASOLI - MES</option>';
                $data[] = '<option value="311">GE (SOUTH) PATHANKOT - MES</option>';
                $data[] = '<option value="312">GE (WEST) PATHANKOT - MES</option>';
            } elseif ($value == 133) {
                $data[] = '<option value="313">AGE (I) DHARAMSHALA - MES</option>';
                $data[] = '<option value="314">GE DALHOUSIE - MES</option>';
                $data[] = '<option value="315">GE (KH) YOL - MES</option>';
                $data[] = '<option value="316">GE PALAMPUR - MES</option>';
            }
            $i = 0;
            if (!empty($data)) {
                foreach ($data as $_data) {
                    $i++;
                    preg_match_all('/"(.*?)"/s', $_data, $matches);
                    preg_match_all('/>(.*?)</s', $_data, $mvalue);
                    $data = ['gid' => $matches['1']['0'], 'cwengineer' => $value, 'text' => $mvalue['1']['0'], 'user_id' => $user->UserId, 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];
                    $querydata = \Yii::$app
                            ->db
                            ->createCommand()
                            ->insert('gengineers', $data)
                            ->execute();
                }
            }
        }
        die();
    }

    public function actionGetitemdesc() {
        $searchTerm = $_REQUEST['client'];
        $descData = [];
        $items = \common\models\ItemDetails::find()->where(['like', 'description', $searchTerm])->all();
        if (!empty($items)) {
            foreach ($items as $_item) {
                $data['value'] = $_item->description;
                array_push($descData, $data);
            }
        }
        echo json_encode($descData);
    }

    public function actionGettendertwo($id, $vid) {
        $value = $id;
        $finaldata = '';
        $data = [];
        $data[] = '<option value="">Select</option>';
        if ($value == 1) {
            $data[] = '<option value="1">Electrical</option>';
            $data[] = '<option value="2">Air Conditioning</option>';
            $data[] = '<option value="3">Fire Fighting</option>';
            $data[] = '<option value="4">Water supply</option>';
            $data[] = '<option value="5">Lifts</option>';
            $data[] = '<option value="6">Cranes</option>';
            $data[] = '<option value="7">DG Set</option>';
        } elseif ($value == 2) {
            $data[] = '<option value="14">Cement</option>';
            $data[] = '<option value="15">Reinforcement Steel</option>';
            $data[] = '<option value="16">Structural Steel</option>';
            $data[] = '<option value="17">Non Structural Steel</option>';
        } else {
            $data[] = '<option value="8">Building</option>';
            $data[] = '<option value="9">Road</option>';
            $data[] = '<option value="10">Periodical</option>';
            $data[] = '<option value="11">Joinery</option>';
            $data[] = '<option value="12">Plumbing</option>';
        }

        $i = 0;
        if (!empty($data)) {
            foreach ($data as $_data) {
                $i++;
                preg_match_all('/"(.*?)"/s', $_data, $matches);
                preg_match_all('/>(.*?)</s', $_data, $mvalue);
                if ($matches['1']['0'] == $vid) {
                    $newarr[] = '<option value="' . $vid . '" selected>' . $mvalue['1']['0'] . '</option>';
                } else {
                    $newarr[] = $_data;
                }
            }
        }

        if (!empty($newarr)) {
            foreach ($newarr as $_newarr) {
                $finaldata .= $_newarr;
            }
        }
        echo $finaldata;
    }

    public function actionGettenderthree($id, $vid) {
        $value = $id;
        $finaldata = '';
        $data = [];
        $data[] = '<option value="">Select</option>';
        if ($value == 1) {
            $data[] = '<option value="1">LT</option>';
            $data[] = '<option value="2">HT</option>';
        } elseif ($value == 2) {
            $data[] = '<option value="3">VRV Units</option>';
            $data[] = '<option value="4">AC Plants</option>';
        } elseif ($value == 3) {
            $data[] = '<option value="5">Pumps</option>';
            $data[] = '<option value="6">MS Pipes</option>';
            $data[] = '<option value="7">Motors</option>';
        } elseif ($value == 4) {
            $data[] = '<option value="8">Pumps</option>';
            $data[] = '<option value="9">GI Pipes</option>';
            $data[] = '<option value="10">MS Pipes</option>';
            $data[] = '<option value="11">Motors</option>';
            $data[] = '<option value="12">NP 2</option>';
        }

        $i = 0;
        if (!empty($data)) {
            foreach ($data as $_data) {
                $i++;
                preg_match_all('/"(.*?)"/s', $_data, $matches);
                preg_match_all('/>(.*?)</s', $_data, $mvalue);
                if ($matches['1']['0'] == $vid) {
                    $newarr[] = '<option value="' . $vid . '" selected>' . $mvalue['1']['0'] . '</option>';
                } else {
                    $newarr[] = $_data;
                }
            }
        }

        if (!empty($newarr)) {
            foreach ($newarr as $_newarr) {
                $finaldata .= $_newarr;
            }
        }
        echo $finaldata;
    }

    public function actionGettenderfour($id, $vid) {
        $value = $id;
        $finaldata = '';
        $data = [];
        $data[] = '<option value="">Select</option>';
        if ($value == 1) {
            $data[] = '<option value="1">Cables</option>';
            $data[] = '<option value="2">Lighting</option>';
            $data[] = '<option value="3">Fans</option>';
            $data[] = '<option value="4">Accessories</option>';
            $data[] = '<option value="5">Wire</option>';
            $data[] = '<option value="6">DB/MCB/MCCB/Timers</option>';
            $data[] = '<option value="7">Transformers</option>';
            $data[] = '<option value="8">Cable Jointing Kits</option>';
            $data[] = '<option value="9">Panels</option>';
            $data[] = '<option value="10">ACB</option>';
            $data[] = '<option value="13">Motors</option>';
        } elseif ($value == 2) {
            $data[] = '<option value="1">Cables</option>';
            $data[] = '<option value="7">Transformers</option>';
            $data[] = '<option value="8">Cable Jointing Kits</option>';
            $data[] = '<option value="9">Panels</option>';
            $data[] = '<option value="11">VCB</option>';
            $data[] = '<option value="12">Substations</option>';
            $data[] = '<option value="13">Motors</option>';
        }

        $i = 0;
        if (!empty($data)) {
            foreach ($data as $_data) {
                $i++;
                preg_match_all('/"(.*?)"/s', $_data, $matches);
                preg_match_all('/>(.*?)</s', $_data, $mvalue);
                if ($matches['1']['0'] == $vid) {
                    $newarr[] = '<option value="' . $vid . '" selected>' . $mvalue['1']['0'] . '</option>';
                } else {
                    $newarr[] = $_data;
                }
            }
        }

        if (!empty($newarr)) {
            foreach ($newarr as $_newarr) {
                $finaldata .= $_newarr;
            }
        }
        echo $finaldata;
    }

    public function actionGettenderfive($id, $vid) {
        $value = $id;
        $finaldata = '';
        $data = [];
        $data[] = '<option value="">Select</option>';
        if (($value == 1) || ($value == 12)) {
            $data[] = '<option value="1">Copper</option>';
            $data[] = '<option value="2">Aluminium</option>';
        } elseif ($value == 2) {
            $data[] = '<option value="3">Domestic</option>';
            $data[] = '<option value="4">Industrial</option>';
            $data[] = '<option value="5">Street/Road</option>';
        } elseif ($value == 3) {
            $data[] = '<option value="6">Ceiling fans</option>';
            $data[] = '<option value="7">Wall fans</option>';
            $data[] = '<option value="8">Exhaust fans</option>';
        }

        $i = 0;
        if (!empty($data)) {
            foreach ($data as $_data) {
                $i++;
                preg_match_all('/"(.*?)"/s', $_data, $matches);
                preg_match_all('/>(.*?)</s', $_data, $mvalue);
                if ($matches['1']['0'] == $vid) {
                    $newarr[] = '<option value="' . $vid . '" selected>' . $mvalue['1']['0'] . '</option>';
                } else {
                    $newarr[] = $_data;
                }
            }
        }

        if (!empty($newarr)) {
            foreach ($newarr as $_newarr) {
                $finaldata .= $_newarr;
            }
        }
        echo $finaldata;
    }

    public function actionGettendersix($id, $vid) {
        $value = $id;
        $finaldata = '';
        $data = [];
        $data[] = '<option value="">Select</option>';
        if (($value == 1) || ($value == 2)) {
            $data[] = '<option value="1">Armoured</option>';
            $data[] = '<option value="2">Unarmoured</option>';
        }

        $i = 0;
        if (!empty($data)) {
            foreach ($data as $_data) {
                $i++;
                preg_match_all('/"(.*?)"/s', $_data, $matches);
                preg_match_all('/>(.*?)</s', $_data, $mvalue);
                if ($matches['1']['0'] == $vid) {
                    $newarr[] = '<option value="' . $vid . '" selected>' . $mvalue['1']['0'] . '</option>';
                } else {
                    $newarr[] = $_data;
                }
            }
        }

        if (!empty($newarr)) {
            foreach ($newarr as $_newarr) {
                $finaldata .= $_newarr;
            }
        }
        echo $finaldata;
    }

    public function actionTenderone($value) {
        switch ($value) {
            case "1":
                return "E/M";
                break;
            case "2":
                return "Civil";
                break;
            default:
                return "";
        }
    }

    public function actionTendertwo($value) {
        switch ($value) {
            case "1":
                return "Electrical";
                break;
            case "2":
                return "Air Conditioning";
                break;
            case "3":
                return "Fire Fighting";
                break;
            case "4":
                return "Water supply";
                break;
            case "5":
                return "Lifts";
                break;
            case "6":
                return "Cranes";
                break;
            case "7":
                return "DG Set";
                break;
            case "8":
                return "Building";
                break;
            case "9":
                return "Road";
                break;
            case "10":
                return "Periodical";
                break;
            case "11":
                return "Joinery";
                break;
            case "12":
                return "Plumbing";
                break;
            case "14":
                return "Cement";
                break;
            case "15":
                return "Reinforcement Steel";
                break;
            case "16":
                return "Structural Steel";
                break;
            case "17":
                return "Non Structural Steel";
                break;
            default:
                return "";
        }
    }

    public function actionTenderthree($value) {
        switch ($value) {
            case "1":
                return "LT";
                break;
            case "2":
                return "HT";
                break;
            case "3":
                return "VRV Units";
                break;
            case "4":
                return "AC Plants";
                break;
            case "5":
                return "Pumps";
                break;
            case "6":
                return "MS Pipes";
                break;
            case "7":
                return "Motors";
                break;
            case "8":
                return "Pumps";
                break;
            case "9":
                return "GI Pipes";
                break;
            case "10":
                return "MS Pipes";
                break;
            case "11":
                return "Motors";
                break;
            case "12":
                return "NP 2";
                break;
            default:
                return "";
        }
    }

    public function actionTenderfour($value) {
        switch ($value) {
            case "1":
                return "Cables";
                break;
            case "2":
                return "Lighting";
                break;
            case "3":
                return "Fans";
                break;
            case "4":
                return "Accessories";
                break;
            case "5":
                return "Wire";
                break;
            case "6":
                return "DB/MCB/MCCB/Timers";
                break;
            case "7":
                return "Transformers";
                break;
            case "8":
                return "Cable Jointing Kits";
                break;
            case "9":
                return "Panels";
                break;
            case "10":
                return "ACB";
                break;
            case "13":
                return "Motors";
                break;
            case "11":
                return "VCB";
                break;
            case "12":
                return "Substations";
                break;
            default:
                return "";
        }
    }

    public function actionTenderfive($value) {
        switch ($value) {
            case "1":
                return "Copper";
                break;
            case "2":
                return "Aluminium";
                break;
            case "3":
                return "Domestic";
                break;
            case "4":
                return "Industrial";
                break;
            case "5":
                return "Street/Road";
                break;
            case "6":
                return "Ceiling fans";
                break;
            case "7":
                return "Wall fans";
                break;
            case "8":
                return "Exhaust fans";
                break;
            default:
                return "";
        }
    }

    public function actionTendersix($value) {
        switch ($value) {
            case "1":
                return "Armoured";
                break;
            case "2":
                return "Unarmoured";
                break;
            default:
                return "";
        }
    }

    public function actionGetgroupbyid($value) {
        switch ($value) {
            case "1":
                return "Superadmin";
                break;
            case "2":
                return "Admin";
                break;
            case "3":
                return "User";
                break;
            case "4":
                return "Sales";
                break;
            default:
                return "";
        }
    }

    public function actionTechnicalstatus() {
        $user = Yii::$app->user->identity;
        require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
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

        $file_name = time() . $_FILES['filetoupload']['name'];
        $file_tmp = $_FILES['filetoupload']['tmp_name'];
        move_uploaded_file($file_tmp, "assets/files/" . $file_name);

        $keyName = 'files/' . $file_name;
        $pathInS3 = 'http://s3.us-east-2.amazonaws.com/' . Yii::$app->params['bucketName'] . '/' . $keyName;

        try {
// Uploaded:
            $file = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/files/" . $file_name;
            $fileupload = $s3->putObject(
                    array(
                        'Bucket' => Yii::$app->params['bucketName'],
                        'Key' => $keyName,
                        'SourceFile' => $file,
                        'ACL' => 'public-read-write'
                    )
            );
            if ($fileupload) {

                unlink('assets/files/' . $file_name);

                $data = ['tender_id' => $_POST['tid'], 'type' => 1, 'file' => $pathInS3, 'user_id' => $user->id, 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];
                $querydata = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('tenderfiles', $data)
                        ->execute();
                if ($querydata) {
                    $tender = \common\models\Tender::find()->where(['id' => $_POST['tid']])->one();
                    $tender->technical_status = 1;
                    $tender->save();
                    Yii::$app->session->setFlash('success', "Technical Bid Opened");
                } else {
                    Yii::$app->session->setFlash('error', "Technical Bid Not Opened");
                }
                return $this->redirect(array('site/tenders'));
            }
        } catch (S3Exception $e) {
            die('Error:' . $e->getMessage());
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function actionFinancialstatus() {
        $user = Yii::$app->user->identity;
        require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
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

        $file_name_one = time() . $_FILES['fileone']['name'];
        $file_name_two = time() . $_FILES['filetwo']['name'];
        $file_tmp_one = $_FILES['fileone']['tmp_name'];
        $file_tmp_two = $_FILES['filetwo']['tmp_name'];
        move_uploaded_file($file_tmp_one, "assets/files/" . $file_name_one);
        move_uploaded_file($file_tmp_two, "assets/files/" . $file_name_two);

        $keyNameone = 'files/' . $file_name_one;
        $keyNametwo = 'files/' . $file_name_two;

        $pathInS3one = 'http://s3.us-east-2.amazonaws.com/' . Yii::$app->params['bucketName'] . '/' . $keyNameone;
        $pathInS3two = 'http://s3.us-east-2.amazonaws.com/' . Yii::$app->params['bucketName'] . '/' . $keyNametwo;

        try {
// Uploaded:
            $fileone = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/files/" . $file_name_one;
            $filetwo = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/files/" . $file_name_two;
            $fileuploadone = $s3->putObject(
                    array(
                        'Bucket' => Yii::$app->params['bucketName'],
                        'Key' => $keyNameone,
                        'SourceFile' => $fileone,
                        'ACL' => 'public-read-write'
                    )
            );
            $fileuploadtwo = $s3->putObject(
                    array(
                        'Bucket' => Yii::$app->params['bucketName'],
                        'Key' => $keyNametwo,
                        'SourceFile' => $filetwo,
                        'ACL' => 'public-read-write'
                    )
            );
            if ($fileuploadone) {
                unlink('assets/files/' . $file_name_one);
                $dataone = ['tender_id' => $_POST['tid'], 'type' => 2, 'file' => $pathInS3one, 'user_id' => $user->id, 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];
                $querydata = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('tenderfiles', $dataone)
                        ->execute();
            }
            if ($fileuploadtwo) {
                unlink('assets/files/' . $file_name_two);
                $datatwo = ['tender_id' => $_POST['tid'], 'type' => 2, 'file' => $pathInS3two, 'user_id' => $user->id, 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];
                $querydata = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('tenderfiles', $datatwo)
                        ->execute();
                if ($querydata) {
                    $tender = \common\models\Tender::find()->where(['id' => $_POST['tid']])->one();
                    $tender->financial_status = 1;
                    $tender->qvalue = $_POST['qvalue'];
                    $tender->save();
                    Yii::$app->session->setFlash('success', "Financial Bid Opened");
                } else {
                    Yii::$app->session->setFlash('error', "Financial Bid Not Opened");
                }
                return $this->redirect(array('site/' . $this->action->id . ''));
            }
        } catch (S3Exception $e) {
            die('Error:' . $e->getMessage());
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function actionAocstatus() {
        $user = Yii::$app->user->identity;

        if ($_POST['contractor'] == '') {
            $model = new \common\models\Contractor();
            $model->firm = @$_POST['firm'];
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
            $lid = Yii::$app->db->getLastInsertID();
        } else {
            $lid = $_POST['contractor'];
        }


        require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
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

        $file_name_one = time() . $_FILES['fileone']['name'];
        $file_name_two = time() . $_FILES['filetwo']['name'];
        $file_name_three = time() . $_FILES['filethree']['name'];
        $file_tmp_one = $_FILES['fileone']['tmp_name'];
        $file_tmp_two = $_FILES['filetwo']['tmp_name'];
        $file_tmp_three = $_FILES['filethree']['tmp_name'];
        move_uploaded_file($file_tmp_one, "assets/files/" . $file_name_one);
        move_uploaded_file($file_tmp_two, "assets/files/" . $file_name_two);
        move_uploaded_file($file_tmp_three, "assets/files/" . $file_name_three);

        $keyNameone = 'files/' . $file_name_one;
        $keyNametwo = 'files/' . $file_name_two;
        $keyNamethree = 'files/' . $file_name_three;

        $pathInS3one = 'http://s3.us-east-2.amazonaws.com/' . Yii::$app->params['bucketName'] . '/' . $keyNameone;
        $pathInS3two = 'http://s3.us-east-2.amazonaws.com/' . Yii::$app->params['bucketName'] . '/' . $keyNametwo;
        $pathInS3three = 'http://s3.us-east-2.amazonaws.com/' . Yii::$app->params['bucketName'] . '/' . $keyNamethree;

        try {
// Uploaded:
            $fileone = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/files/" . $file_name_one;
            $filetwo = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/files/" . $file_name_two;
            $filethree = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/files/" . $file_name_three;
            $fileuploadone = $s3->putObject(
                    array(
                        'Bucket' => Yii::$app->params['bucketName'],
                        'Key' => $keyNameone,
                        'SourceFile' => $fileone,
                        'ACL' => 'public-read-write'
                    )
            );
            $fileuploadtwo = $s3->putObject(
                    array(
                        'Bucket' => Yii::$app->params['bucketName'],
                        'Key' => $keyNametwo,
                        'SourceFile' => $filetwo,
                        'ACL' => 'public-read-write'
                    )
            );
            $fileuploadthree = $s3->putObject(
                    array(
                        'Bucket' => Yii::$app->params['bucketName'],
                        'Key' => $keyNamethree,
                        'SourceFile' => $filethree,
                        'ACL' => 'public-read-write'
                    )
            );
            if ($fileuploadone) {
                unlink('assets/files/' . $file_name_one);
                $dataone = ['tender_id' => $_POST['tid'], 'type' => 3, 'file' => $pathInS3one, 'user_id' => $user->id, 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];
                $querydata = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('tenderfiles', $dataone)
                        ->execute();
            }
            if ($fileuploadtwo) {
                unlink('assets/files/' . $file_name_two);
                $datatwo = ['tender_id' => $_POST['tid'], 'type' => 2, 'file' => $pathInS3two, 'user_id' => $user->id, 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];
                $querydata = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('tenderfiles', $datatwo)
                        ->execute();
            }
            if ($fileuploadthree) {
                unlink('assets/files/' . $file_name_three);
                $datathree = ['tender_id' => $_POST['tid'], 'type' => 3, 'file' => $pathInS3three, 'user_id' => $user->id, 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];
                $querydata = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('tenderfiles', $datathree)
                        ->execute();
                if ($querydata) {
                    $tender = \common\models\Tender::find()->where(['id' => $_POST['tid']])->one();
                    $tender->aoc_status = 1;
                    $tender->qvalue = @$_POST['qvalue'];
                    $tender->aoc_date = @$_POST['aoc_date'];
                    $tender->contractor = $lid;
                    $tender->save();
                    Yii::$app->session->setFlash('success', "AOC Completed");
                } else {
                    Yii::$app->session->setFlash('error', "AOC Not Completed");
                }
                return $this->redirect(array('site/tenders'));
            }
        } catch (S3Exception $e) {
            die('Error:' . $e->getMessage());
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

}
