<template>
  <div class="splach_main_area">
    <div><img v-bind:src="$root.asset.startup.startup" class="splach_main_image"></div>
    <div><img v-bind:src="$root.asset.common.loading" class="splach_loading"></div>
  </div>
</template>

<script>
export default {
  name: 'startup',
  data () {
    return {}
  },
  methods: {
    movePortal: function () {
      this.$root.startup_load = true
      this.$root.execLogout()
    }
  },
  created: function () {
    this.$root.startup_load = false
  },
  mounted: function () {
    window.setTimeout(this.movePortal, 3000)
  }
}
</script>

<style scoped>
</style>
