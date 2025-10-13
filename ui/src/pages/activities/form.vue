
<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row pl-2 pt-2">
        <div class="col-12 col-sm-3 col-md-7 pb-1 pv info-page">
          <div class="title">
            <span class="text-caption text-grey-8">Master {{Meta.name}}</span><br>
            <span class="text-h5 bold text-primary capital">{{title}} {{Meta.name}} <span v-if="title === 'Update'" >#{{dataModel.id}}</span></span>
          </div>
        </div>
      </div>

      <q-card class="box-shadow mv-2">
          <q-card-section>
            <q-form @submit="submit">
              <q-card-section class="row">

                <div class="col-6">
                  <div class="row">
                    <div class="col-md-12 pv ph">
                      <q-input
                        dense
                        filled
                        square
                        label="Title"
                        v-model="dataModel.title"
                      />
                    </div>
                    <div class="col-md-12 pv ph">
                      <q-input
                        dense
                        filled
                        square
                        label="Description"
                        type="textarea"
                        v-model="dataModel.description"
                      />
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="row">
                    <div class="col-md-12 pv ph">
                      <img-uploader v-if="uploadImage.show" :data="uploadImage" class="animated fadeIn" />
                    </div>
                  </div>
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
</template>

<script>
import Meta from './meta'

export default {
  name: 'Activities',
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
      uploadImage: { // wajib seperti ini formatnya
        show: true,
        file: null,
        callback: this.callbackImgUploader, // callback function harus di include jg di script
        subFix: ''
      }
    }
  },

  created () {
    this.dataModel = this.$Helper.unReactive(this.Meta.model)
    this.initTopBar()
    this.$ModuleConfig.getCurrentPermissions((status, data) => {
      console.log('initPermissionPage:' + Meta.module, data)
      this.initialize()
    }, 'activities')
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
      var query = this.$route.query
      if (this.$Helper.checkParams(params)) { // checking access update
        if (this.checkPermission('update')) {
          if (params.id !== undefined) {
            if (params.reference === undefined) {
              this.getData(params.id)
            } else {
              this.onRefresh()
            }
          } else this.backToRoot()
        }
      } else if (query !== null) {
        this.dataModel.reference = query.reference
        this.dataModel.reference_id = query.id
        this.onRefresh()
      } else { // checking access create
        if (this.checkPermission('create')) {
          //
        }
      }
    },

    onRefresh () {
      //
    },

    initTopBar () {
      this.Meta.topBarMenu = [{ name: 'Refresh', event: this.onRefresh }]
    },

    getData (id) {
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

    callbackImgUploader (data) {
      console.log('callbackImgUploader', data)
      this.uploadImage.file = data
      // custom assign file bellow
    },

    save () {
      var query = this.$route.query
      var file = {
        photo: this.uploadImage.file // pastikan atrribute seesuai dengan receiver di API
      }
      this.dataModel.file = file

      this.$Helper.loadingOverlay(true, 'Saving..')
      this.API.isMultipartForm = true
      this.API.post(this.Meta.module, this.dataModel, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          this.messageSubmit('Saving', message)
          if (query !== null) {
            this.$router.push({ name: 'view-' + query.reference, params: query })
          } else {
            this.backToRoot()
          }
        } else this.disableSubmit = false
      })
    },

    update () {
      var query = this.$route.query
      this.$Helper.loadingOverlay(true, 'Saving..')
      this.API.put(this.Meta.module + '/' + this.dataModel.id, this.dataModel, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          this.messageSubmit('Update', message)
          if (query !== null) {
            this.$router.push('/' + query.reference + '/view/' + query.reference_id)
          } else {
            this.backToRoot()
          }
        } else this.disableSubmit = false
      })
    },

    messageSubmit (titleAdd = '', msg) {
      this.$Helper.showAlert(titleAdd + ' Succesfully', msg)
    }
  }
}
</script>
