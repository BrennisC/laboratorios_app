# SecureShop PHP - OWASP Top 10 2017 Defenses

SecureShop is the safe counterpart to `web vulnerable`. It implements the same basic product-store idea with defensive coding practices.

## Requirements

- Ubuntu
- PHP 8+
- PostgreSQL 14+
- PHP PostgreSQL extensions enabled: `pdo_pgsql` and `pgsql`

## PostgreSQL Configuration

Default local lab values:

- Database: `secureshop_db`
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
cd "web seguro"
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
cd "C:\UNAS FIIS\PRACTICAS\webs_practica\web seguro"
psql -U postgres -f .\postgresql_setup.sql
```

If `psql` is not recognized, add PostgreSQL's `bin` folder to `PATH`, for example `C:\Program Files\PostgreSQL\16\bin`.

## IIS On Windows

1. Install IIS with CGI support: `Windows Features > Internet Information Services > World Wide Web Services > Application Development Features > CGI`.
2. Install PHP for Windows and configure IIS FastCGI to use `php-cgi.exe`.
3. Enable `pdo_pgsql` and `pgsql` in `php.ini`.
4. Create an IIS site pointing to this folder: `web seguro`.
5. Set the site port, for example `8080`, and open `http://localhost:8080`.

The IIS application pool identity needs read access to the project folder.

Run locally:

```bash
php -S 127.0.0.1:8080
```

Open: `http://127.0.0.1:8080`

## Demo Accounts

- Admin: `admin@secureshop.local` / `AdminPass123!`
- User: `user@secureshop.local` / `UserPass123!`

The SQL script inserts placeholder hashes for demo users. On first app load, PHP replaces them with real `password_hash()` values.

## OWASP Top 10 2017 Defense Map

| OWASP 2017 | Defense | Where |
| --- | --- | --- |
| A1 Injection | Prepared statements and typed input validation | `lib/db.php`, all queries |
| A2 Broken Authentication | `password_hash`, `password_verify`, session regeneration, login throttling | `login.php`, `lib/security.php` |
| A3 Sensitive Data Exposure | No plaintext secrets in pages, masked cards, secure headers | `config.php`, `profile.php`, `includes/header.php` |
| A4 XML External Entities | XML parser disables network/entity expansion | `import_xml.php` |
| A5 Broken Access Control | Server-side role checks and object ownership checks | `admin.php`, `profile.php`, `orders.php` |
| A6 Security Misconfiguration | Errors not displayed, no debug endpoint, restrictive headers | `config.php`, `includes/header.php` |
| A7 Cross-Site Scripting | Output encoding with `e()` | `lib/security.php`, views |
| A8 Insecure Deserialization | No `unserialize()` on user input; JSON allow-list parsing | `import_json.php` |
| A9 Known Vulnerable Components | No old third-party JS dependency | `includes/header.php` |
| A10 Insufficient Logging & Monitoring | Security events logged to DB | `lib/security.php`, `security_logs.php` |
