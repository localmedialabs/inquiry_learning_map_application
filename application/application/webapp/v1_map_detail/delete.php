<?php
function authentication($_d) { return getPrivateRoll();}
function validate($_d) {
    $outValidateInfo = array();

    $outValidateInfo = [
        ['type'=>'required','param_name'=>'mapkey'],
        ['type'=>'required','param_name'=>'latitude'],
        ['type'=>'required','param_name'=>'longitude'],
    ];
    return $outValidateInfo;
}

function validateModel($_d) {
    return $_d;
}

function execute($_d) {

    $_datasetObject = new app\lib\util\datastoreHelperUtil();
    $setStoreName = $_d->getRequestData('mapkey');
    $accountKey = '';
    $accountKey = $_d->getAccountData('account_key');

    $rgkey = md5($_d->getRequestData('latitude').':'.$_d->getRequestData('longitude'));
    //データセットテーブル
    $_datasetObject->setDataStoreName($setStoreName);
    $_datasetObject->setDataStoreType('MAP');
    //削除
    $_datasetObject->deleteDataStoreData($rgkey);

    $_d->setResposeData('map_delete_marker_id', $rgkey);

    return $_d;
}