<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProvidersAndProviderServicesTable extends Migration
{
    public function up()
    {
        // Providers table
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
            'api_url' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            // Sensitive API Key - protected, never sent to frontend
            'api_key' => [
                'type'       => 'TEXT',
            ],
            'balance' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
                'default'    => '0.0000',
            ],
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'default'    => 'USD',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'last_sync_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addKey('status');
        $this->forge->createTable('providers', true, ['ENGINE' => 'InnoDB', 'ROW_FORMAT' => 'DYNAMIC']);

        // Provider Services Cache table
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'provider_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'remote_service_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => '60',
                'default'    => 'Default',
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
                'default'    => '0.0000',
            ],
            'min' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 10,
            ],
            'max' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 100000,
            ],
            'dripfeed' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'refill' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'cancel' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
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
        $this->forge->addKey('provider_id');
        $this->forge->addKey('remote_service_id');
        $this->forge->addForeignKey('provider_id', 'providers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('provider_services', true, ['ENGINE' => 'InnoDB', 'ROW_FORMAT' => 'DYNAMIC']);
    }

    public function down()
    {
        $this->forge->dropTable('provider_services', true);
        $this->forge->dropTable('providers', true);
    }
}
