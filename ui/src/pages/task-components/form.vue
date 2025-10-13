
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

                <div v-if="dataModel.tasks" class="col-12 pv ph-2" >
                  <div class="bold text-h6">From Task : <span class="text-primary">{{dataModel.tasks.name}}</span></div>
                </div>

                <div class="col-12 col-sm-9 pv ph" >
                  <q-input v-model="dataModel.name" label="name" dense filled square
                    lazy-rules :rules="[
                    val => val !== null && val !== '' || 'Component Name must be filled',
                  ]" />
                </div>

                <div class="col-12 col-sm-3 pv ph" >
                  <q-select label="Type" dense filled square class=""
                    :options="select.type" v-model="dataModel.type"
                  >
                  <template v-slot:selected-item="scope">
                    <q-icon :name="$Config.iconComponent(scope.opt)" /> &nbsp;  <span>{{scope.opt}}</span>
                  </template>
                  <template v-slot:option="scope">
                      <q-item
                        v-bind="scope.itemProps"
                        v-on="scope.itemEvents"
                      >
                        <q-item-section avatar>
                          <q-icon :name="$Config.iconComponent(scope.opt)" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label v-html="scope.opt" />
                        </q-item-section>
                      </q-item>
                    </template>
                  </q-select>
                </div>

                <div class="col-12 pv ph" >
                  <q-input v-model="dataModel.summary" label="summary" dense filled square />
                </div>

                <div class="col-12 pv ph mt-1" >
                  <span class="bold text-h5 text-dark">Description</span>
                </div>
                <div class="col-12 pv ph" >

                  <q-editor v-model="dataModel.description"
                    :toolbar="$Config.editorTollbar($q)"
                    :fonts="$Config.editorFont()"
                    :min-height="'400px'"
                  />

                </div>

              </q-card-section>

              <q-card-actions align="right" class="">
                <q-btn class="capital bold" unelevated flat color="red" label="Cancel" icon="cancel" @click="backToRoot" />
                <q-btn class="capital bold" unelevated color="green" label="Save" :disable="disableSubmit" type="submit" icon="check_circle"/>
              </q-card-actions>
            </q-form>
          </q-card-section>
      </q-card>

      <div class="mb-5">.</div>

  </div>
</template>

<script>
import Meta from './meta'

export default {
  name: 'TaskComponents',
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
        type: ['DOC', 'DOC-API', 'SQL', 'RULE', 'NOTE', 'OTHER']
      }
    }
  },

  created () {
    this.dataModel = this.$Helper.unReactive(this.Meta.model)
    this.initTopBar()
  },

  mounted () {
    this.dataModel.project_id = this.$Helper.hasProjectId(this)

    var params = this.$route.params
    if (this.$Helper.checkParams(params)) { // checking access update
      if (params.task_id !== undefined) this.dataModel.task_id = params.task_id

      if (params.id !== undefined) {
        this.onRefresh()
        this.getData(params.id)
      } else this.onRefresh()
    } else this.onRefresh()
  },

  watch: {
    $route (to, from) {
      this.dataModel = Meta.model
    }
  },

  methods: {

    onRefresh () {
      //
      this.getTask()
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
          if (data.description === null) data.description = ''
          this.dataModel = data
          this.title = 'Edit'
        }
      })
    },

    backToRoot () {
      this.$router.push({ name: 'view-tasks', params: { project_id: this.dataModel.project_id, id: this.dataModel.task_id } })
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

    getTask () {
      this.$Helper.loadingOverlay(true, 'Loading..')
      var endpoint = 'tasks/' + this.dataModel.task_id
      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          // inject data
          this.dataModel.tasks = data
        }
      })
    }
  }
}
</script>
