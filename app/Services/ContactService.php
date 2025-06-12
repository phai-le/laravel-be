<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Enums\ContactEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ContactService
{
    public function __construct(
        protected Contact $model,
    ) {}

    /**
     *
     * @param array $data
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function get(array $data):\Illuminate\Pagination\LengthAwarePaginator
    {
        $query = $this->model->query()
            ->leftJoin('users', 'contacts.user_id', '=', 'users.id')
            ->select('contacts.*');

        $query->when(Arr::get($data, 'user_id', '') !== '', function ($q) use ($data) {
            $q->where('contacts.user_id', '=', Arr::get($data, 'user_id'));
        })
        ->when(Arr::get($data, 'guest_name'), function ($q, $guest_name) {
            $q->where(function($query) use ($guest_name) {
                $query->where('users.name', 'like', "%$guest_name%")
                      ->orWhere('contacts.guest_name', 'like', "%$guest_name%");
            });
        })
        ->when(Arr::get($data, 'guest_email'), function ($q, $guest_email) {
            $q->where(function($query) use ($guest_email) {
                $query->where('users.email', 'like', "%$guest_email%")
                      ->orWhere('contacts.guest_email', 'like', "%$guest_email%");
            });
        })
        ->when(Arr::get($data, 'status', '') !== '', function ($q) use ($data) {
            $q->where('contacts.status', '=', Arr::get($data, 'status'));
        });

        $perPage = $data['per_page'] ?? 10;
        $contacts = $query->paginate($perPage);

        // Load thông tin user cho mỗi contact
        $contacts->getCollection()->transform(function ($contact) {
            if ($contact->user_id) {
                $contact->guest_name = $contact->user->name;
                $contact->guest_email = $contact->user->email;
            }
            return $contact;
        });

        return $contacts;
    }

    /**
     * Summary of show
     * @param int $id
     * @return Contact
     */
    public function show(int $id): Contact
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Summary of sendMail
     * @param Request $request
     * @param int $id
     * @return Contact
     */
    public function sendMail(array $data,int $id): Contact
    {
        $contact = $this->model->findOrFail($id);

        $toEmail = $contact->user_id ? $contact->user->email : $contact->guest_email;
        
        $mailData = [
            'subject' => "Reply {$contact->title}",
            'body' => $data['content_reply'],
        ];

        Mail::raw($mailData['body'], function ($message) use ($toEmail, $mailData) {
            $message->to($toEmail)
                    ->subject($mailData['subject']);
        });

        $contact->update([
            'content_reply' => $data['content_reply'],
            'status' => ContactEnum::Response
        ]);

        return $contact;
    }

    /**
     * Summary of sendContact
     * @param array $data
     * @return Contact
     */
    public function sendContact(array $data): Contact
    {
        
        $contact = $this->model->create($data);

        return $contact;
    }
}
