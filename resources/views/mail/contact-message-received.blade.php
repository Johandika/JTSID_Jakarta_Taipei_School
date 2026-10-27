<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('admin.contact.email.heading') }}</title>
</head>
<body style="margin:0;padding:24px 12px;background:#f1f5f3;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;">
        <tr>
            <td style="background:#176443;padding:20px 24px;color:#ffffff;">
                <p style="margin:0;font-size:12px;letter-spacing:1px;text-transform:uppercase;opacity:.8;">Jakarta Taipei School</p>
                <h1 style="margin:4px 0 0;font-size:20px;">{{ __('admin.contact.email.heading') }}</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:24px;">
                <p style="margin:0 0 16px;font-size:14px;color:#475569;">{{ __('admin.contact.email.intro') }}</p>

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                    @foreach ([
                        'name' => $contactMessage->name,
                        'email' => $contactMessage->email,
                        'phone' => $contactMessage->phone ?: '—',
                        'subject_label' => $contactMessage->getSubjectLabel(),
                        'sent_at' => $contactMessage->created_at?->timezone('Asia/Jakarta')->format('d M Y, H:i').' WIB',
                    ] as $field => $value)
                        <tr>
                            <td style="padding:6px 12px 6px 0;width:130px;color:#64748b;vertical-align:top;">{{ __('admin.contact.email.'.$field) }}</td>
                            <td style="padding:6px 0;font-weight:bold;vertical-align:top;">{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>

                <p style="margin:20px 0 6px;font-size:14px;color:#64748b;">{{ __('admin.contact.email.message') }}</p>
                <div style="padding:14px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;line-height:1.6;">{!! nl2br(e($contactMessage->message)) !!}</div>

                <p style="margin:24px 0 0;">
                    <a href="{{ $adminUrl }}" style="display:inline-block;padding:10px 18px;background:#176443;color:#ffffff;text-decoration:none;border-radius:8px;font-size:14px;font-weight:bold;">{{ __('admin.contact.email.open_in_admin') }}</a>
                </p>
                <p style="margin:16px 0 0;font-size:12px;color:#94a3b8;">{{ __('admin.contact.email.reply_hint') }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
