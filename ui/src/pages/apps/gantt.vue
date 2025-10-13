
<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-if="!shareMode" v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row pt-2" v-if="shareMode">
        <div class="col-12 col-sm-10 pl-1 animated fadeInDown" v-if="!showFilter">
          <div v-if="project">
            <div class="text-h4 bold text-primary">{{project.name}} ({{project.code}})</div>
              <div class="text-subtitle2 pt">
                {{$Helper.toDate(project.start_date, 'DD MMM YYYY - HH:mm')}} -
                {{$Helper.toDate(project.end_date, 'DD MMM YYYY - HH:mm')}}
              </div>
          </div>
        </div>
        <div class="col-12 col-sm-2 ph-1 animated fadeInDown" v-if="!showFilter">
          <q-btn label="show filter" flat class="capital ml-1" icon="filter_alt" color="primary" @click="showFilterForm" />
        </div>
      </div>

      <div class="row" >
        <div class="col-12 row">
          <!-- Form -->
          <div v-if="showFilter" class="col-12 pv-1 ph-1 animated fadeInDown">
            <span class="bold text-primary">Filter & Range Data</span>
          </div>

          <div v-if="showFilter" class="animated fadeIn col-12 col-sm-4 col-md-4 pv-1 ph-1" >
            <q-select clearable ref="works" label="By Works" dense filled square class=""
              :options="select.works"
              v-model="work_id"
              option-value="id" option-label="name"
              emit-value map-options use-input
              @filter="(val, update) => filterSelect(val, update, 'works')"
              @input-value="searchWorksEvt"
              hint="Type to search works.."
              lazy-rules :rules="[
                val => val !== null && val !== '' || 'Work must be choose!',
              ]"
            >
              <template v-slot:no-option>
                <q-item> <q-item-section class="text-grey">{{select.noOptionLabel}}</q-item-section> </q-item>
              </template>
            </q-select>
          </div>

          <div v-if="showFilter" class="animated fadeIn col-12 col-sm-4 col-md-2 pv-1 ph-1">
            <q-input clearable @click="$refs.popupTanggal.show()"  label="From" filled v-model="from" dense square mask="date" >
              <div>
                <q-popup-proxy ref="popupTanggal" transition-show="scale" transition-hide="scale">
                  <q-date v-model="from" @input="() => $refs.popupTanggal.hide()" ></q-date>
                </q-popup-proxy>
              </div>
            </q-input>
          </div>

          <div v-if="showFilter" class="animated fadeIn col-12 col-sm-4 col-md-2 pv-1 ph-1">
            <q-input clearable @click="$refs.popupTanggal1.show()" label="To" filled v-model="to" dense square mask="date" placeholder="empty to set default until end of year">
              <div>
                <q-popup-proxy ref="popupTanggal1" transition-show="scale" transition-hide="scale">
                  <q-date v-model="to" @input="() => $refs.popupTanggal1.hide()" ></q-date>
                </q-popup-proxy>
              </div>
            </q-input>
          </div>

          <div v-if="showFilter" class="animated fadeIn col-12 col-sm-2 col-md-1 pv-1 ph-1">
            <q-toggle size="md" floating dense style="position:relative; top:-17px;"
              v-model="withTask"
              icon="monetization_on"
              label="With Task"
              class="bold toggle-block text-caption"
              emit-value
            />
          </div>

          <div v-if="showFilter" class="animated fadeIn col-12 col-md-3 col-sm-4 pv-1 ph-1">
            <q-btn @click="onRefresh" unelevated color="primary" class="capital" label="Load Data" />
            <q-btn v-if="to !== ''" @click="to = ''" unelevated color="primary" outline class="ml capital" label="to Last Year" />
            <q-btn @click="showFilter = false" unelevated color="red" flat class="ml bg-red-1 capital" label="Hide Filter" />
          </div>
        </div>

        <div class="col-12">
          <div class="text-center mt-5" v-if="!showGantt">
            <q-spinner-facebook class="mt-5"
              color="primary"
              size="8em"
            /> <br><br>
            <span class="pt-3 text-primary" >Compiling data, please wait..</span>
          </div>
          <gantt-elastic v-if="showGantt" class="animated fadeIn"
            :options="opts"
            :tasks="tasks"
            @tasks-changed="tasksUpdate"
            @options-changed="optionsUpdate"
            @dynamic-style-changed="styleUpdate"
          >
            <gantt-header slot="header"></gantt-header>
          </gantt-elastic>
        </div>
      </div>

  </div>
</template>

<script>
import GanttElastic from 'gantt-elastic'
import GanttHeader from 'gantt-elastic-header'
import dayjs from 'dayjs'
import { debounce } from 'quasar'

// just helper to get current dates
function getDate (hours) {
  const currentDate = new Date()
  const currentYear = currentDate.getFullYear()
  const currentMonth = currentDate.getMonth()
  const currentDay = currentDate.getDate()
  const timeStamp = new Date(
    currentYear,
    currentMonth,
    currentDay,
    0,
    0,
    0
  ).getTime()
  return new Date(timeStamp + hours * 60 * 60 * 1000).getTime()
}
const tasks = [
  {
    id: 1,
    label: 'No data loaded',
    user: '-',
    start: getDate(-24 * 5),
    duration: 15 * 24 * 60 * 60 * 1000,
    percent: 85,
    type: 'project'
  }
]

const options = {
  taskMapping: {
    progress: 'percent'
  },
  maxRows: 100,
  maxHeight: 500,
  title: {
    label: '',
    html: false
  },
  row: {
    height: 24
  },
  calendar: {
    hour: {
      display: true
    }
  },
  chart: {
    progress: {
      bar: false
    },
    expander: {
      display: true
    }
  },
  taskList: {
    expander: {
      straight: false
    },
    columns: [
      {
        id: 1,
        label: 'ID',
        value: 'id',
        width: 40
      },
      {
        id: 2,
        label: 'Description',
        value: task => `<span class="bold" style="color:${task.style.base.fill}" >${task.label}</span>`,
        width: 200,
        expander: true,
        html: true,
        events: {
          click ({ data, column }) {
            alert('description clicked!\n' + data.label)
          }
        }
      },
      {
        id: 3,
        label: 'Start',
        value: task => dayjs(task.start).format('DD MMM YYYY'),
        width: 85
      },
      {
        id: 5,
        label: '%',
        value: 'progress',
        width: 35,
        style: {
          'task-list-header-label': {
            'text-align': 'center',
            width: '100%'
          },
          'task-list-item-value-container': {
            'text-align': 'center',
            width: '100%'
          }
        }
      }
    ]
  },
  locale: {
    name: 'en',
    Now: 'Current',
    'X-Scale': '🔍 Calendar',
    'Y-Scale': '🔍 Data',
    'Task list width': 'Task list',
    'Before/After': 'Expand',
    'Display task list': 'Task list'
  },
  times: {
    timeZoom: 21
  }
}

export default {
  name: 'Timeline',
  components: {
    GanttElastic,
    GanttHeader
  },
  data () {
    return {
      day: 25,
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
        noOptionLabel: 'Type 2 character to find work..',
        works: [],
        worksTmp: [],
        tasks: [],
        tasksTmp: []
      },
      //
      tasks,
      options,
      dynamicStyle: {},
      lastId: 16,
      taskTmp: null,
      showGantt: false,
      // config
      showFilter: false,
      from: '',
      to: '',
      withTask: false,
      work_id: null,
      opts: {
        taskMapping: {
          progress: 'percent'
        },
        maxRows: 100,
        maxHeight: 500,
        title: {
          label: '',
          html: false
        },
        row: {
          height: 24
        },
        calendar: {
          hour: {
            display: true
          }
        },
        chart: {
          progress: {
            bar: false
          },
          expander: {
            display: true
          }
        },
        taskList: {
          expander: {
            straight: false
          },
          columns: [
            {
              id: 1,
              label: 'ID',
              value: 'id',
              width: 40
            },
            {
              id: 2,
              label: 'Description',
              value: task => `<span class="bold link" style="color:${task.style.base.fill}" >${task.label}</span>`,
              width: 200,
              expander: true,
              html: true,
              events: {
                click ({ data, column }) {
                  window.open(`${data.host}preview/${data.source}/${data.source_id}`)
                  // alert('description clicked!\n' + data.label)
                }
              }
            },
            {
              id: 3,
              label: 'Start',
              value: task => dayjs(task.start).format('DD MMM YYYY'),
              width: 85
            },
            {
              id: 5,
              label: '%',
              value: 'progress',
              width: 35,
              style: {
                'task-list-header-label': {
                  'text-align': 'center',
                  width: '100%'
                },
                'task-list-item-value-container': {
                  'text-align': 'center',
                  width: '100%'
                }
              }
            }
          ]
        },
        locale: {
          name: 'en',
          Now: 'Today',
          'X-Scale': '🔍 Calendar',
          'Y-Scale': '🔍 Data',
          'Task list width': 'Task list',
          'Before/After': 'Expand',
          'Display task list': 'Task list'
        },
        times: {
          timeZoom: 21
        }
      },
      shareMode: false,
      project: null,
      show404: false
    }
  },

  created () {
    // this.dataModel.project_id = this.$Helper.hasProjectId(this)
    this.searchWorksEvt = debounce(this.searchWorksEvt, 500)
    var params = this.$route
    if (params.query.sharemode !== undefined) {
      this.shareMode = true
      this.dataModel.project_id = params.params.project_id
    } else this.dataModel.project_id = this.$Helper.hasProjectId(this)
    this.initTopBar()
  },

  mounted () {
    this.onRefresh()
  },

  methods: {

    onRefresh () {
      //
      // this.getListSelect('projects/' + this.dataModel.project_id + '/task-status?limit=0', 'taskStatus')
      // this.getListSelect('projects/' + this.dataModel.project_id, 'project')
      this.getProject()
    },

    initTopBar () {
      this.Meta.topBarMenu = [
        { name: 'Refresh', event: this.onRefresh },
        {
          name: 'Tools',
          sub: [
            { name: 'Filter', event: this.showFilterForm },
            { name: 'Share', event: this.share }
          ]
        }
      ]
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

          if (data.length === 0) this.select.noOptionLabel = 'Data not found'

          setTimeout(() => {
            if (selectSource === 'works') {
              var el = this.$refs.works
              el.focus()
            }
          }, 300)
        }
      })
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

    async filterSelect (val, update, target) {
      this.select = await this.$Helper.filterSelect(val, update, target, this.select)
    },

    searchWorksEvt (val) {
      if (val.length > 1) this.getListSelect('projects/' + this.dataModel.project_id + '/works?limit=0&search=name:' + val + '', 'works')
    },

    showFilterForm () {
      this.showFilter = !this.showFilter
    },

    getProject () {
      this.showGantt = false
      var params = this.$route.params
      if (params.project_id !== undefined) {
        var endpoint = `projects/${params.project_id}`
        this.API.get(endpoint, (status, data, message, response, full) => {
          // this.$Helper.loadingOverlay(false)
          if (status === 200) {
            this.project = data
            this.getGanttData()
          } else this.show404 = true
        })
      } else {
        this.show404 = true
      }
    },

    getGanttData () {
      this.showGantt = false
      // this.$Helper.loadingOverlay(true)
      // var endpoint = 'menus'
      var params = this.$route.params
      var pid = this.dataModel.project_id
      if (params.project_id) pid = params.project_id

      var endpoint = `projects/${pid}/timeline?limit=0`
      if (this.work_id) endpoint = endpoint + '&work_id=' + this.work_id
      if (this.withTask) endpoint = endpoint + '&with_task=true'
      if (this.from) endpoint = endpoint + '&from=' + this.$Helper.createYMD(this.from)
      if (this.to) endpoint = endpoint + '&to=' + this.$Helper.createYMD(this.to)

      this.API.get(endpoint, (status, data, message, response, full) => {
        // this.$Helper.loadingOverlay(false)
        if (status === 200) {
          this.taskTmp = data
          this.tasksUpdate(data)
          this.showGantt = true
        }
      })
    },

    addTask () {
      // this.tasks.push({
      //   id: this.lastId++,
      //   label:
      //     '<a href="https://images.pexels.com/photos/423364/pexels-photo-423364.jpeg?auto=compress&cs=tinysrgb&h=650&w=940" target="_blank" style="color:#0077c0;">Yeaahh! you have added a task bro!</a>',
      //   user:
      //     '<a href="https://images.pexels.com/photos/423364/pexels-photo-423364.jpeg?auto=compress&cs=tinysrgb&h=650&w=940" target="_blank" style="color:#0077c0;">Awesome!</a>',
      //   start: getDate(24 * 3),
      //   duration: 1 * 24 * 60 * 60 * 1000,
      //   percent: 50,
      //   type: 'project'
      // })
    },
    tasksUpdate (tasks) {
      this.tasks = tasks
    },
    optionsUpdate (options) {
      // options.title.label = 'zz'
      this.options = options
    },
    styleUpdate (style) {
      this.dynamicStyle = style
    },

    share () {
      this.$Helper.openLink(`/projects/${this.dataModel.project_id}/timeline?sharemode`)
    }

  }
}
</script>
