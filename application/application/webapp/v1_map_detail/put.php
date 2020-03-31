<?php
function authentication($_d) { return getPrivateRoll();}
function validate($_d) {
    $outValidateInfo = array();

    $outValidateInfo = [
        ['type'=>'required','param_name'=>'mapkey'],
        ['type'=>'required','param_name'=>'latitude'],
        ['type'=>'required','param_name'=>'longitude'],
        ['type'=>'required','param_name'=>'title'],
        ['type'=>'required','param_name'=>'contents']
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

    $inLat = (string)$_d->getRequestData('latitude');
    $inLng = (string)$_d->getRequestData('longitude');
    $setPoint = $inLat.':'.$inLng;
    $rgkey = md5($setPoint);
    //データセットテーブル
    $_datasetObject->setDataStoreName($setStoreName);
    $_datasetObject->setDataStoreType('MAP');

    $_datasetObject->setTempDataStore($rgkey, 'point_id', $rgkey);
    $_datasetObject->setTempDataStore($rgkey, 'latitude', $inLat);
    $_datasetObject->setTempDataStore($rgkey, 'longitude', $inLng);
    $_datasetObject->setTempDataStore($rgkey, 'title', $_d->getRequestData('title'));
    $_datasetObject->setTempDataStore($rgkey, 'extension', $_d->getRequestData('extension'));
    $_datasetObject->setTempDataStore($rgkey, 'marker_type', $_d->getRequestData('marker_type'));
    $_datasetObject->setTempDataStore($rgkey, 'icon_type', $_d->getRequestData('icon_type'));
    $_datasetObject->setTempDataStore($rgkey, 'contents', $_d->getRequestData('contents'));
    $_datasetObject->setAssetTempDataStore($rgkey, 'image', $_d->getRequestData('image'));

    //保存
    $_datasetObject->saveDataStore();

    $_d->setResposeData('map_marker_id', $rgkey);

    return $_d;
}