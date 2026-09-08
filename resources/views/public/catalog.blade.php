<x-layouts.site :title="__($kind === 'news' ? 'site.news' : 'site.resources')">
    <x-page-heading :title="__($kind === 'news' ? 'site.news' : 'site.resources')" :description="__($kind === 'news' ? 'site.news_copy' : 'site.resources_copy')"/>
    @if($kind === 'resources')
    <nav class="shell flex flex-wrap gap-x-6 gap-y-1 border-b border-line py-5" aria-label="{{ __('site.type') }}">
        @foreach(['' => 'all', 'calendar' => 'calendar', 'classroom_regulations' => 'classroom_regulations', 'syllabus' => 'syllabus', 'textbook' => 'textbook', 'lesson_progress' => 'lesson_progress', 'weekly_news' => 'weekly_news'] as $code => $label)
        <a href="{{ \App\Support\Site::path('/resources'.($code ? '/'.$code : '')) }}" class="py-2 text-sm {{ $type === $code ? 'font-bold text-jts underline underline-offset-8' : 'text-muted hover:text-jts' }}">{{ __('site.'.$label) }}</a>
        @endforeach
    </nav>
    @endif
    <div class="shell py-9"><livewire:content-catalog :kind="$kind" :type="$type"/></div>
</x-layouts.site>
