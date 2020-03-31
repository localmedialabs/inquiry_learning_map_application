<template>
  <div>
    <div id="map"></div>
    <div class="modalarea_base" v-show="edit_create_mode">
      <div class="card modalarea_contents">
        <div class="card-block">
          <h4 class="card-header">{{$root.current_label.app_parts_mapedit_edit_title}}</h4>
          <div class="card-body">
            <div class="form-group">
              <label for="map_title h4">{{$root.current_label.app_parts_mapedit_edit_base_title}}</label>
              <input type="text" class="form-control" id="map_title" placeholder="" v-model="base_map.title" />
            </div>
            <div class="form-group">
              <label for="map_contents h4">{{$root.current_label.app_parts_mapedit_edit_base_contents}}</label>
              <textarea id="map_contents" class="form-control" v-model="base_map.description" ></textarea>
            </div>
          </div>
          <div class="card-footer">
            <button class="btn btn-lg btn btn-warning btn-block" v-on:click="createMap">{{$root.current_label.app_parts_mapedit_edit_form_create_button}}</button>
          </div>
        </div>
      </div>
    </div>
    <div class="edit_base_area"  v-show="current_edit_flag">
      <div class="card contents_area_ss" v-show="current_edit_mode.menu">
        <p class="mt-1 mb-1 ml-1 mr-1">
          <table style="text-align: center;width: 100%;height: 100%;">
            <tr>
              <td class="col3"><img src="@/assets/map/edit_menu.png" style="height:5vh" v-on:click="changeEditArea"></td>
              <td class="col3"><img src="@/assets/map/trash.png" style="height:5vh" v-on:click="delDetailData"></td>
              <td class="col3"><img src="@/assets/common/cancel.png" style="height:5vh" v-on:click="closeModal"></td>
            </tr>
          </table>
        </p>
      </div>
      <div class="card contents_area_l" v-show="current_edit_mode.edit">
        <div class="card-body">
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a class="nav-link h3" v-on:click="changeContentsArea('base')" v-bind:class="{'active': contentsAreaActiveStatus('base'), '': !contentsAreaActiveStatus('base')}">{{$root.current_label.app_parts_mapedit_tab_title_1}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link h3" v-on:click="changeContentsArea('contents_area_1')" v-bind:class="{'active': contentsAreaActiveStatus('contents_area_1'), '': !contentsAreaActiveStatus('contents_area_1')}">{{$root.current_label.app_parts_mapedit_tab_title_2}}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link h3" v-on:click="changeContentsArea('contents_area_2')" v-bind:class="{'active': contentsAreaActiveStatus('contents_area_2'), '': !contentsAreaActiveStatus('contents_area_2')}">{{$root.current_label.app_parts_mapedit_tab_title_3}}</a>
            </li>
          </ul>
          <div v-show="contentsAreaActiveStatus('base')" class="mt-3 mb-3 ml-3 mr-3">
            <div class="row">
              <div class="col">
                <img v-bind:src="icon_image[current_edit.icon_type]" width="75" />
              </div>
              <div class="col">
                <p class="display-4">{{icon_image_name[current_edit.icon_type]}}</p>
              </div>
            </div>
            <div class="row">
              <div class="col">
                {{$root.current_label.app_parts_mapedit_edit_base_latitude}}{{current_edit.latitude}}
              </div>
              <div class="col">
                {{$root.current_label.app_parts_mapedit_edit_base_longitude}}{{current_edit.longitude}}
              </div>
            </div>
            <div class="row">
              <div class="col" v-if="('radius' in current_edit.extension) === true">
                {{$root.current_label.app_parts_mapedit_edit_base_radius}}{{convDispDistanceFormat(current_edit.extension.radius)}}
              </div>
            </div>
          </div>
          <div v-show="contentsAreaActiveStatus('contents_area_1')">
            <h4>{{$root.current_label.app_parts_mapedit_edit_form_area_title}}</h4>
            <div class="form-group">
              <label for="marker_title h4">{{$root.current_label.app_parts_mapedit_edit_form_title}}</label>
              <input type="text" class="form-control" id="marker_title" placeholder="" v-model="current_edit.title" />
              <errorMessage :errorItem="'title'" :errorItemList="$root.error_item" />
            </div>
            <div class="form-group h4">
              <label for="marker_contents">{{$root.current_label.app_parts_mapedit_edit_form_contents}}</label>
              <textarea id="marker_contents" class="form-control" v-model="current_edit.contents" ></textarea>
              <errorMessage :errorItem="'contents'" :errorItemList="$root.error_item" />
            </div>
          </div>
          <div v-show="contentsAreaActiveStatus('contents_area_2')">
            <h4>{{$root.current_label.app_parts_mapedit_edit_file_area_title}}</h4>
            <div class="form-group">
              <label for="datasettitle">{{$root.current_label.app_parts_mapedit_edit_form_file}}</label>
              <input type="file" class="btn" v-on:change="onFileChange" id="asset_image" >
              <errorMessage :errorItem="'image'" :errorItemList="$root.error_item" />
            </div>
            <div class="form-group" v-if="current_edit.image !== ''">
              <img v-bind:src="current_edit.image" style="height: 20vh;">
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-lg btn btn-warning btn-block" v-on:click="saveMarker">{{$root.current_label.app_parts_mapedit_edit_form_save_button}}</button>
          <template v-if="current_edit.edit_status !== 'create'">
            <button class="btn btn-lg btn btn-block" v-on:click="closeModal">{{$root.current_label.app_parts_mapedit_edit_form_close_button}}</button>
          </template>
          <template v-if="current_edit.edit_status === 'create'">
            <button class="btn btn-lg btn btn-block" v-on:click="closeModalNewEdit">{{$root.current_label.app_parts_mapedit_edit_form_close_button}}</button>
          </template>
        </div>
      </div>
    </div>
    <div class="card bottom_menu_area" v-on:click="changeMenuArea" v-bind:class="{'bottom_menu_area_active':menu_mode, 'bottom_menu_area_close':!menu_mode}">
      <div class="card-block">
        <h4 class="card-header"><img v-bind:src="$root.asset.menu.everyoneMap" width="30">&nbsp;&nbsp;{{$root.current_label.mapedit_app_name}}</h4>
        <div class="card-body">
          <div data-toggle="buttons" v-for="(icmd,icmd_key) in $root.config.map_edit_type"  :key="icmd_key">
            <div class="btn-group btn-group-toggle group_button_box_l ml-1 mb-1" v-for="(icd,icd_key) in icmd"  :key="icd_key">
              <label class="btn" v-bind:class="{'btn btn-warning': buttonActiveStatus(icd_key), 'btn-outline-primary': !buttonActiveStatus(icd_key)}">
                <img v-bind:src="$root.asset.map[icd.image_type]" style="height:5vh;" />
                <input type="radio" v-model="edit_type" v-bind:value="icd_key" v-bind:id="icd_key" autocomplete="off"  v-on:click="setMarker(icmd_key, icd_key)"><br>{{icd.name}}
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import GisHelper from '@/utility/gishelper'
import ApiHelper from '@/utility/requesthelper'

var apiObj = new ApiHelper()

export default {
  name: 'mapedit',
  data () {
    return {
      gisObject: null,
      menu_mode: false,
      contents_mode: false,
      /** 新規・編集切り替え */
      edit_create_mode: false,
      /** */
      edit_type: '',
      /** マーカKEY */
      icon_image_key: '',
      /** マーカーアイコン */
      icon_image: {},
      icon_image_name: {},
      /** 詳細エリア表示 */
      current_edit_flag: false,
      current_edit_mode: {
        'menu': true,
        'edit': false
      },
      /** 詳細エリアタブ */
      current_edit_area: 'base',
      current_edit_layer_id: '',
      current_edit_object: null,
      base_map: {
        'mapkey': '',
        'title': '',
        'description': '',
        'acl_type': '',
        'acl_user': []
      },
      current_edit: {
        'map_detail_key': '',
        'edit_status': '',
        'latitude': '',
        'longitude': '',
        'extension': {},
        'title': '',
        'contents': '',
        'image': '',
        'marker_type': '',
        'icon_type': ''
      },
      data_list: {}
    }
  },
  computed: {
    buttonActiveStatus: function (inType) {
      return function (inType) {
        var outStr = false
        if (inType === this.edit_type) {
          outStr = true
        }
        return outStr
      }
    },
    contentsAreaActiveStatus: function (inType) {
      return function (inType) {
        var outStr = false
        if (inType === this.current_edit_area) {
          outStr = true
        }
        return outStr
      }
    }
  },
  methods: {
    clearEditType: function () {
      this.edit_type = ''
    },
    changeMenuArea: function () {
      if (this.menu_mode === false) {
        this.menu_mode = true
      } else {
        this.menu_mode = false
      }
    },
    initEditArea: function () {
      this.current_edit_mode.menu = true
      this.current_edit_mode.edit = false
    },
    changeEditArea: function () {
      if (this.current_edit_mode.menu === true) {
        this.current_edit_mode.menu = false
        this.current_edit_mode.edit = true
      } else {
        this.current_edit_mode.menu = true
        this.current_edit_mode.edit = false
      }
    },
    createMap: function () {
      apiObj.setRequestParam('title', this.base_map.title)
      apiObj.setRequestParam('description', this.base_map.description)
      apiObj.setRequestParam('exec_mode', '')
      apiObj.sendRequest('map_regist', this.callBackCreateMap)
    },
    callBackCreateMap: function (inResponse) {
      if (inResponse.type === 'success') {
        this.base_map.mapkey = inResponse.data[0].mapkey
        this.edit_create_mode = false
      }
      if (this.base_map.mapkey === '') {
        this.$root.transitionLink('/portal')
      }
    },
    checkEditMode: function () {
    },
    changeContentsArea: function (inContentsType) {
      this.current_edit_area = inContentsType
    },
    /** マーカーデータ選択 */
    setMarker: function (inMenuType, inMarkerType) {
      this.current_edit_flag = false
      this.gisObject.setMapClickCallBack(this.callBackInit)
      switch (this.$root.config.map_edit_type[inMenuType][inMarkerType].type) {
        case 'marker':
          this.gisObject.setCancel()
          /** 設置イベント */
          this.gisObject.setMapClickCallBack(this.callBackMapClick)
          break
        case 'polyline':
          this.gisObject.setDrawPolyline()
          this.gisObject.setDrawCreateCallBack(this.callBackPolyline)
          break
        case 'polygon':
          this.gisObject.setDrawPolygon()
          this.gisObject.setDrawCreateCallBack(this.callBackPolygon)
          break
        case 'circle':
          this.gisObject.setDrawCircle()
          this.gisObject.setDrawCreateCallBack(this.callBackCircle)
          break
        case 'rectangle':
          this.gisObject.setDrawRectangle()
          this.gisObject.setDrawCreateCallBack(this.callBackRectangle)
          break
      }
    },
    clearMarker: function () {
      this.gisObject.setMapClickCallBack(null)
      this.gisObject.setCancel()
      this.edit_type = ''
    },
    /** マーカーデータ描写 */
    loadMarkerData: function (inIconType, inExtension) {
      var setLayerId = 'map_' + inIconType
      switch (inIconType) {
        case 'marker':
          /** setLayerId = inExtension['lat'] + ':' + inExtension['lng'] */
          /** this.gisObject.setMarker('Marker', inExtension['lat'], inExtension['lng'], inExtension['icon_type']) */
          this.gisObject.setMarker(setLayerId, inExtension['lat'], inExtension['lng'], inExtension['icon_type'])
          break
        case 'polyline':
          /** setLayerId = inExtension['latlngs'][0][0] + ':' + inExtension['latlngs'][0][1] */
          this.gisObject.setPolyline(setLayerId, inExtension['latlngs'], this.callbackMapDataEdit)
          break
        case 'polygon':
          /** setLayerId = inExtension['latlngs'][0][0] + ':' + inExtension['latlngs'][0][1] */
          this.gisObject.setPolygon(setLayerId, inExtension['latlngs'], this.callbackMapDataEdit)
          break
        case 'circle':
          /** setLayerId = inExtension['latlngs'][0]['lat'] + ':' + inExtension['latlngs'][0]['lng'] */
          this.gisObject.setCircle(setLayerId, inExtension['latlngs'][0], inExtension['radius'], this.callbackMapDataEdit)
          break
        case 'rectangle':
          /** setLayerId = inExtension['latlngs'][0][0] + ':' + inExtension['latlngs'][0][1] */
          this.gisObject.setRectangle(setLayerId, inExtension['latlngs'], this.callbackMapDataEdit)
          break
      }
    },
    /** マップクリック（新規）イベントコールバック用 */
    callBackMapClick: function () {
      if (this.gisObject.getClickLatitude() !== 0 && this.gisObject.getClickLongitude() !== 0) {
        this.initMapEditData()
        this.current_edit_mode.menu = false
        this.current_edit_mode.edit = true

        /** var setLayerId = this.gisObject.getClickLatitude() + ':' + this.gisObject.getClickLongitude() */
        var setLayerId = 'draw'
        /** this.gisObject.setMarker('Marker', this.gisObject.getClickLatitude(), this.gisObject.getClickLongitude(), this.edit_type) */
        this.gisObject.setMarker(setLayerId, this.gisObject.getClickLatitude(), this.gisObject.getClickLongitude(), this.edit_type)
        this.gisObject.mountMarker()
        this.current_edit.latitude = this.gisObject.getClickLatitude()
        this.current_edit.longitude = this.gisObject.getClickLongitude()
        this.current_edit_flag = true
        this.current_edit_area = 'base'
        this.current_edit.marker_type = 'marker'
        this.current_edit.icon_type = this.edit_type
        this.current_edit.map_detail_key = this.current_edit.latitude + ':' + this.current_edit.longitude
        this.current_edit.edit_status = 'create'

        this.current_edit.extension = {}

        this.gisObject.setPoint(this.gisObject.getClickLatitude(), this.gisObject.getClickLongitude())
        this.gisObject.movePoint()

        /** this.data_list[this.current_edit.map_detail_key] = this.current_edit */
        /** this.clearEditType() */
      }
    },
    callBackPolyline: function (e) {
      this.current_edit_mode.menu = false
      this.current_edit_mode.edit = true

      this.current_edit_layer_id = this.gisObject.getShapesLayerId(e, 'Polyline')
      this.current_edit_object = e
      var setTmpData = this.gisObject.getShapesData(e, 'Polyline')
      this.setCurrentEditData('Polyline', setTmpData['latlngs'][0]['lat'], setTmpData['latlngs'][0]['lng'], 'polyline', setTmpData)
      this.current_edit.edit_status = 'create'
      this.gisObject.setPoint(setTmpData['latlngs'][0]['lat'], setTmpData['latlngs'][0]['lng'])
      this.gisObject.movePoint()
    },
    callBackPolygon: function (e) {
      this.current_edit_mode.menu = false
      this.current_edit_mode.edit = true

      this.current_edit_layer_id = this.gisObject.getShapesLayerId(e, 'Polygon')
      this.current_edit_object = e
      var setTmpData = this.gisObject.getShapesData(e, 'Polygon')
      this.setCurrentEditData('Polygon', setTmpData['latlngs'][0]['lat'], setTmpData['latlngs'][0]['lng'], 'polygon', setTmpData)
      this.current_edit.edit_status = 'create'
      this.gisObject.setPoint(setTmpData['latlngs'][0]['lat'], setTmpData['latlngs'][0]['lng'])
      this.gisObject.movePoint()
    },
    callBackCircle: function (e) {
      this.current_edit_mode.menu = false
      this.current_edit_mode.edit = true

      this.current_edit_layer_id = this.gisObject.getShapesLayerId(e, 'Circle')
      this.current_edit_object = e
      var setTmpData = this.gisObject.getShapesData(e, 'Circle')
      this.setCurrentEditData('Circle', setTmpData['latlngs'][0]['lat'], setTmpData['latlngs'][0]['lng'], 'circle', setTmpData)
      this.current_edit.edit_status = 'create'
      this.gisObject.setPoint(setTmpData['latlngs'][0]['lat'], setTmpData['latlngs'][0]['lng'])
      this.gisObject.movePoint()
    },
    callBackRectangle: function (e) {
      this.current_edit_mode.menu = false
      this.current_edit_mode.edit = true

      this.current_edit_layer_id = this.gisObject.getShapesLayerId(e, 'Rectangle')
      this.current_edit_object = e
      var setTmpData = this.gisObject.getShapesData(e, 'Rectangle')
      this.setCurrentEditData('Rectangle', setTmpData['latlngs'][0]['lat'], setTmpData['latlngs'][0]['lng'], 'rectangle', setTmpData)
      this.current_edit.edit_status = 'create'
      this.gisObject.setPoint(setTmpData['latlngs'][0]['lat'], setTmpData['latlngs'][0]['lng'])
      this.gisObject.movePoint()
    },
    callBackInit: function () {
    },
    /** callback マーカアイコンクリック処理 */
    callbackMapDataEdit: function (inEvent) {
      this.initEditArea()
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
      this.getMarkerDataDetail(this.data_list[setMapDetailKey]['point_id'])
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
          this.setCurrentEditData(targetMapData['marker_type'], targetMapData['latitude'], targetMapData['longitude'], targetMapData['marker_type'], targetMapData['extension'])
          this.current_edit.title = targetMapData['title']
          this.current_edit.contents = targetMapData['contents']
          this.current_edit.icon_type = targetMapData['icon_type']
          this.current_edit.image = targetMapData['image']
          this.gisObject.setPoint(targetMapData['latitude'], targetMapData['longitude'])
          this.gisObject.movePoint()
        }
      }
    },
    setCurrentEditData: function (inType, inLat, inLng, inMarker, inExtension) {
      this.current_edit.map_detail_key = inType + ':' + inLat + ':' + inLng

      this.current_edit.map_detail_key = ''
      this.current_edit.edit_status = ''
      this.current_edit.latitude = inLat
      this.current_edit.longitude = inLng
      this.current_edit.extension = inExtension
      this.current_edit.title = ''
      this.current_edit.contents = ''
      this.current_edit.image = ''
      this.current_edit.marker_type = inMarker
      this.current_edit.icon_type = ''

      this.current_edit_flag = true
      this.current_edit_area = 'base'
      this.current_edit.icon_type = this.edit_type
    },
    onFileChange: function (e) {
      var fileMetaData = {}
      fileMetaData = apiObj.loadFileMeta(e)
      if (apiObj.fileTypeCheck(fileMetaData[0], 'image') === true) {
        apiObj.loadFile(e, this.callbackDataFileLoad, 'bin')
      } else {
        alert('ファイル形式が異なるため添付できませんでした。')
      }
    },
    callbackDataFileLoad (inData) {
      this.current_edit.image = inData.target.result
    },
    saveMarker: function () {
      this.changeContentsArea('contents_area_1')
      if (window.confirm('このデータを記録しますか？') === true) {
        apiObj.clearRequestParam()
        apiObj.setRequestParam('mapkey', this.base_map.mapkey)
        apiObj.setRequestParam('latitude', this.current_edit.latitude)
        apiObj.setRequestParam('longitude', this.current_edit.longitude)
        apiObj.setRequestParam('extension', this.current_edit.extension)
        apiObj.setRequestParam('title', this.current_edit.title)
        apiObj.setRequestParam('contents', this.current_edit.contents)
        apiObj.setRequestParam('marker_type', this.current_edit.marker_type)
        apiObj.setRequestParam('icon_type', this.current_edit.icon_type)
        apiObj.setRequestParam('image', this.current_edit.image)
        apiObj.setRequestParam('exec_mode', '')
        apiObj.sendRequest('map_edit_mod', this.callBackSaveMap)
      }
    },
    callBackSaveMap: function (inResponse) {
      this.$root.loadErrorItem(inResponse)
      if (inResponse.type === 'success') {
        this.initMapEditData()
        this.clearMarker()
        this.reloadMapData()
        this.current_edit_flag = false
        this.current_edit_object = null
        alert('記録しました。')
      }
    },
    closeModal: function () {
      this.current_edit_flag = false
    },
    closeModalNewEdit: function () {
      if (this.current_edit_object !== null) {
        this.closeModalRemoveDraw()
        this.current_edit_object = null
      } else {
        this.closeModalRemove()
      }
    },
    closeModalRemove: function () {
      this.current_edit_flag = false
      if (window.confirm('編集を破棄します。よろしいですか？') === true) {
        /** var layerId = this.current_edit.latitude + ':' + this.current_edit.longitude */
        var layerId = 'draw'
        this.gisObject.removeMarkerLayer(layerId)
        this.clearMarker()
        this.current_edit_flag = false
      }
    },
    closeModalRemoveDraw: function () {
      this.current_edit_flag = false
      if (window.confirm('編集を破棄します。よろしいですか？') === true) {
        if (this.current_edit_object !== null) {
          this.gisObject.removeMarkerObject('draw', this.current_edit_object.layer)
          this.current_edit_object = null
          this.current_edit_layer_id = ''
          this.current_edit_flag = false
          this.clearMarker()
        }
      }
    },
    delDetailData: function () {
      if (window.confirm('このデータを削除しますか？') === true) {
        apiObj.setRequestParam('mapkey', this.$root.mydata['group'])
        apiObj.setRequestParam('latitude', this.current_edit.latitude)
        apiObj.setRequestParam('longitude', this.current_edit.longitude)
        apiObj.sendRequest('map_detail_del', this.callbackDelDetailData)
      }
    },
    callbackDelDetailData: function (inResponse) {
      if (inResponse.type === 'success') {
        this.clearMarker()
        this.reloadMapData()
        this.initMapEditData()
        this.current_edit_flag = false
        this.current_edit_object = null
        alert('削除しました。')
      }
    },
    /** サーバサイドの地図マーカデータ取得 */
    getMarkerData: function () {
      if (('group' in this.$root.mydata) === true) {
        apiObj.clearRequestParam()
        apiObj.setRequestParam('get_mode', 'index')
        apiObj.setRequestParam('mapkey', this.$root.mydata['group'])
        apiObj.sendRequest('map_detail_list_get', this.loadMapDetailData)
      }
    },
    /** 地図基本情報読み込み */
    loadMapInfo: function (inResponse) {
      if (inResponse.type === 'success') {
        if (inResponse.data[0]['maplist'].length > 0) {
          this.base_map.mapkey = inResponse.data[0]['maplist'][0]['store']
          this.base_map.title = inResponse.data[0]['maplist'][0]['title']
          this.base_map.description = inResponse.data[0]['maplist'][0]['description']
          this.edit_create_mode = false
        }
      }
    },
    /** サーバサイドの地図マーカデータ読み込み設定 */
    loadMapDetailData: function (inResponse) {
      if (inResponse.type === 'success') {
        if (inResponse.data[0]['map_detail_list'].length > 0) {
          for (var mdkey in inResponse.data[0]['map_detail_list']) {
            var setMapDetailData = {}
            var setMapDetailKey = ''
            var targetMapData = []
            targetMapData = inResponse.data[0]['map_detail_list'][mdkey]

            setMapDetailKey = targetMapData['latitude'] + ':' + targetMapData['longitude']
            setMapDetailData.point_id = targetMapData['point_id']
            setMapDetailData.latitude = targetMapData['latitude']
            setMapDetailData.longitude = targetMapData['longitude']
            setMapDetailData.icon_type = targetMapData['icon_type']
            setMapDetailData.marker_type = targetMapData['marker_type']
            setMapDetailData.map_detail_key = setMapDetailKey
            setMapDetailData.title = targetMapData['title']
            setMapDetailData.edit_status = ''
            setMapDetailData.extension = targetMapData['extension']
            if (('latlngs' in targetMapData['extension']) === true) {
              setMapDetailData.latitude = targetMapData['extension'].latlngs[0].lat
              setMapDetailData.longitude = targetMapData['extension'].latlngs[0].lng
              setMapDetailKey = setMapDetailData.latitude + ':' + setMapDetailData.longitude
            }

            this.data_list[setMapDetailKey] = setMapDetailData
          }
          this.dispMapDetailData()
        }
      }
    },
    /** 地図マーカデータ読み込み */
    dispMapDetailData: function () {
      var markerFlg = false
      for (var dkey in this.data_list) {
        if (this.data_list[dkey]['marker_type'] === 'marker') {
          this.data_list[dkey]['extension']['lat'] = this.data_list[dkey]['latitude']
          this.data_list[dkey]['extension']['lng'] = this.data_list[dkey]['longitude']
          this.data_list[dkey]['extension']['icon_type'] = this.data_list[dkey]['icon_type']
          markerFlg = true
        }
        this.loadMarkerData(this.data_list[dkey]['marker_type'], this.data_list[dkey]['extension'])
      }
      // マーカー実行
      if (markerFlg === true) {
        this.gisObject.mountMarker(this.callbackMapDataEdit)
      }
    },
    convDispDistanceFormat: function (inData) {
      return this.gisObject.convertDistanceFormat(inData, 'M')
    },
    reloadMapData: function () {
      this.data_list = null
      this.data_list = {}
      this.gisObject.removeMarkerLayerAll()
      this.getMarkerData()
    },
    clearTest: function () {
      this.data_list = null
      this.data_list = {}
      this.gisObject.removeMarkerLayerAll()
    },
    reloadTimerMapData: function () {
      if (this.$route.path === '/mapedit') {
        this.reloadMapData()
        window.setTimeout(this.reloadTimerMapData, 60000)
      }
    },
    initMapEditData: function () {
      this.current_edit = {
        'map_detail_key': '',
        'edit_status': '',
        'latitude': '',
        'longitude': '',
        'extension': {},
        'title': '',
        'contents': '',
        'image': '',
        'marker_type': '',
        'icon_type': ''
      }
      var obj = document.getElementById('asset_image')
      obj.value = ''
    },
    resetDisplay: function () {
      this.$router.go({path: this.$router.currentRoute.path, force: true})
    }
  },
  created: function () {
    apiObj.setAtoken(this.$root.atoken)
    this.$root.checkAuthCode()
    if (('group' in this.$root.mydata) === true) {
      apiObj.setRequestParam('mapkey', this.$root.mydata['group'])
      apiObj.sendRequest('map_list_get', this.loadMapInfo)
    }
  },
  mounted: function () {
    /** GIS関連設定情報読み込み */
    var mapBasePanelConf = this.$root.config.map_config
    var mapBasePanelInfo = this.$root.config.map_panel_info[mapBasePanelConf.portal_map_panel]
    this.gisObject = new GisHelper()
    this.gisObject.setDefaultZoomLevel(mapBasePanelConf.zoomlevel)
    this.gisObject.setPoint(mapBasePanelConf.latitude, mapBasePanelConf.longitude)
    this.gisObject.setMapPanel(mapBasePanelInfo.panel, mapBasePanelInfo.attribution, mapBasePanelConf.maxzoomlevel)

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
    this.gisObject.setMarkerEditOn()
    this.gisObject.createMap()
    this.reloadTimerMapData()
    this.$root.menu_disp_hidden()
  }
}
</script>
<style scoped>
#map {
    width: 100%;
    height:90vh;
}
.edit_base_area {
  position: absolute;
  top: 0vh;
  left: 0vw;
  z-index: 995;
  height:100vh;
  width: 100vw;
  color: #000;
  background: rgba(255,255,255, 0);
}
.col3 {
  text-align: center;
  vertical-align: center;
  width : 33.33333% ;
}
</style>
