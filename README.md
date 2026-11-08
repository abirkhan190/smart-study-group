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
3. Open `http://localhost/phpmyadmin`, go to **Import**, and import `schema.sql`.
4. If your MySQL root user has a password, set it in `config.php`.
5. Open `http://localhost/smart_study/` in your browser, register an account and log in.

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

## Author

Golam Irfan Khan,CSE
