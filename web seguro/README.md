# SecureShop v2 - Guia de Desarrollo y Despliegue Seguro

SecureShop v2 es la version sanitizada de `web vulnerable`. Mantiene el mismo contexto de tienda online, pero aplica controles defensivos y se despliega localmente con HTTPS/TLS mediante Docker y un certificado autofirmado generado con OpenSSL.

No es una aplicacion productiva real. Es una version segura para comparar contra la version vulnerable en un laboratorio local.

## Ruta Rapida con Docker

Desde la raiz del repositorio:

```bash
docker compose up --build secure_app secure_db
```

Abre:

```text
https://localhost:8443
```

El navegador mostrara una advertencia porque el certificado TLS es autofirmado. Para laboratorio local, acepta la excepcion manualmente.

## Arquitectura de Despliegue

```text
Browser
  |
  | HTTPS 8443
  v
secure_app
  PHP 8.2 + Apache + TLS
  DocumentRoot: /var/www/html
  |
  | PostgreSQL internal network
  v
secure_db
  PostgreSQL 16
  Database: secureshop_db
```

Servicios Docker:

| Servicio | Tecnologia | Puerto host | Proposito |
| --- | --- | --- | --- |
| `secure_app` | PHP 8.2 + Apache + OpenSSL | `8443:443`, `8080:80` | App segura v2 con HTTPS |
| `secure_db` | PostgreSQL 16 | `5434:5432` | Base de datos aislada de SecureShop |

El puerto `8080` redirige hacia `https://localhost:8443`.

## Tecnologias Usadas

| Tecnologia | Uso |
| --- | --- |
| PHP 8.2 | Backend web |
| Apache HTTP Server | Servidor web y terminacion TLS |
| OpenSSL | Generacion de certificado autofirmado |
| PostgreSQL 16 | Persistencia de usuarios, productos, ordenes y logs |
| Docker Compose | Orquestacion local de app + base de datos |
| PDO PostgreSQL | Acceso seguro a base de datos con prepared statements |

## Certificado SSL/TLS

El certificado se genera durante el build del contenedor en `Dockerfile`:

```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/apache2/ssl/secureshop.key \
  -out /etc/apache2/ssl/secureshop.crt \
  -subj "/C=PE/ST=Local/L=Lab/O=SecureShop/OU=Training/CN=localhost" \
  -addext "subjectAltName=DNS:localhost,IP:127.0.0.1"
```

La configuracion Apache esta en:

```text
web seguro/docker/apache-ssl.conf
```

Controles TLS/HTTP aplicados:

- HTTPS en puerto `8443`.
- Redireccion HTTP `8080` hacia HTTPS.
- `Strict-Transport-Security` cuando se usa HTTPS.
- `X-Frame-Options: DENY`.
- `X-Content-Type-Options: nosniff`.
- `Referrer-Policy: strict-origin-when-cross-origin`.
- `Content-Security-Policy` desde PHP.
- Cookies de sesion con `Secure`, `HttpOnly` y `SameSite=Lax` en Docker.

## Variables de Entorno

`config.php` usa variables de entorno cuando existen y conserva defaults para ejecucion local sin Docker.

| Variable | Docker | Default local |
| --- | --- | --- |
| `APP_ENV` | `docker-secure-v2` | `local` |
| `DB_HOST` | `secure_db` | `127.0.0.1` |
| `DB_PORT` | `5432` | `5432` |
| `DB_NAME` | `secureshop_db` | `secureshop_db` |
| `DB_USER` | `web_app` | `web_app` |
| `DB_PASS` | `secureshop_pass123` | `secureshop_pass123` |
| `SESSION_SECURE` | `true` | no definido |

## Cuentas Demo

| Rol | Email | Password |
| --- | --- | --- |
| Admin | `admin@secureshop.local` | `AdminPass123!` |
| Usuario | `user@secureshop.local` | `UserPass123!` |

El SQL inserta placeholders y la app los reemplaza con `password_hash()` en el primer acceso.

## Controles de Seguridad Implementados

| Area | Control aplicado | Archivos |
| --- | --- | --- |
| SQL Injection | Prepared statements y parametros tipados | `lib/db.php`, vistas con queries |
| Autenticacion | `password_hash`, `password_verify`, regeneracion de sesion, throttling | `login.php` |
| Autorizacion | `require_login`, `require_admin`, validacion de propiedad | `lib/security.php`, `profile.php`, `orders.php` |
| CSRF | Token por formulario sensible | `lib/security.php`, formularios |
| XSS | Escape centralizado con `e()` | `lib/security.php`, vistas |
| Upload | Validacion de extension, MIME, tamano y almacenamiento fuera de ejecucion PHP | `upload.php`, `storage/uploads` |
| RCE demo seguro | Comandos allowlist, sin input libre hacia shell | `rce.php` |
| XML | Parser sin entidades externas ni red | `import_xml.php` |
| JSON/import | Parsing allowlist, sin `unserialize()` | `import_json.php` |
| Logging | Eventos de seguridad en BD | `lib/security.php`, `security_logs.php` |
| TLS | HTTPS con certificado autofirmado local | `Dockerfile`, `docker/apache-ssl.conf` |

## OWASP Top 10 2025 Defense Map

| OWASP 2025 | Defensa | Donde |
| --- | --- | --- |
| A01 Broken Access Control | Checks server-side y propiedad de recursos | `lib/security.php`, `admin.php`, `profile.php`, `orders.php` |
| A02 Security Misconfiguration | Errores ocultos, headers seguros, TLS y directorios sin indexing | `config.php`, `includes/header.php`, `docker/apache-ssl.conf` |
| A03 Software Supply Chain Failures | Sin jQuery antiguo externo en la version segura | `includes/header.php` |
| A04 Cryptographic Failures | Hash de passwords, tarjetas enmascaradas, HTTPS | `lib/db.php`, `profile.php`, Docker TLS |
| A05 Injection | Prepared statements, validacion tipada, XML seguro | `login.php`, `search.php`, `product.php`, `import_xml.php` |
| A06 Insecure Design | Flujos administrativos restringidos y comandos allowlist | `admin.php`, `rce.php`, `upload.php` |
| A07 Identification and Authentication Failures | Rate limiting, session regeneration, cookies seguras | `login.php`, `config.php` |
| A08 Software or Data Integrity Failures | Upload validado y sin deserializacion insegura | `upload.php`, `import_json.php` |
| A09 Security Logging and Alerting Failures | Eventos de seguridad persistidos | `lib/security.php`, `security_logs.php` |
| A10 Mishandling of Exceptional Conditions | Errores no expuestos al usuario | `config.php`, manejo de fallos |

## Comandos Utiles

Levantar solo la version segura:

```bash
docker compose up --build secure_app secure_db
```

Ver logs:

```bash
docker compose logs secure_app
docker compose logs secure_db
```

Detener servicios:

```bash
docker compose down
```

Borrar volumenes y reiniciar base desde cero:

```bash
docker compose down -v
docker compose up --build secure_app secure_db
```

Validar sintaxis PHP:

```bash
php -l config.php
php -l login.php
php -l upload.php
```

## Comparacion con Web Vulnerable

| Tema | Web vulnerable | SecureShop v2 |
| --- | --- | --- |
| Transporte | HTTP | HTTPS con TLS local |
| SQL | Concatenacion de strings | Prepared statements |
| Passwords | Texto plano | `password_hash()` |
| Sesion | Basica | Regeneracion, `HttpOnly`, `SameSite`, `Secure` en HTTPS |
| Upload | Sin validacion suficiente | Extension, MIME, tamano y storage controlado |
| Admin | Bypass por parametros | Rol validado server-side |
| Logging | Ausente o narrativo | Eventos persistidos |
| Errores | Verbosos | No expuestos al usuario |

## Checklist de Entrega

- [ ] `docker compose config` no muestra errores.
- [ ] `https://localhost:8443` carga SecureShop v2.
- [ ] El navegador muestra certificado TLS autofirmado.
- [ ] Login admin funciona con `admin@secureshop.local` / `AdminPass123!`.
- [ ] Login usuario funciona con `user@secureshop.local` / `UserPass123!`.
- [ ] Upload seguro rechaza archivos no permitidos.
- [ ] Security logs registran login, bloqueo y eventos administrativos.
- [ ] La version vulnerable sigue disponible para comparacion si se levanta `app` + `db`.
