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
