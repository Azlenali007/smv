<?php

namespace App\Controllers\User;

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
        $userId = (int)session()->get('user_id');
        $tickets = $this->ticketModel->getUserTickets($userId);

        return $this->renderView('user/tickets', [
            'title'   => 'Support Tickets | ' . $this->data['site_name'],
            'tickets' => $tickets,
        ]);
    }

    public function create()
    {
        return $this->renderView('user/ticket_create', [
            'title' => 'Open New Support Ticket | ' . $this->data['site_name'],
        ]);
    }

    public function store()
    {
        $userId = (int)session()->get('user_id');

        $rules = [
            'subject'  => 'required|min_length[5]|max_length[200]',
            'priority' => 'in_list[low,medium,high]',
            'message'  => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $ticketId = $this->ticketModel->insert([
            'user_id'       => $userId,
            'subject'       => trim($this->request->getPost('subject')),
            'priority'      => $this->request->getPost('priority') ?? 'medium',
            'status'        => 'open',
            'last_reply_at' => date('Y-m-d H:i:s'),
        ]);

        if ($ticketId) {
            $this->messageModel->insert([
                'ticket_id'   => $ticketId,
                'sender_id'   => $userId,
                'sender_type' => 'user',
                'message'     => trim($this->request->getPost('message')),
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to(site_url('tickets/' . $ticketId))->with('success', 'Support ticket opened successfully.');
    }

    public function show(int $ticketId)
    {
        $userId = (int)session()->get('user_id');
        $ticket = $this->ticketModel->where('id', $ticketId)->where('user_id', $userId)->first();

        if (!$ticket) {
            return redirect()->to(site_url('tickets'))->with('error', 'Ticket not found or access denied.');
        }

        $messages = $this->messageModel->getMessagesForTicket($ticketId);

        return $this->renderView('user/ticket_view', [
            'title'    => "Ticket #{$ticketId}: " . esc($ticket['subject']),
            'ticket'   => $ticket,
            'messages' => $messages,
        ]);
    }

    public function reply(int $ticketId)
    {
        $userId = (int)session()->get('user_id');
        $ticket = $this->ticketModel->where('id', $ticketId)->where('user_id', $userId)->first();

        if (!$ticket) {
            return redirect()->to(site_url('tickets'))->with('error', 'Ticket not found.');
        }

        if ($ticket['status'] === 'closed') {
            return redirect()->back()->with('error', 'Cannot reply to a closed ticket.');
        }

        $message = trim((string)$this->request->getPost('message'));
        if (strlen($message) < 5) {
            return redirect()->back()->with('error', 'Message reply must be at least 5 characters.');
        }

        $this->messageModel->insert([
            'ticket_id'   => $ticketId,
            'sender_id'   => $userId,
            'sender_type' => 'user',
            'message'     => $message,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->ticketModel->update($ticketId, [
            'status'        => 'pending',
            'last_reply_at' => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Your reply has been sent.');
    }
}
