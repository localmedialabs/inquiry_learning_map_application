<?php
require_once( dirname(__FILE__) .'/vendor/autoload.php' );
require_once( dirname(__FILE__) .'/lib/common/common_model_exec.php' );
require_once( dirname(__FILE__) .'/lib/common/common.php' );
require_once( dirname(__FILE__) .'/lib/common/common_authentication.php' );

/**
 * アプリケーションクラス
 *
 * Webアプリケーションの制御の基底となるクラス
 *
 * @package
 * @access  public
 * @author  Kota Toda
 * @create  2019/10/11
 **/
class appHandler{


    //画面出力エンコード
    private $_ENCODE = 'utf-8';

    //APIパス
    private $_ACCESS_PATH = '';

    //execパス
    private $_EXEC_PATH = '';

    //実行ファイル
    private $_EXEC_FILE = '';

    //アプリケーション内のデータ管理オブジェクト
    private $_DATA_OBJECT = null;

    //レスポンス表示VIEW　テンプレートパス
    private $_RESPONSE_VIEW_FILE_PATH = '';

    //VIEWファイル（上書き用ファイル名）
    private $_EXEC_VIEW_FILE = '';

    //レスポンスオプション
    private $_RESPONSE_OPTION_DATA = array();

    //起動モード（validate ）
    private $_EXEC_MODE = '';

    //アクセサメソッド
    public function setAccessPath($inAccessPath) {
        $this->_ACCESS_PATH = $inAccessPath;
    }
    public function getAccessPath() {
        return $this->_ACCESS_PATH;
    }

    public function setExecPath($inExecPath) {
        $this->_EXEC_PATH = $inExecPath;
    }
    public function getExecPath() {
        return $this->_EXEC_PATH;
    }

    //実行ファイル名
    public function setExecFile($inExecFile) {
        $this->_EXEC_FILE = $inExecFile;
    }
    public function getExecFile() {
        return $this->_EXEC_FILE;
    }
    //実行ファイルViewファイル
    public function setViewFile($inExecViewFile) {
        $this->_EXEC_VIEW_FILE = $inExecViewFile;
    }
    public function getViewFile() {
        return $this->_EXEC_VIEW_FILE;
    }
    //実行ファイル名
    public function setExecMode($inMode) {
        $this->_EXEC_MODE = $inMode;
    }

    /**
     * アプリケーション制御実行メソッド
     * @access public
     * @param  なし
     * @return なし
     */
    public function execute() {

        try {
            //実行パス生成
            $this->createExecTargetPath();
            $exec_base_path = dirname(__FILE__) .'/webapp/';
            $_webapp_exec_path = $exec_base_path.$this->getExecPath().'/'.$this->getExecFile();
            $this->_RESPONSE_VIEW_FILE_PATH = $exec_base_path.'/common/view/result_json.tpl';
            //データの取り回し
            $this->_DATA_OBJECT = new app\lib\util\dataHelperUtil();

            if(is_file($_webapp_exec_path) === true) {

                //マスタデータ読み込み
                $this->_DATA_OBJECT->loadMasterData();
                $this->_DATA_OBJECT->loadApplicationInfoData();
                //フィルター処理


                //リクエストデータ読み込み
                $this->_DATA_OBJECT->loadRequestData();
                if($this->_DATA_OBJECT->getRequestData('exec_mode') === 'validate') {
                    $this->_EXEC_MODE = 'validate';
                }
                //実行ファイル読み込み
                require_once($_webapp_exec_path);

                //認証処理（トークン認証など）
                if(function_exists('authentication') === true) {
                    $authInfo = array();
                    $authInfo = authentication($this->_DATA_OBJECT);
                    $this->_DATA_OBJECT = execAuthentication($this->_DATA_OBJECT, $authInfo);
                }

                //起動モード
                $execMode = 'on';
                switch ($this->_EXEC_MODE) {
                    case 'validate':
                        $execMode = 'off';
                    break;
                    default:
                        $execMode = 'on';
                }

                //入力パラメータバリデーション処理
                if(function_exists('validate') === true) {
                    $validateInfo = array();
                    //バリデーション定義取得
                    $this->execParamValidate(validate($this->_DATA_OBJECT));

                    //業務チェック処理
                    if(function_exists('validateModel') === true) {
                        //業務チェック実行
                        $this->_DATA_OBJECT = validateModel($this->_DATA_OBJECT);
                    }

                }
                //入力チェックモード解除時
                if($execMode === 'on') {

                    //実行
                    if(function_exists('execute') === true) {
                        $this->_DATA_OBJECT = execute($this->_DATA_OBJECT);
                        $this->_RESPONSE_OPTION_DATA['response_type'] = 'success';

                        //画面描写処理(上書き)
                        $execViewFilePath = $exec_base_path.$this->getExecPath().'/'.$this->getViewFile();
                        if(is_file($execViewFilePath) === true) {
                            $this->_RESPONSE_VIEW_FILE_PATH = $execViewFilePath;
                        }
                    }
                } else {
                    $this->_DATA_OBJECT->setResposeData('message','validate success');
                    $this->_RESPONSE_OPTION_DATA['response_type'] = 'success';
                }
            } else {
                //API リソース NOT FOUND
                throw new app\lib\exception\fatalException('EAPC1001');
            }

        //継続可能エラー処理(入力チェック)
        } catch (app\lib\exception\warningException $we) {
            $this->_RESPONSE_OPTION_DATA['response_type'] = 'warning';
            $this->_DATA_OBJECT->setResposeDataAll($we->getErrorInfo());

        //継続不可能エラー処理
        } catch (app\lib\exception\fatalException $fe) {
            $this->_RESPONSE_OPTION_DATA['response_type'] = 'fatal';
            $this->_DATA_OBJECT->setResposeDataAll($fe->getErrorInfo());
        }

        //レスポンス処理起動
        $this->execResponse();
    }

    /**
     * API⇔アプリケーション実行ファイル変換メソッド
     * @access private
     * @param  なし
     * @return なし
     */
    private function createExecTargetPath() {

        $tmpAccessPath = $this->getAccessPath();
        //変換
        $tmpAccessPathList = array();
        //空文字変換
        $tmpAccessPathList = array_filter(explode('/',$tmpAccessPath),"strlen");

        $tmpexecPath = implode('_',$tmpAccessPathList);
        $tmpexecFile = strtolower($_SERVER['REQUEST_METHOD']).'.php';
        $tmpexecViewFile = strtolower($_SERVER['REQUEST_METHOD']).'.tpl';
        $this->setExecPath($tmpexecPath);
        $this->setExecFile($tmpexecFile);
        $this->setViewFile($tmpexecViewFile);
    }

    /**
     * パラメータバリデーション処理
     * @access private
     * @param  なし
     * @return なし
     */
    private function execParamValidate($inValFormat) {
        //バリデーション実行
        $_valObject = new app\lib\util\validateHelperUtil($this->_DATA_OBJECT->getRequestDataAll());

        $messageList = array();
        $targetFilePath = dirname(__FILE__) ."/lib/config/message.json";
        if(is_file($targetFilePath) === true) {
            $messageList = json_decode(mb_convert_encoding(file_get_contents($targetFilePath), 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN'),true);
        }
        foreach($inValFormat as $vKey => $vVal) {
            $valParamList = array();
            $valParamList[0] = $vVal['type'];
            $valParamList[1] = $vVal['param_name'];
            $valParamList[2] = '';
            $valParamList[3] = '';
            if(array_key_exists('check_param_name',$vVal) === true) {
                $valParamList[2] = $vVal['check_param_name'];
            }
            if(array_key_exists('check_param_value',$vVal) === true) {
                $valParamList[2] = $vVal['check_param_value'];
            }
            if(array_key_exists('check_param_value_range',$vVal) === true) {
                $valParamList[2] = $vVal['check_param_value_range']['from'];
                $valParamList[3] = $vVal['check_param_value_range']['to'];
            }
            if(array_key_exists('check_param_value_list',$vVal) === true) {
                $valParamList[2] = array();
                $valParamList[2] = $vVal['check_param_value_list'];
            }
            if(array_key_exists('option_setting',$vVal) === true) {
                $valParamList[2] = $vVal['option_setting'];
            }
            if(array_key_exists('msg',$vVal) === true) {
                $_valObject->rule($valParamList[0],$valParamList[1],$valParamList[2],$valParamList[3])->message($vVal['msg']);
            } else {
                //$_valObject->rule($valParamList[0],$valParamList[1],$valParamList[2],$valParamList[3]);
                $_valObject->rule($valParamList[0],$valParamList[1],$valParamList[2],$valParamList[3])->message($messageList['validate'][$valParamList[0]]["message"]);
            }

        }


        if($_valObject->validate() === false ){

            $errorParamList = array();
            $errorParamList = $_valObject->errors();
            $wExp = new app\lib\exception\warningException();
            foreach($errorParamList as $eKey => $eVal) {
                $wExp->setErrorInfo($eKey,'',$eVal[0]);
            }
            throw $wExp;
        }

    }

    /**
     * VIEW領域生成およびレスポンスコード返却
     * @access private
     * @param  なし
     * @return なし
     */
    private function execResponse() {
        $outViewObj = new app\lib\util\templateHelperUtil();
//        $outViewObj->default_modifiers=array('escape:"html"');	// XSS対策
        //ステータスコード
        $outViewObj->assign('result_type',$this->_RESPONSE_OPTION_DATA['response_type']);

        $setResponseData = array();
        //JSONフォーマットをそろえる。
        if($this->_DATA_OBJECT->getResposeSetType() === '0') {
            $setResponseData[] = $this->_DATA_OBJECT->getResposeDataAll();
        } else {
            $setResponseData = $this->_DATA_OBJECT->getResposeDataAll();
        }

        //パラメータセット(JSON変換)
        $outViewObj->assign('data',json_encode($setResponseData));

        //データクリア
        unset($setResponseData);

        //レンダリング
        $outViewObj->display($this->_RESPONSE_VIEW_FILE_PATH);
        //レスポンスコード設定

    }
}