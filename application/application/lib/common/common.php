<?php
//共通設定変数格納領域

define('APP_ROOT_PATH',  realpath(dirname(__FILE__) .'/../../'));
define('APP_ETC_PATH',  APP_ROOT_PATH.'/etc');
define('APP_WEBAPP_PATH',  APP_ROOT_PATH.'/webapp');
define('APP_LIB_PATH',  APP_ROOT_PATH.'/lib');
//認証のタイムアウト（300分 18000秒）
define('AUTH_TIMEOUT',  300 * 60);
