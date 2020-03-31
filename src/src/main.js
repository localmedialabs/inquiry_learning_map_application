// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import VueCookies from 'vue-cookies'

import BootstrapVue from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import './css/common/custom.css'

import ComponentError from '@/components/common/errorItem.vue'
import appConfig from './config/app.config.json'
import appLabel from './config/app.label.json'

import AssetHelper from '@/utility/asset/index.js'
import ApiHelper from '@/utility/requesthelper'
var _ao = new ApiHelper()

Vue.use(BootstrapVue)
Vue.use(VueCookies)

Vue.component('errorMessage', ComponentError)

Vue.config.productionTip = false
/* 認証処理 */
var Auth = {
  loggedIn: false,
  login: function () { this.loggedIn = true },
  logout: function () { this.loggedIn = false }
}

/* eslint-disable no-new */
const _app = new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>',
  data: {
    'atoken': '',
    'startup_load': true,
    'menu_mode': false,
    'lang': 'jp',
    'config': appConfig,
    'label': appLabel,
    'mydata': {},
    'asset': {},
    'current_label': {},
    'auth_status': Auth.loggedIn,
    'error_status': false,
    'error_item': {}
  },
  methods: {
    /** メニュー操作 */
    menu_disp: function () {
      if (this.menu_mode === false) {
        this.menu_mode = true
      } else {
        this.menu_mode = false
      }
    },
    menu_disp_hidden: function () {
      this.menu_mode = false
    },
    /** ログイン（他コンポーネント利用用） */
    login: function (inAtoken) {
      this.atoken = inAtoken
      Vue.$cookies.config('1d')
      Vue.$cookies.set('atoken', inAtoken)
      Auth.login()
      this.auth_status = Auth.loggedIn
      _ao.setAtoken(this.atoken)
      _ao.sendRequest('mydata', this.loadMydataLogin)
    },
    /** ログアウト（他コンポーネント利用用） */
    logout: function () {
      if (confirm(appLabel[this.lang]['com_text_logout']) === true) {
        this.execLogout()
      }
    },
    /** ログアウト処理 */
    execLogout: function () {
      this.atoken = ''
      Vue.$cookies.remove('atoken')
      Auth.logout()
      this.auth_status = Auth.loggedIn
      this.$router.push('/portal', () => {})
    },
    /** 画面遷移 */
    transitionLink: function (inUrl) {
      if (inUrl !== this.$route.path) {
        this.$router.push(inUrl, () => {})
      }
    },
    transitionMenuLink: function (inUrl) {
      this.menu_disp()
      this.transitionLink(inUrl)
    },
    /** リロード時の再ログイン */
    reloadAuthStatus: function () {
      var atoken = Vue.$cookies.get('atoken')
      if (atoken !== '') {
        this.$root.login(atoken)
      }
    },
    /** ログイン後のアカウントデータ取得および画面遷移 */
    loadMydataLogin: function (inResponse) {
      if (inResponse.type === 'success') {
        this.loadMydata(inResponse)
        this.loginTransition()
      }
    },
    /** ログイン後のアカウントデータ取得 */
    loadMydata: function (inResponse) {
      if (inResponse.type === 'success') {
        this.mydata = inResponse.data[0].mydata
      }
    },
    /** ログイン後の画面遷移 */
    loginTransition: function () {
      if ('redirect' in this.$route.query) {
        var rdUrl = this.$route.query['redirect']
        this.$router.push(rdUrl, () => {})
      } else {
        this.$router.push('/portal', () => {})
      }
    },
    checkAuthCode: function () {
      _ao.setAtoken(this.atoken)
      _ao.sendRequest('auth_check', this.refreshAuthCode)
    },
    refreshAuthCode: function (inResponse) {
      if (inResponse.type === 'success') {
        this.atoken = inResponse.data[0].atoken
      } else {
        this.execLogout()
      }
    },
    loadErrorItem: function (inResponse) {
      this.error_item = {}
      this.error_status = false
      if (inResponse.type === 'warning') {
        this.error_status = true
        for (let vidx in inResponse.data) {
          this.error_item[inResponse.data[vidx]['item_name']] = inResponse.data[vidx]['message']
        }
      }
    }
  },
  created: function () {
    this.current_label = appLabel[this.lang]
    if (this.$route.path !== '/') {
      this.reloadAuthStatus()
    }
    var asObj = new AssetHelper()
    this.asset = asObj.getAssetData()
  },
  mounted: function () {
  }
})

router.beforeEach((to, from, next) => {
  /** 存在パスチェック */
  if (!to.matched.some(record => record.path)) {
    next({path: '/startup'})
  } else {
    /** 認証領域 */
    if (to.matched.some(record => record.meta.requiresAuth) === true && Auth.loggedIn === true) {
      /** ログアウト */
      if (to.matched.some(record => record.meta.AuthClear) === true) {
        _app.execLogout()
        next()
      /** 認証移動 */
      } else {
        next()
      }
    /** 認証切れ */
    } else if (to.matched.some(record => record.meta.requiresAuth) === true && Auth.loggedIn === false) {
      next({path: '/login', query: { redirect: to.fullPath }})
    /** 非認証領域 */
    } else {
      next()
    }
  }
})
