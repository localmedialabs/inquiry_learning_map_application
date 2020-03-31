<template>
  <div class="mx-auto" style="width: 60vh">
    <h1>{{$root.current_label.registration_app_name}}</h1>

    <div id="smartwizard" class="sw-theme-arrows">
      <ul class="nav nav-tabs step-anchor">
        <li v-bind:class="{active: stepActiveStatus('input')}"><a href="#">{{$root.current_label.app_parts_registration_input_step_1}}<br><small>{{$root.current_label.app_parts_registration_input_step_1_label}}</small></a></li>
        <li v-bind:class="{active: stepActiveStatus('confirm')}"><a href="#">{{$root.current_label.app_parts_registration_input_step_2}}<br><small>{{$root.current_label.app_parts_registration_input_step_2_label}}</small></a></li>
        <li v-bind:class="{active: stepActiveStatus('complete')}"><a href="#">{{$root.current_label.app_parts_registration_input_step_3}}<br><small>{{$root.current_label.app_parts_registration_input_step_3_label}}</small></a></li>
      </ul>
    </div>

    <template v-if="input_step === 'input'">
    <h2>{{$root.current_label.app_parts_registration_input_area_title}}</h2>
    <div class="form-group">
      <label for="exampleInputEmail1">{{$root.current_label.app_parts_registration_form_name}}</label>
      <input type="text" v-model="input_data.name" class="form-control" id="" aria-describedby="" placeholder="">
      <small id="inputHelp" class="form-text text-muted"></small>
      <errorMessage :errorItem="'name'" :errorItemList="$root.error_item" />
    </div>
    <div class="form-group">
      <label for="exampleInputEmail1">{{$root.current_label.app_parts_registration_form_user_id}}</label>
      <input type="text" v-model="input_data.userId" class="form-control" id="" aria-describedby="" placeholder="">
      <small id="inputHelp" class="form-text text-muted"></small>
      <errorMessage :errorItem="'userId'" :errorItemList="$root.error_item" />
    </div>
    <div class="form-group">
      <label for="exampleInputEmail1">{{$root.current_label.app_parts_registration_form_group}}</label>
      <select v-model="input_data.group" class="form-control">
        <option v-bind:value="ugData.value" v-for="(ugData,ugIdx) in $root.config.user_group" :key="ugIdx">{{ugData.name}}</option>
      </select>
      <small id="inputHelp" class="form-text text-muted"></small>
      <errorMessage :errorItem="'group'" :errorItemList="$root.error_item" />
    </div>
    <div class="form-group">
      <label for="exampleInputEmail1">{{$root.current_label.app_parts_registration_form_password}}</label>
      <input type="text" v-model="input_data.password" class="form-control" aria-describedby="" placeholder="">
      <small id="inputHelp" class="form-text text-muted"></small>
      <errorMessage :errorItem="'password'" :errorItemList="$root.error_item" />
    </div>
    <button class="btn btn-lg btn btn-warning btn-block" v-on:click="step_confirm">
      {{$root.current_label.app_parts_registration_input_comfirm_button}}
    </button>
    </template>
    <template v-if="input_step === 'confirm'">
    <h2>{{$root.current_label.app_parts_registration_comfirm_area_title}}</h2>
    <div class="form-group">
      <label for="exampleInputEmail1">{{$root.current_label.app_parts_registration_form_name}}</label>
      {{input_data.name}}
    </div>
    <div class="form-group">
      <label >{{$root.current_label.app_parts_registration_form_user_id}}</label>
      {{input_data.userId}}
    </div>
    <div class="form-group">
      <label >{{$root.current_label.app_parts_registration_form_group}}</label>
      <div v-for="(ugData,ugIdx) in $root.config.user_group" :key="ugIdx">
        <p v-if="ugData.value === input_data.group">{{ugData.name}}</p>
      </div>
    </div>
    <div class="form-group">
      <label >{{$root.current_label.app_parts_registration_form_password}}</label>
      {{input_data.password}}
    </div>
    <button class="btn btn-lg btn btn-warning btn-block" v-on:click="step_complete">
      {{$root.current_label.app_parts_registration_input_complete_button}}
    </button>
    <button class="btn btn-lg btn-block" v-on:click="step_input">
      {{$root.current_label.app_parts_registration_input_return_button}}
    </button>
    </template>
    <template v-if="input_step === 'complete'">
    <h2>{{$root.current_label.app_parts_registration_complete_area_title}}</h2>
    <button class="btn btn-lg btn btn-warning btn-block" v-on:click="step_input">
      {{$root.current_label.app_parts_registration_continue_regist_button}}
    </button>
    <div class="form-group">
      <router-link to="/login">{{$root.current_label.app_parts_registration_login_button}}</router-link>
    </div>
    </template>
  </div>
</template>

<script>
import ApiHelper from '@/utility/requesthelper'
var apiObj = new ApiHelper()

export default {
  name: 'registration',
  data () {
    return {
      input_step: 'input',
      input_data: {
        'name': '',
        'userId': '',
        'group': '',
        'password': '',
        'password_confirmation': ''
      }
    }
  },
  computed: {
    stepActiveStatus: function (inType) {
      return function (inType) {
        var outStr = false
        if (inType === this.input_step) {
          outStr = true
        }
        return outStr
      }
    }
  },
  methods: {
    step_input: function () {
      this.input_step = 'input'
    },
    step_confirm: function () {
      apiObj.setRequestParam('name', this.input_data.name)
      apiObj.setRequestParam('userId', this.input_data.userId)
      apiObj.setRequestParam('group', this.input_data.group)
      apiObj.setRequestParam('password', this.input_data.password)
      apiObj.sendRequestValidate('account_regist', this.send_confirm)
    },
    step_complete: function () {
      apiObj.setRequestParam('name', this.input_data.name)
      apiObj.setRequestParam('userId', this.input_data.userId)
      apiObj.setRequestParam('group', this.input_data.group)
      apiObj.setRequestParam('password', this.input_data.password)
      apiObj.setRequestParam('exec_mode', '')
      apiObj.sendRequest('account_regist', this.send_complete)
    },
    send_confirm: function (inResponse) {
      this.$root.loadErrorItem(inResponse)
      if (inResponse.type === 'success') {
        this.input_step = 'confirm'
      }
    },
    send_complete: function (inResponse) {
      if (inResponse.type === 'success') {
        this.input_step = 'complete'
        this.reset_input_data()
      }
    },
    reset_input_data: function () {
      this.input_data = {
        'name': '',
        'userId': '',
        'password': '',
        'password_confirmation': ''
      }
    }
  },
  created: function () {
    if (this.$root.config.app_config.user_regist !== 'on') {
      this.$router.push('/portal', () => {})
    }
    this.reset_input_data()
  }
}
</script>
