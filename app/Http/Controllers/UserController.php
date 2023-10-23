<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->only('register') ;
    }

    public function register(RegisterRequest $request){
        $request->merge(['password' => Hash::make($request->password)]) ;
            $user = User::create(
              $request->all()
              ) ;
            $user->assignRole($request->role) ;
            return response()->json([
                'message' => 'user created successfully with the role : '.$request->role ,
            ]) ;
    }
    public function login(LoginRequest $request) {
            $credentials = $request->only(['email','password']) ;
            if(!Auth::attempt($credentials)){
                return response()->json([
                    'error' => 'incorrect password !'
                ]) ;
            }
            $token = Auth::user()->createToken('autToken')->plainTextToken ;
            return response()->json([
                'user' => Auth::user() ,
                'token' => $token
            ]) ;
    }
    public function update(EditUserRequest $request,User $user){
        if($request->role == 'admin' && !Auth::user()->hasRole('admin') || Auth::user()->id != $user->id)
            return response()->json([
                'error' => 'you do not have permession to do this acction! '
            ]) ;
        $user->update(
            $request->all()
        ) ;
        if($request->role == 'seller' || $request->role=='buyer')
            $user->syncRole($request->role);
        $user->syncRoles(['seller', 'buyer','admin']) ;
        return response()->json([
            'message' => 'the user with the id : '.$user->id.' updated successfully !'
        ]) ;

    }
    public function destroy(User $user){
        if(Auth::user()->id == $user->id)
            return response()->json([
                'message' => "You can't delete your account !"
            ]) ;
        $user->delete() ;
        return response()->json([
            'message' => 'User deleted with success !'
        ]) ;
    }
    public function show(User $user){
        return $user;

    }
    public function me(){
        return Auth::user() ;
    }
    public function logout(Request $request){
        $user = $request->user();
        if (!$user) {
            return response()->json(['error'=> 'You are not authorized to do this action !']) ;
        }
        $user->tokens()->delete();
        return response()->json([
            'message' => 'User disconnected successfully!'
        ]);
    }
}
