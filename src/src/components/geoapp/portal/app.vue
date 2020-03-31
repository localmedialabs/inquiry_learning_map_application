<template>
  <div>
    <div id="map"></div>
    <div class="card bottom_menu_area" v-on:click="changeMenuArea" v-bind:class="{'bottom_menu_area_active':menu_mode, 'bottom_menu_area_close':!menu_mode}">
      <div class="card-body">
      </div>
    </div>
  </div>
</template>

<script>
import GisHelper from '@/utility/gishelper'
import ApiHelper from '@/utility/requesthelper'
var apiObj = new ApiHelper()

export default {
  name: 'portal',
  data () {
    return {
      gisObject: null,
      menu_mode: false,
      current_area: '',
      dataset_list: [],
      map_list: []
    }
  },
  computed: {
    dataLayreMenuStatus: function (inType) {
      return function (inType) {
        var outStr = false
        if (inType === this.current_area) {
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
    setArea: function (inArea) {
      this.current_area = inArea
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
