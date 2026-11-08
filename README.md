# 📚 Smart Study Group Management System

A web-based platform that helps students create study groups, collaborate with peers, and manage tasks in one place instead of scattering them across chat apps.

Built as an IP project with **HTML, CSS, PHP, MySQL and Java**.

## Features

- **User authentication:** register, login, logout, forgot password (recovery answer)
- **Group management:** create, join, leave and delete groups, search by name or subject
- **Task management:** leader adds, edits and deletes tasks with deadlines; members toggle status (Pending / Completed)
- **Dashboard:** total, joined and led groups, pending and completed tasks, "Due Soon" reminders with overdue highlight
- **Group page:** progress bar, notice board (leader posts) and group chat
- **Member management:** leader can remove members
- **Java admin panel:** desktop app (Swing + JDBC) to view groups, tasks and users, delete groups and toggle task status

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML, CSS |
| Backend | PHP (PDO) |
| Database | MySQL |
| Desktop tool | Java (Swing, JDBC) |
| Server | XAMPP (Apache + MySQL) |

## How to Run

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**.
2. Copy this folder to `C:\xampp\htdocs\smart_study`.
3. Open `http://localhost/phpmyadmin` and create a database named `smart_study` (collation `utf8mb4_general_ci`).
4. Create the 6 tables listed under **Database Tables** below, with the foreign keys shown there. The full SQL is in `schema.sql` for reference (you can also run it in the **SQL** tab).
5. If your MySQL root user has a password, set it in `config.php`.
6. Open `http://localhost/smart_study/` in your browser, register an account and log in.

## PHP Concepts Used

| Concept | File(s) | What it does |
|---------|---------|--------------|
| Database connection (PDO) | `config.php` | Connects PHP to MySQL |
| Sessions | `config.php`, `index.php`, `logout.php` | Remembers the logged-in user, cleared on logout |
| Page protection | `auth()` in `config.php` | Redirects to login if the user is not logged in |
| Password hashing | `register.php`, `index.php`, `forgot.php` | `password_hash()` to store, `password_verify()` to check |
| Prepared statements | all pages | Prevents SQL injection |
| Form handling | `$_POST`, `$_GET` | Group search, group select, create and update forms |
| CRUD operations | `groups.php`, `tasks.php`, `members.php` | Create, read, update and delete groups, tasks, notices and members |
| Reusable layout (`include`) | `header.php`, `footer.php` | Navbar and footer written once, used on every page |
| XSS protection | `e()` in `config.php` | `htmlspecialchars()` on all user-entered output |
| Redirects | `header("Location: ...")` | Sends the user to another page after an action |
| Permission checks | `tasks.php`, `members.php`, `group.php` | Only the group leader can edit tasks, remove members and post notices |
| SQL JOINs and subqueries | `groups.php`, `dashboard.php`, `group.php` | Member counts, task counts and progress in one query |

## Running the Java Admin Panel

1. Install the JDK and download [MySQL Connector/J](https://dev.mysql.com/downloads/connector/j/) (Platform Independent).
2. Put the connector `.jar` file in the `java` folder.
3. In that folder run:

```
javac StudyGroupAdmin.java
java -cp ".;mysql-connector-j-x.x.x.jar" StudyGroupAdmin
```

(Replace `x.x.x` with your jar version. On Linux/Mac use `:` instead of `;`.)

## Database Tables

- `users`: id, name, email, password (hashed), security_answer (hashed)
- `study_groups`: id, name, subject, leader_id
- `group_members`: group_id, user_id
- `tasks`: id, group_id, title, deadline, status
- `notices`: id, group_id, user_id, message, created_at
- `messages`: id, group_id, user_id, message, created_at

### Foreign Keys (all `ON DELETE CASCADE`)

| Table | Column | References |
|-------|--------|-----------|
| study_groups | leader_id | users.id |
| group_members | group_id | study_groups.id |
| group_members | user_id | users.id |
| tasks | group_id | study_groups.id |
| notices | group_id | study_groups.id |
| notices | user_id | users.id |
| messages | group_id | study_groups.id |
| messages | user_id | users.id |

Primary keys: `id` (auto increment) in every table except `group_members`, which uses (`group_id`, `user_id`). `users.email` is UNIQUE.

## Project Structure

```
smart_study/
├── index.php        # login
├── register.php     # sign up
├── forgot.php       # reset password
├── dashboard.php    # overview and due soon
├── groups.php       # list, create, join, search
├── group.php        # progress, notices, chat
├── members.php      # member list and remove
├── tasks.php        # task management
├── config.php       # database connection
├── schema.sql       # database setup
├── style.css
└── java/StudyGroupAdmin.java
```

## Notes

- Forgot password works only for accounts registered with a recovery answer.
- Notices and chat refresh when the page is reloaded (no JavaScript used).



Your Name, Your University / Department
