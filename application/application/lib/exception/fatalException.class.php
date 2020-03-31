<?php
namespace app\lib\exception;
/**
 * 致命的エラーレベル（処理継続不可）のExceptionクラス
 *
 * @package
 * @access  public
 * @author  Kota Toda
 * @create  2019/10/14
 **/
class fatalException extends \Exception
{
    private $_ERROR_INFO = array();

    private $_ERROR_CODE_INFO = array();

    private $_ERROR_RESPONSE_CODE;

    public function __construct($inCode, $inErrorDtlMsg = '')
    {
        $this->loadErrorInfo();
        if ($inCode !== '') {

            $setErrorDetail['item_name']  = 'internal_system';
            $setErrorDetail['error_type'] = 'internal_error';
            $setErrorDetail['message']    = $this->_ERROR_CODE_INFO[$inCode]['message'];

            if ($inErrorDtlMsg !== '') {
                $setErrorDetail['message'] = $inErrorDtlMsg;
            } 
            $this->_ERROR_INFO[] = $setErrorDetail;
            $this->_ERROR_RESPONSE_CODE = $this->_ERROR_CODE_INFO[$inCode]['reponse_code'];
        }
    }


    //エラー内容を設定
    public function setErrorInfo($inErrorItem,$inErrorType,$inErrorMSG = ''){
        $setErrorDetail = array();

        $setErrorDetail['item_name'] = $inErrorItem;
        $setErrorDetail['error_type'] = $inErrorType;
        $setErrorDetail['message'] = $inErrorMSG;


        $this->_ERROR_INFO[] = $setErrorDetail;
    }
    public function getErrorInfo(){
        return $this->_ERROR_INFO;
    }


    private function loadErrorInfo()
    {
        $targetFilePath = dirname(__FILE__) ."/../config/code.json";
        if(is_file($targetFilePath) === true) {
            $this->_ERROR_CODE_INFO =  json_decode(mb_convert_encoding(file_get_contents($targetFilePath), 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN'),true);
        }
    }
}
