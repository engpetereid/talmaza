# Talmaza — Church Family Discipleship Management System

> A full-featured web platform for managing church servant families, tracking member spiritual progress (Tatmim), submitting weekly/monthly reports, sharing lessons, and broadcasting announcements — all with real-time push notifications via Firebase Cloud Messaging.

---

## Table of Contents

- [Description](#description)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Database Design](#database-design)
- [Roles & Authorization](#roles--authorization)
- [Firebase Push Notifications](#firebase-push-notifications)
- [Architecture Notes](#architecture-notes)
- [Future Improvements](#future-improvements)
- [Contributing](#contributing)
- [License](#license)

---

## Description

**Talmaza** is a Laravel-based web application built for the Coptic Orthodox church context to manage the discipleship (تلمذة) process across multiple servant families. Each "family" is a small group led by a servant (leader) who manages a set of youth members (مخدومين).

The system solves the coordination problem between church leadership (admins) and servants (leaders) by providing:

- A structured weekly meeting record system (التتميم) tracking 12+ spiritual metrics per member
- Weekly and monthly reporting tools with admin reply/feedback threads
- A lesson curriculum library organized by stage (مرحلة)
- Administrative decisions tracking board with execution status
- Real-time push notifications for all major events

---

## Features

### For Leaders (Servants)
- **Dashboard** — View current and past weekly meetings, submit attendance and tatmim records
- **My Family** — Manage members (add, activate/deactivate, edit profiles)
- **Record Tatmim** — 2-step wizard to record meeting details and per-member spiritual metrics (attendance, mass, Tasbeha, reading, family altar, Kholwa count, training count, and more)
- **Weekly Reports** — Submit meeting timelines, achievements, visitation hours, session time, and priest messages
- **Monthly Reports** — Submit monthly summaries with auto-calculated statistics snapshot from real tatmim data
- **Member Stats** — View individual member performance charts (attendance, note score, mass, Tasbeha, Vespers, servants meeting, reading, altar, Kholwa, training, sermon) over a configurable historical period
- **Leader Stats** — Aggregated family performance view
- **Stage Stats** — Cross-family stage-level analytics
- **Notifications** — Receive push notifications for admin replies
- **Lesson Library** — Browse lessons by stage, upload/share study resources (files or text)

### For Admins
- **Admin Dashboard** — Overview of all families: member counts, last lesson taught, last meeting date, unreplied reports count; searchable family list
- **Family Management** — Create new families with an assigned leader account in a single form
- **Family Detail View** — Deep dive into a specific family's activity
- **Family Stats** — Comprehensive per-family analytics across all metrics
- **Family Stage Stats** — Stage-level breakdown per family
- **Reports Review** — Paginated report browser with filters by type (weekly/monthly), month, year, and pending status; reply inline to any section; identify families that have not submitted monthly reports
- **Announcements Board** — Post announcements to all leaders with file attachments and push notifications
- **Decisions Board** — Track administrative decisions with status (pending, implemented, postponed, not implemented) and admin comment updates

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Language** | PHP 8.2 |
| **Framework** | Laravel 12 |
| **Frontend Reactivity** | Livewire 3 + Livewire Volt |
| **CSS** | Tailwind CSS (via Vite) |
| **Database** | SQLite (default) / MySQL or PostgreSQL (configurable) |
| **Push Notifications** | Firebase Cloud Messaging via `kreait/laravel-firebase` |
| **Authentication** | Laravel Breeze (phone-based, no email) |
| **File Storage** | Laravel local disk (`public`) |
| **Queue** | Database queue driver |
| **Build Tool** | Vite |
| **Testing** | PHPUnit 11 |
| **Dev Tools** | Laravel Pail, Laravel Pint, Laravel Sail, Tinker |

---

## System Requirements

- PHP >= 8.2 with extensions: `pdo_sqlite` (or `pdo_mysql`), `fileinfo`, `mbstring`, `openssl`, `tokenizer`, `xml`
- Composer >= 2
- Node.js >= 18 + npm
- A Firebase project with a service account JSON key (for push notifications)

---

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url> talmaza_app
cd talmaza_app
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Configure the Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` as needed. The default database is SQLite; no further configuration is required for a local setup:

```env
APP_NAME=Talmaza
APP_URL=http://localhost

DB_CONNECTION=sqlite
# For MySQL, uncomment and fill:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=talmaza
# DB_USERNAME=root
# DB_PASSWORD=
```

### 5. Run Database Migrations

```bash
php artisan migrate
```

### 6. Configure Firebase (Push Notifications)

1. Go to your Firebase Console → Project Settings → Service Accounts → Generate a new private key.
2. Save the downloaded JSON file to:
   ```
   storage/app/firebase-auth.json
   ```
3. Add the Firebase project config to `.env`:
   ```env
   FIREBASE_CREDENTIALS=storage/app/firebase-auth.json
   ```

### 7. Create Storage Symlink

```bash
php artisan storage:link
```

### 8. Build Frontend Assets

```bash
npm run build
```

### 9. (Optional) One-Step Setup

The `composer.json` includes a convenience script that runs all the above steps:

```bash
composer setup
```

---

## Usage

### Run the Development Server

Use the bundled dev script to start all services concurrently (Laravel server, queue worker, Pail log viewer, and Vite HMR):

```bash
composer dev
```

Or start them individually:

```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

Access the application at: `http://localhost:8000`

### Creating the First Admin Account

Since registration uses phone numbers (not emails), you can seed an admin via Tinker:

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name'     => 'Admin',
    'phone'    => '01000000000',
    'password' => bcrypt('password'),
    'role'     => 'admin',
]);
```

### Typical Workflow

1. **Admin** logs in → creates a family and a leader account via `/admin/families/create`
2. **Leader** logs in → sees their dashboard with the current meeting → opens meeting to record Tatmim
3. In `RecordTatmim`, the leader:
   - Step 1: Selects the lesson (auto-suggested based on curriculum order) or enters a custom topic; sets meeting status and max note score
   - Step 2: Fills per-member spiritual metrics (saved automatically on every field change)
4. Leader submits a **Weekly Report** with meeting timeline, achievements, visitation hours, session time, and priest message
5. At month-end, the leader submits a **Monthly Report** — the system auto-calculates a `stats_snapshot` from the real tatmim records for that month
6. **Admin** reviews reports at `/admin/reports-review`, replies inline to each section; leaders are notified via FCM
7. Admin posts **Announcements** or **Decisions** — all users receive push notifications

---

## Project Structure

```
talmaza_app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Auth/              # Laravel Breeze auth controllers
│   │   └── Middleware/
│   │       └── EnsureAdmin.php    # Route middleware to protect admin routes
│   ├── Livewire/                  # All UI components (one component per page/feature)
│   │   ├── Actions/
│   │   │   └── Logout.php
│   │   ├── Forms/                 # Reusable form sub-components
│   │   ├── AdminDashboard.php
│   │   ├── AdminFamilyView.php
│   │   ├── AdminFamilyStats.php
│   │   ├── AdminFamilyStageStats.php
│   │   ├── AdminReportsReview.php
│   │   ├── AddFamily.php
│   │   ├── AnnouncementsBoard.php
│   │   ├── DecisionsBoard.php
│   │   ├── LeaderDashboard.php
│   │   ├── LeaderReports.php
│   │   ├── LeaderStats.php
│   │   ├── LessonLibrary.php
│   │   ├── MemberStats.php
│   │   ├── MonthlyReportForm.php
│   │   ├── MyFamily.php
│   │   ├── NotificationsList.php
│   │   ├── RecordTatmim.php
│   │   ├── StageStats.php
│   │   ├── UserProfile.php
│   │   └── WeeklyReportForm.php
│   ├── Models/                    # Eloquent models
│   │   ├── Announcement.php
│   │   ├── Family.php
│   │   ├── Lesson.php
│   │   ├── LessonResource.php
│   │   ├── Member.php
│   │   ├── Report.php
│   │   ├── Stage.php
│   │   ├── TatmimRecord.php
│   │   ├── User.php
│   │   └── WeeklyMeeting.php
│   ├── Notifications/
│   │   └── MemberAbsentAlert.php
│   └── Services/
│       └── TatmimStatsService.php  # Reusable stat calculation logic
├── database/
│   ├── migrations/                 # 26 migrations (full audit trail of schema evolution)
│   ├── factories/
│   └── seeders/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── layouts/               # App shell (sidebar, nav, auth layout)
│       ├── components/            # Reusable Blade components
│       └── livewire/              # Per-component Blade templates
├── routes/
│   ├── web.php                    # All application routes
│   └── auth.php                   # Authentication routes (Breeze)
├── storage/
│   └── app/
│       └── firebase-auth.json     # Firebase service account key (not committed)
├── .env.example
├── composer.json
├── package.json
├── tailwind.config.js
└── vite.config.js
```

---

## Database Design

### Core Tables & Relationships

```
families
  ├── id, name
  ├── → users (has many — leader accounts)
  ├── → members (has many — youth members)
  ├── → weekly_meetings (has many)
  └── → reports (has many)

users
  ├── id, name, phone, password, role (admin|leader), family_id, report_pin, fcm_token
  └── → family (belongs to)

members
  ├── id, name, phone, family_id, is_active, birth_date, job_or_college,
  │   confession_father, talents, photo_path
  └── → tatmim_records (has many)

stages
  ├── id, name, order_number
  └── → lessons (has many)

lessons
  ├── id, stage_id, title, order_number
  └── → lesson_resources (has many)

lesson_resources
  └── id, lesson_id, user_id, description, file_path, type (file|text)

weekly_meetings
  ├── id, family_id, week_date, status (pending|completed|cancelled),
  │   lesson_id, custom_topic, training_text, max_note_score
  └── → tatmim_records (has many)

tatmim_records
  └── id, weekly_meeting_id, member_id,
      is_present, note_score,
      has_mass, has_servants_meeting, has_tasbeha, has_vespers,
      has_reading, has_family_altar, has_weekly_kholwa, has_sermon,
      kholwa_count, talmaza_training_count

reports
  └── id, family_id, type (weekly|monthly), report_date,
      timeline (JSON), weekly_achievements (JSON),
      visitation_hours, session_time, session_replies (JSON),
      monthly_summary (JSON), members_notes (JSON),
      stats_snapshot (JSON), stats_replies (JSON),
      priest_message (JSON),
      admin_reply_at

announcements
  └── id, user_id, title, content, attachment,
      type (announcement|decision), status, admin_comment
```

### Key Relationships

| Model | Relationship | Target |
|---|---|---|
| `Family` | `hasMany` | `User`, `Member`, `WeeklyMeeting`, `Report` |
| `Member` | `belongsTo` | `Family` |
| `Member` | `hasMany` | `TatmimRecord` |
| `WeeklyMeeting` | `belongsTo` | `Family`, `Lesson` |
| `WeeklyMeeting` | `hasMany` | `TatmimRecord` |
| `TatmimRecord` | `belongsTo` | `WeeklyMeeting`, `Member` |
| `Report` | `belongsTo` | `Family` |
| `Lesson` | `belongsTo` | `Stage` |
| `Lesson` | `hasMany` | `LessonResource` |

---

## Roles & Authorization

The system uses a simple `role` column on the `users` table with two values:

| Role | Access |
|---|---|
| `admin` | Full access to all families, reports, decisions, announcements; can create families/leaders; read-only on any tatmim |
| `leader` | Scoped to their own family only; can record tatmim, manage members, submit reports |

**`EnsureAdmin` middleware** protects all `/admin/*` routes. Additionally, every Livewire component performs an ownership check in its `mount()` method — e.g., a leader cannot access another family's meeting or report.

Tatmim records become **read-only** automatically after 4 weeks from creation, preventing retroactive edits.

---

## Firebase Push Notifications

Push notifications are sent via **Firebase Cloud Messaging (FCM)** using the `kreait/laravel-firebase` package.

### Notification Events

| Event | Recipients |
|---|---|
| New weekly/monthly report submitted | All admin users |
| Admin replies to a report | The submitting family's leader(s) |
| Leader adds a follow-up comment | All admin users |
| New Tatmim (meeting record) submitted | All admin users |
| New announcement posted | All users (except poster) |
| New decision posted | All users |
| Decision status updated | All users |

### Web Push Clicking

Each notification includes a `WebPushConfig` with an `fcm_options.link` that deep-links the user directly to the relevant report, meeting, or board when the notification is clicked.

### FCM Token Storage

Each authenticated user stores their browser's FCM token in `users.fcm_token`. The token is saved via a dedicated POST endpoint:

```
POST /save-fcm-token
Body: { token: "<fcm_token>" }
```

---

## Architecture Notes

### Livewire-First Design

Every page in the application is a full-page Livewire component. There are no traditional AJAX controllers for UI — all interactivity happens through Livewire's server-side state management. This keeps all business logic server-side and eliminates the need for a separate API layer.

### Auto-Save in RecordTatmim

The `RecordTatmim` component uses Livewire's `updated()` lifecycle hook to persist individual field changes to the database in real time. This means leaders never lose progress if the browser is closed mid-session.

### JSON Columns for Structured Report Sections

Report fields like `timeline`, `weekly_achievements`, `monthly_summary`, `members_notes`, and `priest_message` are stored as JSON arrays. Each item in the array is a structured object containing the text and a `replies` array, allowing inline threaded discussions between admin and leader on any specific section of a report.

### Stats Snapshot

When a monthly report is saved, the system calculates the complete stats for that month from tatmim records and stores a `stats_snapshot` JSON column. This preserves a point-in-time view of family performance even if tatmim data changes later.

### TatmimStatsService

The `App\Services\TatmimStatsService` class centralizes all metric calculation logic (converting raw tatmim booleans and counts to normalized 0–100 percentage scores). This service is injected into Livewire components via method injection, keeping the calculation logic reusable and testable.

### Lesson Auto-Suggestion

When a leader opens a new meeting, `RecordTatmim::suggestNextLesson()` automatically finds the next lesson in curriculum order based on the last completed meeting — streamlining lesson selection and keeping families on track.

### Queue for Background Jobs

The `QUEUE_CONNECTION=database` driver handles any deferred operations. Run `php artisan queue:listen` alongside the web server during development.

---

## Future Improvements

- **SMS Authentication** — Replace phone/password login with OTP-based SMS verification for a smoother onboarding experience
- **Mobile Application** — Expose a REST API or use Livewire Volt's SPA capabilities to build companion iOS/Android apps
- **Advanced Analytics Dashboard** — Trend charts, family comparison leaderboards, and stage progression heatmaps
- **Email Notifications** — Supplement FCM notifications with email digests for admins
- **Curriculum Management UI** — Allow admins to create and reorder stages and lessons from within the app instead of seeding them
- **Automated Absent Alerts** — Use the existing `MemberAbsentAlert` notification and a scheduled command to automatically alert leaders about members with consecutive absences
- **Export to PDF/Excel** — Allow report and stats exports for offline sharing and archiving
- **Multi-Language Support** — Formalize Arabic RTL support via Laravel's localization and add English strings

---

## Contributing

1. Fork the repository and create a feature branch: `git checkout -b feature/your-feature`
2. Follow PSR-12 coding standards. Run `./vendor/bin/pint` before committing
3. Write or update PHPUnit tests as appropriate. Run `composer test` to verify
4. Open a Pull Request with a clear description of the change and its motivation

---

## License

This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).
