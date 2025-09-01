<?php

namespace Database\Seeders\User;

use App\Enums\User\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $this->command->info('Creating Admin User...');


        $user = new User();
        $user->name = 'Admin';
        $user->username = 'Admin';
        $user->email = 'mr10dev10@gmail.com';
        $user->password = 'Mans123456';
        $user->status = UserStatus::ACTIVE;
        $user->save();


    }
}
