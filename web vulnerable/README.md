# VulnShop PHP - Guia de Desarrollo

VulnShop es una aplicacion PHP deliberadamente vulnerable con apariencia de tienda online. El objetivo es practicar desarrollo, configuracion local y analisis de vulnerabilidades OWASP Top 10 2025 en un entorno controlado.

No publiques esta aplicacion en internet. Contiene vulnerabilidades criticas de forma intencional.

## Ruta Rapida

1. Instala PHP 8+, PostgreSQL y las extensiones `pdo_pgsql` y `pgsql`.
2. Crea la base de datos ejecutando `postgresql_setup.sql`.
3. Inicia el servidor local con `php -S 127.0.0.1:8000`.
4. Abre `http://127.0.0.1:8000`.

```bash
cd "web vulnerable"
php -S 127.0.0.1:8000
```

## Ruta Rapida con Docker

Usa esta opcion si quieres levantar PHP/Apache y PostgreSQL sin instalar dependencias manuales.

Desde la raiz del repositorio:

```bash
docker compose up --build
```

Abre:

```text
http://127.0.0.1:8000
```

Servicios incluidos:

| Servicio | Imagen/build | Puerto |
| --- | --- | --- |
| `app` | `web vulnerable/Dockerfile` con PHP 8.2 + Apache | `8000:80` |
| `db` | `postgres:16` | `5433:5432` |

Comandos utiles:

```bash
docker compose ps
docker compose logs app
docker compose logs db
docker compose down
docker compose down -v
```

Usa `docker compose down -v` solo si quieres borrar tambien los datos de PostgreSQL, flags dinamicas y uploads generados dentro de volumenes.

Desde tu maquina host, PostgreSQL queda expuesto en `127.0.0.1:5433` para evitar choque con una instalacion local en `5432`. Dentro de Docker, la app usa `db:5432`.

## Requisitos

| Herramienta | Version recomendada | Uso |
| --- | --- | --- |
| PHP | 8.x | Ejecutar la aplicacion web |
| PostgreSQL | 14+ | Base de datos de usuarios, productos, ordenes y flags |
| pdo_pgsql | Activa en PHP | Conexion PDO hacia PostgreSQL |
| pgsql | Activa en PHP | Soporte PostgreSQL para PHP |
| IIS o PHP built-in server | Opcional | Servir la app en Windows |
| Docker Desktop | Opcional | Levantar app y base de datos con Compose |

## Configuracion de Base de Datos

Valores por defecto usados por `config.php`:

| Campo | Valor |
| --- | --- |
| Base de datos | `vulnshop_db` |
| Usuario | `web_app` |
| Password | `secureshop_pass123` |
| Host | `127.0.0.1` |
| Puerto | `5432` |

En Docker, estos valores se inyectan por variables de entorno desde `docker-compose.yml`. En ejecucion local tradicional, `config.php` conserva los valores por defecto.

## Instalacion en Ubuntu

```bash
sudo apt update
sudo apt install postgresql php php-pgsql
cd "web vulnerable"
sudo -u postgres psql -f postgresql_setup.sql
php -S 127.0.0.1:8000
```

Resultado esperado:

```text
PHP Development Server (http://127.0.0.1:8000) started
```

## Instalacion en Windows

1. Instala PostgreSQL desde `https://www.postgresql.org/download/windows/`.
2. Activa estas extensiones en `php.ini`.

```ini
extension=pdo_pgsql
extension=pgsql
```

3. Reinicia IIS, Apache o el proceso PHP que estes usando.
4. Ejecuta el setup desde PowerShell.

```powershell
cd "C:\UNAS FIIS\PRACTICAS\webs_practica\web vulnerable"
psql -U postgres -f .\postgresql_setup.sql
php -S 127.0.0.1:8000
```

Si `psql` no existe en la terminal, agrega el directorio `bin` de PostgreSQL al `PATH`. Ejemplo: `C:\Program Files\PostgreSQL\16\bin`.

## IIS en Windows

1. Activa IIS con CGI: `Windows Features > Internet Information Services > World Wide Web Services > Application Development Features > CGI`.
2. Configura FastCGI para usar `php-cgi.exe`.
3. Activa `pdo_pgsql` y `pgsql` en `php.ini`.
4. Crea un sitio apuntando a la carpeta `web vulnerable`.
5. Usa un puerto local, por ejemplo `8000`.
6. Abre `http://localhost:8000`.

Permisos necesarios:

| Ruta | Permiso |
| --- | --- |
| `web vulnerable/` | Lectura para el usuario del servidor web |
| `web vulnerable/data/` | Escritura para flags dinamicas |
| `web vulnerable/uploads/` | Escritura para pruebas de subida de archivos |

## Crear Base de Datos Manualmente

Usa esto solo si no ejecutas `postgresql_setup.sql` completo.

```sql
CREATE DATABASE vulnshop_db;
CREATE ROLE web_app LOGIN PASSWORD 'secureshop_pass123';
GRANT ALL PRIVILEGES ON DATABASE vulnshop_db TO web_app;
```

Luego ejecuta el script de tablas y datos:

```bash
psql -U postgres -f postgresql_setup.sql
```

## Cuentas de Prueba

| Rol | Email | Password |
| --- | --- | --- |
| Admin | `admin@example.com` | `admin123` |
| Usuario | `user@example.com` | `user123` |

Estas credenciales son intencionalmente debiles porque forman parte del laboratorio.

## Estructura del Proyecto

| Ruta | Proposito |
| --- | --- |
| `index.php` | Portada comercial de la tienda |
| `config.php` | Configuracion de entorno, base de datos y sesion |
| `db.php` | Conexion PDO, seed de datos y generacion de flags |
| `includes/header.php` | Layout superior y navegacion visible |
| `includes/footer.php` | Footer comun |
| `assets/styles.css` | Estilos principales |
| `postgresql_setup.sql` | Script reproducible de base de datos |
| `backup/` | Archivos de respaldo y notas usadas para enumeracion |
| `fileshare/` | Archivos del portal interno de documentos |
| `uploads/` | Carpeta de archivos subidos durante practica |
| `api/submit-flag.php` | Endpoint JSON para validar flags |

## Rutas Principales

| Ruta | Descripcion |
| --- | --- |
| `/index.php` | Pagina de ventas y productos destacados |
| `/search.php` | Busqueda de productos |
| `/product.php?id=1` | Detalle de producto y comentarios |
| `/cart.php` | Carrito de compras |
| `/checkout.php` | Registro de ordenes |
| `/orders.php` | Ordenes del usuario |
| `/login.php` | Inicio de sesion |
| `/profile.php?id=1` | Perfil de usuario |
| `/admin.php` | Panel administrativo |

## Rutas de Practica Ocultas

Estas rutas no deben aparecer como parte principal de la tienda. Sirven para practicar enumeracion y analisis.

| Ruta | Uso en practica |
| --- | --- |
| `/debug.php` | Exposicion de configuracion, usuarios y sesion |
| `/nodes.php` | Inventario interno de nodos |
| `/healthcheck.php` | Health check con entrada controlada por usuario |
| `/files.php` | Portal de archivos con parametro `path` |
| `/repo.php` | Vista simulada de repositorio interno |
| `/import_xml.php` | Importador XML vulnerable |
| `/deserialize.php` | Herramienta de deserializacion vulnerable |
| `/rce.php` | Diagnostico local con ejecucion de comandos |
| `/logs.php` | Vista narrativa sobre ausencia de logging real |
| `/guide.php` | Guia del profesor o estudiante |
| `/hints.php` | Pistas progresivas |
| `/status.php` | Progreso de flags enviadas |
| `/submit_flag.php` | Envio web de flags |

## Flags Dinamicas

Las flags se generan automaticamente en `data/*.txt` cuando la aplicacion arranca y `db.php` ejecuta el seed.

| Stage | Archivo generado |
| --- | --- |
| Recon | `data/recon.txt` |
| User | `data/user.txt` |
| Admin | `data/admin.txt` |
| Root | `data/root.txt` |
| RCE | `data/rce.txt` |

Los archivos `data/*.txt` estan ignorados por Git porque son artefactos generados en runtime.

## Flujo Recomendado de Desarrollo

1. Cambia una sola funcionalidad por vez.
2. Ejecuta `php -l` sobre cada archivo PHP modificado.
3. Prueba la ruta en navegador.
4. Si cambias esquema o seed, actualiza `postgresql_setup.sql`.
5. Si agregas rutas nuevas, documentalas en este README.
6. Si agregas archivos generados por runtime, agregalos a `.gitignore`.

Comandos utiles:

```bash
php -l index.php
php -l admin.php
php -l db.php
```

## Flujo Recomendado de Practica

1. Visita la tienda como usuario normal.
2. Enumera rutas comunes y archivos de respaldo.
3. Revisa comportamiento de login, busqueda, perfiles y ordenes.
4. Identifica entradas controladas por el usuario.
5. Valida una vulnerabilidad con el payload minimo necesario.
6. Registra impacto, evidencia y correccion recomendada.
7. Envia flags en `/submit_flag.php` o `POST /api/submit-flag.php`.

## OWASP Top 10 2025 Map

| OWASP 2025 | Vulnerabilidad | Donde |
| --- | --- | --- |
| A01 Broken Access Control | IDOR, parametros de rol confiados, acceso a ordenes y traversal | `profile.php`, `orders.php`, `admin.php`, `files.php` |
| A02 Security Misconfiguration | Debug activo, nodos internos expuestos, rutas predecibles | `config.php`, `debug.php`, `nodes.php`, `repo.php` |
| A03 Software Supply Chain Failures | Dependencia frontend antigua | `includes/header.php` |
| A04 Cryptographic Failures | Passwords en texto plano y secretos filtrados | `db.php`, `debug.php`, `repo.php` |
| A05 Injection | SQLi, XSS, XXE, SSRF-style URL injection y command injection | `login.php`, `search.php`, `product.php`, `admin.php`, `import_xml.php`, `healthcheck.php`, `rce.php` |
| A06 Insecure Design | Flujos administrativos y de mantenimiento mal disenados | `admin.php`, `checkout.php`, `healthcheck.php` |
| A07 Identification and Authentication Failures | Sin rate limiting, passwords debiles y sesion simple | `login.php`, `config.php` |
| A08 Software or Data Integrity Failures | Deserializacion insegura y subida de archivos ejecutables | `deserialize.php`, `admin.php`, `uploads/` |
| A09 Security Logging and Alerting Failures | Acciones criticas sin logging ni alertas reales | `login.php`, `admin.php`, `checkout.php`, `logs.php` |
| A10 Mishandling of Exceptional Conditions | Errores verbosos y manejo debil de fallos | `config.php`, `debug.php`, `files.php`, `healthcheck.php` |

## Checklist Antes de Compartir

- [ ] La app corre solo en red local o entorno aislado.
- [ ] PostgreSQL esta usando credenciales de laboratorio, no reales.
- [ ] `data/*.txt` no esta versionado.
- [ ] `uploads/*` no esta versionado.
- [ ] No hay datos personales reales en seeds, backups o screenshots.
- [ ] El README indica que la app es vulnerable de forma intencional.

## Troubleshooting

| Problema | Causa probable | Solucion |
| --- | --- | --- |
| `could not find driver` | Falta `pdo_pgsql` | Activar extension en `php.ini` y reiniciar PHP/IIS |
| `password authentication failed` | Credenciales distintas en PostgreSQL | Revisar `config.php` y recrear rol `web_app` |
| `relation does not exist` | No se ejecuto el setup SQL | Ejecutar `psql -U postgres -f postgresql_setup.sql` |
| `Permission denied` en `data/` | El servidor no puede escribir flags | Dar permiso de escritura a `data/` |
| Upload falla | Falta carpeta o permisos en `uploads/` | Crear `uploads/` y dar permiso de escritura |
| Imagenes no cargan | Bloqueo de red externa o CDN | Usar URLs locales o revisar acceso a internet |

## Notas de Mantenimiento

Si cambias credenciales por defecto, actualiza estos archivos:

- `config.php`
- `postgresql_setup.sql`
- `README.md`

Si agregas una vulnerabilidad nueva, actualiza estas secciones:

- `Rutas de Practica Ocultas`
- `OWASP Top 10 2025 Map`
- `Flujo Recomendado de Practica`

## Uso Permitido

Esta aplicacion es solo para aprendizaje, demostraciones academicas y laboratorios autorizados. No uses estas tecnicas contra sistemas de terceros sin permiso explicito.
