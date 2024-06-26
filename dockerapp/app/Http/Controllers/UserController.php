<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function authenticate (Request $request ){

        $credentials = $request->only('email', 'password');

        try {
            if(!$token = JWTAuth::attempt($credentials)){
                return response()->json(['error'=> 'credenciales invalidas'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error'=> 'no se puede crear el token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'=> 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:255|confirmed'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name'=> $request->get('name'),
            'email'=> $request->get('email'),
            'password'=> Hash::make($request->get('password')),
        ]);

        $token  = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);

    }

    public function getAuthenticatedUser(){
        try {
            if($user = JWTAuth::parseToken()->authenticate()){
                return response()->json(['usuario no encontrado'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token expirado'], $e->getCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token invalido'], $e->getCode());
        } catch (JWTException $e) {
            return response()->json(['token ausente'], $e->getCode());
        }

        return response()->json(compact('user'));
    }

    public function findUser(int $id) {
        $user = User::find($id);

        if(empty($user)){
            return response()->json([
                "message"=>"User not found"
            ], 404);
        }

        return response()->json($user, 200);
    }
}
