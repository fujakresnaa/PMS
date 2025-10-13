
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

                <div class="col-12 pv ph-2" >
                  <q-select label="Works" dense filled square class=""
                    :options="select.works"
                    v-model="dataModel.work_id"
                    option-value="id" option-label="name"
                    emit-value map-options
                    @filter="(val, update) => filterSelect(val, update, 'works')"
                    lazy-rules :rules="[
                      val => val !== null && val !== '' || 'Please select Works!',
                    ]"
                  >
                    <template v-slot:no-option>
                      <q-item> <q-item-section class="text-grey"> Not found </q-item-section> </q-item>
                    </template>
                  </q-select>
                </div>

                <div class="col-12 pv ph" >
                  <q-input v-model="dataModel.name" label="name" dense filled square
                    lazy-rules :rules="[
                    val => val !== null && val !== '' || 'Sprint Name must be filled',
                  ]" />
                </div>

                <div class="col-12 pv pt pb-2" >
                  <q-input v-model="dataModel.goals" label="goals" dense filled square />
                </div>

                <div class="col-12 col-sm-6 col-md-6 pv ph" >
                  <q-input label="Start" filled v-model="dataModel.start_date" dense square >
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
                  <q-input label="End" filled v-model="dataModel.end_date" dense square >
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
  name: 'Sprints',
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
        works: [],
        worksTmp: []
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
      this.getListSelect('projects/' + this.dataModel.project_id + '/works?limit=0', 'works')
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
      this.$router.push({ name: 'project-sprints', params: { project_id: this.dataModel.project_id } })
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
