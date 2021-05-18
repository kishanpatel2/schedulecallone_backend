<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Connect;
use App\Models\Appointment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    //
    function register(Request $req)
    {
    	$user = new User;
    	$user->name= $req->input('name'); 
    	$user->email= $req->input('email');
    	$user->password=Hash::make($req->input('password'));
    	$user->save();
    	return $user;
    }
    function login(Request $req)
    {
        $user= User::where('email', $req->email)->first();
        if(!$user || !Hash::check($req->password,$user->password))
        {
            return ["error"=>"Email or password is incorrect"];
        }
        return $user;
    }
    function list()
    {
        return User::all();
    }
    function makeappointment(Request $req){

        $save= new Appointment;
            $save->connection_id=$req->cid;
            $save->call_date=$req->sdate;
        $save->status="0";

            $save->save();
            if($save){
                return response()->json("Appointment Added");
            }
    }
    function userconnect(Request $req){
       
        $save= new Connect;
        $save->userid_a=$req->user_a;
        $save->userid_b= $req->user_b;
        $save->statu="0";

        $save->save();
        return response()->json("Connect Added");
    }
    public function getconnections(Request $req){
            $userid=$req->user_a;
            $products = DB::table('connects')
        ->join('users', 'connects.userid_b', '=', 'users.id')
        ->select('users.*', 'connects.userid_a','connects.id as cid')
        ->where('connects.userid_a',$userid)
        ->orwhere('connects.userid_b',$userid)
        ->get();
            return response()->json($products);
    }
    public function getappoint(Request $req){
        $userid=$req->userid;
        $products = DB::table('appointments')
    ->join('connects', 'appointments.connection_id', '=', 'connects.id')
    ->join('users', 'connects.userid_b', '=', 'users.id')
    ->select('users.*','appointments.id as a_id','appointments.call_date','appointments.connection_id')
    ->where('connects.userid_a',$userid)
    ->orwhere('connects.userid_b',$userid)
    ->get();
        return response()->json($products);
    }


public function updateappoint(Request $req){
    // $save= new Appointment;
    $id=$req->aid;
    $date=$req->sdate;
   $up= DB::table('appointments')
    ->where('id', $id)
    ->update(['call_date' => $date]);

    return response()->json("Updated Successfullly");

        
}
}
