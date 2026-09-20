<x-static-page slug="about" :title="__('pages.about.title')" :summary="__('pages.about.summary')"
    :seo-title="__('pages.about.seo_title')" hero-image="images/seed-campus.jpg">
    <blockquote><p>{{ __('pages.about.body.notice') }}</p></blockquote>
    <h2>{{ __('pages.about.body.community_heading') }}</h2>
    <p>{{ __('pages.about.body.community_body') }}</p>
    <h2>{{ __('pages.about.body.learning_heading') }}</h2>
    <p>{{ __('pages.about.body.learning_body') }}</p>
    <p>{{ __('pages.about.body.closing') }}</p>
</x-static-page>
