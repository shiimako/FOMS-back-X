<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengajuanJudul extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pengajuan_judul' => [
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
            'id_pengajuan_dosen' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'Saran' => [
                'type'       => 'TEXT',
                'null'       => true,
                'default'   => null,
            ],
            'tgl_pengajuan' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => date('Y-m-d H:i:s'), 
            ],
            'status' => [
                'type'    => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
            ],
            'proposal_file' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id_pengajuan_judul', true);
        $this->forge->addForeignKey('id_mhs', 'mahasiswa', 'id_mhs', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_pengajuan_dosen', 'pengajuan_dosen', 'id_pengajuan_dosen', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pengajuan_judul');

    }

    public function down()
    {
        $this->forge->dropTable('pengajuan_judul');
    }
}
