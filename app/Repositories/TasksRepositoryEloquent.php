<?php

namespace App\Repositories;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TasksRepository;
use App\Repositories\TaskAssigneesRepository;
use App\Models\Tasks;
use App\Validators\TasksValidator;
use Exception;
use Illuminate\Support\Collection;

use App\Providers\HelperProvider;

use App\Traits\StandardRepo;

class TasksRepositoryEloquent extends BaseRepository implements TasksRepository
{
    use StandardRepo;

    protected $log;
    protected $taskAssigneesRepository;

    public function __construct(
        Application $app,
        TaskAssigneesRepository $taskAssigneesRepository,
        ActivityRepository $log
    ){
        parent::__construct($app);

        $this->log = $log;
        $this->taskAssigneesRepository = $taskAssigneesRepository;
    }


    public function model() {
        return Tasks::class;
    }

    /**
     * Model initiate
     * @return object
     */
    public function initModel($id = null) {
        $model = new Tasks;
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
            $data->sprint_id = H_handleRequest($request, 'sprint_id'); 
            $data->work_id = $request['work_id']; 
            $data->status = $request['status']; 
            $data->name = $request['name']; 
            $data->description = $request['description']; 
            $data->start_date = H_handleRequest($request, 'start_date'); 
            $data->end_date = H_handleRequest($request, 'end_date'); 
            $data->actual_start_date = H_handleRequest($request, 'actual_start_date'); 
            $data->actual_end_date = H_handleRequest($request, 'actual_end_date'); 
            $data->labels = H_handleRequest($request, 'labels'); 
            $data->ordering = H_handleRequest($request, 'ordering', 1); 
            $data->mandays = H_handleRequest($request, 'mandays'); 
            $data->mandays_actual = H_handleRequest($request, 'mandays_actual'); 
            $data->is_overtime = H_handleRequest($request, 'is_overtime'); 
            $data->is_done = H_handleRequest($request, 'is_done');
            $data->progress = H_handleRequest($request, 'progress', 0); 
            $data->created_by = H_handleRequest($request, 'created_by'); 
            $data->updated_by = H_handleRequest($request, 'updated_by'); 
            $data->deleted_by = H_handleRequest($request, 'deleted_by'); 

            if ($id) $data->updated_by = H_handleRequest($request, 'updated_by', H_JWT_getUserId($raw_request)); 
            else $data->created_by = H_handleRequest($request, 'created_by', H_JWT_getUserId($raw_request));

            $data->save();


            if (isset($request['assignees'])) {
                $assign = $this->taskAssigneesRepository->assign($data->id, $request['assignees']);
                $data->assignees = $assign;
            }

            // logging injection
            $data->log_name = 'tasks';
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
        
        $this->logger($data, $id, 'task-started'); 

        return $data;
    }

    public function setCompleted($id) {
        $data = $this->initModel($id);
        $data->actual_end_date = H_getCurrentDate(true);
        $data->save();
        
        $this->logger($data, $id, 'task-completed');

        return $data;
    }

    public function setDone($id) {
        $data = $this->initModel($id);
        $data->is_done = true;
        $data->save();
        
        $this->logger($data, $id, 'task-done');

        return $data;
    }

    public function move($raw_request, $id = null) {
        try {
 
            $request = $raw_request->all();

            $data = $this->initModel($id);

            //storing defined property    
            $data->status = $request['status']; 
            $data->ordering = H_handleRequest($request, 'ordering', 1); 

            if ($id) $data->updated_by = H_handleRequest($request, 'updated_by', H_JWT_getUserId($raw_request)); 
            else $data->created_by = H_handleRequest($request, 'created_by', H_JWT_getUserId($raw_request));

            $data->save();

            // logging injection
            $data->log_name = 'tasks';
            $this->logger($data, $id, H_handleRequest($request, 'log_type'));

            // event( new \App\Events\FixOrderingTaskEvent($request['column_update']));
    
            return $data;

        } catch (Exception $e){ 
            throw new Exception($e->getMessage());
        } 
    }

    public function fixOrdering($raw_request, $id = null) {
        try {
 
            $request = $raw_request->all();
            $list = H_toArrayObject($request['data']);
 
            $no = 0;
            $status = $request['status'];
            foreach ($list as $key => $tasks) {
                Tasks::where('id', $tasks->id)
                ->whereStatus($status)
                ->update([
                    'ordering' => $no
                ]);
                $no++;
            }
            return null;

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

    public function board ($raw_request, $project_id, $relations = null) {
        try {
            $payload = $raw_request->all();
            $data = $this->model;

            $select = [
                'tasks.id',
                'tasks.project_id',
                'tasks.name',
                'tasks.status',
                'tasks.is_done',
                'tasks.is_overtime',
                'tasks.ordering',
                'tasks.labels',
            ];
            
            $data = $data->select($select);
            $data = $data->with('TaskAssignees');
            $data = $this->searchable($data, $payload);
            $data = $this->dynamicList($data, $payload);

            // dd(H_toArrayObject($data));

            $collection = collect($data);
            $grouped = $collection->groupBy('status_name');
            $grouped->all();

            $fix = [];
            foreach ($grouped as $key => $value) {
                if (count($value) > 0) {
                    $obj = [
                        "id" => $value[0]->status,
                        "name" => $key,
                        "tasks" => $value,
                    ];
                    $fix[] = $obj;
                }
                
            }

            return $fix;

          } catch (Exception $e){
            throw new Exception($e->getMessage());
          }
    }

    public function report ($raw_request, $project_id, $relations = null) {
        try {
            $payload = $raw_request->all();
            $data = $this->findAll($raw_request, true);
            $data = $data->whereProjectId($project_id);
            
            if ($relations) $data = $data->with($relations);
            if (H_hasRequest($payload, 'work_id')) $data = $data->whereWorkId($payload['work_id']);

            $data = $this->periodical($data, $payload);
            
            $data = $data->orderBy('start_date', 'ASC');
            $data = $data->get();

            return $data;
          } catch (Exception $e){
            throw new Exception($e->getMessage());
          }
    }

    public function myTask($raw_request, $userId) {
        try {
            $payload = $raw_request->all();
            $data = $this->model;
            $data = $data->where('is_done' , 0);
            $data = $data->orderBy('start_date' , 'ASC');
            $data = $data->with([
                // 'TaskStatus'
            ]);

            $projectId = $payload['project_id'];

            $select = [
                'tasks.id',
                'tasks.project_id',
                'tasks.name',
                'tasks.start_date',
                'tasks.end_date',
                'tasks.actual_start_date',
                'tasks.actual_end_date',
                'tasks.status',
                'tasks.labels',
                'tasks.is_overtime',
            ];

            $data = $data->join('task_assignees', function($query) use($userId, $projectId) {
                $query->on('task_assignees.task_id', '=', 'tasks.id');
                $query = $query->where('user_id', $userId);
                $query = $query->where('project_id', $projectId);
                $query = $query->whereNull('task_assignees.deleted_at');
            });

            $data = $data->select($select);
            $data = $this->searchable($data, $payload, 'tasks');
            $data = $this->dynamicList($data, $payload);

            return $data;
        //   $data = $this->findAll($raw_request, true);
        //   $data = $data->whereUserId($userid);

        //   if ($relations) $data = $data->with($relations);
    
        //   return $this->dynamicList($data, $payload);
        } catch (Exception $e){
          throw new Exception($e->getMessage());
        }
    }
    
}
