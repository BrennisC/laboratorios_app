# VulnShop PHP - OWASP Top 10 2017 Lab

VulnShop is a deliberately vulnerable PHP product store for local security practice.

Do not deploy this application to the internet. It intentionally contains critical vulnerabilities.

## Requirements

- PHP 8+
- MySQL Server 8+ or MariaDB
- PHP MySQL extension enabled

## MySQL Configuration For Ubuntu

Default lab values used by the app:

- Database: `vulnshop_db`
- User: `vulnshop_user`
- Password: `vulnshop_pass123`
- Host: `127.0.0.1`
- Port: `3306`

Install dependencies:

```bash
sudo apt update
sudo apt install mysql-server php php-mysql
```

Create the database, user, tables and demo data:

```bash
sudo mysql < mysql_setup.sql
```

Or manually:

```sql
CREATE DATABASE IF NOT EXISTS vulnshop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'vulnshop_user'@'localhost' IDENTIFIED BY 'vulnshop_pass123';
CREATE USER IF NOT EXISTS 'vulnshop_user'@'127.0.0.1' IDENTIFIED BY 'vulnshop_pass123';
GRANT ALL PRIVILEGES ON vulnshop_db.* TO 'vulnshop_user'@'localhost';
GRANT ALL PRIVILEGES ON vulnshop_db.* TO 'vulnshop_user'@'127.0.0.1';
FLUSH PRIVILEGES;
```

The SQL script creates the tables and initial products. The app also checks missing seed data on first load.

## Run Locally

```bash
cd "web vulnerable"
php -S 127.0.0.1:8000
```

Open: `http://127.0.0.1:8000`

The database must exist first. If you used `mysql_setup.sql`, tables and demo products are already loaded.

## Demo Accounts

- Admin: `admin@example.com` / `admin123`
- User: `user@example.com` / `user123`

## HackTheBox-Style Machine Model

This lab now behaves like a small retired HTB-style machine:

- `/machine.php` contains the machine briefing, difficulty and objectives.
- `/hints.php` contains progressive hints without giving full answers immediately.
- `/submit_flag.php` validates discovered flags.
- `robots.txt` and `/backup/users.sql.bak` provide enumeration paths.
- Flags are hidden behind vulnerable functionality instead of listed on the main page.

Flag stages:

- Recon flag: exposed by leaked debug information.
- User flag: exposed through insecure direct object reference.
- Admin flag: exposed through broken access control.
- Root flag: stored in the application data directory and intended to be read through XXE.

## OWASP Top 10 2017 Map

| OWASP 2017 | Vulnerability | Where |
| --- | --- | --- |
| A1 Injection | SQL queries concatenate user input | `login.php`, `search.php`, `product.php`, `admin.php` |
| A2 Broken Authentication | Plaintext passwords, weak session handling, user id override | `login.php`, `profile.php` |
| A3 Sensitive Data Exposure | Debug output, exposed secrets and plaintext passwords | `config.php`, `debug.php`, `profile.php` |
| A4 XML External Entities | XML import enables external entities | `import_xml.php` |
| A5 Broken Access Control | Admin actions trust request parameters | `admin.php`, `profile.php`, `orders.php` |
| A6 Security Misconfiguration | Debug enabled, errors displayed, predictable paths | `config.php`, `debug.php` |
| A7 Cross-Site Scripting | Reflected and stored XSS | `search.php`, `product.php`, `admin.php` |
| A8 Insecure Deserialization | User-controlled serialized object | `deserialize.php` |
| A9 Known Vulnerable Components | Old jQuery included from CDN | `includes/header.php` |
| A10 Insufficient Logging & Monitoring | Security events are not logged | `login.php`, `admin.php`, `checkout.php` |

## Suggested Exercises

- Bypass login with SQL injection.
- Read another user's profile by changing `id` in the URL.
- Add a product review containing JavaScript.
- Trigger XXE through the XML importer.
- Abuse deserialization to write a file.
- Access admin functionality without being an admin.
