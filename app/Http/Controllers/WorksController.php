<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use App\Exports\ExportFromArray;
use Illuminate\Http\Request;
use App\Repositories\WorksRepository;
use App\Providers\HelperProvider;
use App\Providers\AuthProvider;
use Maatwebsite\Excel\Facades\Excel;

class WorksController extends Controller
{
    protected $repository;
    protected $request;

    public function __construct(
        Request $request,
        WorksRepository $repository
    ){
        $this->request = $request;
        $this->repository = $repository;
    }
    
    public function index(Request $request) {
        AuthProvider::has($request, 'works-browse');
        try {
            $payload = $request->all();
            $data = $this->repository->getList($request, null, ['start_date', 'DESC']);
            if (isset($payload['csv'])) return $this->exportCSV($data);
            else return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function findById(Request $request, $id) {
        AuthProvider::has($request, 'works-read');
        try {
            $data = $this->repository->findById($request, $id, false, [
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
        if ($id) AuthProvider::has($request, 'works-update');
        else AuthProvider::has($request, 'works-create');
        try {
            $validate = $this->validateStore($request, $id);
            if($validate['result']) {
                $data = $this->repository->store($request, $id);
                $msg = 'succes saving data';
                if ($id) $msg = 'success update data';
                return H_apiResponse($data, $msg);
            } else {
                return H_apiResponse(null, $validate['message'], 400);
            }
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function restore(Request $request, $id = null) {
        AuthProvider::has($request, 'works-restore');
        try {
            $data = $this->repository->restore($request, $id);
            return H_apiResponse($data, 'Data has successfully restored');
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function remove(Request $request, $id) {
        AuthProvider::has($request, 'works-delete');
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

        $fileName = 'Works-'.H_getCurrentDate();
        return Excel::download($export, ''.$fileName.'.csv');
    }

    public function relatedSub(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->repository->related($request, $id, 'sub', [
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function relatedHead(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->repository->related($request, $id, 'head', [
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    // extend
    public function getByProject(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->repository->getByProject($request, $id, [
                'Project',
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function getByProject2(Request $request, $id) { // simple version fetching works
        try {
            $payload = $request->all();
            $data = $this->repository->getByProject($request, $id, [
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function roadmaps(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->repository->roadmaps($request, $id, 'children');
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function timeline(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->repository->timeline($request, $id);
            
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

    // with views

    public function report(Request $request, $id) {
        try {
            // dd('asd');
            $payload = $request->all();
            // $data = $this->repository->findById($request, $id, false, [
            //     'Project',
            //     'Tasks.Assignees',
            //     'createdByUser',
            //     'updatedByUser',
            //     'deletedByUser',
            // ]);

            $data = $this->repository->reportTask($request, $id);

            $send['data'] = $data;
            return view('reports.works', $send);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function propose(Request $request, $work_id) {
        try {
            $payload = $request->all();
            $data = $this->repository->propose($request, $work_id, 'children');

            $send['data'] = $data;
            return view('apps.propose', $send);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function brd(Request $request, $work_id) {
        try {
            $payload = $request->all();
            $data = $this->repository->propose($request, $work_id, 'children');

            $send['data'] = $data;
            return view('apps.brd', $send);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }


    public function preview(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->repository->findById($request, $id, false, [
                'Project',
                'Assignees',
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);

            $send['data'] = $data;

            return view('apps.work-preview', $send);
            
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
                    'priority' => 'required',  
                    'status' => 'required',  

                ],
                [
                    'project_id.required' => 'project_id is required',  
                    'priority.required' => 'priority is required',  
                    'status.required' => 'status is required',  

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
  
        