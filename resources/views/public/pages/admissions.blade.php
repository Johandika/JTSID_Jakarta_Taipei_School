<x-static-page :title="__('pages.admissions.title')" :summary="__('pages.admissions.summary')"
    :seo-title="__('pages.admissions.seo_title')" hero-image="images/seed-campus.jpg"
    gallery-url="https://www.jtsid.org/infomation03.php">
    <h2>{{ __('pages.admissions.journey_heading') }}</h2>
    <ol>
        @foreach(__('pages.admissions.steps') as $step)
            <li>{{ $step }}</li>
        @endforeach
    </ol>
    <h2>{{ __('pages.admissions.faq_heading') }}</h2>
    <p><strong>{{ __('pages.admissions.fees_question') }}</strong></p>
    <p>{{ __('pages.admissions.fees_answer') }}</p>
    <p><strong>{{ __('pages.admissions.mandarin_question') }}</strong></p>
    <p>{{ __('pages.admissions.mandarin_prefix') }}<a
            href="{{ \App\Support\Site::path('/mandarin-program') }}">{{ __('pages.admissions.mandarin_link') }}</a>{{ __('pages.admissions.mandarin_suffix') }}</p>
</x-static-page>
