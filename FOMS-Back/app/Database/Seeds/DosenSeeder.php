<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DosenSeeder extends Seeder
{
    public function run()
    {
         $dosen = [
            [
                'nama_dosen'  => 'Dr. Budi Santoso',
                'NIDN'        => '1234567890',
                'email_dosen' => 'budi.santoso@gmail.com',
                'telp_dosen'  => '081234567890',
            ],
            [
                'nama_dosen'  => 'Prof. Siti Aisyah',
                'NIDN'        => '0987654321',
                'email_dosen' => 'siti.aisyah@gmail.com',
                'telp_dosen'  => '089876543210',
            ],
            [
                'nama_dosen'  => 'Dr. Andi Kurniawan',
                'NIDN'        => '1122334455',
                'email_dosen' => 'andi.kurniawan@gmail.com',
                'telp_dosen'  => '085123456789',
            ],
            ];

            // Using Query Builder
            $this->db->table('dosen')->insertBatch($dosen);      
    }
}
