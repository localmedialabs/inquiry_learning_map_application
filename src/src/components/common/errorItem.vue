<template>
<div>
  <p v-show="message_flg" class="text-danger">{{message}}</p>
</div>
</template>
<script>
export default {
  props: {
    'errorItem': '',
    'alert': 'off',
    'errorItemList': {}
  },
  data () {
    return {
      'message_flg': false,
      'message': ''
    }
  },
  watch: {
    errorItemList: function () {
      this.loadMessage()
    }
  },
  methods: {
    loadMessage () {
      if ((this.errorItem in this.errorItemList) === true) {
        this.message_flg = true
        this.message = this.errorItemList[this.errorItem]
      } else {
        this.message_flg = false
        this.message = ''
      }
      if (this.alertFlg === 'on') {
        alert('入力された内容に不備があります。入力項目を確認してください。')
      }
    }
  },
  created: function () {
    this.loadMessage()
  },
  mounted: function () {
  },
  updated: function () {
    this.loadMessage()
  }
}
</script>
<style scoped>

</style>
