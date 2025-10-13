
<template >

  <div class="root bg-grey-4">

      <!-- Header Title -->
      <div class="row mb-5">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3">
          <q-card class="box-shadow mv-2 mt-3">
            <q-card-section class="bg-primary text-white">
              <div class="ph-2"><q-icon name="lock" size="62px"/></div>
              <div class="text-h6">Password</div>
              <div class="text-caption">change password</div>
            </q-card-section>
            <q-card-section>
              <q-form @submit="submit">
                <q-card-section class="row">

                <div class="col-12 pv ph" >
                  <q-input type="password" v-model="dataModel.current_password" label="current password *" dense filled square
                  lazy-rules :rules="[
                    val => val !== null && val !== '' || 'current password must be filled!',
                  ]" />
                </div>

                <div class="col-12 pv ph" >
                  <q-input type="password" v-model="dataModel.password" label="new password *" dense filled square
                  lazy-rules :rules="[
                    val => val !== null && val !== '' || 'new password must be filled!',
                  ]" />
                </div>

                </q-card-section>

                <q-card-actions align="right" class="">
                  <q-btn class="capital bold" unelevated flat color="red" label="Cancel" icon="cancel" @click="backToRoot" />
                  <q-btn class="capital bold" unelevated color="green" label="Save" :disable="disableSubmit" type="submit" icon="check_circle"/>
                </q-card-actions>
              </q-form>
            </q-card-section>
          </q-card>
        </div>
      </div>

  </div>
</template>

<script>
import Meta from './meta'

export default {
  name: 'Users',
  data () {
    return {
      Meta,
      API: this.$Api,
      // default data
      title: 'Change Password',
      dataModel: {
        id: null,
        current_password: null,
        password: null
      },
      rules: {
        permission: Meta.permission
      },
      disableSubmit: false,
      select: {
        departments: [],
        departmentsTmp: [],
        roles: [],
        rolesTmp: [],
        menus: [],
        menusTmp: []
      }
    }
  },

  created () {
    this.initTopBar()
    this.$ModuleConfig.getCurrentPermissions((status, data) => {
      console.log('initPermissionPage:' + Meta.module, data)
      this.initialize()
    }, 'user-form')
  },

  mounted () {

  },

  watch: {
    $route (to, from) {
      // this.dataModel = Meta.model
    }
  },

  methods: {

    checkPermission (mode = 'create') {
      var access = this.$ModuleConfig.checkPermission(this.$router, this.Meta.module + '-' + mode)
      if (access) return true
      else this.$router.push({ name: '403' })
    },

    initialize () {
      var credentials = this.$Config.credentials()
      console.log('cre', credentials)
      this.setData(credentials.id)
    },

    onRefresh () {
      //
    },

    initTopBar () {
      this.Meta.topBarMenu = [{ name: 'Refresh', event: this.onRefresh }]
    },

    setData (id) {
      this.dataModel.id = id
    },

    edit (data) {
      this.triggerForm(data)
    },

    backToRoot () {
      this.$router.push({ name: 'home' })
    },

    emitModel (target, val) {
      this.dataModel[target] = val
    },

    submit () {
      if (this.validateSubmit()) {
        this.disableSubmit = true
        this.update()
      }
    },

    validateSubmit () {
      if (this.dataModel.current_password === null && this.dataModel.password === null) {
        this.$Helper.showAlert('Password Empty!', 'Password must be filled!')
        return false
      } else return true
    },

    update () {
      this.$Helper.loadingOverlay(true, 'Saving..')
      this.API.post('me/change-password/' + this.dataModel.id, this.dataModel, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          this.messageSubmit('Update', message)
          this.backToRoot()
        } else this.disableSubmit = false
      })
    },

    messageSubmit (titleAdd = '', msg) {
      this.$Helper.showAlert(titleAdd + ' Succesfully', msg)
    }

  }
}
</script>
