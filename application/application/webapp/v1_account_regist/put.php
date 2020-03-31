<?php
function validate($_d) {
    $outValidateInfo = array();

    $outValidateInfo = [
        ['type'=>'required','param_name'=>'name']
       ,['type'=>'required','param_name'=>'userId']
       ,['type'=>'required','param_name'=>'group']
       ,['type'=>'required','param_name'=>'password']
    ];
    return $outValidateInfo;
}

function validateModel($_d) {

    //アカウント重複チェック
    $setRegKey = md5('user_'.$_d->getRequestData('userId'));
    $_accountObject = new app\lib\util\datastoreHelperUtil();
    //アカウントテーブル
    $_accountObject->setDataStoreName('USER');
    $_accountObject->setDataStoreType('ACCOUNT');
    $_accountObject->loadDataStoreDetail($setRegKey);

    $account_check = false;
    $account_error_list = array();
    if($_accountObject->getExecStatus() === true){
        $account_check = false;
        $accountInfo = array();
        $accountInfo = $_accountObject->getTempDataStoreAll();
        if(array_key_exists('userId',$accountInfo) === true) {
            $wExp = new app\lib\exception\warningException();
            $wExp->setErrorInfo('userId','','このIDは既に登録されています。');
            throw $wExp;
        }
    }

    return $_d;
}

function execute($_d) {

    //キー値発行（ユーザIDをハッシュ化し一意性を担保
    $setRegKey = md5('user_'.$_d->getRequestData('userId'));

    $password = $_d->getRequestData('password');
    $password = md5($password);
    $password = md5($password);
    $_accountObject = new app\lib\util\datastoreHelperUtil();
    //アカウントテーブル
    $_accountObject->setDataStoreType('ACCOUNT');
    $_accountObject->setDataStoreName('USER');
    $_accountObject->setDataStoreNameLabel('ユーザアカウント');
    $_accountObject->setTempDataStore($setRegKey, 'uid', $setRegKey);
    $_accountObject->setTempDataStore($setRegKey, 'name', $_d->getRequestData('name'));
    $_accountObject->setTempDataStore($setRegKey, 'userId', $_d->getRequestData('userId'));
    $_accountObject->setTempDataStore($setRegKey, 'group', $_d->getRequestData('group'));
    $_accountObject->setTempDataStore($setRegKey, 'password', $password);
    $_accountObject->setTempDataStore($setRegKey, 'email', '');
    $_accountObject->setTempDataStore($setRegKey, 'status', true);
    //保存
    $_accountObject->saveDataStore();

    $_datasetObject = new app\lib\util\datastoreHelperUtil();
    $setStoreName = $_d->getRequestData('group');

    //共有地図作成
    $_datasetObject->setDataStoreName($setStoreName);
    $_datasetObject->setDataStoreType('MAP');
    $_datasetObject->setDataStoreNameLabel($setStoreName.'用共有地図');
    $_datasetObject->setDataStoreDescription($setStoreName.'用共有地図');
    $_datasetObject->getDataStoreAcl('protected');
    $_datasetObject->setDataStoreOwner($setRegKey);
    //保存
    $_datasetObject->saveDataStoreInfo();

    //MAPACLデータ
    $_mapObject = new app\lib\util\datastoreHelperUtil();
    $mapList = array();
    $_mapObject->setDataStoreType('MAP');
    $_mapObject->setDataStoreName($setStoreName);
    $_mapObject->setAclAccount($setRegKey);
    $_mapObject->saveDataStoreAcl();

    $_d->setResposeData('uid', $setRegKey);

    return $_d;
}