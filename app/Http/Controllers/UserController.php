<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show($id)
    {
        $user = auth()->user();

        // Check if the requested user ID is the authenticated user's ID
        if ($user->userId == $id) {
            return response()->json([
                'status' => 'success',
                'message' => 'User retrieved successfully',
                'data' => $user
            ], 200);
        }

        // Check if the requested user ID is in the organizations the authenticated user belongs to or created
        $userInOrg = $user->organisations()
            ->with('users')
            ->get()
            ->pluck('users')
            ->flatten()
            ->where('userId', $id)
            ->first();

        if ($userInOrg) {
            return response()->json([
                'status' => 'success',
                'message' => 'User retrieved successfully',
                'data' => $userInOrg
            ], 200);
        }

        return response()->json([
            'status' => 'Forbidden',
            'message' => 'You do not have access to this user\'s record',
            'statusCode' => 403
        ], 403);
    }
}
