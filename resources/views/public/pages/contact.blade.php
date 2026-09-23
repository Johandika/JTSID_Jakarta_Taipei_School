@php($contact = config('site.contact'))

<x-static-page :title="__('pages.contact.title')" :summary="__('pages.contact.summary')"
    :seo-title="__('pages.contact.seo_title')" hero-image="images/seed-campus.jpg">
    <h2>{{ __('pages.contact.heading') }}</h2>
    <p><strong>{{ __('pages.contact.address_label') }}</strong><br>{{ $contact['address']['full'] }}</p>
    <p>
        <strong>{{ __('pages.contact.telephone_label') }}</strong><br>
        <a href="{{ $contact['phones']['primary']['url'] }}">{{ $contact['phones']['primary']['display'] }}</a><br>
        <a href="{{ $contact['phones']['secondary']['url'] }}">{{ $contact['phones']['secondary']['display'] }}</a>
    </p>
    <p><strong>{{ __('pages.contact.email_label') }}</strong><br><a href="{{ $contact['email']['url'] }}">{{ $contact['email']['display'] }}</a></p>
    <p>{{ __('pages.contact.contact_prefix') }}<a
            href="{{ $contact['whatsapp']['url'] }}">{{ __('pages.contact.contact_link') }}</a>{{ __('pages.contact.contact_suffix') }}</p>
</x-static-page>
