<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;
use yii\widgets\LinkPager;
use yii\helpers\Url;

$user = Yii::$app->user->identity;
$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .modal-backdrop.in {
        position: fixed;
        z-index: 1002;
        top: -100px;
        left: 0;
        bottom: 0;
        right: 0;
        height: 125%;
        width: 100%;
        background: #000;
        display: block;
        opacity: 0.5;
        will-change: opacity;
    }
    button.close {
        float: right;
        width: 40px;
        margin: 10px;
        padding: 0px;
        font-size: 30px;
    }
</style>
<div id="tables">
    <table id = "current-project" class="responsive-table">
        <thead>
            <tr>
                <th data-field="firm">Firm name</th>
                <th data-field="name">Name</th>
                <th data-field="address">Address</th>
                <th data-field="contact">Contact No</th>
                <th data-field="email">Email</th>
                <?php if ($user->group_id != 4 && $user->group_id != 5) { ?>
                    <th data-field="actions">Actions</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody id="contacts_list">
            <?php
            if (@$contractors) {
                $i = 0;
                foreach ($contractors as $key => $size) {
                    ?>
                    <tr data-id = "<?= $size->id ?>">
                        <td class = ""><?= $size->firm ?></td>
                        <td class = ""><?= $size->name ?></td>
                        <td class = ""><?= $size->address ?></td>
                        <td class = ""><?= $size->contact ?></td>
                        <td class = ""><?= $size->email ?></td>
                        <?php if ($user->group_id != 4 && $user->group_id != 5) { ?>
                            <td>

                                <a onclick="pop_up('<?= Url::to(['contractor/add-contractor', 'id' => $size->id]) ?>');" class="waves-effect waves-light btn blue">Edit</a>


                                <a onclick="openmodal('modal<?= $size->id; ?>')" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>


                            </td>
                        <?php } ?>
                    </tr>
                <div id="modal<?= $size->id; ?>" class="modal">
                    <button data-dismiss="modal" class="close waves-effect waves-light btn red">×</button>
                    <div class="modal-content">
                        <h4>Confirmation Message</h4>
                        <p>Are you sure you want to delete it ?</p>
                    </div>
                    <div class="modal-footer">
                        <a data-dismiss="modal" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
                        <a href="<?= Url::to(['contractor/delete-contractor', 'id' => $size->id]) ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Yes</a>
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
<script>
    function openmodal(id) {
        $('#' + id + '').modal('toggle');
        $('#' + id + '').css('z-index', '1003');
        $('#' + id + '').css('opacity', '1');
        $('#' + id + '').css('transform', 'scaleX(1)');
        $('#' + id + '').css('top', '10%');
        $(".modalclose").css('position', 'fixed');
        $(".modalclose").css('z-index', '1');
        $(".modalclose").show();

    }

    function pop_up(url) {
        window.open(url, 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1076,height=768,directories=no,location=no')
    }
</script>