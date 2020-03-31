<?php
//業務共通処理領域
//ユーザユニークID取得
function getUserUniqueId($inUserId) {
    return  md5('user_'.$inUserId);
}
//管理者ユニークID取得
function getAdminUniqueId($inUserId) {
    return  md5('admin_'.$inUserId);
}


//操作ログ保存
function setUserActionLog($inRegKey, $inUserId, $inAction) {
    $_accountLogObject = new app\lib\util\datastoreHelperUtil();
    //アカウント操作テーブル
    $setLogRecode = date("YmdHis");
    $setAccessIp = $_SERVER['SERVER_ADDR'];
    $setAgent = $_SERVER['HTTP_USER_AGENT'];
    $_accountLogObject->setDataStoreType('ACCOUNT_LOG');
    $_accountLogObject->setDataStoreName($inRegKey);
    $_accountLogObject->setDataStoreNameLabel('操作履歴:'.$inUserId);
    $_accountLogObject->setTempDataStore($setLogRecode, 'action', $inAction);
    $_accountLogObject->setTempDataStore($setLogRecode, 'ip', $setAccessIp);
    $_accountLogObject->setTempDataStore($setLogRecode, 'user_argent', $setAgent);
    $_accountLogObject->setTempDataStore($setLogRecode, 'action_date', $setLogRecode);
    $_accountLogObject->saveDataStore();
}

//配列削除
function array_col_delete(&$row, $key, $key_name) {
    if(array_key_exists($key_name, $row) === true) {
        unset($row[$key_name]);
    }
}