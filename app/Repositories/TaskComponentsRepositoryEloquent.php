<?php

namespace App\Repositories;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TaskComponentsRepository;
use App\Models\TaskComponents;
use App\Models\Works;
use App\Models\Projects;
use App\Validators\TaskComponentsValidator;
use Exception;

use App\Providers\HelperProvider;

use App\Traits\StandardRepo;

class TaskComponentsRepositoryEloquent extends BaseRepository implements TaskComponentsRepository
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
        return TaskComponents::class;
    }

    /**
     * Model initiate
     * @return object
     */
    public function initModel($id = null) {
        $model = new TaskComponents;
        if (!empty($id)) $model = $this->model->where($this->model->getKeyName(), $id)->first();
        return $model;
    }

    public function store($raw_request, $id = null, $customRequest = null) {
        try {
 
            if ($customRequest === null) $request = $raw_request->all();
            else $request = $customRequest;

            $data = $this->initModel($id);

            //storing defined property    
            $data->project_id = $request['project_id']; 
            $data->task_id = $request['task_id']; 
            $data->name = $request['name']; 
            $data->summary = H_handleRequest($request, 'summary'); 
            $data->description = $request['description']; 
            $data->type = $request['type']; 
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

    public function getByProject($raw_request, $project_id, $relations = null) {
        try {
            $payload = $raw_request->all();
            $data = $this->findAll($raw_request, true);
            $data = $data->whereProjectId($project_id);

            if (H_hasRequest($payload, 'task')) $data = $data->whereTaskId($payload['task']);

            if ($relations) $data = $data->with($relations);
        
            return $this->dynamicList($data, $payload);
        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function getByWork($raw_request, $work_id, $relations = null) {
        try {
            $payload = $raw_request->all();
            $data = $this->findAll($raw_request, true);
            $data = $data->whereHas('Tasks.Works', function ($q) use ($work_id) {
                $q->where('work_id', $work_id);
            });

            if ($relations) $data = $data->with($relations);

            return $this->dynamicList($data, $payload);
        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function getProjectDocs($raw_request, $id) {
        try {
            $payload = $raw_request->all();
            $data = Projects::whereRaw('projects.id = ?', [$id]);
            $data = $data->select([
                'projects.id as pid',
                'projects.name as pname',
                //
                'works.id as wid',
                'works.name as wname',
                //   'project_id',
                //   'summary',
            ]);

            $data = $data->join('works', function ($join) use ($id) {
                $join->on('projects.id', '=', 'works.project_id');
            });

            $data = $data->get();
    
        
            return $data;
        } catch (Exception $e){
          throw new Exception($e->getMessage());
        }
    }

}
