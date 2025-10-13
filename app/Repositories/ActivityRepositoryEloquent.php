<?php

namespace App\Repositories;

use Laravel\Lumen\Application;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\ActivityRepository;
use Spatie\Activitylog\Models\Activity;
use Exception;
use Illuminate\Http\Request;
use App\Traits\StandardRepo;

use App\Repositories\UsersRepositoryEloquent;
use App\Repositories\TasksRepositoryEloquent;
use App\Repositories\WorksRepositoryEloquent;
use App\Models\Users;
use App\Models\UserNotifications;
use App\Models\ProjectMembers;

class ActivityRepositoryEloquent extends BaseRepository implements ActivityRepository
{
    use StandardRepo;
    protected $request;

    public function __construct(
        Application $app,
        Request $request
    ){
        parent::__construct($app);
        $this->request = $request;
    }


    public function model() {
        return Activity::class;
    }

    /**
     * Model initiate
     * @return object
     */
    public function initModel($id = null) {
        $model = new Activity;
        if (!empty($id)) $model = $this->model->where($this->model->getKeyName(), $id)->first();
        return $model;
    }

    public function store($request = []) {
        try {
            $data = $this->initModel();

            $data->log_name = $request['log_name'] ?? 'default';
            $data->description = $request['description'] ?? '';

            if(isset($request['subject'])) {
                $data->subject_id = $request['subject']->getKey();
                $data->subject_type = $request['subject']->getMorphClass();
            }

            if(isset($request['causer'])) {
                $data->causer_id = $request['causer'];
                $data->causer_type = 'App\Models\Users';
            }
            $data->properties = $request['properties'] ?? [];

            $data->save();
            return $data;
        } catch (Exception $e){ 
            throw new Exception($e->getMessage());
        } 
    }

    public function make($send, $mode, $properties = null) { // mode : c,u,d,r (r = restore)
        try {

            $type = "Create";
            $causer = $send->created_by;
            $color = "primary";
            if ($mode == 'u') {
                $type = "Update";
                $causer = $send->updated_by;
                $color = "green";
            }
            if ($mode == 'd') {
                $type = "Delete";
                $causer = $send->deleted_by;
                $color = "red";
            }
            if ($mode == 'r') {
                $type = "Restore";
                $causer = $send->updated_by;
                $color = "orange";
            }

            $data = $this->initModel();
            $data->subject_id = $send->getKey();
            $data->subject_type = $send->getMorphClass();

            $moduleName = str_replace(
                ['App\Models\\'],
                ['', ''],
                $data->subject_type
            );

            $causerId = H_JWT_getUserId($this->request);
            // handler jika data baru dibuat, ambil langsung dari header
            if ($causerId) $causer = $causerId;
            else {
                throw new Exception('Data maybe saved, but notification has failed to generate.');
            }

            $causer = Users::find($causer);
            $data->log_name = $send->log_name ?? 'default';

            $name = '';
            if (isset($send->title)) $name = '"'.$send->title.'"';
            if (isset($send->name)) $name = '"'.$send->name.'"';

            $description = $send->log_description ?? "$type $moduleName : ".$name." by ".$causer->name;
            $data->description = $description;

            $data->causer_id = $causer->id;
            $data->causer_type = 'App\Models\Users';

            // using log definer when log type set
            if (isset($send->log_type) && $send->log_type != null) $data = $this->logDefiner($data, $send, $send->log_type);

            $data->properties = $properties ?  $properties : ["color" => $color];

            $data->save();
            return $data;
        } catch (Exception $e){ 
            throw new Exception($e->getMessage());
        } 
    }

    public function logDefiner($model, $data, $type) {

        $name = 'logDefiner';
        $description = 'logDefiner';

        $causer = Users::find($model->causer_id);

        if ($type == 'works-started') {
            $name = 'works';
            $description = 'Work "' .$data->name. '" has started by '.$causer->name.' ';

            $pathParams = [
                "path" => 'view-works',
                "params" => [
                    "project_id" => $data->project_id,
                    "id" => $data->id,
                ]
            ];
            $this->createNotif($data, "Work Started", $description, $pathParams);
        }

        if ($type == 'work-completed') {
            $name = 'works';
            $description = 'Work "' .$data->name. '" has completed by '.$causer->name.' ';

            $pathParams = [
                "path" => 'view-works',
                "params" => [
                    "project_id" => $data->project_id,
                    "id" => $data->id,
                ]
            ];
            $this->createNotif($data, "Work Completed", $description, $pathParams);
        }

        if ($type == 'work-done') {
            $name = 'works';
            $description = 'Work "' .$data->name. '" has set to Done by '.$causer->name.', this work will not be displayed in Board ';

            $pathParams = [
                "path" => 'view-works',
                "params" => [
                    "project_id" => $data->project_id,
                    "id" => $data->id,
                ]
            ];
            $this->createNotif($data, "Work Done", $description, $pathParams);
        }

        if ($type == 'task-moving') {
            $name = 'board';
            $description = 'Task "' .$data->name. '" has moved to '.$data->status_name.' by '.$causer->name.' ';
            
            $pathParams = [
                "path" => 'view-tasks',
                "params" => [
                    "project_id" => $data->project_id,
                    "id" => $data->id,
                ]
            ];
            $this->createNotif($data, "Task Moving", $description, $pathParams);
        }

        if ($type == 'task-started') {
            
            $name = 'tasks';
            $description = 'Task "' .$data->name. '" has started by '.$causer->name.' ';

            $pathParams = [
                "path" => 'view-tasks',
                "params" => [
                    "project_id" => $data->project_id,
                    "id" => $data->id,
                ]
            ];
            $this->createNotif($data, "Task Started", $description, $pathParams);
        }

        if ($type == 'task-completed') {
            $name = 'tasks';
            $description = 'Task "' .$data->name. '" has completed by '.$causer->name.' ';

            $pathParams = [
                "path" => 'view-tasks',
                "params" => [
                    "project_id" => $data->project_id,
                    "id" => $data->id,
                ]
            ];
            $this->createNotif($data, "Task Completed", $description, $pathParams);
        }

        if ($type == 'task-done') {
            $name = 'tasks';
            $description = 'Task "' .$data->name. '" has set to Done by '.$causer->name.', this task will not be displayed in Board ';

            $pathParams = [
                "path" => 'view-tasks',
                "params" => [
                    "project_id" => $data->project_id,
                    "id" => $data->id,
                ]
            ];
            $this->createNotif($data, "Task Done", $description, $pathParams);
        }

        if ($type == 'sprint-started') {
            $name = 'sprints';
            $description = 'Sprint "' .$data->name. '" has started by '.$causer->name.' ';

            $pathParams = [
                "path" => 'view-sprints',
                "params" => [
                    "project_id" => $data->project_id,
                    "id" => $data->id,
                ]
            ];
            $this->createNotif($data, "Sprint Started", $description, $pathParams);
        }

        if ($type == 'sprint-completed') {
            $name = 'sprints';
            $description = 'Sprint "' .$data->name. '" has completed by '.$causer->name.' ';

            $pathParams = [
                "path" => 'view-sprints',
                "params" => [
                    "project_id" => $data->project_id,
                    "id" => $data->id,
                ]
            ];
            $this->createNotif($data, "Sprint Completed", $description, $pathParams);
        }

        $model->log_name = $name;
        $model->description = $description;
        return $model;
    }

    public function createNotif($data, $title, $description, $pathParams = null) {

        $path = null;
        $params = null;

        if ($pathParams) {
            $path = $pathParams['path'];
            $params = $pathParams['params'];;
        }

        $members = ProjectMembers::whereProjectId($data->project_id)->get();

        foreach ($members as $key => $member) {
            $send = [
                "user_id" => $member->user_id,
                "title" => $title,
                "description" => $description,
                "type" => 'info',
                "link_path" => $path,
                "link_params" => $params,
            ];
            UserNotifications::create($send);
        }
    }

    // add your customize function

}
