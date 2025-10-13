<?php 
$host = H_getServerInfo('host');


$project = H_toArrayObject($data['project']);
$work = H_toArrayObject($data);
// dd($works->tasks);

if (isset($_GET['dd'])) {
  dd($project, $work);
}

if ($data == null) {
  echo "<h2>Propose not found</h2>"; 
  exit();
}

?>

<html>

  <head>
    <title>{{$project->name}} - {{$work->name}}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

    {{-- <script src="https://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script> --}}
    <script src="/assets/jquery.js" type="text/javascript"></script>
    <script src="/assets/export.js" type="text/javascript"></script>
    <style>
      .head { background-color: #0089ca; color: #fff;} .head th {padding:10px;}
      .body td { border: 1px solid; padding:6px;} .right {text-align: right}  .center {text-align: center}
    </style>
  </head>

  <body>
    <form style="margin-top:20px" action="/report/works/{{$work->id}}">
      <input type="date" name="from">
      <input type="date" name="to">
      <button>Load Data</button>
      <input type="button" onclick="tableToExcel('testTable', 'W3C Example Table')" value="Export to Excel" />
    </form>
    
    <table id="testTable" summary="Code page support in different versions of MS Windows."
            rules="groups" frame="hsides" border="1">
        <caption>
          {{$project->name}} - {{$work->name}}
        </caption>
        <colgroup align="center"></colgroup>
        <colgroup align="left"></colgroup>
        <colgroup align="center"></colgroup>
        <colgroup align="center"></colgroup>
        <colgroup align="center"></colgroup>
        <colgroup align="center"></colgroup>
        <thead valign="top">
          <tr class="head">
            <th class="center" width="3%" >No</th>
            <th class="center" width="15%">Module</th>
            <th class="center">Description</th>
            <th class="center" width="10%">Start</th>
            <th class="center" width="10%">End</th>
            <th class="center">Mandays</th>
            <th class="center">PIC</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1;
          foreach ($work->task_report as $key => $task) {
            $totalManday = 0;
            if (count($work->tasks) > 0) {
              $totalManday = H_sumArrayObjectBy($work->tasks, 'mandays');
            }
          ?>
            <tr class="body">
                <td style="vertical-align:middle">{{$no}}</td>
                <td style="vertical-align:middle">{{$task->name}}</td>
                <td style="font-size:10pt; vertical-align:middle">
                  <div>{!! $task->description !!}</div>
                </td>
                <td style="vertical-align:middle">
                  @if($task->start_date)
                  <span>{{H_formatDate($task->start_date,'d F Y - H:i')}}</span>
                  @endif  
                </td>
                <td style="vertical-align:middle">
                  @if($task->end_date)
                  <span>{{H_formatDate($task->end_date,'d F Y - H:i')}}</span>
                  @endif
                </td>
                <td style="vertical-align:middle" class="right">{{$task->mandays}}</td>
                <td style="vertical-align:middle" class="center">
                  <?php 
                    if(isset($task->assignees)) {
                      $total = count($task->assignees); 
                      $no = 1;
                      foreach ($task->assignees as $key => $user) {
                        echo strtoupper($user->name);
                        if ($no != $total) echo ",";
                      $no++;}
                    }
                  ?>
                </td>
            </tr>
          <?php $no++;} ?>
        </tbody>
    </table>

  </body>

</html>