<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
?>

<!-- BODY -->
<table class="body-wrap" style="width: 100%;font-family: arial;">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF" style="display:block!important;max-width:600px!important; margin:0 auto!important; /* makes it centered */clear:both!important;">
            <div class="content" style="padding:15px;max-width:600px;margin:0 auto;	display:block; ">
                <?php foreach ($pages as $pagKey => $page) { ?>
                    <table style="width: 100%;font-family: arial; line-height: 30px;">
                        <tr>
                            <td>
                                <h3 style="line-height: 1.1; margin-bottom:25px; font-weight:500; font-size: 23px;font-family: arial;color: #000;"><?= $page->PageName; ?></h3>
                                <table style="width: 100%;font-family: arial; border-collapse: collapse; border: 1px solid black;">
                                    <tr>
                                        <th style = "border: 1px solid black; font-size: 15px;">
                                            Title
                                        </th>
                                        <th style = "border: 1px solid black; font-size: 15px;">
                                            Description
                                        </th>
                                    </tr>
                                    <?php
                                    foreach ($pageItems[$pagKey] as $pageItem) {
                                        ?>
                                        <tr>
                                            <td style = "border: 1px solid black; text-align:center; font-size: 15px;">
                                                <?= @$pageItem->Title; ?>
                                            </td>
                                            <td style = "border: 1px solid black; text-align:center; font-size: 15px;">
                                                <?= @$pageItem->Description; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                <?php } ?>
            </div>
            <!-- /content -->
        </td>
        <td></td>
    </tr>
</table>
<!-- /BODY -->
<!-- FOOTER -->
<table class="footer-wrap" style="width: 100%;	clear:both!important;">
    <tr>
        <td></td>
        <td class="container" style="display:block!important;	max-width:600px!important;	margin:0 auto!important; /* makes it centered */clear:both!important;">
            <!-- content -->
            <div class="content" style="padding:15px;max-width:600px;margin:0 auto;display:block; ">
                <table style="width: 100%; ">
                    <tr>
                        <td align="center">
                            <p style="border-top: 1px solid rgb(215,215,215); padding-top:15px;font-size:10px;font-weight: bold;font-family: arial;">
                                PM Software

                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- /content -->
        </td>
        <td></td>
    </tr>
</table>
<!-- /FOOTER -->