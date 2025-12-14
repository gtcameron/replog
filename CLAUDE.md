# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

RepLog is a family-based activity logging application. Families can define custom activities (exercises, tasks, etc.) with configurable metrics, and family members can log their activity sessions. The app supports analytics and statistics tracking.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.4, SQLite
- **Frontend:** Vue 3 + Inertia v2, Tailwind CSS v4, TypeScript
- **UI Components:** shadcn-vue (Reka UI based), Lucide icons
- **Routing:** Laravel Wayfinder for type-safe frontend route generation
- **Authentication:** Laravel Fortify
- **Testing:** Pest v4
- **Charts:** Unovis Vue

## Common Commands

```bash
# Development
composer run dev          # Start all services (server, queue, logs, vite)
npm run dev               # Vite dev server only
npm run build             # Production build

# Testing
php artisan test                              # Run all tests
php artisan test tests/Feature/ExampleTest.php  # Run specific file
php artisan test --filter=testName            # Filter by test name

# Code Quality
vendor/bin/pint --dirty   # Format PHP (run before committing)
npm run lint              # ESLint with auto-fix
npm run format            # Prettier formatting
```

## Architecture

### Data Model

All data is scoped to families. A user belongs to a family and can only access their family's data.

- **Family** → has many Users, Activities, ActivityTypes, ActivityLogs
- **User** → can be login-enabled (`can_login=true`) or non-login family member
- **Activity** → belongs to ActivityType, has metric flags (tracks_sets, tracks_reps, tracks_weight, tracks_duration, tracks_distance)
- **ActivityLog** → records a performed activity with metrics, tracks both performer (`user_id`) and logger (`logged_by_id`)

### Family Scoping Pattern

Controllers authorize access by checking `family_id` matches the authenticated user's family. See `ActivityController` for the pattern.

### Frontend Structure

```
resources/js/
├── pages/           # Inertia page components (Vue)
│   ├── Activities/  # CRUD pages
│   ├── ActivityLogs/
│   ├── ActivityTypes/
│   ├── Family/      # Family settings + member management
│   ├── Stats/       # Analytics dashboards
│   └── Auth/        # Fortify auth pages
├── components/ui/   # shadcn-vue components
├── types/index.d.ts # TypeScript interfaces
└── actions/         # Wayfinder-generated route functions (auto-generated)
```

### Wayfinder Usage

Import controller actions for type-safe routing:

```typescript
import { index, store } from '@/actions/App/Http/Controllers/ActivityController';

// Use with Inertia forms
form.submit(store());

// Get URL string
index.url(); // "/activities"
```

### Form Requests

All validation uses Form Request classes in `app/Http/Requests/`. Use array-based validation rules (not string-based).

### Enums

`App\Enums\EquipmentType` defines equipment categories with a `label()` method for display names.
