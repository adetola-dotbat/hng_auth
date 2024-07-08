<?php

namespace App\Http\Controllers;

use App\Http\Requests\Organization\StoreRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $organisations = $user->organisations;
        return response()->json([
            'status' => 'success',
            'message' => 'Organisations retrieved successfully',
            'data' => [
                'organisations' => $organisations
            ]
        ], 200);
    }

    public function show($orgId)
    {
        $user = auth()->user();
        $organisation = Organization::findOrFail($orgId);

        if ($user->organisations->contains($organisation)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Organisation retrieved successfully',
                'data' => $organisation
            ], 200);
        }

        return response()->json([
            'status' => 'Forbidden',
            'message' => 'You do not have access to this organisation',
            'statusCode' => 403
        ], 403);
    }

    public function store(StoreRequest $request)
    {
        $organisation = Organization::create($request->validated());

        $user = auth()->user();
        $user->organisations()->attach($organisation->orgId);

        return response()->json([
            'status' => 'success',
            'message' => 'Organisation created successfully',
            'data' => $organisation
        ], 201);
    }

    public function addUserToOrganisation(Request $request, $orgId)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required|exists:users,userId',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        $organisation = Organization::findOrFail($orgId);
        $user = User::where('userId', $request->userId)->first();
        $organisation->users()->attach($user->id);

        return response()->json([
            'status' => 'success',
            'message' => 'User added to organisation successfully',
        ], 200);
    }
}
