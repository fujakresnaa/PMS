
<?php 
$router->post('user/login', ['uses' => 'UsersAuthController@authenticate']);
$router->post('user/logout', ['uses' => 'UsersAuthController@logout']);
$router->get('questionnaires-dummy', ['uses' => 'QuestionnairesController@dummy']);
$router->get('projects/{id}/docs', ['uses' => 'ProjectsController@getProjectDocs']);
$router->get('projects/{id}/docs-api', ['uses' => 'ProjectsController@getProjectDocsApi']);

$router->group(["prefix" => "/user-notifications"], function() use ($router) {
    $router->get("/", "UserNotificationsController@index");
    $router->get("/user/{id}", "UserNotificationsController@getByUser");
    $router->get("/all/{id}", "UserNotificationsController@getAllByUser");
    $router->get("/generate/{id}", "UserNotificationsController@testNotif");
    $router->get("/{id}/read", "UserNotificationsController@read");
});


$router->group(['middleware' => 'users.auth'],  function() use ($router) {

    $router->group(["prefix" => "/me"], function() use ($router) {
        $router->get("/", "UsersController@info");
        $router->get("/permissions", "UsersController@permissions");
        $router->get("/menus", "UsersController@menus");
        $router->get("/notifications", "UsersController@notifications");
        $router->get("/all-notifications", "UsersController@allNotifications");

        $router->post("/change-password/{id}", "UsersController@changePassword");
        $router->post("/update-profile/{id}", "UsersController@updateProfile");
    
    });

    $router->group(["prefix" => "/user-sessions"], function() use ($router) {
        $router->get("/", "UserSessionsController@index");
        $router->get("/{id}", "UserSessionsController@findById");
        $router->post("/", "UserSessionsController@store");
        $router->put("/{id}", "UserSessionsController@store");
        $router->put("/{id}/restore", "UserSessionsController@restore");
        $router->delete("/{id}", "UserSessionsController@remove");
    });
        
    $router->group(["prefix" => "/permissions"], function() use ($router) {
        $router->get("/", "PermissionsController@index");
        $router->get("/{id}", "PermissionsController@findById");
        $router->post("/", "PermissionsController@store");
        $router->put("/{id}", "PermissionsController@store");
        $router->put("/{id}/restore", "PermissionsController@restore");
        $router->delete("/{id}", "PermissionsController@remove");
    }); 
    
    $router->group(["prefix" => "/role-permissions"], function() use ($router) {
        $router->get("/", "RolePermissionsController@index");
        $router->get("/{id}", "RolePermissionsController@findById");
        $router->post("/", "RolePermissionsController@store");
        $router->put("/{id}", "RolePermissionsController@store");
        $router->put("/{id}/restore", "RolePermissionsController@restore");
        $router->delete("/{id}", "RolePermissionsController@remove");
    }); 
    
    $router->group(["prefix" => "/roles"], function() use ($router) {
        $router->get("/", "RolesController@index");
        $router->get("/{id}", "RolesController@findById");
        $router->post("/", "RolesController@store");
        $router->put("/{id}", "RolesController@store");
        $router->put("/{id}/restore", "RolesController@restore");
        $router->delete("/{id}", "RolesController@remove");
    });
    
    $router->group(["prefix" => "/menu-items"], function() use ($router) {
        $router->get("/", "MenuItemsController@index");
        $router->get("/picker", "MenuItemsController@picker");
        $router->get("/{id}", "MenuItemsController@findById");
        $router->post("/", "MenuItemsController@store");
        $router->put("/{id}", "MenuItemsController@store");
        $router->put("/{id}/restore", "MenuItemsController@restore");
        $router->delete("/{id}", "MenuItemsController@remove");
    });
        
    $router->group(["prefix" => "/menus"], function() use ($router) {
        $router->get("/", "MenusController@index");
        $router->get("/{id}", "MenusController@findById");
        $router->post("/", "MenusController@store");
        $router->put("/{id}", "MenusController@store");
        $router->put("/{id}/restore", "MenusController@restore");
        $router->delete("/{id}", "MenusController@remove");
    });
    
    $router->group(["prefix" => "/users"], function() use ($router) {
        $router->get("/", "UsersController@index");
        $router->get("/{id}", "UsersController@findById");
        $router->get("/{id}/menus", "UsersController@menus");
        $router->get("/{id}/permissions", "UsersController@permissions");
        $router->get("/{id}/notifications", "UsersController@notifications");
        $router->post("/", "UsersController@store");
        $router->put("/{id}", "UsersController@store");
        $router->put("/{id}/restore", "UsersController@restore");
        $router->delete("/{id}", "UsersController@remove");
    }); 
    
    $router->group(["prefix" => "/master-menus"], function() use ($router) {
        $router->get("/", "MasterMenusController@index");
        $router->get("/init", "MasterMenusController@initMenuAdmin");
        $router->get("/{id}", "MasterMenusController@findById");
        $router->post("/", "MasterMenusController@store");
        $router->put("/{id}", "MasterMenusController@store");
        $router->put("/{id}/restore", "MasterMenusController@restore");
        $router->delete("/{id}", "MasterMenusController@remove");
    });

    // default module
       
    Route::group(["prefix" => "/project-members"], function() use ($router) {
        $router->get("/", "ProjectMembersController@index");
        $router->get("/{id}", "ProjectMembersController@findById");
        $router->post("/", "ProjectMembersController@store");
        $router->put("/{id}", "ProjectMembersController@store");
        $router->put("/{id}/restore", "ProjectMembersController@restore");
        $router->delete("/{id}", "ProjectMembersController@remove");
    });   
    
    Route::group(["prefix" => "/work-assignees"], function() use ($router) {
        $router->get("/", "WorkAssigneesController@index");
        $router->get("/{id}", "WorkAssigneesController@findById");
        $router->post("/", "WorkAssigneesController@store");
        $router->put("/{id}", "WorkAssigneesController@store");
        $router->put("/{id}/restore", "WorkAssigneesController@restore");
        $router->delete("/{id}", "WorkAssigneesController@remove");
    });
    
    Route::group(["prefix" => "/sprints"], function() use ($router) {
        $router->get("/", "SprintsController@index");
        $router->get("/{id}", "SprintsController@findById");
        $router->post("/", "SprintsController@store");
        $router->put("/{id}", "SprintsController@store");
        $router->put("/{id}/started", "SprintsController@setStarted");
        $router->put("/{id}/completed", "SprintsController@setCompleted");
        $router->put("/{id}/restore", "SprintsController@restore");
        $router->delete("/{id}", "SprintsController@remove");
    });
        
    Route::group(["prefix" => "/tasks"], function() use ($router) {
        $router->get("/", "TasksController@index");
        $router->get("/{id}", "TasksController@findById");
        $router->post("/", "TasksController@store");
        $router->put("/{id}", "TasksController@store");
        $router->put("/{id}/move", "TasksController@move");
        $router->put("/{id}/restore", "TasksController@restore");
        $router->delete("/{id}", "TasksController@remove");
    });
    
    Route::group(["prefix" => "/task-comments"], function() use ($router) {
        $router->get("/", "TaskCommentsController@index");
        $router->get("/{id}", "TaskCommentsController@findById");
        $router->post("/", "TaskCommentsController@store");
        $router->put("/{id}", "TaskCommentsController@store");
        $router->put("/{id}/restore", "TaskCommentsController@restore");
        $router->delete("/{id}", "TaskCommentsController@remove");
    });
    
    Route::group(["prefix" => "/task-components"], function() use ($router) {
        $router->get("/", "TaskComponentsController@index");
        $router->get("/{id}", "TaskComponentsController@findById");
        $router->post("/", "TaskComponentsController@store");
        $router->put("/{id}", "TaskComponentsController@store");
        $router->put("/{id}/restore", "TaskComponentsController@restore");
        $router->delete("/{id}", "TaskComponentsController@remove");
    });
    
    Route::group(["prefix" => "/task-status"], function() use ($router) {
        $router->get("/", "TaskStatusController@index");
        $router->get("/{id}", "TaskStatusController@findById");
        $router->post("/", "TaskStatusController@store");
        $router->put("/{id}", "TaskStatusController@store");
        $router->put("/{id}/restore", "TaskStatusController@restore");
        $router->delete("/{id}", "TaskStatusController@remove");
    });
        
    Route::group(["prefix" => "/task-labels"], function() use ($router) {
        $router->get("/", "TaskLabelsController@index");
        $router->get("/{id}", "TaskLabelsController@findById");
        $router->post("/", "TaskLabelsController@store");
        $router->put("/{id}", "TaskLabelsController@store");
        $router->put("/{id}/restore", "TaskLabelsController@restore");
        $router->delete("/{id}", "TaskLabelsController@remove");
    });
    
    Route::group(["prefix" => "/task-assignees"], function() use ($router) {
        $router->get("/", "TaskAssigneesController@index");
        $router->get("/{id}", "TaskAssigneesController@findById");
        $router->post("/", "TaskAssigneesController@store");
        $router->put("/{id}", "TaskAssigneesController@store");
        $router->put("/{id}/restore", "TaskAssigneesController@restore");
        $router->delete("/{id}", "TaskAssigneesController@remove");
    });
    
    // app module

    
    

    
    Route::group(["prefix" => "/tasks"], function() use ($router) {
        $router->get("/", "TasksController@index");
        $router->get("/{id}", "TasksController@findById");
        // breakdown
        $router->get("/{id}/assignees", "TaskAssigneesController@getByTask");
        $router->get("/{id}/comments", "TaskCommentsController@getByTask");
        $router->get("/{id}/components", "TaskComponentsController@getByTask");
    
        $router->post("/", "TasksController@store");
        $router->post("/fix-ordering", "TasksController@fixOrdering");
        $router->put("/{id}", "TasksController@store");
        $router->put("/{id}/started", "TasksController@setStarted");
        $router->put("/{id}/completed", "TasksController@setCompleted");
        $router->put("/{id}/done", "TasksController@setDone");
        $router->put("/{id}/restore", "TasksController@restore");
        $router->delete("/{id}", "TasksController@remove");
    });
    
    Route::group(["prefix" => "/work-labels"], function() use ($router) {
        $router->get("/", "WorkLabelsController@index");
        $router->get("/{id}", "WorkLabelsController@findById");
    
        $router->get("/{id}/related-works", "WorkLabelsController@relatedWorks");
    
        $router->post("/", "WorkLabelsController@store");
        $router->put("/{id}", "WorkLabelsController@store");
        $router->put("/{id}/restore", "WorkLabelsController@restore");
        $router->delete("/{id}", "WorkLabelsController@remove");
    });
    
    Route::group(["prefix" => "/project-config"], function() use ($router) {
        $router->get("/", "ProjectConfigController@index");
        $router->get("/{id}", "ProjectConfigController@findById");
        $router->post("/", "ProjectConfigController@store");
        $router->put("/{id}", "ProjectConfigController@store");
        $router->put("/{id}/restore", "ProjectConfigController@restore");
        $router->delete("/{id}", "ProjectConfigController@remove");
    });
    
    Route::group(["prefix" => "/works"], function() use ($router) {
        $router->get("/", "WorksController@index");
        $router->get("/{id}", "WorksController@findById");
        // breakdown
        $router->get("/{id}/sprints", "SprintsController@getByWork");
        $router->get("/{id}/assignees", "WorkAssigneesController@getByWork");
        $router->get("/{id}/related-head", "WorksController@relatedHead");
        $router->get("/{id}/related-sub", "WorksController@relatedSub");
        $router->get("/{id}/tasks", "TasksController@getByWork");
    
        $router->post("/", "WorksController@store");
        $router->put("/{id}", "WorksController@store");
        $router->put("/{id}/started", "WorksController@setStarted");
        $router->put("/{id}/completed", "WorksController@setCompleted");
        $router->put("/{id}/done", "WorksController@setDone");
        $router->put("/{id}/restore", "WorksController@restore");
        $router->delete("/{id}", "WorksController@remove");
    });

    Route::group(["prefix" => "/projects"], function() use ($router) {
        $router->get("/", "ProjectsController@index");
        $router->get("/{id}", "ProjectsController@findById");
        $router->post("/", "ProjectsController@store");
        $router->put("/{id}", "ProjectsController@store");
        $router->put("/{id}/restore", "ProjectsController@restore");
        $router->delete("/{id}", "ProjectsController@remove");
    });
    

});


// WBS
Route::group(['prefix' => 'wbs'], function() use ($router){
    $router->get('/', 'WBSController@get');
    $router->get('/v2', 'WBSController@get2');
});

