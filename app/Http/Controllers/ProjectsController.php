<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use App\Exports\ExportFromArray;
use Illuminate\Http\Request;
use App\Repositories\ProjectsRepository;
use App\Repositories\WorksRepository;
use App\Providers\HelperProvider;
use App\Providers\AuthProvider;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class ProjectsController extends Controller
{
    protected $repository;
    protected $worksRepository;
    protected $request;

    public function __construct(
        Request $request,
        WorksRepository $worksRepository,
        ProjectsRepository $repository
    ){
        $this->request = $request;
        $this->repository = $repository;
        $this->worksRepository = $worksRepository;
    }
    
    public function index(Request $request) {
        try {
            $data = $this->repository->getProject($request, [
                'Members',
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);
            // $payload = $request->all();
            // $data = $this->repository->getList($request, [
            //     'Members',
            //     'createdByUser',
            //     'updatedByUser',
            //     'deletedByUser',
            // ]);
            if (isset($payload['csv'])) return $this->exportCSV($data);
            else return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function findById(Request $request, $id) {
        try {
            $data = $this->repository->findAll($request, true, [
                'Members',
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
        try {
            $data = $this->repository->restore($request, $id);
            return H_apiResponse($data, 'Data has successfully restored');
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function remove(Request $request, $id) {
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

        $fileName = 'Projects-'.H_getCurrentDate();
        return Excel::download($export, ''.$fileName.'.csv');
    }

    // integeration

    public function getWorks(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->worksRepository->getByProject($request, $id, [
                'Projects',
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);
            
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function getProjectDocs(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->repository->getProjectDocs($request, $id);

            $send['data'] = ($data) ? json_decode($data) : null;
            return view('docs.index', $send);
            // return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function getProjectDocsApi(Request $request, $id) {
        try {
            $payload = $request->all();
            $data = $this->repository->getProjectDocs($request, $id, 'api');

            $send['data'] = ($data) ? json_decode($data) : null;
            return view('docs.api', $send);
            // return H_apiResponse($data);
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

                ],
                [

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
  
        