<?php 
$host = H_getServerInfo('host');

$data = H_toArrayObject($data);

if (isset($_GET['dd'])) {
  dd($data);
}

if ($data == null) {
  echo "<h2>Report not found</h2>"; 
  exit();
}

$project = $data->project;
$work = $data->work;
$tasks = $data->tasks;
$from = $data->from;
$to = $data->to;

$title = $project->name ;
if ($work) $title .=  ' - ' .$work->name;

if ($from != null && $to != null) $title .=  ' | Tasks Periode ' .H_formatDate($from, 'd F Y'). ' - ' .H_formatDate($to, 'd F Y');
elseif ($from != null) $title .=  ' | Tasks From ' .H_formatDate($from, 'd F Y');

?>

<html>

  <head>
    <title>{{$title }}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

    <script src="/assets/jquery.js" type="text/javascript"></script>
    <script src="/assets/export.js" type="text/javascript"></script>
    <style>
      .head { background-color: #0089ca; color: #fff;} .head th {padding:10px;} .caption { font-size: 16pt; font-weight:bold;}
      .body td { border: 1px solid; padding:6px;} .right {text-align: right}  .center {text-align: center}
    </style>
  </head>

  <body>
    <div style="padding:20px">
      <button type="button" class="btn btn-success" onclick="tableToExcel('testTable', '{{ $title }}')" >Export to Excel</button>
      <table id="testTable" summary="Code page support in different versions of MS Windows."
              rules="groups"  border="1">
          <caption class="caption">
            {{$title}}
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
            foreach ($tasks as $key => $task) {
              $totalManday = 0;
              if (count($tasks) > 0) {
                $totalManday = H_sumArrayObjectBy($tasks, 'mandays');
              }
            ?>
              <tr class="body">
                  <td>{{$no}}</td>
                  <td>{{$task->name}}</td>
                  <td style="font-size:10pt;">
                    <div>{{ $task->description }}</div>
                  </td>
                  <td>
                    @if($task->start_date)
                    <span>{{H_formatDate($task->start_date,'d F Y - H:i')}}</span>
                    @endif  
                  </td>
                  <td>
                    @if($task->end_date)
                    <span>{{H_formatDate($task->end_date,'d F Y - H:i')}}</span>
                    @endif
                  </td>
                  <td class="right">{{$task->mandays}}</td>
                  <td class="center">
                    <?php 
                      $total = count($task->assignees); 
                      $no = 1;
                      foreach ($task->assignees as $key => $user) {
                        echo strtoupper($user->name);
                        if ($no != $total) echo ", ";
                      $no++;}
                    ?>
                  </td>
              </tr>
            <?php $no++;} ?>
          </tbody>
      </table>
      <button style="margin-top:20px;" type="button" class="btn btn-success" onclick="tableToExcel('testTable', '{{ $title }}')" >Export to Excel</button>
    <div>
  </body>

</html>