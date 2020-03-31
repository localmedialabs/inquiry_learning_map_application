<?php
function authentication($_d) { return getPrivateRoll();}
function validate($_d) {
    $outValidateInfo = array();
    $outValidateInfo = [
        ['type'=>'required','param_name'=>'mapkey']
    ];
    return $outValidateInfo;
}

function validateModel($_d) {
    return $_d;
}

function execute($_d) {

    //登録情報を設定
    $setRegKey = $_d->getAccountData('account_key');

    //データセットテーブル
    $datasetACLList = array();
    $datasetACLList['account_list'] = array();
    $_accountObject = new app\lib\util\datastoreHelperUtil();
    $_accountObject->setDataStoreName('USER');
    $_accountObject->setDataStoreType('ACCOUNT');
    $accountList = array();
    $accountList = $_accountObject->getDataStoreDetailList();
    array_walk($accountList, "array_col_delete", 'password');
    array_walk($accountList, "array_col_delete", 'userId');
    array_walk($accountList, "array_col_delete", 'email');
    array_walk($accountList, "array_col_delete", 'status');
    $datasetACLList['account_list'] = $accountList;

    $_datasetObject = new app\lib\util\datastoreHelperUtil();
    $_datasetObject->setDataStoreName( $_d->getRequestData('mapkey'));
    $_datasetObject->setDataStoreType('MAP');
    $_datasetObject->getAclAccountList();
    $datasetACLList['acl_setting_list'] = $_datasetObject->getAclAccountAll();

    $_d->setResposeData('acl_list', $datasetACLList);

    return $_d;
}

function array_col_delete(&$row, $key, $key_name) {
    unset($row[$key_name]);
}
