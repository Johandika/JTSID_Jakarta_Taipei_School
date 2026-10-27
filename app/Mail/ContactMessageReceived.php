<?php

namespace App\Mail;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Notifies the school inbox about a message sent through the public contact form.
 */
class ContactMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage)
    {
        $this->locale(config('app.locale'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->contactMessage->email, $this->contactMessage->name)],
            subject: __('admin.contact.email.subject', [
                'subject' => $this->contactMessage->getSubjectLabel(),
                'name' => $this->contactMessage->name,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-message-received',
            with: [
                'adminUrl' => ContactMessageResource::getUrl('edit', ['record' => $this->contactMessage], panel: 'admin'),
            ],
        );
    }
}
