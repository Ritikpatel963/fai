# Changelog

All changes made to this project are documented here in reverse chronological order.

---

## 📦 Modules Created

A quick reference of all modules built from scratch during this project.

---

### Module 1 — Resources
**Status:** ✅ Complete  
**URL:** `/admin/resources`

| Item | Details |
|---|---|
| Migration | `create_resources_table` — title, slug, description, file, file_type, status, featured_image, SEO fields, softDeletes |
| Model | `app/Models/Resource.php` — SoftDeletes, fillable, casts |
| Controller | `app/Http/Controllers/Admin/ResourceController.php` — full CRUD |
| Routes | `Route::resource('resources', ...)` — 7 routes |
| Views | `resources/views/admin/resources/` — index, create, edit |
| Features | File upload (PDF/DOC/DOCX/XLS/XLSX/PPT/PPTX/ZIP/MP4/MP3, max 10MB), featured image via media library, auto-generated slug, Add via popup modal on index page |

---

### Module 2 — Contact Leads
**Status:** ✅ Complete  
**URL:** `/admin/contact-leads`

| Item | Details |
|---|---|
| Migration | `create_contact_leads_table` — name, email, phone, company, subject, message, source, status, notes, softDeletes |
| Model | `app/Models/ContactLead.php` — SoftDeletes, fillable |
| Controller | `app/Http/Controllers/Admin/ContactLeadController.php` — index, show, update, destroy |
| Routes | `Route::resource('contact-leads')->only([index, show, update, destroy])` — 4 routes |
| Views | `resources/views/admin/contact_leads/` — index, show |
| Features | Read-only inbox (leads come from frontend), status counts badge row (new/contacted/in_progress/converted/closed), status + notes update on show page, soft delete |

---

### Module 3 — Site Settings
**Status:** ✅ Complete  
**URL:** `/admin/settings`

| Item | Details |
|---|---|
| Migration | `create_settings_table` — key-value store (key, value, type, group) |
| Model | `app/Models/Setting.php` — fillable, get/set/getGroup helpers |
| Controller | `app/Http/Controllers/Admin/SettingController.php` — index, saveHeader, saveFooter |
| Routes | `GET /admin/settings`, `POST /admin/settings/header`, `POST /admin/settings/footer` |
| Views | `resources/views/admin/settings/index.blade.php` |
| Features | Header logo with media library picker and preview, footer logo + short description (char counter) + email + phone + 5 social links (Facebook/Instagram/Twitter/YouTube/LinkedIn), frontend + backend validation, AJAX save with inline errors and spinner |

---

### Module 4 — SEO Settings
**Status:** ✅ Complete  
**URL:** `/admin/seo-settings`

| Item | Details |
|---|---|
| Migration | `create_seo_settings_table` — page, route_name (unique), meta_title, meta_description, meta_keywords, og_title, og_description, og_image, canonical_url, robots, schema_markup |
| Model | `app/Models/SeoSetting.php` — fillable, forPage(), savePage() helpers |
| Controller | `app/Http/Controllers/Admin/SeoSettingController.php` — index, save |
| Routes | `GET /admin/seo-settings`, `POST /admin/seo-settings/save` |
| Views | `resources/views/admin/seo_settings/index.blade.php` |
| Pages covered | Home, About Us, Our Projects, Resources, Blogs Listing, Contact Us |
| Features | Bootstrap accordion — one panel per page, each saves independently via AJAX, status badge (Configured/Not set), Basic Meta (title/description/keywords/canonical), Open Graph (og_title/og_description/og_image with media picker), inline errors, toast notifications |
| Removed fields | Robots meta tag, Schema Markup (removed per instruction) |

---

### Module 5 — Testimonials
**Status:** ✅ Complete  
**URL:** `/admin/testimonials`

| Item | Details |
|---|---|
| Migration | `create_testimonials_table` — name, rating (1–5), message, image, order, status, softDeletes |
| Model | `app/Models/Testimonial.php` — SoftDeletes, fillable, casts |
| Controller | `app/Http/Controllers/Admin/TestimonialController.php` — index, store, show, update, destroy |
| Routes | `Route::resource('testimonials')->except([create, edit])` — 5 routes |
| Views | `resources/views/admin/testimonials/index.blade.php` |
| Features | DataTables with pagination/search/sorting, Add + Edit via popup modal (no page reload), interactive star rating (1–5) with hover effect, circular photo picker from media library, char counter on message, display order input, inline error display, soft delete |

---

## 📋 Existing Modules Fixed / Improved

### Blogs
- Fixed SEO field name mismatch (`meta_title`/`meta_description`)
- Fixed status badge always showing "Published"
- Fixed N+1 query on blog list
- Fixed `published_at` not updating on status change
- Added Meta Keywords field to SEO panel
- Replaced Quill editor with TinyMCE 6
- Removed Visibility dropdown

### Profile
- Added show/hide password toggle on Change Password form

### Auth
- Forgot password shows reset link directly on page (no email service needed)

### Media Library
- Improved search bar UI (labels, clear button, live search on keystroke)
- Added search inside media modal across all pages that use it (blogs, resources, settings, testimonials, SEO)

### Header/Navigation
- Added Profile link to user dropdown
- "Signed in as Admin" now shows actual logged-in user name

---



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

## [2026-10-01] — Blog Editor Upgrade

### 31. Replaced Quill with TinyMCE on Blog Create and Edit Pages
**Files changed:**
- `resources/views/admin/blogs/create.blade.php`
- `resources/views/admin/blogs/edit.blade.php`

**Change:**
Replaced the Quill.js rich text editor with TinyMCE 6 (CDN version) on both blog create and edit pages.

**Why TinyMCE is better here:**
- Full word-processor style toolbar (tables, media embed, find/replace, word count, preview, fullscreen)
- Uses a native `<textarea>` — no hidden input needed, no manual content sync on submit
- `tinymce.triggerSave()` on form submit syncs content automatically
- Built-in image picker integration via `file_picker_callback` — opens our custom media library modal
- Existing blog content pre-populates correctly via Blade `{!! $blog->content !!}`

**What changed structurally:**
- Removed `<div id="blogEditor">` and `<input type="hidden" name="content">` — replaced with `<textarea name="content" id="blogEditor">`
- Removed "Add Media" button above editor — TinyMCE has its own Image button in toolbar
- Removed Quill CSS/JS CDN links
- Added TinyMCE CDN script
- Form submit now calls `tinymce.triggerSave()` instead of manually syncing Quill HTML

---

## [2026-10-01] — Resources Simplified

### 32. Simplified Resources Create & Edit Forms
**Files changed:**
- `resources/views/admin/resources/create.blade.php`
- `resources/views/admin/resources/edit.blade.php`
- `app/Http/Controllers/Admin/ResourceController.php`

**Change:**
Stripped the resource forms down to just 3 fields:
- **Title** — required
- **File** — upload input (PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, MP4, MP3, max 10MB)
- **Image** — single image via media library with preview and remove button

Removed: slug input (now auto-generated from title), short description, full description (Quill editor), file type dropdown (now auto-detected from extension), SEO panel, status dropdown (always active on create).

Controller changes:
- `store()` — validation simplified to title + file_upload only. Slug auto-generated using `Str::slug()` with duplicate handling. File type auto-detected from extension.
- `update()` — same simplification. Slug regenerated from title on update.

---

## [2026-10-01] — Session Bug Fixes

### 33. Fixed Blog Create & Edit — TinyMCE Scripts in Wrong Section
**Files changed:**
- `resources/views/admin/blogs/create.blade.php`
- `resources/views/admin/blogs/edit.blade.php`

TinyMCE CDN script and all JS were inside `@section('content')` instead of `@push('scripts')` / `@endpush`. Moved correctly so scripts render at the bottom of the page as the layout expects.

---

### 34. Fixed ResourceController update() — Duplicate Slug on Title Change
**File changed:** `app/Http/Controllers/Admin/ResourceController.php`

`update()` was regenerating the slug with `Str::slug($request->title)` without excluding the current resource from the uniqueness check. If another resource had the same slug, the DB unique constraint would throw an error. Fixed by adding the same duplicate-handling loop used in `store()`, but excluding the current resource ID with `where('id', '!=', $resource->id)`.

---

## [2026-10-01] — Media Library Modal Search

### 35. Added Image Search to Media Library Modal
**Files changed:**
- `resources/views/admin/blogs/create.blade.php`
- `resources/views/admin/blogs/edit.blade.php`
- `resources/views/admin/resources/create.blade.php`
- `resources/views/admin/resources/edit.blade.php`
- `resources/views/admin/settings/index.blade.php`

**Change:**
Added a search input box inside the Media Library tab of the modal in all 5 files that use it. Typing in the search box triggers `loadMedia(1)` on every keystroke (`oninput`), passing the search term as a `?search=` query parameter to `admin.media.index`. The `MediaController@index` already supported search filtering on `title`, `original_filename`, and `keywords` — no backend changes needed.

---

## [2026-10-01] — Resources Add via Popup Modal

### 36. Add Resource via Popup Modal (No Page Reload)
**File changed:** `resources/views/admin/resources/index.blade.php`

**Change:**
The "Add Resource" button no longer navigates to a separate page. It now opens a Bootstrap modal popup directly on the Resources list page.

**What's in the modal:**
- Title input (required)
- File upload input (PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, MP4, MP3 — max 10MB)
- Image selector with preview (opens nested Media Library modal)
- Cancel and Save buttons

**How it works:**
- Form submits via AJAX `fetch()` — no page reload during save
- On success shows SweetAlert confirmation then reloads the table
- Media Library modal has a Back button to return to the resource modal after selecting an image
- Search functionality included in the media library tab
- Image upload inside media library modal uses XHR with progress bar

**No backend changes needed** — the existing `ResourceController@store` already handles AJAX JSON responses correctly.

---

## [2026-10-01] — Loose Ends Fixed

### 37. Fixed Loose Ends Across Session Files

**`resources/views/admin/resources/index.blade.php`**
- Fixed duplicate Bootstrap Modal instances — changed all `new bootstrap.Modal(el).show()` calls to `bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)` pattern. This prevents Bootstrap 5 from creating multiple instances on the same element which causes warnings and improper close behavior. Applied to `openAddResourceModal()`, `openModalImagePicker()`, Back button, and Insert button handlers.

**`resources/views/admin/media/index.blade.php`**
- Fixed `editModal` being initialized outside `DOMContentLoaded`. `new bootstrap.Modal(document.getElementById('editMediaModal'))` was running at script parse time before the DOM element existed, which could cause a null reference error on slower connections. Moved inside a second `DOMContentLoaded` listener.

**`resources/views/admin/blogs/create.blade.php` and `edit.blade.php`**
- Confirmed TinyMCE CDN script placement is correct — outside `@section('content')`, before `@push('scripts')`. No changes needed.

---

## [2026-10-01] — SEO for Pages Module

### 38. SeoSetting Model Fixed
**File changed:** `app/Models/SeoSetting.php`

The model was an empty stub. Added:
- `$fillable` — all 11 columns: page, route_name, meta_title, meta_description, meta_keywords, og_title, og_description, og_image, canonical_url, robots, schema_markup
- `forPage(routeName)` — static helper to fetch a single page's SEO record by route_name
- `savePage(routeName, page, data)` — static helper to upsert a page's SEO record

---

### 39. SeoSettingController Created
**File created:** `app/Http/Controllers/Admin/SeoSettingController.php`

Two methods:
- `index()` — loads all 6 page SEO records from DB keyed by route_name, passes `$pages` array and `$settings` collection to view
- `save(Request $request)` — validates all fields (meta_title max 255, meta_description max 500, canonical_url URL format, robots enum), upserts record via `SeoSetting::savePage()`, returns AJAX JSON response

Pages managed: Home, About Us, Our Projects, Resources, Blogs Listing, Contact Us — stored as `route_name` values: home, about-us, our-projects, resources, blogs-listing, contact-us.

---

### 40. SEO Settings Routes Registered
**File changed:** `routes/web.php`

Two routes added inside admin auth group:
- `GET  /admin/seo-settings` → `SeoSettingController@index` (admin.seo-settings.index)
- `POST /admin/seo-settings/save` → `SeoSettingController@save` (admin.seo-settings.save)

Route name `admin.seo-settings.index` matches the existing sidebar entry exactly — the sidebar link was already pointing to this route and was rendering as `href="#"` before. Now it resolves correctly.

---

### 41. SEO Settings View Created
**File created:** `resources/views/admin/seo_settings/index.blade.php`

Single page with Bootstrap accordion — 6 collapsible panels, one per page. First panel (Home) is open by default.

Each panel contains:
- **Status badge** — shows "Configured" (green) if meta_title is set, "Not set" (grey) if empty. Updates live after saving.
- **Basic Meta section** — Meta Title (with char count), Meta Description, Meta Keywords, Canonical URL, Robots dropdown (index/follow, noindex/nofollow options)
- **Open Graph section** — OG Title, OG Description, OG Image (with Browse button opening integrated media library modal with search and upload)
- **Schema Markup section** — monospace JSON-LD textarea
- **Save button** — each panel saves independently via AJAX `fetch()`. Shows spinner during save. Displays inline field errors from backend 422 responses. Shows toast notification on success.

Dedicated media library modal (`#seoMediaModal`) — separate from other page modals to avoid ID conflicts. Includes search, upload with progress bar, and grid selection.

---

### 42. SEO Settings Seed Data
6 default rows seeded into `seo_settings` table via SQL (one per page):
- home, about-us, our-projects, resources, blogs-listing, contact-us
- Each has sensible default meta_title, meta_description, meta_keywords
- All robots set to `index, follow`
- OG fields and schema_markup left NULL for admin to fill via UI

---

## [2026-10-01] — Testimonials Module

### 43. Testimonials Migration Created
**File created:** `database/migrations/2026_10_01_000001_create_testimonials_table.php`

Table: `testimonials`

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| name | string | required |
| rating | unsignedTinyInteger | 1–5, default 5 |
| message | text | required |
| image | string | nullable, from media library |
| order | unsignedInteger | default 0, lower = shown first |
| status | boolean | default true |
| deleted_at | softDeletes | nullable |
| timestamps | | created_at, updated_at |

---

### 44. Testimonial Model Created
**File created:** `app/Models/Testimonial.php`

- `SoftDeletes` + `HasFactory` traits
- `$fillable` — name, rating, message, image, order, status
- `$casts` — rating/order as integer, status as boolean

---

### 45. TestimonialController Created
**File created:** `app/Http/Controllers/Admin/TestimonialController.php`

Five methods:
- `index()` — paginated list (20/page), ordered by `order` then `created_at` desc
- `store()` — validates all fields (name required, rating 1–5, message required, image nullable URL, order integer, status in:0,1), creates record, returns AJAX JSON
- `show()` — returns single testimonial as JSON for populating edit modal
- `update()` — same validation as store, updates record, returns AJAX JSON
- `destroy()` — soft deletes record, returns AJAX JSON

---

### 46. Testimonial Route Registered + Sidebar Link Added
**Files changed:** `routes/web.php`, `resources/views/admin/layouts/partials/sidebar.blade.php`

- `Route::resource('testimonials', TestimonialController::class)->except(['create', 'edit'])` — 5 routes registered (index, store, show, update, destroy)
- Sidebar entry added with `bi-chat-quote` icon between Site Settings and SEO Settings

---

### 47. Testimonials View Created
**File created:** `resources/views/admin/testimonials/index.blade.php`

**Table columns:** Order, Name, Star Rating (visual), Message preview (60 chars), Circular image thumbnail, Status badge, Edit/Delete actions

**Add/Edit Modal (single form, handles both create and update):**
- Circular photo picker (90×90px) with person placeholder icon and Remove button
- Interactive star rating (1–5) with hover highlight effect and label (Poor/Fair/Good/Very Good/Excellent)
- Message textarea with live character counter (max 2000)
- Display Order input (lower = shown first)
- Status dropdown (Active/Inactive)
- Spinner on save button, inline error display from backend 422 responses
- `_method=PUT` appended via FormData for updates

**Media Library Modal (separate, `tm` prefix to avoid ID conflicts):**
- Upload tab with drag-and-drop and progress bar
- Library tab with search and grid selection
- Back button returns to testimonial modal
- Use Selected Photo inserts image and returns to testimonial modal

### 48. Migration Executed
`php artisan migrate` — `create_testimonials_table` ran successfully (131ms).

---
