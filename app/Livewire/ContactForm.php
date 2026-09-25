<?php

namespace App\Livewire;

use App\Models\ContactMessage;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ContactForm extends Component
{
    #[Rule('required|string|min:2|max:100', as: 'Nama Lengkap')]
    public string $name = '';

    #[Rule('required|email|max:100', as: 'Email')]
    public string $email = '';

    #[Rule('required|string|min:6|max:30', as: 'No. Telepon')]
    public string $phone = '';

    #[Rule('required|string|in:admissions,mandarin_program,general,academics,other', as: 'Subjek')]
    public string $subject = '';

    #[Rule('required|string|min:10|max:500', as: 'Pesan')]
    public string $message = '';

    public bool $isSuccess = false;

    public ?string $rateLimitError = null;

    public function submit(): void
    {
        $this->rateLimitError = null;

        $ip = request()->ip();
        $rateLimitKey = 'contact-form:'.$ip;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->rateLimitError = __('pages.contact.rate_limit_error', ['seconds' => $seconds]) ?: "Terlalu banyak permintaan. Silakan coba lagi dalam {$seconds} detik.";

            return;
        }

        $validated = $this->validate();

        RateLimiter::hit($rateLimitKey, 600); // 10 minutes throttle window

        ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => ContactMessage::STATUS_UNREAD,
            'ip_address' => $ip,
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);

        $this->reset(['name', 'email', 'phone', 'subject', 'message']);
        $this->isSuccess = true;
    }

    public function resetSuccess(): void
    {
        $this->isSuccess = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.contact-form', [
            'page' => __('pages.contact'),
        ]);
    }
}
