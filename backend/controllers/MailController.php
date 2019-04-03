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
use yii\web\UploadedFile;
use app\models\UploadForm;

/**
 * Mail controller
 */
class MailController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'signup', 'request-password-reset', 'reset-password', 'error', 'sendmail'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'sendmess', 'uploadawsdata', 'mlogs', 'clogs', 'updatelogs', 'resendmail', 'data', 'getdata'],
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
        $tenders = \common\models\Tender::find()->where(['status' => 1])->all();
        $idetails = \common\models\ItemDetails::find()->where(['itemdetails.status' => '1', 'itemdetails.user_id' => $user->id])->leftJoin('items', 'itemdetails.item_id = items.id')->orderBy(['itemdetails.id' => SORT_DESC])->all();
        return $this->render('index', [
                    'tenders' => $tenders,
                    'items' => $idetails
        ]);
    }

    public function actionSendmess() {
        require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
        $sid = "AC0c1ed11285f8d4d6770a5c1069d0a3a2"; // Your Account SID from www.twilio.com/console
        $token = "2b14cf4654294318c3286936e5375c90"; // Your Auth Token from www.twilio.com/console
        $client = new Client($sid, $token);

        $message = $client->messages->create(
                "whatsapp:+917559777007", // Text this number
                array(
            "body" => "Testing all!",
            "from" => "whatsapp:+14155238886"
                )
        );

        print $message->sid;
    }

    public function actionData() {
        require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
        $addr = $_SERVER['REMOTE_ADDR'];
        $baseURL = Yii::$app->params['BASE_URL'];

        $cables = \common\models\Make::find()->where(['mtype' => 1])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
        $lights = \common\models\Make::find()->where(['mtype' => 2])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
        $cements = \common\models\Make::find()->where(['mtype' => 14])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
        $rsteel = \common\models\Make::find()->where(['mtype' => 15])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
        $ssteel = \common\models\Make::find()->where(['mtype' => 16])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
        $nsteel = \common\models\Make::find()->where(['mtype' => 17])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
        //$logs = \common\models\Logs::find()->where(['status' => 1,])->andWhere(['<=', 'createdon', '2019-02-13'])->orderBy(['id' => SORT_ASC])->all();
        return $this->render('data', [
                    'cables' => $cables,
                    'lights' => $lights,
                    'cements' => $cements,
                    'rsteel' => $rsteel,
                    'ssteel' => $ssteel,
                    'nsteel' => $nsteel,
        ]);
    }

    public function actionGetdata() {
        $requestdata = $_POST;
        require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
        $imageURL = Yii::$app->params['IMAGE_URL'];
        $fileURL = Yii::$app->params['FILE_URL'];
        $user = Yii::$app->user->identity;
        $data = [];
        $finalmakes = [];
        $alldetails = [];
        $newidetails = [];
        $size = [];
        $tfit = [];
        $cfit = [];
        $uniqueids = [];
        $array = [];

        if (isset($requestdata['cables']) && count($requestdata['cables'])) {
            $makes = implode(',', @$requestdata['cables']);
        } else {
            $makes = implode(',', @$requestdata['lighting']);
        }

        $tenders = \common\models\Tender::find()->leftJoin('items', 'tenders.id = items.tender_id')->leftJoin('itemdetails', 'items.id = itemdetails.item_id')->where(['tenders.aoc_status' => 1, 'tenders.is_archived' => null, 'items.tenderfour' => $requestdata['authtype']])->andWhere('find_in_set(:key2, itemdetails.make)', [':key2' => $makes])->all();

        if ($tenders) {
            foreach ($tenders as $_tender) {
                $tdetails = '';
                $command = Sitecontroller::actionGetcommand($_tender->command);
                if (!isset($_tender->cengineer) && isset($_tender->gengineer)) {
                    $cengineer = \common\models\Cengineer::find()->where(['cid' => $_tender->gengineer, 'status' => 1])->one();
                } else {
                    $cengineer = \common\models\Cengineer::find()->where(['cid' => $_tender->cengineer, 'status' => 1])->one();
                }
                $cwengineer = \common\models\Cwengineer::find()->where(['cid' => $_tender->cwengineer, 'status' => 1])->one();
                $gengineer = \common\models\Gengineer::find()->where(['gid' => $_tender->gengineer, 'status' => 1])->one();
                $items = \common\models\Item::find()->where(['tender_id' => $_tender->id, 'status' => 1])->all();
                $tdetails = @$command . ' ' . @$cengineer->text . ' ' . @$cwengineer->text . ' ' . @$gengineer->text;
                if ($items) {
                    foreach ($items as $_item) {
                        $idetails = \common\models\ItemDetails::find()->where(['item_id' => $_item->id])->one();
                        if ($idetails) {
                            $imakes = explode(',', $idetails->make);
                            $descfull = '';
                            if ($_item->tendertwo != '' && $_item->tendertwo != 0) {
                                $two = Sitecontroller::actionTendertwo($_item->tendertwo);
                                $descfull .= $two . ',';
                            }
                            if ($_item->tenderthree != '' && $_item->tenderthree != 0) {
                                $three = Sitecontroller::actionTenderthree($_item->tenderthree);
                                $descfull .= $three . ',';
                            }
                            if ($_item->tenderfour != '' && $_item->tenderfour != 0) {
                                $four = Sitecontroller::actionTenderfour($_item->tenderfour);
                                $descfull .= $four . ',';
                            }
                            if ($_item->tenderfour == 1) {
                                if ($_item->tenderfive != '' && $_item->tenderfive != 0) {
                                    $five = Sitecontroller::actionTenderfive($_item->tenderfive);
                                    $descfull .= $five . ',';
                                }
                            }
                            if ($_item->tendersix != '' && $_item->tendersix != 0) {
                                $six = Sitecontroller::actionTendersix($_item->tendersix);
                                $descfull .= $six . ',';
                            }
                            if ($_item->tenderfour == 1) {
                                $itemtype = 'Cables';
                            } elseif ($_item->tenderfour == 2) {
                                $itemtype = 'Lighting';
                            } elseif ($_item->tendertwo == 14) {
                                $itemtype = 'Cement';
                            } elseif ($_item->tendertwo == 15) {
                                $itemtype = 'Reinforcement Steel';
                            } elseif ($_item->tendertwo == 16) {
                                $itemtype = 'Structural Steel';
                            } elseif ($_item->tendertwo == 17) {
                                $itemtype = 'Non Structural Steel';
                            }
                            $descfull = rtrim($descfull, ',');
                            $i = 0;
                            $allmakes = '';
                            $makenameall = '';
                            if (@$imakes) {
                                foreach ($imakes as $mid) {
                                    //$makename = \common\models\Make::find()->where(['id' => $mid])->one();
                                    //if (@$makename) {
                                    $makenameall .= $mid . ',';
                                    //}
                                }
                            }
                            $allmakes = rtrim($makenameall, ',');
                            foreach ($imakes as $_make) {
                                if (isset($idetails->description)) {
                                    $size = \common\models\Size::find()->where(['id' => $idetails->description])->one();
                                }
                                if (isset($idetails->typefitting) && isset($idetails->capacityfitting)) {
                                    $tfit = \common\models\Fitting::find()->where(['id' => $idetails->typefitting, 'type' => 1])->one();
                                    $cfit = \common\models\Fitting::find()->where(['id' => $idetails->capacityfitting, 'type' => 2])->one();
                                }
                                $contractor = \common\models\Contractor::find()->where(['id' => $_tender->contractor])->one();
                                if ($idetails->core == 1) {
                                    $core = '1 Core';
                                } elseif ($idetails->core == 2) {
                                    $core = '2 Core';
                                } elseif ($idetails->core == 3) {
                                    $core = '3 Core';
                                } elseif ($idetails->core == 4) {
                                    $core = '3.5 Core';
                                } elseif ($idetails->core == 5) {
                                    $core = '4 Core';
                                } elseif ($idetails->core == 6) {
                                    $core = '5 Core';
                                } elseif ($idetails->core == 7) {
                                    $core = '6 Core';
                                } elseif ($idetails->core == 8) {
                                    $core = '7 Core';
                                } elseif ($idetails->core == 9) {
                                    $core = '10 Core';
                                }

                                if (isset($_item->tenderfour)) {
                                    $ttype = $_item->tenderfour;
                                } else {
                                    $ttype = $_item->tendertwo;
                                }
                                $foo = (str_replace(',', '', $_tender->qvalue) / 100000);
                                $amount = number_format((float) $foo, 2, '.', '');
                                $newidetails['itemtender'] = $idetails->itemtender;
                                $newidetails['tdetails'] = $tdetails;
                                $newidetails['idetails'] = $descfull;
                                $newidetails['sizes'] = @$size->size;
                                $newidetails['units'] = $idetails->units;
                                $newidetails['quantity'] = $idetails->quantity . '.00';
                                $newidetails['make'] = $_make;
                                $newidetails['itype'] = $itemtype;
                                $newidetails['allmakes'] = $allmakes;
                                $newidetails['core'] = @$core;
                                $newidetails['typefitting'] = @$tfit->text;
                                $newidetails['capacityfitting'] = @$cfit->text;
                                $newidetails['itemid'] = $idetails->item_id;
                                $newidetails['firm'] = @$contractor->firm;
                                $newidetails['cperson'] = @$contractor->name;
                                $newidetails['caddress'] = @$contractor->address;
                                $newidetails['ccontact'] = @$contractor->contact;
                                $newidetails['cemail'] = @$contractor->email;
                                $newidetails['tid'] = $_tender->id;
                                $newidetails['cvalue'] = $amount;
                                $newidetails['ttype'] = $ttype;
                                $alldetails[] = $newidetails;
                                $i++;
                            }
                        }
                    }
                }
            }
        }

        if ($alldetails) {
            foreach ($alldetails as $k => $_all) {
                $makename = \common\models\Make::find()->where(['id' => $_all['make']])->one();
                $tender = \common\models\Tender::find()->where(['id' => $_all['tid']])->one();
                $datatender[$k] = $alldetails[$k];
                $datatender[$k]['ref'] = $tender['tender_id'];
                $datatender[$k]['mid'] = @$makename->id;
                $datatender[$k]['makename'] = @$makename->make;
                $datatender[$k]['email'] = @$makename->email;
            }
        }

        if (@$datatender) {
            foreach ($datatender as $_make) {
                if (array_key_exists($_make['make'], $data)) {
                    $data[$_make['make']][] = $_make;
                } else {
                    $data[$_make['make']][] = $_make;
                }
            }
        }

        if (isset($data) && count($data)) {
            foreach ($data as $k => $_cldata) {
                foreach ($_cldata as $key => $cldata) {
                    $singlemake = [];
                    if (isset($cldata['ttype']) && ($cldata['ttype'] == 1 || $cldata['ttype'] == 2)) {
                        if (isset($cldata['allmakes'])) {
                            $singlemake = explode(',', $cldata['allmakes']);
                            $clmakename = '';
                            $allclmakes = '';
                            if (isset($singlemake) && count($singlemake)) {
                                foreach ($singlemake as $__smake) {
                                    $makename = \common\models\Make::find()->where(['id' => $__smake])->one();
                                    if (@$makename) {
                                        $clmakename .= $makename->make . ',';
                                    }
                                }
                            }
                            $allclmakes = rtrim($clmakename, ',');
                        }
                        $data[$k][$key]['allmakes'] = $allclmakes;
                    } else {
                        if (isset($cldata['allmakes'])) {
                            $singlemake = explode(',', $cldata['allmakes']);
                            $clmakename = '';
                            $allclmakes = '';
                            if (isset($singlemake) && count($singlemake)) {
                                foreach ($singlemake as $__smake) {
                                    $makename = \common\models\Make::find()->where(['id' => $__smake])->one();
                                    if (@$makename) {
                                        $clmakename = $makename->make;
                                    }
                                    $cldata['allmakes'] = $clmakename;
                                    unset($data[$k][$key]);
                                    $data[$k][] = $cldata;
                                }
                            }
                        }
                    }
                }
            }
        }


        $required = '';
        $reqdetails = [];
        $requiredlight = '';
        $reqdetailslight = [];
        $particulardata = [];
        if (isset($requestdata['cables']) && count($requestdata['cables'])) {
            $reqdetails = @$requestdata['cables'];
            if (isset($data) && count($data)) {
                foreach ($data as $k => $___data) {
                    if (in_array($k, $reqdetails)) {
                        $particulardata[] = $___data;
                    }
                }
            }
        }
        if (isset($requestdata['lighting']) && count($requestdata['lighting'])) {
            $requiredlight = @$requestdata['lighting'];
            if (isset($data) && count($data)) {
                foreach ($data as $k => $___data) {
                    if (in_array($k, $requiredlight)) {
                        $particulardata[] = $___data;
                    }
                }
            }
        }
        $filestosend = [];
        $tenderids = [];
        $itemids = [];
        if (isset($particulardata) && count($particulardata)) {
            $mailnum = 1;
            foreach ($particulardata as $k => $_data) {
                $header = [];
                $i = 0;
                $sno = 1;
                $tid = [];
                $firmid = [];
                $final = [];
                foreach ($_data as $key => $__data) {

                    if ($i == 0) {
                        if ($__data['ttype'] == 1) {
                            $header[] = "Sr.No." . "\t";
                            $header[] = "Tender Id" . "\t";
                            $header[] = "Amount of Contract (In Lakhs)" . "\t";
                            $header[] = "Details of Contracting Office" . "\t";
                            $header[] = "Item Details" . "\t";
                            $header[] = "Size" . "\t";
                            $header[] = "Core" . "\t";
                            $header[] = "Units" . "\t";
                            $header[] = "Quantity" . "\t";
                            $header[] = "All Makes In Contract" . "\t";
                            $header[] = "Name of Contractor" . "\t";
                            $header[] = "Name of Contact Person" . "\t";
                            $header[] = "Address of Contractor" . "\t";
                            $header[] = "Contact Number" . "\t";
                            $header[] = "E-mail ID" . "\t";
                        } elseif ($__data['ttype'] == 2) {
                            $header[] = "Sr.No." . "\t";
                            $header[] = "Tender Id" . "\t";
                            $header[] = "Amount of Contract (In Lakhs)" . "\t";
                            $header[] = "Details of Contracting Office" . "\t";
                            $header[] = "Item Details" . "\t";
                            $header[] = "Type of Fitting" . "\t";
                            $header[] = "Capacity of Fitting" . "\t";
                            $header[] = "Units" . "\t";
                            $header[] = "Quantity" . "\t";
                            $header[] = "All Makes In Contract" . "\t";
                            $header[] = "Name of Contractor" . "\t";
                            $header[] = "Name of Contact Person" . "\t";
                            $header[] = "Address of Contractor" . "\t";
                            $header[] = "Contact Number" . "\t";
                            $header[] = "E-mail ID" . "\t";
                        }
//$datas = '';
//$datas .= join($header) . "\n";
                        $final[] = $header;
                    }


                    if ($__data['ttype'] == 1) {
                        $arrayData = [];
                        if (in_array($__data['ref'], $tid)) {
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                        } else {
                            $arrayData[] = $sno;

                            $arrayData[] = $__data['ref'];
                            $arrayData[] = $__data['cvalue'];
                            $arrayData[] = $__data['tdetails'];
                            $sno++;
                        }
//$arrayData[] = $__data['ref'];
                        $arrayData[] = $__data['idetails'];
//$arrayData[] = $__data['itemtender'];
                        $arrayData[] = @$__data['sizes'];
                        $arrayData[] = @$__data['core'];
                        $arrayData[] = $__data['units'];
                        $arrayData[] = $__data['quantity'];
//$arrayData[] = $__data['makename'];
                        $arrayData[] = $__data['allmakes'];
                        if (in_array($__data['ref'], $tid) && in_array($__data['firm'], $firmid)) {
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                        } else {
                            $firmid[] = $__data['firm'];
                            $arrayData[] = $__data['firm'];
                            $arrayData[] = $__data['cperson'];
                            $arrayData[] = $__data['caddress'];
                            $arrayData[] = $__data['ccontact'];
                            $arrayData[] = $__data['cemail'];
                        }
                        $tid[] = $__data['ref'];
                        $final[] = $arrayData;
//$datas .= join("\t", $arrayData) . "\n";
                    } elseif ($__data['ttype'] == 2) {
                        $arrayData = [];
                        if (in_array($__data['ref'], $tid)) {
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                        } else {
                            $arrayData[] = $sno;

                            $arrayData[] = $__data['ref'];
                            $arrayData[] = $__data['cvalue'];
                            $arrayData[] = $__data['tdetails'];
                            $sno++;
                        }
                        $arrayData[] = $__data['idetails'];
//$arrayData[] = $__data['itemtender'];
                        $arrayData[] = @$__data['typefitting'];
                        $arrayData[] = @$__data['capacityfitting'];
                        $arrayData[] = $__data['units'];
                        $arrayData[] = $__data['quantity'];
//$arrayData[] = $__data['makename'];
                        $arrayData[] = $__data['allmakes'];
                        if (in_array($__data['ref'], $tid) && in_array($__data['firm'], $firmid)) {
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                        } else {
                            $firmid[] = $__data['firm'];
                            $arrayData[] = $__data['firm'];
                            $arrayData[] = $__data['cperson'];
                            $arrayData[] = $__data['caddress'];
                            $arrayData[] = $__data['ccontact'];
                            $arrayData[] = $__data['cemail'];
                        }
                        $tid[] = $__data['ref'];
                        $final[] = $arrayData;
//$datas .= join("\t", $row1) . "\n";
                    }
                    $i++;
                    $tenderids[] = $__data['tid'];
                    $itemids[] = $__data['itemid'];
                }


                $spreadsheet = new Spreadsheet();  /* ----Spreadsheet object----- */
//$activeSheet = $spreadsheet->getActiveSheet();
//$spreadsheet->getActiveSheet()->freezePane('D2');
//$arrayData = $datas;

                $activeSheet = $spreadsheet->getActiveSheet()
                        ->fromArray(
                        $final, // The data to set
                        NULL, // Array values with this value will not be set
                        'A1'         // Top left coordinate of the worksheet range where
                );
                $activeSheet->getStyle('A1:O1')->getFont()->setSize(11);
                $styleArray = [
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
                    ]
                ];
                $activeSheet->getStyle('A1:O1')->applyFromArray($styleArray);

                $styleArrayinside = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
                    ]
                ];

                $styleArraylimited = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
                    ]
                ];
                $styleArrayborder = [
                    'borders' => [
                        'outline' => [
                            'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '808080']
                        ],
                    ],
                ];



                if ($final) {
                    $p = 2;
                    $c = 1;
                    foreach ($final as $_final) {
                        if ($p > 2) {
                            if ($_final['0'] != '') {
                                $activeSheet->getStyle('A' . $c . ':O' . $c . '')->getFill()
                                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                        ->getStartColor()->setARGB('D3D3D3');
                                //$activeSheet->getStyle('A' . $c . ':O' . $c . '')->getBorders()->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '808080']]]);
                            }
                        }
                        $activeSheet->getStyle('A' . $c . ':O' . $c . '')->getBorders()->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '808080']]]);
                        $activeSheet->getStyle('D' . $p . ':E' . $p . '')->applyFromArray($styleArrayinside);
                        $activeSheet->getStyle('J' . $p . ':M' . $p . '')->applyFromArray($styleArrayinside);
                        $activeSheet->getStyle('A' . $p . ':C' . $p . '')->applyFromArray($styleArraylimited);
                        $activeSheet->getStyle('F' . $p . ':I' . $p . '')->applyFromArray($styleArraylimited);
                        $activeSheet->getStyle('N' . $p . ':O' . $p . '')->applyFromArray($styleArraylimited);
                        $p++;
                        $c++;
                    }
                }

                $path = $_SERVER['DOCUMENT_ROOT'] . '/admin/images/clogo.png';
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing();
                $drawing->setName('Crispdata logo');
                $drawing->setPath($path);
                $drawing->setHeight(36); // logo height
                $activeSheet->getHeaderFooter()->addImage($drawing, \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter::IMAGE_HEADER_CENTER);

                //$activeSheet->getStyle()->applyFromArray($styleArrayborder);
                //$activeSheet->getStyle('A1:O3')->getBorders()->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_DASHDOT, 'color' => ['rgb' => '808080']]]);
                $activeSheet->getStyle('C1:C' . $activeSheet->getHighestRow())->getNumberFormat()->setFormatCode('0.00');
                $activeSheet->getStyle('I1:I' . $activeSheet->getHighestRow())->getNumberFormat()->setFormatCode('0.00');
                $activeSheet->getStyle('D1:D' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('J1:J' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('C1:C' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('E1:E' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('K1:K' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('L1:L' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('M1:M' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('N1:N' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('O1:O' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('A1:A' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('B1:B' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('F1:F' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('G1:G' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('H1:H' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('I1:I' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);

                $activeSheet->getStyle('A1:O1')
                        ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);


                $cellIterator = $activeSheet->getRowIterator()->current()->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(true);
                /** @var PHPExcel_Cell $cell */
                foreach ($cellIterator as $cell) {
                    if ($cell->getColumn() == 'D') {
                        $activeSheet->getColumnDimension('D')->setWidth(30);
                    } elseif ($cell->getColumn() == 'J') {
                        $activeSheet->getColumnDimension('J')->setWidth(40);
                    } elseif ($cell->getColumn() == 'C') {
                        $activeSheet->getColumnDimension('C')->setWidth(20);
                    } elseif ($cell->getColumn() == 'E') {
                        $activeSheet->getColumnDimension('E')->setWidth(20);
                    } elseif ($cell->getColumn() == 'K' || $cell->getColumn() == 'L' || $cell->getColumn() == 'M' || $cell->getColumn() == 'N' || $cell->getColumn() == 'O') {
                        $activeSheet->getColumnDimension('K')->setWidth(30);
                        $activeSheet->getColumnDimension('L')->setWidth(30);
                        $activeSheet->getColumnDimension('M')->setWidth(30);
                        $activeSheet->getColumnDimension('N')->setWidth(30);
                        $activeSheet->getColumnDimension('O')->setWidth(30);
                    } else {
                        $activeSheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                    }
                }


// Create Excel file and sve in your directory
                $writer = new Xlsx($spreadsheet);

                header('Content-Type: application/application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . str_replace(' ', '_', $__data['makename']) . ' - ' . $__data['itype'] . '.xlsx"');
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                die();
                /* $writer->save("" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace('/', '_', str_replace(' ', '_', $__data['makename'])) . ' - ' . $__data['itype'] . ".xlsx");
                  $url = Yii::$app->params['FILE_URL'].'web/pdf/' . str_replace('/', '_', str_replace(' ', '_', $__data['makename'])) . ' - ' . $__data['itype'] . ".xlsx";
                  echo $url;
                  die(); */
//file_put_contents("" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace(' ', '_', $__data['makename']) . ".xlsx", $datas);
                //$filename = "" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace(' ', '_', $__data['makename']) . ' - ' . $__data['itype'] . ".xlsx";
                //$filenamenew = "" . $fileURL . "web/pdf/" . str_replace(' ', '_', $__data['makename']) . ' - ' . $__data['itype'] . ".xlsx";
//$file = $this->upload($filename);
                //$filestosend[] = ['name' => str_replace(' ', '_', $__data['makename']) . ' - ' . $__data['itype'] . ".xlsx", 'path' => $filename];
                $mailnum++;
            }
        } else {
            Yii::$app->session->setFlash('error', "No Data Available!");
            return $this->redirect(array('mail/data'));
        }
    }

    public function actionUploadawsdata() {
        require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
        $imageURL = Yii::$app->params['IMAGE_URL'];
        $fileURL = Yii::$app->params['FILE_URL'];
        $user = Yii::$app->user->identity;
        $data = [];
        $finalmakes = [];
        $alldetails = [];
        $newidetails = [];
        $size = [];
        $tfit = [];
        $cfit = [];
        $uniqueids = [];
        $array = [];

        $logs = \common\models\Logs::find()->where(['status' => 1])->andWhere(['in', 'id', [194]])->orderBy(['id' => SORT_ASC])->all();

        if (isset($logs) && count($logs)) {
            foreach ($logs as $_log) {
                $newtids = [];
                $newttdd = [];
                $newtids = explode(',', $_log->tid);
                $newttdd = array_unique($newtids);
                $tenders = \common\models\Tender::find()->where(['in', 'id', $newttdd])->orderBy(['id' => SORT_DESC])->all();

                if ($tenders) {
                    foreach ($tenders as $_tender) {
                        $tdetails = '';
                        $command = Sitecontroller::actionGetcommand($_tender->command);
                        if (!isset($_tender->cengineer) && isset($_tender->gengineer)) {
                            $cengineer = \common\models\Cengineer::find()->where(['cid' => $_tender->gengineer, 'status' => 1])->one();
                        } else {
                            $cengineer = \common\models\Cengineer::find()->where(['cid' => $_tender->cengineer, 'status' => 1])->one();
                        }
                        $cwengineer = \common\models\Cwengineer::find()->where(['cid' => $_tender->cwengineer, 'status' => 1])->one();
                        $gengineer = \common\models\Gengineer::find()->where(['gid' => $_tender->gengineer, 'status' => 1])->one();
                        $items = \common\models\Item::find()->where(['tender_id' => $_tender->id, 'status' => 1])->all();
                        $tdetails = @$command . ' ' . @$cengineer->text . ' ' . @$cwengineer->text . ' ' . @$gengineer->text;
                        if ($items) {
                            foreach ($items as $_item) {
                                $idetails = \common\models\ItemDetails::find()->where(['item_id' => $_item->id])->one();
                                if ($idetails) {
                                    $imakes = explode(',', $idetails->make);
                                    $descfull = '';
                                    if ($_item->tendertwo != '' && $_item->tendertwo != 0) {
                                        $two = Sitecontroller::actionTendertwo($_item->tendertwo);
                                        $descfull .= $two . ',';
                                    }
                                    if ($_item->tenderthree != '' && $_item->tenderthree != 0) {
                                        $three = Sitecontroller::actionTenderthree($_item->tenderthree);
                                        $descfull .= $three . ',';
                                    }
                                    if ($_item->tenderfour != '' && $_item->tenderfour != 0) {
                                        $four = Sitecontroller::actionTenderfour($_item->tenderfour);
                                        $descfull .= $four . ',';
                                    }
                                    if ($_item->tenderfour == 1) {
                                        if ($_item->tenderfive != '' && $_item->tenderfive != 0) {
                                            $five = Sitecontroller::actionTenderfive($_item->tenderfive);
                                            $descfull .= $five . ',';
                                        }
                                    }
                                    if ($_item->tendersix != '' && $_item->tendersix != 0) {
                                        $six = Sitecontroller::actionTendersix($_item->tendersix);
                                        $descfull .= $six . ',';
                                    }
                                    if ($_item->tenderfour == 1) {
                                        $itemtype = 'Cables';
                                    } elseif ($_item->tenderfour == 2) {
                                        $itemtype = 'Lighting';
                                    } elseif ($_item->tendertwo == 14) {
                                        $itemtype = 'Cement';
                                    } elseif ($_item->tendertwo == 15) {
                                        $itemtype = 'Reinforcement Steel';
                                    } elseif ($_item->tendertwo == 16) {
                                        $itemtype = 'Structural Steel';
                                    } elseif ($_item->tendertwo == 17) {
                                        $itemtype = 'Non Structural Steel';
                                    }
                                    $descfull = rtrim($descfull, ',');
                                    $i = 0;
                                    $allmakes = '';
                                    $makenameall = '';
                                    if (@$imakes) {
                                        foreach ($imakes as $mid) {
                                            $makename = \common\models\Make::find()->where(['id' => $mid])->one();
                                            if (@$makename) {
                                                $makenameall .= $makename->make . ',';
                                            }
                                        }
                                    }
                                    $allmakes = rtrim($makenameall, ',');
                                    foreach ($imakes as $_make) {
                                        if (isset($idetails->description)) {
                                            $size = \common\models\Size::find()->where(['id' => $idetails->description])->one();
                                        }
                                        if (isset($idetails->typefitting) && isset($idetails->capacityfitting)) {
                                            $tfit = \common\models\Fitting::find()->where(['id' => $idetails->typefitting, 'type' => 1])->one();
                                            $cfit = \common\models\Fitting::find()->where(['id' => $idetails->capacityfitting, 'type' => 2])->one();
                                        }
                                        $contractor = \common\models\Contractor::find()->where(['id' => $_tender->contractor])->one();
                                        if ($idetails->core == 1) {
                                            $core = '1 Core';
                                        } elseif ($idetails->core == 2) {
                                            $core = '2 Core';
                                        } elseif ($idetails->core == 3) {
                                            $core = '3 Core';
                                        } elseif ($idetails->core == 4) {
                                            $core = '3.5 Core';
                                        } elseif ($idetails->core == 5) {
                                            $core = '4 Core';
                                        } elseif ($idetails->core == 6) {
                                            $core = '5 Core';
                                        } elseif ($idetails->core == 7) {
                                            $core = '6 Core';
                                        } elseif ($idetails->core == 8) {
                                            $core = '7 Core';
                                        } elseif ($idetails->core == 9) {
                                            $core = '10 Core';
                                        }

                                        if (isset($_item->tenderfour)) {
                                            $ttype = $_item->tenderfour;
                                        } else {
                                            $ttype = $_item->tendertwo;
                                        }
                                        $foo = (str_replace(',', '', $_tender->qvalue) / 100000);
                                        $amount = number_format((float) $foo, 2, '.', '');
                                        $newidetails['itemtender'] = $idetails->itemtender;
                                        $newidetails['tdetails'] = $tdetails;
                                        $newidetails['idetails'] = $descfull;
                                        $newidetails['sizes'] = @$size->size;
                                        $newidetails['units'] = $idetails->units;
                                        $newidetails['quantity'] = $idetails->quantity . '.00';
                                        $newidetails['make'] = $_make;
                                        $newidetails['itype'] = $itemtype;
                                        $newidetails['allmakes'] = $allmakes;
                                        $newidetails['core'] = @$core;
                                        $newidetails['typefitting'] = @$tfit->text;
                                        $newidetails['capacityfitting'] = @$cfit->text;
                                        $newidetails['itemid'] = $idetails->item_id;
                                        $newidetails['firm'] = $contractor->firm;
                                        $newidetails['cperson'] = $contractor->name;
                                        $newidetails['caddress'] = $contractor->address;
                                        $newidetails['ccontact'] = $contractor->contact;
                                        $newidetails['cemail'] = $contractor->email;
                                        $newidetails['tid'] = $_tender->id;
                                        $newidetails['cvalue'] = $amount;
                                        $newidetails['ttype'] = $ttype;
                                        $alldetails[] = $newidetails;
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($alldetails) {
                    foreach ($alldetails as $k => $_all) {
                        $makename = \common\models\Make::find()->where(['id' => $_all['make']])->one();
                        $tender = \common\models\Tender::find()->where(['id' => $_all['tid']])->one();
                        $datatender[$k] = $alldetails[$k];
                        $datatender[$k]['ref'] = $tender['tender_id'];
                        $datatender[$k]['mid'] = @$makename->id;
                        $datatender[$k]['makename'] = @$makename->make;
                        $datatender[$k]['email'] = @$makename->email;
                    }
                }

                if (@$datatender) {
                    foreach ($datatender as $_make) {
                        if (array_key_exists($_make['make'], $data)) {
                            $data[$_make['make']][] = $_make;
                        } else {
                            $data[$_make['make']][] = $_make;
                        }
                    }
                }

                $client = \common\models\Clients::find()->where(['id' => $_log->cid])->one();

                $required = '';
                $reqdetails = [];
                $requiredlight = '';
                $reqdetailslight = [];
                $required = $client->cables;
                $reqdetails = explode(',', $required);
                $particulardata = [];
                if (isset($data) && count($data)) {
                    foreach ($data as $k => $___data) {
                        if (in_array($k, $reqdetails)) {
                            $particulardata[] = $___data;
                        }
                    }
                }
                $requiredlight = $client->lighting;
                $reqdetailslight = explode(',', $requiredlight);
                if (isset($data) && count($data)) {
                    foreach ($data as $k => $___data) {
                        if (in_array($k, $reqdetailslight)) {
                            $particulardata[] = $___data;
                        }
                    }
                }


                $filestosend = [];
                $tenderids = [];
                $itemids = [];
                if (isset($particulardata) && count($particulardata)) {
                    $mailnum = 1;
                    foreach ($particulardata as $k => $_data) {
                        $header = [];
                        $i = 0;
                        $sno = 1;
                        $tid = [];
                        $firmid = [];
                        $final = [];
                        foreach ($_data as $key => $__data) {

                            if ($i == 0) {
                                if ($__data['ttype'] == 1) {
                                    $header[] = "Sr.No." . "\t";
                                    $header[] = "Tender Id" . "\t";
                                    $header[] = "Amount of Contract (In Lakhs)" . "\t";
                                    $header[] = "Details of Contracting Office" . "\t";
                                    $header[] = "Item Details" . "\t";
                                    $header[] = "Size" . "\t";
                                    $header[] = "Core" . "\t";
                                    $header[] = "Units" . "\t";
                                    $header[] = "Quantity" . "\t";
                                    $header[] = "All Makes In Contract" . "\t";
                                    $header[] = "Name of Contractor" . "\t";
                                    $header[] = "Name of Contact Person" . "\t";
                                    $header[] = "Address of Contractor" . "\t";
                                    $header[] = "Contact Number" . "\t";
                                    $header[] = "E-mail ID" . "\t";
                                } elseif ($__data['ttype'] == 2) {
                                    $header[] = "Sr.No." . "\t";
                                    $header[] = "Tender Id" . "\t";
                                    $header[] = "Amount of Contract (In Lakhs)" . "\t";
                                    $header[] = "Details of Contracting Office" . "\t";
                                    $header[] = "Item Details" . "\t";
                                    $header[] = "Type of Fitting" . "\t";
                                    $header[] = "Capacity of Fitting" . "\t";
                                    $header[] = "Units" . "\t";
                                    $header[] = "Quantity" . "\t";
                                    $header[] = "All Makes In Contract" . "\t";
                                    $header[] = "Name of Contractor" . "\t";
                                    $header[] = "Name of Contact Person" . "\t";
                                    $header[] = "Address of Contractor" . "\t";
                                    $header[] = "Contact Number" . "\t";
                                    $header[] = "E-mail ID" . "\t";
                                }
//$datas = '';
//$datas .= join($header) . "\n";
                                $final[] = $header;
                            }


                            if ($__data['ttype'] == 1) {
                                $arrayData = [];
                                if (in_array($__data['ref'], $tid)) {
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                } else {
                                    $arrayData[] = $sno;

                                    $arrayData[] = $__data['ref'];
                                    $arrayData[] = $__data['cvalue'];
                                    $arrayData[] = $__data['tdetails'];
                                    $sno++;
                                }
//$arrayData[] = $__data['ref'];
                                $arrayData[] = $__data['idetails'];
//$arrayData[] = $__data['itemtender'];
                                $arrayData[] = @$__data['sizes'];
                                $arrayData[] = @$__data['core'];
                                $arrayData[] = $__data['units'];
                                $arrayData[] = $__data['quantity'];
//$arrayData[] = $__data['makename'];
                                $arrayData[] = $__data['allmakes'];
                                if (in_array($__data['ref'], $tid) && in_array($__data['firm'], $firmid)) {
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                } else {
                                    $firmid[] = $__data['firm'];
                                    $arrayData[] = $__data['firm'];
                                    $arrayData[] = $__data['cperson'];
                                    $arrayData[] = $__data['caddress'];
                                    $arrayData[] = $__data['ccontact'];
                                    $arrayData[] = $__data['cemail'];
                                }
                                $tid[] = $__data['ref'];
                                $final[] = $arrayData;
//$datas .= join("\t", $arrayData) . "\n";
                            } elseif ($__data['ttype'] == 2) {
                                $arrayData = [];
                                if (in_array($__data['ref'], $tid)) {
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                } else {
                                    $arrayData[] = $sno;

                                    $arrayData[] = $__data['ref'];
                                    $arrayData[] = $__data['cvalue'];
                                    $arrayData[] = $__data['tdetails'];
                                    $sno++;
                                }
                                $arrayData[] = $__data['idetails'];
//$arrayData[] = $__data['itemtender'];
                                $arrayData[] = @$__data['typefitting'];
                                $arrayData[] = @$__data['capacityfitting'];
                                $arrayData[] = $__data['units'];
                                $arrayData[] = $__data['quantity'];
//$arrayData[] = $__data['makename'];
                                $arrayData[] = $__data['allmakes'];
                                if (in_array($__data['ref'], $tid) && in_array($__data['firm'], $firmid)) {
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                } else {
                                    $firmid[] = $__data['firm'];
                                    $arrayData[] = $__data['firm'];
                                    $arrayData[] = $__data['cperson'];
                                    $arrayData[] = $__data['caddress'];
                                    $arrayData[] = $__data['ccontact'];
                                    $arrayData[] = $__data['cemail'];
                                }
                                $tid[] = $__data['ref'];
                                $final[] = $arrayData;
//$datas .= join("\t", $row1) . "\n";
                            }
                            $i++;
                            $tenderids[] = $__data['tid'];
                            $itemids[] = $__data['itemid'];
                        }


                        $spreadsheet = new Spreadsheet();  /* ----Spreadsheet object----- */
//$activeSheet = $spreadsheet->getActiveSheet();
//$spreadsheet->getActiveSheet()->freezePane('D2');
//$arrayData = $datas;

                        $activeSheet = $spreadsheet->getActiveSheet()
                                ->fromArray(
                                $final, // The data to set
                                NULL, // Array values with this value will not be set
                                'A1'         // Top left coordinate of the worksheet range where
                        );
                        $activeSheet->getStyle('A1:O1')->getFont()->setSize(11);
                        $styleArray = [
                            'font' => [
                                'bold' => true,
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
                            ]
                        ];
                        $activeSheet->getStyle('A1:O1')->applyFromArray($styleArray);

                        $styleArrayinside = [
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
                            ]
                        ];

                        $styleArraylimited = [
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
                            ]
                        ];
                        $styleArrayborder = [
                            'borders' => [
                                'outline' => [
                                    'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['rgb' => '808080']
                                ],
                            ],
                        ];



                        if ($final) {
                            $p = 2;
                            $c = 1;
                            foreach ($final as $_final) {
                                if ($p > 2) {
                                    if ($_final['0'] != '') {
                                        $activeSheet->getStyle('A' . $c . ':O' . $c . '')->getFill()
                                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                                ->getStartColor()->setARGB('D3D3D3');
                                        //$activeSheet->getStyle('A' . $c . ':O' . $c . '')->getBorders()->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '808080']]]);
                                    }
                                }
                                $activeSheet->getStyle('A' . $c . ':O' . $c . '')->getBorders()->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '808080']]]);
                                $activeSheet->getStyle('D' . $p . ':E' . $p . '')->applyFromArray($styleArrayinside);
                                $activeSheet->getStyle('J' . $p . ':M' . $p . '')->applyFromArray($styleArrayinside);
                                $activeSheet->getStyle('A' . $p . ':C' . $p . '')->applyFromArray($styleArraylimited);
                                $activeSheet->getStyle('F' . $p . ':I' . $p . '')->applyFromArray($styleArraylimited);
                                $activeSheet->getStyle('N' . $p . ':O' . $p . '')->applyFromArray($styleArraylimited);
                                $p++;
                                $c++;
                            }
                        }

                        $path = $_SERVER['DOCUMENT_ROOT'] . '/admin/images/clogo.png';
                        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing();
                        $drawing->setName('Crispdata logo');
                        $drawing->setPath($path);
                        $drawing->setHeight(36); // logo height
                        $activeSheet->getHeaderFooter()->addImage($drawing, \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter::IMAGE_HEADER_CENTER);

                        //$activeSheet->getStyle()->applyFromArray($styleArrayborder);
                        //$activeSheet->getStyle('A1:O3')->getBorders()->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_DASHDOT, 'color' => ['rgb' => '808080']]]);
                        $activeSheet->getStyle('C1:C' . $activeSheet->getHighestRow())->getNumberFormat()->setFormatCode('0.00');
                        $activeSheet->getStyle('I1:I' . $activeSheet->getHighestRow())->getNumberFormat()->setFormatCode('0.00');
                        $activeSheet->getStyle('D1:D' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('J1:J' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('C1:C' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('E1:E' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('K1:K' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('L1:L' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('M1:M' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('N1:N' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('O1:O' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('A1:A' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('B1:B' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('F1:F' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('G1:G' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('H1:H' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('I1:I' . $activeSheet->getHighestRow())
                                ->getAlignment()->setWrapText(true);

                        $activeSheet->getStyle('A1:O1')
                                ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);


                        $cellIterator = $activeSheet->getRowIterator()->current()->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(true);
                        /** @var PHPExcel_Cell $cell */
                        foreach ($cellIterator as $cell) {
                            if ($cell->getColumn() == 'D') {
                                $activeSheet->getColumnDimension('D')->setWidth(30);
                            } elseif ($cell->getColumn() == 'J') {
                                $activeSheet->getColumnDimension('J')->setWidth(40);
                            } elseif ($cell->getColumn() == 'C') {
                                $activeSheet->getColumnDimension('C')->setWidth(20);
                            } elseif ($cell->getColumn() == 'E') {
                                $activeSheet->getColumnDimension('E')->setWidth(20);
                            } elseif ($cell->getColumn() == 'K' || $cell->getColumn() == 'L' || $cell->getColumn() == 'M' || $cell->getColumn() == 'N' || $cell->getColumn() == 'O') {
                                $activeSheet->getColumnDimension('K')->setWidth(30);
                                $activeSheet->getColumnDimension('L')->setWidth(30);
                                $activeSheet->getColumnDimension('M')->setWidth(30);
                                $activeSheet->getColumnDimension('N')->setWidth(30);
                                $activeSheet->getColumnDimension('O')->setWidth(30);
                            } else {
                                $activeSheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                            }
                        }


// Create Excel file and sve in your directory
                        $writer = new Xlsx($spreadsheet);


                        $writer->save("" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace(' ', '_', $__data['makename']) . ' - ' . $__data['itype'] . ".xlsx");
                        $filepath = "" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace(' ', '_', $__data['makename']) . ' - ' . $__data['itype'] . ".xlsx";
                        $filename = time() . str_replace('/', '_', str_replace(' ', '_', $__data['makename'])) . ".xlsx";
                        $filestosend[] = ['name' => str_replace(' ', '_', $__data['makename']) . ' - ' . $__data['itype'] . ".xlsx", 'path' => $filepath, 'fname' => $filename];
                        $mailnum++;
                    }
                }
                $uploadedpaths = $this->actionAwsupload($filestosend);
                $names = '';
                $paths = '';
                $logupdate = \common\models\Logs::find()->where(['id' => $_log->id])->one();
                if ($uploadedpaths) {
                    foreach ($uploadedpaths as $_path) {
                        $names .= $_path['name'] . ',';
                        $paths .= $_path['path'] . ',';
                    }
                    $names = rtrim($names, ',');
                    $paths = rtrim($paths, ',');
                    $logupdate->filename = $names;
                    $logupdate->filepath = $paths;
                    $logupdate->save();
                }
            }
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

    public function actionSendmail() {
        require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
        $imageURL = Yii::$app->params['IMAGE_URL'];
        $user = Yii::$app->user->identity;
        $data = [];
        $finalmakes = [];
        $alldetails = [];
        $newidetails = [];
        $size = [];
        $tfit = [];
        $cfit = [];
        $tenders = \common\models\Tender::find()->where(['on_hold' => null, 'aoc_status' => 1, 'is_archived' => null])->orderBy(['id' => SORT_DESC])->all();


        if ($tenders) {
            foreach ($tenders as $_tender) {
                $tdetails = '';
                $command = Sitecontroller::actionGetcommand($_tender->command);
                if (!isset($_tender->cengineer) && isset($_tender->gengineer)) {
                    $cengineer = \common\models\Cengineer::find()->where(['cid' => $_tender->gengineer, 'status' => 1])->one();
                } else {
                    $cengineer = \common\models\Cengineer::find()->where(['cid' => $_tender->cengineer, 'status' => 1])->one();
                }
                $cwengineer = \common\models\Cwengineer::find()->where(['cengineer' => $_tender->cengineer, 'cid' => $_tender->cwengineer, 'status' => 1])->one();
                $gengineer = \common\models\Gengineer::find()->where(['cwengineer' => $_tender->cwengineer, 'gid' => $_tender->gengineer, 'status' => 1])->one();
                $items = \common\models\Item::find()->where(['tender_id' => $_tender->id, 'status' => 1])->all();
                $tdetails = @$command . ' ' . @$cengineer->text . ' ' . @$cwengineer->text . ' ' . @$gengineer->text;
                if ($items) {
                    foreach ($items as $_item) {
                        $idetails = \common\models\ItemDetails::find()->where(['item_id' => $_item->id])->one();
                        if ($idetails) {
                            $imakes = explode(',', $idetails->make);
                            $descfull = '';
                            if ($_item->tendertwo != '' && $_item->tendertwo != 0) {
                                $two = Sitecontroller::actionTendertwo($_item->tendertwo);
                                $descfull .= $two . ',';
                            }
                            if ($_item->tenderthree != '' && $_item->tenderthree != 0) {
                                $three = Sitecontroller::actionTenderthree($_item->tenderthree);
                                $descfull .= $three . ',';
                            }
                            if ($_item->tenderfour != '' && $_item->tenderfour != 0) {
                                $four = Sitecontroller::actionTenderfour($_item->tenderfour);
                                $descfull .= $four . ',';
                            }
                            if ($_item->tenderfour == 1) {
                                if ($_item->tenderfive != '' && $_item->tenderfive != 0) {
                                    $five = Sitecontroller::actionTenderfive($_item->tenderfive);
                                    $descfull .= $five . ',';
                                }
                            }
                            if ($_item->tendersix != '' && $_item->tendersix != 0) {
                                $six = Sitecontroller::actionTendersix($_item->tendersix);
                                $descfull .= $six . ',';
                            }
                            if ($_item->tenderfour == 1) {
                                $itemtype = 'Cables';
                            } elseif ($_item->tenderfour == 2) {
                                $itemtype = 'Lighting';
                            } elseif ($_item->tendertwo == 14) {
                                $itemtype = 'Cement';
                            } elseif ($_item->tendertwo == 15) {
                                $itemtype = 'Reinforcement Steel';
                            } elseif ($_item->tendertwo == 16) {
                                $itemtype = 'Structural Steel';
                            } elseif ($_item->tendertwo == 17) {
                                $itemtype = 'Non Structural Steel';
                            }
                            $descfull = rtrim($descfull, ',');
                            $i = 0;
                            $allmakes = '';
                            $makenameall = '';
                            if (@$imakes) {
                                foreach ($imakes as $mid) {
                                    //$makename = \common\models\Make::find()->where(['id' => $mid])->one();
                                    //if (@$makename) {
                                    $makenameall .= $mid . ',';
                                    //}
                                }
                            }
                            $allmakes = rtrim($makenameall, ',');
                            foreach ($imakes as $_make) {
                                if (isset($idetails->description)) {
                                    $size = \common\models\Size::find()->where(['id' => $idetails->description])->one();
                                }
                                if (isset($idetails->typefitting) && isset($idetails->capacityfitting)) {
                                    $tfit = \common\models\Fitting::find()->where(['id' => $idetails->typefitting, 'type' => 1])->one();
                                    $cfit = \common\models\Fitting::find()->where(['id' => $idetails->capacityfitting, 'type' => 2])->one();
                                }
                                $contractor = \common\models\Contractor::find()->where(['id' => $_tender->contractor])->one();
                                if ($idetails->core == 1) {
                                    $core = '1 Core';
                                } elseif ($idetails->core == 2) {
                                    $core = '2 Core';
                                } elseif ($idetails->core == 3) {
                                    $core = '3 Core';
                                } elseif ($idetails->core == 4) {
                                    $core = '3.5 Core';
                                } elseif ($idetails->core == 5) {
                                    $core = '4 Core';
                                } elseif ($idetails->core == 6) {
                                    $core = '5 Core';
                                } elseif ($idetails->core == 7) {
                                    $core = '6 Core';
                                } elseif ($idetails->core == 8) {
                                    $core = '7 Core';
                                } elseif ($idetails->core == 9) {
                                    $core = '10 Core';
                                }

                                if (isset($_item->tenderfour)) {
                                    $ttype = $_item->tenderfour;
                                } else {
                                    $ttype = $_item->tendertwo;
                                }
                                $foo = (str_replace(',', '', $_tender->qvalue) / 100000);
                                $amount = number_format((float) $foo, 2, '.', '');
                                $newidetails['itemtender'] = $idetails->itemtender;
                                $newidetails['tdetails'] = $tdetails;
                                $newidetails['idetails'] = $descfull;
                                $newidetails['sizes'] = @$size->size;
                                $newidetails['units'] = $idetails->units;
                                $newidetails['quantity'] = $idetails->quantity . '.00';
                                $newidetails['make'] = $_make;
                                $newidetails['itype'] = $itemtype;
                                $newidetails['allmakes'] = $allmakes;
                                $newidetails['core'] = @$core;
                                $newidetails['typefitting'] = @$tfit->text;
                                $newidetails['capacityfitting'] = @$cfit->text;
                                $newidetails['itemid'] = $idetails->item_id;
                                $newidetails['firm'] = $contractor->firm;
                                $newidetails['cperson'] = $contractor->name;
                                $newidetails['caddress'] = $contractor->address;
                                $newidetails['ccontact'] = $contractor->contact;
                                $newidetails['cemail'] = $contractor->email;
                                $newidetails['tid'] = $_tender->id;
                                $newidetails['cvalue'] = $amount;
                                $newidetails['ttype'] = $ttype;
                                $alldetails[] = $newidetails;
                                $i++;
                            }
                        }
                    }
                }
            }
        }

        if ($alldetails) {
            foreach ($alldetails as $k => $_all) {
                $makename = \common\models\Make::find()->where(['id' => $_all['make']])->one();
                $tender = \common\models\Tender::find()->where(['id' => $_all['tid']])->one();
                $datatender[$k] = $alldetails[$k];
                $datatender[$k]['ref'] = $tender['tender_id'];
                $datatender[$k]['mid'] = @$makename->id;
                $datatender[$k]['makename'] = @$makename->make;
                $datatender[$k]['email'] = @$makename->email;
            }
        }

        if (@$datatender) {
            foreach ($datatender as $_make) {
                if (array_key_exists($_make['make'], $data)) {
                    $data[$_make['make']][] = $_make;
                } else {
                    $data[$_make['make']][] = $_make;
                }
            }
        }

        if (isset($data) && count($data)) {
            foreach ($data as $k => $_cldata) {
                foreach ($_cldata as $key => $cldata) {
                    $singlemake = [];
                    if (isset($cldata['ttype']) && ($cldata['ttype'] == 1 || $cldata['ttype'] == 2)) {
                        if (isset($cldata['allmakes'])) {
                            $singlemake = explode(',', $cldata['allmakes']);
                            $clmakename = '';
                            $allclmakes = '';
                            if (isset($singlemake) && count($singlemake)) {
                                foreach ($singlemake as $__smake) {
                                    $makename = \common\models\Make::find()->where(['id' => $__smake])->one();
                                    if (@$makename) {
                                        $clmakename .= $makename->make . ',';
                                    }
                                }
                            }
                            $allclmakes = rtrim($clmakename, ',');
                        }
                        $data[$k][$key]['allmakes'] = $allclmakes;
                    } else {
                        if (isset($cldata['allmakes'])) {
                            $singlemake = explode(',', $cldata['allmakes']);
                            $clmakename = '';
                            $allclmakes = '';
                            if (isset($singlemake) && count($singlemake)) {
                                foreach ($singlemake as $__smake) {
                                    $makename = \common\models\Make::find()->where(['id' => $__smake])->one();
                                    if (@$makename) {
                                        $clmakename = $makename->make;
                                    }
                                    $cldata['allmakes'] = $clmakename;
                                    unset($data[$k][$key]);
                                    $data[$k][] = $cldata;
                                }
                            }
                        }
                    }
                }
            }
        }

        $clients = \common\models\Clients::find()->where(['type' => [1, 3], 'status' => 1])->all();

        if ($data) {
            foreach ($data as $k => $civil) {
                foreach ($civil as $key => $_civil) {
                    if ($_civil['ttype'] == 14 || $_civil['ttype'] == 15 || $_civil['ttype'] == 16 || $_civil['ttype'] == 17) {
                        $civildata[$k][$key] = $_civil;
                    }
                }
            }
        }

        $filestosend = [];
        $tenderids = [];
        $tarchives = [];
        $itemids = [];
        if (isset($civildata) && count($civildata)) {
            $mailnum = 1;
            foreach ($civildata as $k => $_data) {
                $header = [];
                $filestosend = [];
                $cdetails = [];
                $i = 0;
                $sno = 1;
                $tid = [];
                $firmid = [];
                $final = [];

                foreach ($_data as $key => $__data) {

                    if ($i == 0) {
                        if ($__data['ttype'] == 14 || $__data['ttype'] == 15 || $__data['ttype'] == 16 || $__data['ttype'] == 17) {
                            $header[] = "Sr.No." . "\t";
                            $header[] = "Tender Id" . "\t";
                            $header[] = "Amount of Contract (In Lakhs)" . "\t";
                            $header[] = "Details of Contracting Office" . "\t";
                            $header[] = "Item Details" . "\t";
                            $header[] = "All Approved Makes In Contract" . "\t";
                            $header[] = "Name of Contractor" . "\t";
                            $header[] = "Name of Contact Person" . "\t";
                            $header[] = "Address of Contractor" . "\t";
                            $header[] = "Contact Number" . "\t";
                            $header[] = "E-mail ID" . "\t";
                        }
                        $final[] = $header;
                    }


                    if ($__data['ttype'] == 14 || $__data['ttype'] == 15 || $__data['ttype'] == 16 || $__data['ttype'] == 17) {
                        $arrayData = [];
                        if (in_array($__data['ref'], $tid)) {
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                        } else {
                            $arrayData[] = $sno;
                            $arrayData[] = $__data['ref'];
                            $arrayData[] = $__data['cvalue'];
                            $arrayData[] = $__data['tdetails'];
                            $arrayData[] = $__data['idetails'];
                            $sno++;
                        }

                        $arrayData[] = $__data['allmakes'];
                        if (in_array($__data['ref'], $tid) && in_array($__data['firm'], $firmid)) {
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                            $arrayData[] = '';
                        } else {
                            $firmid[] = $__data['firm'];
                            $arrayData[] = $__data['firm'];
                            $arrayData[] = $__data['cperson'];
                            $arrayData[] = $__data['caddress'];
                            $arrayData[] = $__data['ccontact'];
                            $arrayData[] = $__data['cemail'];
                        }
                        $tid[] = $__data['ref'];
                        $final[] = $arrayData;
                    }
                    $i++;
                    $tenderids[] = $__data['tid'];
                    $tarchives[] = $__data['tid'];
                    $itemids[] = $__data['itemid'];
                }

                $excelsecond = $this->actionCreateexcel($final, $__data['ttype']);

                /* header('Content-Type: application/vnd.ms-excel');
                  header('Content-Disposition: attachment;filename="' . $__data['makename'] . '.xls"');
                  header('Cache-Control: max-age=0');
                  $excelsecond->save('php://output');
                  DIE(); */

                $excelsecond->save("" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace('/', '_', str_replace(' ', '_', $__data['makename'])) . ' - ' . $__data['itype'] . ".xlsx");
                $filepath = "" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace('/', '_', str_replace(' ', '_', $__data['makename'])) . ' - ' . $__data['itype'] . ".xlsx";
                $filename = time() . str_replace('/', '_', str_replace(' ', '_', $__data['makename'])) . ".xlsx";
                $filestosend[] = ['name' => str_replace(' ', '_', $__data['makename']) . ' - ' . $__data['itype'] . ".xlsx", 'path' => $filepath, 'fname' => $filename];
                $cdetails = (object) ['id' => $__data['mid'], 'cemail' => $__data['email']];
                $mailnum++;

                $mailsent = $this->actionConfiguremail($filestosend, $tenderids, $itemids, $cdetails, 2);
            }
        }



        if (isset($clients) && count($clients)) {
            foreach ($clients as $_client) {
                $required = '';
                $reqdetails = [];
                $requiredlight = '';
                $reqdetailslight = [];
                $required = $_client->cables;
                $reqdetails = explode(',', $required);
                $particulardata = [];
                if (isset($data) && count($data)) {
                    foreach ($data as $k => $___data) {
                        if (in_array($k, $reqdetails)) {
                            $particulardata[] = $___data;
                        }
                    }
                }
                $requiredlight = $_client->lighting;
                $reqdetailslight = explode(',', $requiredlight);
                if (isset($data) && count($data)) {
                    foreach ($data as $k => $___data) {
                        if (in_array($k, $reqdetailslight)) {
                            $particulardata[] = $___data;
                        }
                    }
                }


                $filestosend = [];
                $tenderids = [];
                $itemids = [];
                if (isset($particulardata) && count($particulardata)) {
                    $mailnum = 1;
                    foreach ($particulardata as $k => $_data) {
                        $header = [];
                        $i = 0;
                        $sno = 1;
                        $tid = [];
                        $firmid = [];
                        $final = [];
                        foreach ($_data as $key => $__data) {

                            if ($i == 0) {
                                if ($__data['ttype'] == 1) {
                                    $header[] = "Sr.No." . "\t";
                                    $header[] = "Tender Id" . "\t";
                                    $header[] = "Amount of Contract (In Lakhs)" . "\t";
                                    $header[] = "Details of Contracting Office" . "\t";
                                    $header[] = "Item Details" . "\t";
                                    $header[] = "Size" . "\t";
                                    $header[] = "Core" . "\t";
                                    $header[] = "Units" . "\t";
                                    $header[] = "Quantity" . "\t";
                                    $header[] = "All Approved Makes In Contract" . "\t";
                                    $header[] = "Name of Contractor" . "\t";
                                    $header[] = "Name of Contact Person" . "\t";
                                    $header[] = "Address of Contractor" . "\t";
                                    $header[] = "Contact Number" . "\t";
                                    $header[] = "E-mail ID" . "\t";
                                } elseif ($__data['ttype'] == 2) {
                                    $header[] = "Sr.No." . "\t";
                                    $header[] = "Tender Id" . "\t";
                                    $header[] = "Amount of Contract (In Lakhs)" . "\t";
                                    $header[] = "Details of Contracting Office" . "\t";
                                    $header[] = "Item Details" . "\t";
                                    $header[] = "Type of Fitting" . "\t";
                                    $header[] = "Capacity of Fitting" . "\t";
                                    $header[] = "Units" . "\t";
                                    $header[] = "Quantity" . "\t";
                                    $header[] = "All Approved Makes In Contract" . "\t";
                                    $header[] = "Name of Contractor" . "\t";
                                    $header[] = "Name of Contact Person" . "\t";
                                    $header[] = "Address of Contractor" . "\t";
                                    $header[] = "Contact Number" . "\t";
                                    $header[] = "E-mail ID" . "\t";
                                } else {
                                    $header[] = "Sr.No." . "\t";
                                    $header[] = "Tender Id" . "\t";
                                    $header[] = "Amount of Contract (In Lakhs)" . "\t";
                                    $header[] = "Details of Contracting Office" . "\t";
                                    $header[] = "Item Details" . "\t";
                                    $header[] = "All Approved Makes In Contract" . "\t";
                                    $header[] = "Name of Contractor" . "\t";
                                    $header[] = "Name of Contact Person" . "\t";
                                    $header[] = "Address of Contractor" . "\t";
                                    $header[] = "Contact Number" . "\t";
                                    $header[] = "E-mail ID" . "\t";
                                }

                                $final[] = $header;
                            }


                            if ($__data['ttype'] == 1) {
                                $arrayData = [];
                                if (in_array($__data['ref'], $tid)) {
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                } else {
                                    $arrayData[] = $sno;
                                    $arrayData[] = $__data['ref'];
                                    $arrayData[] = $__data['cvalue'];
                                    $arrayData[] = $__data['tdetails'];
                                    $sno++;
                                }
                                $arrayData[] = $__data['idetails'];
                                $arrayData[] = @$__data['sizes'];
                                $arrayData[] = @$__data['core'];
                                $arrayData[] = $__data['units'];
                                $arrayData[] = $__data['quantity'];
                                $arrayData[] = $__data['allmakes'];
                                if (in_array($__data['ref'], $tid) && in_array($__data['firm'], $firmid)) {
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                } else {
                                    $firmid[] = $__data['firm'];
                                    $arrayData[] = $__data['firm'];
                                    $arrayData[] = $__data['cperson'];
                                    $arrayData[] = $__data['caddress'];
                                    $arrayData[] = $__data['ccontact'];
                                    $arrayData[] = $__data['cemail'];
                                }
                                $tid[] = $__data['ref'];
                                $final[] = $arrayData;
                            } elseif ($__data['ttype'] == 2) {
                                $arrayData = [];
                                if (in_array($__data['ref'], $tid)) {
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                } else {
                                    $arrayData[] = $sno;
                                    $arrayData[] = $__data['ref'];
                                    $arrayData[] = $__data['cvalue'];
                                    $arrayData[] = $__data['tdetails'];
                                    $sno++;
                                }
                                $arrayData[] = $__data['idetails'];
                                $arrayData[] = @$__data['typefitting'];
                                $arrayData[] = @$__data['capacityfitting'];
                                $arrayData[] = $__data['units'];
                                $arrayData[] = $__data['quantity'];
                                $arrayData[] = $__data['allmakes'];
                                if (in_array($__data['ref'], $tid) && in_array($__data['firm'], $firmid)) {
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                } else {
                                    $firmid[] = $__data['firm'];
                                    $arrayData[] = $__data['firm'];
                                    $arrayData[] = $__data['cperson'];
                                    $arrayData[] = $__data['caddress'];
                                    $arrayData[] = $__data['ccontact'];
                                    $arrayData[] = $__data['cemail'];
                                }
                                $tid[] = $__data['ref'];
                                $final[] = $arrayData;
                            } else {
                                $arrayData = [];
                                if (in_array($__data['ref'], $tid)) {
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                } else {
                                    $arrayData[] = $sno;
                                    $arrayData[] = $__data['ref'];
                                    $arrayData[] = $__data['cvalue'];
                                    $arrayData[] = $__data['tdetails'];
                                    $arrayData[] = $__data['idetails'];
                                    $sno++;
                                }

                                $arrayData[] = $__data['allmakes'];
                                if (in_array($__data['ref'], $tid) && in_array($__data['firm'], $firmid)) {
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                    $arrayData[] = '';
                                } else {
                                    $firmid[] = $__data['firm'];
                                    $arrayData[] = $__data['firm'];
                                    $arrayData[] = $__data['cperson'];
                                    $arrayData[] = $__data['caddress'];
                                    $arrayData[] = $__data['ccontact'];
                                    $arrayData[] = $__data['cemail'];
                                }
                                $tid[] = $__data['ref'];
                                $final[] = $arrayData;
                            }
                            $i++;
                            $tenderids[] = $__data['tid'];
                            $tarchives[] = $__data['tid'];
                            $itemids[] = $__data['itemid'];
                        }

                        $excel = $this->actionCreateexcel($final, $__data['ttype']);

                        /* header('Content-Type: application/vnd.ms-excel');
                          header('Content-Disposition: attachment;filename="' . $__data['makename'] . '.xls"');
                          header('Cache-Control: max-age=0');
                          $excel->save('php://output');
                          DIE(); */

                        $excel->save("" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace('/', '_', str_replace(' ', '_', $__data['makename'])) . ' - ' . $__data['itype'] . ".xlsx");
                        $filepath = "" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace('/', '_', str_replace(' ', '_', $__data['makename'])) . ' - ' . $__data['itype'] . ".xlsx";
                        $filename = time() . str_replace('/', '_', str_replace(' ', '_', $__data['makename'])) . ".xlsx";
                        $filestosend[] = ['name' => str_replace(' ', '_', $__data['makename']) . ' - ' . $__data['itype'] . ".xlsx", 'path' => $filepath, 'fname' => $filename];
                        $mailnum++;
                    }
                }

                $mail = $this->actionConfiguremail($filestosend, $tenderids, $itemids, $_client, 1);
            }
        }

        $archived = $this->actionArchivetenders($tarchives);

        Yii::$app->session->setFlash('success', "Mails successfully sent");
        return $this->redirect(array('mail/index'));
    }

    public function actionResendmail() {
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

        $email = $_REQUEST['email'];
        $filename = $_REQUEST['filename'];
        $filepath = $_REQUEST['filepath'];

        $allnames = explode(',', $filename);
        $allpaths = explode(',', $filepath);

        $totalfiles = count($allnames);



        $htmlfilepath = Yii::getAlias('@common/mail/home-link.php');
        $textfilepath = '';
        $ses = new \SimpleEmailService(Yii::$app->params['IAM_KEY'], Yii::$app->params['IAM_SECRET']);
        $m = new \SimpleEmailServiceMessage();

        for ($i = 0; $i < $totalfiles; $i++) {
            $filecontent = file_get_contents($allpaths[$i]);
            file_put_contents("" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace('/', '_', str_replace(' ', '_', time() . $allnames[$i])) . ".xlsx", $filecontent);
            $m->addAttachmentFromFile($allnames[$i], "" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace('/', '_', str_replace(' ', '_', time() . $allnames[$i])) . ".xlsx", 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            unlink("" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace('/', '_', str_replace(' ', '_', time() . $allnames[$i])) . ".xlsx");
        }

        $allemails = explode(',', $email);
        if (isset($allemails) && count($allemails)) {
            foreach ($allemails as $_email) {
                $m->addTo($_email);
            }
        } else {
            $m->addTo('sajstyles21@gmail.com');
        }

        $m->setFrom("Crispdata <info@crispdata.co.in>");
        $m->setSubject('Tender Details By Crispdata');
        $m->setMessageFromFile($textfilepath, $htmlfilepath);
        $m->setSubjectCharset('ISO-8859-1');
        $m->setMessageCharset('ISO-8859-1');
        $m->addAttachmentFromFile('logo.png', Yii::getAlias('@app/web/assets/images/crispdatalogo.png'), 'application/octet-stream', '<logo.png>', 'inline');
        $m->addAttachmentFromFile('crispdata.png', Yii::getAlias('@app/web/assets/images/crispdatalogo.png'), 'application/octet-stream');
        if ($ses->sendEmail($m)) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function actionAwsupload($files) {

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

        if (isset($files) && count($files)) {
            foreach ($files as $__file) {

                $keyName = 'excelfiles/' . $__file['fname'];
                $pathInS3 = 'http://s3.us-east-2.amazonaws.com/' . Yii::$app->params['bucketName'] . '/' . $keyName;

                try {
                    // Uploaded:
                    $file = $__file['path'];
                    $fileupload = $s3->putObject(
                            array(
                                'Bucket' => Yii::$app->params['bucketName'],
                                'Key' => $keyName,
                                'SourceFile' => $file,
                                'ACL' => 'public-read-write'
                            )
                    );
                    if ($fileupload) {
                        unlink($__file['path']);
                        $s3paths[] = ['name' => $__file['name'], 'path' => $pathInS3];
                    }
                } catch (S3Exception $e) {
                    die('Error:' . $e->getMessage());
                } catch (Exception $e) {
                    die('Error:' . $e->getMessage());
                }
            }
        }
        return $s3paths;
    }

    public function actionConfiguremail($filestosend, $tenderids, $itemids, $client, $type) {
        $allmails = [];
        $htmlfilepath = Yii::getAlias('@common/mail/home-link.php');
        $textfilepath = '';
        $ses = new \SimpleEmailService(Yii::$app->params['IAM_KEY'], Yii::$app->params['IAM_SECRET']);
        $m = new \SimpleEmailServiceMessage();
        if (isset($filestosend) && count($filestosend)) {
            foreach ($filestosend as $_file) {
                $m->addAttachmentFromFile($_file['name'], $_file['path'], 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            }
        }

        if (isset($client->cemail) && $client->cemail != '') {
            $allmails = explode(',', $client->cemail);
            if (isset($allmails) && count($allmails)) {
                foreach ($allmails as $_mail) {
                    if ($_mail) {
                        $m->addTo($_mail);
                    } else {
                        $m->addTo('sajstyles21@gmail.com');
                    }
                }
            } else {
                $m->addTo('sajstyles21@gmail.com');
            }
        } else {
            $m->addTo('sajstyles21@gmail.com');
        }
        //$m->addTo('sajstyles21@gmail.com');
        $m->addBCC('mail@crispdata.co.in');
        $m->setFrom("Crispdata <info@crispdata.co.in>");
        $m->setSubject('Tender Details By Crispdata');
        $m->setMessageFromFile($textfilepath, $htmlfilepath);
        $m->setSubjectCharset('ISO-8859-1');
        $m->setMessageCharset('ISO-8859-1');
        $m->addAttachmentFromFile('logo.png', Yii::getAlias('@app/web/assets/images/crispdatalogo.png'), 'application/octet-stream', '<logo.png>', 'inline');
        $m->addAttachmentFromFile('crispdata.png', Yii::getAlias('@app/web/assets/images/crispdatalogo.png'), 'application/octet-stream');
        if ($ses->sendEmail($m)) {
            $uploadedpaths = $this->actionAwsupload($filestosend);
            $filenames = [];
            $filepaths = [];
            $ttdds = implode(',', $tenderids);
            $iiddss = implode(',', $itemids);
            if (isset($uploadedpaths) && count($uploadedpaths)) {
                foreach ($uploadedpaths as $_filesend) {
                    $filenames[] = $_filesend['name'];
                    $filepaths[] = $_filesend['path'];
                }
            }
            $allfnames = implode(',', $filenames);
            $allfpaths = implode(',', $filepaths);
            $datasave = ['cid' => $client->id, 'type' => $type, 'tid' => $ttdds, 'itemids' => $iiddss, 'filename' => $allfnames, 'filepath' => $allfpaths, 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];
            $mailsent = \Yii::$app
                    ->db
                    ->createCommand()
                    ->insert('maillogs', $datasave)
                    ->execute();
            //if ($mailsent) {

            /* $array = explode(',', $ttdds);
              $uniqueids = array_unique($array);
              if (isset($uniqueids) && count($uniqueids)) {
              foreach ($uniqueids as $_unique) {
              $archivetender = \common\models\Tender::find()->where(['id' => $_unique])->one();
              $archivetender->is_archived = 1;
              $archivetender->save();
              }
              } */
            //}
        }
    }

    public function actionArchivetenders($tids) {
        $uniqueidsarchive = array_unique($tids);
        if (isset($uniqueidsarchive) && count($uniqueidsarchive)) {
            foreach ($uniqueidsarchive as $_unique) {
                $archivetender = \common\models\Tender::find()->where(['id' => $_unique])->one();
                $archivetender->is_archived = 1;
                $archivetender->save();
            }
        }
    }

    public function actionCreateexcel($final, $type) {

        $spreadsheet = new Spreadsheet();

        $activeSheet = $spreadsheet->getActiveSheet()
                ->fromArray(
                $final, // The data to set
                NULL, // Array values with this value will not be set
                'A1'         // Top left coordinate of the worksheet range where
        );

        if ($type == 1 || $type == 2) {
            $end = 'O';
        } else {
            $end = 'K';
        }
        $activeSheet->getStyle('A1:' . $end . '1')->getFont()->setSize(11);
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
            ]
        ];

        $activeSheet->getStyle('A1:' . $end . '1')->applyFromArray($styleArray);

        $styleArrayinside = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
            ]
        ];

        $styleArraylimited = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
            ]
        ];
        $styleArrayborder = [
            'borders' => [
                'outline' => [
                    'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '808080']
                ],
            ],
        ];



        if ($final) {
            $p = 2;
            $c = 1;
            foreach ($final as $_final) {
                if ($p > 2) {
                    if ($_final['0'] != '') {
                        $activeSheet->getStyle('A' . $c . ':' . $end . '' . $c . '')->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('D3D3D3');
                        //$activeSheet->getStyle('A' . $c . ':O' . $c . '')->getBorders()->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '808080']]]);
                    }
                }
                $activeSheet->getStyle('A' . $c . ':' . $end . '' . $c . '')->getBorders()->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '808080']]]);
                $activeSheet->getStyle('D' . $p . ':E' . $p . '')->applyFromArray($styleArrayinside);
                $activeSheet->getStyle('J' . $p . ':M' . $p . '')->applyFromArray($styleArrayinside);
                $activeSheet->getStyle('A' . $p . ':C' . $p . '')->applyFromArray($styleArraylimited);
                $activeSheet->getStyle('F' . $p . ':I' . $p . '')->applyFromArray($styleArraylimited);
                $activeSheet->getStyle('N' . $p . ':O' . $p . '')->applyFromArray($styleArraylimited);
                $p++;
                $c++;
            }
        }

        $path = $_SERVER['DOCUMENT_ROOT'] . '/admin/images/clogo.png';
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing();
        $drawing->setName('Crispdata logo');
        $drawing->setPath($path);
        $drawing->setHeight(36); // logo height
        $activeSheet->getHeaderFooter()->addImage($drawing, \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter::IMAGE_HEADER_CENTER);

        //$activeSheet->getStyle()->applyFromArray($styleArrayborder);
        //$activeSheet->getStyle('A1:O3')->getBorders()->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_DASHDOT, 'color' => ['rgb' => '808080']]]);
        $activeSheet->getStyle('C1:C' . $activeSheet->getHighestRow())->getNumberFormat()->setFormatCode('0.00');
        $activeSheet->getStyle('I1:I' . $activeSheet->getHighestRow())->getNumberFormat()->setFormatCode('0.00');
        $activeSheet->getStyle('D1:D' . $activeSheet->getHighestRow())
                ->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('J1:J' . $activeSheet->getHighestRow())
                ->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('C1:C' . $activeSheet->getHighestRow())
                ->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('E1:E' . $activeSheet->getHighestRow())
                ->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('K1:K' . $activeSheet->getHighestRow())
                ->getAlignment()->setWrapText(true);
        if ($type == 1 || $type == 2) {
            $activeSheet->getStyle('L1:L' . $activeSheet->getHighestRow())
                    ->getAlignment()->setWrapText(true);
            $activeSheet->getStyle('M1:M' . $activeSheet->getHighestRow())
                    ->getAlignment()->setWrapText(true);
            $activeSheet->getStyle('N1:N' . $activeSheet->getHighestRow())
                    ->getAlignment()->setWrapText(true);
            $activeSheet->getStyle('O1:O' . $activeSheet->getHighestRow())
                    ->getAlignment()->setWrapText(true);
        }
        $activeSheet->getStyle('A1:A' . $activeSheet->getHighestRow())
                ->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('B1:B' . $activeSheet->getHighestRow())
                ->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('F1:F' . $activeSheet->getHighestRow())
                ->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('G1:G' . $activeSheet->getHighestRow())
                ->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('H1:H' . $activeSheet->getHighestRow())
                ->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('I1:I' . $activeSheet->getHighestRow())
                ->getAlignment()->setWrapText(true);

        $activeSheet->getStyle('A1:' . $end . '1')
                ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);


        $cellIterator = $activeSheet->getRowIterator()->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);

        if ($type == 1 || $type == 2) {
            foreach ($cellIterator as $cell) {
                if ($cell->getColumn() == 'D') {
                    $activeSheet->getColumnDimension($cell->getColumn())->setWidth(30);
                } elseif ($cell->getColumn() == 'J') {
                    $activeSheet->getColumnDimension($cell->getColumn())->setWidth(40);
                } elseif ($cell->getColumn() == 'C') {
                    $activeSheet->getColumnDimension($cell->getColumn())->setWidth(20);
                } elseif ($cell->getColumn() == 'E') {
                    $activeSheet->getColumnDimension($cell->getColumn())->setWidth(20);
                } elseif ($cell->getColumn() == 'K' || $cell->getColumn() == 'L' || $cell->getColumn() == 'M' || $cell->getColumn() == 'N' || $cell->getColumn() == 'O') {
                    $activeSheet->getColumnDimension($cell->getColumn())->setWidth(30);
                } else {
                    $activeSheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                }
            }
        } else {
            foreach ($cellIterator as $cell) {
                if ($cell->getColumn() == 'D') {
                    $activeSheet->getColumnDimension($cell->getColumn())->setWidth(30);
                } elseif ($cell->getColumn() == 'J') {
                    $activeSheet->getColumnDimension($cell->getColumn())->setWidth(30);
                } elseif ($cell->getColumn() == 'C') {
                    $activeSheet->getColumnDimension($cell->getColumn())->setWidth(20);
                } elseif ($cell->getColumn() == 'E') {
                    $activeSheet->getColumnDimension($cell->getColumn())->setWidth(20);
                } elseif ($cell->getColumn() == 'F') {
                    $activeSheet->getColumnDimension($cell->getColumn())->setWidth(30);
                } elseif ($cell->getColumn() == 'G' || $cell->getColumn() == 'H' || $cell->getColumn() == 'I' || $cell->getColumn() == 'J' || $cell->getColumn() == 'K') {
                    $activeSheet->getColumnDimension($cell->getColumn())->setWidth(30);
                } else {
                    $activeSheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                }
            }
        }

        $writer = new Xlsx($spreadsheet);
        return $writer;
    }

    public function actionMlogs() {
        $logs = \common\models\Logs::find()->where(['status' => 1, 'type' => 2])->all();
        return $this->render('mlogs', [
                    'logs' => $logs,
        ]);
    }

    public function actionClogs() {
        $logs = \common\models\Logs::find()->where(['status' => 1, 'type' => 1])->all();
        return $this->render('clogs', [
                    'logs' => $logs,
        ]);
    }

    public function actionUpdatelogs() {
        $logs = \common\models\Logs::find()->where(['status' => 1])->all();
        //$archiveds = \common\models\Tender::find()->where(['is_archived' => 1])->all();
        if ($logs) {
            $tids = [];
            foreach ($logs as $_log) {
                $tids = explode(',', $_log->tid);
                $tids = array_unique($tids);
                if ($tids) {
                    foreach ($tids as $__tid) {
                        $data = ['is_archived' => 1];
                        $querydata = \Yii::$app
                                ->db
                                ->createCommand()
                                ->update('tenders', $data, 'id = ' . $__tid . '')
                                ->execute();
                    }
                }
            }
        }
    }

}
