<?php
function authentication($_d) { return getPrivateRoll();}

function execute($_d) {

    $auth_key_name = $_d->getAccountData('auth_token_name');
    $aToken = $_d->getAccountData($auth_key_name);
    //アプリケーション情報の取得(ライセンスコード)
    $appInfo = array();
    $appInfo = $_d->getApplicationInfoData('application_info');
    $certKey = $appInfo['license_code'];

    $_certObject = new app\lib\util\certificationHelperUtil();
    //認証トークン復号化
    $_certObject->setCertificationKeyData($certKey);
    $_certObject->setDecryptionCode($aToken);
    $decData = array();
    $decData = $_certObject->getDecryptionCodeData();

    //認証処理
    $checkFlg = true;
    $currentDate = date("YmdHis");
    // 有効期限チェック
    if ($inLimitDate != "") {
        if ((strtotime($decData['certification_date']) + $inLimitDate) < strtotime($currentDate)) $checkFlg = false;
    }

    // 利用可能期間チェック
    //if ($outArray['available_start_date'] !== '') {
    //  if (intval($outArray['available_start_date']) > intval($nowDate)) $checkFlg = false;
    //}

    //$decData['user_id'];
    //$decData['password'];
    //認証トークン更新
    $decData = $_certObject->updateEncryptionCode($aToken);

    //$_d->setResposeData(キー名,値※配列も可)　でレスポンス用のデータに設定
    $_d->setResposeData('atoken',$_certObject->getEncryptionCode());

    return $_d;
}