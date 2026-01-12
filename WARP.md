# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Commands and workflows

### Running the web application

This is a classic PHP [CodeIgniter 3] application. It is designed to be served by a PHP web server (Apache/nginx + PHP-FPM, or CGI/FastCGI) with the repository root as the document root.

There are no project-specific CLI scripts to start a dev server. For quick local/manual testing without a full web stack you can use PHP's built-in server from the repo root:

- Serve from the current directory (repo root) on port 8000:
  - `php -S localhost:8000`

You will typically access the app via:

- `http://localhost/index.php` (or the mapped virtual host) – the default route is the `login` controller.

### Dependencies and autoloading

- `application/config/config.php` sets `$config['composer_autoload'] = 'vendor/autoload.php';`
  - The application expects a `vendor/` directory containing Composer-installed PHP libraries.
  - In this copy of the repo there is no project-level `composer.json` at the root; `vendor/` and some libraries (e.g. Stripe, dompdf, Laminas, TCPDF) are already checked in.
  - You generally **do not** need to run Composer in this repo unless you are deliberately updating or re-vendoring dependencies.

### Tests

The `tests/` directory contains the CodeIgniter framework test suite (copied from upstream CodeIgniter), not application-specific tests.

From `tests/README.md`:

- Requirements (framework tests):
  - PHPUnit (historically installed via PEAR) and `vfsStream`.
- To run all framework tests:
  1. Install PHPUnit and vfsStream as described in `tests/README.md` (or via a modern PHPUnit installation on your system).
  2. From the repo root:
     - `cd tests`
     - `phpunit`

Running a single PHPUnit test file (standard PHPUnit usage):

- From `tests/`:
  - `phpunit path/to/YourTestFile.php`

There is no dedicated PHPUnit configuration (`phpunit.xml`) or test suite for the business logic under `application/` in this repo.

### Vendored library tooling (usually not needed for app development)

Several third-party libraries include their own development tooling inside this repo:

- `application/libraries/stripe/Makefile` – for developing the Stripe PHP client library, not the main app:
  - Install dev dependencies for the Stripe library: `make vendor`
  - Run its unit tests: `make test` (which executes `vendor/bin/phpunit` inside that library)
  - Run formatting and static analysis for the Stripe library only: `make fmt`, `make fmtcheck`, `make phpstan`
- Front-end plugins under `theme/plugins/*` have their own `package.json` files (e.g. `Bootstrap-Confirmation-2`, `tableExporter`). Their npm scripts are for developing those libraries upstream and are **not** wired into any project-level build.

In normal application development you rarely need to touch these; they are vendored dependencies.


## High-level architecture and structure

### Overall layout

- `index.php` – front controller for CodeIgniter.
  - Defines `ENVIRONMENT` based on `$_SERVER['CI_ENV']` (defaults to `production`).
  - Ensures a `.htaccess` file exists at the repo root by copying from `uploads/htaccess_file/.htaccess` on first run.
  - Sets up `BASEPATH`, `APPPATH`, and `VIEWPATH` and then boots the framework via `require_once BASEPATH.'core/CodeIgniter.php';`.
- `system/` – CodeIgniter core framework (unmodified framework code).
- `application/` – main application code (controllers, models, views, helpers, libraries, config, logs).
- `theme/` – UI assets and third-party front-end plugins.
- `vendor/` – Composer-installed PHP libraries used by the app (Stripe, dompdf, tcpdf, Laminas components, etc.).
- `tests/` – CodeIgniter framework tests (see above; not specific to this business application).

### CodeIgniter application structure (`application/`)

#### Configuration (`application/config`)

Key files:

- `config.php`
  - Derives `base_url` dynamically from `$_SERVER['HTTP_HOST']` and the executing script path.
  - Sets `index_page` to `index.php` (URLs will include `index.php` unless you configure web server rewrites).
  - Enables Composer autoloading of `vendor/autoload.php`.
  - Configures sessions to use the database (`sess_driver = 'database'`, `sess_save_path = 'ci_sessions'`).
  - Enables CSRF protection (`$config['csrf_protection'] = TRUE`).
- `routes.php`
  - Sets `$route['default_controller'] = 'login';` – the login page is the entry point for anonymous users.
  - Leaves 404 override and URI dash translation at defaults.
- Other config files (`database.php`, `autoload.php`, `constants.php`, `stripe.php`, `instamojo.php`, etc.) define DB connections, autoloaded helpers/libraries, global constants, and payment gateway settings.

These configs are the main levers for changing environment-specific behavior (URLs, sessions, DB, payment integrations).

#### Base controller and cross-cutting concerns (`application/core/MY_Controller.php`)

All feature controllers extend `MY_Controller`, which extends `CI_Controller`. This class centralizes most cross-cutting behavior:

- **Versioning and updates**
  - `public $source_version = "1.7.1";`
  - `update_db()` calls `updates_model->index()` to apply DB migrations/updates.
  - In `Dashboard` and likely other controllers, `get_current_version_of_db()` is compared against `app_version()`; mismatch redirects to `updates/update_db`.
- **Language selection and localization**
  - Before login, `__construct()` calls `update_db()` when hitting the `login` route or root.
  - Language is tracked via a `language` session key; if a `language` cookie is present it is migrated into the session and the cookie cleared.
  - Loads language files based on the selected language: `$this->lang->load($default_lang, $default_lang);`.
  - `load_info()` ensures language is set for logged-in users via `language_model` and reloads the appropriate language packs.
- **Store, timezone, and presentation settings**
  - `load_info()` queries `db_sitesettings` and `db_store` to get:
    - Site name and version.
    - Store name, timezone, time format, date format, decimal precision, quantity decimals.
  - Sets PHP's default timezone to the store's timezone and precomputes formatted date/time values.
  - Stores various derived values in the session (`view_date`, `view_time`, `decimals`, `qty_decimals`, `store_name`).
  - Populates `$this->data` (an array shared with views) with:
    - `theme_link`, `base_url`, `SITE_TITLE`, `VERSION`.
    - Currency symbol, placement, and code from session (`load_currency_data()`).
    - Current date/time, system IP/name, and current user info.
- **Currency handling**
  - `load_currency_data()` looks up the store's currency metadata from `db_currency`/`db_store` and stores it in session.
  - `currency()`, `store_wise_currency()`, and `currency_code()` format amounts according to store currency and placement.
- **Authentication and authorization**
  - `load_global()` is the common entry point for controllers:
    - Ensures the user is logged in; otherwise redirects to `logout`.
    - Calls `verify_store_and_user_status()` to enforce active store and user status.
    - Calls `load_info()` to initialize per-request globals.
  - `permissions($permissions)` checks the `db_permissions` table against the user's `role_id`; user ID 1 is treated as a super admin.
  - `permission_check()` and `permission_check_with_msg()` enforce permissions, either with a 403 error page or a simple message/exit.
  - `show_access_denied_page()` provides a shared 403 error page.
- **Store scoping and data ownership**
  - `belong_to($table, $rec_id)` uses `is_it_belong_to_store()` (helper/global) to enforce that records belong to the current store; otherwise it aborts with an error.

When adding new controllers or endpoints, you should:

- Extend `MY_Controller`.
- Call `$this->load_global();` in the controller constructor to inherit authentication, store checks, and global data initialization.
- Use the permission helpers when gating access to actions.

#### Controllers (`application/controllers`)

There are many controllers corresponding to business modules: `Dashboard`, `Sales`, `Purchase`, `Customers`, `Suppliers`, `Reports`, `Items`, `Warehouse`, `Tax`, `Roles`, `Users`, `Online_payments`, etc.

Common patterns (illustrated by `Dashboard`):

- Constructor:
  - Extends `MY_Controller` and calls `$this->load_global();`.
  - Enforces DB version compatibility and redirects to the update controller if needed.
- Actions:
  - Use one or more models (e.g., `dashboard_model`) to fetch data.
  - Merge `$this->data` from `MY_Controller` with model-provided data before rendering views.
  - Use permission checks to decide which view to render (e.g., `role/dashboard_empty` vs `dashboard`).
  - Provide AJAX endpoints (e.g., `dashboard_values()`, `get_storewise_details()`, `ajax_list()`) that output JSON or HTML fragments for front-end charts and tables.

Understanding one controller (like `Dashboard`) gives you a good template for how others are structured.

#### Models (`application/models`)

Models encapsulate database operations and business logic for each domain area; examples include:

- `Sales_model`, `Purchase_model`, `Reports_model`, `Items_model`, `Customers_model`, `Suppliers_model`, `Warehouse_model`, `Tax_model`, `Roles_model`, `Users_model`, `Store_model`, `Store_profile_model`, etc.
- Specialized models like `Gateways_model`, `Subscription_model`, `Updates_model`, `Language_model`, `Twilio_model`, etc.

They typically:

- Use CodeIgniter's query builder (`$this->db`) to query tables like `db_sales`, `db_store`, `db_expense`, `db_permissions`, etc.
- Are consumed directly from controllers.

When modifying business rules or data access, you will likely work in these models.

#### Views (`application/views`)

Views are PHP templates corresponding to pages and partials, for example:

- `dashboard.php`, `dashboard2.php` – main dashboard views.
- `customers.php`, `customers-view.php`, `brand.php`, `category.php`, various lists and forms.
- `email-template.php`, `email-templates-list.php`, etc.
- A `role/dashboard_empty.php` view used when a user lacks permission to see the full dashboard.

Views rely heavily on `$this->data` from `MY_Controller` for global information like current store, currency, and theme paths.

#### Helpers and libraries

- Helpers (`application/helpers`): domain-specific procedural helpers for accounts, inventory, SMS templates, currency conversion, SaaS features, etc.
- Libraries (`application/libraries`):
  - Custom integrations with payment gateways (Instamojo, Skrill, PayPal), email/PDF generation (`Pdf.php`), and Zend-based barcode rendering.
  - A vendored `stripe` library with its own `README` and `Makefile` (standard Stripe PHP client).

### Front-end and theming (`theme/`)

- `theme/` holds CSS, JS, images, and plugin assets.
- `theme/plugins/` contains third-party JS plugins such as:
  - `Bootstrap-Confirmation-2` (with its own `package.json` and build tooling).
  - `tableExporter` (with `package.json`).
  - `ckeditor`, `raphael`, and others.

These are generally consumed directly as static assets; there is no central front-end build step configured at the project root.

### Logs (`application/logs`)

- `application/logs/` stores CodeIgniter log files named like `log-YYYY-MM-DD.php`.
- Logging behavior is configured in `application/config/config.php` (`log_threshold`, `log_path`, etc.).


## Practical notes for future Warp agents

- When adding new HTTP endpoints, always:
  - Extend `MY_Controller`.
  - Call `$this->load_global();` in the constructor.
  - Use `permission_check()` / `permission_check_with_msg()` to enforce access control.
- Be aware of multi-store behavior: many queries and UI elements are scoped by the current store (`get_current_store_id()`) and store-specific currency settings.
- Language and currency are dynamic per-user; avoid hard-coding text or currency formatting in views and controllers – use `$this->lang->line(...)` and the currency helpers in `MY_Controller`.
- Treat `system/` and most of `vendor/` as third-party code; avoid modifying them unless absolutely necessary. Prefer changes in `application/` (controllers, models, helpers, libraries) for app-specific behavior.
