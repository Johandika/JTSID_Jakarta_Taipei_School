# Design Spec: Contact Page Modernization & CMS Message Inbox

## 1. Overview & Context

This design outlines the complete modernization of the public **Contact** page (`/contact`, `/id/contact`, `/zh/contact`) and the backend data collection system for Jakarta Taipei School (JTS).

### Objectives:
- Replace the existing plain static contact page with an elegant, responsive dual-column layout based on the user-provided design mockup.
- **Left Column**: Rich structured contact information (Address, Telephone numbers, Email, and Operational Hours) with circular icon badges and subtle branding motifs.
- **Right Column**: Interactive Livewire contact form (`ContactForm`) providing real-time client-side validation, live character counting, a smooth loading spinner, and an immediate in-place success alert without page reload.
- **Full-Width Map**: Embedded official Google Map using the exact embed code specified by the user with responsive aspect ratio and rounded card styling.
- **Backend CMS Inbox**: A dedicated Filament Resource (`ContactMessageResource`) allowing administrators to view, filter, update status (*Unread*, *Read*, *Replied*, *Archived*), and add internal administrative notes with an unread badge counter in the navigation bar.

---

## 2. Architecture & Data Model

### 2.1 Database Migration (`create_contact_messages_table`)
A new database table `contact_messages` will be created with the following schema:

| Column | Type | Modifiers | Description |
|---|---|---|---|
| `id` | `bigIncrements` | Primary | Unique identifier |
| `name` | `string` | `191` | Sender's full name |
| `email` | `string` | `191` | Sender's email address |
| `phone` | `string` | `50`, `nullable` | Sender's telephone/WhatsApp number |
| `subject` | `string` | `100` | Inquiry category / subject |
| `message` | `text` | Max 500 chars | Body of the message |
| `status` | `string` | Default: `'unread'` | Enum: `unread`, `read`, `replied`, `archived` |
| `admin_notes` | `text` | `nullable` | Internal staff notes on handling the inquiry |
| `ip_address` | `string` | `45`, `nullable` | Client IP address for spam prevention |
| `user_agent` | `string` | `255`, `nullable` | Browser User-Agent summary |
| `created_at` | `timestamp` | Default: `now()` | Submission timestamp |
| `updated_at` | `timestamp` | Default: `now()` | Last modification timestamp |

### 2.2 Eloquent Model (`App\Models\ContactMessage`)
- Extends standard `Illuminate\Database\Eloquent\Model` (or `CmsModel`).
- Status constants:
  - `STATUS_UNREAD = 'unread'`
  - `STATUS_READ = 'read'`
  - `STATUS_REPLIED = 'replied'`
  - `STATUS_ARCHIVED = 'archived'`
- Available Subjects:
  - `admissions`: Penerimaan Siswa Baru / Admissions
  - `mandarin_program`: Program Mandarin / Short-term Mandarin
  - `general`: Pertanyaan Umum / General Inquiry
  - `academics`: Akademik & Kurikulum / Academics & Curriculum
  - `other`: Lainnya / Other
- Helper methods:
  - `getStatusBadgeColor(): string`: `unread` (warning/danger), `read` (info/gray), `replied` (success), `archived` (slate).
  - `getStatusLabel(?string $locale): string`.
  - `scopeUnread(Builder $query)`.
  - `scopeRecent(Builder $query)`.

---

## 3. Frontend Implementation

### 3.1 Page Template (`resources/views/public/pages/contact.blade.php`)
1. **Hero Banner**:
   - Replaces the generic static wrapper with a styled header section featuring the dark green gradient overlay (`#0a2e1f` to `#176443`), breadcrumbs (`Home > Contact`), title, and multilingual subtitle.
2. **Main Section Shell**:
   - Two-column responsive grid (`grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10`).
   - **Left Column (`lg:col-span-5`) — Contact Information Card**:
     - Modern card container with subtle shadow, border, and light green background tint (`bg-[#f8faf9]`).
     - Title: "Contact Information".
     - Subtitle: "Hubungi kami melalui informasi berikut atau isi formulir di samping."
     - 4 item rows with circular green icon badges (`bg-[#e7f3ec] text-[#176443]`):
       1. **Address**: `Jl. Raya Kelapa Hybrida Blok QA3, Kelapa Gading, Jakarta Utara 14250, Indonesia` (with Map Pin icon).
       2. **Telephone**: `+62 (21) 452 3273` & `+62 (21) 453 0535` (with Phone icon, clickable `tel:` links).
       3. **Email**: `jts@jtsid.org` (with Mail icon, clickable `mailto:` link).
       4. **Jam Operasional**: `Senin - Jumat\n07:30 - 16:00 WIB` (with Clock icon).
   - **Right Column (`lg:col-span-7`) — Livewire Contact Form**:
     - Embedded via `<livewire:contact-form />`.
3. **Google Map Section**:
   - Full-width card with rounded corners (`rounded-2xl sm:rounded-3xl`), border, and overflow hidden.
   - Embeds the official JTS iframe provided:
     `<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2472.012996499217!2d106.90572140965325!3d-6.146191130717376!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f54e9c65d815%3A0x37e85c3839067f4!2sJakarta%20Taipei%20School!5e1!3m2!1sen!2sid!4v1790326143752!5m2!1sen!2sid" class="w-full h-[400px] sm:h-[450px] border-0" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>`

### 3.2 Livewire Component (`App\Livewire\ContactForm` & `resources/views/livewire/contact-form.blade.php`)
- **Properties**:
  - `name`: string, required, max 100
  - `email`: string, required, email, max 100
  - `phone`: string, nullable, max 30
  - `subject`: string, required
  - `message`: string, required, min 10, max 500
  - `isSuccess`: boolean, default false
- **Methods**:
  - `submit()`: Validates input, rate-limits submissions (e.g. 5 requests per 10 minutes per IP), creates a `ContactMessage` record, sets `isSuccess = true`, resets form input.
  - `resetForm()`: Clears the success banner and resets state for another submission.
- **UI Elements**:
  - Floating/clean field labels with red asterisks on mandatory fields.
  - Real-time character counter on the textarea: `{{ strlen($message) }}/500`.
  - Submit button with JTS green theme (`bg-[#0d6b46] hover:bg-[#0a5236]`), right arrow icon, and Livewire loading spinner (`wire:loading`).
  - Animated green alert box upon successful submission: *"Pesan Anda telah berhasil dikirim. Tim kami akan segera menghubungi Anda."*

---

## 4. Filament CMS Resource (`ContactMessageResource`)

- **Resource Class**: `App\Filament\Resources\ContactMessages\ContactMessageResource`
- **Navigation Configuration**:
  - Group: `School` (or `Communications`)
  - Icon: `heroicon-o-envelope`
  - Sort: `9`
  - Navigation Badge: Shows the total number of unread messages (`ContactMessage::where('status', 'unread')->count()`).
- **Table Columns**:
  - `status`: Badge with colors (`unread` = warning/yellow, `read` = info/blue, `replied` = success/emerald, `archived` = gray).
  - `name`: Bold text, searchable.
  - `email`: Text with copyable icon, searchable.
  - `phone`: Text with copyable icon.
  - `subject`: Badge or formatted text.
  - `created_at`: Formatted date & time (`d M Y H:i`), sortable, default sort `desc`.
- **Table Filters**:
  - SelectFilter by `status`.
  - SelectFilter by `subject`.
- **View / Edit Modal**:
  - Full display of sender name, email, phone, subject, message body, submission IP, and timestamp.
  - Quick action or select to update status (`read`, `replied`, `archived`).
  - Internal admin notes textarea (`admin_notes`).
- **Bulk Actions**:
  - Bulk Mark as Read.
  - Bulk Delete.

---

## 5. Localization (Trilingual: ID, EN, ZH-TW)

Update `lang/id/pages.php`, `lang/en/pages.php`, and `lang/zh-TW/pages.php`:
- `contact_info_title`: "Contact Information" / "Informasi Kontak" / "聯絡資訊"
- `contact_info_subtitle`: "Hubungi kami melalui informasi berikut atau isi formulir di samping."
- `opening_hours_label`: "Jam Operasional" / "Opening Hours" / "辦公時間"
- `opening_hours_value`: "Senin – Jumat 07:30 – 16:00 WIB" / "Monday – Friday 07:30 – 16:00 WIB" / "週一至週五 07:30 – 16:00 WIB"
- `form_title`: "Kirim Pesan" / "Send a Message" / "發送訊息"
- `form_subtitle`: "Silakan isi formulir di bawah ini dan tim kami akan segera menghubungi Anda."
- Field labels, placeholders, and error messages.
- Subjects options:
  - Admissions
  - Mandarin Programs
  - General Inquiry
  - Academics
  - Other

---

## 6. Testing Strategy

1. **Feature Tests (`tests/Feature/ContactFormTest.php`)**:
   - `test_contact_page_renders_with_information_and_map`: Verifies that the contact page returns 200, displays address, phone, email, opening hours, and contains the Google Maps iframe.
   - `test_contact_form_submits_successfully_and_persists_to_database`: Tests Livewire submission of valid data, ensuring the message is saved with status `unread` and IP address.
   - `test_contact_form_validates_required_fields`: Asserts validation errors when name, email, subject, or message are missing or invalid.
   - `test_contact_form_enforces_character_limits`: Tests 500-character boundary condition on message.
   - `test_admin_can_view_and_manage_messages_in_filament`: Asserts admin authentication, table access, and status transition.
2. **Pint Formatting**: Run Pint on all new and modified PHP files.
3. **Regression Tests**: Ensure `CmsTest` and `MandarinProgramTest` continue to pass 100%.
