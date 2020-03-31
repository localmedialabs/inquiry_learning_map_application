<?php
function authentication($_d) { return getPrivateRoll();}
function validate($_d) {
    $outValidateInfo = array();

    $outValidateInfo = [
        ['type'=>'required','param_name'=>'mapkey']
    ];
    return $outValidateInfo;
}

function validateModel($_d) {
    return $_d;
}

function execute($_d) {

    //登録情報を設定
    $setRegKey = $_d->getAccountData('account_key');

    $setStoreName = $_d->getRequestData('mapkey');
    $aclList = array();
    $aclList = $_d->getRequestData('acl_account');

    $_mapObject = new app\lib\util\datastoreHelperUtil();
    //データセットテーブル
    $mapList = array();
    $_mapObject->setDataStoreType('MAP');
    $_mapObject->setDataStoreName($setStoreName);
    foreach ($aclList as $akey => $aval) {
        $_mapObject->setAclAccount($aval);
    }
    $_mapObject->saveDataStoreAcl();

    $_d->setResposeData('mapkey', $setStoreName);

    return $_d;
}