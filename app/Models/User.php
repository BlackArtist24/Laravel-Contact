<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class User extends Model
{
    use HasFactory;
    protected $table = 'user';
    protected $primary_key = 'id';
    public $timestamps = true;
    CONST CREATED_AT = NULL;
    CONST UPDATED_AT = NULL;
    protected $fillable = ['name','email','mobile','status','created_at','updated_at'];

    public function checkUid($userId){
        $userInfo = DB::table('user')->where('id',$userId)->get();
        if(count($userInfo) > 0){
            return $userInfo;
        }else{
            return [];
        }
    }

    public function getUsers(){
        $usersData = DB::table('user')->get();
        if(count($usersData) > 0){
            return $usersData;
        }else{
            return [];
        }
    }

    public function updateInfo($userId,$mobile){
        $updateData = DB::table('user')->where('id',$userId)->update(['mobile'=>$mobile, 'updated_at'=>Carbon::now()]);
        if($updateData){
            return true;
        }else{
            return false;
        }
    }

    public function deleteInfo($userId){
        $deleteData = DB::table('user')->where('id',$userId)->delete();
        if($deleteData){
            return true;
        }else{
            return false;
        }
    }

    public function createNewUser($data){
        $data['created_at'] = Carbon::now();
        $insertNewUser = DB::table('user')->insertGetId($data);
        if($insertNewUser){
            $arr = array('userId'=>$insertNewUser, 'userData'=>$data);
        }else{
            $arr = [];
        }     
        return $arr;
    }

    public function checkEmail($email){
        $checkEmailId = DB::table('user')->where('email',$email)->get();
        if(count($checkEmailId) > 0){
            return true;
        }else{
            return false;
        }
    }

    public function signUpNewUser($signupArr){
        $signupId = DB::table('user')->insertGetId($signupArr);
        // print_r($signupId); die;
        if($signupId){
            $signupUserArr = DB::table('user')->where('id',$signupId)->get();
            return $signupUserArr;
        }else{
            return [];
        }
    }

    public function checkLoginUser($email){
        $checkEmail = DB::table('user')->where('email',$email)->get();
        if(count($checkEmail) > 0){
            return array('status'=>true, 'loginData'=>$checkEmail);
        }else{
            return array('status'=>false, 'loginData'=>[]);
        }
    }

    public function loginUserData($email){

    }
}
