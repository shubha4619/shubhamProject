<?php

namespace App\Http\Controllers;

use App\Models\StoreMeetLink;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class StoreMeetLinkController extends Controller
{


    public function sendError($error, $errorMessages = [], $code = 200)
    {
    	$response = [
            'status' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }

    public function store(Request $request){
          

        $validator = Validator::make($request->all(), [
            'title' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first() );
        }

        $data  = new StoreMeetLink();
    }
}
