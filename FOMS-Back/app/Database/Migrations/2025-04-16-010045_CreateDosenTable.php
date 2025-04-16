<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDosenTable extends Migration
{
    public function up()
    {

        $this->forge->addField([
            'id_dosen' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_dosen' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'NIDN' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'email_dosen' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'telp_dosen' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
        ]);
        $this->forge->addKey('id_dosen', true);
        $this->forge->createTable('dosen');
    }

    public function down()
    {
        $this->forge->dropTable('dosen');
    }
}
