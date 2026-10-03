<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TicketModel;
use App\Models\TicketMessageModel;

class Ticket extends BaseController
{
    protected TicketModel $ticketModel;
    protected TicketMessageModel $messageModel;

    public function __construct()
    {
        $this->ticketModel   = new TicketModel();
        $this->messageModel  = new TicketMessageModel();
    }

    public function index()
    {
        $tickets = $this->ticketModel->getAllTicketsWithUsers();

        return $this->renderView('admin/tickets', [
            'title'   => 'Customer Support Helpdesk | ' . $this->data['site_name'],
            'tickets' => $tickets,
        ]);
    }

    public function show(int $ticketId)
    {
        $ticket = $this->ticketModel->select('tickets.*, users.name as user_name, users.email as user_email')
                                    ->join('users', 'users.id = tickets.user_id')
                                    ->where('tickets.id', $ticketId)
                                    ->first();

        if (!$ticket) {
            return redirect()->to(site_url('admin/tickets'))->with('error', 'Ticket not found.');
        }

        $messages = $this->messageModel->getMessagesForTicket($ticketId);

        return $this->renderView('admin/ticket_detail', [
            'title'    => "Ticket #{$ticketId}: " . esc($ticket['subject']),
            'ticket'   => $ticket,
            'messages' => $messages,
        ]);
    }

    public function reply(int $ticketId)
    {
        $adminId = (int)session()->get('user_id');
        $message = trim((string)$this->request->getPost('message'));

        if (empty($message)) {
            return redirect()->back()->with('error', 'Reply message cannot be empty.');
        }

        $this->messageModel->insert([
            'ticket_id'   => $ticketId,
            'sender_id'   => $adminId,
            'sender_type' => 'admin',
            'message'     => $message,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->ticketModel->update($ticketId, [
            'status'        => 'pending',
            'last_reply_at' => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Staff reply dispatched to customer.');
    }

    public function updateStatus(int $ticketId)
    {
        $status = $this->request->getPost('status');
        if (!in_array($status, ['open', 'pending', 'closed'], true)) {
            return redirect()->back()->with('error', 'Invalid ticket status.');
        }

        $this->ticketModel->update($ticketId, [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', "Ticket status changed to {$status}.");
    }
}
