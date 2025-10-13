<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use App\Exports\ExportFromArray;
use Illuminate\Http\Request;
use App\Repositories\TasksRepository;
use App\Providers\HelperProvider;
use App\Providers\AuthProvider;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class TasksController extends Controller
{
    protected $repository;
    protected $request;

    public function __construct(
        Request $request,
        TasksRepository $repository
    ){
        $this->request = $request;
        $this->repository = $repository;
    }
    
    public function index(Request $request) {
        AuthProvider::has($request, 'tasks-browse');
        try {
            $payload = $request->all();
            $data = $this->repository->getList($request);
            if (isset($payload['csv'])) return $this->exportCSV($data);
            else return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function findById(Request $request, $id) {
        AuthProvider::has($request, 'tasks-read');
        try {
            $data = $this->repository->findById($request, $id, false, [
                'Sprints',
                'Works',
                'TaskStatus',
                'Assignees',
                // 'TaskAssignees',
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function store(Request $request, $id = null) {
        if ($id) AuthProvider::has($request, 'tasks-update');
        else AuthProvider::has($request, 'tasks-create');
        try {
            DB::beginTransaction();
            $validate = $this->validateStore($request, $id);
            if($validate['result']) {
                $data = $this->repository->store($request, $id);
                $msg = 'succes saving data';
                if ($id) $msg = 'success update data';
                DB::commit();
                return H_apiResponse($data, $msg);
            } else {
                return H_apiResponse(null, $validate['message'], 400);
            }
        } catch (Exception $e){
            DB::rollback();
            return H_apiResError($e);
        }
    }

    public function restore(Request $request, $id = null) {
        AuthProvider::has($request, 'tasks-restore');
        try {
            $data = $this->repository->restore($request, $id);
            return H_apiResponse($data, 'Data has successfully restored');
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function remove(Request $request, $id) {
        AuthProvider::has($request, 'tasks-delete');
        try {
            $payload = $request->all();
            $data = $this->repository->remove($request, $id);
            $msg = 'success deleted data';
            if(isset($payload['permanent'])) $msg = $msg . ' permanently';
            return H_apiResponse($data, $msg);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function exportCSV($data) {
        $data = H_toArrayObject($data);
        $export = new ExportFromArray($data);

        $fileName = 'Tasks-'.H_getCurrentDate();
        return Excel::download($export, ''.$fileName.'.csv');
    }

    public function getByProject(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->repository->getByProject($request, $id, [
                'Sprints',
                'Works',
                'TaskStatus',
                'Assignees',
                // 'TaskAssignees',
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function getByWork(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->repository->getByWork($request, $id, [
                'Sprints',
                'Works',
                'TaskStatus',
                'Assignees',
                // 'TaskAssignees',
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function board(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->repository->board($request, $id, [
                // 'Sprints',
                // 'Works',
                // 'TaskStatus',
                // 'Assignees',
                // // 'TaskAssignees',
                // 'createdByUser',
                // 'updatedByUser',
                // 'deletedByUser',
            ]);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function setStarted(Request $request, $id = null) {
        try {
            $data = $this->repository->setStarted($id);
            return H_apiResponse($data, 'Task started');
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function setCompleted(Request $request, $id = null) {
        try {
            $data = $this->repository->setCompleted($id);
            return H_apiResponse($data, 'Task completed');
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function setDone(Request $request, $id = null) {
        try {
            $data = $this->repository->setDone($id);
            return H_apiResponse($data, 'Task has set to Done');
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function myTask(Request $request, $id = null) {
        try {
            $data = $this->repository->myTask($request, $id);
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function move(Request $request, $id = null) {
        try {
            $data = $this->repository->move($request, $id);
            $msg = 'succes moving task';
            if ($id) $msg = 'success moving task';
            return H_apiResponse($data, $msg);

        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function fixOrdering(Request $request) {
        try {
            $data = $this->repository->fixOrdering($request);
            return H_apiResponse($data, 'Ordering Tasks updated');
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }


    // with views

    public function report(Request $request, $project_id) {
        try {
            $payload = $request->all();
            $data = $this->repository->report($request, $project_id, [
                'Projects',
                'Works',
                'TaskStatus',
                'Assignees',
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);

            $colect = null;
            if (count($data) != 0) {
                $data = H_toArrayObject($data);
                $colect['tasks'] = $data;
                $colect['project'] = $data[0]->projects;
                $colect['work'] = null;
                if (H_hasRequest($payload, 'work_id')) $colect['work'] = $data[0]->works;

                $colect['from'] = H_handleRequest($payload, 'from'); ;
                $colect['to'] = H_handleRequest($payload, 'to');
                $colect = H_toArrayObject($colect);
            }

            $send['data'] = $colect;

            return view('reports.tasks', $send);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function preview(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->repository->findById($request, $id, false, [
                'Projects',
                'Sprints',
                'Works',
                'TaskStatus',
                'Assignees',
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);

            $send['data'] = $data;

            return view('apps.task-preview', $send);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    // Validator
    public function validateStore($request, $id = null) {
        try {
            $result = true;
            $message = '';
            $payload = $request->all();

            $validator = Validator::make( $request->all(),
                [
                    'project_id' => 'required',  
                    'status' => 'required',  
                    'name' => 'required',  
                    'description' => 'required',  

                ],
                [
                    'project_id.required' => 'project_id is required',  
                    'status.required' => 'status is required',  
                    'name.required' => 'name is required',  
                    'description.required' => 'description is required',  

                ]
            );
            if ($validator->fails()) {
                $message = $validator->messages()->first();
                $result = false;
            }

            if ($id != null && empty($this->repository->findById($request, $id))) {
                $message = 'Data not found';
                $result = false;
            }

            return [
                'result' => $result,
                'message' => $message,
            ];
        } catch (Exception $e){
            if(env('APP_DEBUG')) return H_apiResError($e);
            else {
                $msg = $e->getMessage();
                return H_apiResponse(null, $msg, 400);
            }
        }
    }

}
  
        