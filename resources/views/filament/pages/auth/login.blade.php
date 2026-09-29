@php
    $activeLocale = app()->getLocale() === 'zh_TW' ? 'zh-TW' : app()->getLocale();
@endphp

<div class="jts-auth-wrapper">
    <div class="jts-auth-card">
        {{-- Sisi Kiri: Visual Mesh Gradient JTSID --}}
        <div class="jts-card-left">
            <div class="jts-left-top">
                {{-- Asterisk icon putih sesuai referensi gambar --}}
                <div class="jts-left-icon">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6"
                        stroke-linecap="round">
                        <line x1="12" y1="2" x2="12" y2="22"></line>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                        <line x1="19.07" y1="4.93" x2="4.93" y2="19.07"></line>
                    </svg>
                </div>

                {{-- Logo JTS Putih --}}
                <div class="jts-left-logo-wrap">
                    <img src="{{ asset('images/logo-white.webp') }}" alt="Jakarta Taipei School" class="jts-left-logo"
                        width="140" height="42">
                </div>
            </div>

            <div class="jts-left-bottom">
                <span class="jts-left-kicker">Jakarta Taipei School</span>
                <h2 class="jts-left-title">{{ __('admin.login.card_title') }}</h2>
                <p class="jts-left-desc">{{ __('admin.login.card_description') }}</p>
            </div>
        </div>

        {{-- Sisi Kanan: Form Login --}}
        <div class="jts-card-right">
            <div class="jts-form-inner">
                <div class="jts-right-top">
                    {{-- Asterisk icon warna hijau JTSID sesuai referensi --}}
                    <div class="jts-brand-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#176443" stroke-width="2.8"
                            stroke-linecap="round">
                            <line x1="12" y1="2" x2="12" y2="22"></line>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                            <line x1="19.07" y1="4.93" x2="4.93" y2="19.07"></line>
                        </svg>
                    </div>

                    {{-- Language Switcher --}}
                    <div class="jts-lang-nav">
                        <a href="{{ route('admin.language', 'en') }}"
                            class="jts-lang-btn {{ $activeLocale === 'en' ? 'active' : '' }}">EN</a>
                        <span class="jts-lang-dot">•</span>
                        <a href="{{ route('admin.language', 'id') }}"
                            class="jts-lang-btn {{ $activeLocale === 'id' ? 'active' : '' }}">ID</a>
                        <span class="jts-lang-dot">•</span>
                        <a href="{{ route('admin.language', 'zh-TW') }}"
                            class="jts-lang-btn {{ $activeLocale === 'zh-TW' ? 'active' : '' }}">繁中</a>
                    </div>
                </div>

                <div class="jts-form-intro">
                    <h1 class="jts-title">{{ __('admin.login.title') }}</h1>
                    <p class="jts-subtitle">{{ __('admin.login.subtitle') }}</p>
                </div>

                <div class="jts-form-container">
                    {{ $this->content }}
                </div>

                <div class="jts-form-footer">
                    <a href="{{ url('/') }}" class="jts-back-link">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5M12 19l-7-7 7-7" />
                        </svg>
                        <span>{{ __('admin.login.back_to_website') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Reset & Container */
    body {
        margin: 0;
        background-color: #f0f2f8;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: #1f2937;
    }

    .jts-auth-viewport {
        min-height: 100vh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(16px, 2.5vw, 36px);
        box-sizing: border-box;
        background-color: #f0f2f8;
    }

    .jts-auth-wrapper {
        width: 100%;
        max-width: 1360px;
        display: flex;
        justify-content: center;
        margin: 0 auto;
    }

    /* Main Card - Fluid Full Container */
    .jts-auth-card {
        width: 100%;
        min-height: clamp(580px, 78vh, 740px);
        background-color: #ffffff;
        border-radius: 32px;
        box-shadow: 0 25px 70px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(226, 232, 240, 0.85);
        padding: 16px;
        display: flex;
        box-sizing: border-box;
        overflow: hidden;
    }

    /* Sisi Kiri (Gradient Card) */
    .jts-card-left {
        flex: 0 0 45%;
        border-radius: 24px;
        padding: clamp(36px, 3.8vw, 54px) clamp(30px, 3.2vw, 46px);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-sizing: border-box;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        background-color: #064e3b;
        background-image:
            radial-gradient(at 10% 15%, rgba(52, 211, 153, 0.75) 0px, transparent 55%),
            radial-gradient(at 85% 20%, rgba(20, 184, 166, 0.55) 0px, transparent 50%),
            radial-gradient(at 50% 60%, rgba(15, 118, 110, 0.65) 0px, transparent 60%),
            radial-gradient(at 80% 85%, rgba(6, 78, 59, 0.95) 0px, transparent 60%),
            radial-gradient(at 10% 90%, rgba(2, 44, 34, 1) 0px, transparent 70%);
    }

    .jts-left-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .jts-left-icon svg {
        display: block;
        width: 30px;
        height: 30px;
    }

    .jts-left-logo-wrap {
        display: flex;
        align-items: center;
    }

    .jts-left-logo {
        height: 36px;
        width: auto;
        object-fit: contain;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.15));
    }

    .jts-left-bottom {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .jts-left-kicker {
        font-size: 13px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.85);
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    .jts-left-title {
        margin: 0;
        font-size: clamp(24px, 2.3vw, 34px);
        font-weight: 700;
        line-height: 1.25;
        color: #ffffff;
        letter-spacing: -0.4px;
    }

    .jts-left-desc {
        margin: 0;
        font-size: clamp(13px, 1.05vw, 15px);
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.82);
        max-width: 460px;
    }

    /* Sisi Kanan (Form) */
    .jts-card-right {
        flex: 1 1 55%;
        padding: clamp(32px, 3.5vw, 48px) clamp(32px, 4vw, 64px);
        display: flex;
        flex-direction: column;
        justify-content: center;
        box-sizing: border-box;
    }

    .jts-form-inner {
        width: 100%;
        max-width: 520px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .jts-right-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .jts-brand-icon svg {
        display: block;
        width: 26px;
        height: 26px;
    }

    .jts-lang-nav {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
    }

    .jts-lang-btn {
        color: #6b7280;
        text-decoration: none;
        font-weight: 500;
        padding: 3px 8px;
        border-radius: 6px;
        transition: color 0.15s, background-color 0.15s;
    }

    .jts-lang-btn:hover {
        color: #111827;
    }

    .jts-lang-btn.active {
        color: #176443;
        font-weight: 700;
        background-color: #eaf4ee;
    }

    .jts-lang-dot {
        color: #d1d5db;
        font-size: 10px;
    }

    .jts-form-intro {
        margin-top: 18px;
        margin-bottom: 12px;
    }

    .jts-title {
        margin: 0 0 8px 0;
        font-size: clamp(26px, 2.2vw, 34px);
        font-weight: 700;
        color: #111827;
        letter-spacing: -0.5px;
    }

    .jts-subtitle {
        margin: 0;
        font-size: clamp(13px, 1.05vw, 15px);
        line-height: 1.5;
        color: #6b7280;
    }

    .jts-form-container {
        margin-top: 20px;
        margin-bottom: 12px;
    }

    /* Scoped Filament Form Styles */
    .jts-form-container .fi-fo-field-wrp {
        margin-bottom: 16px;
    }

    .jts-form-container .fi-fo-field-wrp-label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .jts-form-container .fi-input-wrp {
        border: 1px solid #d1d5db;
        border-radius: 12px;
        background-color: #ffffff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .jts-form-container .fi-input-wrp:focus-within {
        border-color: #176443;
        box-shadow: 0 0 0 3px rgba(23, 100, 67, 0.14);
    }

    .jts-form-container .fi-input {
        height: 46px;
        padding: 0 16px;
        font-size: 15px;
        color: #111827;
    }

    .jts-form-container .fi-btn {
        width: 100%;
        height: 48px;
        border-radius: 12px;
        background: #176443;
        color: #ffffff;
        font-size: 15px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(23, 100, 67, 0.28);
        transition: background-color 0.15s, box-shadow 0.15s, transform 0.1s;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 14px;
    }

    .jts-form-container .fi-btn:hover {
        background: #125236;
        box-shadow: 0 12px 24px rgba(23, 100, 67, 0.38);
    }

    .jts-form-container .fi-btn:active {
        transform: scale(0.99);
    }

    .jts-form-container .fi-checkbox-input:checked {
        background-color: #176443;
        border-color: #176443;
    }

    .jts-form-footer {
        margin-top: 24px;
        text-align: center;
    }

    .jts-back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #6b7280;
        text-decoration: none;
        transition: color 0.15s;
    }

    .jts-back-link:hover {
        color: #176443;
    }

    .jts-back-link svg {
        display: block;
        width: 15px;
        height: 15px;
    }

    /* Tablet breakpoint (1024px) */
    @media (max-width: 1024px) {
        .jts-card-left {
            flex: 0 0 42%;
            padding: 32px 26px;
        }

        .jts-card-right {
            flex: 1 1 58%;
            padding: 32px 28px;
        }
    }

    /* Mobile breakpoint (860px) */
    @media (max-width: 860px) {
        .jts-auth-viewport {
            padding: 12px;
        }

        .jts-auth-card {
            flex-direction: column;
            border-radius: 24px;
            padding: 12px;
            min-height: auto;
        }

        .jts-card-left {
            flex: none;
            width: 100%;
            min-height: 240px;
            padding: 26px 20px;
            border-radius: 18px;
        }

        .jts-card-right {
            flex: none;
            width: 100%;
            padding: 28px 16px;
        }

        .jts-form-inner {
            max-width: 100%;
        }
    }
</style>