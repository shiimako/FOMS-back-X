<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengajuanDosenSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_dosen' => '1',
                'id_mhs' => '2',
                'status' => 'pending',
                'tgl_pengajuan' => '2025-04-11',
            ],
        ];

         $this->db->table('pengajuan_dosen')->insertBatch($data);
    }
}
