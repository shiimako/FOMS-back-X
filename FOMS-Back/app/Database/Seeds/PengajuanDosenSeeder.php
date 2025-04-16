<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengajuanDosenSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_dosen' => '2',
                'id_mhs' => '1',
                'status' => 'pending',
                'tgl_pengajuan' => '2025-04-11',
            ],
            [
                'id_dosen' => '3',
                'id_mhs' => '2',
                'status' => 'approved',
                'tgl_pengajuan' => '2025-04-10',
            ],
        ];

         $this->db->table('pengajuan_dosen')->insertBatch($data);
    }
}
