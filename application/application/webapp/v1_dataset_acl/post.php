<?php
function authentication($_d) { return getPrivateRoll();}
function validate($_d) {
    $outValidateInfo = array();

    $outValidateInfo = [
        ['type'=>'required','param_name'=>'dataset_id']
    ];
    return $outValidateInfo;
}

function validateModel($_d) {
    return $_d;
}

function execute($_d) {

    //登録情報を設定
    $setRegKey = $_d->getAccountData('account_key');

    $setStoreName = $_d->getRequestData('dataset_id');
    $aclList = array();
    $aclList = $_d->getRequestData('acl_account');

    $_datasetObject = new app\lib\util\datastoreHelperUtil();
    //データセットテーブル
    $mapList = array();
    $_datasetObject->setDataStoreType('DATASET');
    $_datasetObject->setDataStoreName($setStoreName);
    foreach ($aclList as $akey => $aval) {
        $_datasetObject->setAclAccount($aval);
    }
    $_datasetObject->saveDataStoreAcl();

    $_d->setResposeData('dataset_id', $setStoreName);

    return $_d;
}