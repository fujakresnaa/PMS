<?php

namespace App\Repositories;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TaskStatusRepository;
use App\Repositories\TasksRepository;
use App\Models\TaskStatus;
use App\Models\Tasks;
use App\Validators\TaskStatusValidator;
use Exception;
use Carbon\Carbon;

use App\Providers\HelperProvider;

use App\Traits\StandardRepo;

class TaskStatusRepositoryEloquent extends BaseRepository implements TaskStatusRepository
{
    use StandardRepo;

    protected $log;
    protected $tasksRepository;

    public function __construct(
        Application $app,
        TasksRepository $tasksRepository,
        ActivityRepository $log
    ){
        parent::__construct($app);

        $this->log = $log;
        $this->tasksRepository = $tasksRepository;
    }


    public function model() {
        return TaskStatus::class;
    }

    /**
     * Model initiate
     * @return object
     */
    public function initModel($id = null) {
        $model = new TaskStatus;
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
            $data->name = $request['name']; 
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

    public function board($raw_request, $project_id, $relations = null) {
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

    public function board2($raw_request, $project_id, $relations = null) {
        try {
            $payload = $raw_request->all();
            $data = $this->findAll($raw_request, true);
            $data = $data->orderBy('ordering', 'ASC');
            $data = $data->whereProjectId($project_id);
            $data = $data->get();

            $fix = [];
            foreach ($data as $key => $sts) {
                $obj = $sts;
                $obj->tasks = $this->tasksRepository
                                    ->findAll($raw_request, true)
                                    ->whereStatus($sts->id)
                                    ->orderBy('ordering', 'ASC')
                                    ->get();
                $fix[] = $obj;
            }

            return $fix;
        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function board3($raw_request, $project_id, $relations = null) {
        try {
            $payload = $raw_request->all();
            $data = $this->findAll($raw_request, true);
            $data = $data->orderBy('ordering', 'ASC');
            $data = $data->whereProjectId($project_id);
            $data = $data->get();

            $fix = [];
            foreach ($data as $key => $sts) {
                $obj = $sts;
                $obj->props = [ 
                    'className' => 'card-container',
                    'orientation' => 'vertical',
                ];
                $obj->type = 'container';

                $tasks = $this->tasksRepository
                        ->findAll($raw_request, true)
                        ->whereStatus($sts->id)
                        ->with(['Assignees'])
                        ->get();

                $tasksFix = [];
                $no = 0;
                foreach ($tasks as $key2 => $tsk) {
                    $obj2 = $tsk;
                    $obj2->ordering = $no;

                    $obj2->props = [ 
                        'className' => 'card',
                        'style' => [
                            'backgroundColor' => '#ffffff'
                        ],
                    ];
                    $obj2->type = 'draggable';
                    $obj2->data = $tsk->description;
                    $obj2->indicator = 'default';

                    $today = strtotime(H_getCurrentDate());
                    $start = strtotime(H_formatDate($obj2->start_date, 'Y-m-d'));
                    $end = strtotime(H_formatDate($obj2->end_date, 'Y-m-d'));

                    if ($start === $today) $obj2->indicator = 'today';
                    if ($obj2->start_date != null && $start < $today) $obj2->indicator = 'due';
                    if ($obj2->end_date != null && $end < $today) $obj2->indicator = 'over';

                    if ($obj2->actual_start_date !== null && $obj2->actual_end_date !== null) $obj2->indicator = 'default';

                    $tasksFix[] = $obj2;
                    $no++;

                    
                }

                $obj->children = $tasksFix;

                $fix[] = $obj;
            }

            $res = [
                'type' => 'container',
                'props' => [
                    'orientation' => 'container'
                ],
                'children' => $fix,
            ];

            return $res;
        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

}
