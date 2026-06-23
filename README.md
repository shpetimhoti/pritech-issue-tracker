# Trackly — Mini Issue Tracker

Trackly is a Laravel-based issue-tracking application built as part of the PRITECH Laravel Developer technical challenge.

It allows small teams to manage projects, issues, tags, comments, deadlines, priorities, and assigned members through a clean Blade interface.

The application includes the required functionality from the challenge, together with the optional assignment, authorization, and AJAX search features.

## Features

### Projects

* List all projects
* Create new projects
* View project details and related issues
* Edit and delete projects
* Project start date and deadline
* Project ownership
* Owner-based edit and delete authorization
* Issue count per project

### Issues

* List all issues
* Create, view, edit, and delete issues
* Assign an issue to a project
* Status options:

  * Open
  * In progress
  * Closed
* Priority options:

  * Low
  * Medium
  * High
* Optional due dates
* Filter by status
* Filter by priority
* Filter by tag
* Combine multiple filters
* Debounced AJAX search by title and description
* AJAX pagination for search results

### Tags

* Create and list tags
* Unique tag names
* Optional custom colors
* Attach tags to issues without reloading the page
* Detach tags from issues without reloading the page
* Duplicate tag assignments are prevented

### Comments

* Load issue comments asynchronously
* Paginated AJAX comment loading
* Add comments without reloading the page
* New comments are added to the top of the list
* Inline validation errors
* “Load more” pagination

### Members

* Assign multiple users to an issue
* Remove users from an issue
* AJAX attach and detach
* Duplicate assignments are prevented

### Authentication and authorization

* User registration
* Login and logout
* Session-based authentication
* Guests may view project and issue pages
* Authenticated users may create projects
* Only a project owner may edit or delete their project
* Unauthorized actions return an HTTP 403 response

### Additional functionality

* Responsive desktop and mobile interface
* Form Request validation
* Laravel Policies
* Eloquent relationships
* Eager loading to reduce N+1 queries
* Model factories and database seeders
* Feature tests
* Logical Git commit history

## Technology stack

* PHP 8.2+
* Laravel 12
* MySQL
* Blade
* Tailwind CSS
* Vanilla JavaScript
* AJAX using the Fetch API
* Pest or PHPUnit
* Vite

## Database relationships

* A project has many issues
* An issue belongs to a project
* An issue has many comments
* An issue belongs to many tags through `issue_tag`
* An issue belongs to many users through `issue_user`
* A tag belongs to many issues
* A comment belongs to an issue
* A project belongs to an owner

## Installation

Clone the repository:

```bash
git clone https://github.com/shpetimhoti/pritech-issue-tracker.git
cd pritech-issue-tracker
```

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Create the environment file:

```bash
cp .env.example .env
```

On Windows PowerShell, use:

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

## Database configuration

Create a MySQL database, for example:

```text
pritech_issue_tracker
```

Update the database settings in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pritech_issue_tracker
DB_USERNAME=root
DB_PASSWORD=
```

Run the migrations and seed the database:

```bash
php artisan migrate --seed
```

To completely rebuild the demo database:

```bash
php artisan migrate:fresh --seed
```

Warning: `migrate:fresh` deletes all existing database data.

## Running the application

Start the Laravel development server:

```bash
php artisan serve
```

Start the frontend development server:

```bash
npm run dev
```

Open the application at:

```text
http://127.0.0.1:8000
```

## Demo account

A demo user is created by the database seeder.

```text
Email: test@example.com
Password: password
```

Additional users are also seeded for testing issue assignments and project authorization.

Check `database/seeders/DatabaseSeeder.php` if the demo credentials have been changed.

## Running tests

Run the application test suite:

```bash
php artisan test
```

Build the frontend assets:

```bash
npm run build
```

Clear cached application files when needed:

```bash
php artisan optimize:clear
```

## AJAX functionality

The application uses the browser Fetch API for asynchronous interactions.

AJAX is used for:

* Attaching and detaching issue tags
* Loading paginated comments
* Adding comments
* Assigning and removing issue members
* Debounced issue search
* AJAX search-result pagination

Validation failures return HTTP `422` JSON responses and are displayed inline in the interface.

## Search behavior

Issue search matches text from:

* Issue title
* Issue description

The search uses a short debounce delay so a request is not sent after every keystroke.

Search can be combined with:

* Status
* Priority
* Tag

An `AbortController` is used to prevent older search requests from replacing newer results.

## Authorization

Project authorization is implemented with `ProjectPolicy`.

The policy allows:

* Public viewing of projects
* Authenticated project creation
* Editing only by the project owner
* Updating only by the project owner
* Deleting only by the project owner

Ownership is assigned on the server using the authenticated user ID and is not accepted from browser input.

## Validation examples

The application validates:

* Required project and issue fields
* Valid issue statuses and priorities
* Project deadline after or equal to start date
* Existing project and tag IDs
* Unique tag names
* Valid hexadecimal tag colors
* Comment author name and body
* Confirmed registration passwords

Validation is handled through dedicated Form Request classes.

## Project structure

Important application areas:

```text
app/
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Models/
└── Policies/

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── css/
├── js/
└── views/

routes/
└── web.php

tests/
└── Feature/
```

## Screenshots

Add screenshots to a folder such as:

```text
docs/screenshots/
```

Suggested screenshots:

* Projects list
* Project details
* Issue list and filters
* Issue details
* AJAX tag management
* AJAX comments
* Member assignments
* Login page
* Mobile project list

Example Markdown:

```markdown
![Projects](docs/screenshots/projects.png)
![Issue details](docs/screenshots/issue-details.png)
![Mobile layout](docs/screenshots/mobile-projects.png)
```

## Design decisions

### String values for status and priority

Issue status and priority are stored as strings and constrained through application validation. This keeps the database structure flexible while still allowing only supported values.

### Separate project date migration

The `start_date` and `deadline` fields are added through a separate migration, following the technical-task requirement.

### Server-rendered AJAX results

Search results are rendered using a reusable Blade partial. This avoids duplicating issue markup in JavaScript and keeps the server-rendered and AJAX interfaces consistent.

### Vanilla JavaScript

The AJAX features use the native Fetch API without introducing an additional JavaScript framework.

### Conventional Laravel architecture

The project uses:

* Resource controllers
* Form Request validation
* Route model binding
* Eloquent relationships
* Laravel Policies
* Model factories
* Database seeders
* Feature tests

Unnecessary repository or service abstractions were intentionally avoided to keep the project clear and maintainable.

## Possible future improvements

* Email notifications for issue changes
* Activity history
* File attachments
* Role and permission management
* Project dashboards and reporting
* Drag-and-drop issue boards
* Real-time updates
* API authentication

## Author

Shpetim Hoti

GitHub: [shpetimhoti](https://github.com/shpetimhoti)
