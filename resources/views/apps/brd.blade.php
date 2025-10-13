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
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css"> --}}

    <script src="/assets/jquery.js" type="text/javascript"></script>
    <script src="/assets/export.js" type="text/javascript"></script>

    <style>
      @import url('https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
      body {
        font-family: 'Inter', sans-serif;
      }

      .text-primary { color:#0089ca }
      @page { size: 21cm 29.7cm; margin: 2cm }
      @media print {
        .noprint { display: none !important;}
        #word-area {
          page-break-after: always;
          padding-top:50px;
          padding-bottom:20px;
        }

        .pb {
          page-break-before: always;
        }

      }
    </style>

  </head>

  <body>
    <div id="word-area" class="container" >
      <h1><strong>Business Require Documentation</strong></h1>

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
          <h2 class="text-primary"><strong>{{$work->name}}</strong></h2>
          <p>
            {{$work->summary}}
          </p>
          <p>
            {!!$work->description !!}
          </p>
          <br style="page-break-before: always">
        <?php $no++;} ?>

      </div>

    </div>
    <div class="content-footer">
        <button id="btn-export" onclick="exportWord('tet', 'word-area');">Export to
            word doc</button>
    </div>
 
  </body>

</html>