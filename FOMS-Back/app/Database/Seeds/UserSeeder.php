<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Myth\Auth\Entities\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $userModel = model('Myth\Auth\Models\UserModel');
        $auth      = service('authorization');

        // Admin
        $admin = new User([
            'email'    => 'admin@gmail.com',
            'username' => 'admin',
            'password' => 'admin123',
            'active'   => 1
        ]);
        $userModel->save($admin);
        $auth->addUserToGroup($userModel->getInsertID(), 'admin');

        // Dosen
        $dosen = new User([
            'email'    => 'siti.aisyah@gmail.com',
            'username' => 'siti aisyah',
            'password' => 'aisyah123',
            'active'   => 1
        ]);
        $userModel->save($dosen);
        $auth->addUserToGroup($userModel->getInsertID(), 'dosen');

        // Mahasiswa
        $mhs = new User([
            'email'    => 'rina@gmail.com',
            'username' => 'Rina Ayu',
            'password' => 'rinaa123',
            'active'   => 1
        ]);
        $userModel->save($mhs);
        $auth->addUserToGroup($userModel->getInsertID(), 'mahasiswa');
    }
}

