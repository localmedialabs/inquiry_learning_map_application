<template>
  <div>
    <div id="map"></div>
    <div class="card contents_area_ll" v-show="contents_mode">
      <div class="card-body contents_area_body_ll">
        <h2>{{$root.current_label.app_parts_dataset_title}}</h2>
        <template v-if="contents_display_status === 'input'">
          <div class="form-group">
            <p class="h4">{{$root.current_label.app_text_dataset_description_1}}</p>
          </div>
          <div class="form-group text-center">
            <div class="btn-group btn-group-toggle group_button_box_ll" data-toggle="buttons" v-for="(df, df_index) in reg_data_format_list" :key="df_index">
              <label class="btn" v-bind:class="{'btn btn-primary': buttonActiveStatus(df_index), 'btn-outline-primary': !buttonActiveStatus(df_index)}">
                <img v-bind:src="$root.asset.dataset[df.image_type]" width="70" />
                <input type="radio" v-model="reg_format_type" v-bind:value="df_index" v-bind:id="df_index" autocomplete="off" v-on:click="loadFormat(df_index)"><br>{{df.name}}
              </label>
            </div>
            <errorMessage :errorItem="'type'" :errorItemList="$root.error_item" />
          </div>
          <div class="form-group">
            <p class="h4">{{$root.current_label.app_text_dataset_description_2}}</p>
          </div>
          <div class="form-group">
            <label for="datasettitle">{{$root.current_label.app_parts_dataset_name}}</label>
            <input type="text" v-model="reg_title" class="form-control" id="" aria-describedby="" placeholder="●●市向けAED設置箇所一覧">
            <small id="inputHelp" class="form-text text-muted"></small>
            <errorMessage :errorItem="'title'" :errorItemList="$root.error_item" />
          </div>
          <div class="form-group">
            <p class="h4">{{$root.current_label.app_text_dataset_description_3}}</p>
          </div>
          <div class="form-group">
            <label for="datasettitle">{{$root.current_label.app_parts_dataset_file}}</label>
            <input type="file" class="btn" v-on:change="onFileChange" value="ファイルを添付する">
            <errorMessage :errorItem="'data'" :errorItemList="$root.error_item"/>
          </div>
        </template>
        <template v-if="contents_display_status === 'loading'">
          <div class="text-center h2">{{$root.current_label.app_parts_dataset_loading}}</div>
          <div class="text-center">
            <img v-bind:src="$root.asset.common.loading" class="splach_loading">
          </div>
        </template>
        <template v-if="contents_display_status === 'complete'">
          <div class="text-center h2">{{$root.current_label.app_parts_dataset_complete}}</div>
          <div class="form-group text-center alert alert-primary" role="alert">
            <p class="h4">{{$root.current_label.app_text_dataset_description_4}}</p>
            <p class="h4">{{$root.current_label.app_text_dataset_description_5}}</p>
            <p class="h4">{{$root.current_label.app_text_dataset_description_6}}</p>
          </div>
        </template>
      </div>
      <div class="card-footer">
        <template v-if="contents_display_status === 'input'">
          <button class="btn btn btn-warning btn-block"  v-on:click="saveData">{{$root.current_label.com_parts_regist_button}}</button>
        </template>
        <button class="btn btn btn-block"  v-on:click="closeModal">{{$root.current_label.com_parts_close_button}}</button>
      </div>
    </div>
    <div class="card bottom_menu_area" v-on:click="changeMenuArea" v-bind:class="{'bottom_menu_area_active':menu_mode, 'bottom_menu_area_close':!menu_mode}">
      <h4 class="card-header"><img v-bind:src="$root.asset.menu.facilities" width="30">&nbsp;&nbsp;{{$root.current_label.app_parts_dataset_title}}</h4>
      <div class="card-body">
        <div class="form-group">
          <div class="btn-group btn-group-toggle group_button_box_l ml-1 mb-1" data-toggle="buttons" v-for="(dl, dl_index) in dataset_list" :key="dl_index">
           <label class="btn" v-bind:class="{'btn-primary': buttonActiveStatus(dl.store), 'btn-outline-primary': !buttonActiveStatus(dl.store)}">
             <img v-bind:src="getDatasetAssetPath(dl.store)" width="50">
             <input type="checkbox" v-model="dataset_type" v-bind:value="dl.store" v-bind:id="dl.store" autocomplete="off" v-on:click="getDatasetDetail(dl.store)"><br>{{dl.name}}
           </label>
          </div>
          <div class="btn-group btn-group-toggle ml-1 mb-1" data-toggle="buttons">
           <label class="btn">
             <img v-bind:src="$root.asset.dataset.regist" width="50">
             <input type="radio" v-model="data_reg" value="" v-on:click="showDataRegistArea()"><br>{{$root.current_label.app_parts_dataset_regist_button}}
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
  name: 'data',
  data () {
    return {
      gisObject: null,
      menu_mode: false,
      contents_mode: false,
      contents_display_status: 'input',
      current_data_layer_id: '',
      data_reg: '',
      dataset_type: '',
      dataset_list: [],
      data_list: {},
      forcus_dataset_list: {},
      current_format_type: '',
      reg_current_area: '',
      reg_title: '',
      reg_dataset_file_data: {
        'name': '',
        'data': ''
      },
      reg_format_type: '',
      reg_data_format_list: {},
      reg_current_data_format_item: [],
      reg_data_list: []
    }
  },
  computed: {
    buttonActiveStatus: function (inType) {
      return function (inType) {
        var outStr = false
        if (inType === this.reg_format_type) {
          outStr = true
        }
        return outStr
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
    showDataRegistArea () {
      this.menu_mode = false
      this.contents_display_status = 'input'
      this.contents_mode = true
      this.changeMenuArea()
    },
    loadFormat: function (inType) {
      this.reg_format_type = inType
      if (inType !== '' && (inType in this.reg_data_format_list)) {
        this.reg_current_data_format_item = this.reg_data_format_list[inType]['format']
      }
    },
    onFileChange: function (e) {
      apiObj.loadFile(e, this.callbackDataFileLoad, 'text')
    },
    callbackDataFileLoad (inData) {
      this.reg_dataset_file_data.data = inData.target.result
      if (this.checkFormat() === true) {
        this.reg_data_list = apiObj.convertCsv2Json(this.reg_dataset_file_data.data, this.convertFormat())
      } else {
        alert('このファイルは読み込めませんでした。')
      }
    },
    convertFormat () {
      var outFormat = {}
      var fmIdx = 0
      for (let fIdx in this.reg_current_data_format_item) {
        let currentKey = this.reg_current_data_format_item[fIdx]['item_name']
        outFormat[currentKey] = {}
        outFormat[currentKey]['target_key_idx'] = []
        outFormat[currentKey]['target_key_idx'].push(fmIdx)
        fmIdx++
      }
      return outFormat
    },
    checkFormat () {
      var result = true
      var csvArray = this.reg_dataset_file_data.data.split('\n')
      for (var i = 1; i < csvArray.length; i++) {
        if (csvArray[i].trim() !== '') {
          var csvArrayData = csvArray[i].replace(/\r?\n/g, '').split(',')
          if (csvArrayData.length === this.reg_current_data_format_item.length) {
            for (let vkey in this.reg_current_data_format_item) {
              if (this.reg_current_data_format_item[vkey]['required'] === true) {
                if (apiObj.dataEmptyCheck(csvArrayData[vkey]) === false) {
                  result = false
                }
              }
            }
          } else {
            result = false
          }
        }
      }
      return result
    },
    saveData: function () {
      if (confirm('データを登録しますか？') === true) {
        apiObj.setRequestParam('type', this.reg_format_type)
        apiObj.setRequestParam('title', this.reg_title)
        apiObj.setRequestParam('data', JSON.stringify(this.reg_data_list))
        apiObj.sendRequest('dataset_regist', this.saveLoading)
        this.reg_data_regist_mode = false
      }
    },
    saveLoading: function (inResponse) {
      this.$root.loadErrorItem(inResponse)
      if (inResponse.type === 'success') {
        this.contents_display_status = 'loading'
        window.setTimeout(this.saveComplete, 2000)
      }
    },
    saveComplete: function () {
      this.reg_data_regist_mode = false
      this.contents_display_status = 'complete'
      apiObj.clearRequestParam()
      this.reg_title = ''
      this.reg_format_type = ''
      this.reg_data_list = []
      this.getDatasetList()
    },
    getDatasetList: function () {
      apiObj.sendRequest('dataset_list', this.loadDatasetList)
    },
    loadDatasetList: function (inResponse) {
      if (inResponse.type === 'success') {
        this.dataset_list = inResponse.data[0]['dataset']
      }
    },
    /** ラジオボタン型 */
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
      this.gisObject.removeMarkerLayerAll()
      if ((inDataSetType in this.forcus_dataset_list) === true) {
        /** this.gisObject.removeMarkerLayer(inDataSetType) */
        delete this.forcus_dataset_list[inDataSetType]
        delete this.data_list[inDataSetType]
      } else {
        this.forcus_dataset_list[inDataSetType] = true
        this.current_data_layer_id = inDataSetType
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
        if ((this.current_data_layer_id in this.data_list) === false) {
          this.data_list[this.current_data_layer_id] = []
        }
        this.data_list[this.current_data_layer_id] = apiObj.copyObject(inResponse.data[0]['dataset_detail_list'])
        this.loadMapData()
      }
    },
    loadMapData: function () {
      /** this.gisObject.removeMarkerLayerAll() */
      var setKey = this.current_data_layer_id
      var imagePath = this.$root.asset.dataset[this.current_format_type]
      var setOption = {}
      setOption['size'] = []
      setOption['size'] = [52.5, 62]
      this.gisObject.setMarkerIcon(setKey, imagePath, imagePath, setOption)
      var targetDataList = this.data_list[this.current_data_layer_id]
      for (var di in targetDataList) {
        if (targetDataList[di]['latitude'] !== '' && targetDataList[di]['longitude'] !== '') {
          let setMessage = this.createDataMessage(targetDataList[di])
          this.gisObject.setMarker(this.current_data_layer_id, targetDataList[di]['latitude'], targetDataList[di]['longitude'], setKey, setMessage)
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
      outString = '<div class="container" style="overflow:scroll;height:30vh;">' + outString + '</div>'
      return outString
    },
    callbackMapData: function (inEvent) {
      this.gisObject.setPoint(inEvent.latlng.lat, inEvent.latlng.lng)
      this.gisObject.movePoint()
    },
    closeModal: function () {
      this.contents_mode = false
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
  }
}
</script>
<style scoped>
#map {
    width: 100%;
    height:90vh;
}
.leaflet-popup-content {
     width:auto !important;
}
</style>
