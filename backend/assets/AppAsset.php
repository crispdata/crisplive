<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle {
    
    public $basePath = '@webroot';
    public $baseUrl = '/';
    public $css = [
        'assets/plugins/materialize/css/materialize.min.css',
        '//fonts.googleapis.com/icon?family=Material+Icons',
        'assets/plugins/datatables/css/jquery.dataTables.min.css',
        'assets/plugins/sweetalert/sweetalert.css',
        'assets/plugins/select2/css/select2.css',
        'assets/css/alpha.min.css',
        'assets/css/daterangepicker.css',
    ];
    public $js = [
        '//code.jquery.com/ui/1.10.4/jquery-ui.js',
        'assets/js/jsdata.js',
        'assets/plugins/materialize/js/materialize.min.js',
        'assets/plugins/material-preloader/js/materialPreloader.min.js',
        'assets/plugins/datatables/js/jquery.dataTables.min.js',
        'assets/plugins/jquery-validation/jquery.validate.min.js',
        'assets/plugins/jquery-steps/jquery.steps.min.js',
        'assets/plugins/select2/js/select2.min.js',
        'assets/js/pages/form-wizard.js',
        'assets/js/alpha.min.js',
        'assets/js/pages/form-select2.js',
        'assets/js/pages/table-data.js',
    ];
    public $depends = [
            //'yii\web\YiiAsset',
            //'yii\bootstrap\BootstrapAsset',
    ];

}
