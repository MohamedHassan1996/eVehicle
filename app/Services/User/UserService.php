<?php

namespace App\Services\User;

use App\Enums\User\UserStatus;
use App\Filters\User\FilterUser;
use App\Filters\User\FilterUserRole;
use App\Models\User;
use App\Services\Upload\UploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
class UserService
{

    public function __construct(protected User $users, protected UploadService $uploadService)
    {
    }
    public function changeUserPasswordByEmail(string $email, string $password): void
    {
        $user = $this->getUserByEmail($email);

        $user->password = $password;
        $user->save();
    }

    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function allUsers()
    {
        $auth = auth()->user();
        $perPage = request()->get('pageSize', 10);

        $users = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FilterUser()),
                AllowedFilter::exact('status', 'is_active'),
                AllowedFilter::custom('role', new FilterUserRole()),
            ])
            ->whereNot('id', $auth->id)
            ->paginate($perPage); // Pagination applied here


        return $users;
    }


    public function createUser(array $userData): User
    {

        $avatarPath = null;

        if(isset($userData['avatar']) && $userData['avatar'] instanceof UploadedFile){
            $avatarPath =  $this->uploadService->uploadFile($userData['avatar'], 'avatars');
        }

        $user = User::create([
            'name' => $userData['name'],
            'username' => $userData['username'],
            'email' => $userData['email']??'',
            'password' => $userData['password'],
            'status' => UserStatus::from($userData['status']),
            'avatar' => $avatarPath,
        ]);


        return $user;

    }

    public function editUser(int $userId)
    {
        return User::findOrFail($userId);
    }

    public function updateUser(int $userId, array $userData)
    {

        $avatarPath = null;

        if(isset($userData['avatar']) && $userData['avatar'] instanceof UploadedFile){
            $avatarPath =  $this->uploadService->uploadFile($userData['avatar'], 'avatars');
        }

        $user = User::find($userId);
        $user->name = $userData['name'];
        $user->username = $userData['username'];
        $user->email = $userData['email']??'';

        if(isset($userData['password'])){
            $user->password = $userData['password'];
        }

        $user->status = UserStatus::from($userData['status']);

        if ($avatarPath) {
            // Delete the old avatar file if it's present
            if ($user->avatar) {
                Storage::disk('public')->delete($user->getRawOriginal('avatar'));
            }
            // Set the new avatar path
            $user->avatar = $avatarPath;
        }

        $user->save();

        return $user;

    }


    public function deleteUser(int $userId)
    {

        $user = User::find($userId);
        if($user->avatar){
            Storage::disk('public')->delete($user->getRawOriginal('avatar'));
        }

        $user->delete();

    }

    public function changeUserStatus(int $userId, int $isActive)
    {

        return User::where('id', $userId)->update(['status' => UserStatus::from($isActive)->value]);

    }

}
