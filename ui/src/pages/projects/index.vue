<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta" :disableDrawer="true" v-bind:topBarMenu="Meta.topBarMenu"  />

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
      <div v-if="rules.permission.create" class="action animated zoomIn" ></div>
      <div class="q-pa-md">
        <q-btn @click="add" unelevated color="primary" class="capital  mr-1 ml-1" icon="add" label="Add" />
        <q-table class=" th-lg "
          grid
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
          <template v-slot:item="props">
            <div class="ph pv col-12 col-sm-3 grid-style-transition animated fadeIn" >
              <q-card class="box-shadow" bordered>

                <!-- Head -->
                <q-item class="bg-grad text-light">
                  <q-item-section>
                    <q-item-label class="text-h5 bold">{{props.row.code}}</q-item-label>
                    <q-item-label class="text-light" caption>{{props.row.name}}</q-item-label>
                  </q-item-section>
                </q-item>

                <q-separator />

                <q-item>
                  <q-item-section>
                    <q-item-label caption>Start</q-item-label>
                    <q-item-label>{{$Helper.toDate(props.row.start_date, 'DD MMM YYYY - HH:mm')}}</q-item-label>
                  </q-item-section>
                </q-item>

                <q-separator />

                <q-item>
                  <q-item-section>
                    <q-item-label caption>End</q-item-label>
                    <q-item-label>{{$Helper.toDate(props.row.end_date, 'DD MMM YYYY - HH:mm')}}</q-item-label>
                  </q-item-section>
                </q-item>

                <q-separator />

                <q-card-section>
                  <div>
                  <small class="text-grey-5">Members :</small> <br>
                    <q-chip size="sm" v-for="(user, i) in props.row.members" :key="i+'idx'">
                      <q-avatar :color="$Helper.randomColor()" text-color="white" >
                        <span class="uppercase">{{$Helper.getFirstChar(user.name)}}</span>
                      </q-avatar>
                      {{user.name}}
                    </q-chip>
                  </div>
                </q-card-section>

                <q-card-section class="row">
                  <div class="col-6 pv">
                    <q-btn size="md" class="full-width" color="orange" unelevated @click="edit(props.row)" icon="edit">&nbsp; Edit</q-btn>
                  </div>
                  <div class="col-6 pv">
                    <q-btn size="md" class="full-width" color="primary" unelevated @click="detail(props.row)" icon="visibility">&nbsp; View</q-btn>
                  </div>
                </q-card-section>

              </q-card>
            </div>
          </template>
        </q-table>
      </div>

      <div class="q-pa-md" v-if="false">
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
              <q-btn v-if="rules.permission.update" class="bg-grey-2" dense round flat color="orange" @click="edit(props.row)" icon="edit"></q-btn>
              <q-btn class="bg-grey-2" dense round flat color="primary" @click="detail(props.row)" icon="visibility"></q-btn>
            </q-td>
          </template>

          <template v-slot:body-cell-members="props">
            <q-td :props="props">
              <q-chip size="sm" v-for="(user, i) in props.row.members" :key="i+'idx'">
                <q-avatar :color="$Helper.randomColor()" text-color="white" >
                  <span class="uppercase">{{$Helper.getFirstChar(user.name)}}</span>
                </q-avatar>
                {{user.name}}
              </q-chip>
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
  name: 'Projects',
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
          { name: 'code', label: 'code', field: 'code', align: 'left' },
          { name: 'name', label: 'name', field: 'name', align: 'left' },
          { name: 'members', label: 'members', align: 'left' },
          { name: 'start_date', label: 'Start', field: 'start_date', align: 'left', format: (val) => this.$Helper.toDate(val, 'DD MMM YYYY - HH:mm') },
          { name: 'end_date', label: 'End', field: 'end_date', align: 'left', format: (val) => this.$Helper.toDate(val, 'DD MMM YYYY - HH:mm') }
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

      },
      fetchAll: false
    }
  },

  created () {
    this.initTopBar()
    var params = this.$route.params
    if (this.$Helper.checkParams(params)) {
      if (params.all !== undefined && params.all === 'all') this.fetchAll = true
    }
    this.onRefresh()
  },

  mounted () {
    // generate filter search
    // for (const col of this.table.columns) {
    //   if (col.name !== 'action') this.table.searchBy.push(col)
    // }
    this.table.searchBy.push({ name: 'code', label: 'code', field: 'code' })
    this.table.searchBy.push({ name: 'name', label: 'name', field: 'name' })
    this.dataModel.searchBySelected = this.table.searchBy[0]
    // this.dataModel.searchBySelected = { name: 'code', label: 'code', field: 'code' }
  },

  methods: {

    checkPermission () {
      for (const perm in this.rules.permission) {
        this.rules.permission[perm] = this.$ModuleConfig.checkPermission(this.$router, this.Meta.module + '-' + perm)
      }
    },

    onRefresh () {
      this.refreshList()
    },

    initialize () {
      this.onRefresh()
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

      var endpoint = this.Meta.module + '?table'
      endpoint = endpoint + '&page=' + page
      endpoint = endpoint + '&limit=' + perpage
      if (this.table.search !== '') endpoint = endpoint + '&search=' + this.dataModel.searchBySelected.field + ':' + this.table.search
      if (this.dataModel.status === 'TRASH') endpoint = endpoint + '&trash=true'

      var cre = this.$Config.credentials()
      if (cre === undefined) {
        this.$Helper.showAlert('Opps!', "There's problem found, Please reload this page to fix that.")
      }
      if (!this.fetchAll) endpoint = endpoint + '&member=' + cre.id

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
      this.$router.push({ name: 'add-' + this.Meta.module })
    },

    edit (data) {
      this.$router.push({ name: 'edit-' + this.Meta.module, params: data })
    },

    detail (data) {
      data.project_id = data.id
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
    }

  }
}
</script>
