<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->user = new User();
    }

    public function getUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => "bail|required|numeric",
        ])->stopOnFirstFailure();

        if ($validator->fails()) {
            $response = array('status' => false, 'message' => "Validation error occurred", 'error_message' => $validator->errors()->first());
            $statuscode = 400;
        } else {
            $checkUser = $this->user->checkUid($request->uid);
            if (count($checkUser) > 0) {
                $response = array('status' => true, 'message' => "User data fetched successfully", 'userData' => $checkUser[0]);
                $statuscode = 200;
            } else {
                $response = array('status' => false, 'message' => "Invalid user");
                $statuscode = 400;
            }
        }
        return response()->json($response, $statuscode);
    }

    public function getUsersList()
    {
        $usersList = $this->user->getUsers();
        if (count($usersList) > 0) {
            $response = array('status' => true, 'message' => "User data fetched successfully", 'userData' => $usersList);
            $statuscode = 200;
        } else {
            $response = array('status' => false, 'message' => "No record found");
            $statuscode = 400;
        }
        return response()->json($response, $statuscode);
    }

    public function updateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => "bail|required|numeric",
            'mobile' => "bail|required|numeric|digits:10",
        ])->stopOnFirstFailure(true);

        if ($validator->fails()) {
            $response = array('status' => false, 'message' => "Validation error occurred", 'error_message' => $validator->errors()->first());
            $statuscode = 400;
        } else {
            $checkUser = $this->user->checkUid($request->uid);
            if ($checkUser) {
                $updateInfoRes = $this->user->updateInfo($request->uid, $request->mobile);
                if ($updateInfoRes) {
                    $response = array('status' => true, 'message' => "User info updated successfully");
                    $statuscode = 200;
                } else {
                    $response = array('status' => false, 'message' => "Unable to update user info");
                    $statuscode = 400;
                }
            } else {
                $response = array('status' => false, 'message' => "Invalid user found");
                $statuscode = 400;
            }
        }
        return response()->json($response, $statuscode);
    }

    public function deleteData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => "bail|required|numeric"
        ])->stopOnFirstFailure(true);

        if ($validator->fails()) {
            $response = array('status' => false, 'message' => "Validation error occurred", 'error_message' => $validator->errors()->first());
            $statuscode = 400;
        } else {
            $checkUser = $this->user->checkUid($request->uid);
            if ($checkUser) {
                $deleteUserStatus = $this->user->deleteInfo($request->uid);
                if ($deleteUserStatus) {
                    $response = array('status' => true, 'message' => "User deleted successfully");
                    $statuscode = 200;
                } else {
                    $response = array('status' => false, 'message' => "Unable to delete user at this moment");
                    $statuscode = 400;
                }
            } else {
                $response = array('status' => false, 'message' => "Invalid user found");
                $statuscode = 400;
            }
        }
        return response()->json($response, $statuscode);
    }

    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => "bail|required|string",
            'email'  => "bail|required|string|email:filter",
            'mobile' => "bail|required|numeric|digits:10",
            'status' => "bail|required|numeric",
        ])->stopOnFirstFailure(true);

        if ($validator->fails()) {
            $response = array('status' => false, 'message' => "Validation error occurred", 'error_message' => $validator->errors()->first());
            $statuscode = 400;
        } else {
            $data = array(
                'name' => $request->name, 'email' => $request->email,
                'mobile' => $request->mobile, 'status' => $request->status
            );
            $createUser = $this->user->createNewUser($data);
            if ($createUser) {
                $response = array('status' => true, 'message' => "User registered successfully", 'userId' => $createUser['userId'], 'userData' => $createUser['userData']);
                $statuscode = 200;
            } else {
                $response = array('status' => false, 'message' => "Unable to register user at this moment");
                $statuscode = 400;
            }
        }
        return response()->json($response, $statuscode);
    }

    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "bail|required|string",
            'email' => "bail|required|string|email:filter",
            'password' => "bail|required|alpha_num",
        ])->stopOnFirstFailure(true);

        if ($validator->fails()) {
            $response = array('status' => false, 'message' => "Validation error occurred", 'error_message' => $validator->errors()->first());
            $statuscode = 400;
        } else {
            $checkEmail = $this->user->checkEmail($request->email);
            if ($checkEmail) {
                $response = array('status' => false, 'message' => "This seems email id already exist.Please try login with the same email id.");
                $statuscode = 400;
            } else {
                $signupArr = array('name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password));
                $signupNewUser = $this->user->signUpNewUser($signupArr);
                if (count($signupNewUser) > 0) {
                    $response = array('status' => true, 'message' => "Signup Successfull.");
                    $statuscode = 200;
                } else {
                    $response = array('status' => false, 'message' => "Unable to signup right now.Please try again later.");
                    $statuscode = 400;
                }
            }
        }
        return response()->json($response, $statuscode);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => "bail|required|string|email:filter",
            'password' => "bail|required|alpha_num|between:6,8"
        ])->stopOnFirstFailure(true);

        if ($validator->fails()) {
            $response = array('status' => false, 'message' => "Validation error occurred", 'error_message' => $validator->errors()->first());
            $statuscode = 400;
        } else {
            $checkEmail = $this->user->checkLoginUser($request->email);
            if ($checkEmail['status']) {
                if (Hash::check($request->password, $checkEmail['loginData'][0]->password)) {
                    $response = array('status' => true, 'message' => "Login successfull.");
                    $statuscode = 200;
                } else {
                    $response = array('status' => false, 'message' => "Apologies, looks like you have entered wrong password.");
                    $statuscode = 400;
                }
            } else {
                $response = array('status' => false, 'message' => "Apologies, looks like you have entered wrong email id.");
                $statuscode = 400;
            }
        }
        return response()->json($response, $statuscode);
    }

    public function queryCheck(Request $request)
    {   
        // $status = DB::table('user')->select('status')->where('status',$request->status)->get();
        $status = $request->status;
        $query = DB::table('user')->when($status, function ($query, $status) {
            $status >= 1 ? $query->where('status','>=', $status) : $query->where('status', $status);
        }, function ($query) {
            $query->orderBy('status', 'asc');
        })->get();

        print_r($query);
    }

    public function chunkResult(){
        // $query = DB::table('user')->where('status','>',0)->orderBy('id','desc')->chunkById(100,function);
    }
}
