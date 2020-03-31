<template>
  <div>
    <div id="map"></div>
    <div class="card bottom_menu_area_l" v-on:click="changeMenuArea" v-bind:class="{'bottom_menu_area_l_active':menu_mode, 'bottom_menu_area_close':!menu_mode}">
      <h4 class="card-header"><img v-bind:src="$root.asset.menu.map" width="30">&nbsp;&nbsp;{{$root.current_label.datamap_app_name}}</h4>
      <div class="card-body">
        <div class="form-group">
          <div class="btn-group btn-group-toggle group_button_box_m">
              <label class="btn"  v-bind:class="{'btn-primary': currentMapForcusStatus(), 'btn-light': !currentMapForcusStatus()}">
                <img v-bind:src="$root.asset.map['information']" width="50"/>
                <input type="checkbox" v-model="dataset_type" v-bind:value="'map'" autocomplete="off"  v-on:click="getMapMarkerData()"><br>{{$root.current_label.mapedit_app_name}}
              </label>
          </div>

          <div class="btn-group btn-group-toggle group_button_box_m" data-toggle="buttons" v-for="(dl, dl_index) in dataset_list" :key="dl_index">
           <label class="btn" v-bind:class="{'btn-primary': buttonActiveStatusDataSet(dl.store), 'btn-light': !buttonActiveStatusDataSet(dl.store)}">
             <img v-bind:src="getDatasetAssetPath(dl.store)" width="50">
             <input type="checkbox" v-model="dataset_type" v-bind:value="dl.store" v-bind:id="dl.store" autocomplete="off" v-on:click="getDatasetDetail2(dl.store)"><br>{{dl.name}}
           </label>
          </div>
        </div>
        <div class="form-group">
          <div class="btn-group btn-group-toggle group_button_box_m" data-toggle="buttons" v-for="(mp, mp_index) in $root.config.map_datalayter_info" :key="mp_index">
           <label class="btn" v-bind:class="{'btn btn-primary': buttonActiveStatusDataLayter(mp_index), 'btn-light': !buttonActiveStatusDataLayter(mp_index)}">
             <img v-bind:src="$root.asset.disaster[mp.image]" width="50">
             <input type="checkbox" v-model="dataset_type" v-bind:value="mp_index" v-bind:id="mp_index" autocomplete="off" v-on:click="changeMapPanel(mp_index)"><br>{{mp.name}}
           </label>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import GisHelper from '@/utility/gishelper'
import datasetFormat from '@/config/dataset_format.json'
import ApiHelper from '@/utility/requesthelper'
var apiObj = new ApiHelper()

export default {
  name: 'datalayer',
  data () {
    return {
      gisObject: null,
      menu_mode: false,
      contents_mode: false,
      map_forcus_mode: false,
      current_data_layer_id: '',
      forcus_datalayer_list: {},
      forcus_dataset_list: {},
      reg_data_format_list: {},
      icon_image: {},
      icon_image_name: {},
      dataset_type: '',
      dataset_list: [],
      data_list: [],
      map_data_list: []
    }
  },
  computed: {
    currentMapForcusStatus: function () {
      return function () {
        return this.map_forcus_mode
      }
    },
    buttonActiveStatusMap: function (inType) {
      return function (inType) {
        var outStr = false
        if (inType === this.format_type) {
          outStr = true
        }
        return outStr
      }
    },
    buttonActiveStatusDataLayter: function (inType) {
      return function (inType) {
        return this.forcus_datalayer_list[inType]
      }
    },
    buttonActiveStatusDataSet: function (inType) {
      return function (inType) {
        return this.forcus_dataset_list[inType]
      }
    },
    checkbuttonActiveStatus: function (inType) {
      return function (inType) {
        var outStr = false
        if ((inType in this.forcus_dataset_list) === true) {
          outStr = true
        }
        return outStr
      }
    }
  },
  methods: {
    changeMenuArea: function () {
      if (this.menu_mode === false) {
        this.menu_mode = true
      } else {
        this.menu_mode = false
      }
    },
    /** データレイヤー */
    changeMapPanel: function (inType) {
      if (this.forcus_datalayer_list[inType] === false) {
        var mapBasePanelConf = this.$root.config.map_config
        var mapBasePanelInfo = this.$root.config.map_datalayter_info[inType]
        this.gisObject.setMapPanel(mapBasePanelInfo.panel, mapBasePanelInfo.attribution, mapBasePanelConf.maxzoomlevel)
        this.gisObject.changeBaseMap(inType)
        this.forcus_datalayer_list[inType] = true
      } else {
        this.gisObject.removeBaseMap(inType)
        this.forcus_datalayer_list[inType] = false
      }
    },
    /** データセット */
    getDatasetList: function () {
      apiObj.setRequestParam('get_mode', 'common')
      apiObj.sendRequest('dataset_list', this.loadDatasetList)
    },
    loadDatasetList: function (inResponse) {
      if (inResponse.type === 'success') {
        this.dataset_list = inResponse.data[0]['dataset']
        for (var dlKey in this.dataset_list) {
          this.forcus_dataset_list[this.dataset_list[dlKey].store] = false
        }
      }
    },
    getDatasetDetail: function (inDataSetType) {
      this.gisObject.removeMarkerLayerAll()
      this.forcus_dataset_list[inDataSetType] = true
      this.current_data_layer_id = inDataSetType
      var datasetStrList = inDataSetType.split('_')
      if (datasetStrList.length > 1) {
        this.current_format_type = datasetStrList[0]
        apiObj.setRequestParam('detaset_id', inDataSetType)
        apiObj.sendRequest('dataset_detail', this.loadDatasetDetail)
      }
    },
    /** チェックボックス型 */
    getDatasetDetail2: function (inDataSetType) {
      this.current_data_layer_id = inDataSetType
      if (this.forcus_dataset_list[inDataSetType] === true) {
        this.forcus_dataset_list[inDataSetType] = false
        this.gisObject.removeMarkerLayer(inDataSetType)
        this.data_list[inDataSetType] = null
        this.data_list[inDataSetType] = []
      } else {
        this.forcus_dataset_list[inDataSetType] = true
        var datasetStrList = inDataSetType.split('_')
        if (datasetStrList.length > 1) {
          this.current_format_type = datasetStrList[0]
          apiObj.setRequestParam('detaset_id', inDataSetType)
          apiObj.sendRequest('dataset_detail', this.loadDatasetDetail)
        }
      }
    },
    getDatasetAssetPath: function (inDataSetType) {
      var datasetStrList = inDataSetType.split('_')
      var outPath = ''
      if (datasetStrList.length > 1) {
        outPath = this.$root.asset.dataset[datasetStrList[0]]
      }
      return outPath
    },
    loadDatasetDetail: function (inResponse) {
      if (inResponse.type === 'success') {
        var dataSetId = inResponse.data[0]['detaset_id']
        this.data_list[dataSetId] = null
        this.data_list[dataSetId] = []
        this.data_list[dataSetId] = apiObj.copyObject(inResponse.data[0]['dataset_detail_list'])
        this.loadMapData(dataSetId)
      }
    },
    loadMapData: function (inDataSetId) {
      /** this.gisObject.removeMarkerLayerAll() */
      this.gisObject.removeMarkerLayer(inDataSetId)
      var setKey = inDataSetId
      var datasetStrList = inDataSetId.split('_')
      var imagePath = this.$root.asset.dataset[datasetStrList[0]]
      var setOption = {}
      setOption['size'] = []
      setOption['size'] = [52.5, 62]
      this.gisObject.setMarkerIcon(setKey, imagePath, imagePath, setOption)
      var targetDataList = []
      targetDataList = this.data_list[inDataSetId]
      for (var di in targetDataList) {
        if (targetDataList[di]['latitude'] !== '' && targetDataList[di]['longitude'] !== '') {
          let setMessage = this.createDataMessage(targetDataList[di])
          this.gisObject.setMarker(inDataSetId, targetDataList[di]['latitude'], targetDataList[di]['longitude'], setKey, setMessage)
        }
      }
      this.gisObject.mountMarker(this.callbackMapData)
      /** this.gisObject.resetMarkerLayerData(this.current_data_layer_id) */
    },
    createDataMessage: function (inData) {
      var outString = ''
      var formatList = this.reg_data_format_list[this.current_format_type]['format']
      for (let dkey in formatList) {
        if (formatList[dkey]['format_type'] !== 'image') {
          outString = outString + '<div class="col">' + formatList[dkey]['name'] + ':<span class="text-primary">' + inData[formatList[dkey]['item_name']] + '</span></div>'
        } else {
          if (inData[formatList[dkey]['item_name']] !== '') {
            outString = outString + '<div class="col"><image src="' + inData[formatList[dkey]['item_name']] + '" style="max-height:500px;max-width:500px;"></div>'
          }
        }
      }
      outString = '<div class="container" style="overflow:hidden;height:30vh;width:50vh;">' + outString + '</div>'
      return outString
    },
    callbackMapData: function (inEvent) {
      this.gisObject.setPoint(inEvent.latlng.lat, inEvent.latlng.lng)
      this.gisObject.movePoint()
    },
    /** みんなの地図 */
    /** サーバサイドの地図マーカデータ取得 */
    getMapMarkerData: function () {
      if (this.map_forcus_mode === false) {
        if (('group' in this.$root.mydata) === true) {
          apiObj.clearRequestParam()
          apiObj.setRequestParam('mapkey', this.$root.mydata['group'])
          apiObj.sendRequest('map_detail_list_get', this.loadMapDetailData)
          this.map_forcus_mode = true
        }
      } else {
        /** this.gisObject.removeMarkerLayer('map') */
        this.gisObject.removeMarkerLayer('map_marker')
        this.gisObject.removeMarkerLayer('map_polyline')
        this.gisObject.removeMarkerLayer('map_circle')
        this.gisObject.removeMarkerLayer('map_polygon')
        this.gisObject.removeMarkerLayer('map_rectangle')
        this.map_forcus_mode = false
      }
    },
    /** サーバサイドの地図マーカデータ読み込み設定 */
    loadMapDetailData: function (inResponse) {
      if (inResponse.type === 'success') {
        if (inResponse.data[0]['map_detail_list'].length > 0) {
          for (var mdkey in inResponse.data[0]['map_detail_list']) {
            var setExtension = {}
            var targetMapData = []
            var mapDetailKey = ''
            targetMapData = inResponse.data[0]['map_detail_list'][mdkey]

            if (('extension' in targetMapData) === true) {
              setExtension = targetMapData['extension']
            }
            setExtension['point_id'] = targetMapData['point_id']
            setExtension['lat'] = targetMapData['latitude']
            setExtension['lng'] = targetMapData['longitude']
            setExtension['icon_type'] = targetMapData['icon_type']

            setExtension['title'] = targetMapData['title']
            setExtension['contents'] = targetMapData['contents']
            setExtension['image'] = targetMapData['image']
            mapDetailKey = targetMapData['latitude'] + ':' + targetMapData['longitude']
            if (('latlngs' in targetMapData['extension']) === true) {
              mapDetailKey = targetMapData['extension'].latlngs[0].lat + ':' + targetMapData['extension'].latlngs[0].lng
            }

            this.map_data_list[mapDetailKey] = setExtension
            this.loadMarkerData(targetMapData['marker_type'], setExtension)
          }
          this.gisObject.mountMarker(this.callbackMapEditData)
        }
      }
    },
    /** マーカーデータ描写 */
    loadMarkerData: function (inIconType, inExtension) {
      var setLayerId = 'map'
      setLayerId = setLayerId + '_' + inIconType
      let setContents = this.createMapDataMessage(inExtension)
      switch (inIconType) {
        case 'marker':
          this.gisObject.setMarker(setLayerId, inExtension['lat'], inExtension['lng'], inExtension['icon_type'], setContents)
          break
        case 'polyline':
          this.gisObject.setPolyline(setLayerId, inExtension['latlngs'], this.callbackMapEditData, setContents)
          break
        case 'polygon':
          this.gisObject.setPolygon(setLayerId, inExtension['latlngs'], this.callbackMapEditData, setContents)
          break
        case 'circle':
          this.gisObject.setCircle(setLayerId, inExtension['latlngs'][0], inExtension['radius'], this.callbackMapEditData, setContents)
          break
        case 'rectangle':
          this.gisObject.setRectangle(setLayerId, inExtension['latlngs'], this.callbackMapEditData, setContents)
          break
      }
    },
    callbackMapEditData: function (inEvent) {
      var inLat = ''
      var inLng = ''
      if (('_latlngs' in inEvent.target) === true) {
        inLat = inEvent.target._latlngs[0].lat
        inLng = inEvent.target._latlngs[0].lng
      } else {
        inLat = inEvent.target._latlng.lat
        inLng = inEvent.target._latlng.lng
      }
      var setMapDetailKey = inLat + ':' + inLng
      this.getMarkerDataDetail(this.map_data_list[setMapDetailKey]['point_id'])
    },
    getMarkerDataDetail: function (inPointId) {
      if (('group' in this.$root.mydata) === true) {
        apiObj.clearRequestParam()
        apiObj.setRequestParam('get_mode', 'detail')
        apiObj.setRequestParam('point_id', inPointId)
        apiObj.setRequestParam('mapkey', this.$root.mydata['group'])
        apiObj.sendRequest('map_detail_list_get', this.callBackMapDataEditDataset)
      }
    },
    callBackMapDataEditDataset: function (inResponse) {
      if (inResponse.type === 'success') {
        if (inResponse.data[0]['map_detail_list'].length === 1) {
          var targetMapData = inResponse.data[0]['map_detail_list'][0]
          this.createMapDataMessageData(targetMapData)
          this.gisObject.setPoint(targetMapData['latitude'], targetMapData['longitude'])
          this.gisObject.movePoint()
        }
      }
    },
    createMapDataMessage: function (inData) {
      var outString = ''
      outString = '<div class="mapcontents" style="overflow:scroll;height:30vh;width:50vw;"><p style="overflow:scroll;height:30vh;width:50vw;">読み込み中</p></div>'
      return outString
    },
    createMapDataMessageData: function (inData) {
      var outString = ''
      outString = outString + '<div class="col"><h3>' + inData['title'] + '</h3></div>'
      outString = outString + '<div class="col"><div class="alert alert-info" style="width:100%;" role="alert">' + inData['contents'] + '</div></div>'
      if (inData['image'] !== '') {
        outString = outString + '<div class="col"><img src="' + inData['image'] + '" style="max-height:500px;max-width:500px;"></div>'
      } else {
        outString = outString + '<div class="col"><img src="' + this.$root.asset.common.noimage + '" style="max-height:500px;max-width:500px;"></div>'
      }
      outString = '<div class="container">' + outString + '</div>'
      var mapContents = document.querySelector('.mapcontents')
      mapContents.innerHTML = outString
    }
  },
  created: function () {
    apiObj.setAtoken(this.$root.atoken)
    this.$root.checkAuthCode()
    this.reg_data_format_list = datasetFormat
  },
  mounted: function () {
    var mapBasePanelConf = this.$root.config.map_config
    var mapBasePanelInfo = this.$root.config.map_panel_info[mapBasePanelConf.portal_map_panel]
    this.gisObject = new GisHelper()
    this.gisObject.setDefaultZoomLevel(mapBasePanelConf.zoomlevel)
    this.gisObject.setPoint(mapBasePanelConf.latitude, mapBasePanelConf.longitude)
    this.gisObject.setMapPanel(mapBasePanelInfo.panel, mapBasePanelInfo.attribution, mapBasePanelConf.maxzoomlevel)
    this.gisObject.createMap()
    this.getDatasetList()
    this.$root.menu_disp_hidden()

    for (var ekey in this.$root.config.map_edit_type) {
      for (var mkey in this.$root.config.map_edit_type[ekey]) {
        var iconBasePath = this.$root.asset.map[this.$root.config.map_edit_type[ekey][mkey]['image_type']]
        var iconPath = iconBasePath
        var iconRealPath = iconPath
        this.icon_image[mkey] = iconRealPath
        this.icon_image_name[mkey] = this.$root.config.map_edit_type[ekey][mkey]['name']
        var setOption = {}
        setOption['size'] = []
        setOption['size'] = [52.5, 62]
        this.gisObject.setMarkerIcon(mkey, iconRealPath, iconRealPath, setOption)
      }
    }

    for (var dlKey in this.$root.config.map_datalayter_info) {
      this.forcus_datalayer_list[dlKey] = false
    }
  }
}
</script>
<style scoped>
#map {
    width: 100%;
    height:90vh;
}
.leaflet-popup-content {
     width:50vw !important;
}
.leaflet-popup {
    position: absolute;
    text-align: center;
    margin-bottom: 20px;
    width: 55vw !important;
}
</style>
