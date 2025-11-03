<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FieldsExtraProductos extends Migration {

    public function up() {
        // FieldsExtraProductos
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'idEmpresa' => ['type' => 'int', 'constraint' => 11, 'null' => false],
            'idCategory' => ['type' => 'int', 'constraint' => 11, 'null' => false],
            'idSubCategory' => ['type' => 'int', 'constraint' => 11, 'null' => false],
            'type' => ['type' => 'int', 'constraint' => 11, 'null' => false],
            'description' => ['type' => 'varchar', 'constraint' => 125, 'null' => false],
            'options' => ['type' => 'varchar', 'constraint' => 512, 'null' => false],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('fieldsExtraProductos', true);
    }

    public function down() {
        $this->forge->dropTable('fieldsExtraProductos', true);
    }
}
