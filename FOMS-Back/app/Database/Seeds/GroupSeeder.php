<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Myth\Auth\Models\GroupModel;
use Myth\Auth\Models\PermissionModel;

class GroupSeeder extends Seeder
{
    public function run()
    {
        $groups = new GroupModel();
        $groups->insert([
            'name' => 'Admin',
            'description' => 'Admin Group',
        ]);

        $groups->insert([
            'name' => 'Mahasiswa',
            'description' => 'Mahasiswa Group',
        ]);

        $groups->insert([
            'name' => 'Dosen',
            'description' => 'Dosen Group',
        ]);

        $permission = new PermissionModel();
        $admin = $permission->findAll();
        foreach ($admin as $permission) {
            $groups->addPermissionToGroup($permission->id, $groups->getInsertID());
            }

        $mahasiswa = $permission->where('name', 'user-module')->findAll();
        foreach ($mahasiswa as $permission) {
            $groups->addPermissionToGroup($permission->id, $groups->getInsertID());
            }

        $dosen = $permission->where('name', 'user-module')->findAll();
        foreach ($dosen as $permission) {
            $groups->addPermissionToGroup($permission->id, $groups->getInsertID());
            }
        

    }
}
