# Leave Management System

### ICS Travel Group

A web-based leave management system built for internal use at ICS Travel Group. It handles the full leave lifecycle — from employee submissions to admin approvals — including balance tracking, compensate leave claims, public holidays, and team calendar visibility.

Built with plain PHP and MySQL, no framework dependency.

---

## Features

**For Employees**

- Submit leave requests with an interactive calendar (date range picker, team visibility, holiday awareness)
- View remaining balance per leave type in real time
- Track request history with status updates
- Claim compensate leave from overtime records
- Cancel pending requests
- View and update personal profile

**For Admins**

- Approve or reject leave requests with optional rejection reason
- Manage employees, departments, job titles, and work schedules
- Create leave periods and generate annual leave balances in bulk
- Manually grant event-based leave (marriage, maternity, paternity, bereavement)
- Adjust individual leave balances with audit trail
- Manage public holidays
- View team calendar
- Reset employee passwords

---

## Tech Stack

- **Backend:** PHP 8.x (no framework)
- **Database:** MySQL / MariaDB
- **Frontend:** Vanilla HTML, CSS, JavaScript
- **Local Server:** XAMPP (Apache + MySQL)
- **Routing:** `.htaccess` rewrite rules

---

## Requirements

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.4+
- Apache with `mod_rewrite` enabled
- PHP extensions: `PDO`, `PDO_MySQL`, `mbstring`
- XAMPP (recommended for local development)

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/yralkautsar/leave-system
```

Place the project folder inside your XAMPP `htdocs` directory:

```
C:/xampp/htdocs/leave-system/
```

### 2. Start XAMPP

Open XAMPP Control Panel and start both **Apache** and **MySQL**.

### 3. Create the database

Open phpMyAdmin at `http://localhost/phpmyadmin`, then create a new database:

```sql
CREATE DATABASE leave_system_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Run the migrations

Go to phpMyAdmin, select your database, open the **SQL** tab, and run the migration files **in this exact order**:

| Order | File                             | Description                             |
| ----- | -------------------------------- | --------------------------------------- |
| 1     | `migration_rejection_reason.sql` | Adds rejection reason to leave requests |
| 2     | `migration_approved_by.sql`      | Tracks who approved each request        |
| 3     | `migration_hod_gm.sql`           | HOD and GM approval roles               |
| 4     | `migration_settings.sql`         | System settings table                   |
| 5     | `migration_password_resets.sql`  | Password reset tokens                   |
| 6     | `migration_comp_claims.sql`      | Compensate leave claims table           |
| 7     | `migration_balance_source.sql`   | Balance source architecture (run last)  |

> **Important:** Migration 7 depends on tables created in Migration 6. Do not change the order.

### 5. Configure the database connection

Open `config/database.php` and update with your local credentials:

```php
return [
    'host'     => '127.0.0.1',
    'dbname'   => 'leave_system_dev',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4'
];
```

### 6. Enable Apache mod_rewrite

Make sure `mod_rewrite` is enabled in your XAMPP Apache config, and that `.htaccess` is allowed in the `httpd.conf`:

```
AllowOverride All
```

### 7. Access the system

Open your browser and go to:

```
http://localhost/leave-system/public/
```

---

## Default Login

After running the migrations, create your first admin account directly in the database:

```sql
INSERT INTO users (name, email, password_hash, role, is_active, created_at)
VALUES (
    'Admin HR',
    'admin@ics.com',
    '$2y$10$yourhashedpasswordhere',
    'admin_approver',
    1,
    NOW()
);
```

To generate a `password_hash`, run this in PHP:

```php
echo password_hash('your_password', PASSWORD_DEFAULT);
```

---

## Project Structure

```
leave-system/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── LeaveController.php
│   │   └── CalendarController.php
│   ├── Models/
│   │   └── User.php
│   └── Services/
│       ├── Database.php
│       ├── LeaveCalculator.php
│       ├── LeaveBalanceGenerator.php
│       └── MailService.php
├── config/
│   ├── database.php
│   └── mail.php
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── assets/
│       └── css/
│           └── admin.css
├── resources/
│   └── views/
│       ├── layout.php
│       ├── login.php
│       ├── dashboard_admin.php
│       ├── dashboard_employee.php
│       ├── leave_create.php
│       ├── leave_history.php
│       ├── calendar.php
│       ├── admin_users.php
│       ├── admin_requests.php
│       ├── admin_balances.php
│       ├── admin_periods.php
│       ├── admin_leave_types.php
│       ├── admin_leave_grants.php
│       ├── admin_comp_claims.php
│       ├── admin_holidays.php
│       ├── admin_departments.php
│       ├── admin_jobtitles.php
│       └── admin_settings.php
└── migrations/
    ├── migration_rejection_reason.sql
    ├── migration_approved_by.sql
    ├── migration_hod_gm.sql
    ├── migration_settings.sql
    ├── migration_password_resets.sql
    ├── migration_comp_claims.sql
    └── migration_balance_source.sql
```

---

## Usage Guide

Here is the recommended workflow when setting up for real use:

**Step 1 — Master data first**
Before adding any employees, make sure the following are configured:

- Departments (`Admin > Departments`)
- Job Titles (`Admin > Job Titles`)
- Work Schedules (directly in database for now)
- Leave Types (`Admin > Leave Types`) — check that each type has the correct balance source
- Public Holidays (`Admin > Public Holidays`)

**Step 2 — Add employees**
Go to `Admin > Users` and add each employee. Make sure join date and probation end date are correct, as these affect balance eligibility.

**Step 3 — Create a leave period**
Go to `Admin > Leave Periods` and create a period (e.g. `2025 — Jan 2025 to Dec 2025`).

**Step 4 — Generate balances**
On the Leave Periods page, click `Generate Balance` for your period. The system will automatically skip employees still in probation and skip leave types that are not period-based (sick leave, compensate leave, etc.).

**Step 5 — Employees can now submit leave**
Once balances are generated, employees can log in and start submitting leave requests through the interactive calendar.

**Step 6 — Admin approves or rejects**
Pending requests appear on the admin dashboard. Admin can approve (which deducts balance) or reject with a reason. Employees will see the updated status on their dashboard.

---

## Leave Balance Sources

The system handles four types of leave balance, each with different logic:

| Source        | Example Leave Types                         | How Balance Works                                                  |
| ------------- | ------------------------------------------- | ------------------------------------------------------------------ |
| `period`      | Annual Leave                                | Generated per period, deducted on approval                         |
| `unlimited`   | Sick Leave                                  | No quota, usage is recorded for reporting only                     |
| `comp`        | Compensate Leave                            | Sourced from approved comp claims (FIFO), expires after set period |
| `admin_grant` | Marriage, Maternity, Paternity, Bereavement | Manually granted by admin per employee                             |

---

## Contributing

If you are working on this project as part of the ICS internal team:

- Create a new branch from `main` using the format `feature/short-description` or `fix/short-description`
- Keep pull requests focused — one feature or fix per PR
- Test locally with XAMPP before pushing
- Do not commit `config/database.php` with real credentials — use the placeholder values

---

## License

Internal use only. All rights reserved by ICS Travel Group.
