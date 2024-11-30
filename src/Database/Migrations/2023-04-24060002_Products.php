<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Products extends Migration
{
    public function up()
    {
        // Products
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'idEmpresa' => ['type' => 'bigint', 'null' => true],
            'idCategory' => ['type' => 'int', 'constraint' => 11, 'null' => true],
            'code' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'barcode' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'unidad' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'description' => ['type' => 'varchar', 'constraint' => 512, 'null' => true],
            'stock' => ['type' => 'decimal', 'constraint' => '18,2', 'null' => true],
            'validateStock' => ['type' => 'varchar', 'constraint' => 4, 'null' => true],
            'inventarioRiguroso' => ['type' => 'varchar', 'constraint' => 4, 'null' => true],
            'buyPrice' => ['type' => 'decimal', 'constraint' => '18,2', 'null' => true],
            'salePrice' => ['type' => 'decimal', 'constraint' => '18,2', 'null' => true],
            'porcentSale' => ['type' => 'int', 'constraint' => 11, 'null' => true],
            'porcentTax' => ['type' => 'int', 'constraint' => 11, 'null' => true],

            'unidadSAT' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'claveProductoSAT' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],

            'nombreUnidadSAT' => ['type' => 'varchar', 'constraint' => 256, 'null' => true],
            'nombreClaveProducto' => ['type' => 'varchar', 'constraint' => 256, 'null' => true],

            'porcentIVARetenido' => ['type' => 'decimal', 'constraint' => '18,4', 'null' => true],
            'porcentISRRetenido' => ['type' => 'decimal', 'constraint' => '18,4', 'null' => true],
            
            'routeImage' => ['type' => 'varchar', 'constraint' => 256, 'null' => true],

            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('products', true);
    }
    public function down()
    {
        $this->forge->dropTable('products', true);
    }
}