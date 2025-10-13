<?php 
$host = H_getServerInfo('host');

if (isset($_GET['dd'])) {
  dd( H_toArrayObject($data));
}

if ($data == null) {
  echo "<h2>Task not found</h2>"; 
  exit();
}

$data = H_toArrayObject($data);
?>

<html>

  <head>
    <title>{{$data->name}} | {{ ($data->projects) ? $data->projects->name : '' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>    

    <script src="/assets/jquery.js" type="text/javascript"></script>
    <script src="/assets/export.js" type="text/javascript"></script>

    <style>
      @import url('https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
      body {
        font-family: 'Inter', sans-serif;
      }

      .text-primary { color:#0089ca }
      @page { size: 21cm 29.7cm; margin: 2cm }

    </style>

  </head>

  <body>  
    <div class="col-lg-8 mx-auto p-2 py-md-5">
      <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
        <strong class="fs-2">{{ ($data->projects) ? $data->projects->name : '' }} &nbsp; </strong>
      </header>

      <main>

        <div class="mb-3">
          From Work : <a class="badge bg-success text-wrap btn" target="_blank" href="/preview/works/{{ $data->work_id }}">{{ ($data->works) ? $data->works->name : '' }}</a>
        </div>

        <div class="row g-5">
          <div class="col-md-4 text-center">
            <h5 class=" text-dark"><b>Plan</b></h5>
            <ul class="icon-list">
              <li class="text-muted">▶  : {{ ($data->start_date) ? H_formatDate($data->start_date, 'd F Y - H:i') : '-'}} </li>
              <li class="text-muted">🤚 :  {{ ($data->end_date) ? H_formatDate($data->end_date, 'd F Y - H:i') : '-'}}</li>
            </ul>
          </div>

          <div class="col-md-4 text-center">
            <h5 class=" text-dark"><b>Actual</b></h5>
            <ul class="icon-list">
              <li class="text-muted">▶  : {{ ($data->actual_start_date) ? H_formatDate($data->actual_start_date, 'd F Y - H:i') : '?'}} </li>
              <li class="text-muted">🤚 :  {{ ($data->actual_end_date) ? H_formatDate($data->actual_end_date, 'd F Y - H:i') : '?'}}</li>
            </ul>
          </div>

          <div class="col-md-4 text-center">
            <h5 class=" text-dark"><b>Status</b></h5>
            <button type="button" class="btn btn-primary">
              {{$data->status_name}}
            </button>
          </div>

        </div>

        <hr class=" mb-3">
        
        <h2 class="text-primary">{{ $data->name }}</h2>
        <p class="fs-5 col-md-12 mb-2">
          {!! $data->description !!}
          <br>
        </p>

        <hr class="mt-2 mb-5">

        <div class="row g-5">
          <div class="col-md-4 text-center">
            <h5 class=" text-dark"><b>PIC</b></h5>
            <ul class="icon-list">
              <?php 
                foreach ($data->assignees as $key => $user) {
                 echo' <li class="text-muted">'.$user->name.'</li>';
                }  
              ?>
            </ul>
          </div>

          <div class="col-md-4 text-center">
            <h5 class=" text-dark"><b>Tags</b></h5>
            <?php  foreach ($data->task_labels as $key => $label) { ?>
              <span class="badge bg-secondary">{{ $label->name }}</span>
            <?php } ?>
          </div>
 
          <div class="col-md-4 text-center">
            <a href="{{ env('UI_URL') }}/projects/{{ $data->project_id }}/tasks/view/{{ $data->id }}" class="btn btn-success" target="_blank">
              Open in PMS
            </a>
          </div>

        </div>

      </main>
      <footer class="pt-5 my-5 text-muted border-top">
        Created by {{($data->created_by_user) ? $data->created_by_user->name : ''}} - {{ H_formatDate($data->created_at, 'd F Y - H:i')}}
      </footer>
    </div>
  </body>

</html>