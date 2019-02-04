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

<script src="//code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<input type = "hidden" value = "<?= $baseURL ?>" id = "base_url">
<style>
    .header-title span{margin:8px 0;}
    .chapter-title img {
        width: 150px;
    }
    .header-title {
        float: left;
        width: 25%;
    }

    .modalclose {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1040;
    }
    form.left.search.col.s2.hide-on-small-and-down {
        float: left;
        margin-left: 150px;
    }
    a#sbutton {
        margin-left: 10px;
    }
    .fetchmess p {
        font-size: 35px;
        color: #00ACC1!important;
        margin-top: 25px!important;
    }
    span.fetchmess {
        text-align: center;
    }
    .select-wrapper span.caret{color:#000!important;}

</style>
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
                    <span class="chapter-title"><a href="<?= $baseURL; ?>"><img src='<?= $imageURL; ?>/images/clogo.png'></a></span>
                </div>
                <form id="searchform" class="left search col s2 hide-on-small-and-down">
                    <div class="input-field">
                        <input id="searchdata" type="search" placeholder="Search Tenders By Id" autocomplete="off">
                        <label for="searchdata"><i class="material-icons search-icon">search</i></label>

                    </div>


                    <a href="javascript: void(0)" class="close-search"><i class="material-icons">close</i></a>
                </form>
                <a class="btn green" id="sbutton">Search</a>
                <ul class="right col s7 m3 nav-right-menu setting">
                    <li><a href="javascript:void(0)" data-activates="chat-sidebar" class="chat-button show-on-large"><i class="material-icons">more_vert</i></a></li>
                    <?php
                    if ($user->Logo != '') {
                        $logo = $imageURL . $user->Logo;
                    } else {
                        $logo = $imageURL . 'assets/images/profile-image.png';
                    }
                    ?>
                    <li class="hide-on-small-and-down"><a href="javascript:void(0)" id="messagebuttons" class="dropdown-button dropdown-right show-on-large"><img src="<?= $logo; ?>" class="circle" alt=""><span class="badge"></span></a></li>
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

    <div id="searchresult">

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
                    <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'site' && ($action == 'tenders' || $action == 'technicaltenders' || $action == 'financialtenders' || $action == 'aoctenders' || $action == 'utenders' || $action == 'atenders')) ? 'active' : '' ?>"><i class="material-icons">assignment</i>Tenders<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                    <div class="collapsible-body">
                        <ul>
                            <li><a href="<?= $baseURL ?>site/tenders" class="<?= ($controller == 'site' && $action == 'tenders') ? 'active-page' : '' ?>">All</a></li>
                            <li><a href="<?= $baseURL ?>site/utenders" class="<?= ($controller == 'site' && $action == 'utenders') ? 'active-page' : '' ?>">Unapproved</a></li>
                            <li><a href="<?= $baseURL ?>site/atenders" class="<?= ($controller == 'site' && $action == 'atenders') ? 'active-page' : '' ?>">Approved</a></li>
                            <li><a href="<?= $baseURL ?>site/technicaltenders" class="<?= ($controller == 'site' && $action == 'technicaltenders') ? 'active-page' : '' ?>">Technical</a></li>
                            <li><a href="<?= $baseURL ?>site/financialtenders" class="<?= ($controller == 'site' && $action == 'financialtenders') ? 'active-page' : '' ?>">Financial</a></li>
                            <li><a href="<?= $baseURL ?>site/aoctenders" class="<?= ($controller == 'site' && $action == 'aoctenders') ? 'active-page' : '' ?>">AOC</a></li>
                        </ul>
                    </div>
                </li>
                <!--li class="no-padding <?= ($controller == 'site' && $action == 'upcomingtenders') ? 'active' : '' ?>">
                    <a class="waves-effect waves-grey" href="<?= $baseURL ?>site/upcomingtenders">
                        <i class="material-icons">assignment</i>
                        Upcoming Tenders
                    </a>
                </li>
                <li class="no-padding <?= ($controller == 'site' && $action == 'tenders') ? 'active' : '' ?>">
                    <a class="waves-effect waves-grey" href="<?= $baseURL ?>site/tenders">
                        <i class="material-icons">assignment</i>
                        All Tenders
                    </a>
                </li-->
                <li class="no-padding <?= ($controller == 'site' && $action == 'makes') ? 'active' : '' ?>">
                    <a class="waves-effect waves-grey" href="<?= $baseURL ?>site/makes">
                        <i class="material-icons">description</i>
                        Makes
                    </a>
                </li>
                <li class="no-padding <?= ($controller == 'site' && $action == 'sizes') ? 'active' : '' ?>">
                    <a class="waves-effect waves-grey" href="<?= $baseURL ?>site/sizes">
                        <i class="material-icons">description</i>
                        Sizes
                    </a>
                </li>
                <li class="no-padding <?= ($controller == 'site' && $action == 'fittings') ? 'active' : '' ?>">
                    <a class="waves-effect waves-grey" href="<?= $baseURL ?>site/fittings">
                        <i class="material-icons">description</i>
                        Fittings
                    </a>
                </li>

                <li class="no-padding <?= ($controller == 'contractor' && $action == 'index') ? 'active' : '' ?>">
                    <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'contractor' && ($action == 'index' || $action == 'allcontractors')) ? 'active' : '' ?>"><i class="material-icons">assignment</i>Contractors<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                    <div class="collapsible-body">
                        <ul>
                            <li><a href="<?= $baseURL ?>contractor/index" class="<?= ($controller == 'contractor' && $action == 'index') ? 'active-page' : '' ?>">Upload</a></li>
                            <li><a href="<?= $baseURL ?>contractor/allcontractors" class="<?= ($controller == 'contractor' && $action == 'allcontractors') ? 'active-page' : '' ?>">All Contractors</a></li>
                        </ul>
                    </div>
                </li>
                <?php if ($user->group_id != 3) { ?>
                    <li class="no-padding <?= ($controller == 'mail' && $action == 'index') ? 'active' : '' ?>">
                        <a class="waves-effect waves-grey" href="<?= $baseURL ?>mail/index">
                            <i class="material-icons">mail</i>
                            Mail
                        </a>
                    </li>
                <?php } ?>
                <?php if ($user->group_id == 1) { ?>
                    <li class="no-padding <?= ($controller == 'site' && $action == 'users') ? 'active' : '' ?>">
                        <a class="waves-effect waves-grey" href="<?= $baseURL ?>site/users">
                            <i class="material-icons">perm_identity</i>
                            Users
                        </a>
                    </li>
                <?php } ?>
                <?php if ($user->group_id != 3) { ?>
                   
                    <li class="no-padding <?= ($controller == 'site' && $action == 'clients') ? 'active' : '' ?>">
                        <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'site' && ($action == 'dealers' || $action == 'contractors' || $action == 'manufacturers')) ? 'active' : '' ?>"><i class="material-icons">perm_identity</i>Clients<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="<?= $baseURL ?>site/dealers" class="<?= ($controller == 'site' && $action == 'dealers') ? 'active-page' : '' ?>">Dealers</a></li>
                                <li><a href="<?= $baseURL ?>site/contractors" class="<?= ($controller == 'site' && $action == 'contractors') ? 'active-page' : '' ?>">Contractors</a></li>
                                <li><a href="<?= $baseURL ?>site/manufacturers" class="<?= ($controller == 'site' && $action == 'manufacturers') ? 'active-page' : '' ?>">Manufacturers</a></li>
                            </ul>
                        </div>
                    </li>
                <?php } ?>
<!--li class="no-padding <?= ($controller == 'site' && $action == 'items') ? 'active' : '' ?>">
<a class="waves-effect waves-grey" href="<?= $baseURL ?>site/items">
<i class="material-icons">assessment</i>
Items
</a>
</li-->

            </ul>
            <div class="footer">
                <p class="copyright">Crispdata</p>
                <a target="_blank" href="/index.php/site/privacy">Privacy</a> &amp; <a target="_blank" href="/index.php/site/terms">Terms</a>
            </div>
        </div>
    </aside>
    <div class="modalclose" style="display: none;"></div>
