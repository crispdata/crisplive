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

/**
 * Products controller
 */
class ProductsController extends Controller {

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
                        'actions' => ['logout', 'index', 'category-em', 'getaccessories', 'category-civil', 'create-accessory', 'delete-accessory', 'prices', 'create-price', 'accessories', 'getsizes', 'delete-price'],
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

    public function actionCategoryEm() {
        $user = Yii::$app->user->identity;
        $tids = [];
        $iids = [];
        $allquantity = [];
        $aoctids = [];
        $aociids = [];
        $aocquantity = [];
        $archivetids = [];
        $archiveiids = [];
        $finalquantity = [];
        $finalaocquantity = [];
        $finalarchivequantity = [];
        $archivequantity = [];
        if (isset($_POST['submit'])) {
            $type = $_POST['authtype'];
            $command = $_POST['command'];
            if (isset($command) && $command != 13) {
                $tenders = \common\models\Tender::find()->where(['status' => 1, 'command' => $command])->all();
                $aoctenders = \common\models\Tender::find()->where(['aoc_status' => 1, 'is_archived' => null, 'command' => $command])->all();
                $archivetenders = \common\models\Tender::find()->where(['is_archived' => 1, 'command' => $command])->all();
            } else {
                $tenders = \common\models\Tender::find()->where(['status' => 1])->all();
                $aoctenders = \common\models\Tender::find()->where(['aoc_status' => 1, 'is_archived' => null])->all();
                $archivetenders = \common\models\Tender::find()->where(['is_archived' => 1])->all();
            }


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
                    if ($type == 1) {
                        $size = \common\models\Size::find()->where(['id' => $_idetail->description])->one();
                        $var = preg_replace("/[^0-9\.]/", '', $size->size);
                        $allquantity[] = ['size' => $var, 'quantity' => $_idetail->quantity];
                    } elseif ($type == 2) {
                        $watt = \common\models\Fitting::find()->where(['id' => $_idetail->capacityfitting, 'type' => 2])->one();
                        $var = preg_replace("/[^0-9\.]/", '', $watt->text);
                        $allquantity[] = ['size' => $var, 'quantity' => $_idetail->quantity];
                    }
                }
            }

            if (isset($aoctenders) && count($aoctenders)) {
                foreach ($aoctenders as $_tender) {
                    $aoctids[] = $_tender->id;
                }
            }
            $items = \common\models\Item::find()->where(['tender_id' => $aoctids, 'tenderfour' => $type])->all();
            if (isset($items) && count($items)) {
                foreach ($items as $_item) {
                    $aociids[] = $_item->id;
                }
            }
            $idetails = \common\models\ItemDetails::find()->where(['item_id' => $aociids])->all();
            if (isset($idetails) && count($idetails)) {
                foreach ($idetails as $_idetail) {
                    if ($type == 1) {
                        $size = \common\models\Size::find()->where(['id' => $_idetail->description])->one();
                        $var = preg_replace("/[^0-9\.]/", '', $size->size);
                        $aocquantity[] = ['size' => $var, 'quantity' => $_idetail->quantity];
                    } elseif ($type == 2) {
                        $watt = \common\models\Fitting::find()->where(['id' => $_idetail->capacityfitting, 'type' => 2])->one();
                        $var = preg_replace("/[^0-9\.]/", '', $watt->text);
                        $aocquantity[] = ['size' => $var, 'quantity' => $_idetail->quantity];
                    }
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
                    if ($type == 1) {
                        $size = \common\models\Size::find()->where(['id' => $_idetail->description])->one();
                        $var = preg_replace("/[^0-9\.]/", '', $size->size);
                        $archivequantity[] = ['size' => $var, 'quantity' => $_idetail->quantity];
                    } elseif ($type == 2) {
                        $watt = \common\models\Fitting::find()->where(['id' => $_idetail->capacityfitting, 'type' => 2])->one();
                        $var = preg_replace("/[^0-9\.]/", '', $watt->text);
                        $archivequantity[] = ['size' => $var, 'quantity' => $_idetail->quantity];
                    }
                }
            }

            usort($allquantity, function($a, $b) {
                return $a['size'] <=> $b['size'];
            });
            usort($aocquantity, function($a, $b) {
                return $a['size'] <=> $b['size'];
            });
            usort($archivequantity, function($a, $b) {
                return $a['size'] <=> $b['size'];
            });

            $sizes = [];
            $quantity = 0;
            if (isset($allquantity) && count($allquantity)) {
                foreach ($allquantity as $_quantity) {
                    if (in_array($_quantity['size'], $sizes)) {
                        $quantity += $_quantity['quantity'];
                        $finalquantity[$_quantity['size']] = $quantity;
                    } else {
                        $quantity = 0;
                        $sizes[] = $_quantity['size'];
                        $quantity += $_quantity['quantity'];
                        $finalquantity[$_quantity['size']] = $quantity;
                    }
                }
            }

            $sizes = [];
            $quantity = 0;
            if (isset($aocquantity) && count($aocquantity)) {
                foreach ($aocquantity as $_quantity) {
                    if (in_array($_quantity['size'], $sizes)) {
                        $quantity += $_quantity['quantity'];
                        $finalaocquantity[$_quantity['size']] = $quantity;
                    } else {
                        $quantity = 0;
                        $sizes[] = $_quantity['size'];
                        $quantity += $_quantity['quantity'];
                        $finalaocquantity[$_quantity['size']] = $quantity;
                    }
                }
            }

            $sizes = [];
            $quantity = 0;
            if (isset($archivequantity) && count($archivequantity)) {
                foreach ($archivequantity as $_quantity) {
                    if (in_array($_quantity['size'], $sizes)) {
                        $quantity += $_quantity['quantity'];
                        $finalarchivequantity[$_quantity['size']] = $quantity;
                    } else {
                        $quantity = 0;
                        $sizes[] = $_quantity['size'];
                        $quantity += $_quantity['quantity'];
                        $finalarchivequantity[$_quantity['size']] = $quantity;
                    }
                }
            }
        }

        if (@$type == 1) {
            return $this->render('categoryem', [
                        'finalquantity' => $finalquantity,
                        'finalaocquantity' => $finalaocquantity,
                        'finalarchivequantity' => $finalarchivequantity,
                        'head' => 'Cables',
                        'column' => 'Sqmm',
                        'type' => 1,
                        'unit' => 'RM'
            ]);
        } elseif (@$type == 2) {
            return $this->render('categoryem', [
                        'finalquantity' => $finalquantity,
                        'finalaocquantity' => $finalaocquantity,
                        'finalarchivequantity' => $finalarchivequantity,
                        'head' => 'Lighting',
                        'column' => 'Watt',
                        'type' => 2,
                        'unit' => 'NOS'
            ]);
        } else {
            return $this->render('categoryem', [
                        'finalquantity' => $finalquantity,
                        'finalaocquantity' => $finalaocquantity,
                        'finalarchivequantity' => $finalarchivequantity,
            ]);
        }
    }

    public function actionCategoryCivil() {
        $user = Yii::$app->user->identity;
        $idetails = \common\models\ItemDetails::find()->leftJoin('items', 'itemdetails.item_id = items.id')->orderBy(['itemdetails.id' => SORT_DESC])->all();
        $tenders = \common\models\Tender::find()->where(['>=', 'bid_end_date', date('d-m-Y')])->orderBy(['bid_end_date' => SORT_ASC])->all();
        return $this->render('categorycivil', [
                    'tenders' => $tenders,
                    'items' => $idetails
        ]);
    }

    public function actionPrices() {
        $user = Yii::$app->user->identity;
        $prices = [];
        if (isset($_POST)) {
            if (isset($_POST['mtypesortone']) && $_POST['mtypesortone'] != '' && $_POST['mtypesortone'] == 1) {
                $prices = \common\models\Prices::find()->where(['mtype' => @$_POST['mtypesort'], 'mtypeone' => @$_POST['mtypesortone'], 'mtypetwo' => $_POST['mtypesorttwo'], 'mtypethree' => $_POST['mtypesortthree']])->orderBy(['price' => SORT_ASC])->all();
            }
        }
        return $this->render('prices', [
                    'prices' => $prices
        ]);
    }

    public function actionCreatePrice() {
        $user = Yii::$app->user->identity;
        $id = @$_GET['id'];

        if (isset($_POST['submit'])) {

            if ($_POST['id']) {
                $model = \common\models\Prices::find()->where(['id' => $_POST['id']])->one();
                if (isset($_POST['mtype'])) {
                    $model->mtype = $_POST['mtype'];
                }
                if (isset($_POST['mtypeone'])) {
                    $model->mtypeone = $_POST['mtypeone'];
                }
                if (isset($_POST['mtypetwo'])) {
                    $model->mtypetwo = $_POST['mtypetwo'];
                }
                if (isset($_POST['mtypethree'])) {
                    $model->mtypethree = $_POST['mtypethree'];
                }
                if (isset($_POST['mtypefour'])) {
                    $model->mtypefour = $_POST['mtypefour'];
                }
                if (isset($_POST['mtypefive'])) {
                    $model->mtypefive = $_POST['mtypefive'];
                }

                $model->price = $_POST['price'];

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Price successfully updated");
                }

                return $this->redirect(array('products/prices'));
            } else {
                $model = new \common\models\Prices();

                $prices = explode(',', $_POST['price']);
                $_POST['price'] = $prices;

                if (isset($_POST['price'])) {
                    foreach ($_POST['price'] as $k => $_price) {
                        $singleprice = explode('/', $_price);
                        if (isset($singleprice)) {
                            foreach ($singleprice as $key => $_sprice) {
                                if (isset($_POST['mtype'])) {
                                    $model->mtype = $_POST['mtype'];
                                }
                                if (isset($_POST['mtypeone'])) {
                                    $model->mtypeone = $_POST['mtypeone'];
                                }
                                if (isset($_POST['mtypetwo'])) {
                                    $model->mtypetwo = $_POST['mtypetwo'];
                                }
                                if (isset($_POST['mtypethree'])) {
                                    $model->mtypethree = $_POST['mtypethree'];
                                }
                                $model->mtypefour = $_POST['mtypefour'][$k];
                                $model->mtypefive = $_POST['mtypefive'][$key];
                                $model->price = $_sprice;
                                $model->user_id = $user->UserId;
                                $model->createdon = date('Y-m-d h:i:s');
                                $model->status = 1;

                                $pricesadded = \Yii::$app
                                        ->db
                                        ->createCommand()
                                        ->insert('prices', $model)
                                        ->execute();
                            }
                        }
                    }
                }

                if ($pricesadded) {
                    Yii::$app->session->setFlash('success', "Price successfully added");
                }

                return $this->redirect(array('products/create-price'));
            }

            die();
        } else {
            if ($id) {
                $prices = \common\models\Prices::find()->where(['id' => $id])->one();
                $sizes = \common\models\Size::find()->where(['mtypeone' => $prices->mtypeone, 'mtypetwo' => $prices->mtypetwo, 'mtypethree' => $prices->mtypethree])->all();
            } else {
                $prices = [];
                $sizes = [];
            }

            return $this->render('createprice', [
                        'size' => $prices,
                        'sizes' => $sizes
            ]);
        }
    }

    public function actionGetsizes() {
        $one = $_REQUEST['one'];
        $two = $_REQUEST['two'];
        $three = $_REQUEST['three'];
        $sizes = [];
        $allsizes = [];
        $sizes = \common\models\Size::find()->where(['mtypeone' => $one, 'mtypetwo' => $two, 'mtypethree' => $three])->all();
        if ($sizes) {
            foreach ($sizes as $_size) {
                $allsizes[$_size->id] = $_size->size;
            }
        } else {
            $allsizes['0'] = 'No Sizes';
        }

        echo json_encode(['select' => $allsizes]);
        die();
    }

    public function actionDeletePrice() {
        $id = $_GET['id'];
        $delete = \common\models\Prices::deleteAll(['id' => $id]);
        if ($delete) {
            Yii::$app->session->setFlash('success', "Price successfully deleted");
            return $this->redirect(array('products/prices'));
        }
    }

    public function actionAccessories() {
        $user = Yii::$app->user->identity;

        $accessories = \common\models\Accessories::find()->where(['status' => 1])->orderBy(['text' => SORT_ASC])->all();
        return $this->render('accessories', [
                    'accessories' => $accessories
        ]);
    }

    public function actionCreateAccessory() {
        $user = Yii::$app->user->identity;
        $id = @$_GET['id'];

        if (isset($_POST['submit'])) {

            if ($_POST['id']) {
                $model = \common\models\Accessories::find()->where(['id' => $_POST['id']])->one();

                $accessory = \common\models\Accessories::find()->where(['text' => $_POST['text']])->andWhere(['!=', 'id', $_POST['id']])->one();
                $model->text = $_POST['text'];

                if ($accessory) {
                    Yii::$app->session->setFlash('error', "Accessory already existed");
                } else {
                    if ($model->save()) {
                        Yii::$app->session->setFlash('success', "Accessory successfully updated");
                    }
                }
                return $this->redirect(array('products/accessories'));
            } else {
                $model = new \common\models\Accessories();
                $model->text = $_POST['text'];
                $model->user_id = $user->UserId;
                $model->createdon = date('Y-m-d h:i:s');
                $model->status = 1;
                $accessory = \common\models\Accessories::find()->where(['text' => $_POST['text']])->one();
                if ($accessory) {
                    Yii::$app->session->setFlash('error', "Accessory already existed");
                } else {
                    $accessorys = \Yii::$app
                            ->db
                            ->createCommand()
                            ->insert('accessories', $model)
                            ->execute();


                    if ($accessorys) {
                        Yii::$app->session->setFlash('success', "Accessory successfully added");
                    }
                }
                return $this->redirect(array('products/create-accessory'));
            }
            die();
        } else {
            if ($id) {
                $accessory = \common\models\Accessories::find()->where(['id' => $id])->one();
            } else {
                $accessory = [];
            }

            return $this->render('createaccessory', [
                        'accessory' => $accessory
            ]);
        }
    }

    public function actionDeleteAccessory() {
        $id = $_GET['id'];
        $delete = \common\models\Accessories::deleteAll(['id' => $id]);
        if ($delete) {
            Yii::$app->session->setFlash('success', "Accessory successfully deleted");
            return $this->redirect(array('products/accessories'));
        }
    }
    
    public function actionGetaccessories() {
        $user = Yii::$app->user->identity;
        $alltypes = [];

        $types = \common\models\Accessories::find()->where(['status' => 1])->all();

        if ($types) {
            foreach ($types as $_type) {
                $alltypes[$_type->id] = $_type->text;
            }
        } else {
            $alltypes['0'] = 'No Accessories';
        }

        echo json_encode(['alltypes' => $alltypes]);
        die;
    }

}