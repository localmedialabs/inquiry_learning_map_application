<?php
namespace app\lib\util;

/**
 * パラメータデータ補助クラス
 *
 * Webアプリケーションのrequestからレスポンスまでのデータ（パラメータ）の流れを補助するクラス
 *
 * @package
 * @access  public
 * @author  Kota Toda
 * @create  2019/10/24
 **/
class dataHelperUtil {


    //画面出力エンコード
    private $_ENCODE = 'utf-8';

    //リクエストヘッダーデータ
    private $_REQUEST_HEADER_DATA = array();

    //リクエストデータ
    private $_REQUEST_DATA = array();
    //リクエストデータ(ファイル)
    private $_REQUEST_FILE_DATA = array();

    //レスポンスデータ
    private $_RESPONSE_DATA = array();
    //レスポンスデータセットタイプ(上書き型、setterでのセット)
    private $_RESPONSE_SET_TYPE = '0';

    //システムマスタデータ
    private $_MASTER_DATA = array();
    //アプリケーション情報データ
    private $_APPLICATION_INFO_DATA = array();
    //ログインアカウントデータ（認証時のみ）
    private $_ACCOUNT_DATA = array();

    //アクセサメソッド
    //リクエストデータ領域へセット
    public function setRequestData($inKey,$inValue) {
        //リクエストデータが配列かどうか
        if(is_array($inValue) === false) {
            $this->_REQUEST_DATA[$inKey] = $inValue;
        } else {
            if(array_key_exists($inKey,$this->_REQUEST_DATA) === false) {
                $this->_REQUEST_DATA[$inKey] = array();
            }
            $this->_REQUEST_DATA[$inKey] = $inValue;
        }
    }
    public function getRequestData($inKey) {
        $outData = '';
        if(array_key_exists($inKey, $this->_REQUEST_DATA) === true) {
            $outData = $this->_REQUEST_DATA[$inKey];
        }
        return $outData;
    }
    //リクエストデータすべて返却
    public function getRequestDataAll() {
        return $this->_REQUEST_DATA;
    }

    //リクエストヘッダー
    public function setRequestHeaderData($inKey,$inValue) {
        $this->_REQUEST_HEADER_DATA[$inKey] = $inValue;
    }
    public function getRequestHeaderData($inKey) {
        return $this->_REQUEST_HEADER_DATA[$inKey];
    }
    public function getRequestHeaderDataAll() {
        return $this->_REQUEST_HEADER_DATA;
    }

    //リクエストファイル
    public function setRequestFileData($inKey,$inValue) {
        $this->_REQUEST_FILE_DATA[$inKey] = $inValue;
        $this->_REQUEST_FILE_DATA[$inKey]['file_data_base64']  = '';
        //実態ファイルの格納
        if(is_file($inValue['tmp_name']) === true) {
            $this->_REQUEST_FILE_DATA[$inKey]['file_data_base64'] = base64_encode(readfile($inValue['tmp_name']));
        }
    }
    //リクエストファイルデータ
    public function getRequestFileData($inKey) {
        return $this->_REQUEST_FILE_DATA[$inKey];
    }
    //リクエストファイル詳細データ
    public function getRequestFileDetailData($inKey,$inSubKey) {
        return $this->_REQUEST_FILE_DATA[$inKey][$inSubKey];
    }
    public function getRequestFileDataAll() {
        return $this->_REQUEST_FILE_DATA;
    }

    //レスポンスデータ領域へセット
    public function setResposeData($inKey,$inValue) {
        $this->_RESPONSE_SET_TYPE = '0';
        //配列かどうか
        if(is_array($inValue) === false) {
            $this->_RESPONSE_DATA[$inKey] = $inValue;
        } else {
            if(array_key_exists($inKey,$this->_RESPONSE_DATA) === false) {
                $this->_RESPONSE_DATA[$inKey] = array();
            }
            $this->_RESPONSE_DATA[$inKey] = $inValue;
        }
    }
    //レスポンスデータ(配列でそのまま上書き)
    public function setResposeDataAll($inValue) {
        $this->_RESPONSE_SET_TYPE = '0';
        //配列かどうか
        if(is_array($inValue) === true) {
            $this->_RESPONSE_SET_TYPE = '1';
            $this->_RESPONSE_DATA = $inValue;
        }
    }
    public function getResposeData($inKey) {
        return $this->_RESPONSE_DATA[$inKey];
    }
    public function getResposeDataAll() {
        return $this->_RESPONSE_DATA;
    }
    //レスポンスタイプ
    public function getResposeSetType() {
        return $this->_RESPONSE_SET_TYPE;
    }

    //マスタデータ
    public function getMasterData($inKey) {
        return $this->_MASTER_DATA[$inKey];
    }
    public function getMasterDataAll() {
        return $this->_MASTER_DATA;
    }

    //アプリケーションデータ
    public function getApplicationInfoData($inKey) {
        return $this->_APPLICATION_INFO_DATA[$inKey];
    }
    public function getApplicationInfoDataAll() {
        return $this->_APPLICATION_INFO_DATA;
    }
    //アカウントデータ（ログインユーザ情報）
    public function setAccountData($inKey, $inValue) {
        $this->_ACCOUNT_DATA[$inKey] = $inValue;
    }
    public function setAccountDataAll($inData) {
        $this->_ACCOUNT_DATA = $inData;
    }
    public function getAccountData($inKey) {
        return $this->_ACCOUNT_DATA[$inKey];
    }
    public function getAccountDataAll() {
        return $this->_ACCOUNT_DATA;
    }

    /**
     * サーバ変数からクラスデータへ読み込み
     * @access public
     * @param  なし
     * @return なし
     */
    public function loadRequestData() {

        //ヘッダーデータ
        foreach(getallheaders() as $h_key => $h_value) {
            $this->setRequestHeaderData($h_key,$h_value);
        }

        //httpパラメータ
        foreach ($_REQUEST as $r_key => $r_value) {
            $this->setRequestData($r_key,$r_value);
        }
        //httpパラメータ(payload)
        $requestPayload = file_get_contents('php://input');
        if(trim($requestPayload) !== '') {
            $reqPayloadList = array();
            $reqPayloadList = json_decode($requestPayload);
            foreach ($reqPayloadList as $rp_key => $rp_value) {
                $this->setRequestData($rp_key,$rp_value);
            }
        }

        //ファイルデータ
        if (isset($_FILES) === true) {
            foreach ($_FILES as $f_key => $f_value) {
                $this->setRequestFileData($f_key,$f_value);
            }
        }
    }

    /**
     * リクエストデータ・レスポンス用のデータへのマージ処理
     * @access public
     * @param  なし
     * @return なし
     */
    public function margeRequestResponseData() {
        $tmpResponseData = array();
        $tmpResponseData = array_merge($this->_RESPONSE_DATA,$this->_REQUEST_DATA);
        $this->_RESPONSE_DATA = $tmpResponseData;
        unset($tmpResponseData);
    }

    /**
     * マスタデータ読み込み（設定ファイルからの読み込み）
     * @access public
     * @param  なし
     * @return なし
     */
    public function loadMasterData() {
        $targetFilePath = dirname(__FILE__) ."/../config/master.json";
        if(is_file($targetFilePath) === true) {
            $this->_MASTER_DATA =  json_decode(mb_convert_encoding(file_get_contents($targetFilePath), 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN'),true);
        }
    }
    /**
     * アプリケーション情報データ読み込み（設定ファイルからの読み込み）
     * @access public
     * @param  なし
     * @return なし
     */
    public function loadApplicationInfoData() {
        $targetFilePath = dirname(__FILE__) ."/../config/application.json";
        if(is_file($targetFilePath) === true) {
            $this->_APPLICATION_INFO_DATA =  json_decode(mb_convert_encoding(file_get_contents($targetFilePath), 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN'),true);
        }
    }
}