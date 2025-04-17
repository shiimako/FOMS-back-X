<?php

namespace App\Database\Seeds;

use App\Models\UserModel;
use CodeIgniter\Database\Seeder;
use Myth\Auth\Models\GroupModel;
use Myth\Auth\Password;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $users = new UserModel();
        $groups = new GroupModel();

        $users ->insert([
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password_hash' => Password::hash('admin12345'),
                'active' => 1,
            ]);

         $groups->addUserToGroup($users->getInsertID(), 1);

    }
}
