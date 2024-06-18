<?php

namespace App\Http\Controllers;

use App\DTO\LoginDTO;
use App\Helpers\ApiResponse\ApiResponseHelper;
use App\Helpers\ApiResponse\Result;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * @throws AuthenticationException
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {

        /**
         * @var array{
         *     user_name : string,
         *     password:string
         * }$requestedData
         */
        $requestedData = $request->validated();
        $data = LoginDTO::fromRequest($requestedData);
        $user = User::query()->where('phone', $data->phone)->first();

        if (! $user || ! Hash::check($data->password, $user->password)) {
            throw new AuthenticationException();
        }

        $result['user'] = $user;
        $result['token'] = $user->createToken('token')->plainTextToken;

        return ApiResponseHelper::sendResponse(new Result(LoginResource::make($result)));

    }
}
