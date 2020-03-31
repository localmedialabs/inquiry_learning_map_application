import axios from 'axios'
import apiConfig from '../../config/api.json'

/**
 * request通信操作共通部品クラス（ベースはaxiosによる操作）
 *
 * @class request通信操作共通部品クラス
 * @param なし
 */
export default class requestHelper {
  /** 認証トークン */
  _currentAuthToken = ''
  /** 認証トークン */
  _currentAuthTokenMode = 'header'
  /** 認証トークン */
  _currentAuthTokenName = 'negotiation'
  /** リクエストメソッド */
  _currentSendMethod = 'GET'
  /** レスポンス設定 */
  _currentResponse = {}
  /** リクエストパラメータヘッダー設定 */
  _currentRequestHeader = {}
  /** リクエストパラメータ設定 */
  _currentRequestParameter = {}
  /** リクエストパラメータ設定（複数型） */
  _currentRequestParameterList = {}
  /** リクエスト設定ファイル項目 */
  _requestConfig = apiConfig
  /** リクエスト設定ファイル読み込み */
  loadConfig (inConfigData) {
    this._requestConfig = inConfigData
  }
  /** 認証トークン設定 */
  setAtoken (inToken) {
    this._currentAuthToken = inToken
    if (this._currentAuthTokenMode === 'header') {
      this._currentRequestHeader[this._currentAuthTokenName] = this._currentAuthToken
    }
  };
  /** リクエストヘッダー設定 */
  setRequestHeader (inParamName, inParamValue) {
    this._currentRequestHeader[inParamName] = inParamValue
  };
  /** リクエストパラメータ設定 */
  setRequestParam (inParamName, inParamValue) {
    var inSetType = arguments[2]
    var inMultiKey = this.dataEmptyCheck(arguments[3]) ? arguments[3] : ''

    // 複数パラメータ対応(第三引数にmultiple OR multiple_key_valueを設定した場合)
    if (inSetType === 'multiple' || inSetType === 'multiple_key_value') {
      var inParamNameMlt = inParamName + '[]'
      if (inSetType === 'multiple_key_value') {
        // 第三引数にmultiple_key_valueを設定した場合は、nameを連想配列表現にする）
        inParamNameMlt = inParamName + '[' + inMultiKey + ']'
      }
      if ((inParamNameMlt in this._currentRequestParameterList) === false) {
        this._currentRequestParameterList[inParamNameMlt] = {}
      }
      this._currentRequestParameter[inParamNameMlt].push(inParamValue)
    } else {
      this._currentRequestParameter[inParamName] = inParamValue
    }
  };
  /** リクエストパラメータ設定クリア */
  clearRequestParam () {
    this._currentRequestParameter = {}
    this._currentRequestParameterList = {}
  };

  /** リクエスト送信（設定ファイルに沿って送信) */
  /** バリデーションのみ */
  sendRequestValidate (inCMD, inCallBack) {
    var setURL = ''
    if (inCMD in this._requestConfig.api_info) {
      setURL = this._requestConfig.api_common_info.host + this._requestConfig.api_info[inCMD].endpoint
      this._currentSendMethod = this._requestConfig.api_info[inCMD].method
    }
    this._currentRequestParameter['exec_mode'] = 'validate'
    this.sendRequestURL(setURL, inCallBack)
  };

  sendRequest (inCMD, inCallBack) {
    var setURL = ''
    if (inCMD in this._requestConfig.api_info) {
      setURL = this._requestConfig.api_common_info.host + this._requestConfig.api_info[inCMD].endpoint
      this._currentSendMethod = this._requestConfig.api_info[inCMD].method
    }
    this._currentRequestParameter['exec_mode'] = ''
    this.sendRequestURL(setURL, inCallBack)
  };

  /** リクエスト送信（URL指定) */
  sendRequestURL (inUrl, inCallBack) {
    switch (this._currentSendMethod) {
      case 'GET':
        axios.get(inUrl, {
          headers: this._currentRequestHeader,
          params: this._currentRequestParameter,
          data: {}
        }).then(
          response => (
            inCallBack(response.data)
          )
        )
        break
      case 'PUT':
        axios.put(inUrl, this._currentRequestParameter, {
          headers: this._currentRequestHeader
        }).then(
          response => (
            inCallBack(response.data)
          )
        )
        break
      case 'POST':

        axios.post(inUrl, this._currentRequestParameter, {
          headers: this._currentRequestHeader
        }).then(
          response => (
            inCallBack(response.data)
          )
        )
        break
      case 'DELETE':
        axios.delete(inUrl, {
          headers: this._currentRequestHeader,
          data: this._currentRequestParameter
        }).then(
          response => (
            inCallBack(response.data)
          )
        )
        break
    }
  };

  /** ファイル読み込み */
  loadFileMeta (inEvent) {
    var target = inEvent.target
    var outfiles = target.files
    return outfiles
  }

  loadFile (inEvent, inCallBack, inLoadType) {
    var files = inEvent.target.files || inEvent.dataTransfer.files
    if (!files.length) {
      return
    }
    this.loadFileData(files[0], inCallBack, inLoadType)
  }

  loadFileData (inFileObj, inCallBack, inLoadType) {
    var reader = new FileReader()
    reader.onload = function (e) {
      inCallBack(e)
    }
    if (inLoadType === 'text') {
      reader.readAsText(inFileObj)
    } else {
      reader.readAsDataURL(inFileObj)
    }
  }
  /** CSV→JSONコンバート */
  convertCsv2Json (csvArrayStr, jsonKeyArray) {
    /** function csv2json(csvArrayStr,jsonKeyArray){ */
    var jsonArray = []
    /**  1行目から「項目名」の配列を生成する */
    var csvArray = csvArrayStr.split('\n')
    for (var i = 1; i < csvArray.length; i++) {
      /** var aLine = new Object() */
      var aLine = {}
      if (csvArray[i].trim() !== '') {
        var csvArrayD = csvArray[i].replace(/\r?\n/g, '').split(',')
        /**   ,"trouble_type_1":{"target_key_idx":[14],"conv_type":{"split":"-","target_idx":0}} */
        for (let jsonKey in jsonKeyArray) {
          aLine[jsonKey] = ''
          for (let tkIdx in jsonKeyArray[jsonKey]['target_key_idx']) {
            var setKeyIdx = jsonKeyArray[jsonKey]['target_key_idx'][tkIdx] * 1
            /** 値変換 */
            if (('conv_type' in jsonKeyArray[jsonKey]) === true) {
              /** デリミタ部分抽出 */
              if ('split' in jsonKeyArray[jsonKey]['conv_type']) {
                var spDmstr = jsonKeyArray[jsonKey]['conv_type']['split']
                if (csvArrayD[setKeyIdx].indexOf(spDmstr) !== -1) {
                  var tmpDataList = csvArrayD[setKeyIdx].split(spDmstr)
                  aLine[jsonKey] = aLine[jsonKey] + tmpDataList[jsonKeyArray[jsonKey]['conv_type']['target_idx']]
                }
              }
              /** パディング処理 */
              if ('padding' in jsonKeyArray[jsonKey]['conv_type']) {
                if (csvArrayD[setKeyIdx].length <= jsonKeyArray[jsonKey]['conv_type']['target_length']) {
                  aLine[jsonKey] = aLine[jsonKey] + (jsonKeyArray[jsonKey]['conv_type']['padding'] + csvArrayD[setKeyIdx]).slice((jsonKeyArray[jsonKey]['conv_type']['target_length'] * -1))
                } else {
                  aLine[jsonKey] = aLine[jsonKey] + csvArrayD[setKeyIdx]
                }
              }
            } else {
              aLine[jsonKey] = aLine[jsonKey] + csvArrayD[setKeyIdx]
            }
          }
        }
        jsonArray.push(aLine)
      }
    }
    return jsonArray
  }
  /** ファイルメタ情報チェック */
  fileTypeCheck (inData, inCheckType) {
    var outFlg = false
    var fileType = {}
    fileType = {
      'image': {'reg_exp': '^image/'},
      'pdf': {'reg_exp': '^application/pdf'}
    }
    if (!inData.type.match(new RegExp(fileType[inCheckType].reg_exp))) {
      outFlg = false
    } else {
      outFlg = true
    }
    return outFlg
  }
  /** 値チェック */
  dataEmptyCheck (inData) {
    var outFlg = false
    if (inData !== undefined) {
      if (inData !== null) {
        if (inData !== '') {
          outFlg = true
        }
      }
    }
    return outFlg
  };
  /** オブジェクトのコピー */
  copyObject (inTargetObject) {
    return JSON.parse(JSON.stringify(inTargetObject))
  }
}
