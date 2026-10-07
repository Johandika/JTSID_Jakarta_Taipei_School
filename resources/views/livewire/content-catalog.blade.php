<div>
    <form wire:submit.prevent="$refresh" class="grid gap-4 border-b border-line pb-7 md:grid-cols-2 {{ $kind === 'news' ? 'xl:grid-cols-[2fr_repeat(4,minmax(0,1fr))]' : ($usesDetailedAcademicPeriod ? 'xl:grid-cols-[2fr_repeat(4,minmax(0,1fr))]' : 'xl:grid-cols-[2fr_repeat(3,minmax(0,1fr))]') }}">
        <label><span class="field-label">{{ __('site.search') }}</span><div class="relative"><x-site-icon name="search" class="pointer-events-none absolute left-3 top-3 size-4 text-muted"/><input wire:model.live.debounce.350ms="search" type="search" maxlength="150" class="field pl-10" placeholder="{{ __('site.search_placeholder') }}"></div></label>
        @if($usesDetailedAcademicPeriod)
            <label><span class="field-label">{{ __('site.class_level') }}</span><select wire:model.live="classLevel" aria-label="{{ __('site.class_level') }}" class="field"><option value="">{{ __('site.all_class_levels') }}</option>@foreach($classLevels as $code => $label)<option value="{{ $code }}">{{ $label }}</option>@endforeach</select></label>
            <label><span class="field-label">{{ __('site.semester') }}</span><select wire:model.live="semester" aria-label="{{ __('site.semester') }}" class="field"><option value="">{{ __('site.all_semesters') }}</option>@foreach($semesters as $code => $label)<option value="{{ $code }}">{{ $label }}</option>@endforeach</select></label>
        @else
            <label><span class="field-label">{{ __('site.school_level') }}</span><select wire:model.live="schoolLevel" aria-label="{{ __('site.school_level') }}" class="field"><option value="all">{{ __('site.all_levels') }}</option>@foreach($schoolLevels as $code => $label)<option value="{{ $code }}">{{ $label }}</option>@endforeach</select></label>
        @endif
        <label><span class="field-label">{{ __('site.department') }}</span><select wire:model.live="department" aria-label="{{ __('site.department') }}" class="field"><option value="">{{ __('site.all_departments') }}</option>@foreach($departments as $d)<option value="{{ $d->id }}">{{ \Illuminate\Support\Facades\Lang::has('site.'.$d->code) ? __('site.'.$d->code) : $d->name }}</option>@endforeach</select></label>
        @if($kind === 'news')<label><span class="field-label">{{ __('site.subject') }}</span><select wire:model.live="category" aria-label="{{ __('site.subject') }}" class="field"><option value="">{{ __('site.all_subjects') }}</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ __('site.'.$c->code) }}</option>@endforeach</select></label>
        @endif
        <label><span class="field-label">{{ __('site.sort') }}</span><select wire:model.live="sort" aria-label="{{ __('site.sort') }}" class="field"><option value="newest">{{ __('site.newest') }}</option><option value="oldest">{{ __('site.oldest') }}</option></select></label>
    </form>
    <div class="flex min-h-16 items-center justify-between gap-4 text-xs text-muted"><span>{{ $items->total() }} {{ __('site.results') }}</span><button wire:click="clearFilters" class="py-3 font-semibold text-jts hover:underline">{{ __('site.clear') }}</button></div>
    <div wire:loading.class="opacity-50" class="transition-opacity">
        @if($items->isEmpty())
            <div class="py-18 text-center"><x-site-icon name="search" class="mx-auto mb-4 size-8 text-muted"/><h2 class="text-xl font-semibold">{{ __('site.no_results') }}</h2><p class="mt-2 text-sm text-muted">{{ __('site.no_results_copy') }}</p></div>
        @elseif($kind === 'news')
            <div class="grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">@foreach($items as $post)<div wire:key="post-{{ $post->id }}"><x-news-item :post="$post"/></div>@endforeach</div>
        @elseif($type === 'weekly_news')
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($items as $item)
                    <article wire:key="weekly-news-{{ $item->id }}" class="group flex h-full flex-col overflow-hidden rounded-xl border border-line/80 bg-white shadow-2xs transition-all duration-300 hover:border-jts/40 hover:shadow-lg">
                        <div class="relative aspect-[16/10] w-full overflow-hidden bg-gray-100">
                            <img
                                src="{{ $item->image_url }}"
                                alt="{{ $item->title }}"
                                class="size-full object-cover object-center transition-transform duration-400 ease-out group-hover:scale-105"
                                loading="lazy"
                                width="600"
                                height="375"
                            />

                            <div class="absolute left-3 top-3 z-10">
                                <span class="inline-flex items-center rounded-md bg-jts/95 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-white shadow-sm backdrop-blur-xs">
                                    {{ __('site.weekly_news') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col p-5 sm:p-6">
                            <div class="mb-3 flex items-center justify-between gap-2 text-xs text-muted">
                                <span class="inline-flex items-center gap-1.5 font-medium">
                                    <x-site-icon name="calendar" class="size-3.5 text-jts/80" />
                                    {{ $item->upload_date->translatedFormat('d M Y') }}
                                </span>
                                <span class="inline-block rounded bg-gray-100 px-2 py-0.5 text-[11px] font-semibold text-ink">
                                    {{ $item->school_level_label }}
                                </span>
                            </div>

                            <h3 class="mb-2.5 text-base font-bold leading-snug text-ink transition-colors duration-200 group-hover:text-jts line-clamp-2 sm:text-lg">
                                {{ $item->title }}
                            </h3>

                            @if($item->translation()?->summary)
                                <p class="mb-4 flex-1 text-xs leading-relaxed text-muted line-clamp-2 sm:text-sm">
                                    {{ $item->translation()?->summary }}
                                </p>
                            @else
                                <div class="flex-1"></div>
                            @endif

                            <div class="mt-auto flex justify-center border-t border-line/60 pt-4">
                                <a href="{{ \App\Support\Site::path('/download/resources/'.$item->id) }}" class="btn btn-primary w-full">
                                    <span>{{ __('site.download') }}</span>
                                    <x-site-icon name="download" class="size-4" />
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="overflow-x-auto rounded border border-line"><table class="data-table min-w-[660px]"><thead><tr><th>{{ __('site.topic') }}</th><th>{{ $usesDetailedAcademicPeriod ? __('site.class_level') : __('site.grade') }}</th><th>{{ __('site.department') }}</th><th>{{ __('site.date') }}</th><th><span class="sr-only">{{ __('site.download') }}</span></th></tr></thead><tbody>
                @foreach($items as $item)<tr wire:key="resource-{{ $item->id }}"><td><p class="mb-1 text-xs text-jts">{{ __('site.'.$item->type->code) }}</p><a class="font-semibold hover:text-jts" href="{{ \App\Support\Site::path('/download/resources/'.$item->id) }}">{{ $item->title }}</a></td><td>{{ $usesDetailedAcademicPeriod ? ($item->resource_level_label ?? '-') : $item->school_level_label }}</td><td>{{ __('site.'.$item->department->code) }}</td><td class="whitespace-nowrap text-muted">{{ $item->upload_date->translatedFormat('d M Y') }}</td><td><a href="{{ \App\Support\Site::path('/download/resources/'.$item->id) }}" class="icon-btn" title="{{ __('site.download') }}" aria-label="{{ __('site.download') }} {{ $item->title }}"><x-site-icon name="download" class="size-4"/></a></td></tr>@endforeach
            </tbody></table></div>
        @endif
    </div>
    <div class="mt-10">{{ $items->links() }}</div>
</div>
