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

    $_datasetObject = new app\lib\util\datastoreHelperUtil();

    //データセットテーブル
    $_datasetObject->setDataStoreName($setStoreName);
    $_datasetObject->setDataStoreType('DATASET');
    $_datasetObject->setDataStoreOwner($setRegKey);
    $_datasetObject->deleteDataStore();

    $_d->setResposeData('dataset', $setStoreName);

    return $_d;
}