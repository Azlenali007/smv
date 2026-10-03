<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketModel extends Model
{
    protected $table            = 'tickets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'subject',
        'priority',
        'status',
        'last_reply_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getUserTickets(int $userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('updated_at', 'DESC')
                    ->findAll();
    }

    public function getAllTicketsWithUsers()
    {
        return $this->select('tickets.*, users.name as user_name, users.email as user_email')
                    ->join('users', 'users.id = tickets.user_id')
                    ->orderBy('tickets.updated_at', 'DESC')
                    ->findAll();
    }
}
