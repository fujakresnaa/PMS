
<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row mv-2 mt-2">

        <div class="col-12 mt-2 animated fadeIn" >
          <q-tabs v-model="panel"
            inline-label narrow-indicator align="left"
            class="q-mb-lg"
          >
            <q-tab class="text-dark" name="work" icon="fact_check" label="Work" />
            <q-tab class="text-dark" name="task" icon="app_registration" label="Task" />
            <q-tab class="text-dark" name="component" icon="view_in_ar" label="Components" />
          </q-tabs>
        </div>

        <div v-if="panel === 'task'" class="col-12 animated fadeIn">
          <q-table class="box-shadow th-lg "
            title="Tasks on Work"
            :data="tableTask.data"
            :columns="tableTask.columns"
            row-key="id"
            :loading="tableTask.loading"
            :rows-per-page-options="[8, 16, 20, 50, 75, 100]"
          >

            <template v-slot:body-cell-action="props">
              <q-td :props="props">
                <q-btn class="bg-grey-2" dense round flat color="primary" @click="viewTask(props.row)" icon="visibility"></q-btn>
              </q-td>
            </template>

            <template v-slot:body-cell-name="props">
              <q-td :props="props">
                <div class="bold">{{props.row.name}}</div>
                <q-badge v-if="props.row.is_overtime" label="Overtime" color="red"/>
              </q-td>
            </template>

            <template v-slot:body-cell-status="props">
              <q-td :props="props">
                <q-chip v-if="props.row.status_name">
                  <q-avatar icon="stars" color="purple" text-color="white" />
                  {{props.row.status_name}}
                </q-chip>
              </q-td>
            </template>

            <template v-slot:body-cell-is_overtime="props">
              <q-td :props="props">
                <q-chip v-if="props.row.is_overtime">
                  <q-avatar icon="update" color="red" text-color="white" />
                  Overtime
                </q-chip>
              </q-td>
            </template>

            <template v-slot:body-cell-mandays="props">
              <q-td :props="props">
                <span v-if="props.row.mandays > 0" class="bold text-primary">
                  {{props.row.mandays}}
                  <span class="bold text-purple" v-if="props.row.mandays_actual > 0" >({{props.row.mandays_actual}})</span>
                </span>
              </q-td>
            </template>

            <template v-slot:body-cell-start_date="props">
              <q-td :props="props">
                <div>{{$Helper.toDate(props.row.start_date, 'DD MMM YYYY - HH:mm')}}</div>
                <div v-if="props.row.actual_start_date" class="text-primary" >{{$Helper.toDate(props.row.start_date, 'DD MMM YYYY - HH:mm')}}</div>
              </q-td>
            </template>

            <template v-slot:body-cell-end_date="props">
              <q-td :props="props">
                <div>{{$Helper.toDate(props.row.end_date, 'DD MMM YYYY - HH:mm')}}</div>
                <div v-if="props.row.actual_end_date" class="text-primary" >{{$Helper.toDate(props.row.end_date, 'DD MMM YYYY - HH:mm')}}</div>
              </q-td>
            </template>

            <template v-slot:body-cell-labels="props">
              <q-td :props="props">
                <b v-for="(label, i) in props.row.task_labels" :key="i+'idx'"
                :style="'font-size:13px; margin-bottom:3px; color:' + label.color + ''">{{label.name}}, </b>
              </q-td>
            </template>

            <template v-slot:no-data="{icon}">
              <div class="full-width row flex-center text-primary q-gutter-sm">
                <q-icon size="2em" :name="icon" /><span class="bold text-h6"> There are no data Task on this work</span>
              </div>
            </template>

            <template v-slot:top>
              <span class="text-h5 bold text-primary">Tasks on Work</span>

              <q-space />

            </template>

          </q-table>

        </div>

        <div v-if="panel === 'work' && dataModel.id !== null" class="col-12 animated fadeIn">
          <q-card class="box-shadow mt-2">
            <q-card-section>
              <div class="row ">
                <div class="col">
                  <span class="text-h4 bold pt-1 pb-1">
                    <q-icon name="verified" color="green" v-if="dataModel.is_done" />
                    {{dataModel.name}}
                  </span> <br>
                </div>

                <div class="col-auto">
                  <q-btn-dropdown size="sm" unelevated
                    label="Action"
                    color="primary"
                  >
                    <q-list><q-separator /></q-list>

                    <q-list bordered>
                      <q-item clickable v-close-popup @click="preview">
                        <q-item-section avatar>
                          <q-avatar icon="share" color="purple" text-color="white" size="sm"/>
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Share Preview</q-item-label>
                        </q-item-section>
                      </q-item>

                      <q-item clickable v-close-popup @click="edit">
                        <q-item-section avatar>
                          <q-avatar icon="edit" color="primary" text-color="white" size="sm"/>
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Edit</q-item-label>
                        </q-item-section>
                      </q-item>

                      <q-item clickable v-close-popup @click="updateStage('started')"
                        v-if="dataModel.actual_start_date === null">
                        <q-item-section avatar>
                          <q-avatar icon="play_arrow" color="indigo" text-color="white" size="sm"/>
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Start Work</q-item-label>
                        </q-item-section>
                      </q-item>

                      <q-item clickable v-close-popup @click="completed"
                        v-if="dataModel.actual_start_date !== null && dataModel.actual_end_date == null">
                        <q-item-section avatar>
                          <q-avatar icon="task_alt" color="green" text-color="white" size="sm"/>
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Set Completed</q-item-label>
                        </q-item-section>
                      </q-item>

                      <q-item clickable v-close-popup @click="updateStage('done')"
                        v-if="dataModel.actual_start_date !== null && dataModel.actual_end_date !== null">
                        <q-item-section avatar>
                          <q-avatar icon="done_all" color="green" text-color="white" size="sm"/>
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Set Done</q-item-label>
                          <q-item-label caption>Work will be hidden on the roadmaps</q-item-label>
                        </q-item-section>
                      </q-item>

                      <q-separator class="mh" />
                      <small class="text-grey-7 pl-2 mb" >Reports</small>

                      <q-item clickable v-close-popup @click="preview('propose')">
                        <q-item-section avatar>
                          <q-avatar icon="article" color="green-7" text-color="white" size="sm"/>
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Proposal</q-item-label>
                        </q-item-section>
                      </q-item>

                      <q-item clickable v-close-popup @click="preview('brd')">
                        <q-item-section avatar>
                          <q-avatar icon="auto_stories" color="blue-7" text-color="white" size="sm"/>
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>BRD</q-item-label>
                        </q-item-section>
                      </q-item>

                      <q-item clickable v-close-popup @click="preview('report')">
                        <q-item-section avatar>
                          <q-avatar icon="fact_check" color="orange-7" text-color="white" size="sm"/>
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Task of Work</q-item-label>
                        </q-item-section>
                      </q-item>

                    </q-list>

                  </q-btn-dropdown>
                </div>
              </div>
            </q-card-section>

            <q-card-section class="row">
              <div class="col-12">
                <div class="mb-2 quote"> {{dataModel.summary}} </div>
              </div>

              <div class="col-12 col-sm-4 mh-1 text-center">
                <div class="text-dark bold">Plan</div>
                <div class="text-grey-8" v-if="dataModel.start_date">
                  <q-icon name="play_arrow" color="grey-5" size="16px" />
                  {{$Helper.toDate(dataModel.start_date, 'DD MMM YYYY - HH:mm')}}
                </div>
                <div class="text-grey-8" v-if="dataModel.end_date">
                  <q-icon name="stop" color="grey-5" size="16px" />
                  {{$Helper.toDate(dataModel.end_date, 'DD MMM YYYY - HH:mm')}}
                </div>
              </div>

              <div class="col-12 col-sm-4 mh-1 text-center">
                <div class="text-dark bold">Actual</div>
                <div class="text-grey-8" >
                  <q-icon name="play_arrow" color="indigo" size="16px" />
                  {{$Helper.toDate(dataModel.actual_start_date, 'DD MMM YYYY - HH:mm')}}
                  <span v-if="dataModel.actual_start_date === null">Idle</span>
                </div>
                <div class="text-grey-8" >
                  <q-icon name="stop" color="red" size="16px" />
                  {{$Helper.toDate(dataModel.actual_end_date, 'DD MMM YYYY - HH:mm')}}
                  <span v-if="dataModel.actual_end_date === null">?</span>
                </div>
              </div>

              <div class="col-12 col-sm-4 mh-1 text-center">
                <div class="text-dark bold">Status</div>
                <q-chip v-if="dataModel.status">
                  <q-avatar :icon="$Config.iconStatusWork(dataModel.status)" :color="$Config.colorStatusWork(dataModel.status)" text-color="white" />
                  {{dataModel.status}}
                </q-chip>
              </div>
            </q-card-section>

            <q-card-section class="">
              <div class="pt">
                <q-btn size="xs" :label="dataModel.priority" unelevated outline rounded :color="$Config.colorPriority(dataModel.priority)" class="mv mh bold capital" :icon="$Config.iconPriority(dataModel.priority)" >
                  <q-tooltip>Priority: {{dataModel.priority}}</q-tooltip>
                </q-btn>

                <q-chip dense square v-for="(label, i) in dataModel.work_labels" :key="i+'idx'">
                  <q-avatar icon="bookmark" :style="'background:' + label.color" text-color="white" />
                  {{label.name}}
                </q-chip>
              </div>
              <q-separator class="mt-2" />
              <div class="mh-2" v-html="dataModel.description"></div>
            </q-card-section>
          </q-card >

          <q-card class="box-shadow mt-2 mb-2" v-if="dataModel.mockup_link">
            <q-card-section class="bg-indigo text-white">
              <div class="text-h6">Mockup
                <q-btn  @click="$Helper.openLink(dataModel.mockup_link)"
                size="sm" flat dense color="light" class="capital">
                <q-icon name="open_in_new" style="font-size: 12px !important;" /> &nbsp; Open in new tab</q-btn>
              </div>
            </q-card-section>
            <q-card-section class="">
              <div class="mh-2" >
                <iframe frameborder="0" style="width:100%;height:480px;" :src="dataModel.mockup_link"></iframe>
              </div>
            </q-card-section>
          </q-card >

          <q-card class="box-shadow mt-2 mb-2" v-if="dataModel.flow_link">
            <q-card-section class="bg-orange-8 text-white">
              <div class="text-h6">Flow Chart
                <q-btn  @click="$Helper.openLink(dataModel.flow_link)"
                size="sm" flat dense color="light" class="capital">
                <q-icon name="open_in_new" style="font-size: 12px !important;" /> &nbsp; Open in new tab</q-btn>
              </div>
            </q-card-section>
            <q-card-section class="">
              <div class="mh-2" >
                <iframe frameborder="0" style="width:100%;height:480px;" :src="dataModel.flow_link"></iframe>
              </div>
            </q-card-section>
          </q-card >

        </div>

        <div v-if="panel === 'component' && dataModel.id !== null" class="col-12 animated fadeIn">
          <div class="row pv-2 ph-2">

            <div class="col-12 col-sm-12 col-md-4">
              <span class="text-h5 bold text-primary">Work Components</span> <br>
              <span class="text-grey-6">All Component of this works</span>
            </div>

            <q-table class="no-shadow th-lg col-12"
              grid
              :data="tableComponent.data"
              :columns="tableComponent.columns"
              row-key="id"
              :loading="tableComponent.loading"
              :filter="tableComponent.search"
              :rows-per-page-options="[8, 16, 32, 50]"
            >
              <template v-slot:item="props">
                <div class="ph pv col-12 col-sm-6 grid-style-transition animated fadeIn" >
                  <q-card class="no-shadow" bordered>

                    <!-- Head -->
                    <q-item>
                      <q-item-section avatar>
                        <q-avatar text-color="white"
                          :icon="$Config.iconComponent(props.row.type)"
                          :color="$Config.colorComponent(props.row.type)"
                        />
                      </q-item-section>
                      <q-item-section>
                        <q-item-label>{{props.row.name}}</q-item-label>
                        <q-item-label caption>{{props.row.type}}</q-item-label>
                      </q-item-section>
                    </q-item>

                    <q-separator />

                    <!-- Summary Desc -->
                    <q-card-section>
                      <div class="quote text-subtitle2">{{props.row.summary}}</div>
                      <div class="mt-2 pt-2 right">
                        <q-btn @click="gotToLink('form', props.row)" size="sm" icon="edit" outline unelevated color="primary" label="Edit" class="capital mr"/>
                        <q-btn @click="gotToLink('view', props.row)" size="sm" icon="find_in_page" unelevated color="primary" label="Detail" class="capital "/>
                      </div>
                    </q-card-section>

                  </q-card>
                </div>
              </template>
            </q-table>
          </div>

        </div>

        <div v-if="dataModel.id === null" class="col-12 animated fadeIn">
          <q-card class="box-shadow mt-2">
            <q-card-section>
              <div class="row ">
                <div class="col">
                  <q-skeleton width="90%" height="45px" />
                </div>

                <div class="col-auto">
                  <q-skeleton width="100px" height="30px" />
                </div>
              </div>
            </q-card-section>

            <q-card-section class="row">
              <div class="col-12">
                <q-skeleton width="100%" height="70px" />
              </div>

              <div class="col-12 col-sm-4 mh-1 text-center">
                <div class="text-dark bold">Plan</div>
                <q-skeleton width="90%" height="30px" />
              </div>

              <div class="col-12 col-sm-4 mh-1 text-center">
                <div class="text-dark bold">Actual</div>
                <q-skeleton width="90%" height="30px" />
              </div>

              <div class="col-12 col-sm-4 mh-1 text-center">
                <div class="text-dark bold">Status</div>
                <q-skeleton width="100%" height="30px" />
              </div>
            </q-card-section>

            <q-card-section class="">
              <div class="pt">
                <q-skeleton width="80px" height="30px" />
              </div>
              <q-separator class="mt-2 mb-2" />
              <q-skeleton width="100%" height="130px" />
            </q-card-section>
          </q-card >

          <q-card class="box-shadow mt-2 mb-2" v-if="dataModel.mockup_link">
            <q-card-section class="bg-indigo text-white">
              <div class="text-h6">Mockup
                <q-btn  @click="$Helper.openLink(dataModel.mockup_link)"
                size="sm" flat dense color="light" class="capital">
                <q-icon name="open_in_new" style="font-size: 12px !important;" /> &nbsp; Open in new tab</q-btn>
              </div>
            </q-card-section>
            <q-card-section class="">
              <div class="mh-2" >
                <iframe frameborder="0" style="width:100%;height:480px;" :src="dataModel.mockup_link"></iframe>
              </div>
            </q-card-section>
          </q-card >

          <q-card class="box-shadow mt-2 mb-2" v-if="dataModel.flow_link">
            <q-card-section class="bg-orange-8 text-white">
              <div class="text-h6">Flow Chart
                <q-btn  @click="$Helper.openLink(dataModel.flow_link)"
                size="sm" flat dense color="light" class="capital">
                <q-icon name="open_in_new" style="font-size: 12px !important;" /> &nbsp; Open in new tab</q-btn>
              </div>
            </q-card-section>
            <q-card-section class="">
              <div class="mh-2" >
                <iframe frameborder="0" style="width:100%;height:480px;" :src="dataModel.flow_link"></iframe>
              </div>
            </q-card-section>
          </q-card >

        </div>

      </div>

      <div class="mb-5"><small>.</small></div>

  </div>
</template>

<script>
import Meta from './meta'

export default {
  name: 'Works',
  data () {
    return {
      Meta,
      API: this.$Api,
      // default data
      dataModel: this.$Helper.unReactive(Meta.model),
      rules: {
        permission: Meta.permission
      },
      panel: 'work',
      tableTask: {
        loading: false,
        search: '',
        data: [],
        searchBy: [],
        searchBySelected: null,
        columns: [
          { name: 'action', label: '#', align: 'left', style: 'width: 20px' },
          { name: 'name', label: 'name', field: 'name', align: 'left' },
          { name: 'status', label: 'status', field: 'status', align: 'left' },
          { name: 'start_date', label: 'Start', field: 'start_date', align: 'center' },
          { name: 'end_date', label: 'End', field: 'end_date', align: 'center' }
        ],
        pagination: {
          page: 1,
          rowsPerPage: 100,
          rowsNumber: 0
        },
        selected: [],
        inFocus: false
      },
      showFilter: false,
      tableComponent: {
        search: '',
        data: [],
        filterType: ['DOC', 'DOC-API', 'SQL', 'RULE', 'NOTE', 'OTHER'],
        filterByType: null,
        searchByList: ['name', 'summary', 'description'],
        searchBy: 'name',
        columns: [
          { name: 'action', label: '#', align: 'left', style: 'width: 20px' },
          { name: 'name', label: 'name', field: 'name', align: 'left' }
        ],
        pagination: {
          page: 1,
          rowsPerPage: 8,
          rowsNumber: 0
        },
        selected: [],
        inFocus: false
      }
    }
  },

  created () {
    this.initTopBar()
  },

  mounted () {
    this.onRefresh()
  },

  methods: {

    callbackForm (params = null) {
      this.onRefresh()
    },

    onRefresh () {
      var params = this.$route.params
      if (this.$Helper.checkParams(params)) { // checking access update
        if (params.id !== undefined) {
          this.getData(params.id)
        } else this.backToRoot()
      }
    },

    initTopBar () {
      this.Meta.topBarMenu = [{ name: 'Refresh', event: this.onRefresh }]
    },

    getData (id) {
      console.log('getData')
      this.$Helper.loading()
      var endpoint = this.Meta.module + '/' + id
      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loading(false)
        if (status === 200) {
          // inject data
          this.dataModel = data
          this.getListTask()
          this.loadComponent()
        }
      })
    },

    edit () {
      this.$router.push({ name: 'edit-' + this.Meta.module, params: this.dataModel })
    },

    backToRoot () {
      this.$router.push({ name: this.Meta.module })
    },

    messageSubmit (titleAdd = '', msg) {
      this.$Helper.showAlert(titleAdd + ' Succesfully', msg)
    },

    completed () {
      var that = this
      this.$q.dialog({
        title: 'Set Completed Work',
        message: 'Are you sure want to set Completed this work ?',
        persistent: true,
        ok: 'Set Complete',
        cancel: 'Cancel'
      }).onOk(() => {
        that.updateStage('completed')
      }).onCancel(() => {
        // action
      }).onDismiss(() => {
        // action
      })
    },

    updateStage (type = 'completed') {
      this.$Helper.loadingOverlay(true, 'updating..')
      this.API.put('works' + '/' + this.dataModel.id + '/' + type, this.dataModel, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          var msg = 'Work "' + this.dataModel.name + '" has ' + type
          this.messageSubmit('Work ' + type, msg)
          this.getData(this.dataModel.id)
        }
      })
    },

    getListTask () {
      this.$Helper.loading()
      this.tableTask.loading = true
      this.tableTask.selected = []

      // var projectId = this.$Helper.hasProjectId(this)
      // var user = this.$Config.credentials()
      var endpoint = 'works/' + this.dataModel.id + '/tasks?table&limit=0'

      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loading(false)
        this.tableTask.loading = false
        console.log(data)
        if (status === 200) {
          // inject data
          this.tableTask.data.splice(0, this.tableTask.data.length, ...data.data)
        }
      })
    },

    viewTask (data) {
      this.$router.push({ name: 'view-tasks', params: data })
    },

    preview (type = 'preview') {
      var url = this.$Config.getApiRoot() + 'preview/works/' + this.dataModel.id
      if (type === 'brd') url = this.$Config.getApiRoot() + 'report/brd/' + this.dataModel.id
      if (type === 'propose') url = this.$Config.getApiRoot() + 'report/propose/' + this.dataModel.id
      if (type === 'report') url = this.$Config.getApiRoot() + 'report/works/' + this.dataModel.id
      this.$Helper.openLink(url)
    },

    loadComponent () {
      this.getListComponent({
        pagination: this.tableComponent.pagination,
        search: this.tableComponent.search
      })
    },

    getListComponent (props) {
      this.$Helper.loading()
      this.tableComponent.loading = true
      this.tableComponent.selected = []
      const { page, rowsPerPage } = props.pagination
      const perpage = rowsPerPage === 0 ? this.tableComponent.pagination.rowsNumber : rowsPerPage

      var endpoint = 'projects/' + this.dataModel.id + '/work-components?table'

      endpoint = endpoint + '&page=' + page
      endpoint = endpoint + '&limit=' + perpage
      if (this.tableComponent.filterByType) {
        var search = 'type:!' + this.tableComponent.filterByType
        if (this.tableComponent.search !== '') search = search + '|' + this.tableComponent.searchBy + ':' + this.tableComponent.search
        endpoint = endpoint + '&search=' + search
      } else if (this.tableComponent.search !== '') endpoint = endpoint + '&search=' + this.tableComponent.searchBy + ':' + this.tableComponent.search

      if (this.dataModel.status === 'TRASH') endpoint = endpoint + '&trash=true'

      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loading(false)
        this.tableComponent.loading = false
        if (status === 200) {
          // inject data
          this.tableComponent.data.splice(0, this.tableComponent.data.length, ...data.data)
          // updating table
          this.tableComponent.pagination.rowsNumber = data.total
          this.tableComponent.pagination.page = page
          this.tableComponent.pagination.rowsPerPage = perpage
        }
      })
    },

    gotToLink (path, data) {
      data.project_id = this.dataModel.project_id
      // this.$router.push({ name: path, params: data })
      if (path === 'form') this.$Helper.openLink(`projects/${data.project_id}/task-components/task/${data.task_id}/form/${data.id}`)
      else this.$Helper.openLink(`projects/${data.project_id}/task-components/view/${data.id}`)
    }
  }
}
</script>
