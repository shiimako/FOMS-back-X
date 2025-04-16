<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    public function run()
    {
         $data = [
            [
            'nama_mhs'     => 'Rina Ayu',
            'npm'          => '2001010101',
            'kls_mhs'    => 'TI-4A',
            'jurusan_mhs'  => 'Komputer dan Bisnis',
            'prodi_mhs'    => 'Informatika',
            'email_mhs'    => 'rina@gmail.com',
            'telp_mhs'     => '089999999',
        ],
        [
            'nama_mhs'     => 'Dimas Pratama',
            'npm'          => '2001010102',
            'kls_mhs'    => 'TI-3C',
            'jurusan_mhs'  => 'Jurusan Rekayasa Elektronika dan Mesin',
            'prodi_mhs'    => 'Mesin',
            'email_mhs'    => 'dimas@gmail.com',
            'telp_mhs'     => '088888888',
        ],
    ];
    // Using Query Builder
    $this->db->table('mahasiswa')->insertBatch($data);

    }
}
