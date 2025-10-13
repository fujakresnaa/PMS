
<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row mv-2 mt-2">

        <div class="col-12">
          <q-card class="box-shadow mt-2">
            <q-card-section class="row">
              <div class="col-12 col-sm-7 col-md-9">
                <div class="text-h5 bold pt-1 pb-1">
                  {{dataModel.name}}

                  <q-btn size="sm" :label="dataModel.status" unelevated rounded class="mv mh capital"
                  :color="$Config.colorStatusSprints(dataModel.status)" :icon="$Config.iconStatusSprints(dataModel.status)" >
                    <q-tooltip>Status: {{dataModel.status}}</q-tooltip>
                  </q-btn>
                </div>
              </div>

              <div class="col-12 col-sm-4 col-md-3 right">
                <q-btn-dropdown size="sm" unelevated
                  label="Action"
                  color="primary"
                >
                  <q-list>
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
                        <q-item-label>Start Sprint</q-item-label>
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
                  </q-list>

                </q-btn-dropdown>
              </div>

            </q-card-section>
            <q-card-section class="">
              <div class="mh-2">
                <q-markup-table flat :separator="'cell'">
                  <thead>
                    <tr>
                      <th class="text-left">Type</th>
                      <th class="text-left">Plan</th>
                      <th class="text-left">Actual</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-left">Start</td>
                      <td class="text-left">{{$Helper.toDate(dataModel.start_date, 'DD MMM YYYY - HH:mm')}}</td>
                      <td class="text-left"><span v-if="dataModel.actual_start_date">{{$Helper.toDate(dataModel.actual_start_date, 'DD MMM YYYY - HH:mm')}}</span></td>
                    </tr>
                    <tr>
                      <td class="text-left">End</td>
                      <td class="text-left">{{$Helper.toDate(dataModel.end_date, 'DD MMM YYYY - HH:mm')}}</td>
                      <td class="text-left"><span v-if="dataModel.actual_end_date">{{$Helper.toDate(dataModel.actual_end_date, 'DD MMM YYYY - HH:mm')}}</span></td>
                    </tr>
                  </tbody>
                </q-markup-table>
                <q-separator />
              </div>
              <div class="mh-2" ><span class="bold text-h5 text-dark">Goals</span></div>
              <div class="mb-2 quote"> {{dataModel.goals}} </div>
            </q-card-section>
          </q-card >
        </div>

      </div>

  </div>
</template>

<script>
import Meta from './meta'

export default {
  name: 'Sprints',
  data () {
    return {
      Meta,
      API: this.$Api,
      // default data
      dataModel: this.$Helper.unReactive(Meta.model),
      rules: {
        permission: Meta.permission
      }
    }
  },

  created () {
    this.initTopBar()
  },

  mounted () {
    this.onRefresh()
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
        }
      })
    },

    edit () {
      this.$router.push({ name: 'edit-' + this.Meta.module, params: this.dataModel })
    },

    backToRoot () {
      this.$router.push({ name: this.Meta.module })
    },

    detailWork (data) {
      this.$router.push({ name: 'view-works', params: data.works })
    },

    completed () {
      var that = this
      this.$q.dialog({
        title: 'Set Completed Sprints',
        message: 'Are you sure want to set Completed this Sprint ?',
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
      this.API.put('sprints' + '/' + this.dataModel.id + '/' + type, this.dataModel, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
        if (status === 200) {
          var msg = 'Sprint "' + this.dataModel.name + '" has ' + type
          this.$Helper.showAlert('Sprint ' + type, msg)
          this.getData(this.dataModel.id)
        }
      })
    }
  }
}
</script>
