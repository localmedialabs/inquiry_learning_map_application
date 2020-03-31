<?php
namespace app\lib\util;
define('OPENSSL_ENCRYPT_METHOD', 'aes-256-cbc');
define('OPENSSL_ENCRYPT_PHP_VERSION', 70200);

/**
 * 認証処理補助クラス
 *
 * トークン発行など認証関連補助するクラス
 *
 * @package
 * @access  public
 * @author  Kota Toda 
 * @create  2019/10/24
 **/
class certificationHelperUtil {

    //認証コードを生成するための暗号化対象データ（ユーザ情報など）
    private $_ENCRYPTION_DATA = array();
    //認証キーデータ（ライセンスコードなど）
    private $_CERTIFICATION_KEY = '';
    //認証トークン
    private $_CERTIFICATION_CODE = '';
    //復号化したデータ
    private $_DECRYPTION_DATA = array();

    //アクセサメソッド（認証対象データ設定）
    public function setCertificationData($inKey, $inValue) {
        $this->_ENCRYPTION_DATA[$inKey] = $inValue;
    }
    //アクセサメソッド（認証キーデータ設定）
    public function setCertificationKeyData($inValue) {
        $this->_CERTIFICATION_KEY = $inValue;
    }

    //アクセサメソッド(暗号化)
    public function setEncryptionCode() {
        $certificationData = array();
        //認証日時を保存
        $certificationData['certification_date'] =  date("YmdHis");
        foreach($this->_ENCRYPTION_DATA as $enkey => $envalue) {
            $certificationData[$enkey] = $envalue;
        }
        $setEnTrgValue = json_encode($certificationData);
        //認証トークン取得
        $this->_CERTIFICATION_CODE = $this->convertEncryptionCode($setEnTrgValue, $this->_CERTIFICATION_KEY);
    }
    //アクセサメソッド(認証トークン取得)
    public function getEncryptionCode() {
        return $this->_CERTIFICATION_CODE;
    }
    //アクセサメソッド(復号化)
    public function setDecryptionCode($inCertCode) {
        $decCodeData = '';
        $decCodeData = $this->convertDecryptionCode($inCertCode, $this->_CERTIFICATION_KEY);

        $this->_DECRYPTION_DATA = json_decode($decCodeData, true);
    }
    //アクセサメソッド(復号化したデータ取得)
    public function getDecryptionCodeData() {
        return $this->_DECRYPTION_DATA;
    }
    //認証コード
    public function updateEncryptionCode($inCertCode) {
        $decCodeData = '';
        $decCodeData = $this->convertDecryptionCode($inCertCode, $this->_CERTIFICATION_KEY);
        $decCodeList = array();
        $decCodeList = json_decode($decCodeData, true);

        //認証日時を更新
        $decCodeList['certification_date'] =  date("YmdHis");

        //再暗号化
        $setEnTrgValue = json_encode($decCodeList);
        //認証トークン取得
        $this->_CERTIFICATION_CODE = $this->convertEncryptionCode($setEnTrgValue, $this->_CERTIFICATION_KEY);

    }

    //暗号化処理
    private function convertEncryptionCode($inEncStr, $inKey, $inMode = 'aes') {
        $outString = $inEncStr;
        switch ($inMode) {
            case 'aes':
                if ($inKey !== "") {
                    // PHP7.2.0以上ならOPENSSLで暗号化
                    if (PHP_VERSION_ID > OPENSSL_ENCRYPT_PHP_VERSION) {
                        // 方式に応じたIV(初期化ベクトル)に必要な長さを取得
                        $ivSize = openssl_cipher_iv_length(OPENSSL_ENCRYPT_METHOD);
                        //Initial Vector（初期ベクトル）生成
                        $iv = openssl_random_pseudo_bytes($ivSize);
                        // OPENSSL_RAW_DATA と OPENSSL_ZERO_PADDING を指定可
                        $options = 0;
                        //暗号化
                        $outString = base64_encode(openssl_encrypt($inEncStr, OPENSSL_ENCRYPT_METHOD, $inKey, $options, $iv) . $iv);
                    } else {
                        //暗号化方式をRIJNDAELの256bitブロック長、暗号化方式をCBC
                        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
                        //Initial Vector（初期ベクトル）生成
                        srand();
                        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
                        //暗号化
                        $outString = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $inKey, $inEncStr, MCRYPT_MODE_CBC, $iv) . $iv);
                    }
                }
            break;
            default:
            break;
        }
        return $outString;
    }
    //復号化処理
    private function convertDecryptionCode($inDecStr,  $inKey, $inMode = 'aes') {
        $outString = $inDecStr;
        switch ($inMode) {
            case 'aes':
                if ($inKey !== "") {
                    //base64データ群
                    //$base64Text = str_replace(" ", "+", $inDecStr);
                    $base64Text = $inDecStr;
                    //暗号化データ
                    $encryptText = base64_decode($base64Text);
                    // PHP7.2.0以上ならOPENSSLで復号化
                    if (PHP_VERSION_ID > OPENSSL_ENCRYPT_PHP_VERSION) {
                        //Initial Vector（初期ベクトル）取得
                        $ivSize = openssl_cipher_iv_length(OPENSSL_ENCRYPT_METHOD);
                        $encryptTextSize = strlen($encryptText);
                        $iv = substr($encryptText, $encryptTextSize - $ivSize);
                        $encryptText = substr($encryptText, 0, $encryptTextSize - $ivSize);

                        // OPENSSL_RAW_DATA と OPENSSL_ZERO_PADDING を指定可
                        $options = 0;

                        //復号化
                        if (strlen($iv) === $ivSize) {
                            $outString = rtrim(openssl_decrypt($encryptText, OPENSSL_ENCRYPT_METHOD, $inKey, $options, $iv), "\0");
                        }
                    } else {
                        //Initial Vector（初期ベクトル）取得
                        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
                        $encryptTextSize = strlen($encryptText);
                        $iv = substr($encryptText, $encryptTextSize - $ivSize);
                        $encryptText = substr($encryptText, 0, $encryptTextSize - $ivSize);
                        //復号化
                        if (strlen($iv) === $ivSize) {
                            $outString = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $encryptText, MCRYPT_MODE_CBC, $iv), "\0");
                        }
                    }
                }
                break;
            default:
            break;
        }
        return $outString;
    }

}