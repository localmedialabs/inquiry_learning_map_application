import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

import '@geoman-io/leaflet-geoman-free'
import '@geoman-io/leaflet-geoman-free/dist/leaflet-geoman.css'

import '@ansur/leaflet-pulse-icon'

var _GLData = {}
_GLData['currentClickLatitude'] = 0
_GLData['currentClickLongitude'] = 0
_GLData['mapClickCallBack'] = function () {}
var _CURRENTGPSINFO = {}
/**
 * GIS操作共通部品クラス（ベースはleafletjsによる操作）
 *
 * @class GIS操作共通部品クラス
 * @param なし
 */
export default class gisHelper {
    /** 地図埋め込みターゲットID */
    _currentMapTargetId = 'map'
    _currentMapObject = null

    _currentMapBasePanelInfo = {
      /** デフォルトOSM */
      panel: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a>',
      maxZoom: 18
    };

    /** 緯度（中心（デフォルト富士山周辺） */
    _centerLatitude = 35.362222
    /** 経度（中心 */
    _centerLongitude = 138.731389
    /** ズームレベル（デフォルト 10） */
    _defaultZoomLevel = 10
    /** ズーム（デフォルト true） */
    _defaultZoomControl = true
    /** 編集モード */
    _geoEditMode = false
    /** 現在地情報取得モード */
    _gpsMode = false
    /** 緯度 */
    _gpsLatitude = ''
    /** 経度 */
    _gpsLongitude = ''
    /** 現在地情報取得中心点連動モード */
    _gpsCenterSyncMode = false
    /** マーカー編集モード */
    _makerEditMode = false
    /** データ格納領域 */
    _layerData = {};
    /** タイルレイヤー領域 */
    _tileLayer = {};
    /** データマーカー格納領域 */
    _layerMarkerData = {};
    /** データマーカー設定済み */
    _layerMarkerCompData = {};
    /** データレイヤー領域(パネル) */
    _dataLayerPanel = {};
    /** マーカー情報領域 */
    _makerIconData = {};
    /** マーカーポップアップ情報領域 */
    _makerPopupData = {};
    /** クリック緯度 */
    _currentClickLatitude = 0
    /** クリック経度 */
    _currentClickLongitude = 0
    /** ドローモード */
    _currentDrawMode = ''
    /** クリックコールバック */
    _mapClickCallBack = function () {}
    execInit () {}
    setMapClickCallBack (inCallBack) {
      _GLData['mapClickCallBack'] = inCallBack
    }
    /** GPSコールバック */
    setGSPCallBack (inCallBack) {
      _GLData['gpsCallBack'] = inCallBack
    }

    /** 埋め込みIDの設定 */
    setMapTargetId (inTrgId) {
      this._currentMapTargetId = inTrgId
    }

    /** 中心緯度経度を設定 */
    setPoint (inLat, inLng) {
      this.setPointLatitude(inLat)
      this.setPointLongitude(inLng)
    }

    /** 中心点緯度を設定 */
    setPointLatitude (inLat) {
      this._centerLatitude = inLat
    }
    /** 中心点経度を設定 */
    setPointLongitude (inLng) {
      this._centerLongitude = inLng
    }
    /** ズームレベル */
    setDefaultZoomLevel (inZoomLevel) {
      this._defaultZoomLevel = inZoomLevel
    }
    /** ズームコントロール */
    setDefaultZoomControlOn () {
      this._defaultZoomControl = true
    }
    setDefaultZoomControlOff () {
      this._defaultZoomControl = false
    }
    /** エディットモード */
    setMarkerEditOn () {
      this._makerEditMode = true
    }
    setMarkerEditOff () {
      this._makerEditMode = false
    }

    /** クリック緯度を設定 */
    getClickLatitude () {
      this._currentClickLatitude = _GLData['currentClickLatitude']
      return this._currentClickLatitude
    }
    /** クリック経度を設定 */
    getClickLongitude () {
      this._currentClickLongitude = _GLData['currentClickLongitude']
      return this._currentClickLongitude
    }

    /** 地図パネルの設定 */
    setMapPanel (inPanelUrl, inPanelLicense, inPanelZoom) {
      this._currentMapBasePanelInfo = {
        panel: inPanelUrl,
        attribution: inPanelLicense,
        maxZoom: inPanelZoom
      }
    }

    /** 地図作成 */
    createMap () {
    /** 地図作成条件のチェック */
      if (this.chekcMapCreateOrder() === true) {
        this._currentMapObject = L.map(this._currentMapTargetId, {
          zoomControl: this._defaultZoomControl
        }).setView([this._centerLatitude, this._centerLongitude], this._defaultZoomLevel)
        /** OSMレイヤー追加: */
        this._tileLayer['base'] = L.tileLayer(
          this._currentMapBasePanelInfo.panel,
          {
            attribution: this._currentMapBasePanelInfo.attribution,
            maxZoom: this._currentMapBasePanelInfo.maxZoom
          }
        )
        this._tileLayer['base'].addTo(this._currentMapObject)

        if (this._makerEditMode === true) {
          this._currentMapObject.on('click', function (e) {
            _GLData['currentClickLatitude'] = e.latlng.lat
            _GLData['currentClickLongitude'] = e.latlng.lng
            if (typeof (_GLData['mapClickCallBack']) === 'function') {
              _GLData['mapClickCallBack']()
            }
          })
        }
      }
    }

    changeBaseMap (inKey) {
      this._tileLayer[inKey] = L.tileLayer(
        this._currentMapBasePanelInfo.panel,
        {
          attribution: this._currentMapBasePanelInfo.panel.attribution,
          maxZoom: this._currentMapBasePanelInfo.maxZoom
        }
      )
      this._tileLayer[inKey].addTo(this._currentMapObject)
    }

    removeBaseMap (inKey) {
      if ((inKey in this._tileLayer) === true) {
        this._tileLayer[inKey].remove(this._currentMapObject)
        delete this._tileLayer[inKey]
      }
    }
    removeBaseMapAll () {
      for (let inKey in this._tileLayer) {
        this._tileLayer[inKey].remove(this._currentMapObject)
      }
      this._tileLayer = {}
    }

    /** 地図操作系///////////////////////////////////////////////////////////// */
    /** 設定緯度経度に移動 */
    movePoint () {
      if (this._currentMapObject !== null) {
        if (this.emptyCheck(this._centerLatitude) === true && this.emptyCheck(this._centerLongitude) === true) {
          this._currentMapObject.setView([this._centerLatitude, this._centerLongitude])
        }
      }
    }
    /** マーカーセット */
    /** inIconType:アイコンタイプ：this._makerIconDataのキー値 */
    setMarker (inLayerName, inLat, inLon, inIconType) {
      var inContents = ''
      if (arguments.length === 5) {
        inContents = arguments[4]
      }

      if ((inLayerName in this._layerData) === false) {
        this._layerData[inLayerName] = []
      }
      var makerDtailData = {'lat': inLat, 'lon': inLon, 'icon': inIconType, 'contents': inContents}
      this._layerData[inLayerName].push(makerDtailData)
    }

    /** マーカーアイコンセット */
    setMarkerIcon (inIconKey, inIconUrl, inRetinaUrl) {
      var setIconSize = [40, 40]
      var setIconAnchor = [25, 50]
      var setIconpopupAnchor = [0, -50]

      if (arguments.length > 3) {
        var setOption = arguments[3]
        if (('size' in setOption) === true) {
          setIconSize = setOption['size']
        }
        if (('anchor' in setOption) === true) {
          setIconAnchor = setOption['anchor']
        }
        if (('popupanchor' in setOption) === true) {
          setIconpopupAnchor = setOption['popupanchor']
        }
      }

      this._makerIconData[inIconKey] = L.icon({
        iconUrl: inIconUrl,
        iconRetinaUrl: inRetinaUrl,
        iconSize: setIconSize,
        iconAnchor: setIconAnchor,
        popupAnchor: setIconpopupAnchor
      })
    }

    /** マーカーポップアップセット */
    setMarkerPopup (inIconKey) {
      this._makerPopupData[inIconKey] = L.popup()
    }

    /** マーカー配置 */
    mountMarker () {
      var inCallBack = ''
      if (arguments.length > 0) {
        if (typeof (arguments[0]) === 'function') {
          inCallBack = arguments[0]
        }
      }
      for (let lkey in this._layerData) {
        if ((lkey in this._layerMarkerCompData) === false) {
          /** var setdataLayer = [] */
          this._layerMarkerData[lkey] = L.featureGroup()
          for (let dkey in this._layerData[lkey]) {
            var subsetdataLayer = [this._layerData[lkey][dkey]['lat'], this._layerData[lkey][dkey]['lon']]
            var setMarker = L.marker(subsetdataLayer)
            setMarker.setIcon(this._makerIconData[this._layerData[lkey][dkey]['icon']])
            if (('contents' in this._layerData[lkey][dkey]) === true) {
              if (this._layerData[lkey][dkey]['contents'] !== '') {
                setMarker.bindPopup(this._layerData[lkey][dkey]['contents'], {maxWidth: 500, closeOnClick: true})
              }
            }
            if (inCallBack !== '') {
              setMarker.on('click', function (e) { inCallBack(e) })
            }
            this._layerMarkerData[lkey].addLayer(setMarker)
            setMarker = null
            /** setdataLayer.push(subsetdataLayer) */
          }
          for (let dlkey in this._layerMarkerData) {
            this.addMarkerLayer(dlkey)
          }
          /** L.marker(setdataLayer).addTo(this._currentMapObject) */
        }
      }
    }
    addMarkerLayer (inLayer) {
      if ((inLayer in this._layerMarkerData) === true) {
        if ((inLayer in this._layerMarkerCompData) === false) {
          this._currentMapObject.addLayer(this._layerMarkerData[inLayer])
          this._layerMarkerCompData[inLayer] = true
        }
      }
    }

    removeMarkerObject (inLayerName, inLayerObject) {
      this._layerMarkerData[inLayerName].removeLayer(inLayerObject)
    }

    removeMarkerLayer (inLayer) {
      if ((inLayer in this._layerMarkerData) === true) {
        this._currentMapObject.removeLayer(this._layerMarkerData[inLayer])
        /** this._layerMarkerData[inLayer].clearLayers() */
        this._layerMarkerData[inLayer] = null
        this._layerData[inLayer] = null
        this._layerMarkerCompData[inLayer] = null
        delete this._layerMarkerData[inLayer]
        delete this._layerMarkerCompData[inLayer]
        delete this._layerData[inLayer]
      }
    }
    removeMarkerLayerAll () {
      /** 編集領域の削除 */
      var setDrawKey = 'draw'
      if ((setDrawKey in this._layerMarkerData) === true) {
        this._currentMapObject.removeLayer(this._layerMarkerData[setDrawKey])
        this._layerMarkerData[setDrawKey] = null
        this._layerData[setDrawKey] = null
        this._layerMarkerCompData[setDrawKey] = null
        delete this._layerMarkerData[setDrawKey]
        delete this._layerMarkerCompData[setDrawKey]
        delete this._layerData[setDrawKey]
      }

      for (var lkey in this._layerMarkerData) {
        this._currentMapObject.removeLayer(this._layerMarkerData[lkey])
        this._layerMarkerData[lkey] = null
        this._layerData[lkey] = null
        this._layerMarkerCompData[lkey] = null
        delete this._layerMarkerData[lkey]
        delete this._layerMarkerCompData[lkey]
        delete this._layerData[lkey]
      }
    }
    /** ライン設置モード */
    setDrawPolyline () {
      this.setCancel()
      this._currentMapObject.pm.enableDraw('Line', {
        snappable: true,
        templineStyle: {color: 'red'},
        snapDistance: 20
      })
      this._currentDrawMode = 'Line'
    }
    setPolyline (inLayterId, inLatlng) {
      if ((inLayterId in this._layerMarkerData) === false) {
        this._layerMarkerData[inLayterId] = L.featureGroup()
      }
      var inCallBack = ''
      var inPopupContents = ''
      if (arguments.length > 2) {
        if (typeof (arguments[2]) === 'function') {
          inCallBack = arguments[2]
        }
        if (arguments.length > 3) {
          inPopupContents = arguments[3]
        }
      }
      var setMakerObj = L.polyline(inLatlng,
        {
          'weight': 10,
          'opacity': 1
        }
      )
      if (inCallBack !== '') {
        setMakerObj.on('click', function (e) { inCallBack(e) })
      }
      if (inPopupContents !== '') {
        setMakerObj.bindPopup(inPopupContents, {maxWidth: 500, closeOnClick: true})
      }
      /** setMakerObj.addTo(this._currentMapObject) */
      this._layerMarkerData[inLayterId].addLayer(setMakerObj)
      this._currentMapObject.addLayer(this._layerMarkerData[inLayterId])
    }
    /** ポリゴン設置モード */
    setDrawPolygon () {
      this.setCancel()
      this._currentMapObject.pm.enableDraw('Polygon', {
        snappable: true,
        templineStyle: {color: 'green'},
        snapDistance: 20
      })
      this._currentDrawMode = 'Polygon'
    }
    setPolygon (inLayterId, inLatlng) {
      if ((inLayterId in this._layerMarkerData) === false) {
        this._layerMarkerData[inLayterId] = L.featureGroup()
      }
      var inCallBack = ''
      var inPopupContents = ''
      if (arguments.length > 2) {
        if (typeof (arguments[2]) === 'function') {
          inCallBack = arguments[2]
        }
        if (arguments.length > 3) {
          inPopupContents = arguments[3]
        }
      }
      var setMakerObj = L.polygon(inLatlng)
      if (inCallBack !== '') {
        setMakerObj.on('click', function (e) { inCallBack(e) })
      }

      if (inPopupContents !== '') {
        setMakerObj.bindPopup(inPopupContents, {maxWidth: 500, closeOnClick: true})
      }

      /** setMakerObj.addTo(this._currentMapObject) */
      this._layerMarkerData[inLayterId].addLayer(setMakerObj)
      this._currentMapObject.addLayer(this._layerMarkerData[inLayterId])
    }

    /** 四角形設置モード */
    setDrawRectangle () {
      this.setCancel()
      this._currentMapObject.pm.enableDraw('Rectangle', {
        snappable: true,
        templineStyle: {color: 'yellow'},
        snapDistance: 20
      })
      this._currentDrawMode = 'Rectangle'
    }
    setRectangle (inLayterId, inLatlng) {
      if ((inLayterId in this._layerMarkerData) === false) {
        this._layerMarkerData[inLayterId] = L.featureGroup()
      }
      var inCallBack = ''
      if (arguments.length === 3) {
        if (typeof (arguments[2]) === 'function') {
          inCallBack = arguments[2]
        }
      }
      var setMakerObj = L.rectangle(inLatlng)
      if (inCallBack !== '') {
        setMakerObj.on('click', function (e) { inCallBack(e) })
      }
      this._layerMarkerData[inLayterId].addLayer(setMakerObj)
      this._currentMapObject.addLayer(this._layerMarkerData[inLayterId])
    }

    /** ライン設置モード */
    setDrawCircle () {
      this.setCancel()
      this._currentMapObject.pm.enableDraw('Circle', {
        snappable: true,
        snapDistance: 20
      })
      this._currentDrawMode = 'Circle'
    }
    setCircle (inLayterId, inLatlng, inRadius) {
      if ((inLayterId in this._layerMarkerData) === false) {
        this._layerMarkerData[inLayterId] = L.featureGroup()
      }

      var inCallBack = ''
      var inPopupContents = ''
      if (arguments.length > 3) {
        if (typeof (arguments[3]) === 'function') {
          inCallBack = arguments[3]
        }
        if (arguments.length > 4) {
          inPopupContents = arguments[4]
        }
      }
      var setMakerObj = L.circle(inLatlng, inRadius)
      if (inCallBack !== '') {
        setMakerObj.on('click', function (e) { inCallBack(e) })
      }
      if (inPopupContents !== '') {
        setMakerObj.bindPopup(inPopupContents, {maxWidth: 500, closeOnClick: true})
      }
      this._layerMarkerData[inLayterId].addLayer(setMakerObj)
      this._currentMapObject.addLayer(this._layerMarkerData[inLayterId])
    }

    /** ライン編集設置モード コールバック */
    setDrawCreateCallBack (inCallBack) {
      this._currentMapObject.off('pm:create')
      this._currentMapObject.on('pm:create', e => {
        /** drawレイヤーグループの作成 */
        if (('draw' in this._layerMarkerData) === false) {
          this._layerMarkerData['draw'] = L.featureGroup()
        }
        this._layerMarkerData['draw'].addLayer(e.layer)
        this._currentMapObject.addLayer(this._layerMarkerData['draw'])
        this._layerMarkerCompData['draw'] = true

        inCallBack(e)
      })
    }
    setDrawendCallBack (inCallBack) {
      this._currentMapObject.off('pm:drawend')
      this._currentMapObject.on('pm:drawend', e => {
        inCallBack(e)
      })
    }
    setEditCallBack (inCallBack) {
      this._currentMapObject.off('pm:edit')
      this._currentMapObject.on('pm:edit', e => {
        inCallBack(e)
      })
    }
    setDrawClickCallBack (inCallBack) {
      this._currentMapObject.off('click')
      this._currentMapObject.on('click', function (e) { inCallBack(e) })
    }

    /** 設置解除モード */
    setCancel () {
      if (this._currentDrawMode !== '') {
        this._currentMapObject.pm.disableDraw(this._currentDrawMode)
        this._currentDrawMode = ''
      }
    }

    /** 設置データ取得 */
    getShapesLayerId (inTarget, inType) {
      if (inType === 'Polyline') {
        return inTarget.layer._leaflet_id
      } else {
        var setData = []
        for (var key in inTarget.target._targets) {
          setData = inTarget.target._targets[key]
        }
        return setData._latlng['_leaflet_id']
      }
    }

    getShapesData (inTarget, inType) {
      var inData = {}
      var setData = []
      for (var key in inTarget.target._targets) {
        setData = inTarget.target._targets[key]
      }
      if (inType === 'Circle') {
        inData['latlngs'] = []
        if ('_latlng' in setData) {
          let tmpData = {}
          tmpData['lat'] = setData._latlng['lat']
          tmpData['lng'] = setData._latlng['lng']
          inData['latlngs'].push(tmpData)
        }
        if ('_mRadius' in setData) {
          inData['radius'] = setData._mRadius
        }
      } else if (inType === 'Polyline') {
        if ('_latlngs' in setData) {
          inData['latlngs'] = []
          for (let dkey in setData._latlngs) {
            let tmpData = {}
            tmpData['lat'] = setData._latlngs[dkey]['lat']
            tmpData['lng'] = setData._latlngs[dkey]['lng']
            inData['latlngs'].push(tmpData)
          }
        }
      } else {
        if ('_latlngs' in setData) {
          inData['latlngs'] = []
          for (let dkey in setData._latlngs[0]) {
            let tmpData = {}
            tmpData['lat'] = setData._latlngs[0][dkey]['lat']
            tmpData['lng'] = setData._latlngs[0][dkey]['lng']
            inData['latlngs'].push(tmpData)
          }
        }
      }
      return inData
    }
    /** GPS系の操作///////////////////////////////////////////////////////////// */
    /** GPS移動連動モード */
    startSyncGpsMoveMode () {
      this._gpsCenterSyncMode = true
    }
    stopSyncGpsMoveMode () {
      this._gpsCenterSyncMode = false
    }

    /** GPSモードスタート */
    startGpsMode () {
      if (this._gpsMode === false) {
        this._gpsMode = true
        this.runningGpsData()
      }
    }
    /** GPSモードストップ */
    stopGpsMode () {
      if (this._gpsMode === true) {
        this._gpsMode = false
      }
    }
    /** GPS取得中 */
    runningGpsData () {
      if (this._gpsMode === true) {
        this.execGpsData()
      }
    }

    getGpsData () {
      if (this._gpsMode === true) {
        /** 緯度 */
        this._gpsLatitude = _CURRENTGPSINFO['latitude']
        /** 経度 */
        this._gpsLongitude = _CURRENTGPSINFO['longitude']
        /** MAP中心地連動 */
        if (this._gpsCenterSyncMode === true) {
          this._centerLatitude = this._gpsLatitude
          this._centerLongitude = this._gpsLongitude
          this.movePoint()
        }
      }
    }
    /** 現在地取得 */
    execGpsData (inCallBack) {
      if (('gpsCallBack' in _GLData) === true) {
        inCallBack = _GLData['gpsCallBack']
      }
      if (this._gpsMode === true) {
        var optionObj = {
          'enableHighAccuracy': true, /** より精度の高い位置情報を取得するか（true／false） */
          'timeout': 8000, /** 取得タイムアウトまでの時間（ミリ秒） */
          'maximumAge': 5000/** 位置情報の有効期限（ミリ秒） */
        }

        var successResult = function (position) {
          /** 現在位置の緯度。-180 - 180で表す。 */
          _CURRENTGPSINFO['latitude'] = position.coords.latitude
          /** 現在位置の経度。-90 - 90で表す。 */
          _CURRENTGPSINFO['longitude'] = position.coords.longitude
          /** 現在位置の高度。メートル単位で表す。 */
          _CURRENTGPSINFO['altitude'] = position.coords.altitude
          /** 取得した緯度、経度の精度。メートル単位で表す。 */
          _CURRENTGPSINFO['accuracy'] = position.coords.accuracy
          /** 取得した高度の精度。メートル単位で表す。 */
          _CURRENTGPSINFO['altitudeAccuracy'] = position.coords.altitudeAccuracy
          /** 方角。0 - 360の角度で表す。0が北、90が東、180が南、270が西。 */
          _CURRENTGPSINFO['heading'] = position.coords.heading
          /**  */
          _CURRENTGPSINFO['speed'] = position.coords.speed
          if (typeof (inCallBack) === 'function') {
            inCallBack(_CURRENTGPSINFO)
          }
        }

        var errorResult = function (error) {
          /**  エラーコードのメッセージを定義 */
          var errorMessage = {
            0: '原因不明のエラーが発生しました。',
            1: '位置情報の取得が許可されませんでした。',
            2: '電波状況などで位置情報が取得できませんでした。',
            3: '位置情報の取得に時間がかかり過ぎてタイムアウトしました。'
          }
          /**  エラーコードに合わせたエラー内容をアラート表示 */
          alert(errorMessage[error.code])
        }

        navigator.geolocation.watchPosition(successResult, errorResult, optionObj)
      }
    }
    /** その他サポート系 */
    /** 地図作成条件のパラメータチェック */
    chekcMapCreateOrder () {
      var outCheckResult = false
      if (this.emptyCheck(this._currentMapTargetId) === true && this.emptyCheck(this._centerLatitude) === true && this.emptyCheck(this._centerLongitude) === true && this.emptyCheck(this._defaultZoomLevel) === true) {
        outCheckResult = true
      }
      return outCheckResult
    }

    /** 空チェック */
    emptyCheck (inStrData) {
      var outResult = true
      if (inStrData === null || inStrData === '' || typeof inStrData === 'undefined') {
        outResult = false
      }
      return outResult
    }

    /** 距離の計算 */
    getDistance (inFLat, inFLon, inToLat, inToLon) {
      /** 距離の計算 */
      var outData = 6378.14 * Math.acos(Math.cos(this.radians(inFLat)) * Math.cos(this.radians(inToLat)) * Math.cos(this.radians(inToLon) - this.radians(inFLon)) + Math.sin(this.radians(inFLat)) * Math.sin(this.radians(inToLat)))

      /** 結果 */
      return outData
    }

    redians (inDeg) {
      return inDeg * Math.PI / 180
    }
    /** 距離単位変換 */
    convertDistanceFormat (inDsStr, inDataType) {
      var setValue = 1000
      var setCkValue = 1000
      /** キロメータ */
      if (inDataType === 'K') {
        setValue = 1000
      }
      /** メータ */
      if (inDataType === 'M') {
        setValue = 1
        setCkValue = 1000
      }
      return this.convertDistanceFormatCore(inDsStr, setValue, setCkValue)
    }

    convertDistanceFormatCore (inDsStr, inValue, inCkValue) {
      var outStr = ''
      inDsStr = parseInt(inDsStr)
      if (inDsStr > inCkValue) {
        outStr = parseInt(Math.round(inDsStr * inValue)) / 1000 + 'Km'
      } else {
        outStr = parseInt(inDsStr * inValue) + 'm'
      }
      return outStr
    }
}
