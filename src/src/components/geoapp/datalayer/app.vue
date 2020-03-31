<template>
  <div>
    <div id="map"></div>
    <div class="card bottom_menu_area" v-on:click="changeMenuArea" v-bind:class="{'bottom_menu_area_active':menu_mode, 'bottom_menu_area_close':!menu_mode}">
      <h4 class="card-header"><img v-bind:src="$root.asset.menu.disaster" width="30">&nbsp;&nbsp;{{$root.current_label.datalayer_app_name}}</h4>
      <div class="card-body">
        <div class="form-group">
          <div class="btn-group btn-group-toggle group_button_box_m" data-toggle="buttons" v-for="(mp, mp_index) in $root.config.map_datalayter_info" :key="mp_index">
           <label class="btn" v-bind:class="{'btn btn-warning': buttonActiveStatus(mp_index), 'btn-outline-primary': !buttonActiveStatus(mp_index)}">
             <img v-bind:src="$root.asset.disaster[mp.image]" width="50">
             <input type="radio" v-model="dataset_type" v-bind:value="mp_index" v-bind:id="mp_index" autocomplete="off" v-on:click="changeMapPanel(mp_index)"><br>{{mp.name}}
           </label>
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
  name: 'datalayer',
  data () {
    return {
      gisObject: null,
      menu_mode: false,
      contents_mode: false,
      current_data_layer_id: '',
      dataset_type: '',
      dataset_list: [],
      data_list: []
    }
  },
  computed: {
    buttonActiveStatus: function (inType) {
      return function (inType) {
        var outStr = false
        if (inType === this.format_type) {
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
    changeMapPanel: function (inType) {
      this.gisObject.removeBaseMap('datalayer')
      var mapBasePanelConf = this.$root.config.map_config
      var mapBasePanelInfo = this.$root.config.map_datalayter_info[inType]
      this.gisObject.setMapPanel(mapBasePanelInfo.panel, mapBasePanelInfo.attribution, mapBasePanelConf.maxzoomlevel)
      this.gisObject.changeBaseMap('datalayer')
    }
  },
  created: function () {
    apiObj.setAtoken(this.$root.atoken)
  },
  mounted: function () {
    var mapBasePanelConf = this.$root.config.map_config
    var mapBasePanelInfo = this.$root.config.map_panel_info[mapBasePanelConf.portal_map_panel]
    this.gisObject = new GisHelper()
    this.gisObject.setDefaultZoomLevel(mapBasePanelConf.zoomlevel)
    this.gisObject.setPoint(mapBasePanelConf.latitude, mapBasePanelConf.longitude)
    this.gisObject.setMapPanel(mapBasePanelInfo.panel, mapBasePanelInfo.attribution, mapBasePanelConf.maxzoomlevel)
    this.gisObject.createMap()
    this.$root.menu_disp_hidden()
  }
}
</script>
<style scoped>
#map {
    width: 100%;
    height:90vh;
}
</style>
