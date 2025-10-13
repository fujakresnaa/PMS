
<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row pl-2 pt-2" >

        <div class="col-12 pv-1 ph-1">
          <div class="text-h4 text-primary bold">Roadmaps</div>
          <div class="text-subtitle2 pt">
            From {{$Helper.toDate(from, 'DD MMMM YYYY')}}
            <span v-if="to">
            until {{$Helper.toDate(to, 'DD MMMM YYYY')}}
            </span>
            <span> until the end of the year</span>
          </div>
        </div>

        <!-- Form -->
        <div v-if="showFilter" class="animated fadeIn col-12 col-sm-4 pv-1 ph-1">
          <q-input @click="$refs.popupTanggal.show()"  label="From" filled v-model="from" dense square mask="date" >
            <div>
              <q-popup-proxy ref="popupTanggal" transition-show="scale" transition-hide="scale">
                <q-date v-model="from" @input="() => $refs.popupTanggal.hide()" ></q-date>
              </q-popup-proxy>
            </div>
          </q-input>
        </div>

        <div v-if="showFilter" class="animated fadeIn col-12 col-sm-4 pv-1 ph-1">
          <q-input @click="$refs.popupTanggal1.show()" label="To" filled v-model="to" dense square mask="date" placeholder="empty to set default until end of year">
            <div>
              <q-popup-proxy ref="popupTanggal1" transition-show="scale" transition-hide="scale">
                <q-date v-model="to" @input="() => $refs.popupTanggal1.hide()" ></q-date>
              </q-popup-proxy>
            </div>
          </q-input>
        </div>

        <div v-if="showFilter" class="animated fadeIn col-12 col-sm-4 pv-1 ph-1">
          <q-btn @click="onRefresh" unelevated color="primary" class="capital" label="Load Data" />
          <q-btn v-if="to !== ''" @click="to = ''" unelevated color="primary" outline class="ml capital" label="to Last Year" />
          <q-btn @click="showFilter = false" unelevated color="red" flat class="ml bg-red-1 capital" label="Hide Filter" />
        </div>

        <q-separator class="mh-2 mv" />

        <div class="col-12"
          v-for="(group, i) in this.select.works" :key="'rm'+i"
        >
          <div class="ph pv col-12 row grid-style-transition animated slideInUp" >
            <div class="col-12 col-sm-1 ">
              <div class="text-center box-tgl bg-indigo-1 bold text-indigo">
                {{$Helper.toDate(i, 'MMM')}} <br>
                <small class="text-indigo-3">{{$Helper.toDate(i, 'YYYY')}}</small>
              </div>
            </div>
            <div class="col-12 col-sm-9 pl-1">
              <q-tree
                default-expand-all
                :nodes="group"
                node-key="label"
              >
                <template v-slot:default-header="prop">
                  <div class="row items-center">
                    <q-icon :name="$Config.iconStatusWork(prop.node.status)" :color="$Config.colorStatusWork(prop.node.status)" size="20px" class="q-mr-sm" >
                      <q-tooltip>Status : {{prop.node.status}} </q-tooltip>
                    </q-icon>
                    <q-icon name="verified" color="green" v-if="prop.node.is_done" />
                    <div class="text-weight-bold text-dark text-h5">
                      {{ prop.node.name }}
                      <q-btn size="xs" dense :label="prop.node.priority" unelevated outline rounded :color="$Config.colorPriority(prop.node.priority)" class="pv mv mh bold capital" >
                        <q-tooltip>Priority: {{prop.node.priority}}</q-tooltip>
                      </q-btn>
                      <q-btn @click="openWork(prop.node)" size="xs" dense label="View" color="blue" unelevated class="pv mv mh bold capital" />
                    </div>
                  </div>
                </template>

                <template v-slot:default-body="prop">
                  <div>
                    <div class="text-grey-8" v-if="prop.node.start_date">
                      <q-icon name="date_range" color="grey-5" size="16px" />
                       <span class="ml mr text-green-9">{{$Helper.toDate(prop.node.start_date, 'DD MMM YYYY - HH:mm')}} </span>|
                       <span class="ml text-red-9">{{$Helper.toDate(prop.node.end_date, 'DD MMM YYYY - HH:mm')}} </span>
                      <span class="bold text-indigo" v-if="prop.node.total_day !== undefined">
                         <span v-if="prop.node.total_day > 0"> ({{prop.node.total_day}} Day) </span>
                      </span>
                      <br>
                    </div>
                    <div class="text-grey-8" v-if="prop.node.total_mandays !== undefined">
                      <q-icon name="group" color="grey-5" size="16px" /> Mandays: {{prop.node.total_mandays}}
                    </div>
                    <q-expansion-item v-if="prop.node.tasks.length > 0"
                      expand-separator
                      icon="app_registration"
                      label="Task"
                      :caption="prop.node.tasks.length +' Task in this work'"
                    >
                      <q-card flat bordered class="mb-1" v-for="(task, i) in prop.node.tasks" :key="'ts'+i">
                        <q-card-section class="text-dark bold">
                          {{task.name}}
                          <q-btn size="xs" dense :label="task.status_name" unelevated outline rounded color="indigo" class="pv mv mh bold capital" >
                            <q-tooltip>Status: {{task.status_name}}</q-tooltip>
                          </q-btn>
                          <q-btn @click="openTask(task)" size="xs" dense label="View" color="blue" unelevated class="pv mv mh bold capital" />
                          <br>
                          <q-icon name="date_range" color="grey-5" size="16px" />
                          <small>
                            {{$Helper.toDate(task.start_date, 'DD MMM YYYY - HH:mm')}} -
                            {{$Helper.toDate(task.end_date, 'DD MMM YYYY - HH:mm')}}
                          </small>
                        </q-card-section>
                        <q-card-section class="text-dark bold">
                          <q-chip  size="xs" square v-for="(label, i) in task.task_labels" :key="i+'tl'">
                            <q-avatar icon="bookmark" :style="'background:' + label.color" text-color="white" />
                            {{label.name}}
                          </q-chip>
                          <q-separator class="mh" />
                          <q-chip size="sm" v-for="(user, i) in task.task_assignees" :key="i+'us'">
                            <q-avatar :color="$Helper.randomColor()" text-color="white" >
                              <span class="uppercase">{{$Helper.getFirstChar(user.name)}}</span>
                            </q-avatar>
                            {{user.name}}
                          </q-chip>
                        </q-card-section>
                      </q-card>
                    </q-expansion-item>

                  </div>
                </template>
              </q-tree>
            </div>
          </div>
        </div>

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
  name: 'Roadmaps',
  data () {
    return {
      Meta: {
        name: 'Roadmaps',
        icon: 'list_alt',
        module: 'roadmaps',
        topBarMenu: []
      },
      API: this.$Api,
      // default data
      title: 'Roadmaps',
      from: '',
      to: '',
      dataModel: {},
      select: {
        works: [],
        worksTmp: []
      },
      showFilter: false

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
      var filter = `?from=${this.from}`
      if (this.to !== '') filter = `${filter}&to=${this.to}`
      this.getListSelect(`projects/${this.dataModel.project_id}/roadmaps${filter}`, 'works')
    },

    initTopBar () {
      this.Meta.topBarMenu = [
        { name: 'Refresh', event: this.onRefresh },
        { name: 'Filter', event: this.showFilterForm }
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
        }
      })
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
