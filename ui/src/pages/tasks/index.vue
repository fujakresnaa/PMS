<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row pl-2 pt-2">
        <div class="col-12 col-sm-3 col-md-5 pb-1 pv info-page">
          <div class="title">
            <span class="text-caption text-grey-8">Master {{Meta.name}}</span><br>
            <span class="text-h5 bold text-primary capital">{{Meta.name}}</span>
          </div>
        </div>

        <div class="col-6 col-sm-3 col-md-2 pb-1 pr-1 ">
          <q-select :options="select.status" dense outlined  @input="val => { onRefresh() }"
            v-model="dataModel.status" label="Status"
            class="bg-white box-shadow" style="border-radius:5px; " transition-show="jump-up" transition-hide="jump-down"
            >
          </q-select>
        </div>

        <div class="col-6 col-sm-3 col-md-2 pb-1 pr-1-5">
          <q-select :options="table.searchBy" dense outlined
            v-model="dataModel.searchBySelected" label="Search By" class="bg-white box-shadow"
            style="border-radius:5px; " transition-show="jump-up" transition-hide="jump-down" />
        </div>

        <div class="col-12 col-sm-3 col-md-3 pb-1 pr-1-5">
          <q-input debounce="300" placeholder="Search..." v-model="table.search" outlined dense class="fix-after bg-white box-shadow " style="border-radius:5px; " >
              <template v-slot:append>
                <q-icon v-if="table.search !== ''" name="close" @click="table.search = ''" class="cursor-pointer" />
                <q-icon name="search" />
              </template>
            </q-input>
        </div>

      </div>

      <div class="q-pa-md">
        <q-table class="box-shadow th-lg "
          title="Treats"
          :data="table.data"
          :columns="table.columns"
          row-key="id"
          :selected-rows-label="getTableSelected"
          selection="multiple"
          :pagination.sync="table.pagination"
          :loading="table.loading"
          :filter="table.search"
          @request="getList"
          :rows-per-page-options="[8, 16, 20, 50, 75, 100]"
          :selected.sync="table.selected"
          binary-state-sort
        >

          <template v-slot:body-cell-action="props">
            <q-td :props="props">
              <q-btn v-if="rules.permission.update" class="bg-grey-2" dense round flat color="green" @click="edit(props.row)" icon="edit"></q-btn>
              <q-btn class="bg-grey-2" dense round flat color="primary" @click="detail(props.row)" icon="visibility"></q-btn>
            </q-td>
          </template>

          <template v-slot:body-cell-name="props">
            <q-td :props="props">
              <div class="bold">{{props.row.name}}</div>
            </q-td>
          </template>

          <template v-slot:body-cell-relations="props">
            <q-td :props="props">
              <q-chip clickable @click="linkTo('view-works', props.row.works)" class="link" size="sm" v-if="props.row.works">
                <q-avatar icon="fact_check" color="orange" text-color="white" />
                {{props.row.works.name}}
                <q-tooltip>Works : {{props.row.works.name}}</q-tooltip>
              </q-chip> <br>

              <q-chip clickable @click="linkTo('view-sprints', props.row.sprints)" class="link" size="sm" v-if="props.row.sprints">
                <q-avatar icon="bolt" color="blue" text-color="white" />
                {{props.row.sprints.name}}
                <q-tooltip>Sprint : {{props.row.sprints.name}}</q-tooltip>
              </q-chip>

            </q-td>
          </template>

          <template v-slot:body-cell-status="props">
            <q-td :props="props">
              <q-chip v-if="props.row.task_status">
                <q-avatar icon="stars" color="purple" text-color="white" />
                {{props.row.task_status.name}}
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

          <template v-slot:body-cell-assignees="props">
            <q-td :props="props">
              <q-chip size="sm" v-for="(user, i) in props.row.assignees" :key="i+'idx'">
                <q-avatar :color="$Helper.randomColor()" text-color="white" >
                  <span class="uppercase">{{$Helper.getFirstChar(user.name)}}</span>
                </q-avatar>
                {{user.name}}
              </q-chip>
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

          <template v-slot:body-cell-logInfo="props">
            <q-td :props="props"> <log-info :data="props.row" /></q-td>
          </template>

          <template v-slot:body-cell-labels="props">
            <q-td :props="props">
              <b v-for="(label, i) in props.row.task_labels" :key="i+'idx'"
              :style="'font-size:13px; margin-bottom:3px; color:' + label.color + ''">{{label.name}}, </b>
            </q-td>
          </template>

          <template v-slot:no-data="{icon}">
            <div class="full-width row flex-center text-primary q-gutter-sm">
              <q-icon size="2em" :name="icon" /><span class="bold text-h6"> There are no data {{Meta.name}} yet</span>
              <q-btn v-if="rules.permission.create" @click="add" unelevated outline color="primary" label="Add New" />
            </div>
          </template>

          <template v-slot:top>
            <div v-if="rules.permission.create" class="action animated zoomIn" >
              <q-btn @click="add" unelevated color="primary" class="capital  mr-1" icon="add" label="Add" />
            </div>

            <div v-if="rules.permission.delete && table.selected.length !== 0 ">
              <q-btn @click="deleteSelected" unelevated class="capital "
                :label="(dataModel.status === 'TRASH') ? 'Re-Activate' : 'Delete' "
                :color="(dataModel.status === 'TRASH') ? 'green' : 'negative' "
                :icon="(dataModel.status === 'TRASH') ? 'check' : 'delete' "
              />

            </div>

            <q-space />

          </template>

        </q-table>
      </div>

  </div>
</template>

<script>
import Meta from './meta'

export default {
  name: 'Tasks',
  data () {
    return {
      Meta,
      API: this.$Api,
      // default data
      dataModel: {
        searchBy: null,
        status: 'ACTIVE'
      },
      rules: {
        permission: Meta.permission
      },
      table: {
        search: '',
        data: [],
        searchBy: [],
        searchBySelected: null,
        columns: [
          { name: 'action', label: '#', align: 'left', style: 'width: 20px' },
          { name: 'name', label: 'name', field: 'name', align: 'left' },
          { name: 'assignees', label: 'assignees', align: 'left', style: 'width: 10%;' },
          { name: 'relations', label: 'Relations', align: 'left' },
          { name: 'status', label: 'status', field: 'status', align: 'left' },
          { name: 'start_date', label: 'Start', field: 'start_date', align: 'center' },
          { name: 'end_date', label: 'End', field: 'end_date', align: 'center' },
          { name: 'is_overtime', label: 'Overtime', field: 'is_overtime', align: 'left' },
          { name: 'mandays', label: 'mandays', field: 'mandays', align: 'left' },
          { name: 'logInfo', label: 'Log', align: 'left' }

        ],
        pagination: {
          page: 1,
          rowsPerPage: 8,
          rowsNumber: 0
        },
        selected: [],
        inFocus: false
      },
      select: {
        status: ['ACTIVE', 'TRASH']

      }
    }
  },

  created () {
    this.initTopBar()
  },

  mounted () {
    // generate filter search
    for (const col of this.table.columns) {
      if (col.name !== 'action') {
        if (col.name !== 'logInfo') this.table.searchBy.push(col)
      }
    }
    this.dataModel.searchBySelected = this.table.searchBy[0]
    this.onRefresh()
  },

  methods: {

    onRefresh () {
      this.refreshList()
    },

    initTopBar () {
      this.Meta.topBarMenu = [{ name: 'Refresh', event: this.onRefresh }]
    },

    getTableSelected () {
      return this.table.selected.length === 0 ? '' : `${this.table.selected.length} record${this.table.selected.length > 1 ? 's' : ''} selected of ${this.table.data.length}`
    },

    refreshList () {
      this.getList({
        pagination: this.table.pagination,
        search: this.table.search
      })
    },

    getList (props) {
      this.$Helper.loading()
      this.table.loading = true
      this.table.selected = []
      const { page, rowsPerPage } = props.pagination
      const perpage = rowsPerPage === 0 ? this.table.pagination.rowsNumber : rowsPerPage

      var projectId = this.$Helper.hasProjectId(this)
      var endpoint = 'projects/' + projectId + '/tasks?table'

      endpoint = endpoint + '&page=' + page
      endpoint = endpoint + '&limit=' + perpage
      if (this.table.search !== '') endpoint = endpoint + '&search=' + this.dataModel.searchBySelected.field + ':' + this.table.search
      if (this.dataModel.status === 'TRASH') endpoint = endpoint + '&trash=true'

      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loading(false)
        this.table.loading = false
        if (status === 200) {
          // inject data
          this.table.data.splice(0, this.table.data.length, ...data.data)
          // updating table
          this.table.pagination.rowsNumber = data.total
          this.table.pagination.page = page
          this.table.pagination.rowsPerPage = perpage
        }
      })
    },

    add () {
      var projectId = this.$Helper.hasProjectId(this)
      var data = { project_id: projectId }
      this.$router.push({ name: 'add-' + this.Meta.module, params: data })
    },

    edit (data) {
      this.$router.push({ name: 'edit-' + this.Meta.module, params: data })
    },

    detail (data) {
      this.$router.push({ name: 'view-' + this.Meta.module, params: data })
    },

    deleteSelected () {
      var that = this
      var totalData = this.table.selected.length
      var type = 'Delete'
      if (this.dataModel.status === 'TRASH') type = 'Restore'
      this.$q.dialog({
        title: type + ' ' + totalData + ' data selected',
        message: 'Are you sure want to ' + type + ' data ' + this.Meta.name + ' ?',
        persistent: true,
        ok: type,
        cancel: 'Cancel'
      }).onOk(() => {
        that.deleteDataSelected(type)
      }).onCancel(() => {
        // action
      }).onDismiss(() => {
        // action
      })
    },

    deleteDataSelected (type) {
      for (var row of this.table.selected) {
        if (type === 'Delete') this.delete(row.id)
        else this.restore(row.id)
      }
      this.table.selected = []
      setTimeout(() => { this.onRefresh() }, 500)
    },

    delete (id) {
      this.$Helper.loading()
      this.API.delete(this.Meta.module + '/' + id, (status, data, message, response, full) => {
        this.$Helper.loading(false)
        if (status === 200) this.$Helper.showToast(message)
      })
    },

    restore (id) {
      this.$Helper.loading()
      this.API.put(this.Meta.module + '/' + id + '/restore', (status, data, message, response, full) => {
        this.$Helper.loading(false)
        if (status === 200) this.$Helper.showToast(message)
      })
    },

    linkTo (page, data) {
      console.log(page)
      this.$router.push({ name: page, params: data })
    }

  }
}
</script>
