<?php

namespace App\Repositories;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProjectMembersRepository;
use App\Models\ProjectMembers;
use App\Validators\ProjectMembersValidator;
use Exception;

use App\Providers\HelperProvider;

use App\Traits\StandardRepo;

class ProjectMembersRepositoryEloquent extends BaseRepository implements ProjectMembersRepository
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
        return ProjectMembers::class;
    }

    /**
     * Model initiate
     * @return object
     */
    public function initModel($id = null) {
        $model = new ProjectMembers;
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

    public function assign($project_id, $data) {
        try {

            // CLEANUP : delete dlu semua data yang ada atas task terpilih
            $this->model->whereProjectId($project_id)->delete();
            
            $commit = [];
            foreach ($data as $key => $user) {
                $send = [
                    'project_id' => $project_id,
                    'user_id' => $user['user_id'],
                ];
                // checking apakah ada data yg tetap di assign, namun terhapus karena clean up diatas
                $old = $this->model->whereProjectId($project_id)->whereUserId($user['user_id'])->onlyTrashed()->first();
                if ($old) $old->restore(); // jika sudah pernah ada, dan masih masuk assignees, akan di aktifkan ulang
                else $this->store(null, null, $send); // jika blm pernah di assign, akan di insert
            }

            return $commit;
            
        } catch (Exception $e){
          throw new Exception($e->getMessage());
        }
    }
}
