<div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 lg:p-10 shadow-sm relative overflow-hidden">
    {{-- Header --}}
    <div class="mb-6 sm:mb-8">
        <h3 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
            {{ $page['form_title'] ?? 'Kirim Pesan' }}
        </h3>
        <p class="mt-2 text-sm sm:text-base text-slate-600">
            {{ $page['form_subtitle'] ?? 'Silakan isi formulir di bawah ini dan tim kami akan segera menghubungi Anda.' }}
        </p>
    </div>

    {{-- Rate limit error banner --}}
    @if($rateLimitError)
        <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-start gap-3">
            <x-site-icon name="alert-circle" class="size-5 text-amber-600 shrink-0 mt-0.5" />
            <div>
                <p class="font-semibold">{{ __('Perhatian') }}</p>
                <p class="mt-0.5">{{ $rateLimitError }}</p>
            </div>
        </div>
    @endif

    {{-- Success State Banner --}}
    @if($isSuccess)
        <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="size-10 rounded-full bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-700">
                    <x-site-icon name="check" class="size-5" />
                </div>
                <div>
                    <h4 class="font-bold text-emerald-900 text-base">
                        {{ $page['success_title'] ?? 'Pesan Berhasil Terkirim!' }}
                    </h4>
                    <p class="text-xs sm:text-sm text-emerald-700 mt-0.5">
                        {{ $page['success_message'] ?? 'Terima kasih telah menghubungi kami. Tim kami akan segera menanggapi pesan Anda.' }}
                    </p>
                </div>
            </div>
            <button
                type="button"
                wire:click="resetSuccess"
                class="self-start sm:self-center px-4 py-2 text-xs font-semibold text-emerald-800 hover:text-emerald-950 bg-emerald-100/80 hover:bg-emerald-200 rounded-lg transition-colors"
            >
                {{ $page['send_another'] ?? 'Kirim Pesan Lain' }}
            </button>
        </div>
    @endif

    {{-- Form --}}
    <form wire:submit.prevent="submit" class="space-y-5">
        {{-- Nama Lengkap --}}
        <div>
            <label for="contact-name" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                {{ $page['name_label'] ?? 'Nama Lengkap' }} <span class="text-rose-500">*</span>
            </label>
            <input
                id="contact-name"
                wire:model.defer="name"
                type="text"
                placeholder="{{ $page['name_placeholder'] ?? 'Masukkan nama lengkap Anda' }}"
                class="w-full px-4 py-3 rounded-xl border text-sm text-slate-900 placeholder:text-slate-400 bg-white focus:outline-none focus:ring-2 focus:ring-[#0d6b46]/20 focus:border-[#0d6b46] transition-all {{ $errors->has('name') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200' }}"
            />
            @error('name')
                <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- Row: Email & No. Telepon --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
            <div>
                <label for="contact-email" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                    {{ $page['email_label'] ?? 'Email' }} <span class="text-rose-500">*</span>
                </label>
                <input
                    id="contact-email"
                    wire:model.defer="email"
                    type="email"
                    placeholder="{{ $page['email_placeholder'] ?? 'Masukkan alamat email Anda' }}"
                    class="w-full px-4 py-3 rounded-xl border text-sm text-slate-900 placeholder:text-slate-400 bg-white focus:outline-none focus:ring-2 focus:ring-[#0d6b46]/20 focus:border-[#0d6b46] transition-all {{ $errors->has('email') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200' }}"
                />
                @error('email')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="contact-phone" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                    {{ $page['phone_label'] ?? 'No. Telepon' }} <span class="text-rose-500">*</span>
                </label>
                <input
                    id="contact-phone"
                    wire:model.defer="phone"
                    type="tel"
                    placeholder="{{ $page['phone_placeholder'] ?? 'Masukkan nomor telepon Anda' }}"
                    class="w-full px-4 py-3 rounded-xl border text-sm text-slate-900 placeholder:text-slate-400 bg-white focus:outline-none focus:ring-2 focus:ring-[#0d6b46]/20 focus:border-[#0d6b46] transition-all {{ $errors->has('phone') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200' }}"
                />
                @error('phone')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Subjek --}}
        <div>
            <label for="contact-subject" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                {{ $page['subject_label'] ?? 'Subjek' }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select
                    id="contact-subject"
                    wire:model.defer="subject"
                    class="w-full px-4 py-3 rounded-xl border text-sm text-slate-900 bg-white appearance-none focus:outline-none focus:ring-2 focus:ring-[#0d6b46]/20 focus:border-[#0d6b46] transition-all cursor-pointer {{ $errors->has('subject') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200' }}"
                >
                    <option value="">{{ $page['subject_placeholder'] ?? 'Pilih subjek' }}</option>
                    <option value="admissions">{{ $page['subject_admissions'] ?? 'Penerimaan Siswa Baru (Admissions)' }}</option>
                    <option value="mandarin_program">{{ $page['subject_mandarin'] ?? 'Program Mandarin / TOCFL' }}</option>
                    <option value="general">{{ $page['subject_general'] ?? 'Pertanyaan Umum' }}</option>
                    <option value="academics">{{ $page['subject_academics'] ?? 'Akademik & Kurikulum' }}</option>
                    <option value="other">{{ $page['subject_other'] ?? 'Lainnya' }}</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                    <x-site-icon name="chevron-down" class="size-4" />
                </div>
            </div>
            @error('subject')
                <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- Pesan with Character Counter --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="contact-message" class="block text-xs sm:text-sm font-semibold text-slate-700">
                    {{ $page['message_label'] ?? 'Pesan' }} <span class="text-rose-500">*</span>
                </label>
                <span class="text-xs text-slate-400" id="message-counter">
                    {{ strlen($message) }}/500
                </span>
            </div>
            <textarea
                id="contact-message"
                wire:model.live.debounce.150ms="message"
                rows="4"
                maxlength="500"
                placeholder="{{ $page['message_placeholder'] ?? 'Tulis pesan Anda di sini...' }}"
                class="w-full px-4 py-3 rounded-xl border text-sm text-slate-900 placeholder:text-slate-400 bg-white focus:outline-none focus:ring-2 focus:ring-[#0d6b46]/20 focus:border-[#0d6b46] transition-all {{ $errors->has('message') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200' }}"
            ></textarea>
            @error('message')
                <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- Submit Button --}}
        <div class="pt-2">
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center justify-center gap-2 px-6 sm:px-7 py-3 rounded-xl bg-[#0d6b46] hover:bg-[#095235] active:bg-[#073d27] text-white text-sm sm:text-base font-semibold transition-all duration-200 shadow-sm hover:shadow focus:outline-none focus:ring-2 focus:ring-[#0d6b46]/40 disabled:opacity-70 disabled:cursor-not-allowed cursor-pointer"
            >
                <span wire:loading.remove>
                    {{ $page['submit_button'] ?? 'Kirim Pesan' }}
                </span>
                <span wire:loading.inline-flex class="items-center gap-2">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ $page['submitting'] ?? 'Mengirim...' }}</span>
                </span>
                <x-site-icon wire:loading.remove name="arrow-right" class="size-4" />
            </button>
        </div>
    </form>
</div>
