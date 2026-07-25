# Configuracion de IIS y PostgreSQL para los laboratorios PHP

Este MR deja listas las aplicaciones `web vulnerable` y `web seguro` para ejecutarse en IIS usando PostgreSQL, dominios locales y `index.php` como documento predeterminado.

## Resumen

- Se unificaron las credenciales PostgreSQL usadas por ambas aplicaciones.
- Se mantuvieron bases de datos separadas para no mezclar datos entre el laboratorio vulnerable y el seguro.
- Se agrego configuracion de IIS para evitar el error `HTTP 403.14 - Forbidden`.
- Se documento como ejecutar los scripts SQL y como acceder usando DNS local.

## Credenciales PostgreSQL

Ambas aplicaciones usan el mismo usuario de base de datos:

```text
Usuario: web_app
Contrasena: secureshop_pass123
```

Las bases de datos siguen separadas:

```text
web vulnerable -> vulnshop_db
web seguro     -> secureshop_db
```

El usuario `postgres` se usa solamente para ejecutar los scripts de creacion porque es el administrador de PostgreSQL. La aplicacion PHP se conecta con `web_app`.

## Archivos modificados

| Archivo | Cambio |
| --- | --- |
| `web vulnerable/config.php` | Usa `web_app` y `secureshop_pass123` para conectarse a PostgreSQL. |
| `web seguro/config.php` | Usa `web_app` y `secureshop_pass123` para conectarse a PostgreSQL. |
| `web vulnerable/postgresql_setup.sql` | Crea el rol `web_app` y le da permisos sobre `vulnshop_db`. |
| `web seguro/postgresql_setup.sql` | Crea el rol `web_app` y le da permisos sobre `secureshop_db`. |
| `web vulnerable/README.md` | Documenta las nuevas credenciales PostgreSQL. |
| `web seguro/README.md` | Documenta el usuario PostgreSQL compartido. |
| `web vulnerable/web.config` | Configura `index.php` como documento predeterminado en IIS. |
| `web seguro/web.config` | Configura `index.php` como documento predeterminado en IIS. |

## Ejecucion de scripts SQL

Ejecutar el script de la aplicacion vulnerable:

```powershell
cd "C:\UNAS FIIS\PRACTICAS\webs_practica\web vulnerable"
psql -U postgres -f .\postgresql_setup.sql
```

Ejecutar el script de la aplicacion segura:

```powershell
cd "C:\UNAS FIIS\PRACTICAS\webs_practica\web seguro"
psql -U postgres -f .\postgresql_setup.sql
```

Si `psql` no se reconoce, usar la ruta completa:

```powershell
& "C:\Program Files\PostgreSQL\16\bin\psql.exe" -U postgres -f .\postgresql_setup.sql
```

## Configuracion en IIS

Crear dos sitios en IIS:

| Sitio | Ruta fisica | Host name | Puerto |
| --- | --- | --- | --- |
| `web vulnerable` | `C:\UNAS FIIS\PRACTICAS\webs_practica\web vulnerable` | `vulnerable.local` | `80` |
| `web seguro` | `C:\UNAS FIIS\PRACTICAS\webs_practica\web seguro` | `seguro.local` | `80` |

Ambos sitios pueden usar el puerto `80` porque IIS diferencia cada sitio por el `Host name`.

## DNS local

Editar como administrador el archivo:

```text
C:\Windows\System32\drivers\etc\hosts
```

Agregar:

```text
127.0.0.1 vulnerable.local
127.0.0.1 seguro.local
```

Limpiar la cache DNS:

```powershell
ipconfig /flushdns
```

URLs esperadas:

```text
http://vulnerable.local
http://seguro.local
```

## Problemas corregidos

### DNS no resolvia `vulnerable.local`

El navegador mostraba:

```text
DNS_PROBE_POSSIBLE
```

La causa era que `vulnerable.local` no existia en el archivo `hosts`.

### IIS mostraba `HTTP 403.14 - Forbidden`

IIS llegaba a la carpeta fisica, pero no sabia que archivo cargar por defecto.

Se agrego `web.config` en ambas aplicaciones para definir:

```text
index.php
```

como documento predeterminado.

## Validacion realizada

- Se verifico que ambas aplicaciones tienen `index.php`.
- Se verifico que ya no quedan referencias a credenciales anteriores:

```text
vulnshop_user
vulnshop_pass123
secureshop_user
```

- Se confirmo que las configuraciones PHP usan:

```text
web_app
secureshop_pass123
```

## Checklist para probar

- [ ] Ejecutar `postgresql_setup.sql` de `web vulnerable`.
- [ ] Ejecutar `postgresql_setup.sql` de `web seguro`.
- [ ] Confirmar que IIS tiene PHP configurado con FastCGI.
- [ ] Confirmar que `pdo_pgsql` y `pgsql` estan habilitados en `php.ini`.
- [ ] Confirmar que `vulnerable.local` y `seguro.local` existen en `hosts`.
- [ ] Ejecutar `iisreset`.
- [ ] Abrir `http://vulnerable.local`.
- [ ] Abrir `http://seguro.local`.

## Notas para revision

Este cambio no mezcla las bases de datos. Solo comparte el usuario PostgreSQL `web_app` para simplificar la configuracion local.

La aplicacion no usa el usuario `postgres` porque ese rol es administrador y no debe ser usado por codigo PHP.
