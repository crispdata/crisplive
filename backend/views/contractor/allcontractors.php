<?php
/* @var $this yii\web\View */

use yii\widgets\LinkPager;
use yii\helpers\Url;

$this->title = 'Manage Contractors';

$baseURL = Yii::$app->params['BASE_URL'];
$imageURL = Yii::$app->params['IMAGE_URL'];
?>
<style>
    .add-contact{    float: right;
                     margin-right: 15px;}    
    .select-wrapper input.select-dropdown, .select-wrapper input.select-dropdown:disabled{border-color: unset;}
    ul.pagination {
        float: right;
    }
    .card-contractors{padding-bottom: 60px!important;}
    .pagination li.active {
        background-color: #2196F3!important;
    }
    span.totalcon {
        float: left;
        width: 50%;
        margin-top: 20px;
    }
    form#sort-data {
        width: 55%;
        z-index: 100000000;
    }
    .col.s4.searchfield {
        float: left;
        margin-left: 185px;
        height:0px;
    }
    ::placeholder {
        color: rgba(0,0,0,.6);
        opacity: 1; /* Firefox */
    }
</style>

<main class="mn-inner">
    <div class="row">
        <div class="col s6">
            <div class="page-title">Manage Contractors</div>
        </div>
        <form id="sort-data" method = "post" action = "<?= $baseURL ?>contractor/allcontractors">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
            <input type="hidden" name="page" value="<?= @$_GET['page'] ?>">
            <div class="col s2">
                <select class="validate required" name="sort" onchange="submitform()" id="sort">
                    <option value="10" <?= (@$_GET['filter'] == 10) ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= (@$_GET['filter'] == 25) ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= (@$_GET['filter'] == 50) ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= (@$_GET['filter'] == 100) ? 'selected' : '' ?>>100</option>
                </select>
            </div>
        </form>
        <form id="conform">
            <div class="col s4 searchfield">
                <input type="text" name="keyword" value="" id="consearch" placeholder="Search contractors" autocomplete="off">
                <label for="consearch"><i class="material-icons search-icon">search</i></label>
            </div>
        </form>

        <a class="waves-effect waves-light btn blue m-b-xs add-contact" href="<?= $baseURL ?>contractor/add-contractor"> Add Contractor</a>
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


        <div class="col s12 m12 l12">
            <div class="card">
                <div class="card-content card-contractors">

                    <div id="tables">
                        <table class="responsive-table bordered">
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

                                                <a href="<?= Url::to(['contractor/add-contractor', 'id' => $size->id]) ?>" class="waves-effect waves-light btn blue">Edit</a>


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
                        <span class="totalcon">All Contractors: <?= $total; ?></span>
                        <?php
                        echo LinkPager::widget([
                            'pagination' => $pages,
                        ]);
                        ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>
