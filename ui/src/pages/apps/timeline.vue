
<template >

  <div class="root bg-soft">

    <!-- drawer di init di: boot/extend-component.js -->
    <drawer v-bind:topBarInfo="Meta"  v-bind:topBarMenu="Meta.topBarMenu"  />

      <!-- Header Title -->
      <div class="row pl-2 pt-2" >

        <div class="panel-left" >
          <table style="width:100%" border="1">
            <thead>
               <tr>
                  <th rowspan="3">Name</th>
                  <th rowspan="3">Day</th>
                  <th >.</th>
                </tr>
                <tr>
                  <th rowspan="3">.</th>
                </tr>
            </thead>
             <tbody>
              <tr>
                <td rowspan="2" >Project 2</td>
                <td rowspan="3" >31</td>
                <th >.</th>
              </tr>
            </tbody>
            <tbody>
              <tr>
                <td rowspan="2" >Project 2</td>
                <td rowspan="3" >31</td>
                <th >.</th>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="spliter">--</div>
        <div class="panel-right" >
          <div style="width:100%">
            <table class="tbl-tl" >
              <thead>
                <tr>
                  <th :colspan="day">January</th>
                  <th :colspan="day">February</th>
                  <th :colspan="day">March</th>
                </tr>
                <tr>
                  <th v-for="nm in day" :key="'d'+nm" style="width:59px;">{{nm}}</th>
                  <th v-for="nm in day" :key="'d1'+nm" style="width:59px;">{{nm}}</th>
                  <th v-for="nm in day" :key="'d2'+nm" style="width:59px;">{{nm}}</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td :colspan="day"><div class="bg-primary">.</div></td>
                  <td :colspan="day"><div class="bg-primary">.</div></td>
                  <td :colspan="day"><div class="bg-primary">.</div></td>
                </tr>
              </tbody>
              <tbody>
                <tr>
                  <td :colspan="day"><div class="bg-red">.</div></td>
                  <td :colspan="day"><div class="bg-red">.</div></td>
                  <td :colspan="day - 4"><div class="bg-red">.</div></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="chart-wrapper col-12">
            <ul class="chart-values">
                <li>sun</li>
                <li>mon</li>
                <li>tue</li>
                <li>wed</li>
                <li>thu</li>
                <li>fri</li>
                <li>sat</li>
            </ul>
            <ul class="chart-bars">
                <li data-duration="tue½-wed" data-color="#b03532">Task</li>
                <li data-duration="wed-sat" data-color="#33a8a5">Task</li>
                <li data-duration="sun-tue" data-color="#30997a">Task</li>
                <li data-duration="tue½-thu" data-color="#6a478f">Task</li>
                <li data-duration="mon-tue½" data-color="#da6f2b">Task</li>
                <li data-duration="wed-wed" data-color="#3d8bb1">Task</li>
                <li data-duration="thu-fri½" data-color="#e03f3f">Task</li>
                <li data-duration="mon½-wed½" data-color="#59a627">Task</li>
                <li data-duration="fri-sat" data-color="#4464a1">Task</li>
            </ul>
        </div>
      </div>

  </div>
</template>

<style>
  .tbl-tl {
    width:100vw;
    table-layout: auto;
  }

  .tbl-tl,
  .tbl-tl tbody td,
  .tbl-tl thead th {
    border-collapse: collapse;
  }

  .tbl-tl thead th {
    width:30px;
    padding: 2px;
    border: solid 1px;
    text-align: center;
  }
  .tbl-tl tbody td {
    border: solid 1px #ccc;
    text-align: center;
  }

  .panel-left {
    width:200px;
    background:#0089ca;
    min-height:400px;
  }
  .panel-right {
    flex: 4 4 0%;
    background:#ccc;
    min-height:400px;
    overflow-x: scroll;
    position: relative;
    display: flex;
  }

  ul {
    list-style: none;
  }

  a {
    text-decoration: none;
    color: inherit;
  }

  body {
    background: var(--body);
    font-size: 16px;
    font-family: sans-serif;
    padding-top: 40px;
  }

  .chart-wrapper {
    max-width: 1150px;
    padding: 0 10px;
    margin: 0 auto;
  }

  /* CHART-VALUES
  –––––––––––––––––––––––––––––––––––––––––––––––––– */
  .chart-wrapper .chart-values {
    position: relative;
    display: flex;
    margin-bottom: 20px;
    font-weight: bold;
    font-size: 1.2rem;
  }

  .chart-wrapper .chart-values li {
    flex: 1;
    min-width: 80px;
    text-align: center;
  }

  .chart-wrapper .chart-values li:not(:last-child) {
    position: relative;
  }

  .chart-wrapper .chart-values li:not(:last-child)::before {
    content: '';
    position: absolute;
    right: 0;
    height: 510px;
    border-right: 1px solid var(--divider);
  }

  /* CHART-BARS
  –––––––––––––––––––––––––––––––––––––––––––––––––– */
  .chart-wrapper .chart-bars li {
    position: relative;
    color: var(--white);
    margin-bottom: 15px;
    font-size: 16px;
    border-radius: 20px;
    padding: 10px 20px;
    width: 0;
    opacity: 0;
    transition: all 0.65s linear 0.2s;
  }

  @media screen and (max-width: 600px) {
    .chart-wrapper .chart-bars li {
      padding: 10px;
    }
  }
</style>

<script>

export default {
  name: 'Timeline',
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
