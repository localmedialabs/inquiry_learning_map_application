<?php
namespace app\lib\util;

/**
 * データストア処理補助クラス
 *
 * ローカル環境のJSONなどデータ処理を補助するクラス
 *
 * @package
 * @access  public
 * @author  Kota Toda
 * @create  2019/12/3
 **/
class datastoreHelperUtil {

    //ターゲットベースパス
    private $_TARGET_STORE_BASE_PATH = '';

    //データストア名
    private $_TARGET_STORE_TYPE = '';
    //データストア名
    private $_TARGET_STORE_NAME = '';
    //データストア名(和名)
    private $_TARGET_STORE_NAME_LABEL = '';
    //データストア名オーナーID
    private $_TARGET_STORE_OWNER = '';
    //データストア名ACL
    private $_TARGET_STORE_ACL = '';
    //データストア名説明
    private $_TARGET_STORE_DESCRIPTION = '';
    //データストア名説明
    private $_TARGET_STORE_META = array();

    //データストア構成名：インデックス領域(ディレクトリ名)
    private $_STORE_INDEX_NAME = 'index';
    //データストア構成名：詳細情報領域(ディレクトリ名)
    private $_STORE_DETAIL_NAME = 'detail';
    //データストア構成名：ファイル管理領域(ディレクトリ名)
    private $_STORE_ASSET_NAME = 'asset';
    //データストア構成名：アクセスコントロール領域(ディレクトリ名)
    private $_STORE_ACL_NAME = 'acl';
    //データストア構成名：一次作業領域(ディレクトリ名)
    private $_STORE_TEMP_NAME = 'temp';
    //データストア構成・権限領域情報(ファイル名)
    private $_STORE_INFO_NAME = 'info.json';

    //データ(一次領域)
    private $_TEMP_DATA_STORE = array();
    private $_TEMP_DATA_ASSET_STORE = array();
    //データ(ACL一次領域)
    private $_TEMP_DATA_STORE_ACL_ACCOUNT = array();

    //処理結果
    private $_CURRENT_RESULT_STATUS = false;
    private $_CURRENT_RESULT_NAME = '';


    function __construct() {
        $this->_TARGET_STORE_BASE_PATH = realpath(dirname(__FILE__) .'/../../etc/datastore');
    }
    //アクセサメソッド（データストア名設定）
    public function setDataStoreName($inValue) {
        $this->_TARGET_STORE_NAME = $inValue;
    }
    //アクセサメソッド（データストア名設定）
    public function getDataStoreName() {
        return $this->_TARGET_STORE_NAME;
    }
    //アクセサメソッド（データストア名設定）
    public function setDataStoreNameLabel($inValue) {
        $this->_TARGET_STORE_NAME_LABEL = $inValue;
    }
    //アクセサメソッド（データストア名設定）
    public function getDataStoreNameLabel() {
        return $this->_TARGET_STORE_NAME_LABEL;
    }
    //アクセサメソッド（データストア名設定）
    public function setDataStoreType($inValue) {
        $this->_TARGET_STORE_TYPE = $inValue;
    }
    //アクセサメソッド（データストア名設定）
    public function getDataStoreType() {
        return $this->_TARGET_STORE_TYPE;
    }

    //アクセサメソッド（オーナー名設定）
    public function setDataStoreOwner($inValue) {
        $this->_TARGET_STORE_OWNER = $inValue;
    }
    //アクセサメソッド（オーナー名設定）
    public function getDataStoreOwner() {
        return $this->_TARGET_STORE_OWNER;
    }


    //アクセサメソッド（ACL設定）
    public function setDataStoreAcl($inValue) {
        $this->_TARGET_STORE_ACL = $inValue;
    }
    //アクセサメソッド（ACL設定）
    public function getDataStoreAcl() {
        return $this->_TARGET_STORE_ACL;
    }
    //アクセサメソッド（説明設定）
    public function setDataStoreDescription($inValue) {
        $this->_TARGET_STORE_DESCRIPTION = $inValue;
    }
    //アクセサメソッド（説明設定）
    public function getDataStoreDescription() {
        return $this->_TARGET_STORE_DESCRIPTION;
    }

    //アクセサメソッド（メタ情報設定）
    public function setDataStoreMeta($inValue) {
        $this->_TARGET_STORE_META = $inValue;
    }
    //アクセサメソッド（メタ情報設定）
    public function getDataStoreMeta() {
        return $this->_TARGET_STORE_META;
    }

    //アクセサメソッド（処理ステータス）
    public function getExecStatus() {
        return $this->_CURRENT_RESULT_STATUS;
    }

    //アクセサメソッド（データストアインデックスデータ読み込み取得）
    public function loadDataStoreIndex() {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'loadDataStoreIndex';

        $targetDataStorePath = $this->_TARGET_STORE_BASE_PATH.'/'.$this->_TARGET_STORE_TYPE.'/'.$this->_TARGET_STORE_NAME;
        $targetDataStoreIndexPath = $targetDataStorePath.'/'.$this->_STORE_INDEX_NAME;
        $targetDataStoreIndex = array();
        if(is_file($targetDataStoreIndexPath) === true) {
            $targetDataStoreIndex = file_get_contents($targetDataStoreIndexPath);
        }
        $this->_TEMP_DATA_STORE = json_decode($targetDataStoreIndex,true);
        $this->_CURRENT_RESULT_STATUS = true;
    }

    //アクセサメソッド（データストア読み込み取得）
    public function loadDataStoreDetail($inDetailId) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'loadDataStoreDetail';

        $targetDataStorePath = $this->_TARGET_STORE_BASE_PATH.'/'.$this->_TARGET_STORE_TYPE.'/'.$this->_TARGET_STORE_NAME.'/'.$this->_STORE_DETAIL_NAME;
        $targetDataStoreDetailPath = $targetDataStorePath.'/'.$inDetailId.'.json';
        $targetDataStoreDetailStr = '';
        if(is_file($targetDataStoreDetailPath) === true) {
            $targetDataStoreDetailStr = file_get_contents($targetDataStoreDetailPath);
            if(trim($targetDataStoreDetailStr) !== '') {
                $this->_TEMP_DATA_STORE = json_decode($targetDataStoreDetailStr,true);
            }
        }
        $this->_CURRENT_RESULT_STATUS = true;
    }

    //データストアのリストデータ取得
    public function getDataStoreDetailList($inKey = '',$inOrderList = null) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'getDataStoreDetailList';

        $targetDataStorePath = $this->_TARGET_STORE_BASE_PATH.'/'.$this->_TARGET_STORE_TYPE.'/'.$this->_TARGET_STORE_NAME.'/'.$this->_STORE_DETAIL_NAME;
        $outDataList = array();
        $targetDataList = array();
        $targetDataList = $this->getFileDataKeyList($targetDataStorePath);
        foreach($targetDataList as $tdKey => $tdVal) {
            $targetDataStoreDetailPath = $tdVal['full_path'];
            $targetDataStoreDetailStr = '';
            if ($inKey !== '') {
                $targetKey = $tdVal['name'];
//                var_dump(basename("$targetKey",".json").'|'.$inKey);
                if ( basename("$targetKey",".json") !== $inKey) {
                    continue;
                }
            }
            if(is_file($targetDataStoreDetailPath) === true) {
                $targetDataStoreDetailStr = file_get_contents($targetDataStoreDetailPath);
                if(trim($targetDataStoreDetailStr) !== '') {
                    if ($inOrderList === null) {
                        $outDataList[] = json_decode($targetDataStoreDetailStr,true);
                    } else {
                        if (is_array($inOrderList) === true) {
                            $targetList = array();
                            $targetList = json_decode($targetDataStoreDetailStr,true);
                            $rsFlg = true;
                            foreach ($inOrderList as $okey => $ovalue) {
                                if ($targetList[$okey] !== $ovalue) {
                                    $rsFlg = false;
                                }
                            }
                            if($rsFlg === true) {
                                $outDataList[] = $targetList;
                            }
                        }
                    }
                }
            }
        }
        $this->_CURRENT_RESULT_STATUS = true;
        return $outDataList;
    }
    //アセットデータ取得
    public function getDataStoreDetailAsset($inKey) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'getDataStoreDetailList';
        $outData = '';
        $targetDataStorePath = $this->_TARGET_STORE_BASE_PATH.'/'.$this->_TARGET_STORE_TYPE.'/'.$this->_TARGET_STORE_NAME.'/'.$this->_STORE_ASSET_NAME;
        $targetDataAssetPath = $targetDataStorePath.'/'.$inKey;
        if (is_file($targetDataAssetPath) === true) {
            $outData = file_get_contents($targetDataAssetPath);
        }
        $this->_CURRENT_RESULT_STATUS = true;
        return $outData;
    }
    //（データストア初期設定）※再設定可能な構造
    private function initDataStore($inTargetStoreType, $inTargetStore, $inTargetStoreName) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'initDataStore';

        $targetDataStorePath = $this->_TARGET_STORE_BASE_PATH.'/'.$inTargetStoreType;
        if (is_dir($targetDataStorePath) === false) {
            mkdir($targetDataStorePath);
        }
        $targetDataStorePath = $targetDataStorePath.'/'.$inTargetStore;
        if (is_dir($targetDataStorePath) === false) {
            mkdir($targetDataStorePath);
        }
        //インデックス領域
        $targetDataStoreIndexPath = $targetDataStorePath.'/'.$this->_STORE_INDEX_NAME;
        if (is_dir($targetDataStoreIndexPath) === false) {
            mkdir($targetDataStoreIndexPath);
        }
        //詳細領域
        $targetDataStoreDetailPath = $targetDataStorePath.'/'.$this->_STORE_DETAIL_NAME;
        if (is_dir($targetDataStoreDetailPath) === false) {
            mkdir($targetDataStoreDetailPath);
        }
        //TEMP領域
        $targetDataStoreTempPath = $targetDataStorePath.'/'.$this->_STORE_TEMP_NAME;
        if (is_dir($targetDataStoreTempPath) === false) {
            mkdir($targetDataStoreTempPath);
        }
        //アセット領域
        $targetDataStoreAssetPath = $targetDataStorePath.'/'.$this->_STORE_ASSET_NAME;
        if (is_dir($targetDataStoreAssetPath) === false) {
            mkdir($targetDataStoreAssetPath);
        }
        //ACL領域
        $targetDataStoreAclPath = $targetDataStorePath.'/'.$this->_STORE_ACL_NAME;
        if (is_dir($targetDataStoreAclPath) === false) {
            mkdir($targetDataStoreAclPath);
        }
        //INFO領域(JSON)
        $targetDataStoreTempPath = $targetDataStorePath.'/'.$this->_STORE_INFO_NAME;
        if (is_file($targetDataStoreTempPath) === false) {
            $this->saveFileData($targetDataStoreTempPath, $this->getInitDataStoreInfo($inTargetStoreType, $inTargetStore, $inTargetStoreName));
        }
        $this->_CURRENT_RESULT_STATUS = true;
    }
    //初期INFOデータ情報
    private function getInitDataStoreInfo($inTargetStoreType, $inTargetStore,$inTargetName,$inOwner = '',$inACL = 'private') {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'getInitDataStoreInfo';

        $outInfoList = array();
        $outInfoList['store'] = $inTargetStore;
        $outInfoList['name'] = $inTargetName;
        $outInfoList['type'] = $inTargetStoreType;
        $outInfoList['description'] = '';
        $outInfoList['meta'] = array();
        $outInfoList['column_info'] = array();
        $outInfoList['acl'] = $inACL;
        $outInfoList['owner'] = $inOwner;
        $outInfoList['create_date'] = date("YmdHis");
        $outInfoList['update_date'] = date("YmdHis");

        $this->_CURRENT_RESULT_STATUS = true;
        return json_encode($outInfoList);
    }

    //データストアパス
    private function getDataStorePath($inTargetStoreType, $inTargetStore) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'getDataStorePath';
        $outTargetDataStoreInfoPath = $this->_TARGET_STORE_BASE_PATH.'/'.$inTargetStoreType.'/'.$inTargetStore;
        $this->_CURRENT_RESULT_STATUS = true;
        return $outTargetDataStoreInfoPath;
    }


    //データストア基本情報パス
    private function getDataStoreInfoPath($inTargetStoreType, $inTargetStore) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'getDataStoreInfoPath';
        $outTargetDataStoreInfoPath = $this->getDataStorePath($inTargetStoreType, $inTargetStore).'/'.$this->_STORE_INFO_NAME;
        $this->_CURRENT_RESULT_STATUS = true;
        return $outTargetDataStoreInfoPath;
    }

    //INFOデータ情報取得(JSON 配列変換)
    private function loadDataStoreInfo($inTargetStoreType, $inTargetStore) {
        $targetDataStorePath = $this->getDataStoreInfoPath($inTargetStoreType, $inTargetStore);

        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'loadDataStoreInfo';

        $outInfoListStr = '';
        if(is_file($targetDataStorePath) === true) {
            $outInfoListStr = file_get_contents($targetDataStorePath);
        }
        $this->_CURRENT_RESULT_STATUS = true;
        return json_decode($outInfoListStr, true);
    }

    //INFOデータ一覧情報(JSON 配列変換)
    public function getDataStoreInfoList($inTargetStoreType) {
        $targetDataStorePath =  $this->_TARGET_STORE_BASE_PATH.'/'.$inTargetStoreType;

        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'getDataStoreInfoList';

        $infoDataList = $this->getFileDataInfoList($targetDataStorePath);
        $outInfoList = [];
        foreach ($infoDataList as $ikey => $ival) {
          $outInfoList[] = $this->loadDataStoreInfo($inTargetStoreType,$ival['info_dir']);
        }
        $this->_CURRENT_RESULT_STATUS = true;
        return $outInfoList;
    }
    //INFOデータ情報(JSON 配列変換)
    public function getDataStoreInfoData($inTargetStoreType, $inTargetStore) {

        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'getDataStoreInfoData';

        $outInfoList = [];
        $outInfoList[] = $this->loadDataStoreInfo($inTargetStoreType, $inTargetStore);

        $this->_CURRENT_RESULT_STATUS = true;
        return $outInfoList;
    }

    //INFOデータ更新
    private function setInitDataStoreInfo($inTargetStoreType, $inTargetStore,$inTargetName = '',$inDescription = '',$inACL = '',$inOwner = '') {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'setInitDataStoreInfo';

        $outInfoList = array();

        $outInfoList = $this->loadDataStoreInfo($inTargetStoreType, $inTargetStore);
        $outInfoList['type'] = $inTargetStoreType;
        $outInfoList['store'] = $inTargetStore;
        if($inTargetName !== '') {
            $outInfoList['name'] = $inTargetName;
        }
        $outInfoList['meta'] = array();
        if($inDescription !== '') {
            $outInfoList['description'] = $inDescription;
        }
        $outInfoList['column_info'] = array();
        if($inACL !== '') {
            $outInfoList['acl'] = $inACL;
        }
        if($inOwner !== '') {
            $outInfoList['owner'] = $inOwner;
        }
        $outInfoList['update_date'] = date("YmdHis");

        $this->_CURRENT_RESULT_STATUS = true;
        return json_encode($outInfoList);
    }
    //INFOデータ更新保存//////////////////////////////////////////////////////////////////////////////////////
    //$inMode = 'index':インデックスも更新
    public function saveDataStoreInfo() {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'saveDataStoreInfo';

        $this->initDataStore($this->_TARGET_STORE_TYPE, $this->_TARGET_STORE_NAME,$this->_TARGET_STORE_NAME_LABEL, $this->_TARGET_STORE_OWNER);

        $targetPath = '';
        $targetPath = $this->getDataStoreInfoPath($this->_TARGET_STORE_TYPE, $this->_TARGET_STORE_NAME);

        $targetData = array();
        $targetData = $this->setInitDataStoreInfo($this->_TARGET_STORE_TYPE, $this->_TARGET_STORE_NAME, $this->_TARGET_STORE_NAME_LABEL, $this->_TARGET_STORE_DESCRIPTION, $this->_TARGET_STORE_ACL, $this->_TARGET_STORE_OWNER);

        $this->saveFileData($targetPath,$targetData);
        $this->_CURRENT_RESULT_STATUS = true;
    }
    //データストア削除
    public function deleteDataStore() {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'deleteDataStore';
        $targetPath = '';
        if ($this->_TARGET_STORE_TYPE !== '' && $this->_TARGET_STORE_NAME !== '') {
            $targetPath = $this->getDataStorePath($this->_TARGET_STORE_TYPE, $this->_TARGET_STORE_NAME);
            $this->deleteFile ($targetPath);
        }
        $this->_CURRENT_RESULT_STATUS = true;
    }


    //ACL関連//////////////////////////////////////////////////////////////////////////////////////
    private function getDataStoreAclPath($inTargetStoreType, $inTargetStore) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'getDataStoreAclPath';
        $outTargetDataStoreAclPath = $this->getDataStorePath($inTargetStoreType, $inTargetStore).'/'.$this->_STORE_ACL_NAME;
        $this->_CURRENT_RESULT_STATUS = true;
        return $outTargetDataStoreAclPath;
    }

    //データストアに対するACLアカウントの設定
    public function setAclAccount($inAccount) {
        $this->_TEMP_DATA_STORE_ACL_ACCOUNT[$inAccount] = $inAccount;
    }
    public function getAclAccount($inAccount) {
        return $this->_TEMP_DATA_STORE_ACL_ACCOUNT[$inAccount];
    }
    public function getAclAccountAll() {
        return $this->_TEMP_DATA_STORE_ACL_ACCOUNT;
    }
    //ACLデータ
    public function getAclAccountList() {
        $targetPath = $this->getDataStorePath($this->_TARGET_STORE_TYPE, $this->_TARGET_STORE_NAME);
        foreach($this->getFileDataAclList($targetPath) as $ackey => $acVal) {
            $this->_TEMP_DATA_STORE_ACL_ACCOUNT[] = $ackey;
        }

    }

    //ACLデータ保存
    public function saveDataStoreAcl($inMode = '') {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'saveDataStoreInfo';

        $this->initDataStore($this->_TARGET_STORE_TYPE, $this->_TARGET_STORE_NAME,$this->_TARGET_STORE_NAME_LABEL, $this->_TARGET_STORE_OWNER);

        $targetPath = '';
        $targetPath = $this->getDataStoreAclPath($this->_TARGET_STORE_TYPE, $this->_TARGET_STORE_NAME);

        foreach ($this->_TEMP_DATA_STORE_ACL_ACCOUNT as $ackey => $acVal) {
            $targetSavePath = $targetPath.'/'.$ackey;
            $this->saveFileData($targetSavePath,$acVal);
        }
        $this->_CURRENT_RESULT_STATUS = true;
    }
    //ACLチェック
    private function checkAclAccount($inAccountKey, $inTargetStoreType, $inTargetStore) {
        $checkResult = false;
        $outInfoList = array();
        $outInfoList = $this->loadDataStoreInfo($inTargetStoreType, $inTargetStore);

        $inACLType = $outInfoList['acl'];

        switch ($inACLType) {
            case 'public':
              $checkResult = true;
            break;
            case 'protected':
              $checkResult = false;
              $targetPath = $this->getDataStorePath($inTargetStoreType, $inTargetStore);
              $targetPath = $this->getFileDataAclList($targetPath);
              //存在チェック
              if(array_key_exists($inAccountKey, $targetPath) === true) {
                $checkResult = true;
              }
              break;
            case 'private':
              $checkResult = false;
              if($outInfoList === $inAccountKey) {
                $checkResult = true;
              }
            break;
            case 'admin':
              $checkResult = false;
            break;
        }

        return $checkResult;
    }


    //データセット(一次領域にデータをセットする)
    //$inRecKey:レコードキー $inKey：項目名, $inValue：項目値
    public function setTempDataStore($inRecKey, $inKey, $inValue) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'setTempDataStore';

        if (array_key_exists($this->_TARGET_STORE_NAME, $this->_TEMP_DATA_STORE) === false) {
            $this->_TEMP_DATA_STORE[$this->_TARGET_STORE_NAME] = array();
        }
        if (array_key_exists($inRecKey, $this->_TEMP_DATA_STORE[$this->_TARGET_STORE_NAME]) === false) {
            $this->_TEMP_DATA_STORE[$this->_TARGET_STORE_NAME][$inRecKey] = array();
        }
        $this->_TEMP_DATA_STORE[$this->_TARGET_STORE_NAME][$inRecKey][$inKey] = $inValue;
        $this->_CURRENT_RESULT_STATUS = true;
    }
    //アセット
    public function setAssetTempDataStore($inRecKey, $inKey, $inValue) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'setAssetTempDataStore';

        if (array_key_exists($this->_TARGET_STORE_NAME, $this->_TEMP_DATA_STORE) === false) {
            $this->_TEMP_DATA_STORE[$this->_TARGET_STORE_NAME] = array();
        }
        if (array_key_exists($inRecKey, $this->_TEMP_DATA_STORE[$this->_TARGET_STORE_NAME]) === false) {
            $this->_TEMP_DATA_STORE[$this->_TARGET_STORE_NAME][$inRecKey] = array();
        }
        $this->_TEMP_DATA_STORE[$this->_TARGET_STORE_NAME][$inRecKey][$inKey] = $inRecKey.'_'.$inKey;
        $this->_TEMP_DATA_ASSET_STORE[$this->_TARGET_STORE_NAME][$inRecKey][$inKey] = $inValue;
        //var_dump($this->_TEMP_DATA_ASSET_STORE);
        //var_dump($inValue);
        $this->_CURRENT_RESULT_STATUS = true;
    }


    public function getTempDataStoreAll() {
        return $this->_TEMP_DATA_STORE;
    }

    //データセット(一次領域のデータをリセットする)
    public function clearTempDataStore() {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'clearTempDataStore';

        if (array_key_exists($this->_TARGET_STORE_NAME, $this->_TEMP_DATA_STORE) === true) {
            $this->_TEMP_DATA_STORE[$this->_TARGET_STORE_NAME] = array();
        }
        if (array_key_exists($this->_TARGET_STORE_NAME, $this->_TEMP_DATA_ASSET_STORE) === true) {
            $this->_TEMP_DATA_ASSET_STORE[$this->_TARGET_STORE_NAME] = array();
        }
        $this->_CURRENT_RESULT_STATUS = true;
    }
    //データストア削除//////////////////////////////////////////////////////////////////////////////////////
    //データストアデータ削除
    public function deleteDataStoreData($inDeleteKey) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'deleteDataStoreData';
        $targetPath = '';
        if ($this->_TARGET_STORE_TYPE !== '' && $this->_TARGET_STORE_NAME !== '') {
            $targetDataStoreBasePath = $this->getDataStorePath($this->_TARGET_STORE_TYPE, $this->_TARGET_STORE_NAME);
            $targetDataStorePath = $targetDataStoreBasePath.'/'.$this->_STORE_DETAIL_NAME.'/'.$inDeleteKey.'.json';
            $this->deleteFile ($targetDataStorePath);
            if (is_file($targetDataStorePath) === true) {
                unlink($targetDataStorePath);
            }
        }
        $this->_CURRENT_RESULT_STATUS = true;
    }

    //データストア保存//////////////////////////////////////////////////////////////////////////////////////
    //$inMode = 'index':インデックスも更新
    public function saveDataStore($inMode = 'index') {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'saveDataStore';

        $this->initDataStore($this->_TARGET_STORE_TYPE, $this->_TARGET_STORE_NAME,$this->_TARGET_STORE_NAME_LABEL, $this->_TARGET_STORE_OWNER);

        //データストア
        $targetDataStoreBasePath = $this->_TARGET_STORE_BASE_PATH.'/'.$this->_TARGET_STORE_TYPE.'/'.$this->_TARGET_STORE_NAME;
        //詳細
        foreach($this->_TEMP_DATA_STORE[$this->_TARGET_STORE_NAME] as $dskey => $dsvalue) {
            $setDetailData = $dsvalue;
            $setDetailData['update_date'] = date("YmdHis");
            $setDetailDataStr = json_encode($setDetailData);
            $targetDataStorePath = $targetDataStoreBasePath.'/'.$this->_STORE_DETAIL_NAME.'/'.$dskey.'.json';
            $this->saveFileData($targetDataStorePath, $setDetailDataStr);
        }
        //画像
        //var_dump($this->_TEMP_DATA_ASSET_STORE);
        foreach($this->_TEMP_DATA_ASSET_STORE[$this->_TARGET_STORE_NAME] as $dskey => $dsvalue) {
            foreach($dsvalue as $dssubkey => $dssubvalue) {
                $assetFileName = $dskey.'_'.$dssubkey;
                $setDetailDataStr = $dssubvalue;
                $targetDataStorePath = $targetDataStoreBasePath.'/'.$this->_STORE_ASSET_NAME.'/'.$assetFileName;
                $this->saveFileData($targetDataStorePath, $setDetailDataStr);
            }
        }
        if($inMode === 'index') {
          //インデックスファイルマージ
          $this->margeDataStoreIndex($targetDataStoreBasePath);
        }
        $this->_CURRENT_RESULT_STATUS = true;
    }
    //データストアINDEXファイル生成
    private function margeDataStoreIndex($inTargetPath) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'margeDataStoreIndex';

        $margeIndexList = array();
        $margeFileList = array();
        $margeFileList = $this->getFileDataKeyList($inTargetPath.'/'.$this->_STORE_DETAIL_NAME);
        foreach($margeFileList as $mikey => $mivalue) {
            if(is_file($mivalue['full_path']) === '') {
                $margeTargetJsonStr = $mivalue['contents'];
                $margeIndexList[] = json_decode($margeTargetJsonStr,true);
            }
        }
        $margeTargetPath = $inTargetPath.'/'.$this->_STORE_INDEX_NAME;
        $setDetailDataStr = json_encode($margeIndexList);
        $targetDataStorePath = $margeTargetPath.'/index.json';
        $this->saveFileData($targetDataStorePath, $setDetailDataStr);
        unset($margeIndexList);
        unset($setDetailDataStr);
        $this->_CURRENT_RESULT_STATUS = true;
    }

    //ファイル作成・保存
    private function saveFileData($inPath, $inData, $inMode = "w+") {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'saveFileData';

        $fh = fopen($inPath,$inMode);
        fwrite($fh,$inData);
        fclose($fh);
        $this->_CURRENT_RESULT_STATUS = true;
    }

    //JSONファイル一覧取得
    private function getFileDataKeyList($inPath) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'getFileDataKeyList';

        $outFileList = array();
        $rsDir = opendir($inPath);
        while ( $file_name = readdir($rsDir) ) {
            //隠しファイルは対象外
            if (substr($file_name,0,1) !== '.') {
                $targetFilePath = $inPath.'/'.$file_name;
                if(is_file($targetFilePath) === true) {
                   $fileInfo = pathinfo($targetFilePath);
                   if($fileInfo['extension'] === 'json') {
                       //$outFileList[$fileInfo['filename']] = $targetFilePath;
                       $outFileList[] = $this->getFileInfo($targetFilePath);
                   }
                }
            }
        }
        $this->_CURRENT_RESULT_STATUS = true;
        return $outFileList;
    }
    //infoファイル・ディレクトリ一覧
    private function getFileDataInfoList($inPath) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'getFileDataInfoList';
        $outFileList = array();
        $rsDir = opendir($inPath);
        while ( $dir_name = readdir($rsDir) ) {
            //隠しファイルは対象外
            if (substr($dir_name,0,1) !== '.') {
                $targetDirPath = $inPath.'/'.$dir_name;
                if(is_dir($targetDirPath) === true) {
                    $targetFilePath = $targetDirPath.'/'.$this->_STORE_INFO_NAME;
                    if(is_file($targetFilePath) === true) {
                        $setFileInfo = array();
                        $setFileInfo = $this->getFileInfo($targetFilePath);
                        $setFileInfo['info_dir'] = $dir_name;

                        $outFileList[] = $setFileInfo;
                    }
                }
            }
        }
        $this->_CURRENT_RESULT_STATUS = true;
        return $outFileList;
    }
    //ACLファイル一覧
    private function getFileDataAclList($inPath) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'getFileDataAclList';
        $outFileList = array();
        $setPath = $inPath.'/'.$this->_STORE_ACL_NAME;
        $rsDir = opendir($setPath);
        while ( $file_name = readdir($rsDir) ) {
            //隠しファイルは対象外
            if (substr($file_name,0,1) !== '.') {
                $targetFilePath = $setPath.'/'.$file_name;
                if(is_file($targetFilePath) === true) {
                    $outFileList[$file_name] = $targetFilePath;
                }
            }
        }
        $this->_CURRENT_RESULT_STATUS = true;
        return $outFileList;
    }
    //ファイル情報を取得
    private function getFileInfo($inFilePath) {
        $this->_CURRENT_RESULT_STATUS = false;
        $this->_CURRENT_RESULT_NAME = 'getFileInfo';
        $outFileInfo = array();
        if (is_file($inFilePath) === true) {
          $fileTime = filemtime($inFilePath);
          $outFileInfo['name'] = basename($inFilePath);
          $outFileInfo['directory'] = dirname($inFilePath);
          $outFileInfo['full_path'] = $inFilePath;
          $outFileInfo['date'] = date("YmdHis",$fileTime);
          $outFileInfo['size'] = filesize($inFilePath);
          $outFileInfo['contents'] = file_get_contents($inFilePath);
        }
        $this->_CURRENT_RESULT_STATUS = true;
        return $outFileInfo;
    }

    //ディレクトリ削除
    private function deleteFile ($inFilePath) {
        $targetFiles = array_diff(scandir($inFilePath), array('.','..'));
        foreach ($targetFiles as $fileDtl) {
            //ディレクトリ
            if (is_dir("$inFilePath/$fileDtl") === true) {
              $this->deleteFile("$inFilePath/$fileDtl");
            } else {
              unlink("$inFilePath/$fileDtl");
            }
        }
        // 指定したディレクトリを削除
        return rmdir($inFilePath);
    }
}