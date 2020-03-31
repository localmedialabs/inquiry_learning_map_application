<template>
  <div>
    <div id="map"></div>
    <div class="card contents_area_ll">
      <h2 class="card-header">{{$root.current_label.about_app_name}}</h2>
      <div class="card-body contents_area_body_ll">
        <h3>探究学習用地図アプリについて</h3>
        <div class="alert alert-secondary" role="alert">
          <p>当アプリは、様々な探究学習に活用できる地図アプリ（ウェブアプリ）として、[株式会社ローカルメディアラボ](https://lm-labs.com/)が開発しました。経済産業省の「令和元年度経済産業省デジタルプラットフォーム構築事業」で、福岡県行橋市での調査研究において企画、構築したものをベースに、他の取り組みでも活用できるようカスタマイズしたものです。</p>
        </div>
        <p></p>
        <h4>主な機能</h4>
        <p>防災教育や、地域の歴史教育、地域内の様々な施設等を学ぶ場面での活用を想定しています。子どもたちが、自らPOIデータをインポートしたり、取材した情報を書き込んだりすることができます。また、グループ内での情報共有機能も実装しており、グループ学習にも活用できます。</p>
        <h4>想定される利用シーン</h4>
        <p>1. ベースマップ切り替え機能･･･国土地理院地図、オープンストリートマップ等からベースマップを切り替えて表示することができます。</p>
        <p>2. POIデータインポート機能･･･位置情報付きのオープンデータ等をインポートし地図上に表示することができます。</p>
        <p>3. データプリセット機能･･･各種データレイヤーをプリセットし表示することができます（ソースコードの一部改変が必要になります）</p>
        <p>4. データ書き込み機能･･･街歩き等で取材した情報や写真をウェブ側から書き込み、グループ内で共有することができます。</p>
        <p>5. データレイヤー表示機能･･･登録された各種データレイヤーを重ねて表示することができます。</p>
        <h4> 開発元とライセンス</h4>
        <p>本アプリ及びソースコードの著作権は、[株式会社ローカルメディアラボ](https://lm-labs.com/)に帰属します。但し、このソースコードは[MITライセンス](./LICENSE.txt)の元提供されています。このライセンスに従えば、どなたでも利用、改変、及び再配布が可能です。</p>
      </div>
      <div class="card-footer">
        <button class="btn btn btn-block"  v-on:click="$root.transitionLink('/portal')">TOPに戻る</button>
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
    },
    getDatasetList: function () {
      apiObj.sendRequest('dataset_list', this.loadDatasetList)
    },
    getMapList: function () {
      apiObj.sendRequest('map_list_get', this.loadMapList)
    },
    loadDatasetList: function (inResponse) {
      if (inResponse.type === 'success') {
        this.dataset_list = inResponse.data[0]['dataset']
      }
    },
    loadMapList: function (inResponse) {
      if (inResponse.type === 'success') {
        this.map_list = inResponse.data[0]['maplist']
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
#map {
    width: 100%;
    height:90vh;
}
</style>
