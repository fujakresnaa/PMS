
<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row pl-2 pt-2">
        <div class="col-12 col-sm-3 col-md-7 pb-1 pv info-page">
          <div class="title">
            <span class="text-caption text-grey-8">Detail</span><br>
            <span class="text-h5 bold text-primary capital">{{Meta.name}}</span>
          </div>
          <q-btn @click="back" unelevated color="warning" class="capital" icon="arrow_back" label="Back" />&nbsp;
          <q-btn @click="edit" unelevated color="green" class="capital" icon="edit" label="Edit" />
        </div>
      </div>

      <div class="row mv-2">
        <div class="col-12 col-md-6">
          <q-card class="box-shadow full-height">
            <q-card-section>
              <q-list separator>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Title</q-item-label>
                    <q-item-section> {{ dataModel.title }} </q-item-section>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Description</q-item-label>
                    <q-item-section> {{ dataModel.description }} </q-item-section>
                  </q-item-section>
                </q-item>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Log Info</q-item-label>
                    <q-item-section>
                      <log-info :data="dataModel" />
                    </q-item-section>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-12 col-md-6">
          <q-card class="box-shadow full-height">
            <q-card-section>
              <q-list separator>
                <q-item>
                  <q-item-section>
                    <q-item-label caption>Attachment File</q-item-label>
                    <q-item-section> - </q-item-section>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card-section>
          </q-card>
        </div>
      </div>

  </div>
</template>

<script>
import Meta from './meta'

export default {
  name: 'Activities',
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
    this.initialize()
  },

  mounted () {

  },

  methods: {

    callbackForm (params = null) {
      this.onRefresh()
    },

    checkPermission (mode = 'create') {
      var access = this.$ModuleConfig.checkPermission(this.$router, this.Meta.module + '-' + mode)
      if (access) return true
      else this.$router.push({ name: '403' })
    },

    initialize () {
      var params = this.$route.params
      if (this.$Helper.checkParams(params)) { // checking access update
        if (this.checkPermission('read')) {
          if (params.id !== undefined) this.getData(params.id)
          else this.backToRoot()
        }
      } else this.backToRoot()
    },

    onRefresh () {
      this.initialize()
    },

    initTopBar () {
      this.Meta.topBarMenu = [{ name: 'Refresh', event: this.onRefresh }]
    },

    getData (id) {
      console.log('getData')
      this.$Helper.loading()
      var endpoint = this.Meta.module + '/' + id
      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loading(false)
        if (status === 200) {
          // inject data
          this.dataModel = data
        }
      })
    },

    edit () {
      this.$router.push('/activities/form/' + this.dataModel.id + '?reference=' + this.dataModel.reference + '&reference_id=' + this.dataModel.reference_id)
    },

    back () {
      var query = this.dataModel
      console.log(query)
      if (query !== null) {
        this.$router.push('/' + query.reference + '/view/' + query.reference_id)
      } else {
        this.backToRoot()
      }
    },

    backToRoot () {
      this.$router.push({ name: this.Meta.module })
    }
  }
}
</script>
