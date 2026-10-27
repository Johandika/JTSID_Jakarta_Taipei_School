<?php

namespace App\Livewire;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Throwable;

class ContactForm extends Component
{
    private const MAX_ATTEMPTS = 5;

    private const DECAY_SECONDS = 600;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $message = '';

    /**
     * Honeypot field: hidden from people, usually filled in by spam bots.
     */
    public string $website = '';

    public bool $isSuccess = false;

    public ?string $rateLimitError = null;

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'phone' => ['required', 'string', 'min:6', 'max:30'],
            'message' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => __('pages.contact.name_label'),
            'email' => __('pages.contact.email_input_label'),
            'phone' => __('pages.contact.phone_label'),
            'message' => __('pages.contact.message_label'),
        ];
    }

    public function submit(): void
    {
        $this->rateLimitError = null;

        $ip = request()->ip();
        $rateLimitKey = 'contact-form:'.$ip;

        if (RateLimiter::tooManyAttempts($rateLimitKey, self::MAX_ATTEMPTS)) {
            $this->rateLimitError = __('pages.contact.rate_limit_error', [
                'seconds' => RateLimiter::availableIn($rateLimitKey),
            ]);

            return;
        }

        $validated = $this->validate();

        RateLimiter::hit($rateLimitKey, self::DECAY_SECONDS);

        if (filled($this->website)) {
            $this->completeSubmission();

            return;
        }

        $contactMessage = ContactMessage::create([
            ...$validated,
            'subject' => ContactMessage::SUBJECT_ADMISSIONS,
            'status' => ContactMessage::STATUS_UNREAD,
            'ip_address' => $ip,
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);

        $this->notifySchool($contactMessage);
        $this->completeSubmission();
    }

    public function resetSuccess(): void
    {
        $this->isSuccess = false;
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.contact-form', [
            'page' => __('pages.contact'),
        ]);
    }

    /**
     * Email the school inbox. The message is already stored, so a delivery failure is reported but never shown to the visitor.
     */
    private function notifySchool(ContactMessage $contactMessage): void
    {
        try {
            Mail::to(config('site.contact_form.recipient'))->send(new ContactMessageReceived($contactMessage));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function completeSubmission(): void
    {
        $this->reset(['name', 'email', 'phone', 'message', 'website']);
        $this->isSuccess = true;
    }
}
