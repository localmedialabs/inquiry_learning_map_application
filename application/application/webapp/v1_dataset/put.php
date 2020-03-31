<?php
function authentication($_d) { return getPrivateRoll();}
function validate($_d) {
    $outValidateInfo = array();

    $outValidateInfo = [
        ['type'=>'required','param_name'=>'type'],
        ['type'=>'required','param_name'=>'title'],
        ['type'=>'required','param_name'=>'data']
    ];
    return $outValidateInfo;
}

function validateModel($_d) {
    return $_d;
}

function execute($_d) {

    //登録情報を設定
    $setRegKey = $_d->getAccountData('account_key');
    $datasetACLList = array();
    $datasetACLList['account_list'] = array();
    $_accountObject = new app\lib\util\datastoreHelperUtil();
    $_accountObject->setDataStoreName('USER');
    $_accountObject->setDataStoreType('ACCOUNT');
    $accountList = array();
    $accountList = $_accountObject->loadDataStoreDetail($setRegKey);
    $accountList = $_accountObject->getTempDataStoreAll();


    $registTargetDataStr = $_d->getRequestData('data');
    $registTargetData = array();
    $registTargetData = json_decode($registTargetDataStr);

    $_datasetObject = new app\lib\util\datastoreHelperUtil();
    $setStoreName = $_d->getRequestData('type').'_'.$accountList['group'];

    //データセットテーブル
    $_datasetObject->setDataStoreOwner($setRegKey);
    $_datasetObject->setDataStoreName($setStoreName);
    $_datasetObject->setDataStoreType('DATASET');
    $_datasetObject->setDataStoreNameLabel($_d->getRequestData('title'));
    foreach($registTargetData as $rgkey => $rgVal) {
        foreach($rgVal as $rgikey => $rgiVal) {
            $_datasetObject->setTempDataStore($rgkey, $rgikey, $rgiVal);
        }
    }

    //保存
    $_datasetObject->saveDataStore();

    $_d->setResposeData('dataset', $setStoreName);

    return $_d;
}