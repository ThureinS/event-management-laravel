# Event Management API

A RESTful API built with Laravel 12 for managing events and attendees. This is a **learning project** developed as part of a Udemy Laravel course.

> **Note:** This project has no license and is intended for educational purposes only.

## Overview

This API allows users to:

-   Create, read, update, and delete events
-   Register as attendees to events
-   Receive email reminders for upcoming events
-   Authenticate using token-based authentication

## Tech Stack

-   **Framework:** Laravel 12
-   **PHP Version:** 8.2+
-   **Authentication:** Laravel Sanctum (token-based)
-   **Database:** SQLite (default, configurable)
-   **Queue:** Database driver
-   **Frontend Build:** Vite with Tailwind CSS

## Features

### Authentication

-   Token-based authentication using Laravel Sanctum
-   Login and logout endpoints
-   Protected routes for creating, updating, and deleting resources

### Events

-   Create events with name, description, start and end times
-   View all events (public, with pagination)
-   View single event details (public)
-   Update and delete own events (authenticated, owner only)
-   Optional relationship loading via `?include=user,attendees`

### Attendees

-   Register as an attendee to any event (authenticated)
-   View all attendees for an event (public)
-   Remove attendance (authenticated, event owner or self)

### Notifications

-   Queued email reminders for upcoming events
-   Scheduled daily task to send reminders for events starting within 24 hours

### Authorization

-   Policy-based authorization for Events and Attendees
-   Event owners can update/delete their events
-   Attendees can be removed by event owner or the attendee themselves

## API Endpoints

### Authentication

| Method | Endpoint      | Description              | Auth Required |
| ------ | ------------- | ------------------------ | ------------- |
| POST   | `/api/login`  | Login and get token      | No            |
| POST   | `/api/logout` | Logout and revoke tokens | Yes           |
| GET    | `/api/user`   | Get authenticated user   | Yes           |

### Events

| Method    | Endpoint           | Description      | Auth Required |
| --------- | ------------------ | ---------------- | ------------- |
| GET       | `/api/events`      | List all events  | No            |
| GET       | `/api/events/{id}` | Get single event | No            |
| POST      | `/api/events`      | Create event     | Yes           |
| PUT/PATCH | `/api/events/{id}` | Update event     | Yes (owner)   |
| DELETE    | `/api/events/{id}` | Delete event     | Yes (owner)   |

### Attendees

| Method | Endpoint                             | Description          | Auth Required    |
| ------ | ------------------------------------ | -------------------- | ---------------- |
| GET    | `/api/events/{event}/attendees`      | List event attendees | No               |
| GET    | `/api/events/{event}/attendees/{id}` | Get single attendee  | No               |
| POST   | `/api/events/{event}/attendees`      | Register as attendee | Yes              |
| DELETE | `/api/events/{event}/attendees/{id}` | Remove attendee      | Yes (owner/self) |

### Query Parameters

**For Event endpoints:**

-   `?include=user` - Include the event creator's user data
-   `?include=attendees` - Include attendees for the event
-   `?include=attendees.user` - Include attendees with their user data
-   `?include=user,attendees` - Include multiple relationships (comma-separated)

**For Attendee endpoints:**

-   `?include=user` - Include the attendee's user data

## Installation

### Prerequisites

-   PHP 8.2 or higher
-   Composer
-   Node.js and npm

### Setup

1. **Clone the repository**

    ```bash
    git clone <repository-url>
    cd event-management
    ```

2. **Install dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Run migrations**

    ```bash
    php artisan migrate
    ```

5. **Seed the database (optional)**
    ```bash
    php artisan db:seed
    ```
    This creates:
    - 1000 test users
    - 200 events
    - Random attendee registrations

## Running the Application

### Development Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

### With Queue Worker and Vite (using composer script)

```bash
composer run dev
```

This starts the server, queue listener, log watcher (Pail), and Vite concurrently.

### Queue Worker (for notifications)

```bash
php artisan queue:work
```

### Scheduled Tasks

To run the scheduler (sends daily event reminders):

```bash
php artisan schedule:work
```

Or manually trigger reminders:

```bash
php artisan app:send-event-reminders
```

## Project Structure

```
app/
├── Console/Commands/
│   └── SendEventReminders.php    # Artisan command for email reminders
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php    # Login/logout
│   │   ├── EventController.php   # Event CRUD
│   │   └── AttendeeController.php # Attendee management
│   ├── Resources/
│   │   ├── EventResource.php     # Event JSON transformation
│   │   ├── AttendeeResource.php  # Attendee JSON transformation
│   │   └── UserResource.php      # User JSON transformation
│   └── Traits/
│       └── CanLoadRelationships.php # Dynamic relationship loading
├── Models/
│   ├── Event.php                 # Event model
│   ├── Attendee.php              # Attendee model
│   └── User.php                  # User model with Sanctum
├── Notifications/
│   └── EventReminderNotification.php # Queued email notification
├── Policies/
│   ├── EventPolicy.php           # Event authorization
│   └── AttendeePolicy.php        # Attendee authorization
└── Providers/
    └── AppServiceProvider.php    # Rate limiting configuration

database/
├── factories/
│   ├── EventFactory.php          # Event factory for seeding
│   └── UserFactory.php           # User factory
├── migrations/                    # Database schema
└── seeders/
    ├── DatabaseSeeder.php        # Main seeder
    ├── EventSeeder.php           # Event seeder
    └── AttendeeSeeder.php        # Attendee seeder
```

## Database Schema

### Users

-   `id`, `name`, `email`, `password`, `remember_token`, `timestamps`

### Events

-   `id`, `user_id` (creator), `name`, `description`, `start_at`, `end_at`, `timestamps`

### Attendees

-   `id`, `user_id`, `event_id`, `timestamps`

### Personal Access Tokens

-   Managed by Laravel Sanctum for API authentication

## Rate Limiting

The API implements rate limiting:

-   60 requests per minute per user (or IP for unauthenticated requests)
-   Applied to protected routes using `throttle:api` middleware

## Testing

Run the test suite:

```bash
php artisan test
```

Or:

```bash
composer test
```

## Key Laravel Concepts Demonstrated

This project demonstrates several Laravel concepts:

-   **API Resources** - JSON response transformation
-   **Policies** - Authorization logic
-   **Sanctum** - API token authentication
-   **Eloquent Relationships** - HasMany, BelongsTo
-   **Route Model Binding** - Automatic model injection
-   **Form Request Validation** - Input validation in controllers
-   **Queued Notifications** - Async email sending
-   **Task Scheduling** - Daily reminder jobs
-   **Database Seeders & Factories** - Test data generation
-   **Rate Limiting** - API throttling
-   **Traits** - Reusable code for relationship loading

## Notes for Learning

This is a learning project and may not follow all production best practices. Some areas that could be improved for production:

-   Add comprehensive input validation with Form Request classes
-   Implement user registration endpoint
-   Add more robust error handling
-   Write comprehensive test coverage
-   Add API documentation (e.g., OpenAPI/Swagger)
-   Implement caching strategies
-   Add logging and monitoring

---

**Happy Learning! 🚀**
