<template>
<div>
    <div id="map"></div>
    <div class="card form-signin">
      <div class="card-body" v-show="!$root.auth_status">
      <h1  class="h3 mb-3 font-weight-normal">{{$root.current_label.login_app_name}}</h1>
        <div class="form-group">
          <label for="userId">{{$root.current_label.com_parts_user_id}}</label>
          <input type="text" class="form-control" id="userId" v-model="userId" aria-describedby="loginIdHelp" placeholder="">
          <small id="loginIdHelp" class="form-text text-muted"></small>
          <errorMessage :errorItem="'userId'" :errorItemList="$root.error_item" />
        </div>
        <div class="form-group">
          <label for="loginPassword">{{$root.current_label.com_parts_user_password}}</label>
          <input type="text" class="form-control" id="loginPassword" v-model="password" aria-describedby="passwordHelp" placeholder="">
          <small id="passwordHelp" class="form-text text-muted"></small>
          <errorMessage :errorItem="'password'" :errorItemList="$root.error_item" />
        </div>
        <div class="form-group">
          <button class="btn btn-lg btn btn-warning btn-block" v-on:click="login">{{$root.current_label.com_parts_login}}</button>
        </div>
      </div>
      <div class="card-body" v-show="$root.auth_status">
        <h1  class="h3 mb-3 font-weight-normal">{{$root.current_label.logout_app_name}}</h1>
        <div class="form-group">
          <button class="btn btn-lg btn btn-warning btn-block" v-on:click="logout">{{$root.current_label.com_parts_logout}}</button>
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
  name: 'login',
  data () {
    return {
      'userId': '',
      'password': ''
    }
  },
  methods: {
    login: function () {
      apiObj.setRequestParam('userId', this.userId)
      apiObj.setRequestParam('password', this.password)
      apiObj.sendRequest('login', this.authAccess)
    },
    logout: function () {
      this.$root.logout()
    },
    authAccess: function (inResponse) {
      this.$root.loadErrorItem(inResponse)
      if (inResponse.type === 'success') {
        this.$root.login(inResponse.data[0].atoken)
      }
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
.form-signin {
    position: absolute;
    top: 25vh;
    left: 25vw;
    z-index: 950;
    width: 50vw;
    color: #000;
    background: rgba(255,255,255, 0.8);
}
#map {
    width: 100%;
    height:90vh;
}
</style>
