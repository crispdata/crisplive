<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$this->title = 'View Items';
$user = Yii::$app->user->identity;
$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
$stop_date = date('Y-m-d H:i:s', strtotime(@$tdetails->createdon . ' +1 day'));
?>
<style>
    .add-contact{    float: right;
                     margin-right: 15px;}    
    span.viewmake {
        background-color: #E4E4E4;
        border-radius: 15px;
        padding: 10px;
        margin-bottom: 5px;
        width: 80%;
        float: left;
    }
    #return-to-top,#return-to-bottom {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: rgb(0, 0, 0);
        background: rgba(0, 0, 0, 0.7);
        width: 50px;
        height: 50px;
        z-index:1111111;
        display: block;
        text-decoration: none;
        -webkit-border-radius: 35px;
        -moz-border-radius: 35px;
        border-radius: 35px;
        display: none;
        -webkit-transition: all 0.3s linear;
        -moz-transition: all 0.3s ease;
        -ms-transition: all 0.3s ease;
        -o-transition: all 0.3s ease;
        transition: all 0.3s ease;
    }
    #return-to-top i {
        color: #fff;
        margin: 0;
        position: relative;
        left: 16px;
        top: 13px;
        font-size: 19px;
        -webkit-transition: all 0.3s ease;
        -moz-transition: all 0.3s ease;
        -ms-transition: all 0.3s ease;
        -o-transition: all 0.3s ease;
        transition: all 0.3s ease;
    }
    #return-to-bottom i {
        color: #fff;
        margin: 0;
        position: relative;
        left: 16px;
        top: 13px;
        font-size: 19px;
        -webkit-transition: all 0.3s ease;
        -moz-transition: all 0.3s ease;
        -ms-transition: all 0.3s ease;
        -o-transition: all 0.3s ease;
        transition: all 0.3s ease;
    }
    #return-to-top:hover,#return-to-bottom:hover {
        background: rgba(0, 0, 0, 0.9);
    }
    #return-to-top:hover i,#return-to-bottom:hover i {
        color: #fff;
        top: 5px;
    }
    .singlemake {
        float: right;
        cursor: pointer;
    }
    .singlemake img{width:15px;}
</style>
<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
<script>
// ===== Scroll to Top ==== 
    $(window).scroll(function () {
        if ($(this).scrollTop() >= 50) {        // If page is scrolled more than 50px
            $('#return-to-top').fadeIn(200);    // Fade in the arrow
        } else {
            $('#return-to-top').fadeOut(200);   // Else fade out the arrow
        }
    });
    $('#return-to-top').click(function () {      // When arrow is clicked
        $('body,html').animate({
            scrollTop: 0                       // Scroll to top of body
        }, 500);
    });

    $(window).load(function () {
        $('#return-to-bottom').fadeIn(200);
    });

    $(window).scroll(function () {
        if ($(this).scrollTop() >= 50) {        // If page is scrolled more than 50px
            $('#return-to-bottom').fadeOut(200);    // Fade in the arrow
        } else {
            $('#return-to-bottom').fadeIn(200);   // Else fade out the arrow
        }
    });
    $('#return-to-bottom').click(function () {      // When arrow is clicked
        $('body,html').animate({
            scrollTop: $(document).height()                       // Scroll to top of body
        }, 500);
    });
</script>
<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">View Items of <?= $tname; ?></div>
        </div>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <script>
                swal({
                    title: "<?= Yii::$app->session->getFlash('success'); ?>",
                    timer: 2000,
                    type: "success",
                    showConfirmButton: false
                });
                //sweetAlert('Success', '<?= Yii::$app->session->getFlash('success'); ?>', 'success');
            </script>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger">
                <?= Yii::$app->session->getFlash('error'); ?>
            </div>
        <?php endif; ?>

        <form id="create-item" method = "post" onsubmit="return deleteConfirm();" action = "<?= $baseURL ?>site/delete-items">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
            <input type="hidden" name="tid" value="<?php echo $tid; ?>">
            <?php
            if ($user->group_id == 3) {
                if ($stop_date >= date('Y-m-d H:i:s') && $tdetails->status == 0) {
                    ?>
                    <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= Url::to(['site/create-item', 'id' => $tid]) ?>"> Add Item</a>
                    <input type="submit" class="waves-effect waves-light btn blue m-b-xs add-contact" name="btn_delete" value="Delete Items"/>
                    <?php
                }
            } else {
                if ($user->group_id != 4 && $user->group_id != 5 && $user->group_id != 6) {
                    ?>
                    <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= Url::to(['site/create-item', 'id' => $tid]) ?>"> Add Item</a>
                    <input type="submit" class="waves-effect waves-light btn blue m-b-xs add-contact" name="btn_delete" value="Delete Items"/>
                    <input type="submit" class="waves-effect waves-light btn blue m-b-xs add-contact" name="btn_approve" value="Approve Items"/>
                    <?php
                }
            }
            ?>




            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">
                        <table id = "view-items" class="responsive-table">
                            <thead>
                                <tr>
                                    <?php if ($user->group_id != 4 && $user->group_id != 5 && $user->group_id != 6) { ?><th><input type="checkbox" name="check_all" id="check_all" value=""/><label for="check_all"></label></th><?php } ?>
                                    <th data-field="name">Sr. No.</th>
                                    <th data-field="name">Item Sr. No. of Tender</th>
                                    <th data-field="name" width="200px">Item Description</th>
                                    <th data-field="name">Units</th>
                                    <th data-field="email">Quantity</th>
                                    <th data-field="email" width="300px">Make</th>
                                    <?php
                                    if ($user->group_id == 9) {
                                        if ($stop_date >= date('Y-m-d H:i:s') && $tdetails->status == 0) {
                                            ?>
                                            <th data-field="email">Actions</th>
                                            <?php
                                        }
                                    } else {
                                        if ($user->group_id != 4 && $user->group_id != 5 && $user->group_id != 6) {
                                            ?>
                                            <th data-field="email">Actions</th>
                                            <?php
                                        }
                                    }
                                    ?>

                                </tr>
                            </thead>
                            <tbody id="contacts_list">
                                <?php
                                if (@$idetails) {
                                    $i = 0;
                                    foreach ($idetails as $key => $idetail) {
                                        ?>
                                        <tr data-id = "<?= $idetail->id ?>">
                                            <?php if ($user->group_id != 4 && $user->group_id != 5 && $user->group_id != 6) { ?><td align="center"><input type="checkbox" name="selected_id[]" <?= ($idetail->status == 1) ? 'disabled' : '' ?> class="checkbox" id="check<?php echo $idetail->id; ?>" value="<?php echo $idetail->id; ?>"/><label for="check<?php echo $idetail->id; ?>"></label></td><?php } ?> 
                                            <td class = ""><?= $key + 1 ?></td>
                                            <td class = ""><?= ($idetail->itemtender) ? $idetail->itemtender : '---' ?></td>
                                            <td class = ""><?= $idetail->description ?></td>
                                            <td class = ""><?= ($idetail->units) ? $idetail->units : '---' ?></td>
                                            <td class = ""><?= ($idetail->quantity) ? $idetail->quantity : '---' ?></td>
                                            <td class = ""><?= $idetail->make ?></td>

                                            <?php
                                            if ($user->group_id == 9) {
                                                if ($stop_date >= date('Y-m-d H:i:s') && $tdetails->status == 0) {
                                                    ?>
                                                    <td>
                                                        <a href="<?= Url::to(['site/edit-item', 'id' => $idetail->id]) ?>" class="waves-effect waves-light btn blue">Edit</a>
                                                        <a href="#modal<?= $idetail->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>
                                                    </td>
                                                    <?php
                                                }
                                            } else {
                                                if ($user->group_id != 4 && $user->group_id != 5 && $user->group_id != 6) {
                                                    ?>

                                                    <td><?php
                                                        if ($user->group_id != 3) {
                                                            if ($idetail->status == 1) {
                                                                ?>
                                                                <a class="waves-effect waves-light btn green">Approved</a>
                                                            <?php } else { ?>
                                                                <a class="waves-effect waves-light btn blue" onclick='approveitem(<?php echo $idetail->id; ?>)'>Approve</a>
                                                                <?php
                                                            }
                                                        }
                                                        ?>

                                                        <a href="<?= Url::to(['site/edit-item', 'id' => $idetail->id]) ?>" class="waves-effect waves-light btn blue">Edit</a>
                                                        <a href="#modal<?= $idetail->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>

                                                    </td>                               
                                                    <?php
                                                }
                                            }
                                            ?>

                                        </tr>
                                    <div id="modal<?= $idetail->id; ?>" class="modal">
                                        <div class="modal-content">
                                            <h4>Confirmation Message</h4>
                                            <p>Are you sure you want to delete it ?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                                            <a href="<?= Url::to(['site/delete-item', 'id' => $idetail->id, 'tid' => $tid]) ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
                                        </div>
                                    </div>

                                    <?php
                                    $i++;
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<a href="javascript:" id="return-to-top"><i class="icon-chevron-up"></i></a>
<a href="javascript:" id="return-to-bottom"><i class="icon-chevron-down"></i></a>