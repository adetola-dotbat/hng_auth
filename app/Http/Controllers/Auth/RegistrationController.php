<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AuthResponseResource;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    public function register(RegisterRequest $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $user = User::create([
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                ]);

                $organisation = Organization::create([
                    'name' => $request->firstName . "'s Organisation",
                    'description' => '',
                ]);

                $user->organisations()->attach($organisation->orgId);

                $token = JWTAuth::fromUser($user);

                return (new AuthResponseResource((object) [
                    'message' => 'Registration successful',
                    'accessToken' => $token,
                    'user' => $user,
                ], 201))->response();
                // return response()->json([
                //     'status' => 'success',
                //     'message' => 'Registration successful',
                //     'data' => [
                //         'accessToken' => $token,
                //         'user' => $user
                //     ]
                // ], 201);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json([
                    'status' => 'Bad request',
                    'message' => 'Registration unsuccessful',
                    'statusCode' => 400
                ], 400);
            }
        });
    }
}
