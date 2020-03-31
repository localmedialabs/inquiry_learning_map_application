<template>
  <div id="app">
    <nav class="header_area" v-show="$root.startup_load">
      <img v-bind:src="$root.asset.common.title" class="header_title" v-on:click="$root.transitionLink('/portal')">
      <img v-bind:src="$root.asset.common.menu" class="header_menu_button" v-on:click="$root.menu_disp">
    </nav>
    <nav class="navbar app_menu_area" v-bind:class="{'app_menu_area_active':$root.menu_mode, 'app_menu_area_close':!$root.menu_mode}">
      <!-- Menu -->
      <ul class="navbar-nav" v-for="(am, amKey) in $root.config.app_menu"  :key="amKey">
        <li class="nav-item app_menu_item">
          <template v-if="am.enable === true">
          <a class="nav-link" v-on:click="$root.transitionMenuLink(am.url)">
            <img v-bind:src="$root.asset.menu[am.icon_key]" width="30" v-if="$root.asset.menu[am.icon_key] !== ''">
            &nbsp;&nbsp;{{am.name}}</a>
          </template>
          <template v-if="am.enable === false">
            <span   class="disable_menu">
            <img v-bind:src="$root.asset.menu[am.icon_key]" width="30" v-if="$root.asset.menu[am.icon_key] !== ''">
            &nbsp;&nbsp;{{am.name}}
            </span>
          </template>
        </li>
      </ul>
    </nav>
    <router-view/>
  </div>
</template>

<script>

export default {
  name: 'App'
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
