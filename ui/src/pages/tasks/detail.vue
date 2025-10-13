
<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer  v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu" />

    <TaskDetailPage v-if="dataModel.id" :data="dataModel" />

  </div>
</template>

<script>
import Meta from './meta'
import TaskDetailPage from '../tasks/detailBody'

export default {
  components: {
    TaskDetailPage
  },
  name: 'Tasks',
  props: ['bodyOnly', 'data'],
  data () {
    return {
      showBodyOnly: false,
      Meta,
      API: this.$Api,
      // default data
      dataModel: this.$Helper.unReactive(Meta.model),
      rules: {
        permission: Meta.permission
      },
      panel: 'task',
      tableComponent: {
        search: '',
        data: [],
        searchByList: ['name', 'type', 'summary', 'description'],
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
      },
      disableSubmit: false,
      comments: {
        form: false,
        model: {
          id: null,
          task_id: null,
          description: '',
          reply: null,
          reply_info: null,
          created_by: null,
          updated_by: null,
          deleted_by: null

        }
      },
      commentList: []
    }
  },

  created () {
    this.initTopBar()
  },

  mounted () {
    if (this.bodyOnly) this.showBodyOnly = this.bodyOnly
    if (this.data) {
      this.dataModel = this.data
    } else this.onRefresh()
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
      this.$Helper.loadingOverlay()
      var endpoint = this.Meta.module + '/' + id
      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          // inject data
          this.dataModel = data
          this.comments.model.task_id = data.id

          this.getListComponent({
            pagination: this.tableComponent.pagination,
            search: this.tableComponent.search
          })
        }
      })
    },

    edit () {
      this.$router.push({ name: 'edit-' + this.Meta.module, params: this.dataModel })
    },

    backToRoot () {
      this.$router.push({ name: 'project-tasks', params: { project_id: this.dataModel.project_id } })
    },

    linkTo (page, data) {
      console.log(page)
      this.$router.push({ name: page, params: data })
    },

    getListComponent (props) {
      this.$Helper.loading()
      this.tableComponent.loading = true
      this.tableComponent.selected = []
      const { page, rowsPerPage } = props.pagination
      const perpage = rowsPerPage === 0 ? this.tableComponent.pagination.rowsNumber : rowsPerPage

      var projectId = this.$Helper.hasProjectId(this)
      var endpoint = 'projects/' + projectId + '/task-components?task=' + this.dataModel.id + '&table'

      endpoint = endpoint + '&page=' + page
      endpoint = endpoint + '&limit=' + perpage
      if (this.tableComponent.search !== '') endpoint = endpoint + '&search=' + this.tableComponent.searchBy + ':' + this.tableComponent.search
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

        this.getComment()
      })
    },

    getComment () {
      this.$Helper.loadingOverlay()
      var endpoint = 'tasks/' + this.dataModel.id + '/comments'
      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          // inject data
          this.commentList = data
        }
      })
    },

    gotToLink (path, data) {
      data.project_id = this.dataModel.project_id
      this.$router.push({ name: path, params: data })
    },

    addComponent () {
      var projectId = this.$Helper.hasProjectId(this)
      var data = { project_id: projectId, task_id: this.dataModel.id }
      this.$router.push({ name: 'add-task-components', params: data })
    },

    // comments
    submit () {
      if (this.validateSubmit()) {
        this.disableSubmit = true
        if (this.comments.model.id !== null) this.update()
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
      this.API.post('task-comments', this.comments.model, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          this.messageSubmit('Saving', message)
          this.comments.form = false
        } else this.disableSubmit = false
      })
    },

    update () {
      this.$Helper.loadingOverlay(true, 'Saving..')
      this.API.put('task-comments' + '/' + this.comments.model.id, this.comments.model, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          this.messageSubmit('Update', message)
          this.comments.form = false
        } else this.disableSubmit = false
      })
    },

    messageSubmit (titleAdd = '', msg) {
      this.getComment()
      this.$Helper.showAlert(titleAdd + ' Succesfully', msg)
    },

    reply (comment) {
      this.comments.model.reply = comment.id
      this.comments.model.task_id = comment.task_id
      this.comments.model.reply_info = comment
      this.comments.form = true
    },

    resetFormComment (dialog = true) {
      this.comments.model = {
        id: null,
        task_id: null,
        description: '',
        reply: null,
        reply_info: null,
        created_by: null,
        updated_by: null,
        deleted_by: null
      }
      if (dialog) this.comments.form = false
    }
  }
}
</script>
