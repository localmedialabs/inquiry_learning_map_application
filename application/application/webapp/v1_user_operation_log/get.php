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

    $_oprationLogObject = new app\lib\util\datastoreHelperUtil();
    $_oprationLogObject->setDataStoreOwner($setRegKey);
    $_oprationLogObject->setDataStoreType('ACCOUNT_LOG');
    $_oprationLogObject->setDataStoreName($setRegKey);
    //データセットテーブル
    $datasetList = array();
    $datasetList = $_oprationLogObject->getDataStoreDetailList();

    $_d->setResposeData('account_log', $datasetList);

    return $_d;
}