<?php

namespace App\Repositories;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\SprintsRepository;
use App\Models\Sprints;
use App\Validators\SprintsValidator;
use Exception;

use App\Providers\HelperProvider;

use App\Traits\StandardRepo;

class SprintsRepositoryEloquent extends BaseRepository implements SprintsRepository
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
        return Sprints::class;
    }

    /**
     * Model initiate
     * @return object
     */
    public function initModel($id = null) {
        $model = new Sprints;
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
            $data->work_id = $request['work_id']; 
            $data->name = $request['name']; 
            $data->goals = $request['goals']; 
            $data->status = $request['status']; 
            $data->start_date = H_handleRequest($request, 'start_date'); 
            $data->end_date = H_handleRequest($request, 'end_date'); 
            $data->actual_start_date = H_handleRequest($request, 'actual_start_date'); 
            $data->actual_end_date = H_handleRequest($request, 'actual_end_date'); 
            $data->created_by = H_handleRequest($request, 'created_by'); 
            $data->updated_by = H_handleRequest($request, 'updated_by'); 
            $data->deleted_by = H_handleRequest($request, 'deleted_by'); 


            if ($id) $data->updated_by = H_handleRequest($request, 'updated_by', H_JWT_getUserId($raw_request)); 
            else $data->created_by = H_handleRequest($request, 'created_by', H_JWT_getUserId($raw_request)); 
    
            $data->save();

            // logging injection
            $data->log_name = 'sprints';
            $this->logger($data, $id, H_handleRequest($request, 'log_type'));
    
            return $data;

        } catch (Exception $e){ 
            throw new Exception($e->getMessage());
        } 
    }

    public function setStarted($id) {
        $data = $this->initModel($id);
        $data->actual_start_date = H_getCurrentDate(true);
        $data->save();
        
        $this->logger($data, $id, 'sprint-started');

        return $data;
    }

    public function setCompleted($id) {
        $data = $this->initModel($id);
        $data->actual_end_date = H_getCurrentDate(true);
        $data->save();
        
        $this->logger($data, $id, 'sprint-completed');

        return $data;
    }

    // add your customize function

    public function getByWork($raw_request, $work_id, $relations = null) {
        try {
          $payload = $raw_request->all();
          $data = $this->findAll($raw_request, true);
          $data = $data->whereWorkId($work_id);

          if ($relations) $data = $data->with($relations);
    
          return $this->dynamicList($data, $payload);
        } catch (Exception $e){
          throw new Exception($e->getMessage());
        }
    }

}
