<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCalculatelotToProducts extends Migration {

    public function up() {
        $fields = [
            'calculatelot' => [
                'type' => 'varchar',
                'constraint' => 4,
                'null' => true,
                'after' => 'validateStock', // opcional
            ],
        ];

        $this->forge->addColumn('products', $fields);
    }

    public function down() {
        $this->forge->dropColumn('products', 'calculatelot');
    }
}
