<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use atlasmobile\analytics\Analytics;
use Google_Client;

/**
 * Site controller
 */
class SiteController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup', 'sendmail', 'terms', 'privacy', 'sendotp', 'verifyotp', 'getcities', 'register', 'counter'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'sendmail'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex() {
        //require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
        $addr = $_SERVER['REMOTE_ADDR'];
        $baseURL = Yii::$app->params['BASE_URL'];

        $states = \common\models\States::find()->where(['!=', 'name', ''])->andWhere(['country_id' => '101'])->all();
        $cables = \common\models\Make::find()->where(['mtype' => 1])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
        $lights = \common\models\Make::find()->where(['mtype' => 2])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
        $cements = \common\models\Make::find()->where(['mtype' => 14])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
        $rsteel = \common\models\Make::find()->where(['mtype' => 15])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
        $ssteel = \common\models\Make::find()->where(['mtype' => 16])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
        $nsteel = \common\models\Make::find()->where(['mtype' => 17])->andWhere(['status' => '1'])->orderBy(['make' => SORT_ASC])->all();
        $online = $this->actionTotalOnline();
        $visitors = \common\models\Visitors::find()->where(['status' => 1])->count();

        /* $analytics = new Analytics();
          $analytics->startDate = 'today';
          $analytics->endDate = 'today';

          $client = new Google_Client();
          $client->setAuthConfig($baseURL . '/assets/images/client.json');
          $client->addScope(Google_Service_Drive::DRIVE);

          $sessionsData = $analytics->getSessions();
          $visitorsData = $analytics->getUsers();
          $pageViewsData = $analytics->getPageViews();
          $avgSessionsDurationData = $analytics->getAvgSessionDuration();
          $countriesData = $analytics->getCountries();

          echo "<pre/>";
          print_r($pageViewsData);
          die(); */


        /* $googleAssertionCredentials = new Google_Auth_AssertionCredentials(
          $serviceAccountName, $scopes, file_get_contents($p12FilePath)
          ); */

        return $this->render('index', [
                    'states' => $states,
                    'cables' => $cables,
                    'lights' => $lights,
                    'cements' => $cements,
                    'rsteel' => $rsteel,
                    'ssteel' => $ssteel,
                    'nsteel' => $nsteel,
                    'visitor' => $visitors,
                    'online' => $online
        ]);
    }

    public function actionTotalOnline() {
        session_start();
        $_SESSION['session'] = session_id();
        $current_time = time();
        $timeout = $current_time - (60);
        $session_check = \common\models\Visitors::find()->where(['session' => $_SESSION['session']])->count();

        if ($session_check == 0 && $_SESSION['session'] != '') {
            $data = ['session' => $_SESSION['session'], 'time' => $current_time, 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];
            $visitorinsert = \Yii::$app
                    ->db
                    ->createCommand()
                    ->insert('visitors', $data)
                    ->execute();
        } else {
            $datas = ['time' => time()];
            $visitorupdate = \Yii::$app
                    ->db
                    ->createCommand()
                    ->update('visitors', $datas, '`session` = "' . $_SESSION['session'] . '"')
                    ->execute();
        }
        $online = \common\models\Visitors::find()->where(['>=', 'time', $timeout])->count();
        return $online;
    }

    public function actionCounter() {
        session_start();
        $imageURL = Yii::$app->params['IMAGE_URL'];
        $counter_name = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/counter.txt';

        // Check if a text file exists.
        //If not create one and initialize it to zero.
        if (!file_exists($counter_name)) {
            $f = fopen($counter_name, "w");
            fwrite($f, "0");
            fclose($f);
        }
        // Read the current value of our counter file
        $f = fopen($counter_name, "r");
        $counterVal = fread($f, filesize($counter_name));
        fclose($f);

        // Has visitor been counted in this session?
        // If not, increase counter value by one
        if (!isset($_SESSION['hasVisited'])) {
            $_SESSION['hasVisited'] = "yes";
            $counterVal++;
            $f = fopen($counter_name, "w");
            fwrite($f, $counterVal);
            fclose($f);
        }

        /* $counterVal = str_pad($counterVal, 5, "0", STR_PAD_LEFT);
          $chars = preg_split('//', $counterVal);
          $image = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/canvas.png';

          $im = imagecreatefrompng($image);

          $src1 = imagecreatefrompng("$chars[1].png");
          $src2 = imagecreatefrompng("$chars[2].png");
          $src3 = imagecreatefrompng("$chars[3].png");
          $src4 = imagecreatefrompng("$chars[4].png");
          $src5 = imagecreatefrompng("$chars[5].png");

          imagecopymerge($im, $src1, 0, 0, 0, 0, 56, 75, 100);
          imagecopymerge($im, $src2, 60, 0, 0, 0, 56, 75, 100);
          imagecopymerge($im, $src3, 120, 0, 0, 0, 56, 75, 100);
          imagecopymerge($im, $src4, 180, 0, 0, 0, 56, 75, 100);
          imagecopymerge($im, $src5, 240, 0, 0, 0, 56, 75, 100);

          // Output and free from memory
          header('Content-Type: image/png');
          echo imagepng($im);
          imagedestroy($im); */
        return $counterVal;
        die();
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

    public function actionRegister() {
        $data = $_REQUEST;
        if ($data['htype'] == 1 || $data['htype'] == 3) {
            if ((isset($data['cables']) && count($data['cables']) != 0) || (isset($data['lighting']) && count($data['lighting']) != 0 ) || (isset($data['cement']) && count($data['cement']) != 0 ) || (isset($data['rsteel']) && count($data['rsteel']) != 0 ) || (isset($data['ssteel']) && count($data['ssteel']) != 0 ) || (isset($data['nsteel']) && count($data['nsteel']) != 0 )) {
                if (@$data['terms'] == 1) {
                    $client = new \common\models\Clients();
                    $client->type = $data['htype'];
                    $client->firm = $data['prefirm'] . ' ' . $data['firm'];
                    $client->gst = $data['gst'];
                    $client->address = $data['address'];
                    $client->state = $data['state'];
                    if (isset($data['contracttype'])) {
                        $client->contracttype = @$data['contracttype'];
                    }
                    $client->city = $data['city'];
                    $client->pcode = $data['pcode'];
                    $client->cperson = $data['ctype'] . ' ' . $data['cperson'];
                    $client->cnumber = $data['cnumber'];
                    $client->phone = $data['phone'];
                    //$client->mails = $data['mails'];
                    $client->cemail = $data['cemail'] . ',' . $data['mails'];
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
                    $client->createdon = date('Y-m-d h:i:s');
                    $client->status = 1;
                    if ($client->save()) {
                        $name = 'Crispdata';
                        $email = 'suraj@crispdata.co.in';
                        if ($client->type == 1) {
                            $type = 'Manufacturer';
                            $selectedcables = 'No Cables Selected';
                            $selectedlights = 'No Lighting Selected';
                            $selectedcements = 'No Cement Selected';
                            $selectedrsteel = 'No Reinforcement Steel Selected';
                            $selectedssteel = 'No Structural Steel Selected';
                            $selectednsteel = 'No Non Structural Steel Selected';
                            if (isset($data['cables']) && count($data['cables'])) {
                                $allcables = implode(',', @$data['cables']);
                                $selectedcables = $this->actionProducts($allcables, 1);
                            }
                            if (isset($data['lighting']) && count($data['lighting'])) {
                                $alllights = implode(',', @$data['lighting']);
                                $selectedlights = $this->actionProducts($alllights, 2);
                            }
                            if (isset($data['cement']) && count($data['cement'])) {
                                $allcement = implode(',', @$data['cement']);
                                $selectedcements = $this->actionProducts($allcement, 14);
                            }
                            if (isset($data['rsteel']) && count($data['rsteel'])) {
                                $allrsteel = implode(',', @$data['rsteel']);
                                $selectedrsteel = $this->actionProducts($allrsteel, 15);
                            }
                            if (isset($data['ssteel']) && count($data['ssteel'])) {
                                $allssteel = implode(',', @$data['ssteel']);
                                $selectedssteel = $this->actionProducts($allssteel, 16);
                            }
                            if (isset($data['nsteel']) && count($data['nsteel'])) {
                                $allnsteel = implode(',', @$data['nsteel']);
                                $selectednsteel = $this->actionProducts($allnsteel, 17);
                            }
                            $product = '<h4></h4><b>Cables</b>
                                        <p>' . $selectedcables . '</p>
                                        <b>Lighting</b>
                                        <p>' . $selectedlights . '</p>
                                        <b>Cement</b>
                                        <p>' . $selectedcements . '</p>
                                        <b>Reinforcement Steel</b>
                                        <p>' . $selectedrsteel . '</p>
                                        <b>Structural Steel</b>
                                        <p>' . $selectedssteel . '</p>
                                        <b>Non Structural Steel</b>
                                        <p>' . $selectednsteel . '</p>';
                        } elseif ($client->type == 2) {
                            $type = 'Contractor';
                            if ($client->contracttype == 1) {
                                $ctype = 'Proprietorship';
                            } elseif ($client->contracttype == 2) {
                                $ctype = 'Partnership';
                            } elseif ($client->contracttype == 3) {
                                $ctype = 'Limited Liability Partnership';
                            } elseif ($client->contracttype == 4) {
                                $ctype = 'Pvt. Ltd. Company';
                            } elseif ($client->contracttype == 5) {
                                $ctype = 'Ltd. Company';
                            }
                        } elseif ($client->type == 3) {
                            $type = 'Dealer';
                            $selectedcables = 'No Cables Selected';
                            $selectedlights = 'No Lighting Selected';
                            $selectedcements = 'No Cement Selected';
                            $selectedrsteel = 'No Reinforcement Steel Selected';
                            $selectedssteel = 'No Structural Steel Selected';
                            $selectednsteel = 'No Non Structural Steel Selected';
                            if (isset($data['cables']) && count($data['cables'])) {
                                $allcables = implode(',', @$data['cables']);
                                $selectedcables = $this->actionProducts($allcables, 1);
                            }
                            if (isset($data['lighting']) && count($data['lighting'])) {
                                $alllights = implode(',', @$data['lighting']);
                                $selectedlights = $this->actionProducts($alllights, 2);
                            }
                            if (isset($data['cement']) && count($data['cement'])) {
                                $allcement = implode(',', @$data['cement']);
                                $selectedcements = $this->actionProducts($allcement, 14);
                            }
                            if (isset($data['rsteel']) && count($data['rsteel'])) {
                                $allrsteel = implode(',', @$data['rsteel']);
                                $selectedrsteel = $this->actionProducts($allrsteel, 15);
                            }
                            if (isset($data['ssteel']) && count($data['ssteel'])) {
                                $allssteel = implode(',', @$data['ssteel']);
                                $selectedssteel = $this->actionProducts($allssteel, 16);
                            }
                            if (isset($data['nsteel']) && count($data['nsteel'])) {
                                $allnsteel = implode(',', @$data['nsteel']);
                                $selectednsteel = $this->actionProducts($allnsteel, 17);
                            }
                            $product = '<h4></h4><b>Cables</b>
                                        <p>' . $selectedcables . '</p>
                                        <b>Lighting</b>
                                        <p>' . $selectedlights . '</p>
                                        <b>Cement</b>
                                        <p>' . $selectedcements . '</p>
                                        <b>Reinforcement Steel</b>
                                        <p>' . $selectedrsteel . '</p>
                                        <b>Structural Steel</b>
                                        <p>' . $selectedssteel . '</p>
                                        <b>Non Structural Steel</b>
                                        <p>' . $selectednsteel . '</p>';
                        } else {
                            $type = 'Supplier';
                        }
                        $city = $this->actionAddress($client->city, 1);
                        $state = $this->actionAddress($client->state, 2);
                        // Email will be send
                        //$to = "info@crispdata.co.in"; // Change with your email address
                        $to = "sajstyles21@gmail.com"; // Change with your email address
                        $sub = "New Registration on Crispdata"; // You can define email subject
                        // HTML Elements for Email Body
                        $body = <<<EOD
	<strong>Type of user:</strong> $type <br>
	<strong>Firm Name:</strong> $client->firm <br>
	<strong>GST No:</strong> $client->gst <br> 
EOD;

                        if ($client->type == 2) {
                            $body .= <<<EOD
                          <strong>Firm Type:</strong> $ctype <br>
EOD;
                        }
                        $body .= <<<EOD
        <strong>Address:</strong> $client->address, $city, $state, $client->pcode <br>
	<strong>Contact Person:</strong> $client->cperson <br>
	<strong>Phone Number:</strong> $client->phone <br>
	<strong>Mobile Number:</strong> $client->cnumber <br>
	<strong>Email:</strong> <a href="mailto:$client->cemail?subject=feedback" "email me">$client->cemail</a> <br> <br>
EOD;

                        $body .= <<<EOD
                         <b>Products:</b> $product <br> 
EOD;

//Must end on first column

                        $headers = "From: $name <$client->cemail>\r\n";
                        $headers .= 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                        // PHP email sender
                        mail($to, $sub, $body, $headers);
                        echo "1";
                    } else {
                        echo "0";
                    }
                } else {
                    echo "2";
                }
            } else {
                echo "3";
            }
        } else {
            if (@$data['terms'] == 1) {
                $client = new \common\models\Clients();
                $client->type = $data['htype'];
                $client->firm = $data['prefirm'] . ' ' . $data['firm'];
                $client->gst = $data['gst'];
                $client->address = $data['address'];
                $client->state = $data['state'];
                if (isset($data['contracttype'])) {
                    $client->contracttype = @$data['contracttype'];
                }
                $client->city = $data['city'];
                $client->pcode = $data['pcode'];
                $client->cperson = $data['ctype'] . ' ' . $data['cperson'];
                $client->cnumber = $data['cnumber'];
                $client->phone = $data['phone'];
                //$client->mails = $data['mails'];
                $client->cemail = $data['cemail'] . ',' . $data['mails'];
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
                $client->createdon = date('Y-m-d h:i:s');
                $client->status = 1;
                if ($client->save()) {
                    $name = 'Crispdata';
                    $email = 'suraj@crispdata.co.in';
                    if ($client->type == 1) {
                        
                    } elseif ($client->type == 2) {
                        $type = 'Contractor';
                        if ($client->contracttype == 1) {
                            $ctype = 'Proprietorship';
                        } elseif ($client->contracttype == 2) {
                            $ctype = 'Partnership';
                        } elseif ($client->contracttype == 3) {
                            $ctype = 'Limited Liability Partnership';
                        } elseif ($client->contracttype == 4) {
                            $ctype = 'Pvt. Ltd. Company';
                        } elseif ($client->contracttype == 5) {
                            $ctype = 'Ltd. Company';
                        }
                    } elseif ($client->type == 3) {
                        
                    } else {
                        $type = 'Supplier';
                    }
                    $city = $this->actionAddress($client->city, 1);
                    $state = $this->actionAddress($client->state, 2);
                    // Email will be send
                    //$to = "info@crispdata.co.in"; // Change with your email address
                    $to = "sajstyles21@gmail.com"; // Change with your email address
                    $sub = "New Registration on Crispdata"; // You can define email subject
                    // HTML Elements for Email Body
                    $body = <<<EOD
	<strong>Type of user:</strong> $type <br>
	<strong>Firm Name:</strong> $client->firm <br>
	<strong>GST No:</strong> $client->gst <br> 
EOD;

                    if ($client->type == 2) {
                        $body .= <<<EOD
                          <strong>Firm Type:</strong> $ctype <br>
EOD;
                    }
                    $body .= <<<EOD
        <strong>Address:</strong> $client->address, $city, $state, $client->pcode <br>
	<strong>Contact Person:</strong> $client->cperson <br>
	<strong>Phone Number:</strong> $client->phone <br>
	<strong>Mobile Number:</strong> $client->cnumber <br>
	<strong>Email:</strong> <a href="mailto:$client->cemail?subject=feedback" "email me">$client->cemail</a> <br> <br>
EOD;

//Must end on first column

                    $headers = "From: $name <$client->cemail>\r\n";
                    $headers .= 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                    // PHP email sender
                    mail($to, $sub, $body, $headers);
                    echo "1";
                } else {
                    echo "0";
                }
            } else {
                echo "2";
            }
        }

        die();
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

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    /* public function actionLogin()
      {
      if (!Yii::$app->user->isGuest) {
      return $this->goHome();
      }

      $model = new LoginForm();
      if ($model->load(Yii::$app->request->post()) && $model->login()) {
      return $this->goBack();
      } else {
      return $this->render('login', [
      'model' => $model,
      ]);
      }
      } */

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
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact() {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout() {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup() {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
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

    public function actionSendmail() {
        // Variables
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $mobile = trim($_POST['mobile']);
        $subject = trim($_POST['subject']);
        $message = trim($_POST['message']);


        if (isset($name) && isset($email)) {

            // Avoid Email Injection and Mail Form Script Hijacking
            $pattern = "/(content-type|bcc:|cc:|to:)/i";
            if (preg_match($pattern, $name) || preg_match($pattern, $email) || preg_match($pattern, $message)) {
                exit;
            }

            // Email will be send
            $to = "info@crispdata.co.in"; // Change with your email address
            $sub = "$subject"; // You can define email subject
            // HTML Elements for Email Body
            $body = <<<EOD
	<strong>Name:</strong> $name <br>
	<strong>Phone No.:</strong> $mobile <br>
	<strong>Email:</strong> <a href="mailto:$email?subject=feedback" "email me">$email</a> <br> <br>
	<strong>Message:</strong> $message <br>
EOD;
//Must end on first column

            $headers = "From: $name <$email>\r\n";
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            // PHP email sender
            mail($to, $sub, $body, $headers);
        }
    }

    public function actionTerms() {
        return $this->render('terms', [
        ]);
    }

    public function actionPrivacy() {
        return $this->render('privacy', [
        ]);
    }

    public function actionSendotp() {
        $email = @$_REQUEST['email'];
        $mobile = @$_REQUEST['mobile'];
        $vtype = @$_REQUEST['type'];
        if ($vtype == 2) {
            if ($email == '') {
                echo json_encode(['response' => '0']);
            } else {
                $otp = rand(2555, 9999);
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://control.msg91.com/api/sendmailotp.php?otp=" . $otp . "&template=&expiry=30&email=" . $email . "&authkey=257155AGlRw4S7XGNF5c407304",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "",
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);
                $resp = json_decode($response);


                //$name = 'Crispdata';
                //$ouremail = 'info@crispdata.co.in';
                // Email will be send
                /* $to = $email; // Change with your email address
                  $sub = "OTP Verification"; // You can define email subject
                  // HTML Elements for Email Body
                  $body = "One Time Password for Crispdata Signup is:<br/><br/>" . $otp;
                  //Must end on first column
                  $headers = "From: $name <$ouremail>\r\n";
                  $headers .= 'MIME-Version: 1.0' . "\r\n";
                  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                  // PHP email sender
                  $mail = mail($to, $sub, $body, $headers); */
                if (@$resp->msgType == 'success' || @$resp->msg_type == 'success') {
                    $data = ['type' => '2', 'otp' => $otp, 'email' => $email, 'is_expired' => '0', 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];
                    $querydata = \Yii::$app
                            ->db
                            ->createCommand()
                            ->insert('otp_expiry', $data)
                            ->execute();

                    if ($querydata) {
                        echo json_encode(['response' => '2']);
                    }
                } else {
                    echo json_encode(['response' => '3', 'mess' => ucfirst(str_replace('_', ' ', $resp->msg))]);
                }
            }
        } else {
            if ($mobile == '') {
                echo json_encode(['response' => '0']);
            } elseif (!preg_match('/^\d{10}$/', $mobile)) {
                echo json_encode(['response' => '1']);
            } else {
                $mobnumber = '91' . $mobile;
                $otp = rand(1000, 9999);
                $message = urlencode('Your verification code is ' . $otp . '.');
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://control.msg91.com/api/sendotp.php?authkey=257155AGlRw4S7XGNF5c407304&message=" . $message . "&sender=OTPSMS&mobile=" . $mobnumber . "&otp_length=4&otp=" . $otp . "&otp_expiry=30",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "",
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);


                $resp = json_decode($response);

                if ($resp->type == 'success') {
                    $data = ['type' => '1', 'otp' => $otp, 'mobile' => $mobile, 'is_expired' => '0', 'createdon' => date('Y-m-d h:i:s'), 'status' => 1];
                    $querydata = \Yii::$app
                            ->db
                            ->createCommand()
                            ->insert('otp_expiry', $data)
                            ->execute();
                    if ($querydata) {
                        echo json_encode(['response' => '2']);
                    }
                } else {
                    echo json_encode(['response' => '3', 'mess' => ucfirst(str_replace('_', ' ', $resp->message))]);
                }
            }
        }
    }

    public function actionVerifyotp() {
        $email = @$_REQUEST['email'];
        $mobile = @$_REQUEST['mobile'];
        $otp = $_REQUEST['otp'];
        $type = $_REQUEST['type'];
        if ($type == 2) {
            if ($otp == '') {
                echo json_encode(['response' => '0']);
            } else {
                $getotp = \common\models\Otpexpiry::find()->where(['otp' => $otp, 'email' => $email, 'is_expired' => 0, 'type' => 2])->one();
                if ($getotp) {
                    $timefromdatabase = strtotime($getotp->createdon);
                    $dif = strtotime(date('Y-m-d h:i:s')) - $timefromdatabase;
                    if ($dif > 1800) {
                        echo json_encode(['response' => 1]);
                    } else {
                        $getotp->is_expired = 1;
                        $getotp->save();
                        echo json_encode(['response' => 2]);
                    }
                } else {
                    echo json_encode(['response' => 1]);
                }
            }
        } else {
            if ($otp == '') {
                echo json_encode(['response' => '0']);
            } else {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://control.msg91.com/api/verifyRequestOTP.php?authkey=257155AGlRw4S7XGNF5c407304&mobile=91" . $mobile . "&otp=" . $otp . "",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "",
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_HTTPHEADER => array(
                        "content-type: application/x-www-form-urlencoded"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);
                $resp = json_decode($response);
                if ($resp->type == 'success') {
                    $getotp = \common\models\Otpexpiry::find()->where(['otp' => $otp, 'mobile' => $mobile, 'is_expired' => 0, 'type' => 1])->one();
                    $getotp->is_expired = 1;
                    $getotp->save();
                    echo json_encode(['response' => 2]);
                } else {
                    echo json_encode(['response' => 3, 'mess' => ucfirst(str_replace('_', ' ', $resp->message))]);
                }
            }
        }
    }

}
