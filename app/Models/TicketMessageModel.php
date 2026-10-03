<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketMessageModel extends Model
{
    protected $table            = 'ticket_messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ticket_id',
        'sender_id',
        'sender_type',
        'message',
        'created_at',
    ];

    protected $useTimestamps = false;

    public function getMessagesForTicket(int $ticketId)
    {
        return $this->select('ticket_messages.*, users.name as sender_name')
                    ->join('users', 'users.id = ticket_messages.sender_id', 'left')
                    ->where('ticket_messages.ticket_id', $ticketId)
                    ->orderBy('ticket_messages.id', 'ASC')
                    ->findAll();
    }
}
