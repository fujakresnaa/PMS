
<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row pv-2 ph-2">

        <div class="col-12 col-sm-12 col-md-4">
          <span class="text-h5 bold text-primary">Project Components</span> <br>
          <span class="text-grey-6">All Component Task of works</span>
        </div>

        <div class="col-6 col-sm-6 col-md-2 pb-1 pr-1-5">
          <q-select :options="tableComponent.filterType" dense outlined clearable @input="onRefresh"
            v-model="tableComponent.filterByType" label="Filter By" class="bg-white box-shadow"
            style="border-radius:5px; " transition-show="jump-up" transition-hide="jump-down" />
        </div>

        <div class="col-6 col-sm-6 col-md-2 pb-1 pr-1-5">
          <q-select :options="tableComponent.searchByList" dense outlined
            v-model="tableComponent.searchBy" label="Searc By" class="bg-white box-shadow"
            style="border-radius:5px; " transition-show="jump-up" transition-hide="jump-down" />
        </div>

        <div class="col-12 col-sm-12 col-md-4 right">
          <q-input debounce="300" placeholder="Search..." v-model="tableComponent.search" outlined dense class="fix-after bg-white box-shadow " style="border-radius:5px; " >
            <template v-slot:append>
              <q-icon v-if="tableComponent.search !== ''" name="close" @click="tableComponent.search = ''" class="cursor-pointer" />
              <q-icon name="search" />
            </template>
          </q-input>
        </div>

        <q-table class="no-shadow th-lg col-12"
          grid
          :data="tableComponent.data"
          :columns="tableComponent.columns"
          row-key="id"
          :pagination.sync="tableComponent.pagination"
          :loading="tableComponent.loading"
          :filter="tableComponent.search"
          @request="getListComponent"
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

                <!-- Work -->
                <q-item v-if="props.row.tasks.works">
                  <q-item-section>
                    <q-item-label caption>From Work</q-item-label>
                    <q-item-label @click="openWork(props.row.tasks.works)" class="bold text-primary link">{{props.row.tasks.works.name}}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-separator v-if="props.row.tasks.works" />

                <!-- tasks -->
                <q-item v-if="props.row.tasks && props.row.tasks.project_id === dataModel.project_id ">
                  <q-item-section>
                    <q-item-label caption>From Task</q-item-label>
                    <q-item-label @click="openTask(props.row.tasks)" class="bold text-primary link">{{props.row.tasks.name}}</q-item-label>
                  </q-item-section>
                </q-item>
                <q-separator v-if="props.row.tasks && props.row.tasks.project_id === dataModel.project_id " />

                <!-- Summary Desc -->
                <q-card-section>
                  <div class="quote text-subtitle2">{{props.row.summary}}</div>
                  <div class="mt-2 pt-2 right">
                    <q-btn @click="gotToLink('edit-task-components', props.row)" size="sm" icon="edit" outline unelevated color="primary" label="Edit" class="capital mr"/>
                    <q-btn @click="gotToLink('view-task-components', props.row)" size="sm" icon="find_in_page" unelevated color="primary" label="Detail" class="capital "/>
                  </div>
                </q-card-section>

              </q-card>
            </div>
          </template>
        </q-table>
      </div>

  </div>
</template>

<style>
.box-tgl {
  min-height: 10px;
  min-width: 50px;
  width: 100%;
  padding: 20px 20px 20px 15px;
  border-radius: 5px;
  text-align: center;
  font-size: 22px;
}

.box-tgl small {
  font-size: 12px;
  position: relative;
  top: -15px;
}
</style>

<script>

export default {
  name: 'Components',
  data () {
    return {
      Meta: {
        name: 'Components',
        icon: 'list_alt',
        module: 'components',
        topBarMenu: []
      },
      API: this.$Api,
      // default data
      title: 'Components',
      from: '',
      to: '',
      dataModel: {},
      select: {
        works: [],
        worksTmp: []
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

      //
    }
  },

  created () {
    this.initTopBar()
    this.dataModel.project_id = this.$Helper.hasProjectId(this)
  },

  mounted () {
    this.from = this.$Helper.firstOfMonth()
    this.onRefresh()
  },

  methods: {

    onRefresh () {
      this.loadComponent()
    },

    initTopBar () {
      this.Meta.topBarMenu = [
        { name: 'Refresh', event: this.onRefresh }
      ]
    },

    backToRoot () {
      this.$router.push({ name: 'project-tasks', params: { project_id: this.dataModel.project_id } })
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

      var projectId = this.$Helper.hasProjectId(this)
      var endpoint = 'projects/' + projectId + '/task-components?table'

      endpoint = endpoint + '&page=' + page
      endpoint = endpoint + '&limit=' + perpage
      if (this.tableComponent.filterByType) {
        var search = 'type!:' + this.tableComponent.filterByType
        if (this.tableComponent.search !== '') search = this.tableComponent.searchBy + ':' + this.tableComponent.search + '|' + search
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
      this.$router.push({ name: path, params: data })
    },

    openWork (work) {
      var link = '/projects/' + work.project_id + '/works/view/' + work.id
      this.$Helper.openLink(link)
    },

    openTask (work) {
      var link = '/projects/' + work.project_id + '/tasks/view/' + work.id
      this.$Helper.openLink(link)
    },

    showFilterForm () {
      this.showFilter = !this.showFilter
    }

  }
}
</script>
