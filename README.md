# Event Management System

## Project Overview

The goal of this project is to develop a simple, web-based event
management system that allows users to create, manage, and view events,
as well as register attendees and generate event reports.

This system uses a combination of **PHP**, **MySQL**, **Js**, **HTML**, **CSS**, **Bootstrap**, **AJAX** etc.

---

## Info

### Login Credentials

#### Account 
  1. Admin Account: 
      - Email: admin@example.com
      - Password: admin12345

  2. User Account: 
      - Email: doe@example.com
      - Password: doe12345

### DB Design
- **Link:** [Database Design Link](https://drive.google.com/file/d/1WH-RAKDWcwBPLAsbOfweRORfyFRVb4vT/view?usp=drive_link)

### Demo Video



### Screenshots


---

## Features & Details

## 1. Registration and Authentication

### User Registration
- Allows users to register with a unique email and password.
- Passwords are securely hashed using `password_hash()` before storing in the database.
- Client-side and server-side validation for email, password, and other fields.

### User Login
- Secure authentication using sessions.
- Passwords are verified using `password_verify()`.
- Users are redirected to the dashboard after successful login.

### User Roles
- **Admin**: Full access to all features.
- **User**: Limited access (can only edit/delete their own events).

### AJAX Enhancements
- AJAX is used for seamless form submissions (e.g., login, registration, event registration).
- Real-time validation and feedback for better user experience.

### Security
- Prepared statements are used to prevent SQL injection.

---

## 2. Dashboard

### Access Control
- Only logged-in users can access the dashboard.
- **Role-based access**:
  - Admins can view and manage all features.
  - Users can only view and manage their own events.

### Features
- Admins can view all events, users, and reports.
- Users can view their registered events and create new events.

---

## 3. Profile

### User Details
- Displays user information (e.g., name, email, role).

---

## 4. User Management

### Admin Access Only
- Admins can view a list of all users.

### Features
- Live search for users.
- Sorting and pagination.
- Ability to delete users.

---

## 5. Event Management

### Admin Features
- Create, update, and delete any event.
- View all events in a table with live search, sorting, and pagination.

### User Features
- Create events.
- View all events but can only edit/delete their own events.

### Validation
- Both client-side and server-side validation for event creation and updates.

---

## 6. Event Report

### Admin Access Only
- Admins can generate reports for specific events.

### Features
- View event details and a list of attendees.
- Live search, sorting, and pagination for attendees.
- Option to delete attendees.
- Download attendee list as a CSV file.

---

## 7. Attendee Registration

### Authenticated Users
- Users can search for events with available seats.
- Event details are displayed, including available seats.
- Users cannot register for the same event more than once.

### Registration Process
- Users provide their name, email, and registration type (self or others).
- AJAX is used for seamless registration.

---

## 8. API for Fetching Event

### Endpoint
```
GET /ems/api/events/{id}
```

### Description
Fetches an event with details.

### Parameters
- `id`: Pass the ID of an event to fetch details of that event.



---

## Setup Instructions

### Prerequisites

1. **PHP (7.4 or higher)**
2. **MySQL or MariaDB**
3. **Web Server (Apache/Nginx)** with support for PHP
4. **Composer** (for optional PHP dependencies)
5. **Node.js & npm** (for frontend package management, optional)
6. **Git** (For cloning the project)

---

### Step-by-Step Setup

#### 1. Clone the Project
Clone the repository to your local machine:

```bash
git clone <repository-url>
```
Replace `<repository-url>` with the actual URL of your Git repository.

#### 2. Move to the Project Directory
Navigate to the project folder:

```bash
cd <project-folder>
```
Replace `<project-folder>` with the name of your project directory.

#### 3. Configure the Database
Open the `config/database.php` file.

Update the database configuration with the following details:

```php

$dsn = 'mysql:host=127.0.0.1;dbname=ems_db';
$username = 'root';
$password = '';

```

#### 4. Start the Web Server
##### Using XAMPP:
1. Open the **XAMPP Control Panel**.
2. Start **Apache** and **MySQL**.

##### Using WAMP:
1. Open the **WAMP Manager**.
2. Start **Apache** and **MySQL**.

##### Using PHP Built-in Server:
Run the following command in the project root directory:

```bash
php -S localhost:8000
```

#### 5. Import the Database
Open **phpMyAdmin** in your browser:

- **For XAMPP:** [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
- **For WAMP:** [http://localhost/phpmyadmin](http://localhost/phpmyadmin)

##### Steps:
1. Create a new database named **`ems_db`**.
2. Import the provided SQL file (`ems_db.sql`) into the **`ems_db`** database:
   - Go to the **Import** tab.
   - Choose the `ems_db.sql` file from the project root directory.
   - Click **Go** to import the database.

#### 6. Test the Application
Open your web browser and navigate to the project URL:

- **If using XAMPP/WAMP:** [http://localhost/ems/] or [https://127.0.0.1/ems/] (http://localhost/ems/)
- **If using PHP built-in server:** [http://localhost:8000/ems/](http://localhost:8000/ems/)

#### 7. Troubleshooting
##### Database Connection Issues:
- Ensure the database credentials in `config/database.php` are correct.
- Verify that **MySQL** is running.

##### File Permissions:
- Ensure the `logs` and `uploads` directories (if any) have write permissions.

##### 404 Errors:
- Check if the `.htaccess` file is properly configured (if using Apache).
- Ensure the `mod_rewrite` module is enabled in Apache.

#### 8. Additional Notes
##### Environment Variables:
- If your project uses environment variables, ensure the `.env` file is properly configured.

##### Composer Dependencies:
- If the project uses Composer, run the following command to install dependencies:

```bash
composer install
```

##### Debugging:
Enable error reporting in PHP for debugging:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

---









