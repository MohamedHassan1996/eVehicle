<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\CreateUserRequest;
use App\Http\Requests\V1\User\UpdateUserRequest;
use App\Http\Resources\V1\User\AllUserCollection;
use App\Http\Resources\V1\User\UserResource;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class UserController extends Controller implements HasMiddleware
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
            // new Middleware('permission:all_users', only:['index']),
            // new Middleware('permission:create_user', only:['create']),
            // new Middleware('permission:edit_user', only:['edit']),
            // new Middleware('permission:update_user', only:['update']),
            // new Middleware('permission:destroy_user', only:['destroy']),
        ];
    }


    public function index(Request $request)
    {
        $users = $this->userService->allUsers();

        return ApiResponse::success(new AllUserCollection($users));


        //return ApiResponse::success(new AllUserCollection(PaginateCollection::paginate($users->getCollection(), $request->pageSize?$request->pageSize:10)));

    }

    /**
     * Show the form for creating a new resource.
     */

    public function store(CreateUserRequest $createUserRequest)
    {
        try {
            DB::beginTransaction();

            $this->userService->createUser($createUserRequest->validated());

            DB::commit();

            return ApiResponse::success([], __('crud.created'));


        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


    }

    /**
     * Show the form for editing the specified resource.
     */

    public function show($user)
    {
        $user  =  $this->userService->editUser($user);
        return ApiResponse::success(new UserResource($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($user,UpdateUserRequest $updateUserRequest)
    {
        try {
            DB::beginTransaction();
            $this->userService->updateUser($user, $updateUserRequest->validated());
            DB::commit();
            return ApiResponse::success([], __('crud.updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user)
    {

        try {
            DB::beginTransaction();
            $this->userService->deleteUser($user);
            DB::commit();
            return response()->json([
                'message' => __('crud.updated')
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


    }

    public function changeStatus(Request $request)
    {

        try {
            DB::beginTransaction();
            $this->userService->changeUserStatus($request->userId, $request->status);
            DB::commit();

            return response()->json([
                'message' => __('crud.deleted')
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

}
