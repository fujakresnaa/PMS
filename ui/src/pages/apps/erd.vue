
<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row pl-2 pt-2" >
        <div id="sample text-dark">
            <div id="myDiagramDiv" style="background-color: white; border: solid 1px black; width: 100%; height: 700px"></div>
        </div>
      </div>

  </div>
</template>

<script>
// import go from 'gojs'

export default {
  name: 'ERD',
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

    // const $ = go.GraphObject.make // for conciseness in defining templates
    // const myDiagram = $(go.Diagram, 'myDiagramDiv', // create a Diagram for the DIV HTML element
    //   { // enable undo & redo
    //     'undoManager.isEnabled': true
    //   })

    // // define a simple Node template
    // myDiagram.nodeTemplate =
    //   $(go.Node, 'Auto', // the Shape will go around the TextBlock
    //     $(go.Shape, 'RoundedRectangle',
    //       { strokeWidth: 0, fill: 'white' }, // default fill is white
    //       // Shape.fill is bound to Node.data.color
    //       new go.Binding('fill', 'color')),
    //     $(go.TextBlock,
    //       { margin: 8 }, // some room around the text
    //       // TextBlock.text is bound to Node.data.key
    //       new go.Binding('text', 'key'))
    //   )

    // // but use the default Link template, by not setting Diagram.linkTemplate

    // // create the model data that will be represented by Nodes and Links
    // myDiagram.model = new go.GraphLinksModel(
    //   [
    //     { key: 'Alpha', color: 'lightblue' },
    //     { key: 'Beta', color: 'orange' },
    //     { key: 'Gamma', color: 'lightgreen' },
    //     { key: 'Delta', color: 'pink' }
    //   ],
    //   [
    //     { from: 'Alpha', to: 'Beta' },
    //     { from: 'Alpha', to: 'Gamma' },
    //     { from: 'Beta', to: 'Beta' },
    //     { from: 'Gamma', to: 'Delta' },
    //     { from: 'Delta', to: 'Alpha' }
    //   ]
    // )

    // console.log('myDiagram', myDiagram)
  },

  methods: {

    onRefresh () {
      //
      // this.getListSelect('projects/' + this.dataModel.project_id + '/task-status?limit=0', 'taskStatus')
    //   this.getListSelect('projects/' + this.dataModel.project_id + '/board', 'tasks')
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
    }

  }
}
</script>
