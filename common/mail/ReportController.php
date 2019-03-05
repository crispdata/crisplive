<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use common\models\Project;
use common\models\Role;
use common\models\Page;
use common\models\PageItem;
use common\models\ItemPage;
use common\models\ItemTimeline;
use common\models\ItemColor;
use common\models\ItemPeople;
use common\models\Report;
use common\models\Setting;
use backend\models\GenerateReport;
use kartik\mpdf\Pdf;

/**
 * RecentchangesController controller
 */
class ReportController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['generate-report', 'test', 'download', 'get-report', 'report-content'],
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
     * Generate Report action.
     *
     * @return mixed
     */
    public function actionGenerateReport() {
        $GenerateReport = [];
        $graph = 0;

        if (isset($_POST['GenerateReport'])) {

            if (isset($_POST['IsTimeline']) && $_POST['IsTimeline'] == "true") {

                $GenerateReport = $_POST['GenerateReport'];
            } else {

                $project = Project::find()->where(['ProjectId' => $_POST['GenerateReport']['project_id']])->one();
                $EmailToReportArray = [];
                $EmailFromContactsArray = [];
                $EmailToOthersArray = [];
                $CompanyName = "";
                $EventName = "";
                $clientName = "";
                $EventStartDate = "";
                $EventEndDate = "";
                $Address = "";
                $Version = "";



                if ($_POST['GenerateReport']['ReportFormat'] != 'download_pdf') {

                    if ($_POST['GenerateReport']['ReportFormat'] != 'email_me') {

                        if (isset($_POST['GenerateReport']['FromProject'])) {

                            $project = Project::find()->where(['ProjectId' => $_POST['GenerateReport']['FromProject']])->one();
                            $roles = $project->getRoles($project->ProjectId);

                            foreach ($roles as $role) {
                                if (isset($role->UserId) && !empty($role->UserId)) {
                                    $contact = $project->getContact($role->UserId);
                                    $EmailToReportArray[] = $contact->email;
                                }
                            }
                        }

                        if (isset($_POST['GenerateReport']['ContactValue'])) {

                            $project = new Project();

                            foreach ($_POST['GenerateReport']['ContactValue'] as $ContactId) {
                                $contact = $project->getContact($ContactId);
                                $EmailFromContactsArray[] = $contact->email;
                            }
                        }

                        if (isset($_POST['GenerateReport']['FromOther'])) {

                            $EmailToOthersArray[] = $_POST['GenerateReport']['FromOther'];
                        }
                    } else {

                        if (isset($_POST['GenerateReport']['OtherEmail'])) {

                            $EmailToOthersArray = $_POST['GenerateReport']['OtherEmail'];
                        }
                    }
                } else {

                    $CompanyName = $_POST['GenerateReport']['CompanyName'];
                    $EventName = $_POST['GenerateReport']['EventName'];
                    $clientName = $_POST['GenerateReport']['clientName'];
                    $EventStartDate = $_POST['GenerateReport']['EventStartDate'];
                    $EventEndDate = $_POST['GenerateReport']['EventEndDate'];
                    $Address = $_POST['GenerateReport']['Address'];
                    $Version = $_POST['GenerateReport']['Version'];
                }

                $model = new GenerateReport();

                $model->ProjectId = $_POST['GenerateReport']['project_id'];

                if (in_array('all', $_POST['GenerateReport']['PageName'])) {
                    unset($_POST['GenerateReport']['PageName']['0']);
                }

                $Pages = implode(", ", $_POST['GenerateReport']['PageName']);

                $model->Pages = $Pages;
                $model->PagesOrder = $_POST['GenerateReport']['OrganizedBy'];
                $model->IncludeTimeline = $_POST['GenerateReport']['IsTimeline'];

                if (isset($_POST['GenerateReport']['DateType']) && $_POST['GenerateReport']['DateType'] != "0") {
                    if ($_POST['GenerateReport']['DateType'] == "date_range") {
                        $model->EventRangeFrom = $_POST['GenerateReport']['StartDate'];
                        $model->EventRangeTo = $_POST['GenerateReport']['EndDate'];
                    }
                    if ($_POST['GenerateReport']['DateType'] == "event_days") {
                        $model->EventDateFrom = $_POST['GenerateReport']['StartDate'];
                        $model->EventDateTo = $_POST['GenerateReport']['EndDate'];
                    }
                }

                $model->TimeLineView = isset($_POST['GenerateReport']['TimelineDisplay']) ? $_POST['GenerateReport']['TimelineDisplay'] : '';
                $model->ReportFormat = $_POST['GenerateReport']['ReportFormat'];

                $EmailToReport = implode(", ", $EmailToReportArray);
                $EmailFromContacts = implode(", ", $EmailFromContactsArray);
                $EmailToOthers = implode(", ", $EmailToOthersArray);

                $model->EmailToReport = $EmailToReport;
                $model->EmailFromContacts = $EmailFromContacts;
                $model->EmailToOthers = $EmailToOthers;
                $model->CompanyName = $CompanyName;
                $model->EventName = $EventName;
                $model->clientName = $clientName;
                $model->EventStartDate = $EventStartDate;
                $model->EventEndDate = $EventEndDate;
                $model->Address = $Address;
                $model->Logo = "";
                $model->Version = $Version;

                if (isset($_FILES['LogoImage']['name']) && !empty($_FILES['LogoImage']['name'])) {

                    $last_report = Report::find()->orderBy('ReportId DESC')->One();

                    $logo_id = $last_report->ReportId + 1;

                    $logo_target = 'images/report_logo_' . $logo_id . '.' . pathinfo($_FILES['LogoImage']['name'], PATHINFO_EXTENSION);

                    if (move_uploaded_file($_FILES["LogoImage"]["tmp_name"], $logo_target)) {
                        $model->Logo = $logo_target;
                    }
                } else {

                    $user_id = Yii::$app->user->identity->UserId;

                    $setting = Setting::find()->where(['UserId' => $user_id])->One();

                    $model->Logo = isset($setting->Logo) ? $setting->Logo : "";
                }

                if ($report = $model->create()) {

                    //$report->getPages($report->Pages);

                    $pagesList = explode(", ", $report->Pages);
                    $pageObj = Page::find()->where(['PageID' => $pagesList])->all();

                    $pageItems = [];
                    $ItemTimeline = [];

                    if (!empty($report->EventRangeFrom)) {
                        $start_date_range = $report->EventRangeFrom;
                        $end_date_range = $report->EventRangeTo;
                    } else if (!empty($report->EventDateFrom)) {
                        $start_date_range = $report->EventDateFrom;
                        $end_date_range = $report->EventDateTo;
                    } else {
                        $start_date_range = "";
                        $end_date_range = "";
                    }

                    $timeline = [];
                    $timeline1 = [];
                    $timelinePDF = [];
                    $TimeLineViews = [];

                    if ($report->IncludeTimeline) {



                        foreach ($pageObj as $page) {

                            $DupPageItemIdArr = [];
                            $DupPageItemIdArr1 = [];

                            $DupPageItemId = ItemPage::find()
                                    ->select('PageItemId')
                                    ->where(['PageId' => $page->PageID])
                                    ->all();

                            foreach ($DupPageItemId as $id) {
                                $DupPageItemIdArr1[] = $id->PageItemId;
                            }

                            $PageItemId = PageItem::find()
                                    ->select('ItemID')
                                    ->where(['PageId' => $page->PageID])
                                    ->all();

                            foreach ($PageItemId as $id) {
                                $DupPageItemIdArr1[] = $id->ItemID;
                            }

                            $sortByColor = '';

                            if ($report->PagesOrder == "A") {

                                $sortArr = [];

                                foreach ($DupPageItemIdArr1 as $id) {
                                    $ItemTitle = PageItem::find()
                                            ->select('Title')
                                            ->where(['ItemID' => $id])
                                            ->One();

                                    $sortArr[$id] = @$ItemTitle->Title;
                                }

                                asort($sortArr);

                                $DupPageItemIdArr = array_keys($sortArr);
                            } else if ($report->PagesOrder == "U") {

                                $sortArr = [];

                                foreach ($DupPageItemIdArr1 as $id) {

                                    $ItemRoleId = ItemPeople::find()
                                            ->select('RoleId')
                                            ->where(['PageItemId' => $id])
                                            ->One();

                                    $role_id = isset($ItemRoleId->RoleId) ? $ItemRoleId->RoleId : '';

                                    $ItemRoleName = Role::find()
                                            ->select('RoleName')
                                            ->where(['RoleId' => $role_id])
                                            ->One();

                                    $sortArr[$id] = isset($ItemRoleName->RoleName) ? $ItemRoleName->RoleName : '';
                                }

                                $DupPageItemIdArr = array_keys($sortArr);
                            } else if ($report->PagesOrder == "C") {

                                $sortByColor = 'Yes';

                                $DupPageItemIdArr = $DupPageItemIdArr1;
                            } else {

                                $DupPageItemIdArr = $DupPageItemIdArr1;
                            }

                            $p_i = [];

                            foreach ($DupPageItemIdArr as $id) {
                                $p_i_first = PageItem::find()->with('timeline');

                                if ($sortByColor == "Yes") {
                                    $p_i_first->with('color');
                                }

                                $p_i_first->where(['ItemID' => $id]);


                                $p_i[] = $p_i_first->One();
                            }
                            $pageItems[] = $p_i;

                            $timelinePrev['pagename'] = $page->PageName;
                            $timelinePDF[] = $timelinePrev;
                        }

                        $t_id_array = [];

                        foreach ($pageItems as $key => $pageItem) {
                            $timelinePrev = [];
                            foreach ($pageItem as $item) {

                                if (isset($item->timeline->StartDate) && !empty($item->timeline->StartDate) && $item->timeline->IsActive == 1) {

                                    if (!empty($start_date_range)) {

                                        if (strtotime($start_date_range) <= strtotime($item->timeline->StartDate) && strtotime($item->timeline->StartDate) <= strtotime($end_date_range)) {

                                            $t_l_v = [];

                                            $t_id_array[] = $item->timeline->ItemTimelineId;

                                            $t_l_v['Day'] = date('D', strtotime($item->timeline->StartDate));
                                            $t_l_v['Date'] = date('l m/d/Y', strtotime($item->timeline->StartDate));
                                            $t_l_v['Activity'] = $item->Description;
                                            $t_l_v['StartTime'] = isset($item->timeline->StartTime) ? $item->timeline->StartTime : '';
                                            $t_l_v['EndTime'] = isset($item->timeline->EndTime) ? $item->timeline->EndTime : '';
                                            $t_l_v['StartDate'] = isset($item->timeline->StartDate) ? $item->timeline->StartDate : '';
                                            $t_l_v['EndDate'] = isset($item->timeline->EndDate) ? $item->timeline->EndDate : '';
                                            $t_l_v['Title'] = $item->Title;

                                            if ($sortByColor == "Yes") {
                                                $t_l_v['Color'] = $t_l['Color'] = isset($item->color->Color) ? $item->color->Color : '';
                                            }
                                            $timeline1[] = $t_l_v;
                                        }
                                    }
                                }
                                $t_l = [];
                                $t_l['Title'] = $item->Title;
                                $t_l['Note'] = $item->Description;
                                $timelinePrev[] = $t_l;
                            }
                            $timelinePDF[$key]['timeline'] = $timelinePrev;
                        }

                        $rest_timeline = $this->getRestTimeline($_POST['GenerateReport']['project_id'], array_unique($t_id_array), $start_date_range, $end_date_range);

                        $timeline = array_merge($timeline1, $rest_timeline);


                        usort($timeline, function($a, $b) {
                            $t1 = strtotime($a['StartDate']);
                            $t2 = strtotime($b['StartDate']);
                            return $t1 - $t2;
                        });


                        if ($report->TimeLineView == "W") {

                            $dup_timeline = $timeline;

                            foreach ($dup_timeline as $dup_t_l) {
                                $TimeLineView = [];

                                $resp = $this->rangeWeek($dup_t_l['Date']);

                                $inserted = false;

                                if (!empty($TimeLineViews)) {
                                    foreach ($TimeLineViews as $key => $t_l_v) {
                                        if ($t_l_v['start'] == $resp['start']) {
                                            $TimeLineViews[$key]['timeline'][] = $dup_t_l;
                                            $inserted = true;
                                        }
                                    }
                                }
                                if (!$inserted) {
                                    $TimeLineView['start'] = $resp['start'];
                                    $TimeLineView['end'] = $resp['end'];
                                    $TimeLineView['timeline'][] = $dup_t_l;

                                    $TimeLineViews[] = $TimeLineView;
                                }
                            }
                        } else if ($report->TimeLineView == "D") {
                            $dup_timeline = $timeline;

                            foreach ($dup_timeline as $dup_t_l) {
                                $TimeLineView = [];

                                $inserted = false;

                                if (!empty($TimeLineViews)) {
                                    foreach ($TimeLineViews as $key => $t_l_v) {
                                        if ($t_l_v['start'] == $dup_t_l['Date']) {
                                            $TimeLineViews[$key]['timeline'][] = $dup_t_l;
                                            $inserted = true;
                                        }
                                    }
                                }
                                if (!$inserted) {
                                    $TimeLineView['start'] = $dup_t_l['Date'];
                                    $TimeLineView['end'] = "";
                                    $TimeLineView['timeline'][] = $dup_t_l;

                                    $TimeLineViews[] = $TimeLineView;
                                }
                            }
                        } else if ($report->TimeLineView == "M") {
                            $dup_timeline = $timeline;

                            foreach ($dup_timeline as $dup_t_l) {
                                $TimeLineView = [];

                                $resp = $this->rangeMonth($dup_t_l['Date']);

                                $inserted = false;

                                if (!empty($TimeLineViews)) {
                                    foreach ($TimeLineViews as $key => $t_l_v) {
                                        if ($t_l_v['start'] == $resp['start']) {
                                            $TimeLineViews[$key]['timeline'][] = $dup_t_l;
                                            $inserted = true;
                                        }
                                    }
                                }
                                if (!$inserted) {
                                    $TimeLineView['start'] = $resp['start'];
                                    $TimeLineView['end'] = $resp['end'];
                                    $TimeLineView['timeline'][] = $dup_t_l;

                                    $TimeLineViews[] = $TimeLineView;
                                }
                            }
                        } else if ($report->TimeLineView == "O") {
                            $dup_timeline = $timeline;

                            $TimeLineView = [];

                            $TimeLineView['start'] = "";
                            $TimeLineView['end'] = "";
                            $TimeLineView['timeline'] = [];
                            foreach ($dup_timeline as $dup_t_l) {

                                $TimeLineView['timeline'][] = $dup_t_l;
                            }
                            $TimeLineViews[] = $TimeLineView;
                        } else {
                            $graph = '1';
                            $dup_timeline = $timeline;

                            $TimeLineView = [];

                            $TimeLineView['start'] = "";
                            $TimeLineView['end'] = "";
                            $TimeLineView['timeline'] = [];
                            foreach ($dup_timeline as $dup_t_l) {

                                $TimeLineView['timeline'][] = $dup_t_l;
                            }
                            $TimeLineViews[] = $TimeLineView;
                        }

                        if (!empty($TimeLineViews)) {
                            foreach ($TimeLineViews as $t_v) {
                                $timeA[] = strtotime($t_v['start']);
                            }

                            asort($timeA);

                            $TimeLineViews1 = [];
                            $TimeLineViews1 = $TimeLineViews;
                            $TimeLineViews = [];

                            foreach ($timeA as $key => $time_val) {
                                $TimeLineViews[] = $TimeLineViews1[$key];
                            }
                        }
                    } else {

                        foreach ($pageObj as $page) {

                            $DupPageItemIdArr = [];
                            $DupPageItemIdArr1 = [];

                            $DupPageItemId = ItemPage::find()
                                    ->select('PageItemId')
                                    ->where(['PageId' => $page->PageID])
                                    ->all();

                            foreach ($DupPageItemId as $id) {
                                $DupPageItemIdArr1[] = $id->PageItemId;
                            }

                            $PageItemId = PageItem::find()
                                    ->select('ItemID')
                                    ->where(['PageId' => $page->PageID])
                                    ->all();

                            foreach ($PageItemId as $id) {
                                $DupPageItemIdArr1[] = $id->ItemID;
                            }

                            $sortByColor = "";

                            if ($report->PagesOrder == "A") {

                                $sortArr = [];

                                foreach ($DupPageItemIdArr1 as $id) {
                                    $ItemTitle = PageItem::find()
                                            ->select('Title')
                                            ->where(['ItemID' => $id])
                                            ->One();

                                    $sortArr[$id] = @$ItemTitle->Title;
                                }

                                asort($sortArr);

                                $DupPageItemIdArr = array_keys($sortArr);
                            } else if ($report->PagesOrder == "U") {

                                $sortArr = [];

                                foreach ($DupPageItemIdArr1 as $id) {

                                    $ItemRoleId = ItemPeople::find()
                                            ->select('RoleId')
                                            ->where(['PageItemId' => $id])
                                            ->One();

                                    $role_id = isset($ItemRoleId->RoleId) ? $ItemRoleId->RoleId : '';

                                    $ItemRoleName = Role::find()
                                            ->select('RoleName')
                                            ->where(['RoleId' => $role_id])
                                            ->One();

                                    $sortArr[$id] = isset($ItemRoleName->RoleName) ? $ItemRoleName->RoleName : '';
                                }

                                $DupPageItemIdArr = array_keys($sortArr);
                            } else if ($report->PagesOrder == "C") {

                                $sortByColor = "Yes";

                                $DupPageItemIdArr = $DupPageItemIdArr1;
                            } else {
                                $DupPageItemIdArr = $DupPageItemIdArr1;
                            }

                            $p_i = [];

                            foreach ($DupPageItemIdArr as $id) {
                                $p_i_first = PageItem::find();

                                if ($sortByColor == "Yes") {
                                    $p_i_first->with('color');
                                }

                                $p_i_first->where(['ItemID' => $id]);


                                $p_i[] = $p_i_first->asArray()->One();
                            }

                            $pageItems[] = $p_i;
                        }
                    }

                    /* print_r($timelinePDF);
                      print_r($pageObj);
                      print_r($pageItems);
                      print_r($TimeLineViews);
                      die(); */
                    if ($model->ReportFormat == "download_pdf") {

                        $this->generatePdf($project, $report, $timelinePDF, $pageObj, $pageItems, $TimeLineViews, $graph);
                        $resp = [
                            'status' => 'success',
                            'report_id' => $report->ReportId,
                            'project_id' => $project->ProjectId,
                            'project_name' => $project->EventName,
                            'version' => str_replace(' ', '', $report->Version),
                        ];
                        $report->ReportFile = 'pdf/' . $project->EventName . '-Summary-' . str_replace(' ', '', $report->Version) . '.pdf';
                        if ($report->save()) {

                            echo json_encode($resp);
                        }
                        die();
                    } else {

                        $username = Yii::$app->user->identity->username;
                        $this->generatePdfEmail($project, $report, $timelinePDF, $pageObj, $pageItems, $TimeLineViews, $graph);
                        $filename = 'pdf/' . $project->EventName . '.pdf';

                        $emailArray = [];
                        $A1 = [];
                        $A2 = [];
                        $A3 = [];
                        if (!empty($report->EmailToReport)) {
                            $A1 = explode(', ', $report->EmailToReport);
                        }
                        if (!empty($report->EmailFromContacts)) {
                            $A2 = explode(', ', $report->EmailFromContacts);
                        }
                        if (!empty($report->EmailToOthers)) {
                            $A3 = explode(', ', $report->EmailToOthers);
                        }
                        $emailArray = array_merge($A1, $A2, $A3);

                        $mail = Yii::$app
                                ->mailer
                                ->compose(
                                        ['html' => 'simplemailattach-html', 'text' => 'simplemailattach-text'], ['pages' => $pageObj, 'pageItems' => $pageItems, 'username' => $username]
                                )
                                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                                ->setTo($emailArray)
                                ->setSubject('Report: ' . Yii::$app->name)
                                ->attach($filename)
                                ->send();
                        

                        if ($mail) {
                            $resp = [
                                'stat' => 'success',
                                'project_id' => $project->ProjectId,
                            ];
                            echo json_encode($resp);
                        } else {
                            $resp = [
                                'stat' => 'failure',
                                'project_id' => $project->ProjectId,
                            ];
                            echo json_encode($resp);
                        }
                    }
                    die();
                }
            }
        }

        $projects = Project::find()->orderBy('CreatedOn DESC')->all();
        $project = Project::find()->where(['ProjectId' => $_GET['project']])->one();


        $pages = Page::find()
                ->where(['ProjectId' => $_GET['project'], 'IsActive' => '1'])
                ->orderBy('CreatedOn DESC')
                ->all();

        return $this->render('generateReport', [
                    'projects' => $projects,
                    'project' => $project,
                    'pages' => $pages,
                    'GenerateReport' => $GenerateReport,
        ]);
    }

    /**
     * generatePdf action.
     *
     * @return mixed
     */
    public function generatePdf($project, $report, $timeline, $pageObj, $pageItems, $TimeLineViews, $graph) {
        // get your HTML raw content without any layouts or scripts
        $content = '';
        if (!empty($timeline)) {
            if ($graph == 0) {
                $content = $this->renderPartial('_reportView', ['project' => $project, 'report' => $report, 'timeline' => $timeline, 'TimeLineViews' => $TimeLineViews]);
            } else {
                $content = $this->renderPartial('_graphical', ['project' => $project, 'report' => $report, 'timeline' => $timeline, 'TimeLineViews' => $TimeLineViews]);
            }
        } else {
            $content = $this->renderPartial('_withoutTimeline', ['project' => $project, 'report' => $report, 'pages' => $pageObj, 'pageItems' => $pageItems, 'TimeLineViews' => $TimeLineViews]);
        }

        //$content = "Welcome to test pdf";
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'filename' => 'pdf/' . $project->EventName . '-Summary-' . str_replace(' ', '', $report->Version) . '.pdf',
            'destination' => Pdf::DEST_FILE,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            'methods' => []
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * generatePdf action.
     *
     * @return mixed
     */
    public function generatePdfEmail($project, $report, $timeline, $pageObj, $pageItems, $TimeLineViews, $graph) {
        // get your HTML raw content without any layouts or scripts
        $content = '';
        if (!empty($timeline)) {
            if ($graph == 0) {
                $content = $this->renderPartial('_reportView', ['project' => $project, 'report' => $report, 'timeline' => $timeline, 'TimeLineViews' => $TimeLineViews]);
            } else {
                $content = $this->renderPartial('_graphical', ['project' => $project, 'report' => $report, 'timeline' => $timeline, 'TimeLineViews' => $TimeLineViews]);
            }
        } else {
            $content = $this->renderPartial('_withoutTimeline', ['project' => $project, 'report' => $report, 'pages' => $pageObj, 'pageItems' => $pageItems, 'TimeLineViews' => $TimeLineViews]);
        }

        //$content = "Welcome to test pdf";
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'filename' => 'pdf/' . $project->EventName . '.pdf',
            'destination' => Pdf::DEST_FILE,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            'methods' => []
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * test action.
     *
     * @return mixed
     */
    public function actionDownload() {
        $fname = str_replace('pdf/', '', $_REQUEST['f']);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $fname . '');
        readfile(Yii::$app->params['BASE_URL'] . $_REQUEST['f']);
        exit;
    }

    public function actionGetReport() {

        $pages = ['page1', 'page2', 'page3'];
        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('_reportView', ['pages' => $pages]);
        //$content = "Welcome to test pdf";
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            'methods' => []
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    public function actionReportContent() {

        $user_id = Yii::$app->user->identity->UserId;

        $project = Project::find()->where(['ProjectId' => $_POST['project_id']])->One();
        $count = Report::find()->where(['ProjectId' => $_POST['project_id']])->Count();

        $setting = Setting::find()->where(['UserId' => $user_id])->One();

        $CompanyName = isset($setting->CompanyName) ? $setting->CompanyName : '';
        $Address = isset($project->EventLocation) ? $project->EventLocation : '';
        $Version = !empty($count) ? 'Version ' . ++$count : 'Version 1';
        $clientName = isset($project->getClient($project->ClientId)->clientName) ? $project->getClient($project->ClientId)->clientName : '';
        $EventName = isset($project->EventName) ? $project->EventName : '';

        $EventStartDateShow = $project->EventStartDate ? date('m-d-Y', strtotime($project->EventStartDate)) : '';
        $EventStartDateValue = $project->EventStartDate ? date('Y-m-d', strtotime($project->EventStartDate)) : '';
        $EventEndDateShow = $project->EventEndDate ? date('m-d-Y', strtotime($project->EventEndDate)) : '';
        $EventEndDateValue = $project->EventEndDate ? date('Y-m-d', strtotime($project->EventEndDate)) : '';
        $logo = isset($setting->Logo) ? str_replace('images/', '', $setting->Logo) : '';

        $color = $EventStartDateShow ? 'style = "color:#666666"' : '';

        $Report_html = <<<HTML
				
				<div class="row">
					<div class="input-field col s12 m4 offset-m4">
						<label for="company_name" class = "active">Company Name</label>
						<input id="company_name" type="text" value = "{$CompanyName}" name = "GenerateReport[CompanyName]">
					</div>
					<div class="input-field col s12 m4 offset-m4">
						<label for="EventName" class = "active">Event Name</label>
						<input id="EventName" type="text" value = "{$EventName}" name = "GenerateReport[EventName]">
					</div>
					<div class="input-field col s12 m4 offset-m4">
						<label for="clientName" class = "active">Client Name</label>
						<input id="clientName" type="text" value = "{$clientName}" name = "GenerateReport[clientName]">
					</div>
					<div class="input-field col s12 m4 offset-m4">
						<label for="address" class = "active">Event Address</label>
						<input id="address" type="text" value = "{$Address}" name = "GenerateReport[Address]">
					</div>
					
					<div class="input-field col s12 m4 offset-m4">
						<div class="input-field col s12 m12">
							<div class="row datepick1">
								<div class="input-field col s6">
									<label for="EventStartDate-1" class = "active">Event Start Date</label>
									<input id="EventStartDate-1" value = "{$EventStartDateShow}" type="text" placeholder = "MM-DD-YYYY" {$color}>
									<input id="EventStartDate1-1" type="hidden" name = "GenerateReport[EventStartDate]" value = "{$EventStartDateValue}">
								</div>
								<div class="input-field col s6">
									<label for="EventEndDate-1" class = "active">Event End Date</label>
									<input id="EventEndDate-1" type="text" value = "{$EventEndDateShow}" placeholder = "MM-DD-YYYY" {$color}>
									<input id="EventEndDate1-1" type="hidden" name = "GenerateReport[EventEndDate]" value = "{$EventEndDateValue}">
								</div>
							</div>
						</div>
					</div>
					
					<div class="input-field col s12 m4 offset-m4">
						<label for="version" class = "active">Version</label>
						<input id="version" type="text" name = "GenerateReport[Version]" value = "{$Version}" style = "color:#464444de;">
					</div>
					
					<div class="file-field input-field col s12 m4 offset-m4">
						<div class="btn teal lighten-1">
							<span>Upload Logo</span>
							<input type="file" accept="image/x-png,image/gif,image/jpeg" name = "LogoImage" title = "{$logo}">
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate" type="text" value = "{$logo}">
						</div>
					</div>
					
				</div>
			
HTML;

        echo json_encode($Report_html);
        die();
    }

    public function rangeMonth($datestr) {
        date_default_timezone_set(date_default_timezone_get());
        $dt = strtotime($datestr);
        return array(
            "start" => date('l m/d/Y', strtotime('first day of this month', $dt)),
            "end" => date('l m/d/Y', strtotime('last day of this month', $dt))
        );
    }

    public function rangeWeek($datestr) {
        date_default_timezone_set(date_default_timezone_get());
        $dt = strtotime($datestr);
        return array(
            "start" => date('N', $dt) == 1 ? date('l m/d/Y', $dt) : date('l m/d/Y', strtotime('last monday', $dt)),
            "end" => date('N', $dt) == 7 ? date('l m/d/Y', $dt) : date('l m/d/Y', strtotime('next sunday', $dt))
        );
    }

    public function getRestTimeline($projectId, $existTimeline, $start_date_range, $end_date_range) {

        $timeline = ItemTimeline::find()
                ->where(['ProjectId' => $projectId, 'IsActive' => '1'])
                ->asArray()
                ->all();

        $resp = [];

        foreach ($timeline as $key => $time) {

            if (strtotime($start_date_range) <= strtotime($time['StartDate']) && strtotime($time['StartDate']) <= strtotime($end_date_range)) {

                if (!in_array($time['ItemTimelineId'], $existTimeline)) {

                    $itemdesc = PageItem::find()
                            ->where(['ItemID' => $time['PageItemId'], 'IsActive' => '1'])
                            ->one();
                    if ($itemdesc) {
                        $timeline[$key]['Description'] = $itemdesc->Description;
                    }

                    $t_l_v['Day'] = date('D', strtotime($time['StartDate']));
                    $t_l_v['Date'] = date('l m/d/Y', strtotime($time['StartDate']));
                    $t_l_v['Activity'] = $timeline[$key]['Description'];
                    $t_l_v['StartTime'] = $time['StartTime'];
                    $t_l_v['EndTime'] = $time['EndTime'];
                    $t_l_v['StartDate'] = $time['StartDate'];
                    $t_l_v['EndDate'] = $time['EndDate'];
                    $t_l_v['Title'] = '';
                    $resp[] = $t_l_v;
                }
            }
        }

        return $resp;
    }

}
