<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use App\Exports\ExportFromArray;
use Illuminate\Http\Request;
use App\Repositories\SprintsRepository;
use App\Providers\HelperProvider;
use App\Providers\AuthProvider;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class SprintsController extends Controller
{
    protected $repository;
    protected $request;

    public function __construct(
        Request $request,
        SprintsRepository $repository
    ){
        $this->request = $request;
        $this->repository = $repository;
    }
    
    public function index(Request $request) {
        AuthProvider::has($request, 'sprints-browse');
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
        AuthProvider::has($request, 'sprints-read');
        try {
            $data = $this->repository->findAll($request, true, [
                'Works',
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ])->find($id);
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function store(Request $request, $id = null) {
        if ($id) AuthProvider::has($request, 'sprints-update');
        else AuthProvider::has($request, 'sprints-create');
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
        AuthProvider::has($request, 'sprints-restore');
        try {
            $data = $this->repository->restore($request, $id);
            return H_apiResponse($data, 'Data has successfully restored');
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function remove(Request $request, $id) {
        AuthProvider::has($request, 'sprints-delete');
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

        $fileName = 'Sprints-'.H_getCurrentDate();
        return Excel::download($export, ''.$fileName.'.csv');
    }

    // extend
    public function getByWork(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->repository->getByWork($request, $id, [
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function setStarted(Request $request, $id = null) {
        try {
            $data = $this->repository->setStarted($id);
            return H_apiResponse($data, 'Sprint started');
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function setCompleted(Request $request, $id = null) {
        try {
            $data = $this->repository->setCompleted($id);
            return H_apiResponse($data, 'Sprint completed');
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
                    'work_id' => 'required',  
                    'name' => 'required',  
                    'goals' => 'required',  
                    'status' => 'required',  

                ],
                [
                    'project_id.required' => 'project_id is required',  
                    'work_id.required' => 'work_id is required',  
                    'name.required' => 'name is required',  
                    'goals.required' => 'goals is required',  
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
  
        