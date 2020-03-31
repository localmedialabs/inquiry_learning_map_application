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

    $_mapObject = new app\lib\util\datastoreHelperUtil();
    $_mapObject->setDataStoreOwner($setRegKey);
    //データセットテーブル
    $mapList = array();

    if($_d->getRequestData('mapkey') !== '') {
        $setStoreName = $_d->getRequestData('mapkey');
        $mapList = $_mapObject->getDataStoreInfoData('MAP',$setStoreName);
    } else {
        $mapList = $_mapObject->getDataStoreInfoList('MAP');
    }

    $_d->setResposeData('maplist', $mapList);

    return $_d;
}