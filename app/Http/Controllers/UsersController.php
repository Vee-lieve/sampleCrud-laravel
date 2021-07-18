<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
   /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function createUser(Request $request) {

       $validation = Validator::make($request->all(), [
        'first_name'=> 'required',
        'last_name'=> 'required',
        'username'=> 'required',
        'email_address'=> 'required|email|unique:users', //required, email type and unique
        'type'=> 'required',
        'password'=> 'required',
       ]);

       $response = [];              //global variable (for error and success)
       if ($validation->fails()) {
           $response["erors"] = $validation->errors();  //store errors in the response var 
           $response["code"] = 400;
       }else {
            DB::beginTransaction();  //query builder
            try {
                $data = $request->all();      //store
                $data["password"] = Hash::make($data["password"]); //encrypt password
                $user = User::create($data);     //create data 
                $response["message"] = "Successfully added"; 
                $response["last inserted id"] = $user->id;     //
                $response["code"] = 201;
                
                DB::commit(); 
            } 
            //errors
           catch(\Exception $e) {   
               DB::rollBack();
               $response["errors"] = "The user was not created. .$e";  
               $response["code"] = 400;
           }
       }

       return response($response, $response["code"]);   //$response(message, last inserted id, errors)
   }
}
