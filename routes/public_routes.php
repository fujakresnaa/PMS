<?php

$router->post('upload-image', ['uses' => 'FileHandlingController@uploadImage']);
$router->get('init-menu', ['uses' => 'MasterMenusController@initMenuAdmin']);

$router->get('references', 'ReferenceController@index');

$router->get('init-initan', 'MasterMenusController@addNewModule');
$router->get('wbs', 'MasterMenusController@wbs');
$router->get('my-task/{id}', 'TasksController@myTask');
// $router->get('my-task/{id}', 'TaskAssigneesController@myTask');

// views
$router->group(["prefix" => "/report"], function() use ($router) { // resources/views/apps/..

    $router->get("/propose/{work_id}", "WorksController@propose");
    $router->get("/brd/{work_id}", "WorksController@brd");
    $router->get("/works/{id}", "WorksController@report");
    $router->get("/tasks/{project_id}", "TasksController@report");

});

// preview
$router->group(["prefix" => "/preview"], function() use ($router) { // resources/views/apps/..

    $router->get("/works/{id}", "WorksController@preview");
    $router->get("/tasks/{id}", "TasksController@preview");

});

// projects
Route::group(["prefix" => "/projects"], function() use ($router) {
    // breakdown
    $router->get("/{id}/works", "WorksController@getByProject");
    $router->get("/{id}/status", "TaskStatusController@getByProject");
    $router->get("/{id}/members", "ProjectMembersController@getByProject");
    $router->get("/{id}/work-labels", "WorkLabelsController@getByProject");
    $router->get("/{id}/tasks", "TasksController@getByProject");
    $router->get("/{id}/board", "TaskStatusController@board");
    $router->get("/{id}/board-v2", "TaskStatusController@board2");
    $router->get("/{id}/board-v3", "TaskStatusController@board3");
    $router->get("/{id}/task-status", "TaskStatusController@getByProject");
    $router->get("/{id}/task-labels", "TaskLabelsController@getByProject");
    $router->get("/{id}/task-components", "TaskComponentsController@getByProject");
    $router->get("/{id}/work-components", "TaskComponentsController@getByWork");
    $router->get("/{id}/config", "ProjectConfigController@getByProject");
    $router->get("/{id}/roadmaps", "WorksController@roadmaps");
    $router->get("/{id}/timeline", "WorksController@timeline");

});

