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
    $datasetACLList = array();
    $datasetACLList['account_list'] = array();
    $_accountObject = new app\lib\util\datastoreHelperUtil();
    $_accountObject->setDataStoreName('USER');
    $_accountObject->setDataStoreType('ACCOUNT');
    $accountList = array();
    $accountList = $_accountObject->loadDataStoreDetail($setRegKey);
    $accountList = $_accountObject->getTempDataStoreAll();
    $groupKey = $accountList['group'];

    $getMode = $_d->getRequestData('get_mode');


    $_datasetObject = new app\lib\util\datastoreHelperUtil();
    //データセットテーブル
    $datasetList = array();
    $_datasetObject->setDataStoreOwner($setRegKey);
    $datasetList = $_datasetObject->getDataStoreInfoList('DATASET');
    $outDatasetList = array();

    foreach($datasetList as $dkey => $dval) {
        if($groupKey !== '') {
            if(strpos($dval['store'], $groupKey) !== false) {
                $outDatasetList[] = $dval;
            }
        }
    }

    if($getMode === 'common') {
        foreach($datasetList as $dkey => $dval) {
            if(strpos($dval['store'], 'common') !== false) {
                $outDatasetList[] = $dval;
            }
        }
    }

    $_d->setResposeData('dataset', $outDatasetList);

    return $_d;
}