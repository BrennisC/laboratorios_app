# VulnShop PHP - OWASP Top 10 2017 Lab

VulnShop is a deliberately vulnerable PHP product store for local security practice.

Do not deploy this application to the internet. It intentionally contains critical vulnerabilities.

## Requirements

- PHP 8+
- PostgreSQL 14+
- PHP PostgreSQL extensions enabled: `pdo_pgsql` and `pgsql`

## PostgreSQL Configuration

Default lab values used by the app:

- Database: `vulnshop_db`
- User: `web_app`
- Password: `secureshop_pass123`
- Host: `127.0.0.1`
- Port: `5432`

### Ubuntu

```bash
sudo apt update
sudo apt install postgresql php php-pgsql
```

Create the database, user, tables and demo data:

```bash
cd "web vulnerable"
sudo -u postgres psql -f postgresql_setup.sql
```

### Windows

1. Install PostgreSQL from `https://www.postgresql.org/download/windows/`.
2. Enable PostgreSQL extensions in `php.ini`:

```ini
extension=pdo_pgsql
extension=pgsql
```

3. Restart IIS or your PHP process.
4. Run the setup script from PowerShell:

```powershell
cd "C:\UNAS FIIS\PRACTICAS\webs_practica\web vulnerable"
psql -U postgres -f .\postgresql_setup.sql
```

If `psql` is not recognized, add PostgreSQL's `bin` folder to `PATH`, for example `C:\Program Files\PostgreSQL\16\bin`.

## IIS On Windows

1. Install IIS with CGI support: `Windows Features > Internet Information Services > World Wide Web Services > Application Development Features > CGI`.
2. Install PHP for Windows and configure IIS FastCGI to use `php-cgi.exe`.
3. Enable `pdo_pgsql` and `pgsql` in `php.ini`.
4. Create an IIS site pointing to this folder: `web vulnerable`.
5. Set the site port, for example `8000`, and open `http://localhost:8000`.

The IIS application pool identity needs read access to the project folder and write access to `data` and `uploads` if those folders are used.

Manual PostgreSQL user/database values:

```sql
CREATE DATABASE vulnshop_db;
CREATE ROLE web_app LOGIN PASSWORD 'secureshop_pass123';
GRANT ALL PRIVILEGES ON DATABASE vulnshop_db TO web_app;
```

The SQL script creates the tables and initial products. The app also checks missing seed data on first load.

## Run Locally

```bash
cd "web vulnerable"
php -S 127.0.0.1:8000
```

Open: `http://127.0.0.1:8000`

The database must exist first. If you used `postgresql_setup.sql`, tables and demo products are already loaded.

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
