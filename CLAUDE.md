# CLAUDE.md - Module ICD

This file provides guidance to Claude Code when working with the `hanafalah/module-icd` package.

## Module Overview

Module ICD provides integration with the WHO (World Health Organization) ICD API for importing and managing International Classification of Diseases (ICD) codes. It supports ICD-10 and ICD-9-CM versions with automatic translation capabilities.

**Namespace:** `Hanafalah\ModuleIcd`

## CRITICAL: ServiceProvider Registration Warning

**The current `ModuleIcdServiceProvider` uses `registers(['*'])`:**

```php
public function register()
{
    $this->registerMainClass(ModuleIcd::class)
        ->registerCommandService(Providers\CommandServiceProvider::class)
        ->registers(['*']);  // POTENTIALLY DANGEROUS
}
```

### Why This Matters

The `registers(['*'])` pattern can cause **memory exhaustion** when:
1. It auto-loads Schema classes that extend `PackageManagement`
2. `PackageManagement` uses `HasModelConfiguration` trait
3. This triggers recursive `config()` calls during class loading
4. Memory grows rapidly until PHP crashes (536MB limit)

See `/var/www/projects/wellmed/repositories/laravel-support/CLAUDE.md` for detailed explanation.

### Safe Pattern (Recommended)

```php
public function register()
{
    $this->registerMainClass(ModuleIcd::class)
        ->registerCommandService(Providers\CommandServiceProvider::class);
    // Don't use registers(['*']) - the safe methods are already registered
    // Schemas will be bound via contracts when needed
}
```

## Dependencies

```json
{
    "require": {
        "hanafalah/laravel-support": "dev-main",
        "hanafalah/module-disease": "dev-main",
        "stichoza/google-translate-php": "^5.2"
    }
}
```

- **laravel-support** - Base package with `BaseServiceProvider`, `PackageManagement`
- **module-disease** - Provides `Disease` model that ICD models extend
- **google-translate-php** - Translates disease names (EN to ID by default)

## Architecture Overview

```
module-icd/
├── assets/
│   └── config/
│       └── config.php           # Module configuration
├── src/
│   ├── Commands/                # Artisan commands
│   │   ├── EnvironmentCommand.php   # Base command class
│   │   ├── InstallMakeCommand.php   # module-icd:install
│   │   ├── ScrappingMakeCommand.php # module-icd:scrapping
│   │   └── IcdTranslateCommand.php  # module-icd:translate
│   ├── Concerns/Base/           # Traits for API integration
│   │   ├── Authentication.php   # OAuth2 with WHO API
│   │   ├── HasConfig.php        # Config accessor methods
│   │   ├── HasEndPoint.php      # API endpoint building
│   │   ├── HasEntity.php        # Entity search/lookup
│   │   ├── HasRelease.php       # ICD release data fetching
│   │   └── HasRequest.php       # HTTP client handling
│   ├── Contracts/               # Interfaces
│   │   ├── ModuleIcd.php        # Main module contract
│   │   └── Schemas/
│   │       ├── Icd.php          # Icd schema contract
│   │       └── Icd10.php        # Icd10 schema contract
│   ├── Enums/
│   │   └── ICD/Version.php      # Version enum (Icd10, Icd9CM)
│   ├── Models/
│   │   ├── Icd.php              # Base ICD model (extends Disease)
│   │   └── Icd10.php            # ICD-10 with global scope filter
│   ├── Providers/
│   │   └── CommandServiceProvider.php  # Registers artisan commands
│   ├── Resources/               # API resources
│   │   ├── Icd/ViewIcd.php
│   │   └── Icd10/ViewIcd10.php
│   ├── Schemas/                 # Schema implementations
│   │   ├── Icd.php              # Base ICD schema
│   │   └── Icd10.php            # ICD-10 schema with installIcd10()
│   ├── ModuleIcd.php            # Main module class
│   └── ModuleIcdServiceProvider.php  # Service provider
```

## Key Classes

### ModuleIcd (Main Class)

The core class that handles WHO ICD API integration. Extends `PackageManagement`.

**Key Methods:**

| Method | Purpose | Notes |
|--------|---------|-------|
| `oauth()` | Authenticate with WHO API | Uses client credentials |
| `installIcd()` | Import ICD data recursively | Stores in Disease table |
| `setIcdModel()` | Set the model to use | Icd or Icd10 |
| `setVersion()` | Set ICD version string | e.g., "Icd10_2019" |
| `setYearReleaseId()` | Set release year | e.g., "2019" |
| `setup()` | Initialize config and auth | Must call before API requests |

**State Properties (Octane Warning):**
```php
protected $__icd_model;        // Current model instance
protected $__icd_version;      // Version string
protected $__year_release;     // Release year
protected $__translate;        // GoogleTranslate instance
```

### Icd10 Schema

Extends `ModuleIcd` with ICD-10 specific functionality.

```php
// Example usage
$icd10 = app(Icd10::class);
$icd10->setup()->oauth();
$icd10->installIcd10('2019');  // Import entire ICD-10 2019 release
```

### Models

**Icd** - Base model extending `Disease`:
- Table: `diseases` (shared with module-disease)
- Uses morph class flag for polymorphism

**Icd10** - ICD-10 specific model:
- Adds global scope: `whereLike('version', 'Icd10')`
- Automatically filters to ICD-10 records only

## Traits (Concerns/Base/)

### Authentication
Handles OAuth2 authentication with WHO ICD API.

**Properties:**
- `$__token_end_point` - WHO token endpoint
- `$__client_id`, `$__client_secret` - Credentials from config
- `$__token`, `$__auth` - Current auth state

**Methods:**
- `oauth()` - Get access token
- `getAuthorization()` - Returns "Bearer {token}"
- `setAuthorization()` - Load credentials from config

### HasRequest
HTTP client wrapper for API calls.

**Methods:**
- `http()` - Returns configured `PendingRequest`
- `post()` - POST request (form data)
- `makeRequest()` - GET request with auto-retry on 401
- `setHeader()` - Set auth headers
- `setQueries()` - Set query parameters

### HasRelease
Fetches ICD release data.

```php
$icd->getRelease10('2019');           // Full release
$icd->getRelease10('2019', 'A00');    // Specific code
```

### HasEntity
Entity search and lookup.

```php
$icd->getEntityBySearch('cholera');   // Search diseases
$icd->getEntityAutocode('diarrhea');  // Auto-code text
$icd->getEntityById('http://...');    // Get by URI
```

## Artisan Commands

### module-icd:install
```bash
php artisan module-icd:install
```
Publishes migrations for the ICD module.

### module-icd:scrapping
```bash
php artisan module-icd:scrapping {version} {releaseId} {--code=}
```

**Arguments:**
- `version` - ICD version (currently only `10` supported)
- `releaseId` - Year of release (e.g., "2019", "2020", "2024")

**Options:**
- `--code=` - Comma-separated specific codes to import

**Examples:**
```bash
# Import entire ICD-10 2019 release (takes hours)
php artisan module-icd:scrapping 10 2019

# Import specific codes
php artisan module-icd:scrapping 10 2019 --code=A00,A01,B00
```

### module-icd:translate
```bash
php artisan module-icd:translate {code} {to} {--from=} {--childs}
```

**Arguments:**
- `code` - ICD code to translate
- `to` - Target language code (e.g., "id" for Indonesian)

**Options:**
- `--from=` - Source language (auto-detect if not specified)
- `--childs` - Also translate child codes recursively

**Example:**
```bash
php artisan module-icd:translate A00 id --childs
```

## Configuration

**File:** `assets/config/config.php` (published as `config/module-icd.php`)

### Required Environment Variables

```env
ICD_CLIENT_ID=your_who_client_id
ICD_CLIENT_SECRET=your_who_client_secret
ICD_API_VERSION=v2
```

**Get credentials from:** https://icd.who.int/icdapi

### Configuration Options

```php
return [
    'namespace' => 'Hanafalah\\ModuleIcd',
    'lang' => 'en',                    // Source language
    'api_version' => env('ICD_API_VERSION', 'v2'),
    'authentication' => [
        'client_id' => env('ICD_CLIENT_ID'),
        'client_secret' => env('ICD_CLIENT_SECRET')
    ],
    'translate' => [
        'to' => 'id'                   // Target language (Indonesian)
    ],
    'disease_model' => 'Disease',
    'commands' => [
        // Override commands if needed
    ]
];
```

## WHO ICD API Integration

### Base URL
`https://id.who.int/icd/`

### Authentication Endpoint
`https://icdaccessmanagement.who.int/connect/token`

### Authentication Flow
1. POST to token endpoint with client credentials
2. Receive OAuth2 access token
3. Use `Bearer {token}` header for API requests
4. Auto-retry on 401 (re-authenticate and retry)

### API Endpoints Used
- `release/10/{releaseId}` - Get ICD-10 release root
- `release/10/{releaseId}/{code}` - Get specific code details
- `entity/search` - Search entities
- `entity/autocode` - Auto-code text to ICD
- `entity/{id}` - Get entity by ID

## Data Model

ICD records are stored in the `diseases` table (shared with module-disease):

| Field | Type | Description |
|-------|------|-------------|
| `id` | bigint | Primary key |
| `code` | string | ICD code (e.g., "A00") |
| `name` | string | Disease name (English) |
| `local_name` | string | Translated name (Indonesian) |
| `version` | string | ICD version (e.g., "Icd10_2019") |
| `flag` | string | Model morph class |
| `parent_id` | bigint | Parent disease for hierarchy |
| `lang` | string | Original language code |
| `release_date` | date | ICD release date |
| `inclusions` | json | Array of inclusion terms |
| `exclusions` | json | Array of exclusion terms |
| `coding_hints` | json | Array of coding hints |
| `class_kind` | string | Classification kind |

## Laravel Octane Considerations

**CRITICAL:** This module stores state in instance properties that persist between Octane requests.

### Problematic Properties
```php
// In ModuleIcd class
protected $__icd_model;
protected $__icd_version;
protected $__year_release;
protected $__translate;

// In Authentication trait
protected string $__token;
protected object $__auth;
protected ?string $__client_id;
protected ?string $__client_secret;
```

### Safe Usage in Request Handlers

**DON'T** reuse the same instance across requests:
```php
// BAD - state persists
$icd = app(Icd10::class);
$icd->setup()->oauth();
// ... next request still has old $__token
```

**DO** create fresh instances or reset state:
```php
// GOOD - fresh instance each time
$icd = new Icd10();
$icd->setup()->oauth();
```

### Console Commands Are Safe
For `module-icd:scrapping` and `module-icd:translate`, this is not a concern since each command execution is isolated.

## Common Issues and Solutions

### "Unauthenticated" Errors
- Verify `ICD_CLIENT_ID` and `ICD_CLIENT_SECRET` are set correctly
- Check WHO API credentials are valid at https://icd.who.int/icdapi
- Ensure `setup()->oauth()` is called before API requests
- Token may have expired - will auto-retry once

### Translation Rate Limiting
Google Translate has rate limits for large imports:
```php
// Consider adding delays in bulk operations
foreach ($codes as $code) {
    $this->translateIcd($icd);
    usleep(100000);  // 100ms delay
}
```

### Missing Parent Codes
When importing specific codes, parents must exist first:
```bash
# Import parent chapter first
php artisan module-icd:scrapping 10 2019 --code=A00

# Then import children
php artisan module-icd:scrapping 10 2019 --code=A00.0,A00.1
```

### Memory Issues During Large Imports
The scrapping command imports recursively and may use significant memory:
```bash
# Run with increased memory limit
php -d memory_limit=1G artisan module-icd:scrapping 10 2019
```

### Database Collision with module-disease
Both modules use the `diseases` table. Ensure:
1. `module-disease` migrations run first
2. Don't run conflicting migrations
3. Use version/flag fields to distinguish records

## Testing

```bash
cd /var/www/projects/wellmed/repositories/module-icd
vendor/bin/phpunit
```

**Note:** Tests require valid WHO API credentials in environment.

## Modification Checklist

Before modifying module-icd:

- [ ] Change doesn't affect `registers(['*'])` behavior negatively
- [ ] OAuth credentials are not hardcoded or exposed
- [ ] State properties are documented if added
- [ ] Octane implications considered for new stateful code
- [ ] Commands tested with actual WHO API
- [ ] Translation tested with Google Translate service
- [ ] Parent-child hierarchy maintained in imports

## Usage Examples

### Programmatic Import
```php
use Hanafalah\ModuleIcd\Schemas\Icd10;

$icd10 = app(Icd10::class);
$icd10->setup()->oauth();

// Import specific release
$icd10->installIcd10('2019');
```

### Query ICD Codes
```php
use Hanafalah\ModuleIcd\Models\Icd10;

// Get all ICD-10 codes
$codes = Icd10::all();

// Search by code
$disease = Icd10::where('code', 'A00')->first();

// Get children of a code
$children = Icd10::where('parent_id', $parentId)->get();

// Search by name
$results = Icd10::where('name', 'like', '%cholera%')->get();
```

### API Search Integration
```php
$icd = app(Icd10::class);
$icd->setup()->oauth();

// Search WHO API directly
$results = $icd->getEntityBySearch('cholera');

// Auto-code diagnosis text
$suggestion = $icd->getEntityAutocode('acute diarrhea');
```
