<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use function GuzzleHttp\Promise\all;

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
            DB::beginTransaction();  //query builder for an interaction on database
            try {
                $data = $request->all();      //store data into the $data variable
                $data["password"] = Hash::make($data["password"]);  //encrypt password
                $user = User::create($data);     //create data 
                $response["message"] = "Successfully added"; 
                $response["last inserted id"] = $user->id;     //
                $response["code"] = 201;   //create data
                
                DB::commit(); 
            } 
            //errors
           catch(\Exception $e) {   
               DB::rollBack();   //
               $response["errors"] = "The user was not created. .$e";  
               $response["code"] = 400;
           }
       }

       return response($response, $response["code"]);   //$response(message, last inserted id, errors)
   }

   public function getUser() {

        $response = [];

        try {
            $users = User::all();           //check all the values of users
            $response["users"] = $users;   //stores the value of $users to the $response var
            $response["code"] = 200;    //get data
       }

       catch (\Exception $e) {
           $response["errors"] = "No users found. .$e";
           $response["code"] = 400;
       }

       return response($response, $response["code"]);
   }

   public function getUserByID($id) {

        $response = [];

        try {
            //query builder
            // $user = User::where('id', $id)->get();      
            $user = User::findOrFail($id);             //other way to find user by id, built in method
            $response["user"] = $user;
            $response["code"] = 200;
        }

        catch(\Exception $e) {
            $response["errors"] = "No user found. .$e";
            $response["code"] = 400;
    }

        return response($response, $response["code"]);
   }
   
   public function deleteUserById($id) {

        $response = [];

        try {
            $user = User::findOrFail($id)->delete();
            $response["user"] = $user;
            $response["message"] = "Successfully deleted.";
            $response["code"] = 200;
        }

        catch(\Exception $e) {
            $response["errors"] = "No user to be deleted. .$e";
            $response["code"] = 400;
        }

        return response($response, $response["code"]);
   }

   public function updateUserById(Request $request, $id) {

        $validation = Validator::make($request->all(), [
            'first_name'=> 'required',
            'last_name'=> 'required',
            'username'=> 'required',
            'email_address'=> 'required|email|unique:users'
        ]);
        
        $response = [];
        if ($validation->fails()) {
            $response["erors"] = $validation->errors();
            $response["code"] = 400;
        } else {
            DB::beginTransaction();
            try {   
                $data = $request->all();
                $data["password"] = Hash::make(($data["password"]));
                // $user = User::where('id', $id)->update($data);
                $user = User::findOrFail($id)->update($data);
                $response["message"] = "Successfully updated";
                $response["last inserted id"] = $user->$id;
                $response["code"] = 200;
            }

            catch(\Exception $e) {
                $response["errors"] = "User not updated. .$e";
                $response["code"] = 400;
            }
        }

        return response($response, $response["code"]);
   }
}
