<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengajuanDosenTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pengajuan_dosen' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_mhs' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_dosen' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tgl_pengajuan' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => date('Y-m-d H:i:s'),
            ],
            'status' => [
                'type'       => "ENUM('pending','approved','rejected')",
                'default'    => 'pending',
            ],
        ]);
        $this->forge->addKey('id_pengajuan_dosen', true);
        $this->forge->addForeignKey('id_mhs', 'mahasiswa', 'id_mhs', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_dosen', 'dosen', 'id_dosen', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pengajuan_dosen');
    }

    public function down()
    {
        $this->forge->dropTable('pengajuan_dosen');
    }
}
