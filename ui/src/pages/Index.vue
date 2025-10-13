<template >

  <div class="root">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta" :disableDrawer="true" v-bind:topBarMenu="Meta.topBarMenu"  />

    <div class="row">
      <div class="col-12 text-center pt-5 img-responsive" >
        <q-img src="assets/icon.png" style="max-width:62px" > </q-img>
      </div>

      <div class="col-12 text-center pt-5">
        <span class="text-h4 bold">Welcome to Project Management System</span> <br>
        <span class="text-grey-8">Lets start manage something!</span>
      </div>

      <div class="col-12 col-sm-3 offset-sm-3 text-center pt-5 pv-1">
        <q-card @click="goToLink('projects')" class=" link hover">
          <q-card-actions align="center" class="ph-5">
            <q-icon name="developer_board" size="82px" color="primary"/>
          </q-card-actions>

          <q-card-section class="bg-primary text-white">
            <div class="text-h6">Projects</div>
            <div class="text-subtitle2">manage projects</div>
          </q-card-section>

        </q-card>
      </div>

      <div class="col-12 col-sm-3 offset-sm-1 text-center pt-5 pv-1">
        <q-card @click="goToLink('users')" class=" link hover">
          <q-card-actions align="center" class="ph-5">
            <q-icon name="people_alt" size="82px" color="primary"/>
          </q-card-actions>

          <q-card-section class="bg-primary text-white">
            <div class="text-h6">Users</div>
            <div class="text-subtitle2">manage users</div>
          </q-card-section>

        </q-card>
      </div>

    </div>

  </div>
</template>

<script>

export default {
  name: 'Index',
  data () {
    return {
      API: this.$Api,
      Meta: {
        name: 'Dashboard',
        icon: 'stop_circle',
        module: 'users',
        topBarMenu: []
      },
      mining: {
        percentage: 69
      }
    }
  },

  created () {
    this.Meta.topBarMenu = [{ name: 'Projects', event: this.gotToProject }]
    this.checkSession()
  },

  mounted () {

  },

  methods: {

    gotToProject () {
      this.goToLink('projects')
    },

    goToLink (path) {
      this.$router.push({ name: path })
    },

    checkSession (id) { // untuk check session
      this.$Helper.loadingOverlay(true, 'Loading Dashboard...')
      var endpoint = '/me'
      this.API.get(endpoint, (status, data, message, response, full) => {
        this.$Helper.loadingOverlay(false)
      })
    }
  }
}
</script>
