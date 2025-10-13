
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

                <div v-if="dataModel.sprint_id !== null && dataModel.works" class="col-12 pv ph-2" >
                  <div class="bold text-h6">From Works : <span class="text-primary">{{dataModel.works.name}}</span></div>
                </div>

                <div v-if="dataModel.sprints" class="col-12 pv ph-2" >
                  <div class="bold text-h6">From Sprints : <span class="text-primary">{{dataModel.sprints.name}}</span></div>
                </div>

                <div class="col-12 pv ph-2" v-if="dataModel.sprint_id === null" >
                  <q-select ref="works" label="Works" dense filled square class=""
                    :options="select.works"
                    v-model="dataModel.work_id"
                    option-value="id" option-label="name"
                    emit-value map-options use-input
                    @filter="(val, update) => filterSelect(val, update, 'works')"
                    @input-value="searchWorksEvt"
                    hint="Type to search works.."
                    lazy-rules :rules="[
                      val => val !== null && val !== '' || 'Work must be choose!',
                    ]"
                  >
                    <template v-slot:no-option>
                      <q-item> <q-item-section class="text-grey">{{select.noOptionLabel}}</q-item-section> </q-item>
                    </template>
                  </q-select>
                </div>

                <div class="col-12 col-sm-9 pv ph" >
                  <q-input v-model="dataModel.name" label="name" dense filled square
                    lazy-rules :rules="[
                    val => val !== null && val !== '' || 'Task Name must be filled',
                  ]" />
                </div>

                <div class="col-12 col-sm-3 pv ph" >
                  <q-select label="Status" dense filled square class=""
                    :options="select.status"
                    v-model="dataModel.status"
                    option-value="id" option-label="name"
                    emit-value map-options
                    lazy-rules :rules="[
                      val => val !== null && val !== '' || 'Please select one of status',
                    ]"
                  >
                    <template v-slot:no-option>
                      <q-item> <q-item-section class="text-grey"> Not found </q-item-section> </q-item>
                    </template>
                  </q-select>
                </div>

                <div class="col-12 pv ph-2" >
                  <q-select label="Labels" dense filled square class=""
                    multiple
                    :options="select.labels"
                    v-model="dataModel.labels"
                    option-value="id" option-label="name"
                    emit-value map-options use-chips
                  >
                    <template v-slot:no-option>
                      <q-item> <q-item-section class="text-grey"> Not found </q-item-section> </q-item>
                    </template>
                  </q-select>
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

                <div class="col-12 pv ph-2" >
                  <q-select label="Assignees" dense filled square class=""
                    multiple
                    :options="select.users"
                    v-model="dataModel.assignees"
                    option-value="user_id" option-label="name"
                    map-options use-chips
                  >
                    <template v-slot:no-option>
                      <q-item> <q-item-section class="text-grey"> Not found </q-item-section> </q-item>
                    </template>
                  </q-select>
                </div>

                <div class="col-12 col-sm-3 col-md-3 pv ph" >
                  <q-field v-model="dataModel.mandays" dense filled square label="Mandays" >
                    <template v-slot:control="{ id, floatingLabel, value }">
                      <money :id="id" class="q-field__input text-right" :value="value" @input="val => $Helper.emitModel('mandays', val, dataModel)" v-bind="moneyFormat" v-show="floatingLabel" />
                    </template>
                  </q-field>
                </div>

                <div class="col-12 col-sm-3 col-md-3 pv ph" v-if="dataModel.id" >
                  <q-field v-model="dataModel.mandays_actual" dense filled square label="Actual Mandays" >
                    <template v-slot:control="{ id, floatingLabel, value }">
                      <money :id="id" class="q-field__input text-right" :value="value" @input="val => $Helper.emitModel('mandays_actual', val, dataModel)" v-bind="moneyFormat" v-show="floatingLabel" />
                    </template>
                  </q-field>
                </div>

                <div class="col-12 col-sm-2 col-md-2 pv ph " >
                  <q-input type="number" v-model="dataModel.progress" label="Progress" dense filled square />
                </div>

                <div class="col-12 col-sm-2 pv ph mt-1" >
                  <q-toggle size="lg" floating dense
                    v-model="dataModel.is_overtime"
                    icon="update"
                    label="Overtime ?"
                    class="bold "
                    emit-value left-label
                  >
                    <q-tooltip>To define this Task is basic or Overtime</q-tooltip>
                  </q-toggle>
                </div>

                <div class="col-12 pv ph mt-1" >
                  <span class="bold text-h5 text-dark">Description</span>
                </div>

                <div class="col-12 pv ph">
                  <q-editor v-model="dataModel.description"
                    :toolbar="$Config.editorTollbar($q)"
                    :fonts="$Config.editorFont()"
                    :min-height="'400px'"
                  />
                </div>

                <div class="col-12 pv ph" v-if="false"  >
                  <Editor v-model="dataModel.description"  />
                  <vl-textarea v-model="dataModel.description"  />
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
// import Editor from '../../components/Editor'
import { debounce } from 'quasar'

export default {
  name: 'Tasks',
  components: {
    // Editor
  },
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
      moneyFormat: this.$Config.currencyConfig(),
      disableSubmit: false,
      select: {
        noOptionLabel: 'Type 2 character to find work..',
        works: [],
        worksTmp: [],
        sprints: [],
        sprintsTmp: [],
        status: [],
        statusTmp: [],
        users: [],
        usersTmp: [],
        labels: [],
        labelsTmp: []
      }
    }
  },

  created () {
    this.dataModel = this.$Helper.unReactive(this.Meta.model)
    this.initTopBar()
    this.searchWorksEvt = debounce(this.searchWorksEvt, 500)
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
      this.getListSelect('projects/' + this.dataModel.project_id + '/task-status?limit=0', 'status')
      this.getListSelect('projects/' + this.dataModel.project_id + '/task-labels?limit=0', 'labels')
      // this.getListSelect('projects/' + this.dataModel.project_id + '/works?limit=0', 'works')
      this.getListSelect('projects/' + this.dataModel.project_id + '/members?limit=0', 'users')
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

          if (data.works) {
            this.select.works.push(data.works)
            this.select.worksTmp.push(data.works)
          }
        }
      })
    },

    backToRoot () {
      this.$router.push({ name: 'project-tasks', params: { project_id: this.dataModel.project_id } })
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

          if (data.length === 0) this.select.noOptionLabel = 'Data not found'

          setTimeout(() => {
            if (selectSource === 'works') {
              var el = this.$refs.works
              el.focus()
            }
          }, 300)
        }
      })
    },

    async filterSelect (val, update, target) {
      this.select = await this.$Helper.filterSelect(val, update, target, this.select)
    },

    searchWorksEvt (val) {
      console.log('select', val)
      if (val.length > 1) this.getListSelect('projects/' + this.dataModel.project_id + '/works?limit=0&search=name:' + val + '', 'works')
    }
  }
}
</script>
