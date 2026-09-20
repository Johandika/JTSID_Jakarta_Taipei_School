<x-static-page slug="contact" :title="__('pages.contact.title')" :summary="__('pages.contact.summary')"
    :seo-title="__('pages.contact.seo_title')" hero-image="images/seed-campus.jpg">
    <blockquote><p>{{ __('pages.contact.notice') }}</p></blockquote>
    <h2>{{ __('pages.contact.heading') }}</h2>
    <p>{{ __('pages.contact.address') }}</p>
    <p>{{ __('pages.contact.telephone') }}</p>
    <p>{{ __('pages.contact.contact_prefix') }}<a
            href="https://wa.me/6281385435909">{{ __('pages.contact.contact_link') }}</a>{{ __('pages.contact.contact_suffix') }}</p>
</x-static-page>
