<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdSubCategoriaToProducts extends Migration
{
    public function up()
    {
        $fields = [
            'idSubCategoria' => [
                'type'       => 'int',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'idCategory' // lo colocará justo después de idCategory
            ],
        ];

        $this->forge->addColumn('products', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'idSubCategoria');
    }
}
