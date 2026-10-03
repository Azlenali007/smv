<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentGatewaysAndTransactionsTable extends Migration
{
    public function up()
    {
        // Payment Gateways table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'min_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
                'default'    => '100.0000', // e.g. 100 INR min
            ],
            'max_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
                'default'    => '500000.0000',
            ],
            'fee_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '0.00',
            ],
            'currency_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'default'    => 'INR',
            ],
            // Sensitive gateway credentials stored as JSON encrypted/protected
            'credentials' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'instructions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
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
        $this->forge->addKey('code');
        $this->forge->addKey('is_active');
        $this->forge->createTable('payment_gateways', true, ['ENGINE' => 'InnoDB', 'ROW_FORMAT' => 'DYNAMIC']);

        // Payment Transactions table
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
            'gateway_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'transaction_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'gateway_transaction_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
            ],
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'default'    => 'INR',
            ],
            // Converted amount in internal base currency INR
            'amount_in_inr' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
            ],
            'fee' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
                'default'    => '0.0000',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'completed', 'failed', 'cancelled'],
                'default'    => 'pending',
            ],
            'gateway_payload' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'gateway_response' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'verified_at' => [
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('gateway_id');
        $this->forge->addKey('status');
        $this->forge->addKey('transaction_reference');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('gateway_id', 'payment_gateways', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('payment_transactions', true, ['ENGINE' => 'InnoDB', 'ROW_FORMAT' => 'DYNAMIC']);
    }

    public function down()
    {
        $this->forge->dropTable('payment_transactions', true);
        $this->forge->dropTable('payment_gateways', true);
    }
}
