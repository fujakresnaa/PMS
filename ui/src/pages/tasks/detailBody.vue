
<template >

  <div class="root bg-soft">

      <!-- Header Title -->
      <div class="row mv-2">

        <div class="col-12 mt-2">
          <q-tabs v-model="panel"
            inline-label narrow-indicator align="left"
            class="q-mb-lg"
          >
            <q-tab class="text-dark" name="task" icon="app_registration" label="Task" />
            <q-tab @click="loadComponent" class="text-dark" name="components" icon="view_in_ar" label="Components" />
            <q-tab @click="getComment" class="text-dark" name="comments" icon="speaker_notes" label="Comments" />
          </q-tabs>
        </div>

        <div class="col-12">
          <q-tab-panels v-model="panel" animated class="shadow-2 rounded-borders box-shadow" >

            <q-tab-panel name="task" class="row">
              <div class="col-12 col-sm-7 col-md-9">
                <div class="text-h5 bold pt-1 pb-1">
                  {{dataModel.name}}

                  <q-chip v-if="dataModel.is_overtime">
                    <q-avatar icon="update" color="red" text-color="white" />
                    Overtime
                  </q-chip>
                </div>
              </div>

              <div class="col-12 col-sm-4 col-md-3 right">
                <q-btn-dropdown size="sm" unelevated
                  label="Action"
                  color="primary"
                >
                  <q-list>
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
                        <q-item-label>Start Task</q-item-label>
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
                        <q-item-label caption>Task will be hidden on the board</q-item-label>
                      </q-item-section>
                    </q-item>

                  </q-list>

                </q-btn-dropdown>
              </div>

              <div class="col-12">
                <div class="bold pt-1 text-grey-6" v-if="dataModel.works">
                  <small>From Work</small> <br> <q-btn flat outline color="primary" @click="linkTo('works', dataModel.works)" class="capital text-primary link" >{{dataModel.works.name}}</q-btn>
                </div>
                <div class="bold pt-1 text-grey-6" v-if="dataModel.sprints">
                  <small>From Spints</small> <br> <q-btn flat outline color="primary" @click="linkTo('sprints', dataModel.sprints)" class="capital text-primary link" >{{dataModel.sprints.name}}</q-btn>
                </div>
                <q-separator class="mb-2"/>
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
                <q-chip v-if="dataModel.status_name">
                  <q-avatar icon="stars" color="purple" text-color="white" />
                  {{dataModel.status_name}}
                </q-chip>
              </div>

              <div class="col-12 text-dark">

                <q-separator class="mh-1" v-if="dataModel.mandays > 0"/>
                <div class="bold pt-1 text-grey-6" v-if="dataModel.mandays > 0">
                  <small>Mandays : </small><br>
                  <span  class="bold text-primary">
                    {{dataModel.mandays}}
                    <span class="bold text-purple">({{dataModel.mandays_actual}})</span>
                  </span>
                </div>

                <q-separator class="mh-1"/>
                <div class="bold pt-1 text-grey-6"> <small>Assignees : </small><br>
                  <q-chip size="sm" v-for="(user, i) in dataModel.assignees" :key="i+'idx'">
                    <q-avatar :color="$Helper.randomColor()" text-color="white" >
                      <span class="uppercase">{{$Helper.getFirstChar(user.name)}}</span>
                    </q-avatar>
                    {{user.name}}
                  </q-chip>
                </div>

                <q-separator class="mh-1"/>
                <q-chip dense square v-for="(label, i) in dataModel.task_labels" :key="i+'idx'">
                  <q-avatar icon="bookmark" :style="'background:' + label.color" text-color="white" />
                  {{label.name}}
                </q-chip>

              </div>
              <div class="col-12">
                <div class="mh-2" v-html="dataModel.description"></div>
              </div>
            </q-tab-panel>

            <q-tab-panel name="components">
              <div class="row">
                <div class="col-6 col-sm-3 col-md-6">
                  <q-btn @click="addComponent" icon="note_add" unelevated color="primary" label="Add Component" class="ml capital"/>
                </div>

                <div class="col-6 col-sm-3 col-md-2 pb-1 pr-1-5">
                  <q-select :options="tableComponent.searchByList" dense outlined
                    v-model="tableComponent.searchBy" label="Searc By" class="bg-white box-shadow"
                    style="border-radius:5px; " transition-show="jump-up" transition-hide="jump-down" />
                </div>

                <div class="col-12 col-sm-6 col-md-4 right">
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
                            <q-item-label @click="gotToLink('view-works', props.row.tasks.works)" class="bold text-primary link">{{props.row.tasks.works.name}}</q-item-label>
                          </q-item-section>
                        </q-item>
                        <q-separator v-if="props.row.tasks.works" />

                        <!-- tasks -->
                        <q-item v-if="props.row.tasks && props.row.tasks.project_id === dataModel.project_id ">
                          <q-item-section>
                            <q-item-label caption>From Task</q-item-label>
                            <q-item-label @click="gotToLink('view-tasks', props.row.tasks)" class="bold text-primary link">{{props.row.tasks.name}}</q-item-label>
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
            </q-tab-panel>

            <q-tab-panel name="comments">

              <!-- comment form -->
              <div class="mh-2">

                <q-dialog v-model="comments.form" :persistent="true">
                  <q-card >

                    <q-card-section v-if="comments.model.reply_info" >
                      <div class="quote bold" >Reply </div>
                      <q-item >
                        <q-item-section avatar>
                          <q-avatar :color="$Helper.randomColor()" text-color="white" >
                            <span class="uppercase">{{$Helper.getFirstChar(comments.model.reply_info.created_by_user.name)}}</span>
                          </q-avatar>
                        </q-item-section>

                        <q-item-section>
                          <q-item-label lines="1">
                            <b>{{comments.model.reply_info.created_by_user.name}}</b>
                          </q-item-label>
                          <q-item-label lines="2">
                           <div v-html="comments.model.reply_info.description"></div>
                          </q-item-label>
                        </q-item-section>
                      </q-item>
                      <q-separator />
                    </q-card-section >

                    <q-card-section >
                      <q-form class="row" @submit="submit">
                        <q-card-section >
                          <q-editor v-model="comments.model.description"
                            :toolbar="$Config.editorTollbar($q)"
                            :fonts="$Config.editorFont()"
                            :min-height="'140px'"
                            :max-width="'400px'"
                          />

                        </q-card-section>

                        <q-card-actions align="right" class="right">
                          <q-btn class="capital bold" unelevated flat color="red" label="Cancel" icon="cancel" @click="resetFormComment(true)" />
                          <q-btn class="capital bold" unelevated color="green" label="Save" type="submit" icon="check_circle"/>
                        </q-card-actions>
                      </q-form>
                    </q-card-section>
                  </q-card>
                </q-dialog>
                <q-btn @click="comments.form = true" label="Add Comment" icon="add_comment" color="primary" class="capital" unelevated />
              </div>

              <!-- Comment list -->
              <div>
                <q-list bordered class="rounded-borders mb-1" style="max-width: 100%"
                  v-for="(comment, i) in this.commentList" :key="i + 'cm'"
                  >

                  <!-- user -->
                  <q-item >
                    <q-item-section avatar>
                      <q-avatar :color="$Helper.randomColor()" text-color="white" >
                        <span class="uppercase">{{$Helper.getFirstChar(comment.created_by_user.name)}}</span>
                      </q-avatar>
                    </q-item-section>

                    <q-item-section>
                      <q-item-label lines="1">
                        <b>{{comment.created_by_user.name}}</b>
                        <small  v-if="comment.replies.length > 0" class="text-primary bold">- {{comment.replies.length}} Replies</small>
                      </q-item-label>
                      <q-item-label lines="1">
                        <small  class="text-grey-5 bold">{{$Helper.toDate(comment.updated_at, 'DD MMM YYYY - HH:mm')}}</small>
                      </q-item-label>
                    </q-item-section>

                    <q-item-section side top>
                      <small class="text-grey-6 italic">{{comment.timestamps}}</small>
                    </q-item-section>
                  </q-item>

                  <!-- Comment -->
                  <q-item >
                    <q-item-section avatar> </q-item-section>
                    <q-item-section>
                      <q-item-label class="text-subtitle2 text-dark" lines="0">
                        <div v-html="comment.description"></div>
                      </q-item-label>
                      <q-item-label class="text-subtitle2 text-dark" lines="0">
                        <q-btn @click="reply(comment)" size="sm" outline unelevated label="reply" icon="reply" color="primary" class="capital mt-1" />
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                  <!-- Reply -->
                  <q-item >
                    <q-item-section avatar> </q-item-section>
                    <q-item-section>
                      <q-item-label class="text-subtitle2 text-dark" lines="0">
                        <q-list class="pt-1 mb-1" style="max-width: 100%; border-top:1px solid #eee"
                          v-for="(commentRep, i) in comment.replies" :key="i + 'cmr'"
                          >

                          <!-- user -->
                          <q-item >
                            <q-item-section avatar>
                              <q-avatar :color="$Helper.randomColor()" text-color="white" >
                                <span class="uppercase">{{$Helper.getFirstChar(commentRep.created_by_user.name)}}</span>
                              </q-avatar>
                            </q-item-section>

                            <q-item-section>
                              <q-item-label lines="1">
                                <b>{{commentRep.created_by_user.name}}</b>
                              </q-item-label>
                              <q-item-label lines="1">
                                <small  class="text-grey-5 bold">{{$Helper.toDate(commentRep.updated_at, 'DD MMM YYYY - HH:mm')}}</small>
                              </q-item-label>
                            </q-item-section>

                            <q-item-section side top>
                              <small class="text-grey-6 italic">{{commentRep.timestamps}}</small>
                            </q-item-section>
                          </q-item>

                          <!-- Comment -->
                          <q-item >
                            <q-item-section avatar> </q-item-section>
                            <q-item-section>
                              <q-item-label class="text-subtitle2 text-dark" lines="0">
                                <div v-html="commentRep.description"></div>
                              </q-item-label>
                            </q-item-section>
                          </q-item>

                        </q-list>
                      </q-item-label>
                    </q-item-section>
                  </q-item>

                </q-list>
              </div>
            </q-tab-panel>
          </q-tab-panels>
        </div>

      </div>

  </div>
</template>

<script>
import Meta from './meta'

export default {
  name: 'Tasks',
  props: ['data'],
  data () {
    return {
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
      var link = '/projects/' + data.project_id + '/' + page + '/view/' + data.id
      this.$Helper.openLink(link)
      console.log(page)
      // this.$router.push({ name: page, params: data })
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
      this.comments.model.task_id = this.dataModel.id
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
          this.resetFormComment()
        } else this.disableSubmit = false
      })
    },

    update () {
      this.$Helper.loadingOverlay(true, 'updating..')
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
      this.$Helper.showSuccess(titleAdd + ' Succesfully', msg)
    },

    reply (comment) {
      this.comments.model.reply = comment.id
      this.comments.model.task_id = comment.task_id
      this.comments.model.reply_info = comment
      this.comments.model.description = ''
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
    },

    completed () {
      var that = this
      this.$q.dialog({
        title: 'Set Completed Task',
        message: 'Are you sure want to set Completed this task ?',
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
      this.API.put('tasks' + '/' + this.dataModel.id + '/' + type, this.dataModel, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          var msg = 'Task "' + this.dataModel.name + '" has ' + type
          this.messageSubmit('Task ' + type, msg)
          this.getData(this.dataModel.id)
        }
      })
    },

    preview () {
      var url = this.$Config.getApiRoot() + 'preview/tasks/' + this.dataModel.id
      this.$Helper.openLink(url)
    }
  }
}
</script>
