<?php
function validate($_d) {
    $outValidateInfo = array();

    $outValidateInfo = [
        ['type'=>'required','param_name'=>'userId']
       ,['type'=>'required','param_name'=>'password']
    ];
    return $outValidateInfo;
}

function validateModel($_d) {
    return $_d;
}

function execute($_d) {

    //認証処理(DB)
    $setRegKey = getUserUniqueId($_d->getRequestData('userId'));

    if(checkUserAuthentication($setRegKey, $_d->getRequestData('userId'), $_d->getRequestData('password')) === true) {
        $appInfo = array();
        $appInfo = $_d->getApplicationInfoData('application_info');
        $certKey = $appInfo['license_code'];

        $_certObject = new app\lib\util\certificationHelperUtil();
        $_certObject->setCertificationData('userId', $_d->getRequestData('userId'));
        $_certObject->setCertificationData('password', $_d->getRequestData('password'));
        $_certObject->setCertificationData('account_key', $setRegKey);
        $_certObject->setCertificationData('aclType', 'private');
        $_certObject->setCertificationKeyData($certKey);
        $_certObject->setEncryptionCode();

        $_d->setResposeData('atoken',$_certObject->getEncryptionCode());
        //ログ操作書き込み
        setUserActionLog($setRegKey, $_d->getRequestData('userId'), 'login');
    }

    return $_d;
}