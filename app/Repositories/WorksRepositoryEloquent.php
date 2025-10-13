<?php

namespace App\Repositories;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WorksRepository;
use App\Models\Works;
use App\Models\WorkLabels;
use App\Models\Projects;
use App\Validators\WorksValidator;
use Exception;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

use App\Providers\HelperProvider;

use App\Traits\StandardRepo;

class WorksRepositoryEloquent extends BaseRepository implements WorksRepository
{
    use StandardRepo;

    protected $log;
    protected $collapsedTimeline;
    protected $roadmapRelations;

    public function __construct(
        Application $app,
        ActivityRepository $log
    ){
        parent::__construct($app);

        $this->log = $log;
        $this->collapsedTimeline = true;
        $this->roadmapRelations = [
            'Tasks',
            'Tasks.TaskAssignees',
        ];
    }


    public function model() {
        return Works::class;
    }

    /**
     * Model initiate
     * @return object
     */
    public function initModel($id = null) {
        $model = new Works;
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
            $data->related = H_handleRequest($request, 'related');
            // if ($data->related)
            $data->name = H_handleRequest($request, 'name'); 
            $data->summary = H_handleRequest($request, 'summary'); 
            $data->description = H_handleRequest($request, 'description'); 
            $data->priority = $request['priority']; 
            $data->status = $request['status']; 
            $data->labels = H_handleRequest($request, 'labels'); 
            $data->is_module = H_handleRequest($request, 'is_module', 0); 
            $data->is_done = H_handleRequest($request, 'is_done', 0); 
            $data->start_date = H_handleRequest($request, 'start_date'); 
            $data->end_date = H_handleRequest($request, 'end_date'); 
            $data->actual_start_date = H_handleRequest($request, 'actual_start_date'); 
            $data->actual_end_date = H_handleRequest($request, 'actual_end_date'); 
            $data->mockup_link = H_handleRequest($request, 'mockup_link'); 
            $data->flow_link = H_handleRequest($request, 'flow_link'); 
            $data->progress = H_handleRequest($request, 'progress', 0); 
            $data->created_by = H_handleRequest($request, 'created_by'); 
            $data->updated_by = H_handleRequest($request, 'updated_by'); 
            $data->deleted_by = H_handleRequest($request, 'deleted_by'); 


            if ($id) $data->updated_by = H_handleRequest($request, 'updated_by', H_JWT_getUserId($raw_request)); 
            else $data->created_by = H_handleRequest($request, 'created_by', H_JWT_getUserId($raw_request)); 
    
            $data->save();

            // logging injection
            $data->log_name = 'works';
            $this->logger($data, $id, H_handleRequest($request, 'log_type'));
    
            return $data;

        } catch (Exception $e){ 
            throw new Exception($e->getMessage());
        } 
    }

    public function setStarted($id) {
        $data = $this->initModel($id);
        $data->status = 'RUNNING';
        $data->actual_start_date = H_getCurrentDate(true);
        $data->save();
        
        $this->logger($data, $id, 'work-started');

        return $data;
    }

    public function setCompleted($id) {
        $data = $this->initModel($id);
        $data->status = 'COMPLETED';
        $data->actual_end_date = H_getCurrentDate(true);
        $data->save();
        
        $this->logger($data, $id, 'work-completed');

        return $data;
    }

    public function setDone($id) {
        $data = $this->initModel($id);
        $data->is_done = true;
        $data->save();
        
        $this->logger($data, $id, 'work-done');

        return $data;
    }

    // add your customize function

    public function getByProject($raw_request, $project_id, $relations = null) {
        try {
          $payload = $raw_request->all();
          $data = $this->findAll($raw_request, true);
        //   $data = $data->whereProjectId($project_id); // di handle di UI jadinya, agar tidak tercampur
          $data = $data->orderBy('start_date', 'DESC');

          if ($relations) $data = $data->with($relations);
    
          return $this->dynamicList($data, $payload);
        } catch (Exception $e){
          throw new Exception($e->getMessage());
        }
    }

    public function related($raw_request, $id, $type, $relations = null) { // $type = head | sub
        try {
            $payload = $raw_request->all();
            $data = $this->findAll($raw_request, true);
            $data = $data->whereId($id);
            
            if ($relations) $data = $data->with($relations);
            $data = $data->first();

            if ($data) {
                $related = $this->model;
                $data->related_works = [];
                if ($type == 'sub') {
                    $related = $related->whereJsonContains('related', (int) $id);
                    $data->related_works = $related;
                }
                else {
                    if ($data->related) {
                        $related = $related->whereIn('id', $data->related);
                        $related = $related->get();
                        $data->related_works = $related;
                    }
                }
                
            }

            return $data;
        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function roadmaps($raw_request, $project_id, $subName = 'sub') {
        try {
            $payload = $raw_request->all();
            $data = $this->model;
            $data = $data->with($this->roadmapRelations);
            $data = $data->whereProjectId($project_id);

            $from = Carbon::now()->setMonth(1)->endOfMonth()->toDateString();
            $to = Carbon::now()->setMonth(12)->endOfMonth()->toDateString();
            if (H_hasRequest($payload, 'from')) {
                $from = $payload['from'];
                if (H_hasRequest($payload, 'to')) $to = $payload['to'];
            }

            // $data = $data->where('start_date', '>=', $from);
            // $data = $data->where('end_date', '<=', $to);

            $data = $data->where('start_date', '<=', $to);
            $data = $data->where('end_date', '>=', $from);

            $data = $data->whereRaw('related IS NULL');
            $data = $data->orderBy('start_date', 'ASC');
            $data = $data->get();

            $fix = [];
            foreach ($data as $key => $work) {
                $obj = $work;
                $obj->label = $work->name;
                $obj->{$subName} = $this->getSubWork($work->id, $subName);
                $fix[] = $obj;
            }

            $collection = collect($fix);
            $grouped = $collection->groupBy('periode_start');
            $grouped->all();

            return $grouped;

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function timeline($raw_request, $project_id, $subName = 'sub') {
        try {
            $payload = $raw_request->all();
            $payload = $raw_request->all();
            $data = $this->model;
            $data = $data->with('Tasks');
            $data = $data->whereProjectId($project_id);

            $from = Carbon::now()->setMonth(1)->endOfMonth()->toDateString();
            $to = Carbon::now()->setMonth(12)->endOfMonth()->toDateString();
            if (H_hasRequest($payload, 'from')) {
                $from = $payload['from'];
                if (H_hasRequest($payload, 'to')) $to = $payload['to'];

                $data = $data->where('end_date', '<=', $to);
                $data = $data->where('start_date', '>=', $from);
            }

            if (H_hasRequest($payload, 'work_id')) {
                $data = $data->where('id', $payload['work_id']);
                $this->collapsedTimeline = false;
            } else {
                $data = $data->where(function ($query)  use ($project_id){
                    $query->whereRaw('related IS NULL')
                          ->whereNotNull('start_date')
                          ->whereProjectId($project_id);
                })->orWhere(function($query) use ($project_id) {
                    $query->whereJsonLength('related', 0)
                    ->whereNotNull('start_date')
                    ->whereProjectId($project_id);
                });

                $data = $data->orderBy('start_date', 'ASC');
            }
            $data = $data->get();

            
            $fix = [];
            foreach ($data as $key => $work) {
                $obj = $work;
                $obj->label = $work->name;
                $obj->{$subName} = $this->getSubWork($work->id, $subName);
                $fix[] = $obj;
            }

            $fix = $this->setToRootWorks($fix, $subName);
            if (H_hasRequest($payload, 'with_task')) $fix = $this->setToRootTasks($fix);
            // $fix= H_toArrayObject($fix);

            return $fix;

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    function setToRootWorks($arr, $childrenName = 'sub') {
        $res = [];
        foreach($arr as $r) {
            if (count($r[$childrenName])) {
                $res[] = $this->formatGantt($r);
                $res = array_merge($res, $this->setToRootWorks($r[$childrenName]));
            } else {
                $res[] = $this->formatGantt($r);
            }
        }
        return $res;
    }

    function setToRootTasks($arr) {
        $res = [];
        foreach($arr as $r) {
            // dd($r);
            if (count($r->tasks)) {
                $res[] = $r;
                foreach ($r->tasks as $key => $task) {
                    // dd($task);
                    $res[] = $this->formatGanttTask($task);
                }
                // unset($r->tasks);
                
            } else $res[] = $r;
        }
        return $res;
    }

    function getRelated($id) {
        try {

            $res = [];
                           
            $data = $this->model;
            $data = $data->with($this->roadmapRelations);
            $data = $data->whereJsonContains('related', [$id]);
            $data = $data->orderBy('start_date', 'ASC');
            $data = $data->get();

            foreach($data as $r) {
                array_merge($res, $this->getRelated($r->id));
            }

            return $res;

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    function formatGantt($work, $no = 0) {
        $obj =$work;
        $obj = ['id' => 'W'.$work->id];
        $obj = H_toArrayObject($obj);
        $obj->tasks = $work->tasks;
        $obj->project_id = $work->project_id;
        $obj->work_id = ($work->work_id) ? $work->work_id : $work->id;
        $obj->parentId = ($work->parent) ? 'W'.$work->parent : null;
        $obj->label = $work->name;
        $obj->user = '<a href="https://www.google.com/search?q=John+Doe" target="_blank" style="color:#0077c0">John Doe</a>';
        $obj->start = H_formatDate($work->start_date, 'Y-m-d');
        $obj->duration = H_getGanttDuration($work->total_day);
        $obj->percent = ($work->progress) ? $work->progress : 0;
        $obj->type = ($work->is_module) ? 'milestone' : 'project';
        $obj->host = H_getServerInfo('host');
        $obj->source = 'works';
        $obj->source_id = $work->id;
        $obj->collapsed = $this->collapsedTimeline;
        $obj->style = [
            'base' => [
                'fill' =>  ($work->is_module) ? '#0c9134' : '#841bc2',
                'stroke' => ($work->is_module) ? '#0c9134' : '#841bc2',
            ]
        ];
        return $obj;
    }

    function formatGanttTask($task, $no = 0) {
        // $obj =$task;
        // dd($task);
        $obj = ['id' => 'T'.$task->id];
        $obj = H_toArrayObject($obj);
        $obj->parentId = ($task->work_id) ? 'W'.$task->work_id : null;
        $obj->project_id = $task->project_id;
        $obj->work_id = $task->work_id;
        $obj->label = $task->name;
        $obj->user = '<a href="https://www.google.com/search?q=John+Doe" target="_blank" style="color:#0077c0">John Doe</a>';
        $obj->start = H_formatDate($task->start_date, 'Y-m-d');
        $obj->duration = H_getGanttDuration($task->total_day);
        $obj->percent = ($task->progress) ? $task->progress : 0;
        $obj->type = 'task';
        $obj->host = H_getServerInfo('host');
        $obj->source = 'tasks';
        $obj->source_id = $task->id;
        $obj->collapsed = true;
        $obj->style = [
            'base' => [
                'fill' => '#b56707',
                'stroke' => '#b56707',
            ]
        ];

        return $obj;
    }


    function getSubWork($id, $subName = 'sub') {
        try {
            $data = $this->model;
            $data = $data->with($this->roadmapRelations);
            $data = $data->whereJsonContains('related', [$id]);
            $data = $data->orderBy('start_date', 'ASC');
            $data = $data->get();

            // dd($id, $data);

            $fix = [];
            foreach ($data as $key => $work) {
                $haveSub = $this->model->whereJsonContains('related', [$id])->count();
                $obj = $work;
                $obj->parent = $id;
                $obj->label = $work->name;
                $obj->{$subName} = [];
                if ($haveSub > 0) $obj->{$subName} = $this->getSubWork($work->id, $subName);
                $fix[] = $obj;
            }

            return $fix;

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function propose($raw_request, $work_id, $subName = 'sub') {
        try {
            $payload = $raw_request->all();
            $data = $this->model;
            $data = $data->with($this->roadmapRelations);
            $data = $data->whereId($work_id);

            // $data = $data->whereRaw('related IS NULL');
            $data = $data->orderBy('name', 'ASC');
            $works = $data->first();

            $works->{$subName} = $this->getSubWork($works->id, $subName);

            // $collection = collect($fix);
            // $grouped = $collection->groupBy('start_date');
            // $grouped->all();


            $res = null;
            if ($works) {
                $project = Projects::find($works->project_id);
                $res = [
                    "project" => $project,
                    "works" => $works,
                ];
            }
           

            return $res;

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    
    public function reportTask($raw_request, $work_id) {
        try {
            $payload = $raw_request->all();

            
            $data = $this->model;
            $data = $data->whereId($work_id);
            
            $data = $data->with([
                // 'Project',
                // 'Tasks.Assignees',
                // 'createdByUser',
                // 'updatedByUser',
                // 'deletedByUser',
            ]);

            // filter date
            $from = Carbon::now()->firstOfMonth()->toDateString();
            $to = Carbon::now()->endOfMonth()->toDateString();
            if (H_hasRequest($payload, 'from')) {
                $from = $payload['from'];
                if (H_hasRequest($payload, 'to')) $to = $payload['to'];
            }

            $data = $data->with('TaskReport', function ($q) use($from, $to) {
                $q->where('tasks.start_date', '<=', $to);
                $q->where('tasks.end_date', '>=', $from);
                $q->orderBy('tasks.start_date', 'ASC');
            });


            // $data = $data->toSql();
            $data = $data->first();
            // dd(H_toArrayObject($data));

            return $data;

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    

}
