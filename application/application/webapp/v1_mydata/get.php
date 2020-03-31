<?php
function authentication($_d) { return getPrivateRoll();}

function execute($_d) {
    //データセットテーブル
    $setRegKey = $_d->getAccountData('account_key');
    $datasetACLList = array();
    $datasetACLList['account_list'] = array();
    $_accountObject = new app\lib\util\datastoreHelperUtil();
    $_accountObject->setDataStoreName('USER');
    $_accountObject->setDataStoreType('ACCOUNT');
    $accountList = array();
    $accountList = $_accountObject->loadDataStoreDetail($setRegKey);
    $accountList = $_accountObject->getTempDataStoreAll();
    array_walk($accountList, "array_col_delete", 'password');

    $_d->setResposeData('mydata', $accountList);

    return $_d;
}