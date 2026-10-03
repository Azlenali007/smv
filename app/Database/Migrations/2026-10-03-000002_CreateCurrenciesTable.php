<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCurrenciesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '60',
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'unique'     => true,
            ],
            'symbol' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            // Rate relative to 1 INR (Base currency is strictly INR = 1.000000)
            // Example: 1 USD = 86.500000 INR => rate_to_inr = 86.500000
            'rate_to_inr' => [
                'type'       => 'DECIMAL',
                'constraint' => '18,6',
                'default'    => '1.000000',
            ],
            'decimal_precision' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'default'    => 2,
            ],
            'is_default' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('code');
        $this->forge->addKey('is_active');
        $this->forge->createTable('currencies', true, ['ENGINE' => 'InnoDB', 'ROW_FORMAT' => 'DYNAMIC']);
    }

    public function down()
    {
        $this->forge->dropTable('currencies', true);
    }
}
