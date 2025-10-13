<?php

namespace App\Repositories;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WorkLabelsRepository;
use App\Models\WorkLabels;
use App\Validators\WorkLabelsValidator;
use Exception;
use DB;

use App\Providers\HelperProvider;

use App\Traits\StandardRepo;

class WorkLabelsRepositoryEloquent extends BaseRepository implements WorkLabelsRepository
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
        return WorkLabels::class;
    }

    /**
     * Model initiate
     * @return object
     */
    public function initModel($id = null) {
        $model = new WorkLabels;
        if (!empty($id)) $model = $this->model->where($this->model->getKeyName(), $id)->first();
        return $model;
    }

    public function store($raw_request, $id = null, $customRequest = null) {
        try {
 
            if ($customRequest === null) $request = $raw_request->all();
            else $request = $customRequest;

            $data = $this->initModel($id);

            //storing defined property    
            $data->name = $request['name']; 
            $data->project_id = $request['project_id']; 
            $data->color = H_handleRequest($request, 'color'); 
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

    public function getByProject($raw_request, $project_id, $relations = null) {
        try {
            $payload = $raw_request->all();
            $data = $this->findAll($raw_request, true);
            $data = $data->whereProjectId($project_id);

            if ($relations) $data = $data->with($relations);
        
            return $this->dynamicList($data, $payload);
        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function relatedWorks($raw_request, $id, $relations = null) {
        try {
            $payload = $raw_request->all();
            $data = $this->findAll($raw_request, true);
            $data = $data->whereId($id);
            
            if ($relations) $data = $data->with($relations);
            $data = $data->first();
            
            if ($data) {
                $related_works = DB::table('works')
                ->whereJsonContains('labels', (int) $id)
                ->get();
                $data->related_works = $related_works;
            }

            return $data;
        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

}
