# VD Membership — Copilot Instructions

## Project overview
WordPress plugin (PHP 8.1+) that manages members for a Hungarian tourist association. The core feature is CRUD (with soft-delete) for members stored in a legacy external MySQL database. The plugin connects to that database using custom credentials configured by an administrator.

## Architecture

Layered OOP, namespace `VDMembership\`, PSR-4 autoloaded from the plugin root via a custom autoloader in `vd-membership.php`. Composer is **not** required at runtime — it is used only for testing (PHPUnit 10).

```
vd-membership.php          Bootstrap: registers autoloader, calls Application::bootstrap()
│
├── Application/           Application layer — orchestrates hooks and services
│   ├── Application.php    Central entry point; registers WP hooks; tests DB on admin_init
│   └── MemberService.php  CRUD application logic
│
├── Configuration/         Plugin settings via WP Options API
│   └── SettingsRepository.php
│
├── Infrastructure/        External database access
│   └── Database/
│       ├── ExternalDatabaseConnection.php   Custom wpdb instance; latin2 charset
│       └── MysqlMemberRepository.php        SQL for `ugyfel` table
│
├── Domain/                Pure PHP domain objects; no WP dependency
│   ├── Member.php
│   ├── MemberRepositoryInterface.php
│   └── MemberValidator.php
│
├── Admin/                 WordPress screens + form handling
│   ├── AdminMenu.php      ✅ Menu registration; defines all slug constants
│   ├── MembersPage.php    (not yet implemented)
│   ├── EditMemberPage.php (not yet implemented)
│   ├── NewMemberPage.php  (not yet implemented)
│   └── SettingsPage.php   ✅ Credentials form + Test Connection handler
│
├── assets/css/admin.css   (not yet created)
├── assets/js/admin.js     (not yet created)
│
└── templates/admin/       PHP view templates
    ├── members.php         (not yet created)
    ├── member-form.php     (not yet created) shared by new and edit pages
    └── settings.php        ✅
```

## Implementation status
Steps 1–9.1 complete (see `plan/vd-membership-plan.md`):
- ✅ Bootstrap & PSR-4 autoloader
- ✅ `Application` class (WP hooks, DB health check, admin notices, transient notice rendering)
- ✅ `SettingsRepository` (WP Options API, stores DB credentials)
- ✅ `ExternalDatabaseConnection` (custom `wpdb`, `latin2` charset)
- ✅ `Member` domain object + `MemberRepositoryInterface`
- ✅ `MysqlMemberRepository`
- ✅ `MemberValidator`
- ✅ `MemberService` (CRUD orchestration, catches DB errors, stores transient notices)
- ✅ `AdminMenu` (slug constants, menu registration)
- ✅ `SettingsPage` + `templates/admin/settings.php`
- ⬜ `MembersPage` + `templates/admin/members.php`
- ⬜ `EditMemberPage` + `NewMemberPage` + `templates/admin/member-form.php`
- ⬜ Assets (`assets/css/admin.css`, `assets/js/admin.js`)

## Key design decisions

### No dependency injection
Classes call each other directly via static methods or `new`. Do not introduce constructor/setter injection.

### Static-method pattern
All service/repository classes use `public static` methods. No class instantiation is required by callers.

### External database connection
`ExternalDatabaseConnection::get()` pre-checks the connection with `mysqli_connect()` (catching `\mysqli_sql_exception` for PHP 8.1+) before creating the `wpdb` instance — this prevents `wpdb` from calling `wp_die()` on failure. The connection is cached in a static property after first successful connect. Call `ExternalDatabaseConnection::reset()` when credentials change.

### Settings storage
All plugin settings are stored as one serialised array under the WP option key `vd_membership_settings` (host, name, user, password). Password is **never** overwritten with an empty string on save — the existing value is preserved. `has_credentials()` requires host + name + user to be non-empty; password is optional (some MySQL setups allow passwordless users).

### Admin notices
`Application` uses three static flags (`$db_credentials_given`, `$db_error`, `$db_connected`) set during `admin_init` and rendered during `admin_notices`. For POST-operation results, use **transients** (so notices survive the PRG redirect). Render transient notices inside `Application::display_notices()`.

Transient key: `MemberService::TRANSIENT_KEY` (`'vd_membership_notices'`). Value: `array<array{type: string, message: string}>`. TTL: 60 s. `MemberService` and `SettingsPage` both append to this transient via the same pattern; `Application::display_notices()` reads, deletes, and renders it.

### POST → Redirect → GET
All form submissions must redirect after processing to prevent re-submission on refresh.

### Soft delete
Never `DELETE` a row. Set `statusz = 0` to deactivate a member. `statusz = 1` means active.

### `ugyfel` primary key
The `ugyfel` column is the primary key with no auto-increment. New records must use `MAX(ugyfel) + 1`.

## External database schema (`ugyfel` table)
Encoding: `latin2` / `latin2_hungarian_ci`. The column names are Hungarian — keep them as-is as PHP property names.

| Column | Type | Notes |
|---|---|---|
| `ugyfel` | int | PK, manual max+1 |
| `ugyfel_nev` | varchar(50) | Required — only field besides PK |
| `lenykori` | varchar(50) | |
| `dat_szul` | date | |
| `szulhely` | varchar(50) | |
| `anya` | varchar(50) | |
| `cim_irsz` | varchar(6) | |
| `cim_varos` | varchar(25) | |
| `cim_cim` | varchar(50) | |
| `telefon` | varchar(25) | |
| `mobil` | varchar(25) | |
| `emil` | varchar(50) | |
| `dat_belep` | date | |
| `figyelmeztet` | char(1) | Read-only in UI; default `'N'` on create; preserved on update |
| `figy_dat` | date | |
| `figy_szoveg` | varchar(255) | |
| `dij` | float | `<input type="number">`, no arithmetic |
| `honap` | int | 1–12, nullable |
| `generalva` | date | System-controlled; always NULL on create; never modified |
| `esedekes` | char(6) | System-controlled; always NULL on create; never modified |
| `megjegyzes` | varchar(1024) | |
| `statusz` | int | 1 = active member, 0 = inactive (soft-delete) |

## Security rules (apply everywhere)
- Gate every operation (menu registration, page render, form processing) with `current_user_can('manage_options')`.
- Every POST form must include a WordPress nonce (verify with `check_admin_referer()`).
- Sanitise all input: `sanitize_text_field()`, `sanitize_email()`, `intval()`, etc.
- Escape all output: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`.
- Never display the DB password — always use `<input type="password">`.
- Display full PHP/DB errors in admin notices (audience is always `manage_options` admins).

## Testing conventions
- PHPUnit 10, run with: `php vendor/phpunit/phpunit/phpunit`
- `tests/bootstrap.php` loads `vendor/autoload.php` then `tests/mocks/wordpress-stubs.php`.
- `tests/mocks/wordpress-stubs.php` uses **bracketed namespaces**: global WP stubs in `namespace {}`, and `mysqli_connect` / `mysqli_close` / `mysqli_connect_error` overrides in `namespace VDMembership\Infrastructure\Database {}` (so `ExternalDatabaseConnection` never touches a real DB).
- Test globals:
  - `$GLOBALS['_vd_test_options']` — simulates WP options (used by `get_option` / `update_option`)
  - `$GLOBALS['_vd_test_mysqli_result']` — truthy = mysqli connection succeeds
  - `$GLOBALS['_vd_test_mysqli_error']` — error string returned by `mysqli_connect_error()`
  - `$GLOBALS['_vd_test_can_manage_options']` — return value of `current_user_can()`
  - `$GLOBALS['_vd_test_wpdb_dbh']` — value assigned to `wpdb::$dbh` on construction
  - `$GLOBALS['_vd_test_wpdb_last_error']` — string read by every `wpdb` stub method into `$this->last_error`
  - `$GLOBALS['_vd_test_wpdb_get_results']` — array returned by `wpdb::get_results()`
  - `$GLOBALS['_vd_test_wpdb_get_row']` — value returned by `wpdb::get_row()`
  - `$GLOBALS['_vd_test_wpdb_insert_result']` — int|false returned by `wpdb::insert()`
  - `$GLOBALS['_vd_test_wpdb_update_result']` — int|false returned by `wpdb::update()`
  - `$GLOBALS['_vd_test_transients']` — associative array backing `set_transient` / `get_transient` / `delete_transient`
- Static classes that cache state expose a `reset()` method for test isolation (`ExternalDatabaseConnection::reset()`, `Application::reset()`). Always call both in `setUp()` in any test that exercises Application or ExternalDatabaseConnection.
- Capture `display_notices()` output with `ob_start()` / `ob_get_clean()`.
- Admin UI classes (`AdminMenu`, `SettingsPage`, etc.) are not unit-tested — deferred to step 11 polish.

## Constraints & conventions
- No REST API, no custom endpoints.
- No JS bundlers (npm/webpack/esbuild). Enqueue external JS from CDN when needed.
- No pagination needed (member list stays under ~200 rows).
- No concurrent-admin safeguards needed.
- Use WordPress native admin UI styles — no custom CSS frameworks.
- Keep JS minimal; all CRUD handled server-side in PHP.
- Use `type="date"` for all date inputs.
- The `inc/` folder contains legacy helper functions — do not modify or remove those files.
