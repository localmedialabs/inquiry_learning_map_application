<?php
function authentication($_d) { return getPrivateRoll();}

function validate($_d) {
    $outValidateInfo = array();

    return $outValidateInfo;
}

function validateModel($_d) {
    return $_d;
}

function execute($_d) {

    //登録情報を設定
    $setRegKey = $_d->getAccountData('account_key');
    $setStoreName = $_d->getRequestData('detaset_id');

    $_mapObject = new app\lib\util\datastoreHelperUtil();
    $_mapObject->setDataStoreOwner($setRegKey);
    $_mapObject->setDataStoreName($setStoreName);
    $_mapObject->setDataStoreType('DATASET');
    //データセットテーブル
    $datasetList = array();
    $datasetList = $_mapObject->getDataStoreDetailList();

    $_d->setResposeData('detaset_id', $setStoreName);
    $_d->setResposeData('dataset_detail_list', $datasetList);

    return $_d;
}