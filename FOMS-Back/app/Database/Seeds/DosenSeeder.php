<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DosenSeeder extends Seeder
{
    public function run()
    {
         $dosen = [
            [
                'id_user' => 2,
                'nama_dosen'  => 'Prof. Siti Aisyah',
                'NIDN'        => '0987654321',
                'email_dosen' => 'siti.aisyah@gmail.com',
                'telp_dosen'  => '089876543210',
            ],
            ];

            // Using Query Builder
            $this->db->table('dosen')->insertBatch($dosen);      
    }
}
