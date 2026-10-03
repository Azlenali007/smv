<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'service_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
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
            'provider_order_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'link' => [
                'type'       => 'TEXT',
            ],
            'quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            // Charge in INR base currency
            'charge' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
            ],
            'display_currency' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'default'    => 'INR',
            ],
            'display_charge' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
            ],
            'start_counter' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'remains' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processing', 'completed', 'cancelled', 'refunded'],
                'default'    => 'pending',
            ],
            'provider_status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'api_response' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('service_id');
        $this->forge->addKey('provider_id');
        $this->forge->addKey('status');
        $this->forge->addKey('provider_order_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('service_id', 'services', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('orders', true, ['ENGINE' => 'InnoDB', 'ROW_FORMAT' => 'DYNAMIC']);
    }

    public function down()
    {
        $this->forge->dropTable('orders', true);
    }
}
