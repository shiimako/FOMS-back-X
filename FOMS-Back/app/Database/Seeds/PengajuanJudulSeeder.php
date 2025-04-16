<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengajuanJudulSeeder extends Seeder
{
    public function run()
    {
         $data = [
            [
                'id_mhs' => 2,
                'id_pengajuan_dosen' => 2,
                'judul' => 'Sistem Informasi Cinta dan Kerinduan',
                'Saran' => '',
                'tgl_pengajuan' => '2025-04-16',
                'status' => 'pending',
                'proposal_file' => 'Miika_try.pdf',
            ]
            ];
            // Using Query Builder
            $this->db->table('pengajuan_judul')->insert($data);
    }
}
