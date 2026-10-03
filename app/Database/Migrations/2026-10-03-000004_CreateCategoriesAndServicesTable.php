<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesAndServicesTable extends Migration
{
    public function up()
    {
        // Categories table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
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
        $this->forge->addKey('sort_order');
        $this->forge->addKey('status');
        $this->forge->createTable('categories', true, ['ENGINE' => 'InnoDB', 'ROW_FORMAT' => 'DYNAMIC']);

        // Services table
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'provider_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'provider_service_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'min_quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 10,
            ],
            'max_quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 100000,
            ],
            // Base selling rate per 1,000 units in INR
            'rate_per_1k' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
                'default'    => '0.0000',
            ],
            // Profit margin percentage (e.g. 20.00%)
            'margin_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'default'    => '15.00',
            ],
            'provider_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
                'null'       => true,
            ],
            'provider_currency' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'default'    => 'USD',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
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
        $this->forge->addKey('category_id');
        $this->forge->addKey('provider_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('services', true, ['ENGINE' => 'InnoDB', 'ROW_FORMAT' => 'DYNAMIC']);
    }

    public function down()
    {
        $this->forge->dropTable('services', true);
        $this->forge->dropTable('categories', true);
    }
}
