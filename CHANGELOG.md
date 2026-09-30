# Changelog

All changes made to this project are documented here in reverse chronological order.

---

## [2026-09-28] — Blog Feature Fixes & Improvements

### 1. SEO Field Name Bug Fix (Critical)
**Files changed:**
- `app/Http/Controllers/Admin/BlogController.php`

**Problem:**
The `store()` and `update()` methods were saving SEO data using wrong column names (`title` and `description`). The actual database columns in the `blog_seos` table are `meta_title` and `meta_description`, so SEO data was never actually being saved.

**Fix:**
Changed `title` → `meta_title` and `description` → `meta_description` in both `store()` and `update()` methods.

---

### 2. Status Badge Always Showed "Published"
**Files changed:**
- `resources/views/admin/blogs/index.blade.php`

**Problem:**
The condition `@if($blog->status)` was truthy for any non-empty string, including `"draft"`. Every blog post was showing a green "Published" badge regardless of its actual status.

**Fix:**
Changed to strict string comparison `@if($blog->status === 'published')`. Also added a yellow "Scheduled" badge for scheduled posts.

---

### 3. N+1 Query on Blog List
**Files changed:**
- `app/Http/Controllers/Admin/BlogController.php`

**Problem:**
The `index()` method was loading blogs without eager-loading the author relationship (`Blog::latest()->paginate(10)`). The view calls `$blog->author->name` for every row, causing one extra database query per blog post.

**Fix:**
Added eager loading: `Blog::with('author')->latest()->paginate(10)`.

---

### 4. published_at Not Updated on Edit
**Files changed:**
- `app/Http/Controllers/Admin/BlogController.php`

**Problem:**
The `update()` method did not include `published_at` in the update array. If a blog was changed from draft to published via the edit form, the `published_at` timestamp stayed null.

**Fix:**
Added `published_at` to the update array:
```php
'published_at' => $request->status === 'published' ? ($blog->published_at ?? now()) : null,
```
This preserves the original publish date if already set, or sets it to now if publishing for the first time.

---

### 5. Removed Visibility Dropdown
**Files changed:**
- `resources/views/admin/blogs/create.blade.php` (already commented out by developer)
- `resources/views/admin/blogs/edit.blade.php`

**Change:**
Removed the Visibility dropdown (Public/Private) from the edit blog page to match the create blog page where it was already commented out.

---

### 6. Added Meta Keywords Field to SEO Settings
**Files changed:**
- `resources/views/admin/blogs/create.blade.php`
- `resources/views/admin/blogs/edit.blade.php`
- `app/Http/Controllers/Admin/BlogController.php`

**Change:**
Added a new "Meta Keywords" input field in the SEO Settings panel on both create and edit blog pages. Keywords are stored in the existing `focus_keyword` column in the `blog_seos` table — no migration was needed.

- `create.blade.php` — added `<input name="seo_keywords">` field
- `edit.blade.php` — added same field pre-populated with `$blog->seo->focus_keyword`
- `BlogController.php` — both `store()` and `update()` now save `seo_keywords` into `focus_keyword`

---

## [2026-09-28] — Project Setup

### Environment Setup
- Installed PHP 8.5.11 (NTS VS17 x64) at `C:\php`
- Added `C:\php` to System PATH (moved above XAMPP PHP entry)
- Enabled PHP extensions in `php.ini`: `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `pdo_sqlite`, `gd`, `mysqli`
- Installed Composer 2.10.3
- Installed Node.js (already present) and ran `npm install`

### Laravel Project Setup
- Ran `composer install` to install all PHP dependencies
- Created `.env` file from friend-provided `.env.local` config
- Database: MySQL via XAMPP, database name `bio_agri_db`
- Ran `php artisan migrate` — created 13 tables successfully
- Ran `npm run build` — frontend assets compiled (Sass deprecation warnings are non-critical, from Bootstrap internals)
- Ran `php artisan optimize:clear` — cleared all cached files
- Ran `php artisan storage:link` — linked `storage/app/public` to `public/storage`
- Created admin user via `php artisan db:seed` — credentials: `admin@gmail.com` / `12345678`
- App running at `http://localhost:8089` via `php artisan serve --port 8089`

## [2026-09-28] — Header & Navigation

### 7. Added Profile Link to User Dropdown
**Files changed:**
- `resources/views/admin/layouts/partials/header.blade.php`

**Change:**
Added a Profile link to the top-right user dropdown menu so users can navigate to their profile page. Also fixed the "Signed in as Admin" text to dynamically show the actual logged-in user's name. Added icons to both the Profile and Sign out items for better visual clarity.

The profile route `admin.profile.index` and `ProfileController` were already in place — only the dropdown link was missing.

---

## [2026-09-28] — Profile Page

### 8. Added Show/Hide Password Toggle on Profile Page
**Files changed:**
- `resources/views/admin/profile/index.blade.php`

**Change:**
Added an eye icon toggle button to all three password fields on the Change Password form (Current Password, New Password, Confirm New Password). Clicking the eye icon switches the field between `type="password"` and `type="text"`, and the icon switches between `bi-eye` and `bi-eye-slash` to reflect the current state.

---

## [2026-09-28] — Auth

### 9. Forgot Password — Show Reset Link on Page
**Files changed:**
- `app/Http/Controllers/Auth/ForgotPasswordController.php`
- `resources/views/auth/passwords/email.blade.php`

**Problem:**
Forgot password was not working because `.env` had `MAIL_MAILER=log`, meaning emails were never actually sent to users.

**Fix:**
Overrode `sendResetLinkEmail()` in `ForgotPasswordController` to generate the reset token and build the reset URL directly, then flash it to the session. The view now displays the reset link as a clickable button on the same page instead of sending an email. This is suitable for local/internal admin panel use.

---

## [2026-09-28] — Resources Section (Full CRUD Implementation)

### 10. Resource Model Fixed
**File changed:** `app/Models/Resource.php`

The model was an empty stub. Added:
- `SoftDeletes` trait — matches the `deleted_at` column in the migration
- `$fillable` — all 13 non-PK columns: title, slug, short_description, description, featured_image, file, file_type, status, published_at, meta_title, meta_description, meta_keywords
- `$casts` — `status` as boolean, `published_at` as datetime

---

### 11. Resource Route Registered
**File changed:** `routes/web.php`

Added `Route::resource('resources', ResourceController::class)` inside the admin auth middleware group. This registers all 7 RESTful routes under `/admin/resources`.

---

### 12. ResourceController Fully Implemented
**File changed:** `app/Http/Controllers/Admin/ResourceController.php`

All 7 methods were empty stubs. Implemented:
- `index()` — paginated list (10 per page), returns `admin.resources.index` view
- `create()` — returns `admin.resources.create` view
- `store()` — validates title, slug (unique), status, description; creates resource with all fields including `published_at` logic; returns AJAX JSON response
- `show()` — redirects to edit page (no separate show view needed in admin)
- `edit()` — returns `admin.resources.edit` view with `$resource`
- `update()` — validates with slug unique-ignore for current record; updates all fields; preserves `published_at` if already set; returns AJAX JSON response
- `destroy()` — soft deletes the record; returns AJAX JSON response

---

### 13. Resources Views Created (3 files)
**Files created:**
- `resources/views/admin/resources/index.blade.php`
- `resources/views/admin/resources/create.blade.php`
- `resources/views/admin/resources/edit.blade.php`

**index.blade.php:**
- Paginated table with columns: ID, Title (+ short description preview), File Type badge, Status badge (Active/Inactive), Created At, Actions
- Edit button links to edit page, Delete via AJAX with SweetAlert confirmation
- `@forelse` handles empty state gracefully

**create.blade.php:**
- Title with auto-generated slug
- Short description textarea
- Quill.js rich text editor for full description
- File attachment panel: file path input + file type dropdown (PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, MP4, MP3, Other)
- SEO panel: meta title, meta description, meta keywords
- Featured image via integrated Media Library modal
- Status toggle (Active/Inactive) with Save Inactive and Publish buttons
- AJAX form submit with SweetAlert success/error feedback

**edit.blade.php:**
- Mirrors create.blade.php with all fields pre-populated from `$resource`
- File type dropdown pre-selects current value via `@foreach`
- Status pre-selected based on current value
- Featured image shows existing image if set
- AJAX PUT submit

---

## [2026-09-28] — Resources Module Bug Fixes

### 14. Fixed Resources Module Issues (Review Pass)

**Files changed:**
- `app/Http/Controllers/Admin/ResourceController.php`
- `resources/views/admin/resources/index.blade.php`
- `resources/views/admin/resources/create.blade.php`
- `resources/views/admin/resources/edit.blade.php`

**Fixes applied:**

1. **Validation rule for status** — Changed `required|boolean` to `required|in:0,1` in both `store()` and `update()`. HTML forms send status as string `"1"` or `"0"` which is more reliably validated with `in:0,1`.

2. **`Str::limit()` facade not available in Blade** — `index.blade.php` was calling `Str::limit()` without importing the facade. Changed to fully qualified `\Illuminate\Support\Str::limit()` to prevent errors when short_description is present.

3. **Scripts outside `@endsection` in create and edit views** — The `<script>` blocks in `create.blade.php` and `edit.blade.php` were placed inside `@section('content')` instead of `@push('scripts')`. Moved them to `@push('scripts')` / `@endpush` for correct Blade stack rendering.

4. **DataTables empty state conflict (previously fixed)** — `@forelse` with `colspan` row was conflicting with DataTables column detection. Changed to `@foreach` and let DataTables handle the empty state natively.

---

## [2026-09-28] — Contact Leads Section (Full Implementation)

### 15. ContactLead Model Fixed
**File changed:** `app/Models/ContactLead.php`

The model was an empty stub. Added:
- `SoftDeletes` trait — matches the `deleted_at` column in the migration
- `HasFactory` trait
- `$fillable` — all 9 columns: name, email, phone, company, subject, message, source, status, notes

---

### 16. Contact Leads Route Registered
**File changed:** `routes/web.php`

Added `Route::resource('contact-leads', ContactLeadController::class)->only(['index', 'show', 'update', 'destroy'])`.
No create/edit/store routes — contact leads come from the frontend contact form, not from the admin panel.

---

### 17. ContactLeadController Implemented
**File changed:** `app/Http/Controllers/Admin/ContactLeadController.php`

All methods implemented:
- `index()` — paginated list (15/page) + status counts array for summary badges (all, new, contacted, in_progress, converted, closed)
- `show()` — single lead detail view
- `update()` — validates status (enum: new/contacted/in_progress/converted/closed) and notes, updates only those two fields
- `destroy()` — soft deletes the lead, returns AJAX JSON response

---

### 18. Contact Leads Views Created (2 files)
**Files created:**
- `resources/views/admin/contact_leads/index.blade.php`
- `resources/views/admin/contact_leads/show.blade.php`

**index.blade.php:**
- Status summary badge row showing count per status at the top
- DataTables paginated table with columns: ID, Name/Company, Email, Phone, Subject, Status (color-coded badge), Received, Actions
- View button links to show page, Delete via AJAX with SweetAlert confirmation
- Color-coded status badges: New=blue, Contacted=info, In Progress=yellow, Converted=green, Closed=dark

**show.blade.php:**
- Left panel: full contact info (name, company, email as mailto link, phone as tel link, source, received date) + message section (subject + full message)
- Right sidebar: status dropdown + internal notes textarea with Save Changes button (AJAX PUT via X-HTTP-Method-Override)
- Delete button at bottom of sidebar redirects back to index after deletion

---

### 19. Contact Leads Seed Data
7 sample leads seeded via SQL covering all 5 statuses:
- 3 New leads (no notes)
- 1 Contacted (with follow-up note)
- 1 In Progress (with action note)
- 1 Converted (deal closed note)
- 1 Closed (not moving forward note)

---

## [2026-09-28] — Contact Leads Bug Fixes

### 20. Fixed Contact Leads Bug — Route Model Binding Mismatch
**File changed:** `app/Http/Controllers/Admin/ContactLeadController.php`

**Problem:**
Laravel auto-generates the URL parameter name from the resource name. Since the resource is `contact-leads` (hyphenated), Laravel converts it to `contact_lead` (snake_case) as the route parameter. The controller methods were using `$contactLead` (camelCase) which didn't match the route parameter `{contact_lead}`, causing Laravel's automatic model injection to fail.

**Fix:**
Renamed parameter variable from `$contactLead` to `$contact_lead` in `show()`, `update()`, and `destroy()` methods to match the route parameter. The view still receives the variable as `$contactLead` for consistency.

---

### 21. Fixed Contact Leads Bug — PUT Request Method Override
**File changed:** `resources/views/admin/contact_leads/show.blade.php`

**Problem:**
The update form was sending `_method: 'PUT'` inside a JSON body. Laravel can only read the `_method` override from form-encoded data, not JSON. So the PUT was never being recognized.

**Fix:**
Changed the fetch request to use `FormData` instead of `JSON.stringify()`, appending `_method=PUT` as a form field which Laravel correctly reads.

---

## [2026-09-28] — Resources Bug Fix

### 22. Fixed Resources create.blade.php — Missing CSRF Token in Fetch Header
**File changed:** `resources/views/admin/resources/create.blade.php`

**Problem:**
The AJAX fetch call in `create.blade.php` was missing the `X-CSRF-TOKEN` header. Laravel requires this header for all non-GET requests made via AJAX. Without it, Laravel returns a 419 (Page Expired) error and the form submission fails silently.

**Fix:**
Added `'X-CSRF-TOKEN': '{{ csrf_token() }}'` to the fetch headers. The `edit.blade.php` already had this header correctly set.

---

## [2026-09-28] — Site Settings Page

### 23. Setting Model Fixed
**File changed:** `app/Models/Setting.php`

The model was an empty stub. Added:
- `$fillable` — key, value, type, group
- `get(key, default)` — static helper to fetch a single setting value by key
- `set(key, value, group)` — static helper to upsert a setting
- `getGroup(group)` — static helper to get all settings for a group as key => value array

---

### 24. SettingController Created
**File created:** `app/Http/Controllers/Admin/SettingController.php`

Three methods implemented:
- `index()` — loads all header and footer settings using `getGroup()` and passes to view
- `saveHeader()` — validates and saves `header_logo` (max 500 chars), returns AJAX JSON
- `saveFooter()` — validates and saves 9 footer fields with full backend rules:
  - `footer_email` — email format, max 255
  - `footer_phone` — max 20 characters
  - `footer_description` — max 500 characters
  - `footer_facebook/instagram/twitter/youtube/linkedin` — URL format, max 500
  - Custom error messages for all fields

---

### 25. Settings Routes Registered
**File changed:** `routes/web.php`

Three routes added inside admin auth group:
- `GET  /admin/settings` → `SettingController@index` (admin.settings.index)
- `POST /admin/settings/header` → `SettingController@saveHeader` (admin.settings.header)
- `POST /admin/settings/footer` → `SettingController@saveFooter` (admin.settings.footer)

---

### 26. Settings Seed Data
10 default setting keys seeded via SQL:
- Header: `header_logo`
- Footer: `footer_logo`, `footer_email`, `footer_phone`, `footer_description`, `footer_facebook`, `footer_instagram`, `footer_twitter`, `footer_youtube`, `footer_linkedin`

Default values provided for email, phone, and description. All other fields seeded as NULL for admin to fill in.

---

### 27. Site Settings View Created
**File created:** `resources/views/admin/settings/index.blade.php`

Single page with two sections:

**Header Settings panel:**
- Header logo input with Browse button (opens media library)
- Live image preview when logo is selected
- Save Header Settings button with spinner

**Footer Settings panel:**
- Footer logo input with Browse button and live preview
- Short description textarea with real-time character counter (max 500)
- Email field with envelope icon
- Phone field with telephone icon
- 5 social media link fields — Facebook (blue), Instagram (pink), Twitter/X (black), YouTube (red), LinkedIn (blue) — each with brand-colored icon prefix
- Save Footer Settings button with spinner

**Frontend validation (JavaScript):**
- Email format check using regex
- URL format check using `new URL()` for all social links
- Max length checks for phone (20) and description (500)
- Inline error display under each invalid field
- Errors cleared on each new submit attempt

**Backend validation (Laravel):**
- All rules mirrored in `SettingController`
- Backend errors returned as JSON and displayed inline if frontend validation is bypassed

**Media Library:**
- Integrated for both header and footer logo fields
- Selected image auto-fills the input and updates the preview

---

## [2026-09-28] — Site Settings Bug Fixes

### 28. Fixed Settings View — Two Bugs
**File changed:** `resources/views/admin/settings/index.blade.php`

**Bug 1 — Backend validation errors not showing inline:**
Fetch `.then()` only runs on 2xx responses. Laravel returns 422 on validation failure which was going to `.catch()` showing a generic error. Fixed by chaining `.then(res => res.json().then(data => ({ ok: res.ok, data })))` to check `res.ok` before deciding whether to show success or inline errors. Applied to both header and footer forms.

**Bug 2 — header_logo error div not visible:**
The `invalid-feedback` div for `header_logo` was missing `d-block` class. Bootstrap only shows `invalid-feedback` automatically when inside a standard form-group structure, not inside an `input-group`. All other error divs had `d-block` — only `header_logo` was missing it. Fixed by adding `d-block`.

---

## [2026-09-28] — Resources File Upload Improvement

### 29. Replaced URL Input with Actual File Upload in Resources
**Files changed:**
- `resources/views/admin/resources/create.blade.php`
- `resources/views/admin/resources/edit.blade.php`
- `app/Http/Controllers/Admin/ResourceController.php`

**Change:**
Replaced the "File URL / Path" text input with a proper `<input type="file">` upload field in both create and edit views.

- Accepted formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, MP4, MP3
- Max file size: 10MB (enforced both in HTML accept attribute and backend validation)
- File type dropdown auto-detects from uploaded file extension via JavaScript
- Edit view shows the current file with a clickable link — leaving the upload empty keeps the existing file
- Files saved to `storage/app/public/resources/` with unique filename prefix
- Accessible at `/storage/resources/filename.ext` via the storage symlink

---

## [2026-09-28] — Resources File Upload Bug Fixes

### 30. Fixed 3 Bugs in Resources File Upload
**Files changed:**
- `resources/views/admin/resources/create.blade.php`
- `resources/views/admin/resources/edit.blade.php`
- `app/Http/Controllers/Admin/ResourceController.php`

**Bug 1 — Missing enctype on form tags (Frontend):**
File uploads require `enctype="multipart/form-data"` on the form tag. Without it some browsers don't send file data correctly. Added to both create and edit form tags.

**Bug 2 — Storage directory not created before upload (Backend):**
`storeAs()` fails silently if the target directory doesn't exist. Added directory existence check and creation (`Storage::disk('public')->makeDirectory('resources')`) in both `store()` and `update()` methods, matching the same pattern used in MediaController.

**Bug 3 — Storage facade not imported (Backend):**
Was using `\Storage::` with full namespace. Added proper `use Illuminate\Support\Facades\Storage` import at the top of ResourceController.

---
