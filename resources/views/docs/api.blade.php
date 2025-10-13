<?php 
$host = H_getServerInfo('host');
if (isset($_GET['dd'])) dd($data);

function getDocs($module) {
    $docs = [];
    foreach ($module->tasks as $key2 => $task) {
        foreach ($task->docs_api as $key3 => $doc) {
            $docs[] = $doc;
        }
    }
    return $docs;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>API Documentation | {{$data->name}}</title>
    <meta charset="utf-8">
    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="stylesheet" href="{{$host}}/docs/spectre.css">
    <link rel="stylesheet" href="{{$host}}/docs/spectre-ext.css">
    <link rel="stylesheet" href="{{$host}}/docs/doc.css">
  </head>
  <body>
    <div class="docs-container off-canvas off-canvas-sidebar-show">
      <div class="docs-navbar"><a class="off-canvas-toggle btn btn-link btn-action" href="#sidebar" style="rotate: 90deg;" >|||</a>
      </div>
      <div class="docs-sidebar off-canvas-sidebar" id="sidebar">
        <div style="padding: 10px 10px 15px 20px;position: fixed;">
            <a class="docs-logo" href="">
                <b>{{$data->name}}</b><br>
                <small class="label label-error text-bold">API-DOCS</small>
            </a>
        </div>
        <!-- Menu -->
        <div class="docs-nav">
          <div class="accordion-container">

            <?php 
                $modules = (isset($data->modules)) ? $data->modules : [];
                foreach ($modules as $key => $module) {

                    $docs = getDocs($module);
                    $slugMod = H_makeSlug($module->name);
   
            ?>
            <div class="accordion">
                <input id="accordion-{{$slugMod}}" type="checkbox" name="docs-accordion-checkbox" hidden=""/>
                <label class="accordion-header c-hand" for="accordion-{{$slugMod}}">{{$module->name}}</label>
                <div class="accordion-body">
                  <ul class="menu menu-nav">
                    <?php 
                        foreach ($docs as $kd => $doc) {
                            # code...
                            $slug = H_makeSlug($doc->name);
                    ?>
                        <li class="menu-item"><a href="#{{$slug}}">{{$doc->name}}</a></li>
                    <?php } ?>
                  </ul>
                </div>
              </div>
            <?php }?>

          </div>
        </div>
        
      </div>
      <a class="off-canvas-overlay" href="#close"></a>
      <div class="off-canvas-content">
        <div class="docs-content" id="content">

            <?php 
                $modules = (isset($data->modules)) ? $data->modules : [];
                foreach ($modules as $key => $module) {

                    $docs = getDocs($module);
                    $slugMod = H_makeSlug($module->name);
   
            ?>
                <?php 
                    foreach ($docs as $kd => $doc) {
                        # code...
                        $slug = H_makeSlug($doc->name);
                ?>
                    <div class="container" id="{{$slug}}">
                        <h3 class="s-title">{{$doc->name}}<a class="anchor" href="#tabs" aria-hidden="true">#</a></h3>

                        <div>{!! nl2br($doc->description) !!}</div>
                    </div>

                <?php } ?>
            <?php } ?>

          <div class="docs-footer container grid-lg" id="copyright">
            <p>{{$data->name}} | API Documentation</p>
          </div>


        </div>
      </div>
    </div>

  </body>
</html>