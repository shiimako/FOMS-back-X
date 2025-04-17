<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    public function run()
    {
         $data = [
            [
                'id_user' => '3',
            'nama_mhs'     => 'Rina Ayu',
            'npm'          => '2001010101',
            'kls_mhs'    => 'TI-4A',
            'jurusan_mhs'  => 'Komputer dan Bisnis',
            'prodi_mhs'    => 'Informatika',
            'email_mhs'    => 'rina@gmail.com',
            'telp_mhs'     => '089999999',
        ],
    ];
    // Using Query Builder
    $this->db->table('mahasiswa')->insertBatch($data);

    }
}
