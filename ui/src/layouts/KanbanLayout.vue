<template>
  <transition  appear mode='out-in'
    enter-active-class='animated-page fadeIn '
    leave-active-class='animated-page fadeOut'
  >
    <router-view class=''></router-view>
  </transition>
</template>

<script>
import { Config } from '../config'

export default {
  name: 'UserPageLayout',
  data () {
    return {
      thumbStyle: Config.scrollOptions('thumb'),
      barStyle: Config.scrollOptions('bar'),
      drawer: false,
      fabPos: [18, 18],
      draggingFab: false
    }
  },
  created () {
    var element = document.querySelector('#q-app')
    element.style.position = 'fixed'
    element.style.display = 'flex'
    element.style.height = '100%'
  },

  beforeDestroy () {
    //
    var element = document.querySelector('#q-app')
    element.removeAttribute('style')
  },

  methods: {
    drawerCtrl () {
      this.drawer = !this.drawer
      console.log('drawerCtrl: ', this.drawer)
    }
  }
}
</script>
