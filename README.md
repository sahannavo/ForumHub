<div align="center">

# 💬 ForumHub
### *A Lightweight, Community-Driven Forum & Discussion Platform*

[![PHP](https://img.shields.io/badge/PHP-7.4%2B%20%7C%208.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![CSS3](https://img.shields.io/badge/CSS3-Neumorphic%20UI-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://www.w3.org/Style/CSS/)
[![License](https://img.shields.io/badge/License-Academic-blue?style=for-the-badge)]()

<br>

<p align="center">
  <b>ForumHub</b> is an interactive web-based community forum built with <b>PHP</b> and <b>MySQL</b>. Designed to deliver clean discussions across multiple interest domains (Programming, Gaming, Education, Careers, Tech & Science), it features secure authentication, categorized threads, interactive comments, thread reactions, and moderation tools.
</p>

[Explore Features](#-key-features) •
[UI Showcase](#-ui-showcase) •
[Database Architecture](#-database--system-design) •
[Getting Started](#-getting-started) •
[The Team](#-project-team--contributors)

---

</div>

<br>

## 📸 UI Showcase

<div align="center">

### 🏠 Community Feed & Thread Explorer
*Browse trending topics, filter by category, and search discussions in real-time.*

![Search & Explore](assets/Search%20Bar.png)

<br>

### 🔐 Authentication (Soft Neumorphic Interface)
| User Login | User Registration |
|:---:|:---:|
| <img src="assets/Login%20Page.png" width="380" alt="Login Page"/> | <img src="assets/Signup%20page.png" width="380" alt="Signup Page"/> |

<br>

### 💬 Discussion & Moderation Flow
| Dynamic Comment Thread | Edit Discussion Thread |
|:---:|:---:|
| <img src="assets/Comment%20Section.png" width="460" alt="Comment Section"/> | <img src="assets/Thread%20Editing.png" width="460" alt="Thread Editing"/> |

| Inline Comment Editing | About Community Page |
|:---:|:---:|
| <img src="assets/Comment%20Editing.png" width="460" alt="Comment Editing"/> | <img src="assets/About%20Us%20-%20page.png" width="460" alt="About Community"/> |

</div>

---

## ✨ Key Features

- 👤 **Secure Authentication & Sessions:** User sign up, password hashing via PHP `password_hash()`, validated sign-in sessions, and logout flows.
- 📂 **Categorized Discussions:** Discover and organize threads across specialized tags:
  - `Programming` • `Gaming` • `Education` • `Careers` • `Technology` • `Science`
- 🔍 **Live Search & Discovery:** Quickly find relevant topics and questions by keyword search.
- 📝 **Full Thread Lifecycle:**
  - Create rich discussion threads.
  - Author edit and update capabilities.
  - Safe deletion handling.
- 💬 **Interactive Commenting:** Nested replies to discussions, user attribution, and comment editing/deletion.
- ❤️ **Thread Reactions:** Real-time like and unlike tracking with duplicate prevention per user.
- 📊 **Community Insights:** Live sidebar metrics tracking active member counts, total discussions, and replies.

---

## 🏛 Database & System Design

ForumHub follows a classic **3-Tier Web Architecture**:
- **Presentation Layer:** Semantic HTML5, Neumorphic-inspired CSS, responsive DOM interaction with JavaScript.
- **Application Logic:** Native PHP scripts handling authentication, form validation, and query dispatching.
- **Data Persistence:** Relational MySQL database adhering to normalization rules.

### Entity Relationship Overview

```mermaid
erDiagram
    USERS ||--o{ THREADS : "creates (1:N)"
    USERS ||--o{ COMMENTS : "posts (1:N)"
    USERS ||--o{ THREAD_LIKES : "likes (1:N)"
    THREADS ||--o{ COMMENTS : "contains (1:N)"
    THREADS ||--o{ THREAD_LIKES : "receives (1:N)"

    USERS {
        int id PK
        string username
        string email
        string password
        string first_name
        string last_name
        int age
        timestamp created_at
    }

    THREADS {
        int id PK
        int user_id FK
        string title
        string category
        text content
        timestamp created_at
    }

    COMMENTS {
        int id PK
        int thread_id FK
        int user_id FK
        text content
        timestamp created_at
    }

    THREAD_LIKES {
        int id PK
        int thread_id FK
        int user_id FK
        timestamp created_at
    }
```

---

## 🗂 Project Structure

```text
ForumHub/
├── assets/                 # UI screenshots and project media
│   ├── About Us - page.png
│   ├── Comment Editing.png
│   ├── Comment Section.png
│   ├── Login Page.png
│   ├── Search Bar.png
│   ├── Signup page.png
│   └── Thread Editing.png
├── css/                    # Modular stylesheets
│   ├── comment_edite.css
│   ├── edit_thread.css
│   ├── index.css
│   └── thread.css
├── login_signup/           # Auth subsystem
│   ├── db.php
│   ├── login.php
│   ├── login_signup.css
│   ├── logout.php
│   ├── script.js
│   └── signup.php
├── php/                    # Backend API scripts & DB migrations
│   ├── create_comment.php
│   ├── create_thread.php
│   ├── db.php
│   ├── db.sql              # Database schema dump
│   ├── delete_comment.php
│   ├── delete_thread.php
│   ├── like_thread.php
│   └── unlike_thread.php
├── About_us.php            # Community About page
├── category.php            # Category-based thread listing
├── edit_comment.php        # Comment update interface
├── edit_thread.php         # Thread update interface
├── footer.php              # Global footer component
├── index.php               # Homepage & discussion feed
├── Right_Sidebar.php       # Community statistics sidebar
├── search.php              # Discussion search handler
├── thread.php              # Thread view & replies page
└── README.md
```

---

## 🚀 Getting Started

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) / [WampServer](https://www.wampserver.com/en/) / [LAMP stack](https://ubuntu.com/server/docs/lamp-applications)
- PHP `7.4` or `8.x`
- MySQL `5.7+` or MariaDB

### Local Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone https://github.com/sahannavo/ForumHub.git
   ```

2. **Move to Web Root:**
   Copy the `ForumHub` directory into your local server web root:
   - For XAMPP (Windows): `C:\xampp\htdocs\ForumHub`
   - For Linux (Apache): `/var/www/html/ForumHub`

3. **Import Database:**
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
   - Create a new database named `forumhub` (or match the name in `php/db.php`).
   - Import the [php/db.sql](php/db.sql) file.

4. **Configure Database Connection:**
   Verify connection credentials in `php/db.php` and `login_signup/db.php`:
   ```php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "forumhub";
   ```

5. **Run the Application:**
   Start Apache & MySQL from your server control panel, then visit:
   ```text
   http://localhost/ForumHub/index.php
   ```

---

## 👥 Project Team & Contributors

*Developed as part of the **SE102.3 – Web Application Development** module at the Faculty of Computing, **NSBM Green University** (Group B).*

| Student Name | Student ID | Core Contribution |
|:---|:---:|:---|
| **M.A.S.N. Weerasinghe** *(Team Leader)* | `35173` | Project Architecture, Homepage UI/Backend, DB Connection, Report Writing |
| **K.A.Y.N. Kuruppu** | `35117` | Thread View, Reply, Edit & Delete features |
| **U.G.D.S. Munasinghe** | `34702` | Category-based Thread Listing, Report Documentation |
| **R.M.O. Purwa** | `34657` | Thread Sorting, Filtering & Analysis |
| **U.M. Methvindi** | `35213` | Recent Comments Module, System UML & Architecture Diagrams |
| **K.M.C.A. Kumarasinghe** | `35464` | User Thread Creation, Presentation Deck, Documentation |
| **R.A.S. Piyathilake** | `35155` | Specific Thread Viewing with Nested Reply Engine |
| **D.V. Liyanage** | `35205` | Comment Editing Module, System Testing |
| **W.M.S.S. Warshakoon** | `34419` | Search Bar, Navigation Menu, Auth integration |
| **S.K.P. Arachchi** | `35229` | Search Bar, Navigation Menu, Signup & Login interface |

---

<div align="center">
  <sub>Built with passion for learning web development • NSBM Faculty of Computing</sub>
</div>
