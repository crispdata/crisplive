<?php
$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;

use yii\web\Controller;

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];

use yii\web\Session;
use common\models\Project;
use common\models\Page;
use common\models\Role;

$user = Yii::$app->user->identity;
$session = Yii::$app->session;
$projects = [];
$pids = [];
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . '' . $_SERVER['REQUEST_URI'];

if (($actual_link == $baseURL) || ((!isset($_GET['project'])) && (!isset($_GET['page'])))) {
    $session->remove('header_project');
} else {
    if (isset($_GET['project']) && $_GET['project'] != '') {
        $session['header_project'] = $_GET['project'];
    }
}

$project_dropdown_class = "";

if ($controller == 'site' && ( $action == 'current-projects' || $action == 'create-new-project' || $action == 'create-project-by-template' || $action == 'duplicate-project')) {
    $project_dropdown_class = 'active';
}

$setting_dropdown_class = "";

if (($controller == 'site' && ( $action == 'manage-templates' || $action == 'create-template' || $action == 'settings' || $action == 'set-dismiss-time' || $action == 'add-cable-length')) || ($action == 'all-cables')) {
    $setting_dropdown_class = 'active';
}


$pages = [];
$projectid = '';
$allroles = [];

?>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<input type = "hidden" value = "<?= $baseURL ?>" id = "base_url">
<div class="loader-bg"></div>
<div class="loader">
    <div class="preloader-wrapper big active">
        <div class="spinner-layer spinner-blue">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
        </div>
        <div class="spinner-layer spinner-teal lighten-1">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
        </div>
        <div class="spinner-layer spinner-yellow">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
        </div>
        <div class="spinner-layer spinner-green">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
        </div>
    </div>
</div>
<div class="mn-content fixed-sidebar">
    <header class="mn-header navbar-fixed">
        <nav class="cyan darken-1">
            <div class="nav-wrapper row">
                <section class="material-design-hamburger navigation-toggle">
                    <a href="javascript:void(0)" data-activates="slide-out" class="button-collapse show-on-large material-design-hamburger__icon">
                        <span class="material-design-hamburger__layer"></span>
                    </a>
                </section>
                <div class="header-title">      
                    <span class="chapter-title"><a href="<?= $baseURL; ?>">Crisp Data</a></span>
                </div>

              
                <ul class="right col s7 m3 nav-right-menu setting">
                    <li><a href="javascript:void(0)" data-activates="chat-sidebar" class="chat-button show-on-large"><i class="material-icons">more_vert</i></a></li>
                    <?php
                    if ($user->Logo != '') {
                        $logo = $imageURL . $user->Logo;
                    } else {
                        $logo = $imageURL . 'assets/images/profile-image.png';
                    }
                    ?>
                    <li class="hide-on-small-and-down"><a href="javascript:void(0)" data-activates="dropdown1" id="messagebutton" class="dropdown-button dropdown-right show-on-large"><img src="<?= $logo; ?>" class="circle" alt=""><span class="badge"></span></a></li>
                    <li class="hide-on-med-and-up"><a href="javascript:void(0)" class="search-toggle"><i class="material-icons">search</i></a></li>

                </ul>

                <ul id="dropdown1" class="dropdown-content notifications-dropdown">
                    <li class="notificatoins-dropdown-container">
                        <ul id="message-dropdown">



                        </ul>
                    </li>
                </ul>


            </div>
        </nav>
    </header>
    <div id="searchdata">

    </div>
    <aside id="chat-sidebar" class="side-nav white">
        <div class="side-nav-wrapper">
            <div id="sidebar-chat-tab" class="col s12 sidebar-messages right-sidebar-panel">
                <ul>
                    <li class="no-padding">
                        <a class="waves-effect waves-grey" href="<?= $baseURL . 'site/editprofile' ?>"><i class="material-icons">perm_identity</i>Profile</a>
                    </li>
                   
                    <li class="divider"></li>
                    <li class="no-padding">

                        <a href="javascript:{}" onclick="document.getElementById('logout-btn').submit();" class="waves-effect waves-grey"><i class="material-icons">exit_to_app</i>Sign Out</a>

                    </li>
                </ul>

            </div>
        </div>
    </aside>

    <form method="post" action = "<?= $baseURL ?>site/logout" id = "logout-btn">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
    </form>

    <aside id="slide-out" class="side-nav white fixed">
        <div class="side-nav-wrapper">

            <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
                <li class="no-padding <?= ($controller == 'site' && $action == 'index') ? 'active' : '' ?>">
                    <a class="waves-effect waves-grey" href="<?= $baseURL ?>">
                        <i class="material-icons">dashboard</i>
                        Dashboard
                    </a>
                </li>
                <li class="no-padding <?= ($controller == 'site' && $action == 'tenders') ? 'active' : '' ?>">
                    <a class="waves-effect waves-grey" href="<?= $baseURL ?>site/tenders">
                        <i class="material-icons">dashboard</i>
                        Tenders
                    </a>
                </li>
                <?php if ($action == 'project-details' || $action == 'page-details' || $action == 'create-page-item' || $action == 'message-screen' || $action == 'documents' || $action == 'timeline' || $action == 'addtimeline' || $action == 'create-thread' || $action == 'generate-report' || $action == 'drawing-tool' || $action == 'add-page' || $action == 'createsheetsort' || $action == 'requests' || $action == 'update-project' || ($controller == 'tool' && $action == 'index') || ($controller == 'tool' && $action == 'all-access') || ($controller == 'tool' && $action == 'assign-diagram')) { ?>
                    <li class="no-padding <?= ($action == 'project-details') ? 'active' : '' ?>">
                        <a class="waves-effect waves-grey " href="<?= $baseURL ?>site/project-details?project=<?= @$projectid; ?>">
                            <i class="material-icons">details</i>
                            General Details
                        </a>
                    </li>
                    <li class="no-padding <?= ($action == 'page-details' || $action == 'add-page') ? 'active' : '' ?>">
                        <a class="collapsible-header waves-effect waves-grey <?= (isset($_GET['page']) || (isset($_GET['project']) && $action == 'add-page')) ? 'active' : '' ?>">
                            <i class="material-icons">pages</i>
                            Pages
                            <i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                        </a>
                        <div class="collapsible-body">
                            <ul>
                                <?php foreach ($pages as $page) { ?>
                                    <li><a <?= (isset($_GET['page']) && ($_GET['page'] == $page->PageID)) ? 'class="active-page"' : '' ?> href="<?= $baseURL ?>page/page-details?page=<?= $page->PageID; ?>"><?= $page->PageName; ?></a></li>
                                <?php } ?>
                                <li>
                                    <a class="<?= ($action == 'add-page') ? 'active-page' : '' ?>" href="<?= $baseURL ?>page/add-page?project=<?= @$projectid; ?>">
                                        Add New Page
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="no-padding <?= ($action == 'timeline') ? 'active' : '' ?>">
                        <a class="waves-effect waves-grey " href="<?= $baseURL ?>timeline/timeline?project=<?= @$projectid; ?>">
                            <i class="material-icons">timeline</i>
                            Timeline
                        </a>
                    </li>
                    <li class="no-padding <?= ($action == 'message-screen') ? 'active' : '' ?>">
                        <a class="waves-effect waves-grey " href="<?= $baseURL ?>message/message-screen?project=<?= @$projectid; ?>">
                            <i class="material-icons">message</i>
                            Messages
                        </a>
                    </li>
                    <li class="no-padding <?= ($action == 'documents') ? 'active' : '' ?>">
                        <a class="waves-effect waves-grey " href="<?= $baseURL ?>documents/documents?project=<?= @$projectid; ?>">
                            <i class="material-icons">file_copy</i>
                            Files
                        </a>
                    </li>
                <?php }?>

                   



               
            </ul>
            <div class="footer">
                <p class="copyright">Crisp data</p>
                <a href="<?= $baseURL ?>site/privacy">Privacy</a> &amp; <a href="<?= $baseURL ?>site/terms">Terms</a>
            </div>
        </div>
    </aside>
   
   