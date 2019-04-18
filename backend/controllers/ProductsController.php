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
use Aws\Common\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Aws\S3\Exception\S3Exception;
use yii\web\UploadedFile;
use app\models\UploadForm;

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
                        'actions' => ['login', 'signup', 'request-password-reset', 'reset-password', 'error', 'backup'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'category-em', 'files', 'addaddress', 'addresses', 'deleteaddress', 'delete-file', 'allfiles', 'fileimages', 'getaccessories', 'uploadfile', 'updatetend', 'searchcontractor', 'updatedetails', 'updatedetailsitems', 'category-civil', 'create-accessory', 'delete-accessory', 'prices', 'create-price', 'accessories', 'getsizes', 'delete-price'],
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
                                $model->mtypefive = @$_POST['mtypefive'][$key];
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
        if ($one == 1) {
            $sizes = \common\models\Size::find()->where(['mtypeone' => $one, 'mtypetwo' => $two, 'mtypethree' => $three])->all();
        } elseif ($one == 5) {
            $sizes = \common\models\Size::find()->where(['mtypeone' => $one])->all();
        }
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

    public function actionUpdatedetails() {
        $tenders = \common\models\ItemDetails::find()->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => 504])->all();
        echo "<pre/>";
        print_r($tenders);
        die();
        if ($tenders) {
            foreach ($tenders as $_ten) {
                $arr = explode(',', $_ten->make);
                $a = array_search("26", $arr);
                $arr[$a] = '21';
                $narr = array_unique($arr);
                $_ten->make = implode(',', $narr);
                $_ten->save();
            }
        }
    }

    public function actionUpdatedetailsitems() {
        $tenders = \common\models\ItemDetails::find()->where(['typefitting' => 49])->all();
        if ($tenders) {
            foreach ($tenders as $_ten) {
                $_ten->typefitting = 7;
                $_ten->save();
            }
        }
    }

    public function actionSearchcontractor() {
        $val = $_REQUEST['val'];
        $contractors = \common\models\Contractor::find()->from([new \yii\db\Expression('{{%contractors}} USE INDEX (firm)')])->where(['like', 'firm', '%' . $val . '%', false])->orderBy(['id' => SORT_DESC])->all();

        echo $this->renderPartial('scontractors', [
            'contractors' => $contractors,
        ]);

        die();
    }

    public function actionBackup() {
        $files = [];
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/backups';
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    $files[] = $file;
                }
                closedir($dh);
            }
        }

        $nfiles = [];
        if ($files) {
            foreach ($files as $_file) {
                if ($_file != '..' && $_file != '.') {
                    $nfiles[] = $_file;
                }
            }
        }

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

        $keyName = 'backups/' . $nfiles['0'];
        $pathInS3 = 'http://s3.us-east-2.amazonaws.com/' . Yii::$app->params['bucketName'] . '/' . $keyName;

        try {
            // Uploaded:
            $file = $dir . '/' . $nfiles['0'];
            $fileupload = $s3->putObject(
                    array(
                        'Bucket' => Yii::$app->params['bucketName'],
                        'Key' => $keyName,
                        'SourceFile' => $file,
                        'ACL' => 'public-read-write'
                    )
            );
            if ($fileupload) {
                unlink($file);
            }
        } catch (S3Exception $e) {
            die('Error:' . $e->getMessage());
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function actionUpdatetend() {
        $approvedtenders = \common\models\Tender::find()->where(['status' => 1, 'aoc_status' => null])->orWhere(['status' => 0])->orderBy(['id' => SORT_DESC])->all();

        if ($approvedtenders) {
            foreach ($approvedtenders as $_tender) {
                $_tender->on_hold = '1';
                $_tender->save();
            }
        }
    }

    public function actionUploadfile() {

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
                $model = \common\models\Files::find()->where(['id' => $_POST['id']])->one();
                $model->did = @$_POST['did'];

                if ($_FILES['file']['name']) {
                    $file_name = time() . '-' . $_FILES['file']['name'];
                    $file_tmp = $_FILES['file']['tmp_name'];
                    move_uploaded_file($file_tmp, "assets/files/" . $file_name);

                    $keyName = 'files/' . $file_name;
                    $pathInS3 = 'http://s3.us-east-2.amazonaws.com/' . Yii::$app->params['bucketName'] . '/' . $keyName;

                    $file = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/files/" . $file_name;

                    $uploader = new MultipartUploader($s3, $file, [
                        'bucket' => Yii::$app->params['bucketName'],
                        'key' => $keyName,
                        'ACL' => 'public-read-write'
                    ]);

                    $fileupload = $uploader->upload();

                    if ($fileupload) {
                        $model->file = $pathInS3;
                        unlink('assets/files/' . $file_name);
                    }
                }

                $model->save();

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "File successfully updated");
                }
            } else {
                $model = new \common\models\Files();
                $model->did = @$_POST['did'];
                $model->user_id = $user->UserId;
                $model->createdon = date('Y-m-d h:i:s');
                $model->status = 1;

                if ($_FILES['file']['name']) {
                    $file_name = time() . '-' . $_FILES['file']['name'];
                    $file_tmp = $_FILES['file']['tmp_name'];
                    move_uploaded_file($file_tmp, "assets/files/" . $file_name);

                    $keyName = 'files/' . $file_name;
                    $pathInS3 = 'http://s3.us-east-2.amazonaws.com/' . Yii::$app->params['bucketName'] . '/' . $keyName;

                    $file = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/files/" . $file_name;

                    $uploader = new MultipartUploader($s3, $file, [
                        'bucket' => Yii::$app->params['bucketName'],
                        'key' => $keyName,
                        'ACL' => 'public-read-write'
                    ]);

                    $fileupload = $uploader->upload();

                    if ($fileupload) {
                        $model->file = $pathInS3;
                        unlink('assets/files/' . $file_name);
                    }
                }

                $files = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('files', $model)
                        ->execute();

                if ($files) {
                    Yii::$app->session->setFlash('success', "File successfully uploaded");
                }
            }

            return $this->redirect(array('products/files'));
            die();
        } else {
            $departments = User::find()->where(['status' => 10, 'group_id' => 5])->orderBy(['name' => SORT_ASC])->all();
            if ($id) {
                $file = \common\models\Files::find()->where(['id' => $id])->one();
            } else {
                $file = [];
            }
            return $this->render('uploadfile', [
                        'departments' => $departments,
                        'file' => $file
            ]);
        }
    }

    public function actionFiles() {
        $files = \common\models\Files::find()->where(['status' => '1'])->all();
        return $this->render('files', [
                    'files' => $files
        ]);
    }

    public function actionDeleteFile() {
        $id = $_GET['id'];
        $delete = \common\models\Files::deleteAll(['id' => $id]);
        if ($delete) {
            Yii::$app->session->setFlash('success', "File successfully deleted");
            return $this->redirect(array('products/files'));
        }
    }

    public function actionAllfiles() {
        $user = Yii::$app->user->identity;
        $files = \common\models\Files::find()->where(['did' => $user->UserId, 'status' => 1])->all();
        return $this->render('allfiles', [
                    'files' => $files
        ]);
    }

    public function actionFileimages($path) {
        $fsize = 90; //icon px width in output
        $field = 'file';

        $ftype = pathinfo($path);
        $extension = $ftype['extension'];

        $pdfImg = Yii::$app->params['IMAGE_URL'] . 'assets/images/pdf.png';
        $jpgImg = Yii::$app->params['IMAGE_URL'] . 'assets/images/jpg.png';
        $jpegImg = Yii::$app->params['IMAGE_URL'] . 'assets/images/jpeg.png';
        $zipImg = Yii::$app->params['IMAGE_URL'] . 'assets/images/zip.png';
        $csvImg = Yii::$app->params['IMAGE_URL'] . 'assets/images/csv.png';
        $pngImg = Yii::$app->params['IMAGE_URL'] . 'assets/images/iconfinder_png.png';
        $xlsxImg = Yii::$app->params['IMAGE_URL'] . 'assets/images/xlsx.png';
        $xlsImg = Yii::$app->params['IMAGE_URL'] . 'assets/images/xls.png';
        $docxImg = Yii::$app->params['IMAGE_URL'] . 'assets/images/docx.png';
        $docImg = Yii::$app->params['IMAGE_URL'] . 'assets/images/doc.png';
        $pptImg = 'http://cdn2.iconfinder.com/data/icons/sleekxp/Microsoft%20Office%202007%20PowerPoint.png';
        $txtImg = 'http://cdn1.iconfinder.com/data/icons/CrystalClear/128x128/mimetypes/txt2.png';
        $audioImg = 'http://cdn2.iconfinder.com/data/icons/oxygen/128x128/mimetypes/audio-x-pn-realaudio-plugin.png';
        $videoImg = 'http://cdn4.iconfinder.com/data/icons/Pretty_office_icon_part_2/128/video-file.png';
        $htmlImg = 'http://cdn1.iconfinder.com/data/icons/nuove/128x128/mimetypes/html.png';
        $fileImg = Yii::$app->params['IMAGE_URL'] . 'assets/images/file.png';

        switch ($extension) {
            case 'pdf':
                $img = $pdfImg;
                break;
            case 'doc':
                $img = $docImg;
                break;
            case 'docx':
                $img = $docxImg;
                break;
            case 'txt':
                $img = $txtImg;
                break;
            case 'xls':
                $img = $xlsImg;
                break;
            case 'xlsx':
                $img = $xlsxImg;
                break;
            case 'xlsm':
                $img = $xlsImg;
                break;
            case 'ppt':
                $img = $pptImg;
                break;
            case 'pptx':
                $img = $pptImg;
                break;
            case 'mp3':
                $img = $audioImg;
                break;
            case 'wmv':
                $img = $videoImg;
                break;
            case 'mp4':
                $img = $videoImg;
                break;
            case 'mpeg':
                $img = $videoImg;
                break;
            case 'html':
                $img = $htmlImg;
                break;
            case 'zip':
                $img = $zipImg;
                break;
            case 'csv':
                $img = $csvImg;
                break;
            case 'png':
                $img = $pngImg;
                break;
            case 'PNG':
                $img = $pngImg;
                break;
            case 'jpg':
                $img = $jpgImg;
                break;
            case 'JPG':
                $img = $jpgImg;
                break;
            case 'jpeg':
                $img = $jpegImg;
                break;
            case 'JPEG':
                $img = $jpegImg;
                break;
            default:
                $img = $fileImg;
                break;
        }
        return $img;
    }

    public function actionAddaddress() {
        $user = Yii::$app->user->identity;
        $id = @$_GET['id'];

        if (isset($_POST['submit'])) {

            if ($_POST['id']) {
                $model = \common\models\Addresses::find()->where(['id' => $_POST['id']])->one();
                $model->command = @$_POST['command'];
                $model->cengineer = @$_POST['cengineer'];
                $model->cwengineer = @$_POST['cwengineer'];
                $model->gengineer = @$_POST['gengineer'];
                $model->contact = @$_POST['contact'];
                $model->email = @$_POST['email'];
                $model->address = @$_POST['address'];

                $address = \common\models\Addresses::find()->where(['status' => 1])->andWhere(['!=', 'id', $_POST['id']]);
                if (isset($_REQUEST['command']) && $_REQUEST['command'] != '') {
                    $address->andWhere(['and',
                        ['command' => $_REQUEST['command']]
                    ]);
                }
                if (isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') {
                    $address->andWhere(['and',
                        ['cengineer' => $_REQUEST['cengineer']]
                    ]);
                }
                if (isset($_REQUEST['cwengineer']) && $_REQUEST['cwengineer'] != '') {
                    $address->andWhere(['and',
                        ['cwengineer' => $_REQUEST['cwengineer']]
                    ]);
                }
                if (isset($_REQUEST['gengineer']) && $_REQUEST['gengineer'] != '') {
                    $address->andWhere(['and',
                        ['gengineer' => $_REQUEST['gengineer']]
                    ]);
                }
                $addressavailable = $address->one();
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Address successfully updated");
                }
                return $this->redirect(array('products/addresses'));
            } else {
                $model = new \common\models\Addresses();
                $model->command = @$_POST['command'];
                $model->cengineer = @$_POST['cengineer'];
                $model->cwengineer = @$_POST['cwengineer'];
                $model->gengineer = @$_POST['gengineer'];
                $model->contact = @$_POST['contact'];
                $model->email = @$_POST['email'];
                $model->address = @$_POST['address'];
                $model->user_id = $user->UserId;
                $model->createdon = date('Y-m-d h:i:s');
                $model->status = 1;

                $address = \common\models\Addresses::find()->where(['status' => 1]);
                if (isset($_REQUEST['command']) && $_REQUEST['command'] != '') {
                    $address->where(['and',
                        ['command' => $_REQUEST['command']]
                    ]);
                }
                if (isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') {
                    $address->andWhere(['and',
                        ['cengineer' => $_REQUEST['cengineer']]
                    ]);
                }
                if (isset($_REQUEST['cwengineer']) && $_REQUEST['cwengineer'] != '') {
                    $address->andWhere(['and',
                        ['cwengineer' => $_REQUEST['cwengineer']]
                    ]);
                }
                if (isset($_REQUEST['gengineer']) && $_REQUEST['gengineer'] != '') {
                    $address->andWhere(['and',
                        ['gengineer' => $_REQUEST['gengineer']]
                    ]);
                }
                $addressavailable = $address->one();

                /* if ($addressavailable) {
                  Yii::$app->session->setFlash('error', "Address already existed");
                  } else {
                  $files = \Yii::$app
                  ->db
                  ->createCommand()
                  ->insert('addresses', $model)
                  ->execute();

                  if ($files) {
                  Yii::$app->session->setFlash('success', "Address successfully added");
                  }
                  } */
                $files = \Yii::$app
                        ->db
                        ->createCommand()
                        ->insert('addresses', $model)
                        ->execute();

                if ($files) {
                    Yii::$app->session->setFlash('success', "Address successfully added");
                }
            }
            return $this->redirect(array('products/addaddress'));
            die();
        } else {
            if ($id) {
                $address = \common\models\Addresses::find()->where(['id' => $id])->one();
            } else {
                $address = [];
            }
            return $this->render('addaddress', [
                        'address' => $address
            ]);
        }
    }

    public function actionAddresses() {

        if (isset($_POST['submit'])) {
           
            $addresses = \common\models\Addresses::find()->where(['status' => 1]);
            if (isset($_REQUEST['command']) && $_REQUEST['command'] != '' && $_REQUEST['command'] != 0 && @$_REQUEST['cengineer'] == '' && @$_REQUEST['cwengineer'] == '' && @$_REQUEST['gengineer'] == '') {
                $addresses->andWhere(['and',
                    ['command' => $_REQUEST['command']],
                    ['cengineer' => null],
                    ['cwengineer' => null],
                    ['gengineer' => null]
                ]);
            }
            if ((isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') && (@$_REQUEST['cwengineer'] == '') && (@$_REQUEST['gengineer'] == '')) {
                $addresses->andWhere(['and',
                    ['cengineer' => $_REQUEST['cengineer']],
                    ['cwengineer' => null],
                    ['gengineer' => null]
                ]);
            }
            if ((isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') && (isset($_REQUEST['cwengineer']) && $_REQUEST['cwengineer'] != '') && (@$_REQUEST['gengineer'] == '')) {
                $addresses->andWhere(['and',
                    ['cengineer' => $_REQUEST['cengineer']],
                    ['cwengineer' => $_REQUEST['cwengineer']],
                    ['gengineer' => null]
                ]);
            }
            if ((isset($_REQUEST['cengineer']) && $_REQUEST['cengineer'] != '') && (isset($_REQUEST['cwengineer']) && $_REQUEST['cwengineer'] != '') && (isset($_REQUEST['gengineer']) && $_REQUEST['gengineer'] != '')) {
                $addresses->andWhere(['and',
                    ['cengineer' => $_REQUEST['cengineer']],
                    ['cwengineer' => $_REQUEST['cwengineer']],
                    ['gengineer' => $_REQUEST['gengineer']]
                ]);
            }
            if ((!isset($_REQUEST['cengineer'])) && (!isset($_REQUEST['cwengineer'])) && (isset($_REQUEST['gengineer']) && $_REQUEST['gengineer'] != '')) {
                $addresses->andWhere(['and',
                    ['cengineer' => null],
                    ['cwengineer' => null],
                    ['gengineer' => $_REQUEST['gengineer']]
                ]);
            }
            if (isset($_REQUEST['command']) && $_REQUEST['command'] != '' && $_REQUEST['command'] != 0 && isset($_REQUEST['gengineer']) && $_REQUEST['gengineer'] != '' && @$_REQUEST['cwengineer'] == '' && @$_REQUEST['cengineer'] == '') {
                $addresses->andWhere(['and',
                    ['cengineer' => null],
                    ['cwengineer' => null],
                    ['gengineer' => $_REQUEST['gengineer']]
                ]);
            }
            $alladdress = $addresses->orderBy(['id' => SORT_DESC])->all();

            if (isset($alladdress) && count($alladdress)) {
                foreach ($alladdress as $_address) {
                    if ($_address->command != '' && $_address->cengineer != '' && $_address->cwengineer != '' && $_address->gengineer != '') {
                        $office = \common\models\Gengineer::find()->where(['cwengineer' => $_address->cwengineer, 'gid' => $_address->gengineer, 'status' => 1])->one();
                        $officename = $office->text;
                    } elseif ($_address->command != '' && $_address->cengineer != '' && $_address->cwengineer != '' && $_address->gengineer == '') {
                        $office = \common\models\Cwengineer::find()->where(['cengineer' => $_address->cengineer, 'cid' => $_address->cwengineer, 'status' => 1])->one();
                        $officename = $office->text;
                    } elseif ($_address->command != '' && $_address->cengineer != '' && $_address->cwengineer == '' && $_address->gengineer == '') {
                        if (!isset($_address->cengineer) && isset($_address->gengineer)) {
                            $office = \common\models\Cengineer::find()->where(['cid' => $_address->gengineer, 'status' => 1])->one();
                        } else {
                            $office = \common\models\Cengineer::find()->where(['cid' => $_address->cengineer, 'status' => 1])->one();
                        }
                        $officename = $office->text;
                    } elseif ($_address->command != '' && $_address->cengineer == '' && $_address->cwengineer == '' && $_address->gengineer == '') {
                        $officename = $this->actionGetcommand($_address->command);
                    } elseif ($_address->command != '' && $_address->cengineer == '' && $_address->cwengineer == '' && $_address->gengineer != '') {
                        $office = \common\models\Cengineer::find()->where(['cid' => $_address->gengineer, 'status' => 1])->one();
                        $officename = $office->text;
                    }
                    $_address->command = $officename;
                }
            }
        } else {
            $alladdress = [];
        }
        return $this->render('addresses', [
                    'addresses' => $alladdress
        ]);
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
                return "ADG (OF and DRDO)  AND CE (R and D) DELHI - MES";
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

    public function actionDeleteaddress() {
        $id = $_GET['id'];
        $addresses = \common\models\Addresses::deleteAll(['id' => $id]);
        if ($addresses) {
            Yii::$app->session->setFlash('success', "Address successfully deleted");
            return $this->redirect(array('products/addresses'));
        }
    }

}
