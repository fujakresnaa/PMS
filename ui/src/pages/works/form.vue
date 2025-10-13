
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

      <q-card class="box-shadow mv-2 mb-5">
        <q-card-section>
          <q-form @submit="submit">
            <q-card-section class="row">

              <div class="col-12 pv ph-2" >
                <q-select ref="works" label="Related Works" dense filled square class=""
                  multiple
                  :options="select.works"
                  v-model="dataModel.related"
                  option-value="id" option-label="name"
                  emit-value map-options use-chips use-input
                  @filter="(val, update) => filterSelect(val, update, 'works')"
                  @input-value="searchWorksEvt"
                  hint="Type to search works.."
                >
                  <template v-slot:no-option>
                    <q-item> <q-item-section class="text-grey"> Not found </q-item-section> </q-item>
                  </template>
                </q-select>
              </div>

              <div class="col-12 col-sm-9 pv ph" >
                <q-input v-model="dataModel.name" label="name" dense filled square
                  lazy-rules :rules="[
                  val => val !== null && val !== '' || 'Work Name must be filled',
                ]" />
              </div>

              <div class="col-12 col-sm-3 pv ph" >
                <q-select label="Priority" dense filled square class=""
                  :options="select.priority" v-model="dataModel.priority"
                >
                <template v-slot:selected-item="scope">
                  <q-icon :name="$Config.iconPriority(scope.opt)" /> &nbsp;  <span>{{scope.opt}}</span>
                </template>
                <template v-slot:option="scope">
                    <q-item
                      v-bind="scope.itemProps"
                      v-on="scope.itemEvents"
                    >
                      <q-item-section avatar>
                        <q-icon :name="$Config.iconPriority(scope.opt)" />
                      </q-item-section>
                      <q-item-section>
                        <q-item-label v-html="scope.opt" />
                      </q-item-section>
                    </q-item>
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

              <div class="col-12 col-sm-3 col-md-3 pv ph" >
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

              <div class="col-12 col-sm-3 col-md-3 pv ph" >
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

              <div class="col-12 col-sm-2 col-md-1 pv ph " >
                <q-input type="number" v-model="dataModel.progress" label="Progress" dense filled square />
              </div>

              <div class="col-12 col-sm-2 col-md-2 pv ph mt-1 ml-1" >
                <q-toggle size="lg" floating dense
                  v-model="dataModel.is_module"
                  icon="offline_bolt"
                  label="Module"
                  class="bold "
                  emit-value left-label
                >
                  <q-tooltip>To define this work are "Module" or basic work</q-tooltip>
                </q-toggle>
              </div>

              <div class="col-12 col-sm-2 col-md-2 pv ph mt-1 ml-2" >
                <q-toggle size="lg" color="green" floating dense
                  v-model="dataModel.is_done"
                  icon="check"
                  label="Done"
                  class="bold "
                  emit-value left-label
                >
                  <q-tooltip>To define this work is done, and wanna hide in roadmap</q-tooltip>
                </q-toggle>
              </div>

              <div class="col-12 pv ph mt-2" >
                <q-input type="textarea" v-model="dataModel.summary" label="summary" dense filled square />
              </div>

              <div class="col-12 pv ph mt-1" >
                <span class="bold text-dark pr-1">Mockup & Flowchart Links</span>
                <q-btn size="sm" flat dense color="blue-9" label="Create Mockup / Flow Chart" class="capital bg-blue-1 pv" icon="history_edu" @click="$Helper.openLink('https://app.diagrams.net/')" />
                <br>
                <small class="text-grey-7 pr-1">
                Design Mockup & Flowchart di <span class="text-orange-9">"https://app.diagrams.net/"</span> dan hasilnya akan diintegerasikan di view detail work,
                <span @click="readMore = !readMore" class="text-blue link">
                <span v-if="!readMore" >baca</span>
                <span v-if="readMore" >tutup</span>
                detail panduan .. </span>

                <span v-if="readMore" class="animated fadeIn text-dark"> <br>
                Klik tombol "Create Mockup / Flow Chart" diatas, jika sudah di alihkan ke app diagrams, dan muncul modal "Save diagrams to" ,
                pilih / klik "Decide later", namun jika ingin menyimpannya di google drive atau yang lainnnya sialahkan <hr>
                Setelah selesai membuat diagram, kli menu "File > Publish > Link", atur sesuai kebutuhan, jika bingung ikuti default saja, lalu klik "Create" <br>
                lalu copy link yang disediakan atau klik tombol "Copy" , setelah itu paste di kolom mockup / flow link .
                </span>
                </small> <br>
              </div>

              <div class="col-12 col-sm-6 pv ph mt-2" >
                <q-input v-model="dataModel.mockup_link" label="mockup link" dense filled square />
              </div>

              <div class="col-12 col-sm-6 pv ph mt-2" >
                <q-input v-model="dataModel.flow_link" label="Flow chart link" dense filled square />
              </div>

              <div class="col-12 pv ph mt-1" >
                <span class="bold text-h5 text-dark pr-1">Description</span>
                <q-btn v-if="dataModel.id === null" size="sm" flat dense color="indigo" label="use template" class="capital bg-indigo-1 pv" icon="bolt" @click="useTemplate" />
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

      <div class="mb-5 pb-3">.</div>

  </div>
</template>

<script>
import Meta from './meta'
import { debounce } from 'quasar'

export default {
  name: 'Works',
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
        priority: ['REGULAR', 'LOW', 'HIGH', 'URGENT'],
        works: [],
        worksTmp: [],
        labels: [],
        labelsTmp: []
      },
      readMore: false
    }
  },

  created () {
    this.dataModel = this.$Helper.unReactive(this.Meta.model)
    this.initTopBar()
    this.searchWorksEvt = debounce(this.searchWorksEvt, 500)
  },

  mounted () {
    this.dataModel.project_id = this.$Helper.hasProjectId(this)

    this.dataModel.start_date = this.$Helper.getDateNow()

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
      this.getListSelect('projects/' + this.dataModel.project_id + '/work-labels?limit=0', 'labels')
      // this.getListSelect('projects/' + this.dataModel.project_id + '/works?limit=0', 'works')
    },

    initTopBar () {
      this.Meta.topBarMenu = [{ name: 'Refresh', event: this.onRefresh }]
    },

    getData (id) {
      console.log('getData')
      this.$Helper.loadingOverlay(true, 'Loading..')
      var endpoint = this.Meta.module + '/' + id + '/related-head'
      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          // inject data
          if (data.description === null) data.description = ''
          this.dataModel = data
          this.select.works = data.related_works
          this.select.worksTmp = data.related_works
          this.title = 'Edit'
        }
      })
    },

    backToRoot () {
      this.$router.push({ name: 'project-works', params: { project_id: this.dataModel.project_id } })
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

    useTemplate () {
      this.dataModel.description = '<div><h5><b>NamaModul<br></b></h5><div>&gt; keterangan singkat<b><br></b></div><div><b><br></b></div><div><b>moduleName</b> : namaModule</div><div><b>tableName</b> : nama_table</div></div><div><br></div><div>- id : PrimaryKey (Bigint)</div>'
    },

    searchWorksEvt (val) {
      console.log('select', val)
      if (val.length > 1) this.getListSelect('projects/' + this.dataModel.project_id + '/works?limit=0&search=name:' + val + '', 'works')
    }

  }
}
</script>
