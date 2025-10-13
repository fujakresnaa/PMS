<?php

namespace App\Repositories;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TaskCommentsRepository;
use App\Models\TaskComments;
use App\Models\UserNotifications;
use App\Models\ProjectMembers;
use App\Validators\TaskCommentsValidator;
use Exception;

use App\Providers\HelperProvider;

use App\Traits\StandardRepo;

class TaskCommentsRepositoryEloquent extends BaseRepository implements TaskCommentsRepository
{
    use StandardRepo;

    protected $log;

    public function __construct(
        Application $app,
        ActivityRepository $log
    ){
        parent::__construct($app);

        $this->log = $log;
    }


    public function model() {
        return TaskComments::class;
    }

    /**
     * Model initiate
     * @return object
     */
    public function initModel($id = null) {
        $model = new TaskComments;
        if (!empty($id)) $model = $this->model->where($this->model->getKeyName(), $id)->first();
        return $model;
    }

    public function store($raw_request, $id = null, $customRequest = null) {
        try {
 
            if ($customRequest === null) $request = $raw_request->all();
            else $request = $customRequest;

            $data = $this->initModel($id);

            //storing defined property    
            $data->task_id = $request['task_id']; 
            $data->description = $request['description'];

            if (H_hasRequest($request, 'reply')) {
                $paretComment = $this->model->find($request['reply']);
                $data->reply = $request['reply'];
                // jika sudah pernah ada reply, reply di ganti jadi task_id, agar level comment tetap di 2
                if ($paretComment != null && $paretComment->reply != null) $data->reply = $request['task_id']; 
            }
 
            $data->created_by = H_handleRequest($request, 'created_by'); 
            $data->updated_by = H_handleRequest($request, 'updated_by'); 
            $data->deleted_by = H_handleRequest($request, 'deleted_by'); 


            if ($id) $data->updated_by = H_handleRequest($request, 'updated_by', H_JWT_getUserId($raw_request)); 
            else $data->created_by = H_handleRequest($request, 'created_by', H_JWT_getUserId($raw_request)); 
    
            $data->save();

            $this->createNotif($data);
            

            return $data;

        } catch (Exception $e){ 
            throw new Exception($e->getMessage());
        } 
    }

    // add your customize function

    public function getByTask($raw_request, $task_id, $relations = null) {
        try {
          $payload = $raw_request->all();
          $data = $this->findAll($raw_request, true);
          $data = $data->whereRaw('reply IS NULL');
          $data = $data->whereTaskId($task_id);

          if ($relations) $data = $data->with($relations);
    
          return $this->dynamicList($data, $payload);
        } catch (Exception $e){
          throw new Exception($e->getMessage());
        }
    }

    public function createNotif ($data) {
        $title = 'New comment on task ';
        $description = $data->created_by_user->username . ' submited new comment on Task ' . $data->tasks->name ;

        $members = ProjectMembers::whereProjectId($data->tasks->project_id)->get();

        foreach ($members as $key => $member) {
            $send = [
                "user_id" => $member->user_id,
                "title" => $title,
                "description" => $description,
                "type" => 'info',
                "link_path" => 'view-tasks',
                "link_params" => [
                    "id" => $data->task_id,
                    "tab" => 'comments',
                ],
            ];
            UserNotifications::create($send);
        }
    }

}
