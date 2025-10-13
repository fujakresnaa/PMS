<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FileHandlingController extends Controller
{
    protected $request;

    public function __construct(
        Request $request
    ){
        $this->request = $request;
    }
    
    public function uploadImage(Request $request) {
        try {
            $photo = H_uploadFile($request, 'photo', '/test'); // define file photo uploader
            $msg = ($photo) ? $photo['message'] : 'Error';
            return H_apiResponse($photo, $msg);

        } catch (Exception $e){
            return H_apiResError($e);
        }

        
    }

}
  
        