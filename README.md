# Gestion des Absences - QR Code Attendance

Web application to manage student attendance in an engineering school. For each class
session, the administrator displays a QR code; students scan it with their phone to
check in, and when the session ends, every enrolled student who did not check in is
automatically marked absent.

## The Problem

Taking attendance by calling names or passing a sheet around takes time, is easy to
cheat on, and the results then have to be typed in by hand. This application turns
attendance into a two-step process: students check in themselves by scanning a QR code,
and absences are recorded automatically.

## How Attendance Works

```
Admin creates a session ──► unique token + QR code ──► students scan and log in
        ──► check-ins stored in presences_temp ──► admin ends the session
        ──► enrolled students without a check-in are recorded as absent
```

1. **Create a session** (`admin/generer_qr_view.php`): the admin picks a module, a date
   and a time slot. A random token is generated and stored with the session, and a QR
   code pointing to `etudiant/presence_direct.php?token=…` is displayed.
2. **Check in** (`etudiant/presence_direct.php`): the student scans the code. If not
   logged in, they log in first, then their check-in is recorded. A student cannot check
   in twice for the same session.
3. **End the session** (`admin/terminer_seance.php`): all students enrolled in the module
   who did not check in are inserted into the `absences` table.

## Features

**Administrator**

- Manage programs (filières), modules, teachers in charge and students
- Assign students to modules
- Create sessions with a QR code and end them
- Review absences and their status (absent, justified)
- Generate PDF absence reports per module (FPDF)

**Student**

- Register with a photo and choose modules
- View their absences
- Upload a justification document (PDF or image)

## Tech Stack

PHP 8, MySQL / MariaDB (PDO), Bootstrap 4, [endroid/qr-code](https://github.com/endroid/qr-code)
for QR codes, FPDF for PDF reports. Passwords are hashed with `password_hash` (bcrypt) and
all queries use prepared statements.

## Project Structure

```
├── index.php, register.php          Login (admin / student) and student registration
├── dashboard_admin.php              Admin dashboard
├── dashboard_etudiant.php           Student dashboard
├── admin/                           Admin pages (programs, modules, students, sessions, QR codes, reports)
├── etudiant/                        QR code check-in
├── gestion-absences/                PDF reports and justification upload
├── includes/                        Header, footer, authentication, FPDF
├── config/config.php                Database settings and application URL
└── sql/                             Database schema and demo data
```

## Installation (XAMPP)

1. Copy the project into `C:\xampp\htdocs\` (for example `C:\xampp\htdocs\gestion-absences`).
2. Install the PHP dependencies with [Composer](https://getcomposer.org):
   ```bash
   composer install
   ```
3. Start Apache and MySQL in XAMPP, open http://localhost/phpmyadmin, then import
   `sql/schema.sql` and `sql/demo_data.sql` (tab **Import**).
4. Check the database settings in `config/config.php` (in particular `DB_PORT`: XAMPP
   uses `3306` by default). The application path is detected automatically.
5. Open http://localhost/gestion-absences/

### Demo accounts

| Role          | Login                      | Password      |
| ------------- | -------------------------- | ------------- |
| Administrator | `admin`                    | ``            |
| Student       | `20250001` (Apogée number) | `etudiant123` |
| Student       | `20250002` (Apogée number) | `etudiant123` |

All demo data is fictitious.

### Scanning with a real phone

`localhost` only works on the computer itself. To scan the QR code with a phone on the
same Wi-Fi network, open the application with the computer's IP address
(for example `http://192.168.1.20/gestion-absences/`): the QR code then contains that
address and opens directly on the phone.

## Limitations and Next Steps

- A QR code can be shared (for example by sending a photo of it to an absent student).
  Possible improvements: a token that changes every few seconds, a check-in window
  limited to the session time slot, or location checks.
- Email notifications for absences are not implemented.
- No automated tests.

## Context

Academic project, ENSA Marrakech.
