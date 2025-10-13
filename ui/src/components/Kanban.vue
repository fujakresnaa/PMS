
<template >
<div class="card-scene">
<Container
  orientation="horizontal"
  @drop="onColumnDrop($event)"
  drag-handle-selector=".column-drag-handle"
  @drag-start="dragStart"
  :drop-placeholder="upperDropPlaceholderOptions"
>
  <Draggable v-for="column in scene.children" :key="column.id">
  <div :class="column.props.className">
      <div class="card-column-header">
      <span class="column-drag-handle">&#x2630;</span>
      {{ column.name }}
      </div>
      <Container
      group-name="col"
      @drop="(e) => onCardDrop(column.id, e)"
      @drag-start="(e) => log('drag start', e)"
      @drag-end="(e) => log('drag end', e)"
      :get-child-payload="getCardPayload(column.id)"
      drag-class="card-ghost"
      drop-class="card-ghost-drop"
      :drop-placeholder="dropPlaceholderOptions"
      >
      <Draggable v-for="card in column.children" :key="card.id">
          <div :class="card.props.className" :style="card.props.style">
          <p>{{ card.data }}</p>
          </div>
      </Draggable>
      </Container>
  </div>
  </Draggable>
</Container>
</div>
</template>

<script>
import draggable from 'vuedraggable'

import { Container, Draggable } from 'vue-smooth-dnd'
import { applyDrag, generateItems } from '../services/KanbanUtils'

const lorem = `Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.`
const columnNames = ['Lorem', 'Ipsum', 'Consectetur', 'Eiusmod']

const cardColors = [
  'azure',
  'beige',
  'bisque',
  'blanchedalmond',
  'burlywood',
  'cornsilk',
  'gainsboro',
  'ghostwhite',
  'ivory',
  'khaki'
]
const pickColor = () => {
  const rand = Math.floor(Math.random() * 10)
  return cardColors[rand]
}
const scene = {
  type: 'container',
  props: {
    orientation: 'horizontal'
  },
  children: generateItems(4, i => ({
    id: `column${i}`,
    type: 'container',
    name: columnNames[i],
    props: {
      orientation: 'vertical',
      className: 'card-container'
    },
    children: generateItems(+(Math.random() * 10).toFixed() + 5, j => ({
      type: 'draggable',
      id: `${i}${j}`,
      props: {
        className: 'card',
        style: { backgroundColor: pickColor() }
      },
      data: lorem.slice(0, Math.floor(Math.random() * 150) + 30)
    }))
  }))
}

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
      scene
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
      this.getListSelect('projects/' + this.dataModel.project_id + '/board-v2?limit=0', 'tasks')
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

    openTask (task, newTab = true) {
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
      this.API.put(`tasks/${task.id}/move`, task, (status, data, message, response, full) => {
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
      this.API.put(`tasks/${task.id}/move`, task, (status, data, message, response, full) => {
        //
        if (status === 200) {
          this.$Helper.showToast(msg)
        } else {
          msg = 'Failed moving Task "' + task.name + '" to ' + status.name
          this.$Helper.showToast(msg)
        }

        if (fromSelect) this.onRefresh()
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
      }
    },
    getCardPayload (columnId) {
      return index => {
        return this.scene.children.filter(p => p.id === columnId)[0].children[index]
      }
    },
    dragStart () {
      console.log('drag started')
    },
    log (...params) {
      console.log(...params)
    }

  }
}
</script>
