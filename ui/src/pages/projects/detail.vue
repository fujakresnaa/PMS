
<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row mv-2">

        <div class="col-12">
          <q-card class="box-shadow mt-3">
            <q-card-section class="bg-primary text-white">
              <div class="text-h3 bold">{{dataModel.name}} ({{dataModel.code}})</div>
              <div class="text-subtitle2 pt">
                {{$Helper.toDate(dataModel.start_date, 'DD MMM YYYY - HH:mm')}} -
                {{$Helper.toDate(dataModel.end_date, 'DD MMM YYYY - HH:mm')}}
              </div>
            </q-card-section>
            <q-card-section class="">
              <q-btn label="Edit" @click="toPage('edit-projects')" unelevated color="primary" class="mv mh capital" icon="edit" />
              <q-btn label="User Documentations" @click="openDocs('ui')" unelevated color="blue" class="mv mh capital" icon="menu_book" />
              <q-btn label="API Documentations" @click="openDocs('api')" unelevated color="red" class="mv mh capital" icon="menu_book" />
            </q-card-section>
            <q-card-section class="">
              <q-btn label="Add Work" @click="toPage('add-works')" outline rounded color="purple" class="mv mh capital" icon="fact_check" />
              <q-btn label="Add Work Labes" @click="toPage('add-work-labels')" outline rounded color="orange-7" class="mv mh capital" icon="source" />
              <q-btn label="Add Task Satus" @click="toPage('add-task-status')" outline rounded color="blue-6" class="mv mh capital" icon="text_snippet" />
              <q-btn label="Add Task Labels" @click="toPage('add-task-labels')" outline rounded color="indigo" class="mv mh capital" icon="topic" />
            </q-card-section>
          </q-card >
        </div>

        <div class="col-12 mt-2">
          <q-table class="box-shadow th-lg "
            title="My Tasks"
            :data="tableTask.data"
            :columns="tableTask.columns"
            row-key="id"
            :pagination.sync="tableTask.pagination"
            :loading="tableTask.loading"
            :filter="tableTask.search"
            @request="getListTask"
            :rows-per-page-options="[8, 16, 20, 50, 75, 100]"
            binary-state-sort
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
                <q-icon size="2em" :name="icon" /><span class="bold text-h6"> There are no data Task yet for you</span>
              </div>
            </template>

            <template v-slot:top>
              <span class="text-h5 bold text-primary">My Task</span>

              <q-space />

            </template>

          </q-table>

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
      dataModel: this.$Helper.unReactive(Meta.model),
      rules: {
        permission: Meta.permission
      },
      tableTask: {
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
    this.initialize()
  },

  mounted () {

  },

  methods: {

    callbackForm (params = null) {
      this.onRefresh()
    },

    checkPermission (mode = 'create') {
      var access = this.$ModuleConfig.checkPermission(this.$router, this.Meta.module + '-' + mode)
      if (access) return true
      else this.$router.push({ name: '403' })
    },

    initialize () {
      var params = this.$route.params
      if (this.$Helper.checkParams(params)) { // checking access update
        if (this.checkPermission('read')) {
          if (params.id !== undefined) this.getData(params.id)
          else this.backToRoot()
          this.refreshListTask()
        }
      } else this.backToRoot()
    },

    onRefresh () {
      this.initialize()
    },

    initTopBar () {
      this.Meta.topBarMenu = [{ name: 'Refresh', event: this.onRefresh }]
    },

    getData (id) {
      console.log('getData')
      this.$Helper.loadingOverlay()
      var endpoint = this.Meta.module + '/' + id
      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          // inject data
          this.dataModel = data
          this.dataModel.project_id = data.id
          this.$Helper.saveLdb('project', data)
        }
      })
    },

    edit () {
      this.$router.push({ name: 'edit-' + this.Meta.module, params: this.dataModel })
    },

    viewTask (data) {
      this.$router.push({ name: 'view-tasks', params: data })
    },

    backToRoot () {
      this.$router.push({ name: this.Meta.module })
    },

    toPage (page) {
      this.$router.push({ name: page, params: { project_id: this.dataModel.id } })
    },

    openDocs (type = 'ui') {
      if (type === 'ui') type = 'docs'
      if (type === 'api') type = 'docs-api'
      var url = this.$Config.getApiRoot() + 'projects/' + this.dataModel.id + '/' + type
      this.$Helper.openLink(url)
    },

    getTableSelectedTask () {
      return this.tableTask.selected.length === 0 ? '' : `${this.tableTask.selected.length} record${this.tableTask.selected.length > 1 ? 's' : ''} selected of ${this.tableTask.data.length}`
    },

    refreshListTask () {
      this.getListTask({
        pagination: this.tableTask.pagination,
        search: this.tableTask.search
      })
    },

    getListTask (props) {
      this.$Helper.loading()
      this.tableTask.loading = true
      this.tableTask.selected = []
      const { page, rowsPerPage } = props.pagination
      const perpage = rowsPerPage === 0 ? this.tableTask.pagination.rowsNumber : rowsPerPage

      var projectId = this.$Helper.hasProjectId(this)
      var user = this.$Config.credentials()
      var endpoint = 'my-task/' + user.id + '?table'
      // var endpoint = 'my-task/14?table'

      endpoint = endpoint + '&project_id=' + projectId
      endpoint = endpoint + '&page=' + page
      endpoint = endpoint + '&limit=' + perpage
      if (this.tableTask.search !== '') endpoint = endpoint + '&search=' + this.dataModel.searchBySelected.field + ':' + this.tableTask.search
      if (this.dataModel.status === 'TRASH') endpoint = endpoint + '&trash=true'

      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loading(false)
        this.tableTask.loading = false
        if (status === 200) {
          // inject data
          this.tableTask.data.splice(0, this.tableTask.data.length, ...data.data)
          // updating table
          this.tableTask.pagination.rowsNumber = data.total
          this.tableTask.pagination.page = page
          this.tableTask.pagination.rowsPerPage = perpage
        }
      })
    }

  }
}
</script>
