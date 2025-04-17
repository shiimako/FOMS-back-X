<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMahasiswaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_mhs' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_user' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama_mhs' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'npm' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => false],
            'kls_mhs' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'jurusan_mhs' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => false],
            'prodi_mhs' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'email_mhs' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'telp_mhs' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_mhs', true);
        $this->forge->addForeignKey('id_user', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('mahasiswa');
    }

    public function down()
    {
        $this->forge->dropTable('mahasiswa');
    }
}
