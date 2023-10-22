<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function register(Request $request){
        try {
            $request->validate([
                'userName' => 'required' ,
                'email' => 'required|unique:users,email',
                'role' => 'required|in:seller,buyer,admin',
                'phoneNumber' => 'unique:users,phoneNumber' ,
                'password' => 'required|min:8'
                ]) ;
            // return $request->all() ;
            $user = User::create(
               [
                'userName' => $request->userName  ,
                'email' => $request->email ,
                'phoneNumber' => $request->phoneNumber ,
                'password' => Hash::make($request->password),
               ]
            ) ;
            if($request->role == 'seller')
                $user->assignRole('seller') ;
            elseif($request->role == 'buyer')
                $user->assignRole('buyer') ;
            else
                $user->assignRole(['admin','seller','buyer']);
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'message' => 'user registred with success !' ,
                'token' => $token
            ]) ;
        } catch (Exception $exp) {
            return response()->json([
                'error' => $exp->getMessage()
            ],404);
        }

    }
    public function login(Request $request) {
        try {
            $credentials = $request->validate([
                'email' => 'required|email|exists:users,email' ,
                'password' => 'required|min:8'
            ]) ;
            if(Auth::attempt($credentials)){
                $token = Auth::user()->createToken('autToken')->plainTextToken ;
                return response()->json([
                    'user' => Auth::user() ,
                    'token' => $token
                ]) ;
            }
            return response()->json([
                'error' => 'the provided password is incorrect !'
            ]) ;
        } catch (Exception $th) {
            return response()->json([
                'error' => $th->getMessage()
            ]) ;
        }
    }
    public function edit(Request $request,$id){
        try {
            $request->validate([
                'userName' => [
                    'required',
                    Rule::unique('users', 'userName')->ignore(Auth::user()->id)
                    ],
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore(Auth::user()->id)

                ],
                'phoneNumber' => [
                    'numeric',
                    Rule::unique('users','phoneNUmber')->ignore(Auth::user()->id)
                    ] ,
                'role' => 'required|in:seller,buyer,admin'
            ]) ;
            $user = User::find($id) ;
            $user->update([
                'userName' => $request->userName ,
                'email' => $request->email ,
                'phoneNumber' => $request->phoneNumber ,
            ]) ;
            if($request->role == 'admin' && Auth::user()->hasRole('admin'))
                $user->syncRoles(['seller', 'buyer','admin']);
            return response()->json([
                'message' => 'the user with the id : '.$id.' updated with success !'
            ]) ;
        } catch (Exception $th) {
            return response()->json([
                'error' => $th->getMessage()
            ]) ;
        }

    }
    public function delete($id){
        try {
            if(Auth::user()->id == $id)
                return response()->json([
                    'message' => "You can't delete your account !"
                ]) ;
            User::find($id)->delete() ;
            return response()->json([
                'message' => 'User deleted with success !'
            ]) ;
        } catch (Exception $th) {
            return response()->json([
                'error' => $th->getMessage()
            ]) ;
        }
    }
    public function checkAuth(){
        return Auth::user() ;
    }
    public function logout(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                $user->tokens()->delete();
                return response()->json([
                    'message' => 'User disconnected successfully!'
                ]);
            }
            return response()->json(['error'=> 'You are not authorized to do this action !']) ;}
            catch(Exception $msg){
                return response()->json([
                    $msg->getMessage()
                ]);
            }
    }
}
