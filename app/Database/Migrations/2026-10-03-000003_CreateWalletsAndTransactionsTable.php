<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWalletsAndTransactionsTable extends Migration
{
    public function up()
    {
        // Wallets table
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
                'unique'     => true,
            ],
            // Base balance is strictly stored in INR with 4 decimal places
            'balance' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
                'default'    => '0.0000',
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
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('wallets', true, ['ENGINE' => 'InnoDB', 'ROW_FORMAT' => 'DYNAMIC']);

        // Wallet Transactions table
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'wallet_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['deposit', 'order_debit', 'refund', 'manual_adjustment'],
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
            ],
            'opening_balance' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
            ],
            'closing_balance' => [
                'type'       => 'DECIMAL',
                'constraint' => '16,4',
            ],
            'currency_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'default'    => 'INR',
            ],
            'reference_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'remarks' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'admin_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('wallet_id');
        $this->forge->addKey('type');
        $this->forge->addForeignKey('wallet_id', 'wallets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('wallet_transactions', true, ['ENGINE' => 'InnoDB', 'ROW_FORMAT' => 'DYNAMIC']);
    }

    public function down()
    {
        $this->forge->dropTable('wallet_transactions', true);
        $this->forge->dropTable('wallets', true);
    }
}
