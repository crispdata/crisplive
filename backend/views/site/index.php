<?php
/* @var $this yii\web\View */

$this->title = 'Dashboard';
$user = Yii::$app->user->identity;
$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<main class="mn-inner">
    <div class="col s12 m12 l12">

        <div class = "row">
            <div class = "col s12 m6 l3">
                <div class="card white darken-1">
                    <div class="card-content">
                        <span class="card-title">Upcoming Projects</span>
                        <div class = "main-content">
                            <?php
                            if (@$topprojects) {
                                foreach ($topprojects as $key => $_project) {
                                    ?>
                                    <div class = "content-row">
                                        <a href="<?= $baseURL . 'site/project-details?project=' . $_project['ProjectId'] . '' ?>" class=""><?= $_project['EventName']; ?></a>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo "No Upcoming Projects";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class = "col s12 m6 l3">
                <div class="card white darken-1">
                    <div class="card-content">
                        <span class="card-title">Recent Changes</span>
                        <div class = "main-content">
                            <?php
                            if (@$recentchanges) {
                                foreach ($recentchanges as $key => $recentchange) {
                                    $str = '';
                                    $user = \common\models\User::find()->where(['UserId' => $recentchange->UserId])->one();

                                    $start = strtotime($recentchange->CreatedOn);
                                    $end = time();
                                    $diff = $end - $start;

                                    if ($recentchange->IsDismiss && ($diff > 3600)) {
                                        continue;
                                    }

                                    if (strlen($recentchange->Note) > 40) {
                                        $str = $recentchange->Note;
                                    } else {
                                        $str = $recentchange->Note;
                                    }
                                    ?>
                                    <div class = "content-row" data-id = "<?= $recentchange->ProjectId ?>" id = "tr-<?= $recentchange->RecentChangeId ?>"> 
                                        <a href="<?= $baseURL . 'site/project-details?project=' . $recentchange->ProjectId . '' ?>" class=""><?= ucfirst($user->username) . ': ' . $str; ?></a>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo "No Recent Changes";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        
                <div class = "col s12 m6 l3">
                    <div class="card white darken-1">
                        <div class="card-content">
                            <span class="card-title">Recent Messages</span>
                            <div class = "main-content">

                                <?php
                                
                                if ($contacts) {
                                    $i = 0;
                                    foreach ($contacts as $key => $contact) {
                                  
                                        $project = \common\models\Project::findOne($contact['ProjectId']);
                                       
                                        if ($project) {

                                            if ($contact['PageId'] == 0) {
                                                $page = \common\models\PageItem::findOne($contact['PageItemId']);
                                            } else {
                                                $page = common\models\Page::findOne($contact['PageId']);
                                            }
                                            

                                            if ($page) {

                                                $message = 'Message <b style="color:#000;">"' . $contact['Message'] . '"</b> in Thread <b style="color:#000;">"' . $contact['Title'] . '"</b>';
                                                ?>

                                                <div class = "content-row" data-id = "<?= $contact['ProjectId'] ?>">
                                                    <a href="<?= $baseURL . 'message/message-screen?project=' . $contact['ProjectId'] . '#messthrd'.$contact['MessageThreadId'].'' ?>" class=""><?= $message; ?></a>
                                                </div>
                                                <?php
                                                $i++;
                                            }
                                        }
                                    }
                                } else {
                                    echo "No Messages";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!--div class = "col s12 m6 l3">
                    <div class="card white darken-1">
                        <div class="card-content">
                            <span class="card-title">Manage Contacts</span>
                            <div class = "main-content">

                <?php /*
                  if ($contacts) {
                  $i = 0;
                  foreach ($contacts as $key => $contact) {
                  ?>

                  <div class = "content-row" data-id = "<?= $contact->UserId ?>">
                  <a href="<?= $baseURL . 'site/contact-details?contact=' . $contact->UserId . '' ?>" class=""><?= strlen($contact->FirstName) > 40 ? substr($contact->FirstName, 0, 40) . "..." : $contact->FirstName; ?></a>
                  </div>
                  <?php
                  $i++;
                  }
                  } else {
                  echo "No Contacts";
                  }
                 */ ?>
                            </div>
                        </div>
                    </div>
                </div-->
      
            <div class = "col s12 m6 l3">
                <div class="card white darken-1">
                    <div class="card-content">
                        <span class="card-title">To Do</span>
                        <div class = "main-content">

                            <?php
                            if (@$todos) {
                                foreach ($todos as $key => $recentchange) {
                                    $note = '';
                                    ?>

                                    <div class = "content-row" data-id = "<?= @$recentchange['page']['page']->PageID; ?>" id = "tr-<?= $recentchange[0]->ItemTodoId ?>">
                                        <?php
                                        if (strlen(@$recentchange[0]->Note) > 40) {
                                            $note = @$recentchange[0]->Note;
                                        } else {
                                            $note = @$recentchange[0]->Note;
                                        }
                                        ?>
                                        <a href="<?= $baseURL . 'page/page-details?page=' . @$recentchange['page']['page']->PageID . '' ?>" class=""><?= date_format(date_create($recentchange[0]->DueDate), "m/d/Y") . ': ' . $note ?></a>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo "No ToDos";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>