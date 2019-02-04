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
                        'actions' => ['login', 'signup', 'request-password-reset', 'reset-password', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'sendmail', 'sendmess'],
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

        $user = Yii::$app->user->identity;
        $data = [];
        $finalmakes = [];
        $alldetails = [];
        $newidetails = [];
        $size = [];
        $tfit = [];
        $cfit = [];
        $tenders = \common\models\Tender::find()->where(['aoc_status' => 1])->orderBy(['id' => SORT_DESC])->all();
        if ($tenders) {
            foreach ($tenders as $_tender) {
                $tdetails = '';
                $command = Sitecontroller::actionGetcommand($_tender->command);
                $cengineer = \common\models\Cengineer::find()->where(['id' => $_tender->cengineer, 'status' => 1])->one();
                $cwengineer = \common\models\Cwengineer::find()->where(['id' => $_tender->cwengineer, 'status' => 1])->one();
                $gengineer = \common\models\Gengineer::find()->where(['gid' => $_tender->gengineer, 'status' => 1])->one();
                $items = \common\models\Item::find()->where(['tender_id' => $_tender->id, 'status' => 1])->all();
                $tdetails = @$command . ' ' . @$cengineer->text . ' ' . @$cwengineer->text . ' ' . @$gengineer->text;
                if ($items) {
                    foreach ($items as $_item) {
                        $idetails = \common\models\ItemDetails::find()->where(['item_id' => $_item->id])->one();
                        if ($idetails) {
                            $imakes = explode(',', $idetails->make);
                            $descfull = 'E/M,';
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
                                } else {
                                    $core = '4 Core';
                                }
                                $newidetails['itemtender'] = $idetails->itemtender;
                                $newidetails['tdetails'] = $tdetails;
                                $newidetails['idetails'] = $descfull;
                                $newidetails['sizes'] = @$size->size;
                                $newidetails['units'] = $idetails->units;
                                $newidetails['quantity'] = $idetails->quantity;
                                $newidetails['make'] = $_make;
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
                                $newidetails['ttype'] = $_item->tenderfour;
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



        if (@$data) {
            foreach ($data as $k => $_data) {
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
                            //$header[] = "Tender Id" . "\t";
                            $header[] = "Tender Details" . "\t";
                            $header[] = "Item Details" . "\t";
                            //$header[] = "Item Sr.No. of Tender" . "\t";
                            $header[] = "Size" . "\t";
                            $header[] = "Core" . "\t";
                            $header[] = "Units" . "\t";
                            $header[] = "Quantity" . "\t";
                            $header[] = "All Makes" . "\t";
                            $header[] = "Firm Name" . "\t";
                            $header[] = "Contact Person Name" . "\t";
                            $header[] = "Firm Address" . "\t";
                            $header[] = "Firm Contact Number" . "\t";
                            $header[] = "Firm Email Id" . "\t";
                        } elseif ($__data['ttype'] == 2) {
                            $header[] = "Sr.No." . "\t";
                            //$header[] = "Tender Id." . "\t";
                            $header[] = "Tender Details" . "\t";
                            $header[] = "Item Details" . "\t";
                            //$header[] = "Item Sr.No. of Tender" . "\t";
                            $header[] = "Type of Fitting" . "\t";
                            $header[] = "Capacity of Fitting" . "\t";
                            $header[] = "Units" . "\t";
                            $header[] = "Quantity" . "\t";
                            $header[] = "All Makes" . "\t";
                            $header[] = "Firm Name" . "\t";
                            $header[] = "Contact Person Name" . "\t";
                            $header[] = "Firm Address" . "\t";
                            $header[] = "Firm Contact Number" . "\t";
                            $header[] = "Firm Email Id" . "\t";
                        }
                        //$datas = '';
                        //$datas .= join($header) . "\n";
                        $final[] = $header;
                    }


                    if ($__data['ttype'] == 1) {
                        $arrayData = [];
                        if (in_array($__data['ref'], $tid)) {
                            $arrayData[] = '';
                            //$arrayData[] = '';
                            $arrayData[] = '';
                        } else {
                            $arrayData[] = $sno;
                            $tid[] = $__data['ref'];
                            //$arrayData[] = $__data['ref'];
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

                        $final[] = $arrayData;
                        //$datas .= join("\t", $arrayData) . "\n";
                    } elseif ($__data['ttype'] == 2) {
                        $arrayData = [];
                        if (in_array($__data['ref'], $tid)) {
                            $arrayData[] = '';
                            //$arrayData[] = '';
                            $arrayData[] = '';
                        } else {
                            $arrayData[] = $sno;
                            $tid[] = $__data['ref'];
                            //$arrayData[] = $__data['ref'];
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
                        $final[] = $arrayData;
                        //$datas .= join("\t", $row1) . "\n";
                    }
                    $i++;
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

                $styleArray = [
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ]
                ];

                $styleArrayinside = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ]
                ];

                $activeSheet->getStyle('H1:H' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getStyle('K1:K' . $activeSheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);
                $activeSheet->getPageSetup()->setHorizontalCentered(true);
                $activeSheet->getStyle('A1:M1')->applyFromArray($styleArray);
                //$activeSheet->getStyle('E')->applyFromArray($styleArrayinside);
                $activeSheet->getStyle('A1:M1')
                        ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);

                $activeSheet->getStyle('A1:M1')->getFont()->setSize(15);
                $cellIterator = $activeSheet->getRowIterator()->current()->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(true);
                /** @var PHPExcel_Cell $cell */
                foreach ($cellIterator as $cell) {
                    if ($cell->getColumn() != 'H') {
                        $activeSheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                    } else {
                        $activeSheet->getColumnDimension('H')->setWidth(100);
                    }
                }


// Create Excel file and sve in your directory
                $writer = new Xlsx($spreadsheet);

                /* header('Content-Type: application/vnd.ms-excel');
                  header('Content-Disposition: attachment;filename="' . "" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace(' ', '_', $__data['makename']) . '.xlsx"');
                  header('Cache-Control: max-age=0');

                  $writer->save('php://output'); */
                //die();
                $writer->save("" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace(' ', '_', $__data['makename']) . ".xlsx");
                //file_put_contents("" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace(' ', '_', $__data['makename']) . ".xlsx", $datas);
                $filename = "" . $_SERVER['DOCUMENT_ROOT'] . "/backend/web/pdf/" . str_replace(' ', '_', $__data['makename']) . ".xlsx";
                //$file = $this->upload($filename);
                $htmlfilepath = Yii::getAlias('@common/mail/home-link.php');
                $textfilepath = '';
                $ses = new \SimpleEmailService(Yii::$app->params['IAM_KEY'], Yii::$app->params['IAM_SECRET']);
                $m = new \SimpleEmailServiceMessage();
                $m->addTo('sajstyles21@gmail.com');
                $m->setFrom("Crispdata <info@crispdata.co.in>");
                $m->setSubject('Hello, world!');
                $m->setMessageFromString('This is the message body.');
                $m->setMessageFromFile($textfilepath, $htmlfilepath);
                $m->addAttachmentFromFile('logo.png', Yii::getAlias('@app/web/assets/images/crispdatalogo.png'), 'application/octet-stream', '<logo.png>', 'inline');
                $m->addAttachmentFromFile('crispdata.png',  Yii::getAlias('@app/web/assets/images/crispdatalogo.png'), 'application/octet-stream');
                $m->addAttachmentFromFile(str_replace(' ', '_', $__data['makename']).'.xlsx', $filename, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                $m->setSubjectCharset('ISO-8859-1');
                $m->setMessageCharset('ISO-8859-1');

                print_r($ses->sendEmail($m));
                die();

                Yii::$app->mail->compose('home-link', ['logo' => Yii::getAlias('@app/web/assets/images/crispdatalogo.png')]) // a view rendering result becomes the message body here
                        ->setFrom(['info@crispdata.co.in' => 'Sample Mail'])
                        //->setTo($__data['email'])
                        ->setTo('sajstyles21@gmail.com')
                        ->setSubject('Message subject')
                        ->attach($filename)
                        ->send();

                //Yii::$app->mailer->getSES()->enableKeepAlive(false);
            }
        }

        Yii::$app->session->setFlash('success', "Mails successfully sent");
        return $this->redirect(array('mail/index'));
    }

    public function upload($filepath) {
        $bucket = 'crispdata';
        $keyname = '/manual/upload.txt';
        $filepath = '/Users/Jeff/Sites/hello/upload.txt';
        $s = new Storage();
        $result = $s->s3->putObject(array(
            'Bucket' => $bucket,
            'Key' => $keyname,
            'SourceFile' => $filepath,
            'ContentType' => 'text/plain',
            'ACL' => 'public-read',
            'StorageClass' => 'REDUCED_REDUNDANCY',
            'Metadata' => array(
                'param1' => 'value 1',
                'param2' => 'value 2'
            )
        ));
        return $result;
    }

}
