<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
        'assets/plugins/materialize/css/materialize.min.css',
        '//fonts.googleapis.com/icon?family=Material+Icons',
        'assets/plugins/metrojs/MetroJs.min.css',
        'assets/plugins/material-preloader/css/materialPreloader.min.css',
        'assets/plugins/datatables/css/jquery.dataTables.min.css',
        'assets/plugins/weather-icons-master/css/weather-icons.min.css',
        'assets/plugins/google-code-prettify/prettify.css',
        'assets/plugins/sweetalert/sweetalert.css',
        'assets/plugins/select2/css/select2.css',
        'assets/css/alpha.min.css',
        'assets/css/daterangepicker.css',
        'assets/css/custom.css',
        'assets/css/wColorPicker.min.css',
        'assets/css/wPaint.min.css',
        'assets/css/wickedpicker.min.css',
        'assets/css/magnific-popup.css',
    ];
    public $js = [
        'assets/plugins/jquery/jquery-2.2.0.min.js',
        '//code.jquery.com/ui/1.12.1/jquery-ui.js',
        'assets/plugins/materialize/js/materialize.js',
        //'assets/plugins/materialize/js/materialize.min.js',
        'assets/plugins/material-preloader/js/materialPreloader.min.js',
        'assets/plugins/jquery-blockui/jquery.blockui.js',
        'assets/plugins/datatables/js/jquery.dataTables.min.js',
        'assets/plugins/jquery-validation/jquery.validate.min.js',
        'assets/plugins/jquery-steps/jquery.steps.min.js',
        'assets/plugins/waypoints/jquery.waypoints.min.js',
        'assets/plugins/counter-up-master/jquery.counterup.min.js',
        'assets/plugins/jquery-sparkline/jquery.sparkline.min.js',
        'assets/plugins/chart.js/chart.min.js',
        'assets/plugins/flot/jquery.flot.min.js',
        'assets/plugins/flot/jquery.flot.time.min.js',
        'assets/plugins/flot/jquery.flot.symbol.min.js',
        'assets/plugins/flot/jquery.flot.resize.min.js',
        'assets/plugins/flot/jquery.flot.resize.min.js',
        'assets/plugins/flot/jquery.flot.tooltip.min.js',
        'assets/plugins/curvedlines/curvedLines.js',
        'assets/plugins/peity/jquery.peity.min.js',
        'assets/plugins/google-code-prettify/prettify.js',
        'assets/plugins/sweetalert/sweetalert.min.js',
        'assets/plugins/select2/js/select2.min.js',
        'assets/plugins/select2/js/select2.js',
        'assets/js/pages/form-wizard.js',
        'assets/js/alpha.min.js',
        'assets/js/pages/ui-modals.js',
        'assets/js/pages/table-data.js',
        'assets/js/moment.min.js',
        'assets/js/daterangepicker.min.js',
        'assets/js/custom.js',
        'assets/js/custom1.js',
        'assets/js/pages/dashboard.js',
        'assets/js/wickedpicker.js',
        'assets/js/wickedpicker.min.js',
        'assets/js/jquery.magnific-popup.js',
        'assets/js/jquery.magnific-popup.min.js',
    ];
    public $depends = [
            //'yii\web\YiiAsset',
            //'yii\bootstrap\BootstrapAsset',
    ];

}
