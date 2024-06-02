<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * @throws AuthenticationException
     */
    public function __invoke(LoginRequest $request): \Illuminate\Http\JsonResponse
    {

        if (! Auth::attempt($request->validated())) {
            throw new AuthenticationException();
        }
        $user = User::query()->find(Auth::id());
        $result['user'] = $user;
        $result['token'] = $user->createToken('token')->plainTextToken;

        return ApiResponseHelper::sendResponse(new Result(LoginResource::make($result)));

    }
}
