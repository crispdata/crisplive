<?php
/* @var $this yii\web\View */

use backend\controllers\SiteController;
use yii\widgets\LinkPager;
use yii\helpers\Url;

$user = Yii::$app->user->identity;
$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<div id="tables">
    <table id = "current-project" class="responsive-table">
        <thead>
            <tr>
                <th data-field="firm">Firm name</th>
                <th data-field="name">Name</th>
                <th data-field="address">Address</th>
                <th data-field="contact">Contact No</th>
                <th data-field="email">Email</th>
                <th data-field="actions">Actions</th>

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
                        <td>

                            <a onclick="pop_up('<?= Url::to(['contractor/add-contractor', 'id' => $size->id]) ?>');" class="waves-effect waves-light btn blue">Edit</a>


                            <a href="#modal<?= $size->id; ?>" class="waves-effect waves-light btn blue modal-trigger proj-delete">Delete</a>


                        </td>

                    </tr>
                <div id="modal<?= $size->id; ?>" class="modal">
                    <div class="modal-content">
                        <h4>Confirmation Message</h4>
                        <p>Are you sure you want to delete it ?</p>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript::void()" class=" modal-action modal-close waves-effect waves-green btn-flat">No</a>
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
    function pop_up(url) {
        window.open(url, 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1076,height=768,directories=no,location=no')
    }
</script>