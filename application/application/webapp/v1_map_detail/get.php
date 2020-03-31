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
    $setStoreName = $_d->getRequestData('mapkey');

    $getMode = $_d->getRequestData('get_mode');
    $_mapObject = new app\lib\util\datastoreHelperUtil();
    $_mapObject->setDataStoreOwner($setRegKey);
    $_mapObject->setDataStoreName($setStoreName);
    $_mapObject->setDataStoreType('MAP');
    //データセットテーブル
    $datasetList = array();
    if ($getMode === 'detail') {
        $pointId = (string)$_d->getRequestData('point_id');
        $datasetList = $_mapObject->getDataStoreDetailList($pointId);
        if(count($datasetList) === 1) {
          $datasetList[0]['image'] = $_mapObject->getDataStoreDetailAsset($datasetList[0]['image']);
        }
    } else {
        $datasetList = $_mapObject->getDataStoreDetailList();
    }

    if ($getMode === 'index') {
        array_walk($datasetList, "array_col_delete", 'contents');
        array_walk($datasetList, "array_col_delete", 'image');
    }

    $_d->setResposeData('map_detail_list', $datasetList);

    return $_d;
}