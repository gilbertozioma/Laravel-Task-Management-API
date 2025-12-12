# Task Management System API

A RESTful API for task management built with Laravel 12 and Sanctum authentication.

**Developer:** Gilbert Ozioma  
**Project:** Technical Implementation - Task Management System

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technical Requirements](#technical-requirements)
- [Installation](#installation)
- [Database Configuration](#database-configuration)
- [Project Structure](#project-structure)
- [API Documentation](#api-documentation)
- [Testing](#testing)
- [Security Implementation](#security-implementation)
- [Troubleshooting](#troubleshooting)

---

## Overview

This project implements a complete task management REST API with user authentication, CRUD operations, filtering, pagination, and comprehensive testing. The system allows users to register, authenticate, and manage their personal tasks through a secure API.

### Key Capabilities

- User registration and authentication via Laravel Sanctum
- Full CRUD operations for task management
- User-scoped data access (users can only manage their own tasks)
- Task filtering by status
- Paginated responses
- Comprehensive input validation
- Error handling with appropriate HTTP status codes
- Automated test coverage

---

## Features

### Core Implementation

**Authentication**
- User registration with validation
- Token-based login using Laravel Sanctum
- Secure logout with token revocation

**Task Management**
- Create tasks with title, description, status, priority, and due date
- Retrieve all user tasks with pagination
- Retrieve individual tasks by ID
- Update task properties (including priority and due date)
- Delete tasks
- Filter tasks by status and priority (pending, in-progress, completed; low, medium, high)

**Data Validation**
- Form Request validation classes
- Custom error messages
- Input sanitization
- Type checking and constraints

Validation specifics for new fields:
- `priority`: optional, must be one of `low`, `medium`, `high` (defaults to `medium`)
- `due_date`: optional, must be a future datetime in `YYYY-MM-DD HH:MM:SS` format

**Authorization**
- Tasks scoped to authenticated users
- Protection against unauthorized access
- Proper HTTP status codes for authorization failures

**Testing**
- 8 comprehensive feature tests
- Authentication flow testing
- CRUD operation verification
- Authorization testing

---

## Technical Requirements

### System Requirements

- PHP 8.2 or higher
- Composer (latest version)
- MySQL 5.7+ / PostgreSQL 9.6+ / SQLite 3
- Laravel 12.x
- Required PHP Extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON

### Dependencies

- Laravel Framework 12.x
- Laravel Sanctum (API authentication)
- PHPUnit (testing framework)

---

## Installation

### Step 1: Project Setup

```bash
# Clone the repository
git clone git@github.com:gilbertozioma/Laravel-Task-Management-API.git
cd Laravel-Task-Management-API

# Or extract from ZIP
unzip task-management-system-api.zip
cd tms-api
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Environment Variables

Edit `.env` file and update the following:

```env
APP_NAME="Task Management System API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=
```

### Step 5: Install Laravel Sanctum

```bash
composer require laravel/sanctum
php artisan install:api
```

### Step 6: Database Migration

```bash
php artisan migrate
```

### Step 7: Start Development Server

```bash
php artisan serve
```

The API will be accessible at: `http://localhost:8000`

### Step 8: Seed Database (Optional)

To populate the database with sample data for testing:

```bash
php artisan db:seed
```

This will create:
- 3 demo users (including Gilbert Ozioma)
- 15 sample tasks distributed across users with various statuses

---

## Database Configuration

### MySQL Setup

```sql
-- Create database
CREATE DATABASE task_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user (optional)
CREATE USER 'taskuser'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON task_management.* TO 'taskuser'@'localhost';
FLUSH PRIVILEGES;
```

### PostgreSQL Setup

```sql
-- Create database
CREATE DATABASE task_management;

-- Create user (optional)
CREATE USER taskuser WITH PASSWORD 'password';
GRANT ALL PRIVILEGES ON DATABASE task_management TO taskuser;
```

### SQLite Setup

```bash
# Create database file
touch database/database.sqlite

# Update .env
DB_CONNECTION=sqlite
# Remove or comment out other DB_ variables
```

### Database Schema

**Users Table:**
- id (primary key)
- name (string)
- email (string, unique)
- password (hashed)
- timestamps

**Tasks Table:**
- id (primary key)
- title (string, required)
- description (text, nullable)
- status (enum: pending, in-progress, completed)
- user_id (foreign key, cascades on delete)
- timestamps

---

## Project Structure

```
task-management-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php          # Authentication endpoints
│   │   │       └── TaskController.php          # Task CRUD operations
│   │   └── Requests/
│   │       ├── LoginRequest.php                # Login validation
│   │       ├── RegisterRequest.php             # Registration validation
│   │       ├── StoreTaskRequest.php            # Task creation validation
│   │       └── UpdateTaskRequest.php           # Task update validation
│   └── Models/
│       ├── Task.php                            # Task model
│       └── User.php                            # User model with Sanctum
├── bootstrap/
│   └── app.php                                 # Application bootstrap with API exception handling
├── database/
│   ├── factories/
│   │   └── TaskFactory.php                     # Task factory for testing
│   ├── migrations/
│   │   ├── xxxx_create_users_table.php         # Users schema
│   │   └── xxxx_create_tasks_table.php         # Tasks schema
│   └── seeders/
│       ├── DatabaseSeeder.php                  # Main database seeder
│       └── TaskSeeder.php                      # Task seeder for demo data
├── postman/
│   └── Task_Management_API.postman_collection.json  # API test collection
├── routes/
│   └── api.php                                 # API route definitions
├── tests/
│   └── Feature/
│       └── TaskApiTest.php                     # Feature tests
├── .env.example                                # Environment template
├── composer.json                               # Dependencies
├── phpunit.xml                                 # Test configuration
└── README.md                                   # Documentation
```

---

## API Documentation

### Base URL

```
http://localhost:8000/api
```

### Authentication

Protected endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer {token}
```

### Response Format

All responses return JSON:

**Success:**
```json
{
    "message": "Operation successful",
    "data": {}
}
```

**Error:**
```json
{
    "message": "Error description",
    "errors": {
        "field": ["Error detail"]
    }
}
```

### HTTP Status Codes

- `200 OK` - Successful request
- `201 Created` - Resource created successfully
- `401 Unauthorized` - Authentication required or invalid
- `403 Forbidden` - Access denied
- `404 Not Found` - Resource does not exist
- `422 Unprocessable Entity` - Validation failed
- `500 Internal Server Error` - Server error

---

## API Endpoints

### Authentication Endpoints

#### Register User

```http
POST /api/register
Content-Type: application/json

{
    "name": "Gilbert Ozioma",
    "email": "gilbertozioma0@gmail.com",
    "password": "11111111",
    "password_confirmation": "11111111"
}
```

**Response (201):**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "Gilbert Ozioma",
        "email": "gilbertozioma0@gmail.com",
        "created_at": "2024-11-08T10:30:00.000000Z",
        "updated_at": "2024-11-08T10:30:00.000000Z"
    },
    "token": "1|AbCdEfGh..."
}
```

#### Login User

```http
POST /api/login
Content-Type: application/json

{
    "email": "gilbertozioma0@gmail.com",
    "password": "11111111"
}
```

**Response (200):**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Gilbert Ozioma",
        "email": "gilbertozioma0@gmail.com"
    },
    "token": "2|XyZaBcDe..."
}
```

#### Logout User

```http
POST /api/logout
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Logged out successfully"
}
```

---

### Task Endpoints

All task endpoints require authentication.

#### Create Task

```http
POST /api/tasks
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Complete project documentation",
    "description": "Write comprehensive README and API documentation",
    "status": "pending",
    "priority": "medium",
    "due_date": "2025-12-20 18:00:00"
}
```

**Response (201):**
```json
{
    "message": "Task created successfully",
    "task": {
        "id": 1,
        "title": "Complete project documentation",
        "description": "Write comprehensive README and API documentation",
        "status": "pending",
        "priority": "medium",
        "due_date": "2025-12-20 18:00:00",
        "user_id": 1,
        "created_at": "2024-11-08T10:35:00.000000Z",
        "updated_at": "2024-11-08T10:35:00.000000Z"
    }
}
```

**Validation Rules:**
- `title`: required, string, maximum 255 characters
- `description`: optional, string
- `status`: optional, must be one of: pending, in-progress, completed (defaults to pending)
- `priority`: optional, must be one of: low, medium, high (defaults to medium)
- `due_date`: optional, must be a future datetime in `YYYY-MM-DD HH:MM:SS` format

#### Get All Tasks

```http
GET /api/tasks
Authorization: Bearer {token}
```

**Optional Query Parameters:**
- `status`: Filter by status (pending, in-progress, completed)
- `priority`: Filter by priority (low, medium, high)
- `search`: Search tasks by title or description (partial match)
- `sort_by`: Field to sort by (`created_at` or `due_date`, default: `created_at`)
- `sort_order`: Sort order (`asc` or `desc`, default: `desc`)
- `page`: Page number for pagination (default: 1)

**Response (200):**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "title": "Complete project documentation",
            "description": "Write comprehensive README",
            "status": "pending",
            "user_id": 1,
            "created_at": "2024-11-08T10:35:00.000000Z",
            "updated_at": "2024-11-08T10:35:00.000000Z"
        }
    ],
    "first_page_url": "http://localhost:8000/api/tasks?page=1",
    "from": 1,
    "last_page": 1,
    "per_page": 15,
    "to": 1,
    "total": 1
}
```

#### Filter Tasks by Status

```http
GET /api/tasks?status=pending
Authorization: Bearer {token}
```

#### Get Single Task

```http
GET /api/tasks/{id}
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "id": 1,
    "title": "Complete project documentation",
    "description": "Write comprehensive README",
    "status": "pending",
    "user_id": 1,
    "created_at": "2024-11-08T10:35:00.000000Z",
    "updated_at": "2024-11-08T10:35:00.000000Z"
}
```

#### Update Task

```http
PUT /api/tasks/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Complete project documentation - Updated",
    "description": "Write comprehensive README with examples",
    "status": "in-progress",
    "priority": "high",
    "due_date": "2025-12-25 18:00:00"
}
```

**Partial Update Supported:**
```json
{
    "status": "completed"
}
```

**Response (200):**
```json
{
    "message": "Task updated successfully",
    "task": {
        "id": 1,
        "title": "Complete project documentation - Updated",
        "description": "Write comprehensive README with examples",
        "status": "in-progress",
        "priority": "high",
        "due_date": "2025-12-25 18:00:00",
        "user_id": 1,
        "created_at": "2024-11-08T10:35:00.000000Z",
        "updated_at": "2024-11-08T10:40:00.000000Z"
    }
}
```

#### Delete Task

```http
DELETE /api/tasks/{id}
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Task deleted successfully"
}
```

---

### Error Responses

#### Validation Error (422)

```json
{
    "message": "The given data was invalid",
    "errors": {
        "title": ["The title field is required."],
        "status": ["The status must be one of: pending, in-progress, completed."]
    }
}
```

#### Unauthorized (401)

```json
{
    "message": "Unauthenticated. Please login first."
}
```

#### Forbidden (403)

```json
{
    "message": "Unauthorized"
}
```

#### Not Found (404)

```json
{
    "message": "Resource not found."
}
```

---

## Testing with Postman

### Importing the Collection

A complete Postman collection is included with the project:

**File Location:**
```
postman/Task_Management_API.postman_collection.json
```

**Import Steps:**
1. Open Postman
2. Click "Import" button
3. Select "File" tab
4. Navigate to project directory and select the collection file
5. Click "Open" then "Import"

### Collection Contents

The collection includes 22 pre-configured requests organized into three folders:

**Authentication (3 requests)**
- Register User
- Login User
- Logout User

**Tasks - CRUD Operations (14 requests)**
- Create Task (Pending, In Progress, Completed)
- Get All Tasks
- Get All Tasks with Pagination
- Filter Tasks by Status (Pending, In Progress, Completed)
- Combined Filter and Pagination
- Get Single Task by ID
- Update Task (Full, Partial - Status Only, Partial - Title Only)
- Delete Task

**Error Handling Tests (5 requests)**
- Unauthorized Access (No Token)
- Invalid Credentials Login
- Create Task with Invalid Status
- Create Task without Required Title
- Access Non-Existent Task

### Pre-configured Credentials

All authentication requests use:
```
Name: Gilbert Ozioma
Email: gilbertozioma0@gmail.com
Password: 11111111
```

### Automated Features

The collection includes scripts that automatically:
- Save authentication tokens after registration/login
- Save task IDs after creation
- Use saved variables in subsequent requests

### Testing Workflow

1. Run "Register User" to create account and save token
2. Run "Create Task" to create a task and save its ID
3. Execute any other requests - authentication is handled automatically

---

## Testing

### Running Tests

Execute the complete test suite:

```bash
php artisan test
```

Run specific test class:

```bash
php artisan test --filter TaskApiTest
```

Run with coverage report:

```bash
php artisan test --coverage
```

### Test Coverage

The project includes 8 feature tests covering:

1. User registration functionality
2. User login authentication
3. Task creation
4. Task retrieval (list)
5. Task filtering by status
6. Task update operations
7. Task deletion
8. Authorization (users cannot access other users' tasks)

### Expected Test Output

```
PASS  Tests\Feature\TaskApiTest
✓ user can register
✓ user can login
✓ user can create task
✓ user can view own tasks
✓ user can filter tasks by status
✓ user can update task
✓ user can delete task
✓ user cannot access other users tasks

Tests:    8 passed (8 assertions)
```

---

## Security Implementation

### Authentication Security

- Token-based authentication using Laravel Sanctum
- Password hashing with bcrypt algorithm
- Secure token generation
- Token revocation on logout

### Authorization

- User-scoped data access
- Tasks accessible only by their owners
- Authorization checks in all protected endpoints
- Appropriate HTTP status codes for unauthorized access

### Input Validation

- Form Request validation classes
- Comprehensive validation rules
- Custom error messages
- Input sanitization through Laravel validation
- Type checking and constraints

### API Security

- CORS configuration for cross-origin requests
- Rate limiting to prevent abuse
- SQL injection prevention via Eloquent ORM
- XSS protection through Laravel's built-in security features
- CSRF protection disabled for API routes (token-based auth used instead)

---

## Architecture and Design

### Design Decisions

**Laravel Sanctum**
- Lightweight token-based authentication
- Suitable for API-driven applications
- Simpler than OAuth for this use case

**Form Request Validation**
- Separation of validation logic from controllers
- Reusable validation rules
- Cleaner, more maintainable code

**RESTful API Design**
- Standard HTTP methods (GET, POST, PUT, DELETE)
- Predictable URL structure
- Proper use of HTTP status codes
- JSON responses for all endpoints

**Eloquent ORM**
- Database abstraction layer
- Type-safe database operations
- Built-in relationship management
- Query scopes for filtering

### Code Quality

- PSR-12 coding standards
- Type hints and return type declarations
- Comprehensive inline documentation
- Consistent naming conventions
- Single Responsibility Principle
- DRY (Don't Repeat Yourself) principle

---

## Troubleshooting

### Route [login] Not Defined Error

This error occurs when Laravel tries to redirect to a login route that doesn't exist for APIs.

**Solution:**

Update `bootstrap/app.php` to handle API authentication exceptions:

```php
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (AuthenticationException $e, Request $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'message' => 'Unauthenticated. Please login first.'
            ], 401);
        }
    });
})->create();
```

Clear application cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Token Not Working

**Checklist:**
- Verify token is included in Authorization header: `Bearer {token}`
- Confirm user exists in database
- Check that Sanctum is properly installed: `php artisan install:api`
- Verify token hasn't been revoked

### Database Connection Error

**Checklist:**
- Verify database credentials in `.env`
- Confirm database exists
- Check database service is running
- Clear configuration cache: `php artisan config:clear`

### Validation Errors Not Displaying

**Checklist:**
- Include `Accept: application/json` header in requests
- Verify Form Request classes are being used
- Check validation rules are properly defined
- Confirm error response format is JSON

### CORS Issues

Configure CORS settings in `config/cors.php`:

```php
'paths' => ['api/*'],
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

### Test Failures

**Common Issue: SQLite Driver Not Found**

If you see this error when running tests:
```
QueryException: could not find driver (Connection: sqlite, SQL: select exists...)
```

This means the SQLite PHP extension is not enabled.

**Solution for Laragon Users:**

1. **Enable SQLite Extension:**
   - Right-click Laragon tray icon
   - Navigate to: **PHP** → **php.ini**
   - Find these lines (around line 900-950):
     ```ini
     ;extension=pdo_sqlite
     ;extension=sqlite3
     ```
   - Remove the semicolons to enable:
     ```ini
     extension=pdo_sqlite
     extension=sqlite3
     ```
   - Save the file and restart Laragon

2. **Verify SQLite is Enabled:**
   ```bash
   php -m | findstr sqlite
   ```
   
   You should see:
   ```
   pdo_sqlite
   sqlite3
   ```

3. **Run Tests Again:**
   ```bash
   php artisan test
   ```

**Alternative Solution (Use MySQL for Testing):**

If you prefer MySQL for testing, update `phpunit.xml`:

```xml
<!-- Find and replace these lines -->
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>

<!-- With these -->
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="task_management_test"/>
```

Then create the test database:
```bash
mysql -u root -p
CREATE DATABASE task_management_test;
exit;
```

**Other Common Test Issues:**

**Checklist:**
- Verify test database is configured in `phpunit.xml`
- Run migrations for test environment: `php artisan migrate --env=testing`
- Clear test cache: `php artisan config:clear`
- Check database factory definitions
- Ensure SQLite extensions are enabled (for in-memory testing)

---

## Development Notes

### Local Development

```bash
# Start development server
php artisan serve

# Run in background on specific port
php artisan serve --port=8080 &
```

### Database Management

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Refresh database (reset and re-run all migrations)
php artisan migrate:refresh

# Reset database
php artisan migrate:reset

# Seed database with sample data
php artisan db:seed

# Refresh database and seed
php artisan migrate:refresh --seed
```

### Cache Management

```bash
# Clear all caches
php artisan optimize:clear

# Clear specific caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## Project Information

**Developer:** Gilbert Ozioma  
**Email:** gilbertozioma0@gmail.com  
**Laravel Version:** 12.x  
**PHP Version:** 8.2+

---

## Database Seeding

### Running the Seeder

```bash
# Run all seeders
php artisan db:seed

# Run the task management seeder
php artisan db:seed --class=TaskSeeder

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

### What Gets Seeded

The seeder creates:

**Users:**
1. Gilbert Ozioma (gilbertozioma0@gmail.com) - Password: 11111111
2. John Doe (john@example.com) - Password: password
3. Jane Smith (jane@example.com) - Password: password

**Tasks:**
- 10 specific tasks with realistic titles and descriptions
- 5 additional random tasks for Gilbert using the factory
- Tasks distributed across different statuses (pending, in-progress, completed)
- Tasks properly associated with their respective users
  
---

## Implementation Summary

This project demonstrates:

- RESTful API development with Laravel
- Token-based authentication implementation
- Complete CRUD operations
- Input validation and error handling
- Database design and relationships
- Test-driven development
- Security best practices
- Professional code organization
- Comprehensive documentation

