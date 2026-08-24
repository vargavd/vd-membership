# VD Membership Plugin

## Overview

This is a wordpress plugin which manages members for a hungarian tourist association. It requires PHP 8.1 version and uses object oriented PHP. The main feature is a CRUD (with soft delete) function for members.

## Structure
```
VD Membership Plugin
    │
    ├── Plugin main (vd-membership.php)
    │
    ├── Admin UI
    │   ├── Members
    │   ├── Edit Member
    │   └── New Member
    |   └── Settings
    │
    ├── Application / Services
    │   ├── Application
    │   ├── MemberService
    │
    ├── Domain
    │   ├── Member
    │   └── MemberRepositoryInterface
    │   └── MemberValidator
    │
    ├── Infrastructure
    │   ├── ExternalDatabaseConnection
    │   └── MysqlMemberRepository
    │
    ├── WordPress Integration
    │   ├── AdminMenu
    │   └── Assets
    │
    └── Configuration
        └── SettingsRepository
```

## Short description of all the layers and classes/files

`Plugin main (vd-membership.php)` - the main php file for the plugin. It bootstraps the Application.

### Admin UI
Deals with WordPress screens and HTTP/form requests.

- `MembersPage`: Displays the **Members** admin page. Loads members and renders the member list/table.
- `EditMemberPage`: Handles the **Edit Member** page. Loads an existing member, processes the update form, and redirects after saving.
- `NewMemberPage`: Handles the **New Member** page. Displays an empty member form and processes member creation.
- `SettingsPage`: An edit form for all the plugin settings (for example, credentials for the external mysql database). Uses the SettingsRepository class. This page should also include a "Test Database Connection" button (as a POST request with *nonce* and `manage_options` check)


### Application / Services
Decides what needs to happen.

- `Application`: Manages class initializations and plugin activation setups, plus deactivation cleanup (if any). Also, please tests the external db connection on every `admin_init` if the credential options are already filled. It also handles notices with two ways: once it displays some notices nased in the connection availability in the `admin_init` hook, second it also handles some messages coming with transients and converts them to notices (in the `admin_notices` hook)
- `MemberService`: Contains the main member-related application logic. Coordinates validation, domain objects, and repository operations for CRUD. It also catches DB errors coming from the repository and creates notices via transients ( -> they are handled by Application)

### Domain
Represents what a member is.

- `Member`: Represents a member as a PHP domain object. Contains the member's properties and basic domain behavior, independent of WordPress/database details.
- `MemberRepositoryInterface`: Defines how the application can retrieve and persist members without knowing how/where they are stored.           
- `MemberValidator`: Validates member input before it reaches the database: required fields, dates, lengths, email, numeric values, etc.

### Infrastructure
Manages the external MySQL connection.

- `ExternalDatabaseConnection`: Creates and manages the connection to the external `termeszetvedok_tura` database.
- `MysqlMemberRepository`: Implements `MemberRepositoryInterface` and contains all SQL/database-specific logic for the `ugyfel` table.

### WordPress Integration
Connects the application to WordPress

- `AdminMenu`: Registers the **VD Membership**, **Members**, **New Member** and **Settings** pages in the WordPress admin. It also has the slugs as a constant.
- `Assets`: Registers and loads the plugin's admin CSS and JavaScript only on the relevant VD Membership pages.

### Configuration
Provides plugin/database settings.

- `SettingsRepository`: Reads and writes any plugin configuration to/from the WordPress database, via the Options API. 

## Directory/File Structure

```
vd-membership/
│
├── vd-membership.php
├── README.md
├── LICENSE
│
├── assets/
│   ├── css/
│   │   └── admin.css
│   └── js/
│       └── admin.js
├── Admin/
|   ├── AdminMenu.php
|   ├── MembersPage.php
|   ├── EditMemberPage.php
|   ├── NewMemberPage.php
|   └── SettingsPage.php
|
│── Domain/
|   ├── Member.php
|   ├── MemberRepositoryInterface.php
|   └── MemberValidator.php
|
│── Application/
|   └── Application.php
|   └── MemberService.php
|
│── Infrastructure/
|   └── Database/
|       ├── ExternalDatabaseConnection.php
|       └── MysqlMemberRepository.php
|
│── Configuration/
|   └── SettingsRepository.php
│
├── templates/
│   └── admin/
│       ├── members.php
│       ├── member-form.php
|       ├── settings.php
│
├── tests/
│   ├── mocks/
|   └── Admin/
|       └── AdminMenuTest.php
|   └── Application/
|       ├── ApplicationTest.php
|       └── MemberServiceTest.php
│   └── Domain/
|       ├── MemberTest.php
|       └── MemberValidatorTest.php
|   └── Configuration/
|       └── SettingsRepositoryTest.php
```

## Storage logic
The plugin will use two storage.

### 1. Plugin settings
For the plugin settings, use the WordPress Options API. This will use the default wordpress database. Although the plugin will be used by only trustworthy administrators, do not ever display the password. Use the password input field.

### 2. Members storage
For the members, use a custom connection to an external db. The credentials for this connection should come from the first storage option. The user has to provide these in the plugin's settings dashboard page.
Use a custom `wpdb` instance.
We also know the exact schema of the table we will use. Please map these columns in the Domain layer and use these in the UI layer appropriately.

```
CREATE TABLE `ugyfel` (
  `ugyfel` int NOT NULL DEFAULT '0',
  `ugyfel_nev` varchar(50) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `lenykori` varchar(50) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `dat_szul` date DEFAULT NULL,
  `szulhely` varchar(50) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `anya` varchar(50) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `cim_irsz` varchar(6) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `cim_varos` varchar(25) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `cim_cim` varchar(50) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `telefon` varchar(25) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `mobil` varchar(25) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `emil` varchar(50) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `dat_belep` date DEFAULT NULL,
  `figyelmeztet` char(1) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `figy_dat` date DEFAULT NULL,
  `figy_szoveg` varchar(255) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `dij` float DEFAULT NULL,
  `honap` int DEFAULT NULL,
  `generalva` date DEFAULT NULL,
  `esedekes` char(6) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `megjegyzes` varchar(1024) COLLATE latin2_hungarian_ci DEFAULT NULL,
  `statusz` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_hungarian_ci;
```

*Notes*: 
- the database is a legacy one, which cannot be modified - we have to work with it.
- the column names are in "hungarian", without accents. Use the same names as properties, do not translate them to english.
- `ugyfel` is the actual ID (primary key), but with no auto increment. The application should handle it manually! For a new member, `ugyfel` should be *max + 1*. If the table is empty, then the first `ugyfel` should be 1.
- there should be no required fields besides `ugyfel` and `ugyfel_nev` - I want to keep it simple (also `ugyfel` should be autogenerated and readonly on the form) 
- `statusz` represents if someone is still a member or not: 0 - no member, 1 - still a member
- `figyelmeztet` is not editable in the UI, but the value is displayed readonly. New members are created with figyelmeztet = 'N'; when updating an existing member, its current value is preserved. The validator should ignore this field.
- `honap` should be between 1 - 12, but also nullable (should be cleared on the UI)
- `generalva` is system-controlled and is not editable. It is always NULL for newly created members and is never modified by the plugin.
- `esedekes` is system-controlled and is not editable. It is always NULL for newly created members and is never modified by the plugin.
- `dij` - no arithmetic will be needed, treat it as a number field and no conversion is needed. Use a simple `input type="number"`


## Security considerations
There will be no custom roles or capabilities.
Always use the `current_user_can('manage_options')` check for every operation.
Even the menupoints should depend on `manage_options` capability - so show them only to administrators.
Every POST operation should include a WordPress nonce.
Use the built-in WordPress sanitazion and escaping functions, for example the `sanitize_text_field`, `esc_html` etc..

## Other Notes
- No REST API use or a custom endpoint.
- The project should not use JS package managers and bundlers (like npm, esbuild or webpack). Any external JS library should be enqueued from a CDN if possible. Although I am not strict about this, if there are strong counterpoints.
- Use Composer, but only for testing. Unit tests should be present, when appropriate. Composer shouldn't be required to run the plugin.
- The plugin should use a lightweight PSR-4-compatible custom autoloader. Composer is not required for runtime.
- Do not use dependency injection.
- Please use WordPress native admin styles everywhere.
- Keep custom javascript to a minimum. CRUD should be handled by PHP.
- Do not delete a row, only set the `statusz` field to 0. Display all the members and indicate in the table if one is still a member or not.
- The solution should use native wordpress admin notices for every operation. They should use transients, so the redirect after POST doesn't effect them.
- If there is a php or a database error, display it in a notice fully. This tool will be used by only administrators anyway with (`manage_options` capability), there is no risk displaying anything.
- For POST requests, use the **Post -> Redirect -> Get** pattern to avoid resubmitting the form with via refresh.
- The custom database credentials are stored in the WordPress options table and are only accessible to users who can manage plugin settings.
- The members list will not be bigger then 200 - no pagination is needed.
- For date inputs, use the HTML5 type="date" input field.
- It is extremely unlikely to have two administrators editing at the same time. No need to prepare the plugin for that.
- The **edit member** and **new member** page should use the same form template. This template expects a Member|null parameter, and a mode flag.

## Implementation Order

*Note*: every step includes unit tests where relevant.

```
1. Plugin bootstrap and autoloading (vd-membership.php) ✅
        ↓
2. Application (Application.php) ✅
        ↓
3. Configuration / Settings (SettingsRepository.php) ✅
        ↓
4. ExternalDatabaseConnection (ExternalDatabaseConnection.php) ✅
        ↓
5. Member domain object (Member.php, MemberRepositoryInterface.php) ✅
        ↓
6. MysqlMemberRepository (MysqlMemberRepository.php) ✅
        ↓
7. MemberValidator (MemberValidator.php) ✅
        ↓
8. MemberService (MemberService.php) ✅
        ↓
9. Admin UI (AdminMenu, template files, potential assets)
  - 9.1 Settings admin menupoint ✅
        ↓
  - 9.2 Members admin menupoint and the relevant templates ✅
        ↓
  - 9.3 Edit Member admin menupoint and the relevant templates
        ↓
  - 9.4 New Member admin menupoint
        ↓
10. CRUD operations
        ↓
11. Tests / polish
```
