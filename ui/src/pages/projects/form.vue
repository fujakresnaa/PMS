
<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta" :disableDrawer="true" v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-8 offset-md-2">
          <q-card class="box-shadow mv-2 mt-3">
            <q-card-section class="bg-primary text-white">
              <div class="ph-2"><q-icon name="developer_board" size="62px"/></div>
              <div class="text-h6">{{title}} {{Meta.name}} <span v-if="title === 'Update'" >#{{dataModel.id}}</span></div>
              <div class="text-caption">Master {{Meta.name}}</div>
            </q-card-section>
            <q-card-section>
              <q-form @submit="submit">
                <q-card-section class="row">

                  <div class="col-12 pv ph" >
                    <q-input v-model="dataModel.code" label="code" dense filled square
                    lazy-rules :rules="[
                      val => val !== null && val !== '' || 'Code is required!',
                    ]"/>
                  </div>

                  <div class="col-12 pv ph" >
                    <q-input v-model="dataModel.name" label="name" dense filled square
                    lazy-rules :rules="[
                      val => val !== null && val !== '' || 'Name is required!',
                    ]"/>
                  </div>

                  <div class="col-12 col-sm-6 col-md-6 pv ph" >
                    <q-input label="Start Date" filled v-model="dataModel.start_date" dense square
                      lazy-rules :rules="[
                        val => val !== null && val !== '' || 'Start Date is required!',
                      ]"
                      >
                      <!-- Tanggal -->
                      <q-popup-proxy persistent ref="popupDateTime" transition-show="scale" transition-hide="scale">
                        <q-date v-model="dataModel.start_date" @input="() =>{ $refs.popupDateTime.hide(); $refs.popupJam.show() }" mask="YYYY-MM-DD HH:mm"></q-date>
                      </q-popup-proxy>
                      <!-- Jam -->
                      <div>
                        <q-popup-proxy persistent ref="popupJam" transition-show="scale" transition-hide="scale">
                          <q-time v-model="dataModel.start_date" mask="YYYY-MM-DD HH:mm" format24h>
                            <div class="row items-center justify-end"><q-btn v-close-popup label="Oke" color="primary" flat /></div>
                          </q-time>
                        </q-popup-proxy>
                      </div>
                    </q-input>
                  </div>

                  <div class="col-12 col-sm-6 col-md-6 pv ph" >
                    <q-input label="End Date" filled v-model="dataModel.end_date" dense square
                      lazy-rules :rules="[
                        val => val !== null && val !== '' || 'End Date is required!',
                      ]">
                      <!-- Tanggal -->
                      <q-popup-proxy persistent ref="popupDateTime2" transition-show="scale" transition-hide="scale">
                        <q-date v-model="dataModel.end_date" @input="() =>{ $refs.popupDateTime2.hide(); $refs.popupJam2.show() }" mask="YYYY-MM-DD HH:mm"></q-date>
                      </q-popup-proxy>
                      <!-- Jam -->
                      <div>
                        <q-popup-proxy persistent ref="popupJam2" transition-show="scale" transition-hide="scale">
                          <q-time v-model="dataModel.end_date" mask="YYYY-MM-DD HH:mm" format24h>
                            <div class="row items-center justify-end"><q-btn v-close-popup label="Oke" color="primary" flat /></div>
                          </q-time>
                        </q-popup-proxy>
                      </div>
                    </q-input>
                  </div>

                  <div class="col-12 pv ph-2" >
                    <q-select label="Members" dense filled square class=""
                      multiple
                      :options="select.users"
                      v-model="dataModel.members"
                      option-value="user_id" option-label="name"
                      map-options use-chips
                    >
                      <template v-slot:no-option>
                        <q-item> <q-item-section class="text-grey"> Not found </q-item-section> </q-item>
                      </template>
                    </q-select>
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
  name: 'Projects',
  data () {
    return {
      Meta,
      API: this.$Api,
      // default data
      title: 'Create',
      dataModel: {},
      rules: {
        permission: Meta.permission
      },
      disableSubmit: false,
      select: {
        users: [],
        usersTmp: []
      }
    }
  },

  created () {
    this.dataModel = this.$Helper.unReactive(this.Meta.model)
    this.initTopBar()
    this.$ModuleConfig.getCurrentPermissions((status, data) => {
      console.log('initPermissionPage:' + Meta.module, data)
      this.initialize()
    }, 'projects')
  },

  mounted () {

  },

  watch: {
    $route (to, from) {
      this.dataModel = Meta.model
    }
  },

  methods: {

    checkPermission (mode = 'create') {
      var access = this.$ModuleConfig.checkPermission(this.$router, this.Meta.module + '-' + mode)
      if (access) return true
      else this.$router.push({ name: '403' })
    },

    initialize () {
      var params = this.$route.params
      if (this.$Helper.checkParams(params)) { // checking access update
        if (this.checkPermission('update')) {
          if (params.id !== undefined) {
            this.onRefresh()
            this.getData(params.id)
          } else this.backToRoot()
        }
      } else { // checking access create
        if (this.checkPermission('create')) {
          this.onRefresh()
        }
      }
    },

    onRefresh () {
      //
      this.getListSelect('users?limit=0', 'users')
    },

    initTopBar () {
      this.Meta.topBarMenu = [{ name: 'Refresh', event: this.onRefresh }]
    },

    getData (id) {
      console.log('getData')
      this.$Helper.loadingOverlay(true, 'Loading..')
      var endpoint = this.Meta.module + '/' + id
      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          // inject data
          this.dataModel = data
          this.title = 'Edit'
        }
      })
    },

    edit (data) {
      this.triggerForm(data)
    },

    backToRoot () {
      this.$router.push({ name: this.Meta.module })
    },

    emitModel (target, val) {
      this.dataModel[target] = val
    },

    submit () {
      if (this.validateSubmit()) {
        this.disableSubmit = true
        if (this.dataModel.id !== null) this.update()
        else this.save()
      }
    },

    validateSubmit () {
      // if (this.dataModel.name === null) {
      //   this.$Helper.showAlert('Nama Kosong!', 'Nama harus di isi!')
      //   return false
      // } else return true
      return true
    },

    save () {
      this.$Helper.loadingOverlay(true, 'Saving..')
      this.API.post(this.Meta.module, this.dataModel, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          this.messageSubmit('Saving', message)
          this.backToRoot()
        } else this.disableSubmit = false
      })
    },

    update () {
      this.$Helper.loadingOverlay(true, 'Saving..')
      this.API.put(this.Meta.module + '/' + this.dataModel.id, this.dataModel, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          this.messageSubmit('Update', message)
          this.backToRoot()
        } else this.disableSubmit = false
      })
    },

    messageSubmit (titleAdd = '', msg) {
      this.$Helper.showAlert(titleAdd + ' Succesfully', msg)
    },

    getListSelect (endpoint, selectSource = null) {
      this.$Helper.loadingOverlay(true)
      // var endpoint = 'menus'
      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          var targetSource = endpoint
          if (selectSource) targetSource = selectSource
          var tmpName = targetSource + 'Tmp'

          this.select[targetSource] = data
          this.select[tmpName] = data
        }
      })
    },

    async filterSelect (val, update, target) {
      this.select = await this.$Helper.filterSelect(val, update, target, this.select)
    }

  }
}
</script>
