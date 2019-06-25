<?php
# Database Configuration
define( 'DB_NAME', 'crispfront' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'WRcqP^UiFk0#k0L' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_HOST_SLAVE', '127.0.0.1' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'wp_';

# Security Salts, Keys, Etc
define('AUTH_KEY',         'Ld{|K0`cI3}5wF~`L^H!$uxs>+Z[|+2N(v6&IQicI5lgm3Q?)C=3;+v]3)G(n*~ ');
define('SECURE_AUTH_KEY',  'WnOf>aQpe?#k8di1HDVa2pQHDm:6Q)h4(#nDv1EiU;=`&B)<dp{|xC^mSyu4(.aS');
define('LOGGED_IN_KEY',    '[EUq){/UOKF_|wPh&0+Tm|)5<O,$MCQJ/oK2E`=cpq-ZrtY ?!PmfCq[W79-pHhx');
define('NONCE_KEY',        '@5)X2aNhkTL<FtW$^TYTZ-~!i$aNA<ZV]K zFK0BJ6$LsvIZBufAgE+ZfC]Swq8z');
define('AUTH_SALT',        'Zg-eaN7%gM}uU~,kFL5G_ML*.Rln&<rzeqd4Y6I+P2BX+uTUu4S9kGF_#/v;9a#B');
define('SECURE_AUTH_SALT', 'yF~u W25W)X~HDG$13Lt7-=kFv7?fVRONJ]N4Kei#=j!]PS6aTW%Dji.]l<::Nc6');
define('LOGGED_IN_SALT',   'Xb4<^a8h,H||!qswZ/lp}cY--4TBYmhr!4+.T^U-CCODY5WxE7A+p7j;4J|B>>}U');
define('NONCE_SALT',       'YI;#AUAzj)SkQkMO]z){TQpz$|6EvK8m3 HXoPq%;5p;>9f22O-Uzuhz VGc$67|');


# Localized Language Stuff

define( 'WP_CACHE', TRUE );

define( 'WP_AUTO_UPDATE_CORE', false );

define( 'PWP_NAME', 'Crispdata' );

define( 'FS_METHOD', 'direct' );

define( 'FS_CHMOD_DIR', 0775 );

define( 'FS_CHMOD_FILE', 0664 );

define( 'PWP_ROOT_DIR', '/nas/wp' );

define( 'WPE_APIKEY', '56303cef435e18f33154136bb3095fc6aad113ed' );

define( 'WPE_CLUSTER_ID', '120073' );

define( 'WPE_CLUSTER_TYPE', 'pod' );

define( 'WPE_ISP', true );

define( 'WPE_BPOD', false );

define( 'WPE_RO_FILESYSTEM', false );

define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );

define( 'WPE_SFTP_PORT', 2222 );

define( 'WPE_LBMASTER_IP', '' );

define( 'WPE_CDN_DISABLE_ALLOWED', true );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISALLOW_FILE_EDIT', FALSE );

define( 'DISABLE_WP_CRON', false );

define( 'WPE_FORCE_SSL_LOGIN', false );

define( 'FORCE_SSL_LOGIN', false );

/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/

define( 'WPE_EXTERNAL_URL', false );

define( 'WP_POST_REVISIONS', FALSE );

define( 'WPE_WHITELABEL', 'wpengine' );

define( 'WP_TURN_OFF_ADMIN_BAR', false );

define( 'WPE_BETA_TESTER', false );

umask(0002);

$wpe_cdn_uris=array ( );

$wpe_no_cdn_uris=array ( );

$wpe_content_regexs=array ( );

$wpe_all_domains=array ( 0 => 'crispdata.co.in', );

$wpe_varnish_servers=array ( 0 => 'pod-120073', );

$wpe_special_ips=array ( 0 => '104.197.205.60', );

$wpe_netdna_domains=array ( );

$wpe_netdna_domains_secure=array ( );

$wpe_netdna_push_domains=array ( );

$wpe_domain_mappings=array ( );

$memcached_servers=array ( );
define('WPLANG','');


define( 'WP_HOME', 'https://crispdata.co.in/codefront/' );
define( 'WP_SITEURL', 'https://crispdata.co.in/codefront/' );
# WP Engine ID


# WP Engine Settings






# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');

$_wpe_preamble_path = null; if(false){}
