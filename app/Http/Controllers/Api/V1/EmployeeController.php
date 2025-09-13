<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeController extends Controller
{
    public function index(Request $request): JsonResource
    {
        $users = User::with('department')->paginate(15);
        return UserResource::collection($users);
    }
}