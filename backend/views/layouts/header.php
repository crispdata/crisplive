<?php
$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;

use yii\web\Controller;

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
$URL = Yii::$app->params['URL'];

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


$total = \common\models\Tender::find()->orderBy(['id' => SORT_DESC])->count();
$atenders = \common\models\Tender::find()->where(['status' => 1, 'is_archived' => null])->orderBy(['id' => SORT_DESC])->count();
$aoctenders = \common\models\Tender::find()->where(['aoc_status' => 1])->orderBy(['id' => SORT_DESC])->count();
$approvedtenders = \common\models\Tender::find()->where(['status' => 1, 'aoc_status' => null])->orderBy(['id' => SORT_DESC])->count();
$unapprovedtenders = \common\models\Tender::find()->where(['status' => 0])->orderBy(['id' => SORT_DESC])->count();
$archivetenders = \common\models\Tender::find()->where(['aoc_status' => 1, 'is_archived' => 1])->orderBy(['id' => SORT_DESC])->count();
$aocready = \common\models\Tender::find()->where(['on_hold' => null, 'aoc_status' => 1, 'is_archived' => null])->orderBy(['id' => SORT_DESC])->count();
$aochold = \common\models\Tender::find()->where(['on_hold' => 1, 'aoc_status' => 1, 'is_archived' => null])->orderBy(['id' => SORT_DESC])->count();
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
        margin-left: 200px;
    }
    a#sbutton {
        margin-left: 10px;
    }
    .card {
        float: left;
        width: 100%;
    }
    .fetchmess p {
        font-size: 35px;
        color: #00ACC1!important;
        margin-top: 25px!important;
    }
    span.fetchmess {
        text-align: center;
        display: table-cell;
        vertical-align: middle;
        width: 100%;
        height: 200px;
        margin-top: 50px;
        float: left;
    }
    .select-wrapper span.caret{color:#000!important;}
    .notender {
        text-align: center;
        font-size: 30px;
    }
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
                <div class="header-title col s3 m3">      
                    <span class="chapter-title"><a href="<?= $baseURL; ?>"><img src='<?= $imageURL; ?>/images/clogo.png'></a></span>
                </div>
                <form id="searchform" class="left search col s2 hide-on-small-and-down">
                    <div class="input-field">
                        <input id="searchdata" type="search" placeholder="Search Tender By Id" autocomplete="off">
                        <label for="searchdata"><i class="material-icons search-icon">search</i></label>
                    </div>
                    <a href="javascript: void(0)" class="close-search"><i class="material-icons">close</i></a>
                </form>
                <!--a class="btn green" id="sbutton">Search</a-->
                <ul class="right col s7 m3 nav-right-menu setting">
                    <li><a href="javascript:void(0)" data-activates="chat-sidebar" class="chat-button show-on-large"><i class="material-icons">more_vert</i></a></li>
                    <?php
                    if ($user->Logo != '') {
                        $logo = $imageURL . $user->Logo;
                    } else {
                        $logo = $imageURL . 'assets/images/profile-image.png';
                    }
                    ?>
                    <!--li class="hide-on-small-and-down"><a href="javascript:void(0)" id="messagebuttons" class="dropdown-button dropdown-right show-on-large"><img src="<?= $logo; ?>" class="circle" alt=""><span class="badge"></span></a></li-->
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
            <div class="sidebar-profile">
                <div class="sidebar-profile-image">
                    <?php
                    if ($user->Logo != '') {
                        $logo = $imageURL . $user->Logo;
                    } else {
                        $logo = $imageURL . 'assets/images/profile-image.png';
                    }
                    ?>
                    <img src="<?= $logo; ?>" class="circle" alt="">
                </div>
                <div class="sidebar-profile-info">
                    <a>
                        <p><?= ucfirst($user->name); ?></p>
                        <span><?= $user->email ?></span>
                    </a>
                </div>
            </div>
            <?php if ($user->group_id == 4) { ?>
                <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
                    <li class="no-padding <?= ($controller == 'site' && $action == 'index') ? 'active' : '' ?>">
                        <a class="waves-effect waves-grey" href="/">
                            <i class="material-icons">dashboard</i>
                            Dashboard
                        </a>
                    </li>
                    <li class="no-padding <?= ($controller == 'site' && $action == 'approvedtenders') ? 'active' : '' ?>">
                        <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'site' && ($action == 'approvedtenders' || $action == 'archivetenders' || $action == 'searchtenders')) ? 'active' : '' ?>"><i class="material-icons">assignment</i>Tenders (<?= $atenders + $archivetenders ?>)<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="/site/approvedtenders" class="<?= ($controller == 'site' && $action == 'approvedtenders') ? 'active-page' : '' ?>">Approved (<?= $atenders; ?>)</a></li>
                                <li><a href="/site/archivetenders" class="<?= ($controller == 'site' && $action == 'archivetenders') ? 'active-page' : '' ?>">Archived (<?= $archivetenders; ?>)</a></li>
                                <li><a href="/site/searchtenders" class="<?= ($controller == 'site' && $action == 'searchtenders') ? 'active-page' : '' ?>">Search By Make</a></li>
                            </ul>
                        </div>
                    </li>

                </ul>
            <?php } elseif ($user->group_id == 5) { ?>
                <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
                    <li class="no-padding <?= ($controller == 'site' && $action == 'index') ? 'active' : '' ?>">
                        <a class="waves-effect waves-grey" href="/">
                            <i class="material-icons">dashboard</i>
                            Dashboard
                        </a>
                    </li>
                    <li class="no-padding <?= ($controller == 'site' && $action == 'approvedtenders') ? 'active' : '' ?>">
                        <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'site' && ($action == 'archivetenders' || $action == 'approvedtenders')) ? 'active' : '' ?>"><i class="material-icons">assignment</i>Tenders (<?= $atenders + $archivetenders ?>)<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="/site/approvedtenders" class="<?= ($controller == 'site' && $action == 'approvedtenders') ? 'active-page' : '' ?>">Approved (<?= $atenders; ?>)</a></li>
                                <li><a href="/site/archivetenders" class="<?= ($controller == 'site' && $action == 'archivetenders') ? 'active-page' : '' ?>">Archived (<?= $archivetenders; ?>)</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="no-padding <?= ($controller == 'site' && $action == 'makes') ? 'active' : '' ?>">
                        <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'site' && ($action == 'e-m' || $action == 'civil')) ? 'active' : '' ?>"><i class="material-icons">store</i>Makes<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="/site/e-m" class="<?= ($controller == 'site' && $action == 'e-m') ? 'active-page' : '' ?>">E/M</a></li>
                                <li><a href="/site/civil" class="<?= ($controller == 'site' && $action == 'civil') ? 'active-page' : '' ?>">Civil</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="no-padding <?= ($controller == 'contractor' && $action == 'index') ? 'active' : '' ?>">
                        <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'contractor' && ($action == 'index' || $action == 'allcontractors')) ? 'active' : '' ?>"><i class="material-icons">perm_identity</i>Contractors<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="/contractor/allcontractors" class="<?= ($controller == 'contractor' && $action == 'allcontractors') ? 'active-page' : '' ?>">All Contractors</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="no-padding <?= ($controller == 'search' && $action == 'index') ? 'active' : '' ?>">
                        <a class="waves-effect waves-grey" href="/search/index">
                            <i class="material-icons">search</i>
                            Advanced search
                        </a>
                    </li>

                </ul>
            <?php } else { ?>
                <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
                    <li class="no-padding <?= ($controller == 'site' && $action == 'index') ? 'active' : '' ?>">
                        <a class="waves-effect waves-grey" href="/">
                            <i class="material-icons">dashboard</i>
                            Dashboard
                        </a>
                    </li>
                    <li class="no-padding <?= ($controller == 'site' && $action == 'tenders') ? 'active' : '' ?>">
                        <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'site' && ($action == 'tenders' || $action == 'create-tender' || $action == 'aoctenders' || $action == 'utenders' || $action == 'atenders' || $action == 'archivetenders' || $action == 'aocready' || $action == 'aochold' || $action == 'movetoarchive')) ? 'active' : '' ?>"><i class="material-icons">assignment</i>Tenders (<?= $total; ?>)<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <!--li><a href="/site/tenders" class="<?= ($controller == 'site' && $action == 'tenders') ? 'active-page' : '' ?>">All (<?= $total; ?>)</a></li-->
                                <li><a href="/site/create-tender" class="<?= ($controller == 'site' && $action == 'create-tender') ? 'active-page' : '' ?>">Add New Tender</a></li>
                                <li><a href="/site/atenders" class="<?= ($controller == 'site' && $action == 'atenders') ? 'active-page' : '' ?>">Approved (<?= $approvedtenders; ?>)</a></li>
                                <li><a href="/site/utenders" class="<?= ($controller == 'site' && $action == 'utenders') ? 'active-page' : '' ?>">Unapproved (<?= $unapprovedtenders; ?>)</a></li>
                                <?php /* <li><a href="/site/technicaltenders" class="<?= ($controller == 'site' && $action == 'technicaltenders') ? 'active-page' : '' ?>">Technical (<?= $techtenders; ?>)</a></li>
                                  <li><a href="/site/financialtenders" class="<?= ($controller == 'site' && $action == 'financialtenders') ? 'active-page' : '' ?>">Financial (<?= $fintenders; ?>)</a></li> */ ?>
                                <li><a href="/site/aoctenders" class="<?= ($controller == 'site' && $action == 'aoctenders') ? 'active-page' : '' ?>">AOC (<?= $aoctenders; ?>)</a></li>
                                <li><a href="/site/aocready" class="<?= ($controller == 'site' && $action == 'aocready') ? 'active-page' : '' ?>">AOC Ready (<?= $aocready; ?>)</a></li>
                                <li><a href="/site/aochold" class="<?= ($controller == 'site' && $action == 'aochold') ? 'active-page' : '' ?>">AOC OnHold (<?= $aochold; ?>)</a></li>
                                <li><a href="/site/archivetenders" class="<?= ($controller == 'site' && $action == 'archivetenders') ? 'active-page' : '' ?>">Archived (<?= $archivetenders; ?>)</a></li>
                                <li><a href="/site/movetoarchive" class="<?= ($controller == 'site' && $action == 'movetoarchive') ? 'active-page' : '' ?>">Move To Archive</a></li>
                            </ul>
                        </div>
                    </li>
                    <?php if ($user->group_id != 3) { ?>
                        <li class="no-padding <?= ($controller == 'search' && $action == 'stats') ? 'active' : '' ?>">
                            <a class="waves-effect waves-grey" href="/search/stats">
                                <i class="material-icons">graphic_eq</i>
                                Tender stats
                            </a>
                        </li>
                    <?php } ?>
                    <li class="no-padding <?= ($controller == 'site' && $action == 'makes') ? 'active' : '' ?>">
                        <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'site' && ($action == 'e-m' || $action == 'civil')) ? 'active' : '' ?>"><i class="material-icons">store</i>Makes<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="/site/e-m" class="<?= ($controller == 'site' && $action == 'e-m') ? 'active-page' : '' ?>">E/M</a></li>
                                <li><a href="/site/civil" class="<?= ($controller == 'site' && $action == 'civil') ? 'active-page' : '' ?>">Civil</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="no-padding">
                        <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'site' && ($action == 'sizes' || $action == 'fittings') || $controller == 'products' && ($action == 'prices' || $action == 'accessories')) ? 'active' : '' ?>"><i class="material-icons">build</i>Properties<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="/site/sizes" class="<?= ($controller == 'site' && $action == 'sizes') ? 'active-page' : '' ?>">Sizes</a></li>
                                <li><a href="/site/fittings" class="<?= ($controller == 'site' && $action == 'fittings') ? 'active-page' : '' ?>">Fittings</a></li>
                                <li><a href="/products/accessories" class="<?= ($controller == 'products' && $action == 'accessories') ? 'active-page' : '' ?>">Accessories</a></li>
                                <li><a href="/products/prices" class="<?= ($controller == 'products' && $action == 'prices') ? 'active-page' : '' ?>">Prices</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="no-padding <?= ($controller == 'contractor' && $action == 'index') ? 'active' : '' ?>">
                        <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'contractor' && ($action == 'index' || $action == 'allcontractors')) ? 'active' : '' ?>"><i class="material-icons">perm_identity</i>Contractors<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="/contractor/index" class="<?= ($controller == 'contractor' && $action == 'index') ? 'active-page' : '' ?>">Upload</a></li>
                                <li><a href="/contractor/allcontractors" class="<?= ($controller == 'contractor' && $action == 'allcontractors') ? 'active-page' : '' ?>">All Contractors</a></li>
                            </ul>
                        </div>
                    </li>
                    <?php if ($user->group_id != 3) { ?>

                        <li class="no-padding <?= ($controller == 'site' && $action == 'clients') ? 'active' : '' ?>">
                            <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'site' && ($action == 'dealers' || $action == 'contractors' || $action == 'manufacturers')) ? 'active' : '' ?>"><i class="material-icons">perm_identity</i>Clients<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                            <div class="collapsible-body">
                                <ul>
                                    <li><a href="/site/dealers" class="<?= ($controller == 'site' && $action == 'dealers') ? 'active-page' : '' ?>">Dealers</a></li>
                                    <li><a href="/site/contractors" class="<?= ($controller == 'site' && $action == 'contractors') ? 'active-page' : '' ?>">Contractors</a></li>
                                    <li><a href="/site/manufacturers" class="<?= ($controller == 'site' && $action == 'manufacturers') ? 'active-page' : '' ?>">Manufacturers</a></li>
                                </ul>
                            </div>
                        </li>
                    <?php } ?>
                    <?php if ($user->group_id != 3) { ?>
                        <li class="no-padding <?= ($controller == 'mail' && $action == 'index') ? 'active' : '' ?>">
                            <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'mail' && ($action == 'index' || $action == 'mlogs' || $action == 'data' || $action == 'clogs')) ? 'active' : '' ?>"><i class="material-icons">mail</i>Mail<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                            <div class="collapsible-body">
                                <ul>
                                    <li><a href="/mail/index" class="<?= ($controller == 'mail' && $action == 'index') ? 'active-page' : '' ?>">Send Mail</a></li>
                                    <li><a href="/mail/clogs" class="<?= ($controller == 'mail' && $action == 'clogs') ? 'active-page' : '' ?>">Clients Logs</a></li>
                                    <li><a href="/mail/mlogs" class="<?= ($controller == 'mail' && $action == 'mlogs') ? 'active-page' : '' ?>">Manufacturers Logs</a></li>
                                    <!--li><a href="/mail/data" class="<?= ($controller == 'mail' && $action == 'data') ? 'active-page' : '' ?>">Get Data</a></li-->
                                </ul>
                            </div>
                        </li>
                        <li class="no-padding <?= ($controller == 'products' && $action == 'files') ? 'active' : '' ?>">
                            <a class="collapsible-header waves-effect waves-grey <?= ($controller == 'products' && ($action == 'files' || $action == 'uploadfile')) ? 'active' : '' ?>"><i class="material-icons">file_copy</i>Files<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                            <div class="collapsible-body">
                                <ul>
                                    <li><a href="/products/uploadfile" class="<?= ($controller == 'products' && $action == 'uploadfile') ? 'active-page' : '' ?>">Upload New File</a></li>
                                    <li><a href="/products/files" class="<?= ($controller == 'products' && $action == 'files') ? 'active-page' : '' ?>">All Files</a></li>
                                </ul>
                            </div>
                        </li>
                    <?php } ?>
                    <?php if ($user->group_id == 1) { ?>
                        <li class="no-padding <?= ($controller == 'site' && $action == 'users') ? 'active' : '' ?>">
                            <a class="waves-effect waves-grey" href="/site/users">
                                <i class="material-icons">perm_identity</i>
                                Users
                            </a>
                        </li>
                    <?php } ?>
                    <li class="no-padding <?= ($controller == 'search' && $action == 'index') ? 'active' : '' ?>">
                        <a class="waves-effect waves-grey" href="/search/index">
                            <i class="material-icons">search</i>
                            Advanced search
                        </a>
                    </li>

                </ul>
            <?php } ?>

            <div class="footer">
                <p class="copyright">Crispdata ©</p>
                <a target="_blank" href="<?= $URL ?>site/privacy">Privacy</a> &amp; <a target="_blank" href="<?= $URL ?>site/terms">Terms</a>
            </div>
        </div>
    </aside>

