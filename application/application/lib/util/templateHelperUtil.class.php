<?php
namespace app\lib\util;

/**
 * テンプレート処理処理補助クラス
 *
 * テンプレート処理など画面表示関連補助するクラス
 *
 * @package
 * @access  public
 * @author  Kota Toda 
 * @create  2019/10/24
 **/
class templateHelperUtil extends \Smarty {
	function __construct() {
        parent::__construct();
        $base_dir = APP_ETC_PATH.'/smarty/';

        $this->debugging  = FALSE;
        $this->template_dir = $base_dir . "templates/";
        $this->compile_dir  = $base_dir . "templates_c/";
        $this->caching      = FALSE;
        $this->cache_dir    = $base_dir . "cache/";
        $this->config_dir   = $base_dir . "config/";
        if (is_dir($base_dir) === false) {
            mkdir($base_dir);
            mkdir($this->template_dir);
            mkdir($this->compile_dir);
            mkdir($this->cache_dir);
            mkdir($this->config_dir);
        }

    }
}