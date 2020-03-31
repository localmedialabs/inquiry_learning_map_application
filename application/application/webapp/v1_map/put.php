<?php
function authentication($_d) { return getPrivateRoll();}
function validate($_d) {
    $outValidateInfo = array();

    $outValidateInfo = [
        ['type'=>'required','param_name'=>'title'],
        ['type'=>'required','param_name'=>'description']
    ];
    return $outValidateInfo;
}

function validateModel($_d) {
    return $_d;
}

function execute($_d) {

    $_datasetObject = new app\lib\util\datastoreHelperUtil();
    $accountData = '';
    $accountData = $_d->getAccountData('account_key');
    $setStoreName = $accountData.'_'.md5(microtime());

    //データセットテーブル
    $_datasetObject->setDataStoreName($setStoreName);
    $_datasetObject->setDataStoreType('MAP');
    $_datasetObject->setDataStoreNameLabel($_d->getRequestData('title'));
    $_datasetObject->setDataStoreDescription($_d->getRequestData('description'));
    $_datasetObject->getDataStoreAcl('private');
    $_datasetObject->setDataStoreOwner($accountData);
    //保存
    $_datasetObject->saveDataStoreInfo();

    //$_d->setResposeData(キー名,値※配列も可)　でレスポンス用のデータに設定
    $_d->setResposeData('mapkey', $setStoreName);

    return $_d;
}