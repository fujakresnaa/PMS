<?php 
$host = H_getServerInfo('host');

$project = H_toArrayObject($data['project']);
$works = H_toArrayObject($data['works']);

if (isset($_GET['dd'])) {
  dd($project, $works);
}

if ($data == null) {
  echo "<h2>Propose not found</h2>"; 
  exit();
}

?>

<html>

  <head>
    <title>{{$project->name}} - {{$works->name}}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

    <script src="/assets/jquery.js" type="text/javascript"></script>
    <script src="/assets/export.js" type="text/javascript"></script>

    <style>
      .text-primary { color:#0089ca }
    </style>

  </head>

  <body>
    <div id="word-area" class="container" style='font-family: "Arial", Helvetica,sans-serif;'>
      <h2><strong>Lampiran Modul</strong></h2>

      {{-- modul list --}}
      <div class="col-md-12">
        <?php 
        $no = 1;
        foreach ($works->children as $key => $work) {
          $totalManday = 0;
          if (count($work->tasks) > 0) {
            $totalManday = H_sumArrayObjectBy($work->tasks, 'mandays');
          }
        ?>
          <h3 class="text-primary"><strong>{{$work->name}}</strong></h3>
          <p>
            {{$work->summary}}
          </p>
          <p>
            {{-- {!!$work->description !!} --}}
          </p>
        <?php $no++;} ?>

      </div>

      {{-- Price List --}}
      <?php 
      $styleHead = 'text-align:center; padding:10px 5px 10px 5px;color: #fff;background-color: #0089ca;';
      $styleData = 'padding:10px 5px 10px 5px;';
      ?>
      <h2><strong>Price List</strong></h2>
      <table id="tblData" cellpadding="0" cellspacing="0" border="1" style="width:100%">
        <thead>
          <tr>
            <th style="{{$styleHead}}"  width="5%" >No</th>
            <th style="{{$styleHead}}" >Module</th>
            <th style="{{$styleHead}}" >Qty</th>
            <th style="{{$styleHead}}" >Unit</th>
            <th style="{{$styleHead}}" >Price</th>
            <th style="{{$styleHead}}" >Amount</th>
        </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1;
          foreach ($works->children as $key => $work) {
          ?>
            <tr>
                <td style="{{$styleData}} ">{{$no}}</td>
                <td style="{{$styleData}} ">{{$work->name}}</td>
                <td style="{{$styleData}} text-align:right;">1</td>
                <td style="{{$styleData}} text-align:center;">PACKAGE</td>
                <td style="{{$styleData}} text-align:right;">0</td>
                <td style="{{$styleData}} text-align:right;">0</td>

            </tr>
          <?php $no++;} ?>
        </tbody>
      </table>


    </div>
    <div class="content-footer">
        <button id="btn-export" onclick="exportWord('tet', 'word-area');">Export to
            word doc</button>
    </div>
 
  </body>

</html>