<?php
//共通認証処理領域
//管理者ロール情報
function getAdminRoll(){
    $outAuthtication = array();
    $outAuthtication = [
         'authenticationDataType'=>'header',
         'authenticationDataName'=>'negotiation',
         'aclType'=>'admin'
    ];
    return $outAuthtication;
}
//共有アカウントロール
function getProtectedRoll(){
    $outAuthtication = array();
    $outAuthtication = [
         'authenticationDataType'=>'header',
         'authenticationDataName'=>'negotiation',
         'aclType'=>'protected'
    ];
    return $outAuthtication;
}
//個人アカウントロール
function getPrivateRoll(){
    $outAuthtication = array();
    $outAuthtication = [
         'authenticationDataType'=>'header',
         'authenticationDataName'=>'negotiation',
         'aclType'=>'private'
    ];
    return $outAuthtication;
}
//共通認証処理
function execAuthentication($_d, $inRollType) {

    //復号化タイプ
    $setAtoken = '';
    if ($inRollType['authenticationDataType'] === 'header') {
        $setAtoken = $_d->getRequestHeaderData($inRollType['authenticationDataName']);
    }
    if ($inRollType['authenticationDataType'] === 'query') {
        $setAtoken = $_d->getRequestData($inRollType['authenticationDataName']);
    }
    //トークンエラー
    if(trim($setAtoken) === '') {
        $wExp = new app\lib\exception\warningException();
        $wExp->setErrorInfo($inRollType['authenticationDataName'],'','認証コードが設定されていません。');
        throw $wExp;
    }

    $appInfo = array();
    $appInfo = $_d->getApplicationInfoData('application_info');
    $certKey = $appInfo['license_code'];

    $authInfo = array();

    $_certObject = new app\lib\util\certificationHelperUtil();
    $_certObject->setCertificationKeyData($certKey);
    $_certObject->setDecryptionCode($setAtoken,  $certKey);
    //復号化データ取得
    $authUserInfo = $_certObject->getDecryptionCodeData();
    //APIロールチェック
    if($authUserInfo['aclType'] === $inRollType['aclType']) {
        //タイムアウトチェック
        $checkFlg = true;
        $currentDate = date("YmdHis");
        // 有効期限チェック
        if (AUTH_TIMEOUT !== "") {
            if ((strtotime($authUserInfo['certification_date']) + AUTH_TIMEOUT) < strtotime($currentDate)) {
                $wExp = new app\lib\exception\warningException();
                $wExp->setErrorInfo($inRollType['authenticationDataName'],'','このコードは有効期限が終了したため利用できません。');
                throw $wExp;
            }
        }
        if(checkUserAuthentication($authUserInfo['account_key'], $authUserInfo['userId'], $authUserInfo['password'])) {
            //アカウント情報格納
            $_d->setAccountDataAll($authUserInfo);
            //認証コード情報格納
            $_d->setAccountData('auth_token_name', $inRollType['authenticationDataName']);
            $_d->setAccountData($inRollType['authenticationDataName'], $setAtoken);
        }
    } else {
        $wExp = new app\lib\exception\warningException();
        $wExp->setErrorInfo($inRollType['authenticationDataName'],'','このコードは権限が異なるため利用できません。');
        throw $wExp;
    }
    return $_d;

}

//ユーザ認証
function checkUserAuthentication($inUqId, $inUserId, $inUserPassword) {
    $_accountObject = new app\lib\util\datastoreHelperUtil();
    //アカウントテーブル
    $_accountObject->setDataStoreName('USER');
    $_accountObject->setDataStoreType('ACCOUNT');
    $_accountObject->loadDataStoreDetail($inUqId);

    $checkPassword = $inUserPassword;
    $checkPassword = md5($checkPassword);
    $checkPassword = md5($checkPassword);
    $account_check = false;
    $account_error_list = array();
    if($_accountObject->getExecStatus() === true){
        $account_check = false;
        $accountInfo = array();
        $accountInfo = $_accountObject->getTempDataStoreAll();
        if(array_key_exists('userId',$accountInfo) === true) {
            if($accountInfo['status'] === true) {
                if($accountInfo['userId'] === $inUserId) {
                    if($accountInfo['password'] === $checkPassword) {
                        $account_check = true;
                    } else {
                        $account_error_list['password'] = 'パスワードが違います。';
                    }
                } else {
                    $account_error_list['userId'] = 'IDが違います。';
                }
            } else {
                $account_error_list['userId'] = 'アカウントが有効ではありません。';
            }
        } else {
            $account_error_list['userId'] = 'アカウントが存在しません。';
        }
    } else {
        $account_error_list['userId'] = 'アカウント取得処理に失敗しました。';
    }
    if($account_check === false) {
        $wExp = new app\lib\exception\warningException();
        foreach($account_error_list as $eKey => $eVal) {
            $wExp->setErrorInfo($eKey,'',$eVal);
        }
        throw $wExp;
    }

    return $account_check;
}