
<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row mv-2">

        <div class="col-12 mt-2">

          <q-btn @click="edit" icon="edit" unelevated color="primary" label="Edit" class="ml capital"/>

          <q-card class="box-shadow mt-2">
            <q-card-section class="text-dark">
              <div class="text-h4 bold pt-1 pb-1">
                {{dataModel.name}}
                <q-btn size="sm" :label="dataModel.type" unelevated rounded class="mv mh capital"
                :color="$Config.colorComponent(dataModel.type)" :icon="$Config.iconComponent(dataModel.type)" >
                  <q-tooltip>Type: {{dataModel.type}}</q-tooltip>
                </q-btn>
              </div>
              <q-separator />
              <div class="bold text-h6 pt-1 text-grey-6" v-if="dataModel.tasks.works">
                From Works :  <q-btn flat size="md" outline color="primary" @click="detailWorks(dataModel)" class="bold capital text-primary link" >{{dataModel.tasks.works.name}}</q-btn>
              </div>
              <q-separator />
              <div class="bold text-h6 pt-1 text-grey-6" v-if="dataModel.tasks">
                From Task :  <q-btn flat size="md" outline color="primary" @click="detailTask(dataModel)" class="bold capital text-primary link" >{{dataModel.tasks.name}}</q-btn>
              </div>
            </q-card-section>
            <q-card-section class="">
              <div class="mb-2 quote"> {{dataModel.summary}} </div>

              <q-separator class="mt-2" />
              <div class="mh-2" ><span class="bold text-h5 text-dark">Description</span></div>
              <div v-if="dataModel.type === 'SQL'" class="mh-2 sql" v-html="formatSql(dataModel.description)"></div>
              <div v-else v-html="dataModel.description"></div>
            </q-card-section>

          </q-card >
        </div>

      </div>

  </div>
</template>

<script>
import Meta from './meta'

export default {
  name: 'TaskComponents',
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

    detailTask (data) {
      this.$router.push({ name: 'view-tasks', params: data.tasks })
    },

    detailWorks (data) {
      this.$router.push({ name: 'view-works', params: data.tasks.works })
    },

    formatSql (data) {
      var fix = data
      // fix.replace('CREATE TABLE', '<span class="sl-red">CREATE TABLE</span>')
      // console.log(data, fix)
      return fix
    }
  }
}
</script>
