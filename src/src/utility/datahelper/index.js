/**
 * データ処理操作共通部品クラス（ベースはaxiosによる操作）
 *
 * @class データ処理操作共通部品クラス
 * @param なし
 */
export default class dataHelper {
  /** 値チェック */
  dataEmptyCheck (inData) {
    var outFlg = false
    if (inData === null || typeof inData === 'undefined') {
      if (inData !== '') {
        outFlg = true
      }
    }
    return outFlg
  };
  /** 日付フォーマット変換 */
  convertDateFormat (inData, inType) {
    if (inData === null || typeof inData === 'undefined') {
      return inData
    }

    var setStr = inData
    switch (inType) {
      case 'date':
        if (inData.length >= 8 && inData.match(/^[0-9]{8}/)) {
          setStr = inData.substring(0, 4) + '/' + inData.substring(4, 6) + '/' + inData.substring(6, 8)
        }
        break
      case 'date_time':
        if (inData.length >= 14 && inData.match(/^[0-9]{14}/)) {
          setStr = inData.substring(0, 4) + '/' + inData.substring(4, 6) + '/' + inData.substring(6, 8) + ' ' + inData.substring(8, 10) + ':' + inData.substring(10, 12) + ':' + inData.substring(12, 14)
        } else if (inData.length >= 12 && inData.match(/^[0-9]{12}/)) {
          setStr = inData.substring(0, 4) + '/' + inData.substring(4, 6) + '/' + inData.substring(6, 8) + ' ' + inData.substring(8, 10) + ':' + inData.substring(10, 12)
        }
        break
      case 'jp_date':
        if (inData.length >= 8 && inData.match(/^[0-9]{8}/)) {
          setStr = inData.substring(0, 4) + '年' + inData.substring(4, 6) + '月' + inData.substring(6, 8) + '日'
        }
        break
      case 'jp_date_time':
        if (inData.length >= 14 && inData.match(/^[0-9]{14}/)) {
          setStr = inData.substring(0, 4) + '年' + inData.substring(4, 6) + '月' + inData.substring(6, 8) + '日 ' + inData.substring(8, 10) + '時' + inData.substring(10, 12) + '分' + inData.substring(12, 14) + '秒'
        } else if (inData.length >= 12 && inData.match(/^[0-9]{12}/)) {
          setStr = inData.substring(0, 4) + '年' + inData.substring(4, 6) + '月' + inData.substring(6, 8) + '日 ' + inData.substring(8, 10) + '時' + inData.substring(10, 12) + '分'
        }
        break
      case 'fisical_year':
        if (inData.length >= 8 && inData.match(/^[0-9]{8}/)) {
          var year = parseInt(inData.substring(0, 4))
          setStr = (parseInt(inData.substring(4, 6), 10) < 4) ? year - 1 : year
        }
        break
      case 'year':
        if (inData.length >= 4 && inData.match(/^[0-9]{4}/)) {
          setStr = inData.substring(0, 4)
        }
        break
      case 'year_two_degits':
        if (inData.length >= 4 && inData.match(/^[0-9]{4}/)) {
          setStr = inData.substring(2, 4)
        }
        break
      case 'month':
        if (inData.length >= 6 && inData.match(/^[0-9]{6}/)) {
          setStr = inData.substring(4, 6)
        }
        break
      case 'day':
        if (inData.length >= 8 && inData.match(/^[0-9]{8}/)) {
          setStr = inData.substring(6, 8)
        }
        break
      case 'hour':
        if (inData.length >= 10 && inData.match(/^[0-9]{10}/)) {
          setStr = inData.substring(8, 10)
        }
        break
      case 'minute':
        if (inData.length >= 12 && inData.match(/^[0-9]{12}/)) {
          setStr = inData.substring(10, 12)
        }
        break
      case 'second':
        if (inData.length >= 14 && inData.match(/^[0-9]{14}/)) {
          setStr = inData.substring(12, 14)
        }
        break
      case 'time':
        if (inData.length === 4 && inData.match(/^[0-9]{4}/)) {
          setStr = inData.substring(0, 2) + ':' + inData.substring(2, 4)
        } else if (inData.length >= 14 && inData.match(/^[0-9]{14}/)) {
          setStr = inData.substring(8, 10) + ':' + inData.substring(10, 12) + ':' + inData.substring(12, 14)
        } else if (inData.length >= 12 && inData.match(/^[0-9]{12}/)) {
          setStr = inData.substring(8, 10) + ':' + inData.substring(10, 12)
        }
        break
      case 'number':
        setStr = setStr.replace(/\//g, '')
        setStr = setStr.replace(/:/g, '')
        setStr = setStr.replace(/-/g, '')
        setStr = setStr.replace(' ', '')
        setStr = setStr.replace(':', '')
        setStr = setStr.replace('.', '')
        break
    }

    return setStr
  };
}
