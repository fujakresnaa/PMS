<?php

namespace App\Repositories;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TaskAssigneesRepository;
use App\Models\TaskAssignees;
use App\Validators\TaskAssigneesValidator;
use Exception;

use App\Providers\HelperProvider;

use App\Traits\StandardRepo;

class TaskAssigneesRepositoryEloquent extends BaseRepository implements TaskAssigneesRepository
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
        return TaskAssignees::class;
    }

    /**
     * Model initiate
     * @return object
     */
    public function initModel($id = null) {
        $model = new TaskAssignees;
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
            $data->user_id = $request['user_id']; 
            $data->created_by = H_handleRequest($request, 'created_by'); 
            $data->updated_by = H_handleRequest($request, 'updated_by'); 
            $data->deleted_by = H_handleRequest($request, 'deleted_by'); 


            if ($id) $data->updated_by = H_handleRequest($request, 'updated_by', H_JWT_getUserId($raw_request)); 
            else $data->created_by = H_handleRequest($request, 'created_by', H_JWT_getUserId($raw_request));

            $data->save();
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
          $data = $data->whereTaskId($task_id);

          if ($relations) $data = $data->with($relations);
    
          return $this->dynamicList($data, $payload);
        } catch (Exception $e){
          throw new Exception($e->getMessage());
        }
    }

    /*
     * jangan gunakan id untuk update, karena "id" disini tidak valid
     # 'id' yang ada di user yg baru di assign, adalah project_members.id
     # 'id' yang ada di user yg sudah pernah di assign, adalah task_assignees.id
    */
    public function assign($task_id, $data) {
        try {

            // CLEANUP : delete dlu semua data yang ada atas task terpilih
            $this->model->whereTaskId($task_id)->delete();
            
            $commit = [];
            foreach ($data as $key => $user) {
                $send = [
                    'task_id' => $task_id,
                    'user_id' => $user['user_id'],
                ];
                // checking apakah ada data yg tetap di assign, namun terhapus karena clean up diatas
                $old = $this->model->whereTaskId($task_id)->whereUserId($user['user_id'])->onlyTrashed()->first();
                if ($old) $old->restore(); // jika sudah pernah ada, dan masih masuk assignees, akan di aktifkan ulang
                else $this->store(null, null, $send); // jika blm pernah di assign, akan di insert
            }

            return $commit;
            
        } catch (Exception $e){
          throw new Exception($e->getMessage());
        }
    }

    public function myTask($raw_request, $userid, $relations = null) {
        try {
          $payload = $raw_request->all();
          $data = $this->findAll($raw_request, true);
          $data = $data->whereUserId($userid);

          if ($relations) $data = $data->with($relations);
    
          return $this->dynamicList($data, $payload);
        } catch (Exception $e){
          throw new Exception($e->getMessage());
        }
    }
}
