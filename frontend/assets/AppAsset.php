<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '//fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700',
        '//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700',
        'frontend/web/assets/bootstrap/css/bootstrap.min.css',
        'frontend/web/assets/css/plagin-css/plagin.css',
        'frontend/web/assets/icons/toicons/css/styles.css',
        'frontend/web/assets/css/style.css?v=92',
        'frontend/web/assets/css/responsive.css',
        'frontend/web/assets/plugins/select2/css/select2.css',
        'frontend/web/assets/plugins/sweetalert/sweetalert.css',
    ];
    public $js = [
        'frontend/web/assets/js/plagin-js/jquery-1.11.3.js',
        'frontend/web/assets/bootstrap/js/bootstrap.min.js',
        'frontend/web/assets/js/plagin-js/plagin.js',
        'frontend/web/assets/plugins/sweetalert/sweetalert.min.js',
        'frontend/web/assets/plugins/select2/js/select2.min.js',
        'frontend/web/assets/js/custom-scripts.js?v=90',
        
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
