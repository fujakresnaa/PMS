<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use App\Exports\ExportFromArray;
use Illuminate\Http\Request;
use App\Repositories\MasterMenusRepository;
use App\Providers\HelperProvider;
use App\Providers\AuthProvider;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Users;
use App\Repositories\MenuItemsRepository;
use App\Repositories\PermissionsRepository;
use Illuminate\Support\Facades\DB;

class MasterMenusController extends Controller
{
    protected $repository;
    protected $menuItemRepository;
    protected $permissionRepository;
    protected $request;

    public function __construct(
        Request $request,
        MasterMenusRepository $repository,
        MenuItemsRepository $menuItemRepository,
        PermissionsRepository $permissionRepository
    ){
        $this->request = $request;
        $this->repository = $repository;
        $this->menuItemRepository = $menuItemRepository;
        $this->permissionRepository = $permissionRepository;
    }
    
    public function index(Request $request) {
        AuthProvider::has($request, 'master-menus-browse');
        try {
            $payload = $request->all();
            $data = $this->repository->getListFormater($request);
            if (isset($payload['csv'])) return $this->exportCSV($data);
            else return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function findById(Request $request, $id) {
        AuthProvider::has($request, 'master-menus-read');
        try {
            $data = $this->repository->getMenu($request, $id);
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function store(Request $request, $id = null) {
        DB::beginTransaction();
        // if ($id) AuthProvider::has($request, 'master-menus-update');
        // else AuthProvider::has($request, 'master-menus-create');

        try {
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

    public function initMenuAdmin() {
        $menu = '{"id":null,"name":"Admin","detail":[{"id":null,"parent_id":null,"menu_item_id":null,"master_menu_id":null,"overline":null,"ordering":null,"name":"Home","detail":{"id":1,"name":"Home","slug":"home","icon":"home","path":"/","created_by":1,"updated_by":null,"deleted_by":null,"created_at":"2021-05-27T09:36:51.000000Z","updated_at":"2021-05-27T09:36:51.000000Z","deleted_at":null}},{"id":null,"parent_id":null,"menu_item_id":null,"master_menu_id":null,"overline":null,"ordering":null,"name":"Master Data","detail":{"id":19,"name":"Master Data","slug":"master-data","icon":null,"path":"/master-data","created_by":1,"updated_by":null,"deleted_by":null,"created_at":"2021-05-27T09:36:51.000000Z","updated_at":"2021-05-27T09:36:51.000000Z","deleted_at":null},"children":[{"id":null,"parent_id":null,"menu_item_id":null,"master_menu_id":null,"overline":null,"ordering":null,"name":"Menu Items","detail":{"id":8,"name":"Menu Items","slug":"menu-items","icon":null,"path":"/menu-items","created_by":1,"updated_by":null,"deleted_by":null,"created_at":"2021-05-27T09:36:51.000000Z","updated_at":"2021-05-27T09:36:51.000000Z","deleted_at":null}},{"id":null,"parent_id":null,"menu_item_id":null,"master_menu_id":null,"overline":null,"ordering":null,"name":"Master Menus","detail":{"id":10,"name":"Master Menus","slug":"master-menus","icon":null,"path":"/master-menus","created_by":1,"updated_by":null,"deleted_by":null,"created_at":"2021-05-27T09:36:51.000000Z","updated_at":"2021-05-27T09:36:51.000000Z","deleted_at":null}},{"id":null,"parent_id":null,"menu_item_id":null,"master_menu_id":null,"overline":null,"ordering":null,"name":"Permissions","detail":{"id":5,"name":"Permissions","slug":"permissions","icon":null,"path":"/permissions","created_by":1,"updated_by":null,"deleted_by":null,"created_at":"2021-05-27T09:36:51.000000Z","updated_at":"2021-05-27T09:36:51.000000Z","deleted_at":null}},{"id":null,"parent_id":null,"menu_item_id":null,"master_menu_id":null,"overline":null,"ordering":null,"name":"Roles","detail":{"id":7,"name":"Roles","slug":"roles","icon":null,"path":"/roles","created_by":1,"updated_by":null,"deleted_by":null,"created_at":"2021-05-27T09:36:51.000000Z","updated_at":"2021-05-27T09:36:51.000000Z","deleted_at":null}},{"id":null,"parent_id":null,"menu_item_id":null,"master_menu_id":null,"overline":null,"ordering":null,"name":"Users","detail":{"id":3,"name":"Users","slug":"users","icon":null,"path":"/users","created_by":1,"updated_by":null,"deleted_by":null,"created_at":"2021-05-27T09:36:51.000000Z","updated_at":"2021-05-27T09:36:51.000000Z","deleted_at":null}}]},{"id":null,"parent_id":null,"menu_item_id":null,"master_menu_id":null,"overline":null,"ordering":null,"name":"Projects","detail":{"id":12,"name":"Projects","slug":"projects","icon":null,"path":"/projects","created_by":1,"updated_by":null,"deleted_by":null,"created_at":"2021-05-27T09:36:51.000000Z","updated_at":"2021-05-27T09:36:51.000000Z","deleted_at":null}}]}';

        $data = $this->repository->store(null, null, json_decode($menu, true));

        $user = Users::find(1);
        $user->menu_id = $data->id;
        $user->save();

        $msg = 'succes init data';
        return H_apiResponse($data, $msg);
    }

    public function initMenu($menu) {
        $data = $this->repository->store(null, null, json_decode($menu, true));
        return $data;
    }

    public function restore(Request $request, $id = null) {
        AuthProvider::has($request, 'master-menus-restore');
        try {
            $data = $this->repository->restore($request, $id);
            return H_apiResponse($data, 'Data has successfully restored');
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function remove(Request $request, $id) {
        AuthProvider::has($request, 'master-menus-delete');
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

        $fileName = 'MasterMenus-'.H_getCurrentDate();
        return Excel::download($export, ''.$fileName.'.csv');
    }

    public function addNewModule(Request $req) {
        DB::beginTransaction();
        try {
            if ($req->menu) {
                $menu = explode('|', $req->menu);
                $this->permissionRepository->generateNewModule($menu);
                $this->menuItemRepository->generateNewModule($menu);

                DB::commit();
                return H_apiResponse($menu);
            } else {
                return H_apiResponse(null, 'Params menu is required', 400);
            }
        } catch (Exception $e) {
            DB::rollBack();
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
                    'name' => 'required',
                    'detail' => 'required',

                ],
                [
                    'name.required' => 'name is required',
                    'detail.required' => 'menu items is required' 

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

    public function wbs() {
        $data = '{
            "name": "PMP",
            "start_date": "2021-06-20",
            "end_date": "2021-07-10",
            "periode": {
                "june": {
                    "start": 20,
                    "length": 11
                },
                "july": {
                    "start": 1,
                    "length": 10
                }
            },
            "block": 21,
            "task": [
                {
                    "id": 1,
                    "name": "Marketing",
                    "start_date": "2021-06-20",
                    "end_date": "2021-06-30",
                    "start": 0,
                    "length": 11,
                    "index": 0,
                    "activities": [
                        {
                            "date": "2021-06-25",
                            "start": 5,
                            "detail": [
                                {
                                    "name": "Deliver Modul",
                                    "description": "Deliver Customer Module",
                                    "date": "2021-06-25 22:30:00"
                                }
                            ]
                        }
                    ],
                    "children": [
                        {
                            "id": 2,
                            "name": "Customers",
                            "start_date": "2021-06-20",
                            "end_date": "2021-06-25",
                            "start": 0,
                            "length": 6,
                            "index": 1,
                            "children": [],
                            "activities": [
                                {
                                    "date": "2021-06-20",
                                    "start": 1,
                                    "detail": [
                                        {
                                            "name": "Setup Modul",
                                            "description": "Setup Customer Module",
                                            "date": "2021-06-20 09:00:00"
                                        }
                                    ]
                                },
                                {
                                    "date": "2021-06-21",
                                    "start": 1,
                                    "detail": [
                                        {
                                            "name": "Coding UI Modul",
                                            "description": "Coding Customer Module",
                                            "date": "2021-06-21 09:00:00"
                                        },
                                        {
                                            "name": "Coding API Modul",
                                            "description": "Coding Customer Module",
                                            "date": "2021-06-21 13:00:00"
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "id": 3,
                            "name": "Roles",
                            "start_date": "2021-06-25",
                            "end_date": "2021-06-30",
                            "start": 5,
                            "length": 6,
                            "index": 1,
                            "children": [],
                            "activities": []
                        }
                    ] 
                },
                {
                    "id": 4,
                    "name": "Users",
                    "start_date": "2021-07-01",
                    "end_date": "2021-07-10",
                    "start": 12,
                    "length": 10,
                    "index": 0,
                    "children": [],
                    "activities": [
                        {
                            "date": "2021-07-01",
                            "start": 12,
                            "detail": [
                                {
                                    "name": "Develop User",
                                    "description": "Develop user Module",
                                    "date": "2021-07-01 09:30:00"
                                }
                            ]
                        }
                    ]
                }
            ]
        }';

        return H_apiResponse(json_decode($data));
    }

}
  
        