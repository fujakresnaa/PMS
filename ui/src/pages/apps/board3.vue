
<template >
<div class="kanban-content bg-grad">
  <!--Top Menu & Search table-->
  <div class="row">
    <div class="col-12">
      <q-toolbar class="text-light bg-grad">
        <q-btn flat round dense icon="home" @click="home"/>
        <q-toolbar-title>
          <small  class="bold top-bar-title">Boards</small>
          <small class="menu-separate  top-bar-title" @click="onRefresh" >Refresh</small>
        </q-toolbar-title>
        <q-btn flat round :icon="(isCompact) ? 'view_column' : 'horizontal_distribute'" color="light" class="mr-1" @click="isCompact = !isCompact" >
          <q-tooltip>Compact Mode : {{ (isCompact ? 'ON' : 'OFF') }}</q-tooltip>
        </q-btn>
        <q-input debounce="300" placeholder="Search..." v-model="table.search" outlined dense class="gt-xs fix-after bg-white box-shadow " style="border-radius:5px; " >
          <template v-slot:append>
            <q-icon v-if="table.search !== ''" name="close" @click="table.search = ''" class="cursor-pointer" />
            <q-icon name="search" />
          </template>
        </q-input>
      </q-toolbar>
      <q-toolbar class="text-light bg-grad row lt-sm">
        <div class="col-12 ph pl-1 pr-2 text-dark">
          <q-input debounce="300" placeholder="Search..." v-model="table.search" outlined dense class="fix-after bg-white box-shadow " style="border-radius:5px; " >
            <template v-slot:append>
              <q-icon v-if="table.search !== ''" name="close" @click="table.search = ''" class="cursor-pointer" />
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
      </q-toolbar>
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
          <div class="ph pv col-12 col-sm-4 grid-style-transition animated fadeIn" >
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

  <!--Kanban Board -->
  <div class="kanban-inner animated fadeInUp" v-if="table.search === ''">
    <div :class="'card-scene ' + compactMode">
      <Container
        orientation="horizontal"
        @drop="onColumnDrop($event)"
        drag-handle-selector=".column-drag-handle"
        @drag-start="(e) => dragStart(e)"
        :drop-placeholder="upperDropPlaceholderOptions"
      >
        <Draggable v-for="column in scene.children" :key="column.id">
          <div :class="column.props.className">
            <div class="card-column-header">
              <span class="column-drag-handle" v-if="false">&#x2630;</span>
              <q-icon name="drag_indicator" class="text-grey-7 column-drag-handle"/>
              <span class="bold text-grey-10 text-h6"> {{ column.name }}</span>
              <q-badge color="orange-8" :label="column.children.length" style="position: relative;top: -2px;left: 6px;"/>
            </div>
            <Container
              group-name="col"
              @drop="(e) => onCardDrop(column.id, e)"
              @drag-start="(e) => dragHandler('start', e)"
              @drag-end="(e) => dragHandler('end', e)"
              :get-child-payload="getCardPayload(column.id)"
              drag-class="card-ghost"
              drop-class="card-ghost-drop"
              :drop-placeholder="dropPlaceholderOptions"
            >
              <Draggable v-for="card in column.children" :key="card.id">
                <div :class="'card box-shadow link hoverable ' + card.indicator" >
                  <div class="row">

                    <div class="col-12" @click="openTask(card)">
                      <!-- Start Indicator -->
                      <q-icon name="play_arrow" color="indigo" size="16px"
                      v-if="card.actual_start_date !== null && card.actual_end_date === null"/>

                      <!-- Completed Indicator -->
                      <q-icon name="verified" color="green" size="16px"
                      v-if="card.actual_start_date !== null && card.actual_end_date !== null" />

                      <span class="bold mv">{{card.name}}</span>

                      <!-- Overtime Indicator -->
                      <q-icon name="update" color="red" size="16px" v-if="card.is_overtime"/>
                    </div>

                    <div class="col-12">
                      <q-badge v-for="(label, i) in card.task_labels" :key="i+'lbl'"
                        :color="label.color" text-color="white" size="21px" class="mr"
                        :style="'font-size:10px; background:' + label.color"
                        >
                        {{label.name}}
                      </q-badge>
                    </div>

                    <div class="col-12 mt-1 pb" v-if="card.assignees.length">
                      <q-separator class="mb-1" />
                      <q-avatar color="grey-6" text-color="white" size="21px" class="mr"
                        v-for="(user, i) in card.assignees" :key="i+'ass'"
                        >
                        <span class="capital">{{$Helper.getFirstChar(user.name, 1)}}</span>
                        <q-tooltip>{{user.name}}</q-tooltip>
                      </q-avatar>
                    </div>

                  </div>
                </div>
              </Draggable>
            </Container>
          </div>
        </Draggable>
      </Container>
    </div>
  </div>
</div>
</template>

<style>
  .hoverable {
    -webkit-transition: transform 0.2s ease-out;
    -moz-transition: transform 0.2s ease-out;
    -o-transition: transform 0.2s ease-out;
    transition: transform 0.2s ease-out;
  }

  .hoverable:hover {
    transform: scale(1.02);
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.32), 1px 3px 5px rgba(0, 0, 0, 0.3);
    cursor: pointer;
  }

  .card.today {
    border-left:3px blue solid;
  }

  .card.due {
    border-left:3px orange solid;
  }

  .card.over {
    border-left:3px red solid;
  }
</style>

<script>
import draggable from 'vuedraggable'

import { Container, Draggable } from 'vue-smooth-dnd'
import { applyDrag } from '../../services/KanbanUtils'

export default {
  components: {
    draggable,
    Container,
    Draggable
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
      },
      scene: {},
      upperDropPlaceholderOptions: {
        className: 'cards-drop-preview',
        animationDuration: '150',
        showOnTop: true
      },
      dropPlaceholderOptions: {
        className: 'drop-preview',
        animationDuration: '150',
        showOnTop: true
      },
      isCompact: false
      //
    }
  },

  created () {
    this.dataModel.project_id = this.$Helper.hasProjectId(this)
  },

  mounted () {
    this.onRefresh()
  },

  computed: {
    compactMode () {
      return (this.isCompact) ? 'compact' : ''
    }
  },

  methods: {

    onRefresh () {
      //
      this.getListSelect('projects/' + this.dataModel.project_id + '/board-v3?limit=0&order=ordering:ASC', 'tasks')
    },

    home () {
      this.$router.push({ name: 'project-tasks', params: { project_id: this.dataModel.project_id } })
    },

    getListSelect (endpoint, selectSource = null) {
      this.$Helper.loadingOverlay(true)
      // var endpoint = 'menus'
      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          this.scene = data

          // append to table
          var tasks = []
          for (const row of data.children) {
            // console.log('row', row)
            for (const task of row.children) {
              // console.log('task', task)
              tasks.push(task)
            }
          }
          this.table.data = tasks
        }
      })
    },

    async filterSelect (val, update, target) {
      this.select = await this.$Helper.filterSelect(val, update, target, this.select)
    },

    openTask (task, newTab = true) {
      if (newTab) {
        var link = '/projects/' + task.project_id + '/tasks/view/' + task.id
        this.$Helper.openLink(link)
      } else {
        this.TaskDetail.data = task
        this.TaskDetail.show = true
      }
    },

    cardUpdateHandler (mode = 'order', task, currentStatus, currentOrdering, newColumn, fromSelect = false) {
      console.log(`(${mode}) = Status : ${task.status} > ${currentStatus} | Order : ${task.ordering} > ${currentOrdering}`)
      //
      var needUpdate = {
        status: newColumn.id,
        data: []
      }

      newColumn.children.map(task => {
        needUpdate.data.push(task)
      })

      task.column_update = this.$Helper.unReactive(needUpdate)

      if (mode === 'status') {
        var taskStatus = task.status
        task.status = currentStatus
        if (currentOrdering !== null) {
          task.ordering = currentOrdering
        } else task.status = taskStatus
      }
      if (mode === 'order' && currentOrdering !== null) task.ordering = currentOrdering

      if (task.status !== currentStatus) task.log_type = 'task-moving'

      var status = this.$Helper.findObjectByKey(this.scene.children, 'id', currentStatus)
      var msg = `Task (${mode}) ${task.name} moved to ${status.name}`
      console.log(msg, currentStatus, currentOrdering)

      this.API.put(`tasks/${task.id}/move`, task, (status, data, message, response, full) => {
        //
        if (status === 200) {
          if (task.status !== currentStatus) this.$Helper.showToast(msg)
          this.fixOrdering(task.column_update)
        } else {
          msg = 'Failed update Task "' + task.name + '" to ' + status.name
          this.$Helper.showToast(msg)
          console.log(full)
        }
        if (fromSelect) this.onRefresh()
      })
    },

    fixOrdering (data) {
      this.API.post('tasks/fix-ordering', data, (status, data, message, response, full) => {
        //
        if (status === 200) {
          console.info('Fix Ordering Tasks successfully')
        } else {
          this.$Helper.showToast('Failed fix ordering tasks')
        }
      })
    },

    // kanban
    onColumnDrop (dropResult) {
      const scene = Object.assign({}, this.scene)
      scene.children = applyDrag(scene.children, dropResult)
      this.scene = scene
    },
    onCardDrop (columnId, dropResult) {
      if (dropResult.removedIndex !== null || dropResult.addedIndex !== null) {
        const scene = Object.assign({}, this.scene)
        const column = scene.children.filter(p => p.id === columnId)[0]
        const columnIndex = scene.children.indexOf(column)
        const newColumn = Object.assign({}, column)
        newColumn.children = applyDrag(newColumn.children, dropResult)
        scene.children.splice(columnIndex, 1, newColumn)
        this.scene = scene

        if (columnId !== dropResult.payload.status) this.cardUpdateHandler('status', dropResult.payload, columnId, dropResult.addedIndex, newColumn)
        else this.cardUpdateHandler('order', dropResult.payload, columnId, dropResult.addedIndex, newColumn)
        // else if (dropResult.addedIndex !== dropResult.payload.ordering) this.cardUpdateHandler(dropResult.payload, columnId, dropResult.addedIndex)
        // console.log(column, columnIndex, newColumn)

        // var list =
      }
    },
    getCardPayload (columnId) {
      return index => {
        return this.scene.children.filter(p => p.id === columnId)[0].children[index]
      }
    },
    dragStart (e) {
      // console.log('drag started', e)
    },
    dragHandler (type, evt) {
      // console.log(type, evt)
    },
    log (...params) {
      console.log(...params)
    }

  }
}
</script>
