
<template >

  <div class="root bg-grad">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row pl-2 pt-2" >

        <div class="col-12 ph pl-1 pr-2 text-dark">
          <q-input debounce="300" placeholder="Search..." v-model="table.search" outlined dense class="fix-after bg-white box-shadow " style="border-radius:5px; " >
            <template v-slot:append>
              <q-icon v-if="table.search !== ''" name="close" @click="table.search = ''" class="cursor-pointer" />
              <q-icon name="search" />
            </template>
          </q-input>
        </div>

        <div class="col-12 col-sm-4 col-md-3 ph pl pr-2 text-dark"
          v-for="(status, i) in select.tasks" :key="'sts'+i"
          >
          <q-card class="text-dark" v-if="table.search === ''" >
            <q-card-section class="bg-white">
              <span class="bold">{{status.name}}</span>
            </q-card-section>

            <q-card-section class="row ph pl pr-2" style="height: 60vh; overflow-y:scroll; background: #f0f0f0;">
                <draggable class="list-group " :list="status.tasks" group="status" @change="(e) => cardHandler(e)">
                  <q-card class="item-dropzone-area ph pv mh-1 " style="width:100%"
                    v-for="(task, index) in status.tasks" :key="'tsk'+index"
                    >
                    <!-- Name -->
                    <q-card-section @click="openTask(task)" class="bg-white pv pt-1 link">
                      <!-- Start Indicator -->
                      <q-icon name="play_arrow" color="indigo" size="16px"
                      v-if="task.actual_start_date !== null && task.actual_end_date === null"/>

                      <!-- Completed Indicator -->
                      <q-icon name="verified" color="green" size="16px"
                      v-if="task.actual_start_date !== null && task.actual_end_date !== null" />

                      <span class="bold mv">{{task.name}}</span>

                      <!-- Overtime Indicator -->
                      <q-icon name="update" color="red" size="16px" v-if="task.is_overtime"/>
                    </q-card-section>
                    <q-separator />

                    <q-expansion-item expand-separator class="link"
                      label="more.."
                    >
                      <q-card>

                        <!-- Works -->
                        <q-card-section class="bg-white ph pl">
                          <q-chip size="sm" v-if="task.works">
                            <q-avatar icon="fact_check" color="orange" text-color="white" />
                            {{task.works.name}}
                            <q-tooltip>Works : {{task.works.name}}</q-tooltip>
                          </q-chip>
                        </q-card-section>
                        <q-separator v-if="task.works" />

                        <!-- Sprints -->
                        <q-card-section class="bg-white ph pl">
                          <q-chip size="sm" v-if="task.sprints">
                            <q-avatar icon="bolt" color="blue" text-color="white" />
                            <span class="ellipsis" >{{task.sprints.name}}</span>
                            <q-tooltip>Sprints : {{task.sprints.name}}</q-tooltip>
                          </q-chip>
                        </q-card-section>
                        <q-separator v-if="task.sprints"/>

                        <!-- Mandays -->
                        <q-card-section class="bg-white ph pl">
                          <q-chip size="sm" v-if="task.mandays > 0">
                            <q-avatar icon="groups" color="red" text-color="white" />
                            {{task.mandays}} &nbsp;
                            <span v-if="task.mandays_actual > 0">({{task.mandays_actual}})</span>
                             <q-tooltip>Mandays :
                              {{task.mandays}} - Actual
                              <span v-if="task.mandays_actual > 0">({{task.mandays_actual}})</span>
                             </q-tooltip>
                          </q-chip>
                        </q-card-section>
                        <q-separator v-if="task.mandays > 0" />

                        <q-card-section class="bg-white pv ph">
                          <q-select label="Status" dense class=""
                            :options="select.tasks"
                            v-model="task.status"
                            option-value="id" option-label="name"
                            emit-value map-options
                            @input="(val) => cardMovedSelect(task, val)"
                            lazy-rules :rules="[
                              val => val !== null && val !== '' || 'Please select one of status',
                            ]"
                          >
                            <template v-slot:no-option>
                              <q-item> <q-item-section class="text-grey"> Not found </q-item-section> </q-item>
                            </template>
                          </q-select>
                        </q-card-section>

                      </q-card>
                    </q-expansion-item>

                    <!-- Labels -->
                    <q-card-section class="bg-white ph pv dragging">
                      <q-badge v-for="(label, i) in task.task_labels" :key="i+'lbl'"
                        :color="label.color" text-color="white" size="21px" class="mr"
                        :style="'font-size:10px; background:' + label.color"
                        >
                        {{label.name}}
                      </q-badge>
                    </q-card-section>

                    <!-- Assignee -->
                    <q-card-section class="bg-white ph pv dragging">
                      <q-avatar :color="$Helper.randomColor()" text-color="white" size="21px" class="mr"
                        v-for="(user, i) in task.assignees" :key="i+'ass'"
                        >
                        <span class="capital">{{$Helper.getFirstChar(user.name, 1)}}</span>
                        <q-tooltip>{{user.name}}</q-tooltip>
                      </q-avatar>
                    </q-card-section>
                  </q-card>
                </draggable>
            </q-card-section>

          </q-card>
        </div>

        <div class="col-12 pl pr-1 text-dark" v-if="table.search !== ''">
          <q-table class="no-shadow th-lg "
            grid
            :data="table.data"
            :columns="table.columns"
            row-key="id"
            :loading="table.loading"
            :filter="table.search"
            :rows-per-page-options="[100, 400, 500]"
          >
            <template v-slot:item="props">
              <div class="ph pv col-12 col-sm-6 grid-style-transition animated fadeIn" >
                <q-card class="ph pv mh-1 mv " >
                    <!-- Name -->
                    <q-card-section @click="openTask(props.row)" class="bg-white pv pt-1 link">
                      <!-- Start Indicator -->
                      <q-icon name="play_arrow" color="indigo" size="16px"
                      v-if="props.row.actual_start_date !== null && props.row.actual_end_date === null"/>

                      <!-- Completed Indicator -->
                      <q-icon name="verified" color="green" size="16px"
                      v-if="props.row.actual_start_date !== null && props.row.actual_end_date !== null" />

                      <span class="bold mv">{{props.row.name}}</span>

                      <!-- Overtime Indicator -->
                      <q-icon name="update" color="red" size="16px" v-if="props.row.is_overtime"/>
                    </q-card-section>
                    <q-separator />

                    <!-- status -->
                    <q-card-section class="bg-white ph pl">
                      <span class="bold text-primary">{{props.row.status_name}}</span>
                    </q-card-section>
                    <q-separator v-if="props.row.works" />

                    <!-- Works -->
                    <q-card-section class="bg-white ph pl">
                      <q-chip size="sm" v-if="props.row.works">
                        <q-avatar icon="fact_check" color="orange" text-color="white" />
                        {{props.row.works.name}}
                        <q-tooltip>Works : {{props.row.works.name}}</q-tooltip>
                      </q-chip>
                    </q-card-section>
                    <q-separator v-if="props.row.works" />

                    <!-- Sprints -->
                    <q-card-section class="bg-white ph pl">
                      <q-chip size="sm" v-if="props.row.sprints">
                        <q-avatar icon="bolt" color="blue" text-color="white" />
                        <span class="ellipsis" >{{props.row.sprints.name}}</span>
                        <q-tooltip>Sprints : {{props.row.sprints.name}}</q-tooltip>
                      </q-chip>
                    </q-card-section>

                    <!-- Mandays -->
                    <q-separator v-if="props.row.mandays > 0" />
                    <q-card-section class="bg-white ph pl">
                      <q-chip size="sm" v-if="props.row.mandays > 0">
                        <q-avatar icon="groups" color="red" text-color="white" />
                        {{props.row.mandays}} &nbsp;
                        <span v-if="props.row.mandays_actual > 0">({{props.row.mandays_actual}})</span>
                          <q-tooltip>Mandays :
                          {{props.row.mandays}} - Actual
                          <span v-if="props.row.mandays_actual > 0">({{props.row.mandays_actual}})</span>
                          </q-tooltip>
                      </q-chip>
                    </q-card-section>

                    <!-- Labels -->
                    <q-card-section class="bg-white ph pv dragging">
                      <q-badge v-for="(label, i) in props.row.task_labels" :key="i+'lbl'"
                        :color="label.color" text-color="white" size="21px" class="mr"
                        :style="'font-size:10px; background:' + label.color"
                        >
                        {{label.name}}
                      </q-badge>
                    </q-card-section>

                    <!-- Assignee -->
                    <q-card-section class="bg-white ph pv dragging">
                      <q-avatar :color="$Helper.randomColor()" text-color="white" size="21px" class="mr"
                        v-for="(user, i) in props.row.assignees" :key="i+'ass'"
                        >
                        <span class="capital">{{$Helper.getFirstChar(user.name, 1)}}</span>
                        <q-tooltip>{{user.name}}</q-tooltip>
                      </q-avatar>
                    </q-card-section>
                  </q-card>
              </div>
            </template>
          </q-table>
        </div>

      </div>

      <div class="mb-5">.</div>

      <q-dialog v-model="TaskDetail.show" transition-show="jump-up" transition-hide="jump-down" >
        <q-card style="width: 900px; max-width: 90vw;">
          <q-card-section style="max-height: 80vh" class="scroll no-padding">
             <TaskDetailPage v-if="TaskDetail.show" :data="TaskDetail.data" />
          </q-card-section>
          <q-card-actions align="right" class="bg-white text-dark bold">
            <q-btn unelevated class="capital" icon="launch" color="orange-7" label="Open in new tab" @click="openTask(TaskDetail.data, true)" v-close-popup/>
            <q-btn outline class="capital" icon="close" label="Close" @click="onRefresh" v-close-popup/>
          </q-card-actions>
        </q-card>
      </q-dialog>

  </div>
</template>

<style lang="scss">

  .list-group {
    width: 100%;
  }

  $ease-out: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
  $to-do: #f4ce46;
  $in-progress: #2a92bf;
  $approved: #00b961;

  .drag-item {
    margin: 10px;
    height: 100px;
    background: rgba(black, 0.4);
    transition: $ease-out;

    /* items grabbed state */
    &[aria-grabbed="true"] {
      background: #5cc1a6;
      color: #fff;
    }

    .drag-item-text {
      font-size: 1rem;
      padding-left: 1rem;
      padding-top: 1rem;
    }
  }

  @keyframes nodeInserted {
    from {
      opacity: 0.2;
    }
    to {
      opacity: 0.8;
    }
  }

  .item-dropzone-area {
    opacity: 0.8;
    animation-duration: 0.5s;
    animation-name: nodeInserted;
    margin-left: 0.6rem;
    margin-right: 0.6rem;
  }
</style>

<script>
import draggable from 'vuedraggable'
import TaskDetailPage from '../tasks/detailBody'

export default {
  components: {
    draggable,
    TaskDetailPage
  },
  name: 'Board',
  data () {
    return {
      Meta: {
        name: 'Board',
        icon: 'stop_circle',
        module: 'tasks',
        topBarMenu: []
      },
      API: this.$Api,
      // default data
      title: 'Board',
      dataModel: {},
      TaskDetail: {
        show: false,
        data: null
      },
      disableSubmit: false,
      select: {
        tasks: [],
        tasksTmp: []
      },
      table: {
        search: '',
        data: [],
        searchBy: [],
        searchBySelected: null,
        columns: [
          { name: 'action', label: '#', align: 'left', style: 'width: 20px' },
          { name: 'name', label: 'name', field: 'name', align: 'left' }
        ],
        pagination: {
          page: 1,
          rowsPerPage: 100,
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
    this.onRefresh()
  },

  methods: {

    onRefresh () {
      //
      // this.getListSelect('projects/' + this.dataModel.project_id + '/task-status?limit=0', 'taskStatus')
      this.getListSelect('projects/' + this.dataModel.project_id + '/board?limit=0', 'tasks')
    },

    initTopBar () {
      this.Meta.topBarMenu = [{ name: 'Refresh', event: this.onRefresh }]
    },

    backToRoot () {
      this.$router.push({ name: 'project-tasks', params: { project_id: this.dataModel.project_id } })
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

          if (selectSource === 'tasks') {
            var tasks = []
            for (const row of data) {
              // console.log('row', row)
              for (const task of row.tasks) {
                // console.log('task', task)
                tasks.push(task)
              }
            }
            this.table.data = tasks
          }
        }
      })
    },

    async filterSelect (val, update, target) {
      this.select = await this.$Helper.filterSelect(val, update, target, this.select)
    },

    openTask (task, newTab = false) {
      if (newTab) {
        var link = '/projects/' + task.project_id + '/tasks/view/' + task.id
        this.$Helper.openLink(link)
      } else {
        this.TaskDetail.data = task
        this.TaskDetail.show = true
      }
    },

    cardHandler (evt, val) {
      console.log(evt)
      if (evt.moved) this.cardSorted(evt.moved.element, evt.moved.newIndex)
      if (evt.added) this.cardMoved(evt.added.element, evt.added.newIndex)
    },

    cardSorted (task, index) {
      task.ordering = index
      console.log('cardSorted', task)
      this.API.put('tasks' + '/' + task.id, task, (status, data, message, response, full) => {
        //
      })
    },

    cardMoved (task, index) {
      task.ordering = index
      task.log_type = 'task-moving'
      for (const status of this.select.tasks) {
        var check = this.$Helper.findObjectByKey(status.tasks, 'id', task.id)
        // console.log('check', { status, check })
        if (check) { // di check untuk memastikan jika task berhasil di pindah
          this.saveCardMoved(task, status)
        }
      }
    },

    cardMovedSelect (task, _status) {
      var status = this.$Helper.findObjectByKey(this.select.tasks, 'id', _status)
      // console.log(_status, status)
      if (status) this.saveCardMoved(task, status, true)
      else this.$Helper.showToast('status with id:' + _status + ' not found')
    },

    saveCardMoved (task, status, fromSelect = false) {
      task.status = status.id
      var msg = 'Task "' + task.name + '" moved to ' + status.name
      console.log(msg)
      this.API.put('tasks' + '/' + task.id, task, (status, data, message, response, full) => {
        //
        if (status === 200) {
          this.$Helper.showToast(msg)
        } else {
          msg = 'Failed moving Task "' + task.name + '" to ' + status.name
          this.$Helper.showToast(msg)
        }

        if (fromSelect) this.onRefresh()
      })
    }

  }
}
</script>
