<?php

namespace App\Repositories;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProjectsRepository;
use App\Repositories\ProjectMembersRepository;
use App\Models\Projects;
use App\Models\WorkLabels;
use App\Models\TaskStatus;
use App\Models\TaskLabels;
use App\Models\ProjectConfig;
use App\Validators\ProjectsValidator;
use Exception;

use App\Providers\HelperProvider;

use App\Traits\StandardRepo;

class ProjectsRepositoryEloquent extends BaseRepository implements ProjectsRepository
{
    use StandardRepo;

    protected $log;
    protected $projectMembersRepository;

    public function __construct(
        Application $app,
        ProjectMembersRepository $projectMembersRepository,
        ActivityRepository $log
    ){
        parent::__construct($app);

        $this->log = $log;
        $this->projectMembersRepository = $projectMembersRepository;
    }


    public function model() {
        return Projects::class;
    }

    /**
     * Model initiate
     * @return object
     */
    public function initModel($id = null) {
        $model = new Projects;
        if (!empty($id)) $model = $this->model->where($this->model->getKeyName(), $id)->first();
        return $model;
    }

    public function store($raw_request, $id = null, $customRequest = null) {
        try {
 
            if ($customRequest === null) $request = $raw_request->all();
            else $request = $customRequest;

            $data = $this->initModel($id);

            //storing defined property    
            $data->code = H_handleRequest($request, 'code'); 
            $data->name = H_handleRequest($request, 'name'); 
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

            if (isset($request['members'])) {
                $assign = $this->projectMembersRepository->assign($data->id, $request['members']);
                $data->members = $assign;
            }

            if ($id == null) { // jika project baru dibuat, init default data
                $project = $data;

                $workLabel = WorkLabels::create(['project_id' => $project->id, 'name' => 'PROD', 'color' => '#1e9e3a' ]);
                $workLabel = WorkLabels::create(['project_id' => $project->id, 'name' => 'DEV', 'color' => '#3557bd' ]);
                $workLabel = WorkLabels::create(['project_id' => $project->id, 'name' => 'ANALYS', 'color' => '#ff9900' ]);
                $workLabel = WorkLabels::create(['project_id' => $project->id, 'name' => 'NEED ANALYSIS', 'color' => '#ff9900' ]);
                $workLabel = WorkLabels::create(['project_id' => $project->id, 'name' => 'ADDITIONAL', 'color' => '#ff9900' ]);

                $taskStatusTodo = TaskStatus::create(['project_id' => $project->id, 'name' => 'TODO' ]);
                $taskStatusProgress = TaskStatus::create(['project_id' => $project->id, 'name' => 'PROGRESS' ]);
                $taskStatus = TaskStatus::create(['project_id' => $project->id, 'name' => 'RETURNED' ]);
                $taskStatusReview = TaskStatus::create(['project_id' => $project->id, 'name' => 'REVIEW' ]);
                $taskStatusDone = TaskStatus::create(['project_id' => $project->id, 'name' => 'DONE' ]);
                $taskStatusDone = TaskStatus::create(['project_id' => $project->id, 'name' => 'SIT' ]);
                $taskStatusDone = TaskStatus::create(['project_id' => $project->id, 'name' => 'UAT' ]);
                $taskStatusDone = TaskStatus::create(['project_id' => $project->id, 'name' => 'PREPARE TO LIVE' ]);

                $TaskLabels = TaskLabels::create(['project_id' => $project->id, 'name' => 'API', 'color' => '#0089ca' ]);
                $TaskLabels = TaskLabels::create(['project_id' => $project->id, 'name' => 'UI', 'color' => '#ff9900' ]);
                $TaskLabels = TaskLabels::create(['project_id' => $project->id, 'name' => 'INTEGERATION', 'color' => '#10a160' ]);
                $TaskLabels = TaskLabels::create(['project_id' => $project->id, 'name' => 'BUGS', 'color' => '#751e9e' ]);
                $TaskLabels = TaskLabels::create(['project_id' => $project->id, 'name' => 'FINDINGS-SIT', 'color' => '#a3125b' ]);
                $TaskLabels = TaskLabels::create(['project_id' => $project->id, 'name' => 'FINDINGS-UAT', 'color' => '#8db00e' ]);

                ProjectConfig::create([ 'project_id' => $project->id, 'name' => 'task-done', 'value' => $taskStatusDone->id ]);
                ProjectConfig::create([ 'project_id' => $project->id, 'name' => 'task-required', 'value' => $taskStatusTodo->id ]);
                ProjectConfig::create([ 'project_id' => $project->id, 'name' => 'task-progress', 'value' => $taskStatusProgress->id ]);
                ProjectConfig::create([ 'project_id' => $project->id, 'name' => 'task-review', 'value' => $taskStatusReview->id ]);

            }

            return $data;

        } catch (Exception $e){ 
            throw new Exception($e->getMessage());
        } 
    }

    // add your customize function

    public function getProject ($raw_request, $relations = []) {
        try {
  
            $payload = $raw_request->all();
            $data = $this->model;
            // $data = $this->findAll($raw_request, true);
            if ($relations) $data = $data->with($relations);

            $select = [
                'projects.id',
                'projects.code',
                'projects.name',
                'projects.start_date',
                'projects.end_date',
                'projects.created_at as created_at',
                'project_members.created_at as created_at_member',
            ];
            
            // filter user
            if (H_hasRequest($payload, 'member')) {
                $select = [
                    'projects.id',
                    'projects.code',
                    'projects.name',
                    'projects.start_date',
                    'projects.end_date',
                    'project_members.user_id',
                ];

                $data = $data->join('project_members', function($query) use($payload) {
                    $query->on('projects.id', '=', 'project_members.project_id');
                    $query = $query->where('user_id', $payload['member']);
                });
            }

            $data = $data->select($select);
            $data = $this->searchable($data, $payload, 'projects');
            $data = $this->dynamicList($data, $payload);

            return $data;

        } catch (Exception $e){ 
            throw new Exception($e->getMessage());
        } 
    }

    public function getProjectDocs($raw_request, $id, $type = 'ui') {
        try {
            $payload = $raw_request->all();
            $data = $this->model;
            $data = $data->whereId($id);

            $with = [
                'Modules',
                'Modules.Tasks',
                'Modules.Tasks.Docs',
            ];
            if ($type != 'ui') {
                $with = [
                    'Modules',
                    'Modules.Tasks',
                    'Modules.Tasks.DocsApi',
                ];
            }
            $data = $data->with($with);
            $data = $data->first();
            return $data;
        } catch (Exception $e){
          throw new Exception($e->getMessage());
        }
    }

}
