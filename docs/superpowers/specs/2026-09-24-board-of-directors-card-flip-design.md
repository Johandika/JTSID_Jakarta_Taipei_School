# Design Spec: Board of Directors Interactive 3D Flip Cards

**Date:** 2026-09-24  
**Status:** In Review  
**Topic:** `board-of-directors-card-flip`  
**Target Page:** `resources/views/public/pages/board-of-directors.blade.php`

---

## 1. Overview & Objectives

Implement an interactive 1x4 grid of director cards for the Jakarta Taipei School (JTS) Board of Directors page (`/board-of-directors`).
- **Visual Style:** Elegant, high-end institutional aesthetics matching the provided reference (grayscale portrait photography, crisp typography, refined layout).
- **Core Interaction:** Click / tap on a card performs a smooth 3D flip (180° Y-axis rotation), revealing detailed information on the back (educational history, career highlights, biographical overview).
- **No External Dependencies:** Built using native CSS 3D transforms and project-native Alpine.js (bundled with Livewire).

---

## 2. Technical Architecture & Decisions

### 2.1 Why No Extra Library?
- **Zero Bundle Overhead:** Avoids heavy third-party carousel or 3D card npm packages.
- **Hardware-Accelerated 60 FPS:** CSS `transform: rotateY(180deg)` with `transform-style: preserve-3d` runs directly on the GPU.
- **Accessible & Touch-Friendly:** Alpine.js provides straightforward reactive state (`flipped: false`) per card, allowing mouse clicks, mobile tap gestures, and keyboard trigger (`Enter` / `Space`).

### 2.2 CSS 3D Utility Classes in `resources/css/app.css`
```css
.perspective-1000 {
    perspective: 1000px;
}
.transform-style-3d {
    transform-style: preserve-3d;
}
.backface-hidden {
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}
.rotate-y-180 {
    transform: rotateY(180deg);
}
```

---

## 3. UI/UX & Card Anatomy

### 3.1 Responsive Grid Layout
- **Desktop (`lg:grid-cols-4`)**: 1 row, 4 columns (1x4 grid as requested).
- **Tablet (`sm:grid-cols-2`)**: 2 rows, 2 columns.
- **Mobile (`grid-cols-1`)**: 1 column stacked.

### 3.2 Front Face (Default State)
- **Portrait Photo:** Aspect ratio 4:5 (or square/portrait), rounded corners (`rounded-2xl`), grayscale filter (`grayscale hover:grayscale-0 transition-all duration-500`), subtle border.
- **Role / Jabatan:** Small uppercase eyebrow text (e.g., `Ketua Dewan Pengurus`, `Board Chairman`) in muted JTS green tone.
- **Director Name:** Prominent serif / display typography (e.g., `Dr. Alexander Chen`), matching the elegant reference design.
- **Interaction Cue:** Subtle floating pill / icon at the bottom-right or corner with tooltip ("*Klik untuk profil / Click for profile*").

### 3.3 Back Face (Flipped State)
- **Background & Container:** Clean card background with JTS brand accent border (`border-[#176443]/30`, subtle shadow, soft background).
- **Header:** Director's name and role in compact layout.
- **Riwayat Pendidikan (Education):** List of degrees and alma maters.
- **Biografi Singkat (Biography / Experience):** 2-3 sentences explaining leadership contributions and expertise.
- **Return Action:** "Kembali / Back" button or rotate icon at the bottom to flip back.

---

## 4. Content & Localization Structure

Dummy data for 4 directors will be defined in language files (`lang/id/pages.php`, `lang/en/pages.php`, `lang/zh-TW/pages.php`):

```php
'directors' => [
    [
        'id' => 'alexander-chen',
        'name' => 'Dr. Alexander Chen',
        'role' => 'Ketua Dewan Pengurus',
        'image' => '/images/directors/director-1.svg', // or placeholder
        'education' => [
            'Ph.D. in Educational Leadership - National Taiwan Normal University',
            'M.Ed. in International Education - University of Melbourne',
        ],
        'experience' => 'Berpengalaman lebih dari 25 tahun dalam tata kelola institusi pendidikan internasional dan pengembangan kurikulum trilingual.',
    ],
    // 3 more directors: Wakil Ketua, Direktur Pengawas Pendidikan, Direktur Keuangan & Operasional
]
```

---

## 5. Verification & Testing

1. **PHPUnit Feature Tests:**
   - Existing `CmsTest.php` requires `class="prose"` on all static pages. The page will keep an introductory narrative with `class="prose"` so existing assertions continue to pass without regression.
   - Run `vendor/bin/phpunit tests/Feature/CmsTest.php`.
2. **Code Formatting:**
   - Run `vendor/bin/pint --format agent`.
3. **Browser & Mobile Testing:**
   - Verify 3D flip animation is smooth on Chrome/Safari/Firefox and responsive across screen breakpoints.
